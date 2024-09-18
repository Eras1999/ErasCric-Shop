<?php

namespace PaymentPlugins\PPCP\Elementor;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Config;

class Package extends \PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage {

	public $id = 'ppcp_elementor';

	public function is_active() {
		return did_action( 'elementor/loaded' );
	}

	public function initialize() {
		$this->container->get( WidgetController::class )->initialize();
	}

	public function register_dependencies() {
		$this->container->register( WidgetController::class, function ( $container ) {
			return new WidgetController();
		} );
		$this->container->register( 'elementorWidgetAssets', function ( $container ) {
			return new AssetsApi( new Config( $this->version, dirname( __FILE__ ) ) );
		} );
	}

}