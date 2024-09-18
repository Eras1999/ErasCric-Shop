<?php

namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;

use PaymentPlugins\PayPalSDK\Order;
use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;
use PaymentPlugins\WooCommerce\PPCP\CheckoutValidator;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Traits\CheckoutRouteTrait;

/**
 * Route that is called when the PayPal integration requests an order ID.
 */
class CartOrder extends AbstractCart {

	use CheckoutRouteTrait;

	private $settings;

	private $validator;

	public function __construct( AdvancedSettings $settings, ...$args ) {
		parent::__construct( ...$args );
		$this->settings  = $settings;
		$this->validator = new CheckoutValidator( $this->settings );
	}

	public function get_path() {
		return 'cart/order';
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
		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );
		if ( $this->is_checkout_initiated( $request ) ) {
			// only update the customer data if this is the checkout page since the product and cart page
			// don't have any input fields that should update the customer
			$this->update_customer_data( WC()->customer, $request );

			if ( $this->settings->is_shipping_address_disabled() ) {
				add_action( 'wc_ppcp_get_order_from_cart', function ( Order $order ) {
					// If the application context is allowing the address to be changed, override that.
					if ( $order->getApplicationContext()->getShippingPreference() === OrderApplicationContext::GET_FROM_FILE ) {
						$order->getApplicationContext()->setShippingPreference( OrderApplicationContext::SET_PROVIDED_ADDRESS );
					}
				} );
			}
		}
		$this->populate_post_data( $request );
		$this->calculate_totals();
		$order = $this->get_order_from_cart( $request );
		try {
			if ( $this->is_checkout_initiated( $request ) ) {
				if ( $this->is_checkout_validation_enabled( $request ) ) {
					$this->validator->validate_checkout( $request );
				}
				/**
				 * 3rd party code can use this action to perform custom validations.
				 *
				 * @since 1.0.31
				 */
				do_action( 'wc_ppcp_validate_checkout_fields', $request, $this->validator );
			}

			$result = $this->client->orders->create( $order );
			if ( is_wp_error( $result ) ) {
				/**
				 * @var \WP_Error $result
				 */
				if ( $result->get_error_code() === 'MISSING_SHIPPING_ADDRESS' ) {
					throw new \Exception( __( 'Please enter a valid shipping address.', 'pymntpl-paypal-woocommerce' ) );
				}
				throw new \Exception( $result->get_error_message() );
			}
			$this->cache->set( Constants::PAYPAL_ORDER_ID, $result->id );
			$this->cache->set( Constants::SHIPPING_PREFERENCE, $order->getApplicationContext()->getShippingPreference() );

			$this->logger->info(
				sprintf( 'PayPal order created via %s. Args: %s', __METHOD__, print_r( $result->toArray(), true ) ),
				'payment'
			);


			return $result->id;
		} catch ( \Exception $e ) {
			$this->logger->error( sprintf( 'Error creating PayPal order. Msg: %s Params: %s', $e->getMessage(), print_r( $order->toArray(), true ) ) );
			throw new \Exception( $e->getMessage(), $e->getCode() > 0 ? $e->getCode() : 400 );
		}
	}

	public function get_error_response( $error ) {
		if ( $error instanceof \Exception && $this->validator->has_errors() ) {
			return $this->validator->get_failure_response();
		}

		return parent::get_error_response( $error );
	}

}