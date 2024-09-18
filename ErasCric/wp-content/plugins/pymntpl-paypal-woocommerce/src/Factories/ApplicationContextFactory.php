<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;

class ApplicationContextFactory extends AbstractFactory {

	private $settings;

	public function __construct( AdvancedSettings $settings, ...$args ) {
		$this->settings = $settings;
		parent::__construct( ...$args );
	}

	/**
	 * @param false $needs_shipping
	 *
	 * @return \PaymentPlugins\PayPalSDK\OrderApplicationContext
	 */
	public function get( $needs_shipping = false, $set_provided = false ) {
		$context = new OrderApplicationContext();
		if ( $needs_shipping ) {
			if ( $set_provided ) {
				$context->setShippingPreference( OrderApplicationContext::SET_PROVIDED_ADDRESS );
			} else {
				$context->setShippingPreference( OrderApplicationContext::GET_FROM_FILE );
			}
		} else {
			$context->setShippingPreference( OrderApplicationContext::NO_SHIPPING );
		}
		if ( $this->get_order() ) {
			$context->setReturnUrl( add_query_arg( [
				'order_id'       => $this->order->get_id(),
				'order_key'      => $this->order->get_order_key(),
				'payment_method' => 'ppcp'
			], WC()->api_request_url( 'ppcp_order_return' ) ) );
			$context->setCancelUrl( wc_get_checkout_url() );
		} else {
			$context->setReturnUrl( add_query_arg( [
				'_checkoutnonce' => wp_create_nonce( 'checkout-nonce' )
			], WC()->api_request_url( 'ppcp_checkout_return' ) ) );
			$context->setCancelUrl( wc_get_checkout_url() );
		}
		$context->setBrandName( $this->settings->get_option( 'display_name' ) );

		return $context;
	}

}