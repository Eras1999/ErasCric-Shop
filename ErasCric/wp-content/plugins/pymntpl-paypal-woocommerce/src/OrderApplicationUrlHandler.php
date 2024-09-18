<?php

namespace PaymentPlugins\WooCommerce\PPCP;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;

/**
 *
 */
class OrderApplicationUrlHandler {

	private $payment_gateways;

	public function __construct( PaymentGateways $payment_gateways ) {
		$this->payment_gateways = $payment_gateways;
	}

	public function initialize() {
		add_action( 'woocommerce_api_ppcp_checkout_return', [ $this, 'handle_checkout_return' ] );
		add_action( 'woocommerce_api_ppcp_order_return', [ $this, 'handle_order_return' ] );
	}

	public function handle_checkout_return() {
		try {
			check_ajax_referer( 'checkout-nonce', '_checkoutnonce' );
			$order_id = absint( WC()->session->get( 'order_awaiting_payment', 0 ) );
			if ( $order_id ) {
				$order           = wc_get_order( $order_id );
				$token           = isset( $_GET['token'] ) ? \wc_clean( wp_unslash( $_GET['token'] ) ) : null;
				$ba_token        = isset( $_GET['ba_token'] ) ? \wc_clean( wp_unslash( $_GET['ba_token'] ) ) : null;
				$payment_gateway = $this->payment_gateways->get_gateway( $order->get_payment_method() );
				// Set the order ID so it can be retrieved
				$_POST["{$payment_gateway->id}_paypal_order_id"] = $token;
				$_POST["{$payment_gateway->id}_billing_token"]   = $ba_token;
				$result                                          = $payment_gateway->process_payment( $order_id );
				if ( isset( $result['result'] ) && $result['result'] === 'success' ) {
					$redirect = $result['redirect'];
				} else {
					$redirect = wc_get_checkout_url();
				}
			}
		} catch ( \Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			$redirect = wc_get_checkout_url();
		}

		wp_safe_redirect( $redirect );
		exit;
	}

	public function handle_order_return() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$order_id          = isset( $_GET['order_id'] ) ? absint( \wc_clean( \wp_unslash( $_GET['order_id'] ) ) ) : null;
		$order_key         = $_GET['order_key'] ?? null;
		$payment_method_id = $_GET['payment_method'] ?? null;
		$token             = isset( $_GET['token'] ) ? \wc_clean( \wp_unslash( $_GET['token'] ) ) : null;
		$ba_token          = isset( $_GET['ba_token'] ) ? \wc_clean( \wp_unslash( $_GET['ba_token'] ) ) : null;
		if ( $order_id && $order_key && $token && $payment_method_id ) {
			$order = wc_get_order( $order_id );
			if ( $order && $order->key_is_valid( $order_key ) ) {
				$payment_gateway = $this->payment_gateways->get_gateway( $payment_method_id );
				// Set the order ID so it can be retrieved
				$_POST["{$payment_gateway->id}_paypal_order_id"] = $token;
				$_POST["{$payment_gateway->id}_billing_token"]   = $ba_token;
				$result                                          = $payment_gateway->process_payment( $order_id );
				if ( isset( $result['result'] ) && $result['result'] === 'success' ) {
					wp_safe_redirect( $result['redirect'] );
				} else {
					wp_safe_redirect( $order->get_checkout_payment_url() );
					exit;
				}
			}
		}
	}

}