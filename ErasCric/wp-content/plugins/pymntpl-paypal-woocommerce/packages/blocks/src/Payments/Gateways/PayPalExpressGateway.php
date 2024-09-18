<?php

namespace PaymentPlugins\PPCP\Blocks\Payments\Gateways;

class PayPalExpressGateway extends \WC_Payment_Gateway {

	public $id = 'paymentplugins_ppcp_express';

	public function __construct() {
		$this->supports = [];
	}

	public function is_available() {
		return true;
	}

}