<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin;


use PaymentPlugins\PayPalSDK\Authorization;
use PaymentPlugins\PayPalSDK\V1\Tracker;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

class AdminOrder extends \PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin\AbstractRoute {

	private $client;

	public function __construct( WPPayPalClient $client ) {
		$this->client = $client;
	}

	public function get_path() {
		return 'order/(?P<order_id>[\w]+)';
	}

	public function get_routes() {
		return [
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'handle_request' ],
				'permission_callback' => [ $this, 'get_admin_permission_check' ]
			],
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'handle_request' ],
				'permission_callback' => [ $this, 'get_admin_permission_check' ],
				'args'                => [
					'amount' => [
						'required'          => true,
						'validate_callback' => function ( $value ) {
							return \is_numeric( $value ) && $value > 0;
						}
					]
				]
			],
			[
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'handle_request' ],
				'permission_callback' => [ $this, 'get_admin_permission_check' ]
			]
		];
	}

	public function handle_get_request( \WP_REST_Request $request ) {
		$order          = wc_get_order( absint( $request['order_id'] ) );
		$transaction_id = $order->get_transaction_id();
		$data           = [ 'can_capture' => false ];
		//fetch the order
		if ( empty( $transaction_id ) ) {
			$authorization_id = $order->get_meta( Constants::AUTHORIZATION_ID );
			// there is an auth ID and no transaction, so this order can be captured
			if ( $authorization_id ) {
				$authorization = $this->client->orderMode( $order )->authorizations->retrieve( $authorization_id );
				if ( is_wp_error( $authorization ) ) {
					throw new \Exception( $authorization->get_error_message(), 200 );
				}
				$data['authorization'] = $authorization;
				$data['can_capture']   = $authorization->status === Authorization::CREATED;
			}
		} else {
			$capture = $this->client->orderMode( $order )->captures->retrieve( $transaction_id );
			if ( is_wp_error( $capture ) ) {
				throw new \Exception( $capture->get_error_message(), 200 );
			}
			$tracking        = $order->get_meta( Constants::PAYPAL_TRACKING_NUMBER );
			$data['tracker'] = ( new Tracker() )->setTrackingNumber( $tracking )
			                                    ->setStatus( Tracker::SHIPPED )
			                                    ->setTransactionId( $transaction_id )
			                                    ->setCarrierNameOther( '' );
			if ( $tracking ) {
				$tracker = $this->client->tracking->retrieve( $tracking, $transaction_id );
				if ( ! \is_wp_error( $tracker ) ) {
					$data['tracker'] = $tracker;
				}
			}
			$data['capture']         = $capture;
			$data['tracking_number'] = $tracking;
		}

		$paypal_order = $this->client->orders->retrieve( $order->get_meta( Constants::ORDER_ID ) );

		if ( is_wp_error( $paypal_order ) ) {
			throw new \Exception( $paypal_order->get_error_message(), 200 );
		}

		$data['has_shipping'] = ! ! $order->has_shipping_address();
		$data['order']        = $paypal_order;

		return $data;
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		$order           = wc_get_order( absint( $request['order_id'] ) );
		$payment_methods = WC()->payment_gateways()->payment_gateways();
		/**
		 * @var AbstractGateway $payment_method
		 */
		$payment_method = $payment_methods[ $order->get_payment_method() ];
		$amount         = $request['amount'];

		return $payment_method->payment_handler->process_capture( $order, $amount );
	}

	public function handle_delete_request( \WP_REST_Request $request ) {
		$order           = wc_get_order( absint( $request['order_id'] ) );
		$payment_methods = WC()->payment_gateways()->payment_gateways();
		/**
		 * @var AbstractGateway $payment_method
		 */
		$payment_method = $payment_methods[ $order->get_payment_method() ];

		return $payment_method->payment_handler->process_void( $order, true );
	}

}