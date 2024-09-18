<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Shipping
 *
 * @package PaymentPlugins\PayPalSDK
 * @property Name       $name
 * @property string     $type
 * @property Address    $address
 * @property Collection $options
 */
class Shipping extends AbstractObject {

	/**
	 * @return \PaymentPlugins\PayPalSDK\Name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Name $name
	 */
	public function setName( $name ) {
		$this->name = $name;

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
	 * @return \PaymentPlugins\PayPalSDK\Address
	 */
	public function getAddress() {
		return $this->address;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Address $address
	 */
	public function setAddress( $address ) {
		$this->address = $address;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Collection
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Collection $options
	 */
	public function setOptions( $options ) {
		$this->options = $options;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\ShippingOption
	 */
	public function getSelectedOption() {
		$option  = null;
		$options = $this->getOptions();
		if ( $options ) {
			foreach ( $options as $opt ) {
				if ( $opt->isSelected() ) {
					$option = $opt;
					break;
				}
			}
		}

		return $option;
	}

}