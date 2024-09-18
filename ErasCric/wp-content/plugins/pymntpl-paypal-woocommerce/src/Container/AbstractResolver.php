<?php


namespace PaymentPlugins\WooCommerce\PPCP\Container;


abstract class AbstractResolver {

	private $callback;

	public function __construct( $value ) {
		$this->callback = $value;
	}

	public function resolve( $container ) {
		$callback = $this->callback;

		return \is_callable( $callback ) ? $callback( $container ) : $this->callback;
	}

	public abstract function get( $container );
}