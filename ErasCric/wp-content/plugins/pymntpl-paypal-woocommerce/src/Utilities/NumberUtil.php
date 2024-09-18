<?php


namespace PaymentPlugins\WooCommerce\PPCP\Utilities;


class NumberUtil {

	/**
	 * @param     $val
	 * @param int $precision
	 *
	 * @return string
	 */
	public static function round( $val, $precision = 2 ) {
		static $decimals;
		if ( $decimals === null ) {
			$decimals = wc_get_price_decimals();
		}

		// always use the lower precision number since 2 is the max.
		return wc_format_decimal( $val, $precision > $decimals ? $decimals : $precision );
	}

	/**
	 * @param float  $value
	 * @param string $currency
	 * @param int    $decimals
	 *
	 * @return string
	 */
	public static function round_incl_currency( $value, $currency, $decimals = 2 ) {
		$decimals = isset( Currency::get_currency_decimals()[ $currency ] )
			? Currency::get_currency_decimals()[ $currency ] : $decimals;

		return NumberUtil::round( $value, $decimals );
	}

}