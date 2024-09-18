<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\PayPalSDK\OAuthSignUp;

class ConnectService extends \PaymentPlugins\PayPalSDK\Service\BaseService {

	protected $path = 'v1';

	/**
	 * @param $params
	 * @param null $options
	 *
	 * @return ConnectResponse
	 */
	public function createLinks( $params, $options = null ) {
		return $this->post( $this->buildPath( '/customer' ), ConnectResponse::class, $params, $options );

	}

	/**
	 * @param $params
	 * @param null $options
	 *
	 * @return mixed|object
	 */
	public function retrieveStatus( $params, $options = null ) {
		return $this->post( $this->buildPath( '/status' ), null, $params, $options );
	}

	public function getRedirectUrl() {
		return $this->getClient()->getRequestUrl( $this->buildPath( '/redirect' ) );
	}
}