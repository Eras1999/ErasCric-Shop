<?php

namespace PaymentPlugins\PPCP\WooCommerceExtraProductOptions;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Config;

class Package extends \PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage {

	public function is_active() {
		return \defined( 'THEMECOMPLETE_EPO_PLUGIN_FILE' );
	}

	public function initialize() {
		$this->container->get( FrontendAssets::class )->initialize();
	}

	public function register_dependencies() {
		$this->container->register( FrontendAssets::class, function ( $container ) {
			return new FrontendAssets( new AssetsApi(
				new Config( $this->version, dirname( __FILE__ ) )
			) );
		} );
	}

}