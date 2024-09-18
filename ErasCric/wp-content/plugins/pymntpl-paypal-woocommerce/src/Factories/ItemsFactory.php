<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\Collection;
use PaymentPlugins\PayPalSDK\Item;
use PaymentPlugins\PayPalSDK\Money;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;

class ItemsFactory extends AbstractFactory {

	/**
	 * @param \WC_Cart $cart
	 *
	 * @return \PaymentPlugins\PayPalSDK\Collection
	 */
	public function from_cart() {
		$incl_tax = $this->display_prices_including_tax();
		$items    = new Collection();
		foreach ( $this->cart->get_cart() as $key => $cart_item ) {
			/**
			 * Calculate the individual item price using the line_subtotal since that takes into
			 * consideration things like discounts, order bumps etc. We divide by quantity so we know
			 * the per unit price since PayPal does their own unit_price * quantity.
			 */
			if ( $incl_tax ) {
				$total = ( $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'] ) / (int) $cart_item['quantity'];
			} else {
				$total = $cart_item['line_subtotal'] / (int) $cart_item['quantity'];
			}
			$qty  = $cart_item['quantity'];
			$name = $cart_item['data']->get_name();
			$items->add( $this->get_cart_item( abs( $total ), $name, $qty, $cart_item ) );
		}
		if ( 0 < $this->cart->get_fee_total() ) {
			$fees = $this->cart->get_fees();
			/**
			 * 1.0.6 - There is a chance the fee total is greater than $0 but the fees array is not populated.
			 * Make sure the fee total gets added either as one value or all the fees
			 */
			if ( count( $fees ) > 0 ) {
				foreach ( $fees as $fee ) {
					if ( $fee->total > 0 ) {
						$total = $incl_tax ? $fee->total + $fee->tax : $fee->total;
						$items->add( $this->get_cart_item( $total, $fee->name, 1, null ) );
					}
				}
			} else {
				$items->add( $this->get_cart_item( $this->cart->get_fee_total(), __( 'Fees', 'pymntpl-paypal-woocommerce' ), 1, null ) );
			}
		}

		return $items;
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\Collection
	 */
	public function from_order() {
		$items = new Collection();
		foreach ( $this->order->get_items() as $item ) {
			$item = $this->get_order_item( $item );
			$items->add( $item );
		}
		if ( 0 < $this->get_order_total_fees() ) {
			$items->add( $this->get_order_fees() );
		}

		return $items;
	}

	/**
	 * @param $total
	 * @param $name
	 * @param $qty
	 * @param $cart_item
	 *
	 * @return Item
	 */
	public function get_cart_item( $total, $name, $qty, $cart_item = null ) {
		$product = $cart_item ? $cart_item['data'] : null;
		$item    = ( new Item() )->setName( $this->get_product_name( $name, $product ) )
		                         ->setQuantity( $qty )
		                         ->setUnitAmount( ( new Money() )->setCurrencyCode( $this->currency )
		                                                         ->setValue( (string) $this->round( $total ) ) );
		if ( $product ) {
			$item->setSku( $this->get_product_sku( $product ) );
			$item->setDescription( $this->get_product_description( $product ) );
		}

		return apply_filters( 'wc_ppcp_get_cart_item', $item, $cart_item );
	}

	/**
	 * @param \WC_Order_Item_Product $order_item
	 *
	 * @return \PaymentPlugins\PayPalSDK\Item
	 */
	public function get_order_item( $order_item ) {
		$product = $order_item->get_product();
		$item    = ( new Item() )->setName( $this->get_product_name( $order_item->get_name(), $order_item ) )
		                         ->setQuantity( $order_item->get_quantity() )
		                         ->setSku( $this->get_product_sku( $product, $order_item ) )
		                         ->setDescription( $this->get_product_description( $product, $order_item ) )
		                         ->setUnitAmount( ( new Money() )->setCurrencyCode( $this->currency )
		                                                         ->setValue( $this->round( $order_item->get_subtotal() / $order_item->get_quantity() ) ) );

		return apply_filters( 'wc_ppcp_get_order_item', $item, $order_item );
	}

	protected function get_order_fees() {
		return ( new Item() )
			->setName( __( 'Fees', 'pymntpl-paypal-woocommerce' ) )
			->setUnitAmount( ( new Money() )->setCurrencyCode( $this->currency )->setValue( $this->round( $this->get_order_total_fees() ) ) )
			->setQuantity( 1 );
	}

	/**
	 * @param \WC_Product $product
	 * @param             $other
	 *
	 * @return int|string
	 */
	private function get_product_sku( $product, $other = null ) {
		$sku = substr( $product->get_sku(), 0, 127 );
		if ( ! $sku ) {
			if ( $other && $other instanceof \WC_Order_Item_Product ) {
				if ( $other->get_variation_id() ) {
					$sku = $other->get_variation_id();
				} else {
					$sku = $other->get_product_id();
				}
			} else {
				$sku = $product->get_id();
			}
		}

		return $sku;
	}

	/**
	 * @param \WC_Product $product
	 * @param             $other
	 *
	 * @return void
	 */
	private function get_product_description( $product, $other = null ) {
		$description = '';
		if ( $product ) {
			if ( $product->get_type() === 'variation' ) {
				$description = sprintf( __( 'Product ID: %1$s. Variation ID: %2$s', 'pymntpl-paypal-woocommerce' ), $product->get_parent_id(), $product->get_id() );
			} else {
				$description = sprintf( __( 'Product ID: %1$s', 'pymntpl-paypal-woocommerce' ), $product->get_id() );
			}
		} else {
			if ( $other && $other instanceof \WC_Order_Item_Product ) {
				if ( $other->get_variation_id() ) {
					$description = sprintf( __( 'Product ID: %1$s. Variation ID: %2$s', 'pymntpl-paypal-woocommerce' ), $other->get_product_id(), $other->get_variation_id() );
				} else {
					$description = sprintf( __( 'Product ID: %1$s', 'pymntpl-paypal-woocommerce' ), $other->get_product_id() );
				}
			}
		}

		return substr( $description, 0, 127 );
	}

	/**
	 * @param                        $name
	 * @param mixed                  $obj
	 *
	 * @return string
	 */
	private function get_product_name( $name, $obj ) {
		if ( ! $name ) {
			if ( $obj instanceof \WC_Product ) {
				$name = sprintf( __( 'Product %1$s', 'pymntpl-paypal-woocommerce' ), $obj->get_id() );
			} elseif ( $obj instanceof \WC_Order_Item_Product ) {
				$name = sprintf( __( 'Product %1$s', 'pymntpl-paypal-woocommerce' ), $obj->get_product_id() );
			}
		}

		return substr( $name, 0, 127 );
	}

}