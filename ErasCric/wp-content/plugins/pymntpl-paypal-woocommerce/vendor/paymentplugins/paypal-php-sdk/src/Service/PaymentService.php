<?php


namespace PaymentPlugins\PayPalSDK\Service;

use PaymentPlugins\PayPalSDK\Payment;
use PaymentPlugins\PayPalSDK\Refund;

/**
 * Class PaymentService
 * @package PaymentPlugins\PayPalSDK\Service
 */
class PaymentService extends BaseService {

	protected $path = 'v2/payments';

	public function retrieve( $id, $options = null ) {
		return $this->get( $this->buildPath( '/authorizations/%s', $id ), Payment::class, null, $options );
	}

	public function capture( $id, $options = null ) {
		return $this->post( $this->buildPath( '/authorizations/%s/capture', $id ), Payment::class, null, $options );
	}

	public function refund( $id, $params = [], $options = null ) {
		return $this->post( $this->buildPath( '/captures/%s/refund', $id ), Refund::class, $params, $options );
	}

	public function void( $id, $options = null ) {
		return $this->post( $this->buildPath( '/authorizations/%s/void', $id ), Payment::class, null, $options );
	}
}