<?php

namespace PaymentPlugins\WooCommerce\PPCP;

use PaymentPlugins\PayPalSDK\Order;
use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\Cache\CacheInterface;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;

class AjaxFrontendHandler {

	private $client;

	private $payment_gateways;

	private $cache;

	public function __construct( PayPalClient $client, PaymentGateways $payment_gateways, CacheInterface $cache ) {
		$this->client           = $client;
		$this->payment_gateways = $payment_gateways;
		$this->cache            = $cache;
		$this->initialize();
	}

	private function initialize() {
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'process_update_order_review' ] );
		add_filter( 'woocommerce_checkout_posted_data', [ $this, 'add_checkout_data' ] );
	}

	public function process_update_order_review( $data ) {
		parse_str( $data, $posted_data );
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$payment_method = isset( $_POST['payment_method'] ) ? wc_clean( wp_unslash( $_POST['payment_method'] ) ) : null;
		$id             = isset( $posted_data['ppcp_paypal_order_id'] ) ? wc_clean( wp_unslash( $posted_data['ppcp_paypal_order_id'] ) ) : null;
		$data           = $this->get_update_data( $id, $payment_method );
		foreach ( $data as $key => $value ) {
			$_POST[ $key ] = $value;
		}
	}

	public function add_checkout_data( $data ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$payment_method = isset( $data['payment_method'] ) ? wc_clean( wp_unslash( $data['payment_method'] ) ) : null;
		$id             = isset( $_POST['ppcp_paypal_order_id'] ) ? wc_clean( wp_unslash( $_POST['ppcp_paypal_order_id'] ) ) : null;
		if ( \is_array( $data ) ) {
			return array_merge( $data, $this->get_update_data( $id, $payment_method, true ) );
		}

		return $data;
	}

	private function get_update_data( $id, $payment_method, $processing_checkout = false ) {
		$data = [];
		if ( $this->can_update_data( $id, $payment_method ) ) {
			// add the address info in case it's missing.
			$paypal_order = $this->retrieve_order( $id );
			if ( ! is_wp_error( $paypal_order ) ) {
				$address_key = $processing_checkout ? 'address_1' : 'address';
				$payer       = $paypal_order->getPayer();
				/**
				 * @var \PaymentPlugins\PayPalSDK\PurchaseUnit $purchase_unit
				 */
				$purchase_unit = null;
				if ( $paypal_order->getPurchaseUnits()->count() > 0 ) {
					$purchase_unit = $paypal_order->getPurchaseUnits()->get( 0 );
				}
				$address = $payer ? $payer->getAddress() : null;
				if ( ! Utils::is_valid_address( $address ) ) {
					if ( $purchase_unit && isset( $purchase_unit->shipping->address ) ) {
						$address = $purchase_unit->getShipping()->getAddress();
					}
				}
				if ( Utils::is_valid_address( $address ) ) {
					$prefix                          = $processing_checkout ? 'billing_' : '';
					$data["{$prefix}country"]        = $address->getCountryCode();
					$data["{$prefix}state"]          = $address->getAdminArea1();
					$data["{$prefix}postcode"]       = $address->getPostalCode();
					$data["{$prefix}city"]           = $address->getAdminArea2();
					$data["{$prefix}{$address_key}"] = $address->getAddressLine1();
					$data["{$prefix}address_2"]      = $address->getAddressLine2();
				}
				if ( $purchase_unit && WC()->cart->needs_shipping() ) {
					$address         = $purchase_unit->getShipping()->getAddress();
					$shipping_option = $purchase_unit->getShipping()->getSelectedOption();
					if ( $shipping_option ) {
						list( $idx, $value ) = Utils::parse_shipping_option( $shipping_option->getId() );
						if ( ! isset( $data['shipping_method'] ) || ! \is_array( $data['shipping_method'] ) ) {
							$data['shipping_method'] = [];
						}
						$data['shipping_method'][ $idx ] = $value;
					}
					if ( Utils::is_valid_address( $address ) ) {
						$prefix                          = $processing_checkout ? 'shipping_' : 's_';
						$data["{$prefix}country"]        = $address->getCountryCode();
						$data["{$prefix}state"]          = $address->getAdminArea1();
						$data["{$prefix}postcode"]       = $address->getPostalCode();
						$data["{$prefix}city"]           = $address->getAdminArea2();
						$data["{$prefix}{$address_key}"] = $address->getAddressLine1();
						$data["{$prefix}address_2"]      = $address->getAddressLine2();
						if ( $processing_checkout && $payer && ! empty( $payer->phone->phone_number->national_number ) ) {
							$data["{$prefix}phone"] = $payer->phone->phone_number->national_number;
						}
					}
				}
			}
			$data = apply_filters( 'wc_ppcp_update_checkout_data', $data, $paypal_order, $processing_checkout );
		}

		return $data;
	}

	private function retrieve_order( $id ) {
		if ( $this->cache->exists( Constants::PPCP_ORDER_SESSION_KEY ) ) {
			$order = $this->cache->get( Constants::PPCP_ORDER_SESSION_KEY );
			if ( \is_array( $order ) && $id === $order['id'] ) {
				return new Order( $order );
			}
		}


		$order = $this->client->orders->retrieve( $id );
		if ( ! is_wp_error( $order ) ) {
			$this->cache->set( Constants::PPCP_ORDER_SESSION_KEY, $order->toArray() );
		}

		return $order;
	}

	private function can_update_data( $paypal_order_id, $payment_method_id = null ) {
		// The PayPal order ID needs to exist in order for data to be extracted from it.
		if ( $paypal_order_id && $payment_method_id && $this->payment_gateways->has_gateway( $payment_method_id ) ) {
			// Only PayPal orders that have the GET_FROM_FILE
			return $this->cache->get( Constants::SHIPPING_PREFERENCE ) === OrderApplicationContext::GET_FROM_FILE;
		}

		return false;
	}

}