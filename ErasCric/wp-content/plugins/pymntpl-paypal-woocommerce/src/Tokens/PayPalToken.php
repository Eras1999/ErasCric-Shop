<?php

namespace PaymentPlugins\WooCommerce\PPCP\Tokens;

use PaymentPlugins\PayPalSDK\Payer;
use PaymentPlugins\PayPalSDK\PayerInfo;

class PayPalToken extends AbstractToken {

	protected $type = 'PPCP';

	public function get_payment_method_formats() {
		return apply_filters( 'wc_ppcp_payment_method_formats', [
			'name'       => [
				'format'  => __( 'PayPal', 'pymntpl-paypal-woocommerce' ),
				'example' => __( 'PayPal', 'pymntpl-paypal-woocommerce' ),
				'label'   => __( 'PayPal', 'pymntpl-paypal-woocommerce' )
			],
			'name_email' => [
				'format'  => __( 'PayPal', 'pymntpl-paypal-woocommerce' ) . ' - {email}',
				'example' => __( 'PayPal - john@paypal.com', 'pymntpl-paypal-woocommerce' ),
				'label'   => __( 'Name plus email', 'pymntpl-paypal-woocommerce' )
			]
		], $this );
	}

	protected function get_default_format() {
		return 'name_email';
	}

	/**
	 * @param Payer|PayerInfo $payer
	 *
	 * @return mixed|void
	 */
	public function initialize_from_payer( $payer ) {
		if ( $payer instanceof Payer ) {
			$this->set_first_name( $payer->name->given_name );
			$this->set_last_name( $payer->name->surname );
			$this->set_email( $payer->email_address );
			$this->set_payer_id( $payer->payer_id );
		} elseif ( $payer instanceof PayerInfo ) {
			$this->set_first_name( $payer->first_name );
			$this->set_last_name( $payer->last_name );
			$this->set_email( $payer->email );
			$this->set_payer_id( $payer->payer_id );
		}
	}
}