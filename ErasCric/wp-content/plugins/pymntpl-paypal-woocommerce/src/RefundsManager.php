<?php

namespace PaymentPlugins\WooCommerce\PPCP;

class RefundsManager {

	public $refund;

	public function __construct() {
		add_action( 'woocommerce_create_refund', [ $this, 'set_current_refund' ] );
	}

	/**
	 * @param \WC_Order_Refund $refund
	 */
	public function set_current_refund( $refund ) {
		$this->refund = $refund;
	}

}