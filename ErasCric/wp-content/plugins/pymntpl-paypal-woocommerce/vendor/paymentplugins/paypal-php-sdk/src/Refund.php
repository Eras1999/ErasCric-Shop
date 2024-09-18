<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Refund
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property string                                           $id
 * @property Amount                                           $amount
 * @property string                                           $invoice_id
 * @property string                                           $custom_id
 * @property string                                           $status
 * @property \stdClass                                        $status_details
 * @property string                                           note_to_payer
 * @property \PaymentPlugins\PayPalSDK\SellerPayableBreakdown $seller_payable_breakdown
 * @property string                                           $create_time
 * @property string                                           $update_time
 */
class Refund extends AbstractObject {
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
	 * @return Amount
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @param Amount $amount
	 */
	public function setAmount( $amount ) {
		$this->amount = $amount;

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
	 * @return \stdClass
	 */
	public function getStatusDetails() {
		return $this->status_details;
	}

	/**
	 * @param \stdClass $status_details
	 */
	public function setStatusDetails( $status_details ) {
		$this->status_details = $status_details;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getNoteToPayer() {
		return $this->note_to_payer;
	}

	/**
	 * @param string $note_to_payer
	 */
	public function setNoteToPayer( $note_to_payer ) {
		$this->note_to_payer = $note_to_payer;

		return $this;
	}

	/**
	 * @return SellerPayableBreakdown
	 */
	public function getSellerPayableBreakdown() {
		return $this->seller_payable_breakdown;
	}

	/**
	 * @param SellerPayableBreakdown $seller_payable_breakdown
	 */
	public function setSellerPayableBreakdown( $seller_payable_breakdown ) {
		$this->seller_payable_breakdown = $seller_payable_breakdown;

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

}