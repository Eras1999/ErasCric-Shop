<?php


namespace PaymentPlugins\PayPalSDK\Service;


use PaymentPlugins\PayPalSDK\Client\AbstractClient;
use PaymentPlugins\PayPalSDK\Client\BaseHttpClient;
use PaymentPlugins\PayPalSDK\Client\ClientInterface;
use PaymentPlugins\PayPalSDK\Exception\AccessTokenExpiredException;
use PaymentPlugins\PayPalSDK\Utils;

abstract class BaseService {

	protected $path = 'v2/checkout';

	/**
	 * @var BaseHttpClient
	 */
	private $client;

	public function __construct( $client ) {
		$this->client = $client;
	}

	public function getClient() {
		return $this->client;
	}

	public function request( $method, $path, $responseClass = null, $params = null, $options = null ) {
		return $this->client->request( $method, $path, $responseClass, $params, $options );
	}

	public function post( $path, $responseClass = null, $params = null, $options = null ) {
		return $this->request( 'POST', $path, $responseClass, $params, $options );
	}

	public function patch( $path, $responseClass = null, $params = null, $options = null ) {
		return $this->request( 'PATCH', $path, $responseClass, $params, $options );
	}

	public function get( $path, $responseClass = null, $params = null, $options = null ) {
		return $this->request( 'GET', $path, $responseClass, $params, $options );
	}

	public function put( $path, $responseClass = null, $params = null, $options = null ) {
		return $this->request( 'PUT', $path, $responseClass, $params, $options );
	}

	protected function buildPath( $path, ...$args ) {
		$path = sprintf( $path, ...$args );
		if ( $this->path ) {
			$path = rtrim( $this->path, '/\\' ) . $path;
		}

		return $path;
	}

}