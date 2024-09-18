<?php

namespace PaymentPlugins\WooCommerce\PPCP\Conversion;

use PaymentPlugins\PayPalSDK\Token;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\PluginIntegrationController;

abstract class GeneralPayPalPlugin {

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	protected $payment_token_id;

	protected $is_plugin = false;

	public function __construct( PluginIntegrationController $plugin_controller ) {
		$subscriptions = $plugin_controller->get_integration( 'woocommerce_subscriptions' );
		if ( $subscriptions && $subscriptions->is_active() ) {
			add_filter( 'woocommerce_subscription_get_payment_method', [ $this, 'get_payment_method' ], 10, 2 );
			add_filter( 'wc_ppcp_payment_source_from_order', [ $this, 'get_payment_source_from_order' ], 10, 2 );
			add_filter( 'wc_ppcp_add_subscription_payment_meta', [ $this, 'add_subscription_payment_meta' ], 10, 2 );
		}
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\PaymentSource $payment_source
	 * @param \WC_Order                               $order
	 */
	public function get_payment_source_from_order( $payment_source, $order ) {
		if ( $this->is_plugin && $payment_source->getToken() && $payment_source->getToken()->getType() === Token::BILLING_AGREEMENT ) {
			$token = $payment_source->getToken();
			if ( ! $token->getId() ) {
				$id = $order->get_meta( $this->payment_token_id );
				if ( $id ) {
					$token->setId( $id );
				}
			}
		}

		return $payment_source;
	}

	/**
	 * @param string    $payment_method
	 * @param \WC_Order $order
	 */
	public function get_payment_method( $payment_method, $order ) {
		if ( $payment_method === $this->id ) {
			$payment_method  = 'ppcp';
			$this->is_plugin = true;
		}

		return $payment_method;
	}

	/**
	 * @param array     $payment_meta
	 * @param \WC_Order $subscription
	 */
	public function add_subscription_payment_meta( $payment_meta, $subscription ) {
		if ( isset( $payment_meta['ppcp'] ) ) {
			if ( empty( $payment_meta['ppcp']['post_meta'][ Constants::BILLING_AGREEMENT_ID ]['value'] ) ) {
				$id = $subscription->get_meta( $this->payment_token_id );
				if ( $id ) {
					$payment_meta['ppcp']['post_meta'][ Constants::BILLING_AGREEMENT_ID ]['value'] = $id;
				}
			}
		}

		return $payment_meta;
	}

}