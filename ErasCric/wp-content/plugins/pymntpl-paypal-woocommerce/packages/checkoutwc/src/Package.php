<?php

namespace PaymentPlugins\PPCP\CheckoutWC;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;

class Package extends AbstractPackage {

	public $id = 'checkoutwc';

	public function initialize() {
		$this->container->get( PayPalPaymentGateway::class );
	}

	public function is_active() {
		return defined( 'CFW_NAME' );
	}

	public function register_dependencies() {
		$this->container->register( PayPalPaymentGateway::class, function ( $container ) {
			$instance = PayPalPaymentGateway::instance();
			$instance->set_assets_api( $container->get( AssetsApi::class ) );
			$instance->set_payment_gateways( $container->get( PaymentGateways::class ) );
			$instance->init();

			return $instance;
		} );
	}

}