<?php

namespace PaymentPlugins\PPCP\Blocks;

use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\PayPalQueryParams;

class QueryParams extends PayPalQueryParams {

	private $data_registry;

	public function __construct( AssetDataRegistry $data_registry, ContextHandler $context, APISettings $settings ) {
		$this->data_registry   = $data_registry;
		$this->context_handler = $context;
		$this->api_settings    = $settings;
		$this->initialize();
	}

	private function initialize() {
		add_action( 'woocommerce_blocks_checkout_enqueue_data', [ $this, 'add_checkout_query_params' ] );
		add_action( 'woocommerce_blocks_cart_enqueue_data', [ $this, 'add_cart_query_params' ] );
	}

	public function add_checkout_query_params() {
		$this->context_handler->set_context( $this->context_handler::CHECKOUT );
		$this->add_query_params();
	}

	public function add_cart_query_params() {
		$this->context_handler->set_context( $this->context_handler::CART );
		$this->add_query_params();
	}

	public function add_query_params() {
		$this->initialize_paypal_flow();
		$this->initialize_query_params();
		if ( ! $this->data_registry->exists( 'paypalQueryParams' ) ) {
			$this->data_registry->add( 'paypalQueryParams', $this->prepare_query_params() );
		}
	}

}