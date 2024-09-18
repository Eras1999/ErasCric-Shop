<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class PurchaseUnit
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property string     $reference_id
 * @property Amount     $amount
 * @property Payee      $payee
 * @property object     $payment_instruction
 * @property string     $description
 * @property string     $custom_id
 * @property string     $invoice_id
 * @property string     $soft_descriptor
 * @property Collection $items
 * @property Shipping   $shipping
 * @property Payments   $payments
 */
class PurchaseUnit extends AbstractObject {

	const ADD = 'add';

	/**
	 * @return string
	 */
	public function getReferenceId() {
		return $this->reference_id;
	}

	/**
	 * @param string $reference_id
	 */
	public function setReferenceId( $reference_id ) {
		$this->reference_id = $reference_id;

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
	 * @return \PaymentPlugins\PayPalSDK\Payee
	 */
	public function getPayee() {
		return $this->payee;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Payee $payee
	 */
	public function setPayee( $payee ) {
		$this->payee = $payee;

		return $this;
	}

	/**
	 * @return object
	 */
	public function getPaymentInstruction() {
		return $this->payment_instruction;
	}

	/**
	 * @param object $payment_instruction
	 */
	public function setPaymentInstruction( $payment_instruction ) {
		$this->payment_instruction = $payment_instruction;

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
	public function getCustomId() {
		return $this->custom_id;
	}

	/**
	 * @param string $custom_id
	 */
	public function setCustomId( $custom_id ) {
		$this->custom_id = $custom_id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getInvoiceId() {
		return $this->invoice_id;
	}

	/**
	 * @param string $invoice_id
	 */
	public function setInvoiceId( $invoice_id ) {
		$this->invoice_id = $invoice_id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getSoftDescriptor() {
		return $this->soft_descriptor;
	}

	/**
	 * @param string $soft_descriptor
	 */
	public function setSoftDescriptor( $soft_descriptor ) {
		$this->soft_descriptor = $soft_descriptor;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Collection
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Collection $items
	 */
	public function setItems( $items ) {
		$this->items = $items;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Shipping
	 */
	public function getShipping() {
		return $this->shipping;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Shipping $shipping
	 */
	public function setShipping( $shipping ) {
		$this->shipping = $shipping;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Payments
	 */
	public function getPayments() {
		return $this->payments;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Payments $payments
	 */
	public function setPayments( $payments ) {
		$this->payments = $payments;

		return $this;
	}

	public function getPatchPath( $path ) {
		return "/purchase_units/@reference_id=='{$this->getReferenceId()}'{$path}";
	}

}