<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Name
 * @package PaymentPlugins\PayPalSDK
 *
 * @property string $given_name
 * @property string $surname
 * @property string $full_name
 */
class Name extends AbstractObject {

	/**
	 * @return string
	 */
	public function getGivenName(): string {
		return $this->given_name;
	}

	/**
	 * @param string $given_name
	 */
	public function setGivenName( $given_name ) {
		$this->given_name = $given_name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSurname(): string {
		return $this->surname;
	}

	/**
	 * @param string $surname
	 */
	public function setSurname( $surname ) {
		$this->surname = $surname;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFullName(): string {
		return $this->full_name;
	}

	/**
	 * @param string $full_name
	 */
	public function setFullName( $full_name ) {
		$this->full_name = $full_name;

		return $this;
	}

}