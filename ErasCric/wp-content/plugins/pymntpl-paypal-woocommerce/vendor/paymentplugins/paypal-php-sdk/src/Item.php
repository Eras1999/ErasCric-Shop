<?php

namespace PaymentPlugins\PayPalSDK;

/**
 * @property string                          $name
 * @property \PaymentPlugins\PayPalSDK\Money $unit_amount
 * @property \PaymentPlugins\PayPalSDK\Money $tax
 * @property string                          $quantity
 * @property string                          $description
 * @property string                          $sku
 * @property string                          $category
 */
class Item extends AbstractObject {

	const DIGITAL_GOODS = 'DIGITAL_GOODS';

	const PHYSICAL_GOODS = 'PHYSICAL_GOODS';

	const DONATION = 'DONATION';

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	public function getUnitAmount(): Money {
		return $this->unit_amount;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Money $unit_amount
	 */
	public function setUnitAmount( $unit_amount ) {
		$this->unit_amount = $unit_amount;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	public function getTax(): Money {
		return $this->tax;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Money $tax
	 */
	public function setTax( $tax ) {
		$this->tax = $tax;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getQuantity(): string {
		return $this->quantity;
	}

	/**
	 * @param string $quantity
	 */
	public function setQuantity( $quantity ) {
		$this->quantity = $quantity;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSku(): string {
		return $this->sku;
	}

	/**
	 * @param string $sku
	 */
	public function setSku( $sku ) {
		$this->sku = $sku;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCategory(): string {
		return $this->category;
	}

	/**
	 * @param string $category
	 */
	public function setCategory( $category ) {
		$this->category = $category;

		return $this;
	}

}