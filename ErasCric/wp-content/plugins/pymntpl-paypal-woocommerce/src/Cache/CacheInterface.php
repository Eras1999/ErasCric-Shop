<?php

namespace PaymentPlugins\WooCommerce\PPCP\Cache;

interface CacheInterface {

	public function set( $key, $value );

	public function get( $key );

	public function exists( $key );

	public function delete( $key );

}