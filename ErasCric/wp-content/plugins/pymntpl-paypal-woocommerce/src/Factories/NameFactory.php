<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\Name;
use PaymentPlugins\WooCommerce\PPCP\Utils;

class NameFactory extends AbstractFactory {

	public function from_customer( $prefix = 'billing' ) {
		return $this->get_name_from_object( $this->customer, $prefix );
	}

	public function from_order( $prefix = 'billing' ) {
		return $this->get_name_from_object( $this->order, $prefix );
	}

	/**
	 * @param \WC_Order|\WC_Customer $object
	 * @param                        $prefix
	 *
	 * @return \PaymentPlugins\PayPalSDK\Name
	 */
	private function get_name_from_object( $object, $prefix ) {
		$name = sprintf( '%s %s', $object->{"get_{$prefix}_first_name"}(), $object->{"get_{$prefix}_last_name"}() );

		return ( new Name() )->setFullName( substr( $name, 0, 300 ) );
	}

}