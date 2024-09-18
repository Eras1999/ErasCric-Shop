<?php

namespace PaymentPlugins\WooCommerce\PPCP\Utilities;

use PaymentPlugins\WooCommerce\PPCP\Utils;

/**
 * @since 1.0.26
 */
class OrderFilterUtil {

	/**
	 * @param \PaymentPlugins\PayPalSDK\Order $order
	 *
	 * @return void
	 */
	public static function filter_order( $order ) {
		if ( $order->getPayer() ) {
			if ( ! Utils::is_valid_address( $order->getPayer()->getAddress() ) ) {
				unset( $order->getPayer()->address );
			}
		}
	}

}