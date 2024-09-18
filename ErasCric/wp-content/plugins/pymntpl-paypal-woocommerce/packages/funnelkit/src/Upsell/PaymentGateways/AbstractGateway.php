<?php

namespace PaymentPlugins\PPCP\FunnelKit\Upsell\PaymentGateways;

use PaymentPlugins\PayPalSDK\Amount;
use PaymentPlugins\PayPalSDK\Breakdown;
use PaymentPlugins\PayPalSDK\Collection;
use PaymentPlugins\PayPalSDK\Item;
use PaymentPlugins\PayPalSDK\Money;
use PaymentPlugins\PayPalSDK\Order;
use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\PayPalSDK\PurchaseUnit;
use PaymentPlugins\PayPalSDK\Refund;
use PaymentPlugins\PayPalSDK\Shipping;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\FeeCalculation;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\PaymentHandler;
use PaymentPlugins\WooCommerce\PPCP\PaymentResult;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;
use PaymentPlugins\WooCommerce\PPCP\Utilities\OrderLock;
use PaymentPlugins\WooCommerce\PPCP\Utilities\PayPalFee;

class AbstractGateway extends \WFOCU_Gateway {

	public $id = '';

	public $refund_supported = true;

	protected $assets;

	protected $payment_handler;

	/**
	 * @var \WFOCU_Logger
	 */
	protected $logger;

	protected $paypal_order = null;

	public function __construct( AssetsApi $assets, PaymentHandler $payment_handler, $logger ) {
		$this->assets          = $assets;
		$this->payment_handler = $payment_handler;
		$this->logger          = $logger;
	}

	public static function get_instance() {
		return Main::container()->get( static::class );
	}

	public function get_payment_method_script_handles() {
		return [];
	}

	public function process_charge( $order ) {
		$this->handle_client_error();
		$payment_method = $this->get_wc_gateway();
		$this->payment_handler->set_payment_method( $payment_method );
		$client = $this->payment_handler->client;
		try {
			if ( $this->is_processing_redirect() ) {
				$paypal_order = $this->paypal_order;
			} else {
				$this->payment_handler->set_use_billing_agreement( true );
				$paypal_order = $client->orders->create( $this->get_create_order_params( $order ) );
				if ( is_wp_error( $paypal_order ) ) {
					throw new \Exception( $paypal_order->get_error_message() );
				}
			}
			if ( Order::CAPTURE == $paypal_order->intent ) {
				OrderLock::set_order_lock( $order );
				$response = $client->orders->capture( $paypal_order->id, $this->payment_handler->get_payment_source( $order ) );
			} else {
				$response = $client->orders->authorize( $paypal_order->id, $this->payment_handler->get_payment_source( $order ) );
			}
			$result = new PaymentResult( $response, $order, $payment_method );
			if ( $result->success() ) {
				WFOCU_Core()->data->set( '_transaction_id', $result->get_capture_id() );
				$this->update_order_fee( $result, $order );

				return $this->handle_result( true );
			} else {
				throw new \Exception( $result->get_error_message() );
			}
		} catch ( \Exception $e ) {
			$this->logger->log( sprintf( 'WC-PPCP - could not complete upsell payment. Order: %1$s. Error: %2$s', $order->get_id(), $e->getMessage() ) );
			throw new \WFOCU_Payment_Gateway_Exception( $e->getMessage(), 400 );
		}
	}

