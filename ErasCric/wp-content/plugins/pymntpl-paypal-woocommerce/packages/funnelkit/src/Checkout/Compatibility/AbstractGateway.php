<?php

namespace PaymentPlugins\PPCP\FunnelKit\Checkout\Compatibility;

class AbstractGateway {

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway
	 */
	private $payment_gateway;

	public function __construct( $payment_gateway ) {
		$this->payment_gateway = $payment_gateway;
	}

	public function is_active() {
		return $this->payment_gateway->is_available();
	}

	public function is_express_enabled() {
		return $this->payment_gateway->is_section_enabled( 'express_checkout' );
	}

}