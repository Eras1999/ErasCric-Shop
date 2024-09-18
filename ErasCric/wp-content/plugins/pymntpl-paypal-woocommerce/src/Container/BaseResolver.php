<?php


namespace PaymentPlugins\WooCommerce\PPCP\Container;


class BaseResolver extends AbstractResolver {

	private $resolved_value;

	public function get( $container ) {
		if ( ! $this->resolved_value ) {
			$this->resolved_value = $this->resolve( $container );
		}

		return $this->resolved_value;
	}
}