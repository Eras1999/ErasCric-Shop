<?php

namespace PaymentPlugins\PPCP\FunnelKit\Cart;

use PaymentPlugins\WooCommerce\PPCP\PaymentButtonController;

class CartIntegration {

	private $btn_ctrl;

	public function __construct( PaymentButtonController $btn_ctrl ) {
		$this->btn_ctrl = $btn_ctrl;
	}

	public function initialize() {
		add_action( 'fkcart_before_checkout_button', [ $this, 'render_before_checkout_button' ] );
		add_action( 'fkcart_after_checkout_button', [ $this, 'render_after_checkout_button' ] );
	}

	public function render_before_checkout_button() {
		if ( $this->btn_ctrl->get_minicart_location() === 'above' ) {
			$this->render();
		}
	}

	public function render_after_checkout_button() {
		if ( $this->btn_ctrl->get_minicart_location() === 'below' ) {
			$this->render();
		}
	}

	private function render() {
		$this->btn_ctrl->render_minicart_buttons();
		?>
        <style>
            .wc-ppcp-minicart-ppcp {
                display: block;
                margin-top: 10px;
            }
        </style>
		<?php
	}

}