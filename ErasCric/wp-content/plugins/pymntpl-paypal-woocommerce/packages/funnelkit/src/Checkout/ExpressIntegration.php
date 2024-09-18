<?php

namespace PaymentPlugins\PPCP\FunnelKit\Checkout;

use PaymentPlugins\PPCP\FunnelKit\Checkout\Compatibility\PayPal;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\PaymentButtonController;

class ExpressIntegration {

	private $id = 'paymentplugins_wc_ppcp';

	private $settings;

	private $assets;

	/**
	 * @var \PaymentPlugins\PPCP\FunnelKit\Checkout\Compatibility\AbstractGateway[]
	 */
	private $payment_gateways = [];

	public function __construct( AssetsApi $assets ) {
		$this->assets = $assets;
		$this->initialize();
	}

	protected function initialize() {
		add_action( 'wfacp_after_checkout_page_found', [ $this, 'handle_checkout_page_found' ] );
		add_filter( 'wfacp_smart_buttons', [ $this, 'add_buttons' ], 20 );
		add_action( 'wfacp_smart_button_container_' . $this->id, [ $this, 'render_express_buttons' ] );
	}

	public function handle_checkout_page_found() {
		$this->settings = \WFACP_Common::get_page_settings( \WFACP_Common::get_id() );
		if ( $this->has_express_buttons() ) {
			Main::container()->get( AssetsApi::class )->enqueue_script( 'wc-ppcp-checkout-express', 'build/js/paypal-express-checkout.js' );
			$this->assets->enqueue_style( 'wc-ppcp-checkout-express', 'build/wc-ppcp-funnelkit-checkout-styles.css' );
		}
	}

	private function has_express_buttons() {
		foreach ( $this->get_payment_gateways() as $gateway ) {
			if ( $gateway->is_active() && $gateway->is_express_enabled() ) {
				return true;
			}
		}

		return false;
	}

	private function get_payment_gateways() {
		$this->initialize_gateways();

		return $this->payment_gateways;
	}

	private function initialize_gateways() {
		if ( empty( $this->payment_gateways ) ) {
			$payment_methods = WC()->payment_gateways()->payment_gateways();
			$classes         = [ 'ppcp' => PayPal::class ];
			foreach ( $classes as $id => $clazz ) {
				if ( isset( $payment_methods[ $id ] ) ) {
					$this->payment_gateways[ $id ] = new $clazz( $payment_methods[ $id ] );
				}
			}
		}
	}

	public function add_buttons( $buttons ) {
		if ( $this->has_express_buttons() ) {
			$buttons[ $this->id ] = [
				'iframe' => true
			];
			remove_action( 'woocommerce_checkout_before_customer_details', [ Main::container()->get( PaymentButtonController::class ), 'render_express_buttons' ] );
		}

		return $buttons;
	}

	public function render_express_buttons() {
		?>
        <div id="wc-ppcp-express-button"></div>
		<?php
	}

}