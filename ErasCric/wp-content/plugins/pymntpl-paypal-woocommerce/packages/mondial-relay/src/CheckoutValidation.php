<?php

namespace PaymentPlugins\PPCP\MondialRelay;

class CheckoutValidation {

	public function __construct() {
		add_action( 'wc_ppcp_rest_handle_checkout_validation', [ $this, 'validate_checkout' ], 10, 3 );
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\Rest\Routes\AbstractRoute $route
	 * @param array                                                      $data
	 * @param \WP_Error                                                  $errors
	 *
	 * @return void
	 */
	public function validate_checkout( $route, $data, $errors ) {
		if ( WC()->cart->needs_shipping() ) {
			$ids = wc_get_chosen_shipping_method_ids();
			if ( in_array( \MRWP_Shipping_Method::$method_id, $ids ) ) {
				$errors->add( 'mondial-relay-point', $this->get_relay_point_message() );

				add_filter( 'wc_ppcp_checkout_validation_notice', function () {
					return $this->get_relay_point_message();
				} );
			}
		}
	}

	public function get_relay_point_message() {
		return __( 'Please choose a <strong>Point Relais®</strong>.', 'pymntpl-paypal-woocommerce' );
	}

}