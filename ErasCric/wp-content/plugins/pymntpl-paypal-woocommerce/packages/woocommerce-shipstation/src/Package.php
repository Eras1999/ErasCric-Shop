<?php

namespace PaymentPlugins\PPCP\WooCommerceShipStation;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage;

class Package extends AbstractPackage {

	public $id = 'ppcp_woocommerce_shipstation';

	public function is_active() {
		return \function_exists( 'woocommerce_shipstation_init' );
	}

	public function initialize() {
		$this->container->get( TrackingController::class )->initialize();
	}

	public function register_dependencies() {
		$this->container->register( TrackingController::class, function () {
			return new TrackingController(
				$this->container->get( PayPalClient::class ),
				$this->container->get( Logger::class )
			);
		} );
	}

}