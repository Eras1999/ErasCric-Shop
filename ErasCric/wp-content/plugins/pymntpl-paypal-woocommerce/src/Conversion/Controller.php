<?php

namespace PaymentPlugins\WooCommerce\PPCP\Conversion;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\PluginIntegrationController;

class Controller {

	private $registry;

	public function __construct( Registry $registry ) {
		$this->registry = $registry;
		$this->initialize();
	}

	private function initialize() {
		add_action( 'wc_ppcp_loaded', [ $this->registry, 'initialize' ] );
		add_action( 'woocommerce_ppcp_plugin_conversion_registration', [ $this, 'register_instances' ], 10, 2 );
	}

	public function register_instances( $registry, $container ) {
		$this->register_conversions( $container );
		$this->registry->register( $container->get( WooCommercePayPalPayments::class ) );
		$this->registry->register( $container->get( WooCommercePayPalCheckoutGateway::class ) );
		$this->registry->register( $container->get( WooCommercePayPalAngellEYE::class ) );
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\Container\Container $container
	 */
	private function register_conversions( $container ) {
		$container->register( WooCommercePayPalPayments::class, function ( $container ) {
			return new WooCommercePayPalPayments( $container->get( PayPalClient::class ), $container->get( PluginIntegrationController::class ) );
		} );
		$container->register( WooCommercePayPalCheckoutGateway::class, function ( $container ) {
			return new WooCommercePayPalCheckoutGateway( $container->get( PluginIntegrationController::class ) );
		} );
		$container->register( WooCommercePayPalAngellEYE::class, function ( $container ) {
			return new WooCommercePayPalAngellEYE( $container->get( PluginIntegrationController::class ) );
		} );
	}

}