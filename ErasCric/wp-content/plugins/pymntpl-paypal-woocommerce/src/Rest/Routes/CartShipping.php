<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;


use PaymentPlugins\PayPalSDK\Order;
use PaymentPlugins\PayPalSDK\PatchRequest;
use PaymentPlugins\PayPalSDK\PurchaseUnit;
use PaymentPlugins\WooCommerce\PPCP\Constants;

/**
 * Route that handles onShippingChange events from the PayPal modal.
 */
class CartShipping extends AbstractCart {

	public function get_path() {
		return 'cart/shipping';
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::EDITABLE,
				'callback' => [ $this, 'handle_request' ],
				'args'     => [
					'payment_method' => [
						'required' => true
					],
					'order_id'       => [
						'required' => true
					]
				]
			]
		];
	}

	/**
	 * Update the shipping address and shipping methods
	 *
	 * @param $request
	 *
	 * @throws \Exception
	 */
	public function handle_post_request( \WP_REST_Request $request ) {
		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );
		if ( isset( $request['address'] ) ) {
			$this->update_shipping_address( $request['address'] );
		}
		if ( isset( $request['shipping_method'] ) ) {
			$this->update_shipping_methods( $request['shipping_method'] );
		}

		$this->populate_post_data( $request );

		$this->add_shipping_hooks();

		$this->calculate_totals();

		// check if there are any shipping rates available.
		if ( ! $this->validate_shipping_methods( WC()->shipping()->get_packages() ) ) {
			throw new \Exception( __( 'There are no shipping options available for the provided address.', 'pymntpl-paypal-woocommerce' ), 200 );
		}

		// fetch the paypal order
		$order = $this->client->orders->retrieve( $request['order_id'] );
		if ( is_wp_error( $order ) ) {
			throw new \Exception( sprintf( __( 'Error fetching order %s. Reason: %s', 'pymntpl-paypal-woocommerce' ), $request['order_id'], $order->get_error_message() ) );
		}

		$this->prepare_post_response( $order );

		return true;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Order $order
	 */
	private function prepare_post_response( Order $order ) {
		$patches = [];
		$this->factories->initialize( WC()->cart, WC()->customer );
		$pu = $this->factories->purchaseUnit->from_cart();

		/**
		 * @var PurchaseUnit $purchase_unit
		 */
		foreach ( $order->purchase_units as $purchase_unit ) {
			if ( $purchase_unit->getReferenceId() ) {
				$pu->setReferenceId( $purchase_unit->getReferenceId() );
			} else {
				$pu->setReferenceId( 'default' );
			}
			$pu->setPayee( $purchase_unit->getPayee() );
			$pu->getShipping()->remove( 'address' )->remove( 'name' );
			$patches[] = $pu->getPatchRequest( '', PatchRequest::REPLACE );
		}
		$result = $this->client->orders->update( $order->getId(), $patches );
		if ( ! is_wp_error( $result ) ) {
			$this->logger->info( sprintf( 'Shipping updated for PayPal order %s. Patches: %s', $order->getId(), print_r( $patches, true ) ), 'payment' );

			$this->cache->delete( Constants::PPCP_ORDER_SESSION_KEY );
		}
	}

	private function update_shipping_address( $address ) {
		$customer = WC()->customer;
		$location = [
			'country'  => isset( $address['country'] ) ? $address['country'] : null,
			'state'    => isset( $address['state'] ) ? $address['state'] : null,
			'postcode' => isset( $address['postcode'] ) ? $address['postcode'] : null,
			'city'     => isset( $address['city'] ) ? $address['city'] : null
		];
		$customer->set_billing_location( ...array_values( $location ) );
		$customer->set_shipping_location( ...array_values( $location ) );
		WC()->customer->set_calculated_shipping( true );
		WC()->customer->save();
	}

	private function update_shipping_methods( $shipping_methods ) {
		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods', [] );
		foreach ( $shipping_methods as $idx => $method ) {
			$chosen_shipping_methods[ $idx ] = $method;
		}
		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
	}

	private function validate_shipping_methods( $packages ) {
		foreach ( $packages as $i => $package ) {
			if ( ! empty( $package['rates'] ) ) {
				return true;
			}
		}

		return false;
	}

	private function add_shipping_hooks() {
		add_filter( 'woocommerce_cart_ready_to_calc_shipping', '__return_true', 1000 );
	}

}