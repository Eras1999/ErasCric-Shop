<?php


namespace PaymentPlugins\PayPalSDK\Service;


use PaymentPlugins\PayPalSDK\Authorization;
use PaymentPlugins\PayPalSDK\Capture;
use PaymentPlugins\PayPalSDK\Payment;

class PaymentAuthorizationService extends BaseService {

	protected $path = 'v2/payments';

	/**
	 * @param $id
	 * @param null $options
	 *
	 * @return Authorization
	 */
	public function retrieve( $id, $options = null ) {
		return $this->get( $this->buildPath( '/authorizations/%s', $id ), Authorization::class, null, $options );
	}

	/**
	 * @param $id
	 * @param array|mixed $params
	 * @param null $options
	 *
	 * @return Capture
	 */
	public function capture( $id, $params = null, $options = null ) {
		return $this->post( $this->buildPath( '/authorizations/%s/capture', $id ), Capture::class, $params, $options );
	}

	public function void( $id, $options = null ) {
		return $this->post( $this->buildPath( '/authorizations/%s/void', $id ), null, null, $options );
	}
}