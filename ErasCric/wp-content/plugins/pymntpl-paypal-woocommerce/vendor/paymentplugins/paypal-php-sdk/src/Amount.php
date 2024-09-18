<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Amount
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property string    currency_code
 * @property string    $value
 * @property float     $total
 * @property string    currency
 * @property Breakdown $breakdown
 */
class Amount extends AbstractObject {

	/**
	 * @return string
	 */
	public function getCurrencyCode() {
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
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue( $value ) {
		$this->value = $value;

		return $this;
	}

	/**
	 * @return float
	 */
	public function getTotal() {
		return $this->total;
	}

	/**
	 * @param float $total
	 */
	public function setTotal( $total ) {
		$this->total = $total;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @param string $currency
	 */
	public function setCurrency( $currency ) {
		$this->currency = $currency;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Breakdown
	 */
	public function getBreakdown() {
		return $this->breakdown;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Breakdown $breakdown
	 */
	public function setBreakdown( $breakdown ) {
		$this->breakdown = $breakdown;

		return $this;
	}


	/**
	 * @return bool
	 */
	public function amountEqualsBreakdown() {
		if ( $this->getBreakdown() ) {
			return (string) $this->getValue() == (string) $this->getBreakdown()->getTotal();
		}

		return false;
	}

}