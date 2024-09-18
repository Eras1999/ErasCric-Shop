<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\Amount;
use PaymentPlugins\PayPalSDK\Breakdown;
use PaymentPlugins\PayPalSDK\Money;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;

class BreakdownFactory extends AbstractFactory {

	/**
	 * @param \WC_Cart $cart
	 *
	 * @return \PaymentPlugins\PayPalSDK\Breakdown
	 */
	public function from_cart() {
		return ( new Breakdown() )->setItemTotal( $this->get_cart_items_total() )
		                          ->setShipping( $this->get_cart_shipping_total() )
		                          ->setTaxTotal( $this->get_cart_tax_total() )
		                          ->setDiscount( $this->get_cart_discount_total() )
		                          ->setHandling( $this->get_handling() );
	}

	public function from_order() {
		return ( new Breakdown() )
			->setItemTotal( $this->get_order_items_total() )
			->setShipping( $this->get_order_shipping_total() )
			->setTaxTotal( $this->get_order_tax_total() )
			->setDiscount( $this->get_order_discount_total() )
			->setHandling( $this->get_handling() );
	}

	/**
	 * @param \WC_Cart $cart
	 *
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	protected function get_cart_items_total() {
		$incl_tax = $this->display_prices_including_tax();
		$fees     = [ $this->cart->get_fee_total(), $this->cart->get_fee_tax() ];
		if ( $incl_tax ) {
			$item_total = $this->cart->get_subtotal() + $this->cart->get_subtotal_tax();
			if ( $fees[0] > 0 ) {
				$item_total += array_sum( $fees );
			}
		} else {
			$item_total = $this->cart->get_subtotal();
			if ( $fees[0] > 0 ) {
				$item_total += $fees[0];
			}
		}

		return ( new Money() )->setCurrencyCode( $this->currency )
		                      ->setValue( $this->round( $item_total ) );
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Money
	 */
	protected function get_cart_shipping_total() {
		$shipping_total = new Amount();
		$shipping_total->setCurrencyCode( $this->currency );
		if ( $this->cart->needs_shipping() ) {
			$incl_tax = $this->display_prices_including_tax();
			$total    = $this->cart->get_shipping_total();
			if ( $incl_tax ) {
				$total += $this->cart->get_shipping_tax();
			}
			$shipping_total->setValue( $this->round( $total, 2 ) );
		} else {
			$shipping_total->setValue( 0 );
		}

		return $shipping_total;
	}

	/**
	 * @param \WC_Cart $cart
	 *
	 * @return Money
	 */
	protected function get_cart_tax_total() {
		$total = $this->display_prices_including_tax() ? 0 : $this->cart->get_total_tax();

		return ( new Money() )
			->setValue( $this->round( $total ) )
			->setCurrencyCode( $this->currency );
	}

	/**
	 * @param \WC_Cart $cart
	 *
	 * @return Money
	 */
	protected function get_cart_discount_total() {
		$incl_tax = $this->display_prices_including_tax();
		$total    = abs( $this->cart->get_discount_total() );
		if ( 0 < $total ) {
			$total = $incl_tax ? $total + abs( $this->cart->get_discount_tax() ) : $total;
		}

		// make sure negative fees get added as a discount
		foreach ( $this->cart->get_fees() as $fee ) {
			if ( $fee->total < 0 ) {
				$total += $incl_tax ? abs( $fee->total + $fee->tax ) : abs( $fee->total );
			}
		}

		return ( new Money() )
			->setValue( $this->round( $total, 2 ) )
			->setCurrencyCode( $this->currency );
	}

	protected function get_order_items_total() {
		$total = $this->order->get_subtotal() + $this->get_order_total_fees();

		return ( new Money() )->setCurrencyCode( $this->currency )->setValue( $this->round( $total ) );
	}

	public function get_order_shipping_total() {
		return ( new Money() )->setCurrencyCode( $this->currency )->setValue( $this->round( $this->order->get_shipping_total() ) );
	}

	public function get_order_tax_total() {
		return ( new Money() )->setCurrencyCode( $this->currency )->setValue( $this->round( $this->order->get_total_tax() ) );
	}

	public function get_order_discount_total() {
		return ( new Money() )->setCurrencyCode( $this->currency )->setValue( $this->round( $this->order->get_discount_total() ) );
	}

	public function get_handling() {
		return ( new Money() )->setCurrencyCode( $this->currency )->setValue( $this->round( 0 ) );
	}

}