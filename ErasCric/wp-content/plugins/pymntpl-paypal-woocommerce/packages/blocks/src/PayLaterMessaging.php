<?php

namespace PaymentPlugins\PPCP\Blocks;

use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\PayLaterMessageSettings;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;

class PayLaterMessaging {

	/**
	 * @var PayLaterMessageSettings
	 */
	private $settings;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi
	 */
	private $assets;

	private $data_registry;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\ContextHandler
	 */
	private $context;

	private $registered = false;

	public function __construct( PayLaterMessageSettings $settings, AssetsApi $assets, AssetDataRegistry $data_registry ) {
		$this->settings      = $settings;
		$this->assets        = $assets;
		$this->data_registry = $data_registry;
		$this->initialize();
	}

	private function initialize() {
		add_filter( 'woocommerce_blocks_register_script_dependencies', [ $this, 'add_script_dependencies' ], 10, 2 );
		add_action( 'woocommerce_blocks_checkout_enqueue_data', [ $this, 'add_script_data' ] );
		add_action( 'woocommerce_blocks_cart_enqueue_data', [ $this, 'add_script_data' ] );
	}

	public function add_script_dependencies( $dependencies, $handle ) {
		if ( ! in_array( $handle, [ 'wc-checkout-block', 'wc-checkout-block-frontend', 'wc-cart-block', 'wc-cart-block-frontend' ], true ) ) {
			return $dependencies;
		}
		$context = null;
		if ( strpos( $handle, 'wc-checkout' ) !== false ) {
			$context = $this->context::CHECKOUT;
		} elseif ( strpos( $handle, 'wc-cart' ) !== false ) {
			$context = $this->context::CART;
		}
		if ( $context && wc_string_to_bool( $this->settings->get_option( "{$context}_enabled" ) ) ) {
			$this->assets->register_script( 'wc-ppcp-blocks-commons', 'build/blocks-commons.js' );
			$this->assets->register_script( 'wc-ppcp-blocks-paylater-messaging', 'build/paylater-messaging.js', [ 'wc-ppcp-blocks-commons' ] );
			$dependencies[]   = 'wc-ppcp-blocks-paylater-messaging';
			$this->registered = true;
		}

		return $dependencies;
	}

	public function add_script_data() {
		if ( $this->registered && ! $this->data_registry->exists( 'paylaterParams' ) ) {
			$context = $this->context->get_context();
			$this->data_registry->add( 'paylaterParams', [
				'enabled' => wc_string_to_bool( $this->settings->get_option( "{$context}_enabled" ) ),
				'options' => $this->settings->get_context_options( $context )
			] );
		}
	}

	public function set_context_handler( $context ) {
		$this->context = $context;
	}

}