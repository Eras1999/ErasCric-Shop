<?php

namespace PaymentPlugins\WooCommerce\PPCP\Shortcodes;

use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\PayLaterMessageSettings;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;

class ProductPayLaterMessage extends AbstractPayLaterMessage {

	public $id = 'ppcp_product_message';

	/**
	 * @var PayLaterMessageSettings
	 */
	protected $settings;

	protected $style_key = 'product';

	public function is_supported_page( ContextHandler $context ) {
		return $context->is_product();
	}

	public function before_render() {
		$this->assets->register_script( 'wc-ppcp-paylater-msg-product', 'build/js/paylater-message-product.js' );
		$handles[] = 'wc-ppcp-paylater-msg-product';
		$this->payment_gateways->add_scripts( $handles );
	}

	public function render() {
		?>
        <div class="wc-ppcp-paylater-msg__container" style="display: none">
            <div id="wc-ppcp-paylater-msg-product"></div>
        </div>
		<?php
	}

}