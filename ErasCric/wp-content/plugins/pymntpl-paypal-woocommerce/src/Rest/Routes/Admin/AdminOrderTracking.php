<?php

namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\PayPalSDK\V1\Tracker;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

class AdminOrderTracking extends AbstractRoute {

	private $client;

	public function __construct( WPPayPalClient $client ) {
		$this->client = $client;
	}

	public function get_path() {
		return 'order/(?P<order_id>[\w]+)/tracking';
	}

	public function get_routes() {
		return [
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'handle_request' ],
				'permission_callback' => [ $this, 'get_admin_permission_check' ],
				'args'                => [
					'tracking'        => [
						'required'          => true,
						'validate_callback' => function ( $value ) {
							if ( empty( $value ) ) {
								return new \WP_Error( 'rest_invalid_param', __( 'Tracking number is a required field.', 'pymntpl-paypal-woocommerce' ) );
							}
						}
					],
					/*'tracking_type'   => [
						'required' => true
					],*/
					'shipping_status' => [
						'required' => true
					],
					'carrier'         => [
						'required' => true
					],
					'carrier_other'   => [
						'validate_callback' => [ $this, 'validate_carrier_other' ]
					]
				]
			]
		];
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		$order           = \wc_get_order( absint( $request['order_id'] ) );
		$txn_id          = $order->get_transaction_id();
		$tracking_number = $order->get_meta( Constants::PAYPAL_TRACKING_NUMBER );

		if ( ! $txn_id ) {
			throw new \Exception( __( 'Tracking cannot be added until the payment is captured.', 'pymntpl-paypal-woocommerce' ) );
		}
		// get the tracking info
		$tracker = new Tracker();
		$tracker->setTransactionId( $txn_id );
		$tracker->setTrackingNumber( $request['tracking'] );
		//$tracker->setTrackingNumberType( $request['tracking_type'] );
		$tracker->setStatus( $request['shipping_status'] );
		$tracker->setCarrier( $request['carrier'] );

		if ( ! empty( $request['carrier_other'] ) ) {
			$tracker->setCarrierNameOther( $request['carrier_other'] );
		}
		if ( ! empty( $request['notify_buyer'] ) ) {
			$tracker->setNotifyBuyer( $request['notify_buyer'] );
		}

		if ( $tracking_number ) {
			$response = $this->client->tracking->update( $tracking_number, $txn_id, $tracker );
		} else {
			$response = $this->client->orderMode( $order )->tracking->create( $tracker );
		}

		if ( ! \is_wp_error( $response ) ) {
			$order->update_meta_data( Constants::PAYPAL_TRACKING_NUMBER, $tracker->getTrackingNumber() );
			$order->save();
			$response = [
				'message' => __( 'The tracking information has been updated.', 'pymntpl-paypal-woocommerce' )
			];
		}

		return $response;
	}

	public function validate_carrier_other( $value, $request, $param ) {
		$carrier = $request['carrier'];
		if ( $carrier === Tracker::OTHER && ! $value ) {
			return new \WP_Error( 'rest_invalid_param', __( 'Carrier name is required when the selected carrier is "Other".', 'pymntpl-paypal-woocommerce' ) );
		}

		return true;
	}

}