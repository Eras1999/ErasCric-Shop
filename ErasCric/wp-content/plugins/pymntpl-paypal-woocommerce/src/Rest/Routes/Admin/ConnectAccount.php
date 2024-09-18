<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin;


use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

class ConnectAccount extends AbstractRoute {

	private $client;

	private $api_settings;

	private $log;

	public function __construct( WPPayPalClient $client, APISettings $api_settings, Logger $log ) {
		$this->client       = $client;
		$this->api_settings = $api_settings;
		$this->log          = $log;
	}

	public function get_path() {
		return 'account/connect';
	}

	public function get_routes() {
		return [
			[
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'handle_request' ],
				'permission_callback' => [ $this, 'get_admin_permission_check' ]
			],
			[
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'handle_request' ],
				'permission_callback' => [ $this, 'get_admin_permission_check' ],
				'args'                => [
					'environment' => [
						'required' => true
					]
				]
			]
		];
	}

	public function handle_put_request( \WP_REST_Request $request ) {
		$environment    = $request['environment'];
		$connect_params = $this->api_settings->get_option( 'connect_params_' . $environment );
		$this->client->setMerchantId( $connect_params['merchantId'] );
		delete_transient( "wc_ppcp_connection_url_$environment" );
		try {
			// fetch the access_token
			$token = $this->client->environment( $environment )->auth->create( $request['sharedId'], [
				'grant_type'    => 'authorization_code',
				'code'          => $request['authCode'],
				'code_verifier' => $connect_params['sellar_nonce']
			] );
			if ( is_wp_error( $token ) ) {
				throw new \Exception( $token->get_error_message() );
			}

			$this->client->setAccessToken( $token->access_token );

			// fetch the credentials using the access token
			$credentials = $this->client->partner->fetchCredentials();

			if ( is_wp_error( $credentials ) ) {
				throw new \Exception( $credentials->get_error_message() );
			}

			// save the credentials
			$this->client->updateCredentials( $credentials->client_id, $credentials->client_secret, $environment );

			// fetch the new bearer token
			$token = $this->client->auth->refresh();
			if ( is_wp_error( $token ) ) {
				return $token;
			}

			$this->client->setAccessToken( $token->access_token );

			// fetch the merchant Id
			$status = $this->client->connect->retrieveStatus( [
				'trackingId'  => $connect_params['trackingId'],
				'environment' => $environment
			] );

			if ( is_wp_error( $status ) ) {
				throw new \Exception( $status->get_error_message() );
			}
			$this->client->updateApiSettings( [ "merchant_id_{$environment}" => $status->merchant_id ] );

			$url = \rest_url( 'wc-ppcp/v1/webhook/' . $environment );
			if ( ! preg_match( '/^https?:\/\/localhost/', $url ) ) {
				// setup the webhooks
				$webhook = $this->client->webhooks->create( [
					'url'         => $url,
					'event_types' => [
						[ 'name' => 'PAYMENT.CAPTURE.COMPLETED' ],
						[ 'name' => 'PAYMENT.CAPTURE.DENIED' ],
						[ 'name' => 'PAYMENT.CAPTURE.REFUNDED' ],
						[ 'name' => 'CHECKOUT.ORDER.APPROVED' ],
						[ 'name' => 'CHECKOUT.ORDER.PROCESSED' ],
						[ 'name' => 'CUSTOMER.DISPUTE.CREATED' ],
						[ 'name' => 'CUSTOMER.DISPUTE.RESOLVED' ]
					]
				] );
				if ( is_wp_error( $webhook ) ) {
					// enable the create webhook button since it didn't work automatically
					$this->api_settings->set_webhook_id( null, $environment );
				} else {
					$this->api_settings->set_webhook_id( $webhook->id, $environment );
				}
			} else {
				$this->log->info( 'Webhook could not be configured during connect process. PayPal does not allow webhooks where the domain is localhost.' );
			}

			unset( $this->api_settings->settings["connect_params_$environment"] );
			$this->api_settings->settings['environment'] = $environment;
			update_option( $this->api_settings->get_option_key(), $this->api_settings->settings );

			return [
				'success' => true,
				'message' => __( 'Your API credentials have been saved and your PayPal account is connected.', 'pymntpl-paypal-woocommerce' )
			];
		} catch ( \Exception $e ) {
			return new \WP_Error( 'auth-error', $e->getMessage() );
		}
	}

	public function handle_delete_request( \WP_REST_Request $request ) {
		$environment = $request['environment'];

		// delete the webhook if it exists
		if ( ( $webhook_id = $this->api_settings->get_option( "webhook_id_{$environment}" ) ) ) {
			$this->client->webhooks->delete( $webhook_id );
		}
		$keys = [ 'client_id_', 'secret_key_', 'webhook_url_', 'webhook_id_', 'access_token_' ];
		foreach ( $keys as $key ) {
			unset( $this->api_settings->settings["{$key}{$environment}"] );
		}
		update_option( $this->api_settings->get_option_key(), $this->api_settings->settings );

		return [ 'success' => true ];
	}

}