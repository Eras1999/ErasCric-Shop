<?php

namespace PaymentPlugins\WooCommerce\PPCP\Utilities;

use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\FeeCalculation;

class PayPalFee {

	/**
	 * @param \WC_Order                                           $order
	 * @param \PaymentPlugins\PayPalSDK\SellerReceivableBreakdown $breakdown
	 */
	public static function add_fee_to_order( $order, $breakdown, $save = true ) {
		$calculation = new FeeCalculation( $order );
		$calculation->calculate_from_receivable_breakdown( $breakdown );
		if ( $save ) {
			$calculation->save();
		}
	}

	/**
	 * @param \WC_Order $order
	 */
	public static function display_fee( $order ) {
		$calculation = new FeeCalculation( $order );
		if ( is_numeric( $calculation->fee ) ) {
			return wc_price( - 1 * $calculation->fee, array( 'currency' => $order->get_currency() ) );
		}

		return null;
	}

	/**
	 * @param \WC_Order $order
	 */
	public static function display_net( $order ) {
		$calculation = new FeeCalculation( $order );
		if ( is_numeric( $calculation->net ) ) {
			return wc_price( $calculation->net, array( 'currency' => $order->get_currency() ) );
		}

		return null;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Refund $refund
	 * @param \WC_Order                        $order
	 */
	public static function update_net( $refund, $order, $save = true ) {
		self::update_net_from_refund( $refund, $order, $save );
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Refund $refund
	 * @param \WC_Order                        $order
	 * @param bool                             $save
	 *
	 * @return void
	 */
	public static function update_net_from_refund( $refund, $order, $save = true ) {
		if ( isset( $refund->seller_payable_breakdown ) ) {
			$amount           = (float) $refund->seller_payable_breakdown->total_refunded_amount->value;
			$calculation      = new FeeCalculation( $order );
			$calculation->net = (float) $order->get_total() - $calculation->fee - $amount;
			if ( $save ) {
				$calculation->save();
			}
		}
	}

}