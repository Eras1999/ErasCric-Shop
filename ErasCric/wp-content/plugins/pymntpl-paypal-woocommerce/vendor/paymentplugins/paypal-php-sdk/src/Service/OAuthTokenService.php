<?php


namespace PaymentPlugins\PayPalSDK\Service;


use PaymentPlugins\PayPalSDK\OAuthToken;
use PaymentPlugins\PayPalSDK\Utils;

/**
 * Class OAuthTokenService
 * @package PaymentPlugins\PayPalSDK\Service
 */
class OAuthTokenService extends BaseService {

	protected $path = 'v1/oauth2';

	/**
	 * @param string $shared_id
	 * @param array $params
	 *
	 * @return OAuthToken
	 */
	public function create( $shared_id, $params = [] ) {
		return $this->post( $this->buildPath( '/token' ), OAuthToken::class, $params, array( 'headers' => $this->getHeaders( $shared_id ) ) );
	}

	/**
	 * @return OAuthToken
	 */
	public function refresh() {
		return $this->create( null, [
			'grant_type' => 'client_credentials'
		] );
	}

	private function getHeaders( $shared_id = null ) {
		return array(
			'Authorization' => $shared_id ? 'Basic ' . base64_encode( $shared_id ) : $this->getClient()->getBasicAuthorizationHeader(),
			'Content-Type'  => 'application/x-www-form-urlencoded'
		);
	}
}