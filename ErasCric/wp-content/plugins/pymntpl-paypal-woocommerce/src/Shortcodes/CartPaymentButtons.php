<?php

namespace PaymentPlugins\WooCommerce\PPCP\Shortcodes;

use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Utils;

class CartPaymentButtons extends AbstractPaymentButtons {

	public $id = 'ppcp_cart_buttons';

	public function is_supported_page( ContextHandler $context ) {
		return $context->is_cart();
	}

	public function get_supported_pages() {
		return [ 'cart' ];
	}

	public function before_render() {
		$handles = $this->get_gateway()->get_cart_script_handles();
		$this->payment_gateways->add_scripts( $handles );
	}

	public function render() {
		if ( is_ajax() ) {
			$data = apply_filters( 'wc_ppcp_cart_data', Utils::get_cart_data( WC()->cart ) );
			$this->assets_data->print_data( 'wcPPCPCartData', $data );
		}
		$this->templates->load_template( 'cart/payment-methods.php', [
			'payment_methods'   => [ $this->get_gateway() ],
			'below_add_to_cart' => $this->attributes->get( 'location' ) === 'below'
		] );
	}

}