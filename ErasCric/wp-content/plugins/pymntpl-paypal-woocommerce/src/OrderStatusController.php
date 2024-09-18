<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;

class OrderStatusController {

	private $advanced_settings;

	public function __construct( AdvancedSettings $advanced_settings ) {
		$this->advanced_settings = $advanced_settings;
		$this->initialize();
	}

	private function initialize() {
		if ( $this->advanced_settings->is_capture_on_complete() ) {
			add_action( 'woocommerce_order_status_completed', [ $this, 'maybe_capture_payment' ], 10, 2 );
		} elseif ( $this->advanced_settings->is_capture_on_processing() ) {
			add_action( 'woocommerce_order_status_processing', [ $this, 'maybe_capture_payment' ], 10, 2 );
		}
		if ( $this->advanced_settings->is_refund_on_cancel() ) {
			add_action( 'woocommerce_order_status_cancelled', [ $this, 'handle_status_cancelled' ], 10, 2 );
		}
	}

	/**
	 * @param $order_id
	 * @param $order
	 *
	 * @return void
	 * @deprecated 1.0.12
	 */
	public function handle_status_completed( $order_id, $order ) {
		$this->maybe_capture_payment( $order_id, $order );
	}

	/**
	 * @param           $order_id
	 * @param \WC_Order $order
	 */
	public function maybe_capture_payment( $order_id, \WC_Order $order ) {
		$payment_method = $this->get_payment_gateway_from_order( $order );
		if ( $payment_method && $payment_method instanceof AbstractGateway ) {
			if ( ! $payment_method->payment_handler->is_processing( [ 'capture', 'payment' ] ) ) {
				$payment_method->payment_handler->set_processing( 'capture' );
				$payment_method->payment_handler->process_capture( $order );
				$payment_method->payment_handler->remove_processing();
			}
		}
	}

	/**
	 * @param           $order_id
	 * @param \WC_Order $order
	 */
	public function handle_status_cancelled( $order_id, \WC_Order $order ) {
		$payment_method = $this->get_payment_gateway_from_order( $order );
		if ( $payment_method && $payment_method instanceof AbstractGateway ) {
			if ( ! $payment_method->payment_handler->is_processing( 'void' ) ) {
				$payment_method->payment_handler->set_processing( 'void' );
				$payment_method->payment_handler->process_order_cancellation( $order );
			}
		}
	}

	private function get_payment_gateway_from_order( \WC_Order $order ) {
		$payment_methods = WC()->payment_gateways()->payment_gateways();
		$payment_method  = $order->get_payment_method();

		return isset( $payment_methods[ $payment_method ] ) ? $payment_methods[ $payment_method ] : null;
	}

}