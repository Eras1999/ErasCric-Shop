<?php

namespace PaymentPlugins\PPCP\Stripe;

use PaymentPlugins\WooCommerce\PPCP\Container\Container;
use PaymentPlugins\WooCommerce\PPCP\Package\PackageInterface;
use PaymentPlugins\WooCommerce\PPCP\PaymentMethodRegistry;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;

class PaymentButtonController {

	private $container;

	private $settings;

	public function __construct( Container $container, \PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings $settings ) {
		$this->container = $container;
		$this->settings  = $settings;
		$this->initialize();
	}

	private function initialize() {
		if ( $this->is_stripe_express_enabled() ) {
			add_action( 'woocommerce_ppcp_payment_methods_registration', [ $this, 'register_payment_methods' ], 20 );
			$this->container->get( \PaymentPlugins\WooCommerce\PPCP\PaymentButtonController::class )->set_render_cart_buttons( false );
			$this->container->get( \PaymentPlugins\WooCommerce\PPCP\PaymentButtonController::class )->set_render_product_buttons( false );
			$this->container->get( \PaymentPlugins\WooCommerce\PPCP\PaymentButtonController::class )->set_render_express_buttons( false );
		}
	}

	private function is_stripe_express_enabled() {
		return wc_string_to_bool( $this->settings->get_option( 'stripe_express' ) );
	}

	/**
	 * @param PaymentMethodRegistry registry
	 *
	 * @throws \Exception
	 */
	public function register_payment_methods( $registry ) {
		$registry->register( $this->container->get( PayPalPaymentGateway::class ) );
	}

}