<?php

namespace PaymentPlugins\PPCP\FunnelKit\Upsell\Rest\Routes;

use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\AbstractRoute;

class UpsellOrderRoute extends AbstractRoute {

	private $client;

	private $logger;

	public function __construct( PayPalClient $client, Logger $logger ) {
		$this->client = $client;
		$this->logger = $logger;
	}

	public function get_namespace() {
		return parent::get_namespace() . '/funnelkit';
	}

	public function get_path() {
		return 'upsell/order';
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'handle_request' ]
			]
		];
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		// create the order
		$current_offer      = WFOCU_Core()->data->get( 'current_offer' );
		$current_offer_meta = WFOCU_Core()->offers->get_offer_meta( $current_offer );
		WFOCU_Core()->data->set( '_offer_result', true );
		WFOCU_Core()->process_offer->parse_posted_data( $request->get_json_params() );
		WFOCU_Core()->process_offer->execute( $current_offer_meta );
		// set the package so it can be referenced later
		$order         = WFOCU_Core()->data->get_parent_order();
		$offer_package = WFOCU_Core()->data->get( '_upsell_package' );

		$payment_gateway = WFOCU_Core()->gateways->get_integration( 'ppcp' );

		/**
		 * @var \PaymentPlugins\PayPalSDK\Order $paypal_order
		 */
		$paypal_order        = $payment_gateway->get_create_order_params( $order );
		$application_context = $paypal_order->getApplicationContext();
		if ( \in_array( $application_context->getShippingPreference(), [ OrderApplicationContext::GET_FROM_FILE ] ) ) {
			$application_context->setShippingPreference( OrderApplicationContext::SET_PROVIDED_ADDRESS );
		}
		$application_context->setUserAction( OrderApplicationContext::PAY_NOW );
		//set redirect urls
		$application_context->setReturnUrl( add_query_arg( [ 'wfocu-si' => WFOCU_Core()->data->get_transient_key(), 'order_id' => $order->get_id(), 'order_key' => $order->get_order_key() ], WC()->api_request_url( 'wc_ppcp_funnelkit_return' ) ) );
		$application_context->setCancelUrl( add_query_arg( [ 'wfocu-si' => WFOCU_Core()->data->get_transient_key() ], WFOCU_Core()->public->get_the_upsell_url( WFOCU_Core()->data->get_current_offer() ) ) );

		// create the PayPal order
		$result   = $this->client->orderMode( $order )->orders->create( $paypal_order );
		$redirect = null;
		if ( is_wp_error( $result ) ) {
			$this->logger->error( sprintf( 'Error creating upsell order. Reason: %s', $result->get_error_message() ) );
			throw new \Exception( $result->get_error_message(), 400 );
		}
		$order->update_meta_data( '_upsell_package', $offer_package );
		$order->save();

		foreach ( $result->links as $link ) {
			if ( $link->rel === 'approve' ) {
				$redirect = $link->href;
				break;
			}
		}

		// return the ID
		return [
			'redirect' => $redirect
		];
	}

}