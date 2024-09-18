<?php

namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\PayPalSDK\PurchaseUnit;
use PaymentPlugins\WooCommerce\PPCP\Cache\CacheInterface;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Utilities\OrderFilterUtil;
use PaymentPlugins\WooCommerce\PPCP\Utils;

class AbstractCart extends AbstractRoute {

	protected $client;

	protected $logger;

	protected $factories;

	protected $cache;

	public function __construct( PayPalClient $client, Logger $logger, CoreFactories $factories, CacheInterface $cache ) {
		$this->client    = $client;
		$this->logger    = $logger;
		$this->factories = $factories;
		$this->cache     = $cache;
	}

	public function get_path() {
		return 'cart';
	}

	public function get_routes() {
		// TODO: Implement get_routes() method.
	}

	protected function get_order_from_cart( $request ) {
		$payment_method = $this->get_payment_method_from_request( $request );
		$intent         = $payment_method->get_option( 'intent' );
		$order          = $this->factories->initialize( WC()->cart, WC()->customer )->order->from_cart( $intent );
		/**
		 * @var PurchaseUnit $purchase_unit
		 */
		$purchase_unit = $order->getPurchaseUnits()->get( 0 );
		// filter the shipping methods
		if ( $purchase_unit->getShipping() ) {
			$shipping = $purchase_unit->getShipping();
			unset( $shipping->options );

			// validate the address info
			$address = $shipping->getAddress();
			if ( ! Utils::is_valid_address( $address, 'shipping' ) ) {
				unset( $shipping->address );
				// If the payer's address is valid, use that as the default address used by PayPal
				if ( Utils::is_valid_address( $order->getPayer()->getAddress(), 'shipping' ) ) {
					$shipping->setAddress( $order->getPayer()->getAddress() );
				} else {
					unset( $purchase_unit->shipping );
				}
			}
		}
		OrderFilterUtil::filter_order( $order );
		do_action( 'wc_ppcp_get_order_from_cart', $order, $request );

		return $order;
	}

	/**
	 * @param $request
	 *
	 * @return \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway
	 */
	protected function get_payment_method_from_request( $request ) {
		return WC()->payment_gateways()->payment_gateways()[ $request['payment_method'] ];
	}

	protected function calculate_totals() {
		WC()->cart->calculate_totals();
	}

	/**
	 * @param $request
	 *
	 * @since 1.0.6
	 */
	protected function populate_post_data( $request ) {
		/**
		 * Some 3rd party plugins depend on the $_POST array being populated
		 */
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$_POST    = array_merge( $_POST, $request->get_json_params() );
		$_REQUEST = array_merge( $_REQUEST, $request->get_json_params() );
	}

}