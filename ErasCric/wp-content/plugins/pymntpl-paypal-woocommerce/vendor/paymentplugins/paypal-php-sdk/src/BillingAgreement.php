<?php


namespace PaymentPlugins\PayPalSDK;

use PaymentPlugins\PayPalSDK\V1\Address;

/**
 * Class BillingAgreement
 * @package PaymentPlugins\PayPalSDK
 * @property string $id
 * @property string name
 * @property string $description
 * @property string $start_date
 * @property AgreementDetails $agreement_details
 * @property Payer $payer
 * @property Address $shipping_address
 *
 */
class BillingAgreement extends AbstractObject {
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
	public function getName() {
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
	 * @return string
	 */
	public function getDescription() {
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
	public function getStartDate() {
		return $this->start_date;
	}

	/**
	 * @param string $start_date
	 */
	public function setStartDate( $start_date ) {
		$this->start_date = $start_date;

		return $this;
	}

	/**
	 * @return AgreementDetails
	 */
	public function getAgreementDetails() {
		return $this->agreement_details;
	}

	/**
	 * @param AgreementDetails $agreement_details
	 */
	public function setAgreementDetails( $agreement_details ) {
		$this->agreement_details = $agreement_details;

		return $this;
	}

	/**
	 * @return Payer
	 */
	public function getPayer() {
		return $this->payer;
	}

	/**
	 * @param Payer $payer
	 */
	public function setPayer( $payer ) {
		$this->payer = $payer;

		return $this;
	}

	/**
	 * @return Address
	 */
	public function getShippingAddress() {
		return $this->shipping_address;
	}

	/**
	 * @param Address $shipping_address
	 */
	public function setShippingAddress( $shipping_address ) {
		$this->shipping_address = $shipping_address;

		return $this;
	}


}