<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class PaymentSource
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property \PaymentPlugins\PayPalSDK\Token $token
 */
class PaymentSource extends AbstractObject {

	/**
	 * @return \PaymentPlugins\PayPalSDK\Token
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Token $token
	 */
	public function setToken( $token ) {
		$this->token = $token;

		return $this;
	}

}