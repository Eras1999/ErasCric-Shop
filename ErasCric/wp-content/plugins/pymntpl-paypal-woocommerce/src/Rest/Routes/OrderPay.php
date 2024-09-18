<?php

namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;

use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\PayPalSDK\PurchaseUnit;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Utilities\OrderFilterUtil;
use PaymentPlugins\WooCommerce\PPCP\Utils;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

/**
 * Route that handles PayPal order creation requests from the WC pay for order page.
 */
class OrderPay extends AbstractRoute {

	private $factories;

	private $client;

	private $logger;

	public function __construct( CoreFactories $factories, WPPayPalClient $client, Logger $logger ) {
		$this->factories = $factories;
		$this->client    = $client;
		$this->logger    = $logger;
	}

	public function get_path() {
		return 'order/pay';
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'handle_request' ],
				'args'     => [
					'payment_method' => [
						'required' => true
					],
					'order_id'       => [
						'required' => true
					]
				]
			]
		];
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return string|void
	 * @throws \Exception
	 * @todo add purchase unit shipping address
	 */
	public function handle_post_request( \WP_REST_Request $request ) {
		try {
			$order = wc_get_order( absint( $request['order_id'] ) );
			if ( ! $order ) {
				throw new \Exception( __( 'Invalid order Id.', 'pymntpl-paypal-woocommerce' ) );
			}
			$order_key = $request['order_key'];
			if ( ! hash_equals( $order_key, $order->get_order_key() ) ) {
				throw new \Exception( __( 'Invalid order key provided.', 'pymntpl-paypal-woocommerce' ) );
			}
			/**
			 * @var \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway $payment_method
			 */
			$payment_method = WC()->payment_gateways()->payment_gateways()[ $request['payment_method'] ];
			$intent         = $payment_method->get_option( 'intent' );
			$paypal_order   = $this->factories->initialize( $order )->order->from_order( $intent );
			/**
			 * @var PurchaseUnit $purchase_unit
			 */
			$purchase_unit = $paypal_order->getPurchaseUnits()->get( 0 );
			if ( ! $purchase_unit->getAmount()->amountEqualsBreakdown() ) {
				unset( $purchase_unit->getAmount()->breakdown );
				unset( $purchase_unit->items );
			}
			if ( $purchase_unit->getShipping() ) {
				if ( ! Utils::is_valid_address( $purchase_unit->getShipping()->getAddress(), 'shipping' ) ) {
					unset( $purchase_unit->shipping );
					$paypal_order->getApplicationContext()->setShippingPreference( OrderApplicationContext::NO_SHIPPING );
				}
			}
			OrderFilterUtil::filter_order( $paypal_order );
			$result = $this->client->orderMode( $order )->orders->create( $paypal_order );
			if ( is_wp_error( $result ) ) {
				throw new \Exception( $result->get_error_message() );
			}

			return $result->id;
		} catch ( \Exception $e ) {
			$this->logger->error( sprintf( 'Error creating PayPal order. Msg:%s Params: %s', $e->getMessage(), print_r( $paypal_order->toArray(), true ) ) );
			throw new \Exception( $e->getMessage(), 400 );
		}
	}

}