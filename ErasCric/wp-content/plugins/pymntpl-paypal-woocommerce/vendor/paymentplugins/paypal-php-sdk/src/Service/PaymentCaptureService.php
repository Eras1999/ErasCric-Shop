<?php


namespace PaymentPlugins\PayPalSDK\Service;


use PaymentPlugins\PayPalSDK\Capture;
use PaymentPlugins\PayPalSDK\Payment;
use PaymentPlugins\PayPalSDK\Refund;

class PaymentCaptureService extends BaseService {

	protected $path = 'v2/payments';

	/**
	 * @param $id
	 * @param $options
	 *
	 * @return Capture
	 */
	public function retrieve( $id, $options = null ) {
		return $this->get( $this->buildPath( '/captures/%s', $id ), Capture::class, null, $options );
	}

	public function refund( $id, $params = [], $options = null ) {
		return $this->post( $this->buildPath( '/captures/%s/refund', $id ), Refund::class, $params, $options );
	}

}