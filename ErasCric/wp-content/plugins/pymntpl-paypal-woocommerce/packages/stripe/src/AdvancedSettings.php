<?php

namespace PaymentPlugins\PPCP\Stripe;

class AdvancedSettings {

	public function __construct() {
		$this->initialize();
	}

	private function initialize() {
		add_filter( 'woocommerce_settings_api_form_fields_ppcp_advanced', [ $this, 'get_form_fields' ] );
	}

	public function get_form_fields( $fields ) {
		$fields['stripe_express'] = [
			'title'       => __( 'Stripe Express Buttons', 'pymntpl-paypal-woocommerce' ),
			'type'        => 'checkbox',
			'default'     => 'yes',
			'value'       => 'yes',
			'description' => __( 'If enabled, the PayPal express buttons will be grouped with the Stripe express buttons on product pages, cart pages, and the express checkout section.', 'pymntpl-paypal-woocommerce' )
		];

		return $fields;
	}

}