<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Payer
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property Name      $name
 * @property Phone     $phone
 * @property string    email_address
 * @property string    $payer_id
 * @property string    $birth_date
 * @property Address   $address
 * @property PayerInfo $payer_info
 */
class Payer extends AbstractObject {

	/**
	 * @return \PaymentPlugins\PayPalSDK\Name
	 */
	public function getName(): Name {
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
	 * @return \PaymentPlugins\PayPalSDK\Phone
	 */
	public function getPhone(): Phone {
		return $this->phone;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Phone $phone
	 */
	public function setPhone( $phone ) {
		$this->phone = $phone;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmailAddress(): string {
		return $this->email_address;
	}

	/**
	 * @param string $email_address
	 */
	public function setEmailAddress( $email_address ) {
		$this->email_address = $email_address;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPayerId(): string {
		return $this->payer_id;
	}

	/**
	 * @param string $payer_id
	 */
	public function setPayerId( $payer_id ) {
		$this->payer_id = $payer_id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBirthDate(): string {
		return $this->birth_date;
	}

	/**
	 * @param string $birth_date
	 */
	public function setBirthDate( $birth_date ) {
		$this->birth_date = $birth_date;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Address
	 */
	public function getAddress(): Address {
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
	 * @return \PaymentPlugins\PayPalSDK\PayerInfo
	 */
	public function getPayerInfo(): PayerInfo {
		return $this->payer_info;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\PayerInfo $payer_info
	 */
	public function setPayerInfo( $payer_info ) {
		$this->payer_info = $payer_info;

		return $this;
	}

	public function getPatchPath( $path ) {
		return "/payer{$path}";
	}

}