<?php

namespace PaymentPlugins\PPCP\Blocks;

class ContextHandler extends \PaymentPlugins\WooCommerce\PPCP\ContextHandler {

	public function initialize() {
		add_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_before', [ $this, 'set_checkout_block_context' ] );
		add_action( 'woocommerce_blocks_enqueue_cart_block_scripts_before', [ $this, 'set_cart_block_context' ] );
		parent::initialize();
	}

	public function set_checkout_block_context() {
		$this->set_context( $this::CHECKOUT );
	}

	public function set_cart_block_context() {
		$this->set_context( $this::CART );
	}

}