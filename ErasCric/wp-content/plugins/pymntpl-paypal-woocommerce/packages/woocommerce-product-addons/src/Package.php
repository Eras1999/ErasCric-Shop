<?php

namespace PaymentPlugins\PPCP\WooCommerceProductAddons;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Config;
use PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage;

class Package extends AbstractPackage {

	public $id = 'woocommerce_product_addons';

	public function is_active() {
		return \function_exists( 'woocommerce_product_addons_activation' );
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