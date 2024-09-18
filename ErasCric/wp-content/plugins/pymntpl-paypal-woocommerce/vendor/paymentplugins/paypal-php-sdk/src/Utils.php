<?php


namespace PaymentPlugins\PayPalSDK;

use PaymentPlugins\PayPalSDK\V1\Address;
use PaymentPlugins\PayPalSDK\V1\Tracker;

class Utils {

	private static $mappings
		= array(
			'list'                        => Collection::class,
			'amount'                      => Amount::class,
			'links'                       => Link::class,
			'purchase_units'              => PurchaseUnit::class,
			'payment'                     => Payment::class,
			'payments'                    => Payments::class,
			'capture'                     => Capture::class,
			'captures'                    => Capture::class,
			'authorization'               => Authorization::class,
			'authorizations'              => Authorization::class,
			'refund'                      => Refund::class,
			'refunds'                     => Refund::class,
			'address'                     => \PaymentPlugins\PayPalSDK\Address::class,
			'billing_address'             => \PaymentPlugins\PayPalSDK\V1\Address::class,
			'name'                        => Name::class,
			'payer'                       => Payer::class,
			'payee'                       => Payee::class,
			'payer_info'                  => PayerInfo::class,
			'phone'                       => Phone::class,
			'phone_number'                => PhoneNumber::class,
			'payment_source'              => PaymentSource::class,
			'money'                       => Money::class,
			'platform_fees'               => PlatformFee::class,
			'seller_receivable_breakdown' => SellerReceivableBreakdown::class,
			'status_details'              => StatusDetails::class,
			'exchange_rate'               => ExchangeRate::class,
			'event_types'                 => WebhookEventType::class,
			'resource_version'            => WebhookResourceVersion::class,
			'shipping'                    => Shipping::class,
			'options'                     => ShippingOption::class,
			'shipping_address'            => \PaymentPlugins\PayPalSDK\V1\Address::class,
			'seller_payable_breakdown'    => SellerPayableBreakdown::class,
			'total_refunded_amount'       => Amount::class,
			'gross_amount'                => Money::class,
			'paypal_fee'                  => Money::class,
			'net_amount'                  => Money::class,
			'receivable_amount'           => Money::class,
			'tracker_identifiers'         => Tracker::class
		);

	public static function isList( $value ) {
		if ( array() === $value ) {
			return true;
		}
		if ( ! \is_array( $value ) ) {
			return false;
		}
		if ( array_keys( $value ) === \range( 0, count( $value ) - 1 ) ) {
			return true;
		}

		return false;
	}

	private static function hasClassMapping( $key ) {
		return isset( self::$mappings[ $key ] );
	}

	public static function convertToPayPalObject( $value, $key = null ) {
		if ( self::isList( $value ) ) {
			if ( $key && self::hasClassMapping( $key ) ) {
				$data = array();
				foreach ( $value as $prop ) {
					$data[] = self::convertToPayPalObject( $prop, $key );
				}

				return new self::$mappings['list']( $data );
			}

			return new self::$mappings['list']( $value );
		} else {
			// checks if this an object
			if ( \is_array( $value ) ) {
				// it is an object
				if ( $key && isset( self::$mappings[ $key ] ) ) {
					return new self::$mappings[ $key ]( $value );
				}

				// there is no object mapping for this value. Cast to object and continue converting
				$value = (object) $value;
				foreach ( $value as $key2 => $value2 ) {
					$value->{$key2} = self::convertToPayPalObject( $value2, $key2 );
				}

				return $value;
			} else {
				return $value;
			}
		}
	}

	public static function deepMerge( $args, $defaults ) {
		foreach ( $defaults as $key => $value ) {
			if ( ! isset( $args[ $key ] ) ) {
				$args[ $key ] = $value;
			} else {
				// key exists in $args, check if it's an array.
				if ( \is_array( $value ) ) {
					$args[ $key ] = self::deepMerge( $args[ $key ], $value );
				}
			}
		}

		return $args;
	}

	public static function convertResponseToObject( $clazz, $response, $params = null ) {
		$object = new $clazz( $response );

		return $object;
	}

}