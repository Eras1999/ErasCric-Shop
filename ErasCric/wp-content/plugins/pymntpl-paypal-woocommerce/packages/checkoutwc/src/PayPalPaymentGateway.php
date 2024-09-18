<?php

namespace PaymentPlugins\PPCP\CheckoutWC;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Main;

class PayPalPaymentGateway extends \Objectiv\Plugins\Checkout\Compatibility\CompatibilityAbstract {

	/**
	 * @var AssetsApi
	 */
	private $assets_api;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways
	 */
	private $payment_gateways;

	public function set_assets_api( $assets_api ) {
		$this->assets_api = $assets_api;
	}

	public function set_payment_gateways( $payment_gateways ) {
		$this->payment_gateways = $payment_gateways;
	}

	public function is_available(): bool {
		return class_exists( '\PaymentPlugins\WooCommerce\PPCP\Main' );
	}

	public function run() {
		$this->payment_gateways->get_payment_method_registry()->initialize();
		/**
		 * @var \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\PayPalGateway $payment_method
		 */
		$payment_method = $this->payment_gateways->get_gateway( 'ppcp' );
		if ( $payment_method && $payment_method->is_section_enabled( 'express_checkout' ) ) {
			add_action( 'cfw_payment_request_buttons', [ $this, 'output_express_button' ] );
			Main::container()->get( \PaymentPlugins\WooCommerce\PPCP\PaymentButtonController::class )->set_render_express_buttons( false );
		}
	}

	private function enqueue_scripts() {
		$this->assets_api->enqueue_script( 'wc-ppcp-express-checkout', 'build/js/paypal-express-checkout.js' );
		$this->assets_api->enqueue_style( 'wc-ppcp-checkoutwc-style', 'packages/checkoutwc/build/styles.css' );
	}

	public function output_express_button() {
		$this->enqueue_scripts();
		?>
        <ul class="wc-ppcp-checkoutwc-express__container">
            <li class="wc-ppcp-checkoutwc-express__payment ppcp">
                <div id="wc-ppcp-express-button"></div>
            </li>
        </ul>
		<?php
	}

}