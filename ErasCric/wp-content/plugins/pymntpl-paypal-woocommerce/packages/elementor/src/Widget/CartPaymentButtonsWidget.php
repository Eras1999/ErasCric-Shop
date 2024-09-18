<?php

namespace PaymentPlugins\PPCP\Elementor\Widget;

use Elementor\Controls_Manager;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\PaymentButtonController;
use PaymentPlugins\WooCommerce\PPCP\PaymentMethodRegistry;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;
use PaymentPlugins\WooCommerce\PPCP\TemplateLoader;

class CartPaymentButtonsWidget extends AbstractButtonWidget {

	protected $widget_name = 'ppcp_cart_buttons';

	public function get_title() {
		return esc_html__( 'PayPal Cart Payment Buttons', 'pymntpl-paypal-woocommerce' );
	}

	public function get_keywords() {
		return [ 'paypal', 'paypal cart' ];
	}

	public function get_icon() {
		return 'eicon-paypal-button';
	}

	public function get_script_depends() {
		$payment_gateways = Main::container()->get( PaymentGateways::class );
		$handles          = $this->get_gateway()->get_cart_script_handles();
		$payment_gateways->add_scripts( $handles );
		$this->add_script_data();

		return $handles;
	}

	protected function render() {
		$this->template_loader->load_template( 'cart/payment-methods.php', [
			'payment_methods'   => [ $this->get_gateway() ],
			'below_add_to_cart' => 'below'
		] );
	}

	protected function get_widget_page() {
		return 'cart';
	}

	protected function is_supported_page() {
		return $this->context->is_cart();
	}

}