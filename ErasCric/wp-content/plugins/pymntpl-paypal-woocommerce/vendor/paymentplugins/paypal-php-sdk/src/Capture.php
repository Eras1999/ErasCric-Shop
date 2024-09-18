<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Capture
 * @package PaymentPlugins\PayPalSDK
 * @property string $status
 * @property StatusDetails $status_details
 * @property string $id
 * @property Amount $amount
 * @property string $invoice_id
 * @property string $custom_id
 * @property SellerProtection $seller_protection
 * @property bool $final_capture
 * @property SellerReceivableBreakdown $seller_receivable_breakdown
 * @property string $disbursement_mode
 * @property Collection $links
 * @property ProcessorResponse $processor_response
 */
class Capture extends AbstractObject {

	const COMPLETED = 'COMPLETED';

	const DECLINED = 'DECLINED';

	const PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';

	const PENDING = 'PENDING';

	const REFUNDED = 'REFUNDED';

	const FAILED = 'FAILED';

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
	 * @return \PaymentPlugins\PayPalSDK\StatusDetails
	 */
	public function getStatusDetails() {
		return $this->status_details;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\StatusDetails $status_details
	 */
	public function setStatusDetails( $status_details ) {
		$this->status_details = $status_details;

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

	public function getCustomId() {
		return $this->custom_id;
	}

	public function setCustomId( $custom_id ) {
		$this->custom_id = $custom_id;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\SellerProtection
	 */
	public function getSellerProtection() {
		return $this->seller_protection;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\SellerProtection $seller_protection
	 */
	public function setSellerProtection( $seller_protection ) {
		$this->seller_protection = $seller_protection;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isFinalCapture() {
		return $this->final_capture;
	}

	/**
	 * @param bool $final_capture
	 */
	public function setFinalCapture( $final_capture ) {
		$this->final_capture = $final_capture;

		return $this;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\SellerReceivableBreakdown
	 */
	public function getSellerReceivableBreakdown() {
		return $this->seller_receivable_breakdown;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\SellerReceivableBreakdown $seller_receivable_breakdown
	 */
	public function setSellerReceivableBreakdown( $seller_receivable_breakdown ) {
		$this->seller_receivable_breakdown = $seller_receivable_breakdown;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDisbursementMode() {
		return $this->disbursement_mode;
	}

	/**
	 * @param string $disbursement_mode
	 */
	public function setDisbursementMode( $disbursement_mode ) {
		$this->disbursement_mode = $disbursement_mode;

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
	 * @return \PaymentPlugins\PayPalSDK\ProcessorResponse
	 */
	public function getProcessorResponse() {
		return $this->processor_response;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\ProcessorResponse $processor_response
	 */
	public function setProcessorResponse( $processor_response ) {
		$this->processor_response = $processor_response;

		return $this;
	}

}