<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class ShippingOption
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property string $id
 * @property string $label
 * @property string $type
 * @property Amount $amount
 * @property bool   $selected
 */
class ShippingOption extends AbstractObject {

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel( $label ) {
		$this->label = $label;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType( $type ) {
		$this->type = $type;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Amount
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Amount $amount
	 */
	public function setAmount( $amount ) {
		$this->amount = $amount;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSelected() {
		return $this->selected;
	}

	/**
	 * @param bool $selected
	 */
	public function setSelected( $selected ) {
		$this->selected = $selected;

		return $this;
	}

}