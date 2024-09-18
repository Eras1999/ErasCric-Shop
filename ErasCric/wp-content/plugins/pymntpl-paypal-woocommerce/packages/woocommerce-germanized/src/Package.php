<?php

namespace PaymentPlugins\PPCP\WooCommerceGermanized;

use PaymentPlugins\WooCommerce\PPCP\CheckoutValidator;

class Package extends \PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage {

	public $id = 'woocommerce_germanized';

	public function is_active() {
		return \class_exists( 'WooCommerce_Germanized' );
	}

	public function initialize() {
		add_action( 'wc_ppcp_checkout_validation', function ( CheckoutValidator $validator, \WP_REST_Request $request ) {
			if ( \class_exists( 'WC_GZD_Legal_Checkbox_Manager' ) ) {
				$errors = new \WP_Error();
				\WC_GZD_Legal_Checkbox_Manager::instance()->validate_checkout( $request->get_json_params(), $errors );
				if ( $errors->has_errors() ) {
					foreach ( $errors->get_error_messages() as $msg ) {
						$validator->add_error( $msg );
					}
				}
			}
		}, 10, 2 );
	}

	public function register_dependencies() {
	}

}