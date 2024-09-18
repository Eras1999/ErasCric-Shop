<?php

namespace PaymentPlugins\WooCommerce\PPCP\Shortcodes;

use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Utils;

class ProductPaymentButtons extends AbstractPaymentButtons {

	public $id = 'ppcp_product_buttons';

	public function is_supported_page( ContextHandler $context ) {
		return $context->is_product();
	}

	public function get_supported_pages() {
		return [ 'product' ];
	}

	public function before_render() {
		$handles = $this->get_gateway()->get_product_script_handles();
		$this->payment_gateways->add_scripts( $handles );
	}

	public function render() {
		global $product;
		if ( $product ) {
			$this->assets_data->add( 'product', Utils::get_product_data( $product ) );
			$this->templates->load_template( 'product/payment-methods.php', [
				'payment_methods' => [ $this->get_gateway() ],
				'position'        => 'bottom'
			] );
		}
	}

}