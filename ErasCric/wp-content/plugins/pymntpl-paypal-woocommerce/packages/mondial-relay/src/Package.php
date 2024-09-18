<?php

namespace PaymentPlugins\PPCP\MondialRelay;

use PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage;

class Package extends AbstractPackage {

	public $id = 'mondial-relay';

	public function is_active() {
		return function_exists( 'MRWP_plugin_updater' );
	}

	public function initialize() {
		$this->container->get( FrontendScripts::class );
		$this->container->get( CheckoutValidation::class );
	}

	public function register_dependencies() {
		$this->container->register( FrontendScripts::class, function () {
			return new FrontendScripts();
		} );
		$this->container->register( CheckoutValidation::class, function () {
			return new CheckoutValidation();
		} );
	}

}