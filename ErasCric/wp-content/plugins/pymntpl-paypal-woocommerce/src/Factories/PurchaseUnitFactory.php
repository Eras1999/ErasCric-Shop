<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\Amount;
use PaymentPlugins\PayPalSDK\PurchaseUnit;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;
use PaymentPlugins\WooCommerce\PPCP\Utils;

class PurchaseUnitFactory extends AbstractFactory {

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings
	 */
	private $settings;

	public function __construct( AdvancedSettings $settings, ...$args ) {
		$this->settings = $settings;
		parent::__construct( ...$args );
	}

	public function from_cart() {
		$purchase_unit = ( new PurchaseUnit() )
			->setAmount( ( new Amount() )
				->setValue( $this->round( $this->cart->total, 2 ) )
				->setCurrencyCode( $this->currency )
				->setBreakdown( $this->factories->breakdown->from_cart() ) )
			->setItems( $this->factories->items->from_cart() );
		if ( $this->cart->needs_shipping() ) {
			$purchase_unit->setShipping( $this->factories->shipping->from_customer( 'shipping' ) );
		}
		$this->filter_purchase_unit( $purchase_unit, $this->cart->total );

		return apply_filters( 'wc_ppcp_purchase_unit_factory_from_cart', $purchase_unit, $this );
	}

	public function from_order() {
		$purchase_unit = ( new PurchaseUnit() )
			->setAmount( ( new Amount() )
				->setValue( $this->round( $this->order->get_total(), 2 ) )
				->setCurrencyCode( $this->currency )
				->setBreakdown( $this->factories->breakdown->from_order() ) )
			->setInvoiceId( $this->generate_invoice_id( $this->order->get_order_number() ) )
			->setCustomId( $this->order->get_id() )
			->setItems( $this->factories->items->from_order() )
			->setDescription( $this->get_description_from_order() );

		if ( $this->order->has_shipping_address() ) {
			$purchase_unit->setShipping( $this->factories->shipping->from_order( 'shipping' ) );
			// remove the shipping address if it's invalid.
			if ( ! Utils::is_valid_address( $purchase_unit->getShipping()->getAddress(), 'shipping' ) ) {
				unset( $purchase_unit->getShipping()->address );
			}
		}
		$this->filter_purchase_unit( $purchase_unit, $this->order->get_total() );

		return apply_filters( 'wc_ppcp_purchase_unit_factory_from_order', $purchase_unit, $this );
	}

	public function get_description_from_order() {
		return substr( sprintf( __( 'Order %1$s from %2$s', 'pymntpl-paypal-woocommerce' ), $this->order->get_order_number(), get_bloginfo( 'name' ) ), 0, 127 );
	}

	public function filter_purchase_unit( PurchaseUnit $pu, $total ) {
		// compare the breakdown amount to the items total.
		$breakdown_item_total = (float) $pu->getAmount()->getBreakdown()->getItemTotal()->getValue();
		$items_total          = array_reduce( $pu->getItems()->getValues(), function ( $totals, $item ) {
			/**
			 * @var \PaymentPlugins\PayPalSDK\Item $item
			 */
			return $totals + $this->round( $item->getUnitAmount()->getValue() * $item->getQuantity(), 2 );
		}, 0 );
		$diff                 = $this->round( $breakdown_item_total - $items_total );

		if ( abs( $diff ) >= 0.01 ) {
			if ( $diff > 0 ) {
				// add an item to the collection to ensure it matches
				$pu->getItems()->add( $this->factories->items->get_cart_item( $diff, __( 'Reconciliation', 'pymntpl-paypal-woocommerce' ), 1, null ) );
			} else {
				// The item total is greater, so you can't add a reconciliation item. Add to discount instead
				// Example: $item_total = 60.89, $breakdown_item_total = 60.88.
				$discount_value = $pu->getAmount()->getBreakdown()->getDiscount()->getValue();
				$pu->getAmount()->getBreakdown()->getDiscount()->setValue( $this->round( $discount_value + abs( $diff ) ) );
				$pu->getAmount()->getBreakdown()->getItemTotal()->setValue( $this->round( $items_total ) );
			}
		}
		$breakdown_total = $pu->getAmount()->getBreakdown()->getTotal();
		$diff2           = $this->round( $total - $breakdown_total );
		if ( abs( $diff2 ) >= 0.01 ) {
			if ( $diff2 > 0 ) {
				$handling = $pu->getAmount()->getBreakdown()->getHandling();
				$handling->setValue( $this->round( $handling->getValue() + $diff2 ) );
			} else {
				$discount = $pu->getAmount()->getBreakdown()->getDiscount();
				$discount->setValue( $this->round( $discount->getValue() + abs( $diff2 ) ) );
			}
		}
		/**
		 * PayPal's API can't handle quantities less than 1, so remove the items
		 * from the purchase unit if the quantity is less than 1.
		 */
		foreach ( $pu->getItems() as $item ) {
			/**
			 * @var \PaymentPlugins\PayPalSDK\Item $item
			 */
			if ( $item->getQuantity() < 1 ) {
				$pu->getAmount()->setBreakdown( null );
				$pu->setItems( null );
				break;
			}
		}
	}

	private function generate_invoice_id( $id ) {
		$prefix = $this->settings->get_option( 'order_prefix' );
		if ( $prefix ) {
			$id = $prefix . $id;
		}

		return trim( $id );
	}

}