<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\Address;
use PaymentPlugins\WooCommerce\PPCP\Constants;

class AddressFactroy extends AbstractFactory {

	private $lengths = [
		'address_line_1' => 300,
		'address_line_2' => 300,
		'admin_area_1'   => 300,
		'admin_area_2'   => 120,
		'postal_code'    => 60
	];

	/**
	 * @param \WC_Customer $customer
	 *
	 * @return \PaymentPlugins\PayPalSDK\Address
	 */
	public function from_customer( $prefix = 'billing' ) {
		return $this->get_address_from_object( $this->customer, $prefix );
	}

	/**
	 * @param \WC_Order $customer
	 *
	 * @return \PaymentPlugins\PayPalSDK\Address
	 */
	public function from_order( $prefix = 'billing' ) {
		return $this->get_address_from_object( $this->order, $prefix );
	}

	/**
	 * @param \WC_Order|\WC_Customer $object
	 * @param string                 $prefix
	 *
	 * @return \PaymentPlugins\PayPalSDK\Address
	 */
	private function get_address_from_object( $object, $prefix = 'billing' ) {
		$props = [];
		foreach ( Constants::ADDRESS_MAPPINGS as $key => $key2 ) {
			$key    = $prefix . '_' . $key;
			$method = "get_$key";
			if ( method_exists( $object, $method ) ) {
				$props[ $key2 ] = $object->{$method}();
			} else {
				$props[ $key2 ] = $object->get_meta( $key );
			}
			if ( isset( $this->lengths[ $key2 ] ) ) {
				$props[ $key2 ] = substr( $props[ $key2 ], 0, $this->lengths[ $key2 ] );
			}
		}

		return new Address( $props );
	}

}