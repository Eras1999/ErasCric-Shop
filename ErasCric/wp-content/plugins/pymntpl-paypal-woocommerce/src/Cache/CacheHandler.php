<?php

namespace PaymentPlugins\WooCommerce\PPCP\Cache;

class CacheHandler implements CacheInterface {

	private $key;

	/**
	 * @var \WC_Session
	 */
	private $session;

	private $data = [];

	public function __construct( $key ) {
		$this->key     = $key;
		$this->session = WC()->session;
		if ( $this->session ) {
			$this->data = $this->session->get( $this->key, [] );
		}
		$this->initialize();
	}

	public function initialize() {
		add_action( 'wc_ppcp_order_payment_complete', [ $this, 'clear_cache' ] );
	}

	public function set( $key, $value ) {
		$this->data[ $key ] = $value;
		$this->stash();
	}

	public function get( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
	}

	public function delete( $key ) {
		unset( $this->data[ $key ] );
		$this->stash();
	}

	public function exists( $key ) {
		return isset( $this->data[ $key ] );
	}

	public function clear_cache() {
		unset( $this->session->{$this->key} );
		$this->data = [];
	}

	private function stash() {
		if ( $this->session && ! empty( $this->data ) ) {
			$this->session->set( $this->key, $this->data );
		}
	}

}