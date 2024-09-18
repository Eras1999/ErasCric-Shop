<?php

namespace PaymentPlugins\WooCommerce\PPCP\Utilities;


class OrderLock {

	const KEY = 'wc_ppcp_order_lock_';

	/**
	 * Sets a lock on the order using transients
	 *
	 * @param $order
	 */
	public static function set_order_lock( $order, $time = null ) {
		$time = is_null( $time ) ? 2 * MINUTE_IN_SECONDS : $time;
		if ( $order instanceof \WC_Order ) {
			$id = $order->get_id();
		} else {
			$id = $order;
		}
		set_transient( self::KEY . $id, $id, $time );
	}

	/**
	 * Releases the order lock
	 *
	 * @param $order
	 */
	public static function release_order_lock( $order ) {
		if ( $order instanceof \WC_Order ) {
			$id = $order->get_id();
		} else {
			$id = $order;
		}
		delete_transient( self::KEY . $id );
	}

	/**
	 * Returns true if the order has a lock
	 *
	 * @param $order
	 *
	 * @return bool
	 */
	public static function has_order_lock( $order ) {
		if ( $order instanceof \WC_Order ) {
			$id = $order->get_id();
		} else {
			$id = $order;
		}

		return get_transient( self::KEY . $id ) !== false;
	}

}