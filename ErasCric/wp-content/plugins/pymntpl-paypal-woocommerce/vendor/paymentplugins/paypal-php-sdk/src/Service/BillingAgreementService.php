<?php


namespace PaymentPlugins\PayPalSDK\Service;


use PaymentPlugins\PayPalSDK\BillingAgreement;

class BillingAgreementService extends BaseService {

	protected $path = 'v1/billing-agreements';

	/**
	 * @param $params
	 * @param array $options
	 *
	 * @return BillingAgreement
	 */
	public function create( $params, $options = [] ) {
		return $this->post( $this->buildPath( '/agreements' ), BillingAgreement::class, $params, $options );
	}

	public function retrieve( $id, $options = [] ) {
		return $this->get( $this->buildPath( '/agreements/%s', $id ), BillingAgreement::class, null, $options );
	}
}