<?php

namespace PaymentPlugins\WooCommerce\PPCP\Shortcodes;

class ShortcodeAttributes {

	private $attributes;

	public function __construct( $attributes = [] ) {
		$this->attributes = $attributes;
	}

	public function get( $key ) {
		return $this->has( $key ) ? $this->attributes[ $key ] : null;
	}

	public function set( $key, $value ) {
		$this->attributes[ $key ] = $value;
	}

	public function has( $key ) {
		return \array_key_exists( $key, $this->attributes );
	}

}