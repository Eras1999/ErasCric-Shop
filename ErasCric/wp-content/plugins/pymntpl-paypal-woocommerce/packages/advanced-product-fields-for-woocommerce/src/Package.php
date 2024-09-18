<?php

namespace PaymentPlugins\PPCP\SW_WAPF;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Config;
use PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage;

class Package extends AbstractPackage {

	public $id = 'advanced_product_fields_for_woocommerce';

	public function is_active() {
		return function_exists( 'SW_WAPF_auto_loader' );
	}

	public function initialize() {
		$this->container->get( FrontendAssets::class )->initialize();
	}

	public function register_dependencies() {
		$this->container->register( FrontendAssets::class, function ( $container ) {
			return new FrontendAssets(
				new AssetsApi(
					new Config( $this->version, dirname( __FILE__ ) )
				)
			);
		} );
	}

}