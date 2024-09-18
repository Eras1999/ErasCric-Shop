<?php

namespace PaymentPlugins\PPCP\WooCommerceProductAddons;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;

class FrontendAssets {

	private $assets;

	public function __construct( AssetsApi $assets ) {
		$this->assets = $assets;
	}

	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		$this->register_scripts();
	}

	private function register_scripts() {
		$this->assets->register_script( 'wc-ppcp-product-addons', 'build/product.js' );
	}

	public function enqueue_scripts() {
		if ( is_product() ) {
			wp_enqueue_script( 'wc-ppcp-product-addons' );
		}
	}

}