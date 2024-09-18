<?php

namespace PaymentPlugins\PPCP\WooCommerceShipStation;

use PaymentPlugins\PayPalSDK\V1\Tracker;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Utilities\ShippingUtil;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

class TrackingController {

	private $client;

	private $log;

	public function __construct( WPPayPalClient $client, Logger $log ) {
		$this->client = $client;
		$this->log    = $log;
	}

	public function initialize() {
		add_action( 'woocommerce_shipstation_shipnotify', [ $this, 'handle_notification' ], 10, 2 );
	}

	/**
	 * @param \WC_Order $order
	 * @param array     $args
	 *
	 * @return void
	 */
	public function handle_notification( $order, $args ) {
		$args   = (object) $args;
		$txn_id = $order->get_transaction_id();

		if ( $txn_id ) {
			// update the tracking info in PayPal
			$tracking = $order->get_meta( Constants::PAYPAL_TRACKING_NUMBER );
			$tracker  = new Tracker();
			$tracker->setTrackingNumber( $args->tracking_number );
			$tracker->setTransactionId( $txn_id );
			$tracker->setStatus( Tracker::SHIPPED );
			$this->populate_carrier( $args->carrier, $tracker );
			if ( ! $tracking ) {
				$response = $this->client->orderMode( $order )->tracking->create( $tracker );
			} else {
				$response = $this->client->orderMode( $order )->tracking->update( $tracking, $txn_id, $tracker );
			}
			if ( is_wp_error( $response ) ) {
				$this->log->info( sprintf( 'Error sending tracking number to PayPal via Shipstation. Reason: %s. Args: %s',
					$response->get_error_message(),
					print_r( [
						'order_id' => $order->get_id(),
						'tracking' => $args->tracking_number,
						'carrier'  => $args->carrier
					], true ) ) );
			} else {
				$order->update_meta_data( Constants::PAYPAL_TRACKING_NUMBER, $tracker->getTrackingNumber() );
				$order->save();
				$this->log->info( sprintf( 'Tracking info updated in PayPal via Shipstation. Order ID: %s', $order->get_id() ) );
			}
		}
	}

	/**
	 * @param string  $carrier
	 * @param Tracker $tracker
	 *
	 * @return void
	 */
	public function populate_carrier( $carrier, Tracker $tracker ) {
		$key      = strtoupper( $carrier );
		$carriers = ShippingUtil::get_carriers();
		$result   = array_key_exists( $key, $carriers );
		if ( $result ) {
			$tracker->setCarrier( $carriers[ $key ] );
		} else {
			$tracker->setCarrier( Tracker::OTHER );
			$tracker->setCarrierNameOther( $carrier );
		}
	}

}