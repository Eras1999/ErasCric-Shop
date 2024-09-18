<?php

namespace PaymentPlugins\WooCommerce\PPCP\Conversion;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\PayPalSDK\Token;

/**
 * https://wordpress.org/plugins/woocommerce-paypal-payments/
 */
class WooCommercePayPalPayments extends GeneralPayPalPlugin {

	public $id = 'ppcp-gateway';

	protected $payment_token_id = 'payment_token_id';

	private $client;

	public function __construct( PayPalClient $client, ...$args ) {
		$this->client = $client;
		parent::__construct( ...$args );
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\PaymentSource $payment_source
	 * @param \WC_Order                               $order
	 *
	 * @return \PaymentPlugins\PayPalSDK\PaymentSource
	 */
	public function get_payment_source_from_order( $payment_source, $order ) {
		$payment_source = parent::get_payment_source_from_order( $payment_source, $order );
		if ( $this->is_plugin && $payment_source->getToken() ) {
			if ( ! $payment_source->getToken()->getId() ) {
				// try to find the token another way.
				$customer_id = $this->get_customer_id( $order->get_customer_id() );
				if ( $customer_id ) {
					/**
					 * @var $tokens \PaymentPlugins\PayPalSDK\Collection
					 */
					$tokens = $this->client->paymentTokens->all( [ 'customer_id' => $customer_id ] );
					if ( ! is_wp_error( $tokens ) && $tokens->count() > 0 ) {
						$token = $tokens->get( 0 );
						$payment_source->setToken( new Token( [ 'id' => $token->id ] ) );
					}
				}
			}
			$payment_source->getToken()->setType( Token::PAYMENT_METHOD_TOKEN );
		}

		return $payment_source;
	}

	private function get_customer_id( $user_id ) {
		$id = null;
		if ( $user_id > 0 ) {
			$id = get_user_meta( $user_id, 'ppcp_guest_customer_id', true );
			if ( ! $id ) {
				$settings = get_option( 'woocommerce-ppcp-settings', [ 'prefix' => '' ] );
				$id       = $settings['prefix'] . $user_id;
			}
		}

		return $id;
	}

}