<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\PayPalSDK\AbstractObject;
use PaymentPlugins\PayPalSDK\Exception\ApiException;
use PaymentPlugins\PayPalSDK\Exception\BadRequestException;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;

/**
 * Class WPPayPalClient
 *
 * @package PaymentPlugins\WooCommerce\PPCP
 *
 * @property ConnectService $connect
 */
class WPPayPalClient extends \PaymentPlugins\PayPalSDK\PayPalClient {

	/**
	 * @var APISettings
	 */
	private $api_settings;

	/**
	 * @var ConnectService
	 */
	private $connect_service;

	private $current_service;

	private $logger;

	private $partner_id = 'PaymentPlugins_PCP';

	/**
	 * WPPayPalClient constructor.
	 *
	 * @param array $config
	 */
	public function __construct( APISettings $settings, Logger $logger ) {
		$this->api_settings    = $settings;
		$this->logger          = $logger;
		$this->connect_service = new ConnectService( $this );
		$this->registerExpiredTokenHandler( [ $this, 'refreshAccessToken' ] );
		$this->environment = $this->api_settings->get_option( 'environment' );
		parent::__construct( $this->getConfiguration(), new Http() );
	}

	public function __get( $name ) {
		$this->current_service = $name;
		if ( $name === 'connect' ) {
			return $this->connect_service;
		}

		return parent::__get( $name );
	}

	public function request( $method, $path, $responseClass = null, $params = null, $options = [] ) {
		try {
			return parent::request( $method, $path, $responseClass, $params, $options );
		} catch ( ApiException $e ) {
			$this->logger->error( sprintf( 'API error: %s', print_r( [
				'url'         => $this->getRequestUrl( $path ),
				'method'      => $method,
				'http_status' => $e->getCode(),
				'request'     => print_r( $params instanceof AbstractObject ? $params->toArray() : $params, true ),
				'error'       => $e->getData()
			], true ) ) );

			// return WP_Error object
			return new \WP_Error(
				$e->getErrorCode(),
				apply_filters( 'wc_ppcp_api_request_error_message', $e->getMessage(), $e ),
				[
					'status' => \is_numeric( $e->getCode() ) ? $e->getCode() : 200,
					'error'  => $e->getData()
				] );
		}
	}

	public function refreshAccessToken() {
		try {
			$token = $this->auth->refresh();
			if ( ! is_wp_error( $token ) ) {
				$this->setAccessToken( $token->access_token );
			}
		} catch ( ApiException $e ) {
			$this->logger->error( sprintf( 'Error refreshing access token. %s', $e->getMessage() ) );
		}
	}

	protected function baseUrl() {
		if ( $this->current_service === 'connect' ) {
			return $this->getConnectUrl();
		}

		return parent::baseUrl();
	}

	public function environment( $env = '' ) {
		$this->environment = ! $env ? $this->api_settings->get_environment() : $env;
		$this->initialize( $this->getConfiguration() );

		return $this;
	}

	public function orderMode( \WC_Order $order ) {
		$environment = $order->get_meta( Constants::PPCP_ENVIRONMENT );

		return $this->environment( $environment );
	}

	public function setAccessToken( $token ) {
		$this->api_settings->update_option( "access_token_{$this->environment}", $token );
		parent::setAccessToken( $token );
	}

	/**
	 * @param $client_id
	 * @param $secret
	 * @param $environment
	 */
	public function updateCredentials( $client_id, $secret, $environment ) {
		$this->updateApiSettings( [
			"client_id_{$environment}"  => $client_id,
			"secret_key_{$environment}" => $secret
		] );
		$this->initialize( [ 'client_id' => $client_id, 'secret_key' => $secret ] );
	}

	public function updateApiSettings( $options ) {
		$this->api_settings->settings = array_merge( $this->api_settings->settings, $options );
		update_option( $this->api_settings->get_option_key(), $this->api_settings->settings );
	}

	private function getConfiguration() {
		return [
			'client_id'    => $this->api_settings->get_option( "client_id_{$this->environment}" ),
			'secret_key'   => $this->api_settings->get_option( "secret_key_{$this->environment}" ),
			'access_token' => $this->api_settings->get_option( "access_token_{$this->environment}" ),
		];
	}

	public function getConnectUrl() {
		return 'https://paypalconnect.paymentplugins.com';
	}

	protected function getHeaders() {
		if ( $this->current_service === 'connect' ) {
			return [
				'Content-Type' => 'application/json',
			];
		}

		return array_merge( parent::getHeaders(), [
			'PayPal-Partner-Attribution-Id' => $this->partner_id,
		] );
	}

	public function getAPISettings() {
		return $this->api_settings;
	}

	public function handleRequestResponse( $response ) {
		if ( \is_wp_error( $response ) ) {
			throw new BadRequestException( 400, [ 'message' => $response->get_error_message() ] );
		} else {
			$status = \wp_remote_retrieve_response_code( $response );
			$body   = \wp_remote_retrieve_body( $response );
		}

		return [ $status, $body ];
	}

}