<?php

namespace PaymentPlugins\PayPalSDK\Service;

use PaymentPlugins\PayPalSDK\Utils;
use PaymentPlugins\PayPalSDK\V1\Tracker;

class TrackingService extends BaseService {

	protected $path = 'v1/shipping';

	/**
	 * @param $params
	 * @param $options
	 *
	 * @return Tracker
	 */
	public function create( $params, $options = null ) {
		if ( ! Utils::isList( $params ) ) {
			$params = [ $params ];
		}
		$body = [
			'trackers' => $params
		];

		return $this->post( $this->buildPath( '/trackers-batch' ), null, $body, $options );
	}

	/**
	 * @param $id
	 * @param $params
	 * @param $options
	 *
	 * @return null
	 */
	public function update( $id, $txnId, $params, $options = [] ) {
		return $this->put( $this->buildPath( '/trackers/%s-%s', $txnId, $id ), null, $params, $options );
	}

	/**
	 * @param $id
	 * @param $options
	 *
	 * @return Tracker
	 */
	public function retrieve( $id, $txnId, $options = null ) {
		return $this->get( $this->buildPath( '/trackers/%s-%s', $txnId, $id ), Tracker::class, null, $options );
	}

	public function cancel( $id, $txnId, $options = null ) {
		$tracker = new Tracker();
		$tracker->setTransactionId( $txnId );
		$tracker->setTrackingNumber( $id );
		$tracker->setStatus( Tracker::CANCELLED );

		return $this->put( $this->buildPath( '/trackers/%s-%s', $txnId, $id ), null, $tracker, $options );
	}

}