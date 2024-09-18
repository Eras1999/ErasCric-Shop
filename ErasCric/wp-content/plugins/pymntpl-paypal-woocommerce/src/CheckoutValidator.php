<?php

namespace PaymentPlugins\WooCommerce\PPCP;

use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;

/**
 * @since - 1.0.38
 */
class CheckoutValidator {

	private $errors = [];

	const VALIDATION_ERRORS = 8450;

	public function validate_checkout( \WP_REST_Request $request, $throw_exception = true ) {
		$checkout = WC()->checkout();
		$customer = WC()->customer;
		$fields   = $checkout ? $checkout->get_checkout_fields() : [];
		if ( WC()->cart && ! WC()->cart->needs_shipping() ) {
			unset( $fields['shipping'] );
		}
		/**
		 * @since 1.0.37
		 */
		$fields = apply_filters( 'wc_ppcp_checkout_validation_fields', $fields, $request );

		foreach ( $fields as $fieldset_key => $fieldset ) {
			if ( $fieldset_key === 'account' ) {
				if ( is_user_logged_in() || ( ! $checkout->is_registration_required() && empty( $request['createaccount'] ) ) ) {
					continue;
				}
			} elseif ( $fieldset_key === 'shipping' ) {
				if ( ( ! isset( $request['ship_to_different_address'] ) && ! wc_ship_to_billing_address_only() ) || ! WC()->cart->needs_shipping_address() ) {
					continue;
				}
			}
			foreach ( $fieldset as $key => $field ) {
				/**
				 * Use wc_string_to_bool in case some 3rd party plugins change 'required' to a string like 'true'.
				 */
				if ( isset( $field['required'] ) && wc_string_to_bool( $field['required'] ) ) {
					$field_label = $field['label'] ?? $key;
					switch ( $fieldset_key ) {
						case 'billing':
						case 'shipping':
							$method = "get_{$key}";
							if ( method_exists( $customer, $method ) ) {
								$value = $customer->{$method}();
							} else {
								$value = $request[ $key ] ?? '';
							}
							break;
						default:
							$value = $request[ $key ] ?? '';
							break;
					}
					if ( ! \is_string( $value ) || ! strlen( $value ) ) {
						/**
						 * Use substr to determine if the key type is for billing or shipping. Some 3rd party
						 * plugins manipulate the field set value so better to rely on the $key
						 */
						if ( substr( $key, 0, 7 ) === 'billing' ) {
							$field_label = sprintf( _x( 'Billing %s', 'checkout-validation', 'woocommerce' ), $field_label );
						} elseif ( substr( $key, 0, 8 ) === 'shipping' ) {
							$field_label = sprintf( _x( 'Shipping %s', 'checkout-validation', 'woocommerce' ), $field_label );
						}
						$this->add_error(
							apply_filters( 'wc_ppcp_checkout_field_validation_label',
								sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_label ) . '</strong>' ),
								$field,
								$key,
								$fieldset_key
							)
						);
					}
				}
			}
		}

		/**
		 * @since 1.0.39
		 */
		do_action( 'wc_ppcp_checkout_validation', $this, $request );

		if ( ! empty( $this->errors ) && $throw_exception ) {
			throw new \Exception( 'validation_errors', self::VALIDATION_ERRORS );
		}
	}

	public function get_errors() {
		return $this->errors;
	}

	public function has_errors() {
		return ! empty( $this->errors );
	}

	public function add_error( $msg ) {
		$this->errors[] = $msg;
	}

	public function get_notices_html() {
		foreach ( $this->errors as $error ) {
			\wc_add_notice( $error, 'error' );
		}

		return \wc_print_notices( true );
	}

	public function get_failure_response() {
		return new \WP_Error( 'validation_errors', 'Validation errors', [
			'status'   => 400,
			'errors'   => $this->get_errors(),
			'messages' => $this->get_notices_html()
		] );
	}

}