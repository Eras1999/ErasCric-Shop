<?php

namespace PaymentPlugins\PayPalSDK\Service;

use PaymentPlugins\PayPalSDK\Collection;
use PaymentPlugins\PayPalSDK\Utils;

class PaymentTokenService extends BaseService {

	protected $path = 'v2/vault';

	/**
	 * @param array $params customer_id
	 * @param array $options
	 *
	 * @return mixed|object|void
	 */
	public function all( $params = [], $options = [] ) {
		$response = $this->get( $this->buildPath( '/payment-tokens' ), null, $params, $options );

		return Utils::convertResponseToObject( Collection::class, $response->payment_tokens );
	}

}