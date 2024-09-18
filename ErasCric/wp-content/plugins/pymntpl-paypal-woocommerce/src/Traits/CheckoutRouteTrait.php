<?php

namespace PaymentPlugins\WooCommerce\PPCP\Traits;

trait CheckoutRouteTrait {

	/**
	 * @param \WC_Customer     $customer
	 * @param \WP_REST_Request $request
	 */
	protected function update_customer_data( $customer, $request ) {
		$customer->set_billing_email( isset( $request['billing_email'] ) ? $request['billing_email'] : '' );

		$fields         = [ 'first_name', 'last_name', 'country', 'state', 'postcode', 'city', 'address_1', 'address_2', 'phone', 'company' ];
		$billing_prefix = apply_filters( 'wc_ppcp_cart_order_billing_prefix', 'billing', $request );
		$props          = [];
		foreach ( $fields as $field ) {
			$key                     = "{$billing_prefix}_{$field}";
			$props["billing_$field"] = isset( $request[ $key ] ) ? wc_clean( wp_unslash( $request[ $key ] ) ) : '';
		}
		if ( wc_ship_to_billing_address_only() ) {
			$customer->set_props( $props );
		} else {
			$shipping_prefix = apply_filters( 'wc_ppcp_cart_order_shipping_prefix', isset( $request['ship_to_different_address'] ) ? 'shipping' : 'billing', $request );
			foreach ( $fields as $field ) {
				$key                      = "{$shipping_prefix}_{$field}";
				$props["shipping_$field"] = isset( $request[ $key ] ) ? wc_clean( wp_unslash( $request[ $key ] ) ) : '';
			}
			$customer->set_props( $props );
		}
	}

	/**
	 * @param $request
	 *
	 * @return bool
	 */
	protected function is_checkout_initiated( $request ) {
		return isset( $request['context'] ) && $request['context'] === 'checkout';
	}

	/**
	 * @since 1.0.38
	 * @return bool
	 */
	public function is_checkout_validation_enabled( $request ) {
		return apply_filters( 'wc_ppcp_is_checkout_validation_enabled', $this->settings->is_checkout_validation_enabled(), $request );
	}

}