	public function handle_client_error() {
		$package = WFOCU_Core()->data->get( '_upsell_package' );
		if ( $package && isset( $package['_client_error'] ) ) {
			$this->logger->log( sprintf( 'PPCP client error: %s', sanitize_text_field( $package['_client_error'] ) ) );
		}
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \PaymentPlugins\PayPalSDK\Order
	 */
	public function get_create_order_params( $order ) {
		$currency       = $order->get_currency();
		$package        = WFOCU_Core()->data->get( '_upsell_package' );
		$paypal_order   = $this->payment_handler->get_create_order_params( $order );
		$current_offer  = WFOCU_Core()->data->get( 'current_offer' );
		$item_total     = array_reduce( $package['products'], function ( $total, $product ) {
			return $total + $product['price'];
		}, 0 );
		$purchase_units = new Collection();
		$purchase_unit  = ( new PurchaseUnit() )
			->setAmount( ( new Amount() )
				->setValue( NumberUtil::round_incl_currency( $package['total'], $currency ) )
				->setCurrencyCode( $currency )
				->setBreakdown( ( new Breakdown() )
					->setItemTotal( ( new Money() )
						->setCurrencyCode( $currency )
						->setValue( NumberUtil::round_incl_currency( $item_total, $currency ) ) )
					->setShipping( ( new Money() )
						->setCurrencyCode( $currency )
						->setValue( NumberUtil::round_incl_currency( $package['shipping'], $currency ) ) )
					->setTaxTotal( ( new Money() )
						->setCurrencyCode( $currency )
						->setValue( NumberUtil::round_incl_currency( $package['taxes'], $currency ) ) )
					->setDiscount( ( new Money() )
						->setCurrencyCode( $currency )
						->setValue( NumberUtil::round_incl_currency( 0, $currency ) ) )
					->setHandling( ( new Money() )
						->setCurrencyCode( $currency )
						->setValue( NumberUtil::round_incl_currency( 0, $currency ) ) ) ) )
			->setItems( array_reduce( $package['products'], function ( $collection, $product ) use ( $currency ) {
				return $collection->add( ( new Item() )
					->setName( \substr( $product['data']->get_name(), 0, 127 ) )
					->setQuantity( $product['qty'] )
					->setUnitAmount( ( new Money() )
						->setCurrencyCode( $currency )
						->setValue( NumberUtil::round_incl_currency( $product['price'] / $product['qty'], $currency ) ) ) );
			}, new Collection() ) )
			->setInvoiceId( sprintf( '%s-%s', $order->get_id(), $current_offer ? $current_offer : 'wfocu' ) )
			->setCustomId( sprintf( '%1$s_%2$s', 'wfocu', $order->get_id() ) );

		if ( \in_array( $paypal_order->getApplicationContext()->getShippingPreference(), [ OrderApplicationContext::GET_FROM_FILE, OrderApplicationContext::SET_PROVIDED_ADDRESS ] ) ) {
			$purchase_unit->setShipping( ( new Shipping() )
				->setName( $paypal_order->getPurchaseUnits()->get( 0 )->getShipping()->getName() )
				->setAddress( $paypal_order->getPurchaseUnits()->get( 0 )->getShipping()->getAddress() ) );
		}

		Main::container()->get( CoreFactories::class )->purchaseUnit->filter_purchase_unit( $purchase_unit, $purchase_unit->getAmount()->getValue() );
		$purchase_units->add( $purchase_unit );

		return ( new Order() )
			->setIntent( $paypal_order->getIntent() )
			->setPayer( $paypal_order->getPayer() )
			->setApplicationContext( $paypal_order->getApplicationContext() )
			->setPurchaseUnits( $purchase_units );
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return bool|void
	 */
	public function process_refund_offer( $order ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$transaction_id = isset( $_POST['txn_id'] ) ? wc_clean( wp_unslash( $_POST['txn_id'] ) ) : false;
		$amount         = isset( $_POST['amt'] ) ? round( wc_clean( wp_unslash( $_POST['amt'] ) ), 2 ) : false;
		if ( ! $transaction_id || ! $amount ) {
			return;
		}
		$client = $this->payment_handler->client->orderMode( $order );
		$result = $client->captures->refund( $transaction_id, new Refund( [
			'amount'     => new Amount( [
				'value'         => NumberUtil::round_incl_currency( $amount, $order->get_currency() ),
				'currency_code' => $order->get_currency()
			] ),
			'invoice_id' => $transaction_id
		] ) );

		if ( is_wp_error( $result ) ) {
			$this->logger->log( sprintf( 'Error processing refund for order %s. Reason: %s', $order->get_id(), $result->get_error_message() ) );

			return false;
		} else {
			$this->logger->log( sprintf( 'Transaction %s refunded: Amount: %s', $order->get_id(), $amount ) );
			PayPalFee::update_net_from_refund( $result, $order, true );

			return true;
		}
	}

	public function get_transaction_link( $transaction_id, $order_id ) {
		$order = wc_get_order( $order_id );
		$url   = 'https://www.paypal.com/activity/payment/%s';
		if ( $order->get_meta( Constants::PPCP_ENVIRONMENT ) === 'sandbox' ) {
			$url = 'https://www.sandbox.paypal.com/activity/payment/%s';
		}

		return sprintf( $url, $transaction_id );
	}

	public function set_paypal_order( $order ) {
		$this->paypal_order = $order;
	}

	protected function is_processing_redirect() {
		return $this->paypal_order;
	}

	private function update_order_fee( PaymentResult $result, \WC_Order $order ) {
		if ( $result->is_captured() ) {
			$order_behavior = WFOCU_Core()->funnels->get_funnel_option( 'order_behavior' );
			$use_main_order = $order_behavior === 'batching';
			if ( $use_main_order ) {
				// If using the main order update the net and fees
				$calculation = new FeeCalculation( $order );
				$calculation->calculate_from_receivable_breakdown( $result->get_capture()->seller_receivable_breakdown );
				$calculation->save();
			} else {
				add_action( 'wfocu_offer_new_order_created_' . $this->get_key(), function ( $order ) use ( $result ) {
					$calculation = new FeeCalculation( $order, true );
					$calculation->calculate_from_receivable_breakdown( $result->get_capture()->seller_receivable_breakdown );
					$calculation->save();
				} );
			}
		}
	}

}