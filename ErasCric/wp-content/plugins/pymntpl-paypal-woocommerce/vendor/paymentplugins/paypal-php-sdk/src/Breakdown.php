<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Breakdown
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property Money $item_total
 * @property Money $shipping
 * @property Money $handling
 * @property Money $tax_total
 * @property Money $insurance
 * @property Money $shipping_discount
 * @property Money $discount
 */
class Breakdown extends AbstractObject {

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	public function getItemTotal() {
		return $this->item_total;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Money $item_total
	 */
	public function setItemTotal( $item_total ) {
		$this->item_total = $item_total;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	public function getShipping() {
		return $this->shipping;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Money $shipping
	 */
	public function setShipping( $shipping ) {
		$this->shipping = $shipping;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	public function getHandling() {
		return $this->handling;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Money $handling
	 */
	public function setHandling( $handling ) {
		$this->handling = $handling;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	public function getTaxTotal() {
		return $this->tax_total;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Money $tax_total
	 */
	public function setTaxTotal( $tax_total ) {
		$this->tax_total = $tax_total;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	public function getInsurance() {
		return $this->insurance;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Money $insurance
	 */
	public function setInsurance( $insurance ) {
		$this->insurance = $insurance;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	public function getShippingDiscount() {
		return $this->shipping_discount;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Money $shipping_discount
	 */
	public function setShippingDiscount( $shipping_discount ) {
		$this->shipping_discount = $shipping_discount;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	public function getDiscount() {
		return $this->discount;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Money $discount
	 */
	public function setDiscount( $discount ) {
		$this->discount = $discount;

		return $this;
	}

	public function getTotal() {
		$total = 0;
		if ( $this->getItemTotal() ) {
			$total += $this->getItemTotal()->getValue();
		}
		if ( $this->getShipping() ) {
			$total += $this->getShipping()->getValue();
		}
		if ( $this->getTaxTotal() ) {
			$total += $this->getTaxTotal()->getValue();
		}
		if ( $this->getHandling() ) {
			$total += $this->getHandling()->getValue();
		}
		if ( $this->getInsurance() ) {
			$total += $this->getInsurance()->getValue();
		}
		if ( $this->getShippingDiscount() ) {
			$total -= $this->getShippingDiscount()->getValue();
		}
		if ( $this->getDiscount() ) {
			$total -= $this->getDiscount()->getValue();
		}

		return $total;
	}

}