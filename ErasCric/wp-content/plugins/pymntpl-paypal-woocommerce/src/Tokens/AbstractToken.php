<?php

namespace PaymentPlugins\WooCommerce\PPCP\Tokens;

use PaymentPlugins\PayPalSDK\Payer;
use PaymentPlugins\PayPalSDK\PayerInfo;

/**
 * Class AbstractToken
 */
abstract class AbstractToken extends \WC_Payment_Token {

	protected $format;

	protected $extra_data = [
		'email'      => '',
		'first_name' => '',
		'last_name'  => '',
		'payer_id'   => '',
	];

	public abstract function get_payment_method_formats();

	protected abstract function get_default_format();

	/**
	 * @return mixed|null
	 */
	public function get_environment() {
		return $this->get_prop( 'environment' );
	}

	public function set_email( $value ) {
		$this->set_prop( 'email', $value );
	}

	public function set_first_name( $value ) {
		$this->set_prop( 'first_name', $value );
	}

	public function set_last_name( $value ) {
		$this->set_prop( 'last_name', $value );
	}

	public function set_payer_id( $value ) {
		$this->set_prop( 'payer_id', $value );
	}

	public function set_format( $format ) {
		$this->format = $format;
	}

	public function get_email() {
		return $this->get_prop( 'email' );
	}

	public function get_first_name() {
		return $this->get_prop( 'first_name' );
	}

	public function get_last_name() {
		return $this->get_prop( 'last_name' );
	}

	public function get_payer_id() {
		return $this->get_prop( 'payer_id' );
	}

	public function get_payment_method_title( $format = '' ) {
		if ( ! $format && $this->format ) {
			$format = $this->format;
		}
		$format = ! $format ? $this->get_default_format() : $format;
		$format = $this->get_payment_method_formats()[ $format ]['format'];
		$data   = [];
		foreach ( array_keys( $this->data ) as $key ) {
			$data["{{$key}}"] = $this->get_prop( $key );
		}

		return apply_filters( 'wc_ppcp_token_payment_method_title', str_replace( array_keys( $data ), $data, $format ), $this, $data );
	}

	/**
	 * @param Payer|PayerInfo $payer
	 *
	 * @return mixed
	 */
	public abstract function initialize_from_payer( $payer );

}