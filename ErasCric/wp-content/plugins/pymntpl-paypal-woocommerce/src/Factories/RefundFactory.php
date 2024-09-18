<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\Amount;
use PaymentPlugins\PayPalSDK\Refund;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;

class RefundFactory extends AbstractFactory {

	private $settings;

	public function __construct( AdvancedSettings $settings, CoreFactories $factories ) {
		parent::__construct( $factories );
		$this->settings = $settings;
	}

	/**
	 * @param \WC_Order_Refund $order_refund
	 *
	 * @return void
	 */
	public function from_refund( $order_refund, $amount, $reason = '' ) {
		$this->set_currency( $order_refund->get_currency() );
		$refund = new Refund();
		$refund->setAmount(
			( new Amount() )
				->setValue( $this->round( $amount ) )
				->setCurrencyCode( $this->get_currency() )
		);
		if ( $reason ) {
			$refund->setNoteToPayer( $reason );
		}
		$refund->setInvoiceId( trim( $this->settings->get_option( 'order_prefix' ) . $order_refund->get_id() ) );

		return apply_filters( 'wc_ppcp_refund_factory_from_order_refund', $refund, $order_refund, $this );
	}

}