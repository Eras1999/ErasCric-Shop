<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * @property \PaymentPlugins\PayPalSDK\OrderApplicationContext $application_context
 * @property string $id
 * @property string $intent
 * @property PaymentSource $payment_source
 * @property Payer $payer
 * @property Collection $purchase_units
 * @property string $status
 * @property Collection $links
 * @property string $create_time
 * @property string $update_time
 *
 * Class Order
 * @package PaymentPlugins\PayPalSDK
 */
class Order extends AbstractObject {

	const CREATED = 'CREATED';

	const CAPTURE = 'CAPTURE';

	const AUTHORIZE = 'AUTHORIZE';

	const APPROVED = 'APPROVED';

	const COMPLETED = 'COMPLETED';

	const PAYER_ACTION_REQUIRED = 'PAYER_ACTION_REQUIRED';

	/**
	 * @return \PaymentPlugins\PayPalSDK\OrderApplicationContext
	 */
	public function getApplicationContext() {
		return $this->application_context;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\OrderApplicationContext $application_context
	 */
	public function setApplicationContext( $application_context ) {
		$this->application_context = $application_context;

		return $this;
	}

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
	public function getIntent() {
		return $this->intent;
	}

	/**
	 * @param string $intent
	 */
	public function setIntent( $intent ) {
		$this->intent = strtoupper( $intent );

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\PaymentSource
	 */
	public function getPaymentSource() {
		return $this->payment_source;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\PaymentSource $payment_source
	 */
	public function setPaymentSource( $payment_source ) {
		$this->payment_source = $payment_source;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Payer
	 */
	public function getPayer() {
		return $this->payer;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Payer $payer
	 */
	public function setPayer( $payer ) {
		$this->payer = $payer;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Collection
	 */
	public function getPurchaseUnits() {
		return $this->purchase_units;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Collection $purchase_units
	 */
	public function setPurchaseUnits( $purchase_units ) {
		$this->purchase_units = $purchase_units;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus( $status ) {
		$this->status = $status;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Collection
	 */
	public function getLinks() {
		return $this->links;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Collection $links
	 */
	public function setLinks( $links ) {
		$this->links = $links;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCreateTime() {
		return $this->create_time;
	}

	/**
	 * @param string $create_time
	 */
	public function setCreateTime( $create_time ) {
		$this->create_time = $create_time;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUpdateTime() {
		return $this->update_time;
	}

	/**
	 * @param string $update_time
	 */
	public function setUpdateTime( $update_time ) {
		$this->update_time = $update_time;

		return $this;
	}

	public function isApproved() {
		return $this->status === self::APPROVED;
	}

	public function isComplete() {
		return $this->status === self::COMPLETED;
	}

	public function isCreated() {
		return $this->status === self::CREATED;
	}

	/**
	 * return true if the order requires the payer to take an action.
	 *
	 * @return bool
	 */
	public function isActionRequired() {
		return $this->status === self::PAYER_ACTION_REQUIRED;
	}

}