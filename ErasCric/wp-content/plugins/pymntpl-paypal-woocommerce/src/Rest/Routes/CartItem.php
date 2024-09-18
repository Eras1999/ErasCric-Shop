<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;


use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\ProductSettings;

/**
 * Route that handles PayPal product page one-click checkout.
 */
class CartItem extends AbstractCart {

	public function get_path() {
		return 'cart/item';
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::EDITABLE,
				'callback' => [ $this, 'handle_request' ],
				'args'     => [
					'payment_method' => [
						'required' => true
					]
				]
			]
		];
	}

	/**
	 * Add item to the shopping cart
	 *
	 * @param $request
	 */
	public function handle_post_request( \WP_REST_Request $request ) {
		wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
		$this->populate_post_data( $request );
		list( $product_id, $qty, $variation_id, $variation ) = $cart_params = $this->get_add_to_cart_params( $request );
		// remove item before adding, ensuring qty's are accurate
		WC()->cart->remove_cart_item( WC()->cart->generate_cart_id( $product_id, $variation_id, $variation ) );

		try {
			// add item to the cart
			if ( WC()->cart->add_to_cart( ...$cart_params ) === false ) {
				throw new \Exception( __( 'Error adding product to cart.', 'pymntpl-paypal-woocommerce' ) );
			}
			$setting = new ProductSettings( $product_id );
			$order   = $this->get_order_from_cart( $request );
			$order->setIntent( $setting->get_option( 'intent' ) );

			$this->logger->info(
				sprintf( 'Creating PayPal order via %s. Args: %s', __METHOD__, print_r( $order->toArray(), true ) ),
				'payment'
			);

			$result = $this->client->orders->create( $order );
			if ( is_wp_error( $result ) ) {
				throw new \Exception( $result->get_error_message() );
			}

			$this->logger->info( sprintf( 'PayPal order %s created via %s', $result->id, __METHOD__ ), 'payment' );

			$this->cache->set( Constants::PAYPAL_ORDER_ID, $result->id );

			return $result->id;
		} catch ( \Exception $e ) {
			return new \WP_Error( 'add-to-cart-error', $e->getMessage(), [ 'status' => 200 ] );
		}
	}

	private function get_add_to_cart_params( \WP_REST_Request $request ) {
		$args = [
			'product_id'   => $request->get_param( 'product_id' ),
			'qty'          => $request->get_param( 'qty' ),
			'variation_id' => $request->get_param( 'variation_id' ) == null ? 0 : $request->get_param( 'variation_id' ),
			'variation'    => $request->get_param( 'variation' )
		];
		if ( ! $args['variation'] ) {
			$args['variation'] = [];
		}

		return array_values( $args );
	}

}