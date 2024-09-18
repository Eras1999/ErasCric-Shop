<?php


namespace PaymentPlugins\PayPalSDK\Service;

/**
 * Class PartnerService
 *
 * @package PaymentPlugins\PayPalSDK\Service
 */
class PartnerService extends BaseService {

	protected $path = 'v1/customer/partners';

	public function fetchCredentials( $options = null ) {
		return $this->get( $this->buildPath( '/%s/merchant-integrations/credentials/', $this->getClient()->getMerchantId() ), null, null, $options );
	}

}