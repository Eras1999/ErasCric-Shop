<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Money
 * @package PaymentPlugins\PayPalSDK
 * @property string $currency_code
 * @property string $value
 */
class Money extends AbstractObject {

	/**
	 * @return string
	 */
	public function getCurrencyCode(): string {
		return $this->currency_code;
	}

	/**
	 * @param string $currency_code
	 */
	public function setCurrencyCode( $currency_code ) {
		$this->currency_code = $currency_code;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getValue(): string {
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue( $value ) {
		$this->value = $value;

		return $this;
	}

}