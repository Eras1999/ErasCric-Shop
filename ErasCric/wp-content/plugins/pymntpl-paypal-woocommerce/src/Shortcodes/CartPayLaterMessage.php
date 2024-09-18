<?php

namespace PaymentPlugins\WooCommerce\PPCP\Shortcodes;

use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\PayLaterMessageSettings;
use PaymentPlugins\WooCommerce\PPCP\Container\Container;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;

class CartPayLaterMessage extends AbstractPayLaterMessage {

	public $id = 'ppcp_cart_message';

	protected $style_key = 'cart';

	public function is_supported_page( ContextHandler $context ) {
		return $context->is_cart();
	}

	public function before_render() {
		$this->assets->register_script( 'wc-ppcp-paylater-msg-cart', 'build/js/paylater-message-cart.js' );
		$handles[] = 'wc-ppcp-paylater-msg-cart';
		$this->payment_gateways->add_scripts( $handles );
	}

	public function render() {
		?>
        <div class="wc-ppcp-paylater-msg__container" style="display: none">
            <div id="wc-ppcp-paylater-msg-cart"></div>
        </div>
		<?php
	}

}