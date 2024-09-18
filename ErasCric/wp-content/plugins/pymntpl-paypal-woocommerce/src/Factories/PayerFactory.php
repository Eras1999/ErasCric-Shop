<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\Name;
use PaymentPlugins\PayPalSDK\Payer;

class PayerFactory extends AbstractFactory {

	/**
	 * @return \PaymentPlugins\PayPalSDK\Payer
	 */
	public function from_customer() {
		$payer = ( new Payer() )
			->setName( ( new Name() )
				->setGivenName( $this->customer->get_billing_first_name() )
				->setSurname( $this->customer->get_billing_last_name() ) )
			->setAddress( $this->factories->address->from_customer( 'billing' ) );
		if ( ( $email = $this->customer->get_billing_email() ) ) {
			$payer->setEmailAddress( $email );
		}

		return $payer;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Payer
	 */
	public function from_order() {
		$payer = ( new Payer() )
			->setName( ( new Name() )
				->setGivenName( $this->order->get_billing_first_name() )
				->setSurname( $this->order->get_billing_last_name() ) )
			->setAddress( $this->factories->address->from_order( 'billing' ) );
		if ( ( $email = $this->order->get_billing_email() ) ) {
			$payer->setEmailAddress( $email );
		}

		return $payer;
	}

}