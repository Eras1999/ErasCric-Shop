<?php

namespace PaymentPlugins\PPCP\Blocks\Rest;

use PaymentPlugins\PayPalSDK\OrderApplicationContext;

class Controller {

	public function __construct() {
		add_action( 'wc_ppcp_get_order_from_cart', [ $this, 'update_order_before_create' ], 10, 2 );
		add_filter( 'wc_ppcp_cart_order_shipping_prefix', [ $this, 'get_shipping_prefix' ], 10, 2 );
		add_filter( 'wc_ppcp_checkout_validation_fields', [ $this, 'checkout_validation_fields' ], 10, 2 );
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\Order $order
	 * @param \WP_REST_Request                $request
	 */
	public function update_order_before_create( $order, $request ) {
		if ( ! empty( $request['address_provided'] ) ) {
			$context = $order->getApplicationContext();
			if ( $context->getShippingPreference() === OrderApplicationContext::GET_FROM_FILE ) {
				$purchase_unit = $order->getPurchaseUnits()->get( 0 );
				if ( ! $purchase_unit->getShipping() || ! $purchase_unit->getShipping()->getAddress() ) {
					$context->setShippingPreference( OrderApplicationContext::NO_SHIPPING );
				} else {
					$context->setShippingPreference( OrderApplicationContext::SET_PROVIDED_ADDRESS );
				}
			}
		}

		return $order;
	}

	public function get_shipping_prefix( $shipping_prefix, $request ) {
		if ( ! empty( $request['address_provided'] ) ) {
			if ( WC()->cart->needs_shipping() ) {
				$shipping_prefix = 'shipping';
			}
		}

		return $shipping_prefix;
	}

	public function checkout_validation_fields( $fields, $request ) {
		// Checkout Blocks manage their own settings for if the phone or email is required. They don't
		// have a solution yet for validating those so for now unset these fields.
		if ( isset( $request['checkout_blocks'] ) && \wc_string_to_bool( $request['checkout_blocks'] ) ) {
			unset( $fields['billing']['billing_phone'] );
			unset( $fields['billing']['billing_email'] );
		}

		return $fields;
	}

}