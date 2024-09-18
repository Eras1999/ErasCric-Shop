<?php

namespace PaymentPlugins\PPCP\FunnelKit\Checkout;

use PaymentPlugins\WooCommerce\PPCP\Cache\CacheInterface;

class FieldMappings {

	private $cache;

	public function __construct( CacheInterface $cache ) {
		$this->cache = $cache;
		//add_filter( 'wc_ppcp_cart_order_billing_prefix', [ $this, 'get_billing_prefix' ], 10, 2 );
	}

	public function get_billing_prefix( $prefix, $request ) {
		if ( isset( $request['wfacp_billing_same_as_shipping'] ) ) {
			if ( $request['wfacp_billing_same_as_shipping'] === '0' ) {
				if ( WC()->cart && WC()->cart->needs_shipping() ) {
					$prefix = 'shipping';
				}
			}
		}

		return $prefix;
	}

}