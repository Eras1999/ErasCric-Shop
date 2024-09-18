<?php


namespace PaymentPlugins\PayPalSDK\Service;


use PaymentPlugins\PayPalSDK\BillingAgreementToken;

class BillingAgreementTokenService extends BaseService {

	protected $path = 'v1/billing-agreements/agreement-tokens';

	/**
	 * @param $params
	 * @param array $options
	 *
	 * @return BillingAgreementToken
	 */
	public function create( $params, $options = [] ) {
		return $this->post( $this->path, BillingAgreementToken::class, $params, $options );
	}

	public function retrieve( $id, $options = [] ) {
		return $this->get( $this->buildPath( '/%s', $id ), BillingAgreementToken::class, null, $options );
	}
}