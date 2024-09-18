<?php

namespace PaymentPlugins\PPCP\Elementor\Widget;

use Elementor\Controls_Manager;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;

class ProductPaymentButtonsWidget extends AbstractButtonWidget {

	protected $widget_name = 'ppcp_product_buttons';

	public function get_title() {
		return esc_html__( 'PayPal Product Payment Buttons', 'pymntpl-paypal-woocommerce' );
	}

	public function get_keywords() {
		return [ 'paypal', 'paypal product' ];
	}

	public function get_icon() {
		return 'eicon-paypal-button';
	}

	protected function render() {
		$this->template_loader->load_template( 'product/payment-methods.php', [
			'payment_methods' => [ $this->get_gateway() ],
			'position'        => 'bottom'
		] );
	}

	public function get_script_depends() {
		$payment_gateways = Main::container()->get( PaymentGateways::class );
		$handles          = $this->get_gateway()->get_product_script_handles();
		$payment_gateways->add_scripts( $handles );
		$this->add_script_data();

		return $handles;
	}

	protected function get_widget_page() {
		return 'product';
	}

	protected function is_supported_page() {
		return $this->context->is_product();
	}

}