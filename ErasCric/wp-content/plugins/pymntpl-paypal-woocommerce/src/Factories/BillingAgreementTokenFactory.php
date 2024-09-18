<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

class BillingAgreementTokenFactory extends AbstractFactory {

	private $needs_shipping = null;

	public function from_cart( $gateway_id = 'ppcp' ) {
		$payment_methods = WC()->payment_gateways()->payment_gateways();
		$payment_method  = $payment_methods[ $gateway_id ] ?? null;
		$needs_shipping  = $this->needs_shipping;

		if ( \is_null( $needs_shipping ) ) {
			$needs_shipping = $this->cart->needs_shipping();
		}

		$params = $this->create_params( $payment_method, $needs_shipping );

		if ( $needs_shipping ) {
			$this->add_customer_address( $params );
		}


		return $this->validate_params( $params );
	}

	public function from_order( $payment_method = null ) {
		$needs_shipping = $this->needs_shipping;

		if ( ! $payment_method ) {
			$payment_methods = WC()->payment_gateways()->payment_gateways();
			$payment_method  = $payment_methods[ $this->order->get_payment_method() ] ?? null;
		}

		if ( \is_null( $this->needs_shipping ) ) {
			$shipping_items = $this->order->get_items( 'shipping' );
			$needs_shipping = ! empty( $shipping_items );
		}

		$params = $this->create_params( $payment_method, $needs_shipping );

		if ( $needs_shipping ) {
			$this->add_order_address( $params );
		}

		return $this->validate_params( $params );
	}

	public function create_params( $payment_method = null, $needs_shipping = false ) {
		$params = [];
		if ( $payment_method ) {
			$params = [
				'description' => $payment_method->get_option( 'billing_agreement_description' ),
				'payer'       => [
					'payment_method' => 'PAYPAL'
				],
				'plan'        => [
					'type'                 => 'MERCHANT_INITIATED_BILLING',
					'merchant_preferences' => [
						'cancel_url'                 => $this->get_cancel_url(),
						'return_url'                 => $this->get_return_url(),
						'notify_url'                 => $this->get_notify_url(),
						'skip_shipping_address'      => ! $needs_shipping,
						'immutable_shipping_address' => false
					]
				]
			];
		}

		return $params;
	}

	private function get_cancel_url() {
		return wc_get_checkout_url();
	}

	private function get_return_url() {
		$url = WC()->api_request_url( 'ppcp_order_return' );
		if ( $this->order ) {
			return add_query_arg( [
				'order_id'       => $this->order->get_id(),
				'order_key'      => $this->order->get_order_key(),
				'payment_method' => 'ppcp',
				'_checkoutnonce' => wp_create_nonce( 'checkout-nonce' )
			], $url );
		}

		return add_query_arg( [
			'_checkoutnonce' => wp_create_nonce( 'checkout-nonce' )
		], $url );
	}

	private function get_notify_url() {
		$url = WC()->api_request_url( 'ppcp_order_return' );
		if ( $this->order ) {
			return add_query_arg( [
				'order_id'       => $this->order->get_id(),
				'order_key'      => $this->order->get_order_key(),
				'payment_method' => 'ppcp',
				'_checkoutnonce' => wp_create_nonce( 'checkout-nonce' )
			], $url );
		}

		return add_query_arg( [
			'_checkoutnonce' => wp_create_nonce( 'checkout-nonce' )
		], $url );
	}

	private function validate_params( $params ) {
		if ( isset( $params['shipping_address']['country_code'] ) ) {
			$fields = WC()->countries->get_address_fields( $params['shipping_address']['country_code'], '' );
			foreach ( $params['shipping_address'] as $key => $value ) {
				$wc_key = '';
				switch ( $key ) {
					case 'line1':
						$wc_key = 'address_1';
						break;
					case 'line2':
						$wc_key = 'address_2';
						break;
					case 'city':
						$wc_key = 'city';
						break;
					case 'state':
						$wc_key = 'state';
						break;
					case 'postal_code':
						$wc_key = 'postcode';
						break;
					case 'country_code':
						$wc_key = 'country';
						break;
					case 'recipient_name':
						if ( isset( $fields['first_name']['required'] ) && $fields['first_name']['required'] ) {
							if ( empty( $value ) ) {
								unset( $params['shipping_address'] );
								break;
							}
						}
						if ( isset( $fields['last_name']['required'] ) && $fields['last_name']['required'] ) {
							if ( empty( $value ) ) {
								unset( $params['shipping_address'] );
								break;
							}
						}
						break;
				}
				if ( $wc_key && isset( $fields[ $wc_key ]['required'] ) ) {
					if ( $fields[ $wc_key ]['required'] ) {
						if ( empty( $value ) ) {
							unset( $params['shipping_address'] );
							break;
						}
					} else {
						if ( empty( $value ) ) {
							unset( $params['shipping_address'][ $key ] );
						}
					}
				}
			}
		}

		return $params;
	}

	private function get_address_from_customer() {
		return [
			'line1'          => $this->customer->get_shipping_address_1(),
			'line2'          => $this->customer->get_shipping_address_2(),
			'city'           => $this->customer->get_shipping_city(),
			'state'          => $this->customer->get_shipping_state(),
			'postal_code'    => $this->customer->get_shipping_postcode(),
			'country_code'   => $this->customer->get_shipping_country(),
			'recipient_name' => sprintf( '%s %s', $this->customer->get_shipping_first_name(), $this->customer->get_shipping_last_name() )
		];
	}

	private function get_address_from_order() {
		return [
			'line1'          => $this->order->get_shipping_address_1(),
			'line2'          => $this->order->get_shipping_address_2(),
			'city'           => $this->order->get_shipping_city(),
			'state'          => $this->order->get_shipping_state(),
			'postal_code'    => $this->order->get_shipping_postcode(),
			'country_code'   => $this->order->get_shipping_country(),
			'recipient_name' => sprintf( '%s %s', $this->order->get_shipping_first_name(), $this->order->get_shipping_last_name() )
		];
	}

	private function add_customer_address( &$params ) {
		$params['shipping_address'] = $this->get_address_from_customer();
	}

	private function add_order_address( &$params ) {
		$params['shipping_address'] = $this->get_address_from_order();
	}

	public function set_needs_shipping( $bool ) {
		$this->needs_shipping = $bool;
	}

}