<?php

namespace PaymentPlugins\WooCommerce\PPCP;

/**
 * @property float $net
 * @property float $fee
 */
class FeeCalculation {

	/**
	 * @var \WC_Order
	 */
	private $order;

	private $props = [
		'fee' => 0,
		'net' => 0
	];

	public function __construct( $order, $new = false ) {
		$this->order = $order;
		$this->initialize( $new );
	}

	private function initialize( $new ) {
		if ( ! $new ) {
			$this->props = [
				'fee' => (float) $this->order->get_meta( Constants::PAYPAL_FEE ),
				'net' => (float) $this->order->get_meta( Constants::PAYPAL_NET )
			];
		}
	}

	public function __get( $name ) {
		if ( \method_exists( $this, "get_{$name}" ) ) {
			return $this->{"get_{$name}"}();
		}

		return $this->get_prop( $name );
	}

	public function __set( $name, $value ) {
		$this->props[ $name ] = $value;
	}

	protected function get_prop( $key, $default = 0 ) {
		if ( ! isset( $this->props[ $key ] ) ) {
			$this->props[ $key ] = $default;
		}

		return $this->props[ $key ];
	}

	public function get_net() {
		return (float) $this->get_prop( 'net', 0 );
	}

	public function get_fee() {
		return (float) $this->get_prop( 'fee', 0 );
	}

	public function save() {
		if ( $this->order ) {
			$this->populate_order();
			$this->order->save();
		}
	}

	private function populate_order() {
		$this->order->update_meta_data( Constants::PAYPAL_FEE, $this->fee );
		$this->order->update_meta_data( Constants::PAYPAL_NET, $this->net );
	}

	public function add_fee( $fee ) {
		$this->fee += (float) $fee;
	}

	public function add_net( $net ) {
		$this->net += (float) $net;
	}

	public function add_refund( $refund ) {
		$this->net -= (float) $refund;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\SellerReceivableBreakdown $breakdown
	 *
	 * @return void
	 */
	public function calculate_from_receivable_breakdown( $breakdown ) {
		if ( $breakdown ) {
			$this->add_fee( (float) $breakdown->paypal_fee->value );
			$this->add_net( (float) $breakdown->net_amount->value );
			$this->populate_order();
		}
	}

}