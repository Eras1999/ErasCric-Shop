<?php

namespace PaymentPlugins\PPCP\WooCommerceExtraProductOptions;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;

class FrontendAssets {

	private $assets;

	public function __construct( AssetsApi $assets ) {
		$this->assets = $assets;
	}

	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_scripts() {
		$this->assets->register_script( 'wc-ppcp-epo-product', 'build/product.js' );

		if ( is_product() ) {
			wp_enqueue_script( 'wc-ppcp-epo-product' );
		}
	}

}