<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\WooCommerce\PPCP\Utilities\Currency;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;

abstract class AbstractFactory {

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories
	 */
	protected $factories;

	/**
	 * @var \WC_Cart
	 */
	protected $cart;

	/**
	 * @var \WC_Customer
	 */
	protected $customer;

	/**
	 * @var \WC_Order
	 */
	protected $order;

	protected $currency;

	public function __construct( CoreFactories $factories ) {
		$this->factories = $factories;
	}

	/**
	 * @return mixed
	 */
	public function get_cart() {
		return $this->cart;
	}

	/**
	 * @param mixed $cart
	 */
	public function set_cart( $cart ): void {
		$this->cart = $cart;
	}

	/**
	 * @return mixed
	 */
	public function get_customer() {
		return $this->customer;
	}

	/**
	 * @param mixed $customer
	 */
	public function set_customer( $customer ): void {
		$this->customer = $customer;
	}

	/**
	 * @return mixed
	 */
	public function get_order() {
		return $this->order;
	}

	/**
	 * @param mixed $order
	 */
	public function set_order( $order ): void {
		$this->order = $order;
	}

	/**
	 * @return mixed
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * @param mixed $currency
	 */
	public function set_currency( $currency ): void {
		$this->currency = $currency;
	}

	public function display_prices_including_tax() {
		static $incl_tax = null;
		if ( null === $incl_tax ) {
			$cart = WC()->cart;
			if ( method_exists( $cart, 'display_prices_including_tax' ) ) {
				$incl_tax = $this->cart->display_prices_including_tax();
			} elseif ( is_callable( [ $cart, 'get_tax_price_display_mode' ] ) ) {
				$incl_tax = 'incl' == $this->cart->get_tax_price_display_mode() && ( $this->customer && ! $this->customer->is_vat_exempt() );
			} else {
				$incl_tax = 'incl' == $this->cart->tax_display_cart && ( $this->customer && ! $this->customer->is_vat_exempt() );
			}
		}

		//return false;
		return $incl_tax;
	}

	public function get_order_total_fees() {
		if ( method_exists( $this->order, 'get_total_fees' ) ) {
			return $this->order->get_total_fees();
		}

		return array_reduce(
			$this->order->get_fees(),
			function ( $carry, $item ) {
				return $carry + $item->get_total();
			}
		);
	}

	protected function round( $total, $decimals = 2 ) {
		return NumberUtil::round_incl_currency( $total, $this->currency, $decimals );
	}

}