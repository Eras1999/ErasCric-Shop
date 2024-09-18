<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\PayPalSDK\Collection;
use PaymentPlugins\PayPalSDK\Order;
use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\PayPalSDK\PurchaseUnit;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;

class OrderFactory extends AbstractFactory {

	/**
	 * @param \WC_Cart $cart
	 *
	 * @return \PaymentPlugins\PayPalSDK\Order
	 */
	public function from_cart( $intent ) {
		$needs_shipping = $this->cart->needs_shipping();
		$order          = ( new Order() )
			->setIntent( $intent )
			->setPayer( $this->factories->payer->from_customer() )
			->setPurchaseUnits( ( new Collection() )->add( $this->factories->purchaseUnit->from_cart() ) )
			->setApplicationContext( $this->factories->applicationContext->get( $needs_shipping ) );

		/**
		 * @since 1.0.13
		 */
		return apply_filters( 'wc_ppcp_order_factory_from_cart', $order, $this );
	}

	/**
	 * @param \WC_Order $cart
	 *
	 * @return \PaymentPlugins\PayPalSDK\Order
	 */
	public function from_order( $intent ) {
		$needs_shipping = $this->order->needs_shipping_address();

		$order = ( new Order() )
			->setIntent( $intent )
			->setPayer( $this->factories->payer->from_order() )
			->setPurchaseUnits( new Collection( [ $this->factories->purchaseUnit->from_order() ] ) )
			->setApplicationContext( $this->factories->applicationContext->get( $needs_shipping, true ) );

		$order->getApplicationContext()->setUserAction( OrderApplicationContext::PAY_NOW );

		/**
		 * @since 1.0.13
		 */
		return apply_filters( 'wc_ppcp_order_factory_from_order', $order, $this );
	}

}