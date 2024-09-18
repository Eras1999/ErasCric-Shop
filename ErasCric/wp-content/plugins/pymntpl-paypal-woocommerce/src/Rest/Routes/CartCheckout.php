<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;


use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;

/**
 * Route that handles requests to process the checkout.
 */
class CartCheckout extends AbstractCart {

	/**
	 * @var AbstractGateway
	 */
	public $payment_method;

	/**
	 * @var \WP_REST_Request
	 */
	public $request;

	private function initialize() {
		add_action( 'woocommerce_after_checkout_validation', [ $this, 'handle_checkout_validation' ], 10, 2 );
	}

	public function get_path() {
		return 'cart/checkout';
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'handle_request' ],
				'args'     => [
					'payment_method' => [
						'required' => true
					]
				]
			]
		];
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		$this->request = $request;
		$this->initialize();
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$_POST = array_merge( $_POST, $request->get_json_params() );

		$this->payment_method = $this->get_payment_method_from_request( $request );
		$this->set_required_fields();

		// set the checkout nonce so no exceptions are thrown.
		$_REQUEST['_wpnonce'] = $_POST['_wpnonce'] = wp_create_nonce( 'woocommerce-process_checkout' );

		$this->add_checkout_block_filters();

		WC()->checkout()->process_checkout();
	}

	public function handle_checkout_validation( $data, $errors ) {
		do_action( 'wc_ppcp_rest_handle_checkout_validation', $this, $data, $errors );
		if ( $errors->get_error_codes() ) {
			$this->logger->info( sprintf( 'Redirecting to checkout page. Required fields missing: %s', print_r( $errors->get_error_codes(), true ) ) );
			WC()->session->set( 'chosen_payment_method', $this->payment_method->id );
			foreach ( $errors->errors as $code => $messages ) {
				foreach ( $messages as $msg ) {
					\wc_add_notice( $msg, 'error', $errors->get_error_data( $code ) );
				}
			}
			wc_add_notice(
				apply_filters(
					'wc_ppcp_checkout_validation_notice',
					__( 'Please fill out all required fields then click Place Order.', 'pymntpl-paypal-woocommerce' ),
					$data,
					$errors
				),
				'notice'
			);
			wp_send_json(
				[
					'result'   => 'success',
					'redirect' => $this->get_order_review_url(),
					'reload'   => false,
				],
				200
			);
		}
	}

	/**
	 * @param       $payment_method
	 * @param array $fields
	 *
	 * @return string
	 */
	public function get_order_review_url( $fields = [] ) {
		return add_query_arg(
			[
				'_ppcp_order_review' => rawurlencode( base64_encode( wp_json_encode( [
					'payment_method' => $this->payment_method->id,
					'paypal_order'   => $this->payment_method->payment_handler->get_paypal_order_id_from_request(),
					'fields'         => $fields
				] ) ) ),
			],
			wc_get_checkout_url()
		);
	}

	private function set_required_fields() {
		if ( WC()->cart->needs_shipping() ) {
			$_POST['ship_to_different_address'] = true;
		}
		if ( wc_get_page_id( 'terms' ) > 0 ) {
			$_POST['terms'] = 1;
		}
	}

	/**
	 * If the checkout page is a block, check to see if the billing phone is required. If not required, update the
	 * WC checkout fields.
	 *
	 * @since 1.0.45
	 * @return void
	 */
	private function add_checkout_block_filters() {
		$checkout_page_id = wc_get_page_id( 'checkout' );
		if ( function_exists( 'has_block' ) && $checkout_page_id && has_block( 'woocommerce/checkout', $checkout_page_id ) ) {
			$post   = get_post( $checkout_page_id );
			$result = parse_blocks( $post->post_content );
			if ( $result ) {
				foreach ( $result as $block ) {
					if ( $block['blockName'] === 'woocommerce/checkout' ) {
						if ( empty( $block['attrs']['requirePhoneField'] ) ) {
							add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
								if ( isset( $fields['billing']['billing_phone'] ) ) {
									$fields['billing']['billing_phone']['required'] = false;
								}

								return $fields;
							} );
						}
						break;
					}
				}
			}
		}
	}

}