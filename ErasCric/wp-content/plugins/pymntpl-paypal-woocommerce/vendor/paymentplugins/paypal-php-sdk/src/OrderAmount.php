<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Amount
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property string    currency_code
 * @property float     $value
 * @property Breakdown $breakdown
 */
class OrderAmount extends AbstractObject {

	/**
	 * @return string
	 */
	public function getCurrencyCode(): string {
		return $this->currency_code;
	}

	/**
	 * @param string $currency_code
	 */
	public function setCurrencyCode( string $currency_code ): void {
		$this->currency_code = $currency_code;
	}

	/**
	 * @return float
	 */
	public function getValue(): float {
		return $this->value;
	}

	/**
	 * @param float $value
	 */
	public function setValue( float $value ): void {
		$this->value = $value;
	}

	/**
	 * @return Breakdown
	 */
	public function getBreakdown(): Breakdown {
		return $this->breakdown;
	}

	/**
	 * @param Breakdown $breakdown
	 */
	public function setBreakdown( Breakdown $breakdown ): void {
		$this->breakdown = $breakdown;
	}

}