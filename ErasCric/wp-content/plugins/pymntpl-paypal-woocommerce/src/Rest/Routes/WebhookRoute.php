<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;


use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\PayPalSDK\WebhookEvent;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\Logger;

class WebhookRoute extends AbstractRoute {

	private $client;

	private $api_settings;

	private $log;

	private $stop_request = false;

	public function __construct( PayPalClient $client, APISettings $api_settings, Logger $log ) {
		$this->client       = $client;
		$this->api_settings = $api_settings;
		$this->log          = $log;
	}

	public function get_path() {
		return 'webhook/(?P<environment>[\w]+)';
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'handle_request' ]
			]
		];
	}

	public function handle_request( \WP_REST_Request $request ) {
		// authenticate the request
		$environment = $request->get_param( 'environment' );
		$payload     = \json_decode( $request->get_body(), true );
		try {
			$this->validate_headers();
			// get the webhook ID
			$webhook_id = $this->api_settings->get_webhook_id( $environment );
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$params = [
				'auth_algo'         => isset( $_SERVER['HTTP_PAYPAL_AUTH_ALGO'] ) ? $_SERVER['HTTP_PAYPAL_AUTH_ALGO'] : '',
				'cert_url'          => isset( $_SERVER['HTTP_PAYPAL_CERT_URL'] ) ? $_SERVER['HTTP_PAYPAL_CERT_URL'] : '',
				'transmission_id'   => isset( $_SERVER['HTTP_PAYPAL_TRANSMISSION_ID'] ) ? $_SERVER['HTTP_PAYPAL_TRANSMISSION_ID'] : '',
				'transmission_sig'  => isset( $_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG'] ) ? $_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG'] : '',
				'transmission_time' => isset( $_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME'] ) ? $_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME'] : '',
				'webhook_id'        => $webhook_id,
				'webhook_event'     => $payload
			];

			$this->log->info(
				sprintf( 'Webhook received. Event: %s', $payload['event_type'] ),
				sprintf( ' Payload: %s', print_r( $payload, true ) ),
				'webhook'
			);

			if ( has_action( $this->get_action_name( $payload['event_type'] ) ) ) {
				$result = $this->client->environment( $environment )->webhooks->verifySignature( $params );
				if ( ! is_wp_error( $result ) ) {
					if ( $result->verification_status === 'SUCCESS' ) {
						return parent::handle_request( $request );
					}
				}
				throw new \Exception( __( 'Verification of Webhook signature failed.', 'pymntpl-paypal-woocommerce' ) );
			} else {
				$this->stop();
			}
		} catch ( \Exception $e ) {
			$this->log->error( sprintf( 'Webhook failed. Event: %s. Reason: %s', $payload['event_type'], $e->getMessage() ) );

			return new \WP_Error( 'INVALID_WEBHOOK', $e->getMessage(), [ 'status' => 400 ] );
		}
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		if ( ! $this->stop_request ) {
			$payload = \json_decode( $request->get_body(), true );
			$event   = new WebhookEvent( $payload );
			try {
				$this->log->info(
					sprintf( 'Executing action %s', $this->get_action_name( $event->event_type ) ),
					'webhook'
				);

				do_action( $this->get_action_name( $event->event_type ), $event->resource, $event, $request );

				$this->log->info(
					sprintf( 'Webhook executed successfully. Exiting...', $this->get_action_name( $event->event_type ) ),
					'webhook'
				);

				return [];
			} catch ( \Exception $e ) {
				$status = $e->getCode() ? $e->getCode() : 200;
				$this->log->error( sprintf( 'Error processing event %s. Reason: %s', $event->event_type, $e->getMessage() ) );

				return new \WP_Error( 'WEBHOOK_ERROR', $e->getMessage(), [ 'status' => $status ] );
			}
		} else {
			return [];
		}
	}

	/**
	 * @throws \Exception
	 */
	private function validate_headers() {
		$headers = [
			'HTTP_PAYPAL_TRANSMISSION_SIG',
			'HTTP_PAYPAL_AUTH_ALGO',
			'HTTP_PAYPAL_CERT_URL',
			'HTTP_PAYPAL_TRANSMISSION_ID',
			'HTTP_PAYPAL_TRANSMISSION_TIME'
		];
		foreach ( $headers as $header ) {
			if ( empty( $_SERVER[ $header ] ) ) {
				throw new \Exception( sprintf( 'The %s header cannot be empty.', $header ) );
			}
		}
	}

	private function get_action_name( $event_type ) {
		return 'wc_ppcp_webhook_event_' . strtolower( $event_type );
	}

	private function stop() {
		$this->stop_request = true;
	}

}