<?php


namespace PaymentPlugins\WooCommerce\PPCP\Registry;


use PaymentPlugins\WooCommerce\PPCP\Container\Container;

abstract class BaseRegistry implements RegistryInterface {

	protected $registry_id = '';

	protected $registry = [];

	protected $container;

	public function __construct( Container $container ) {
		$this->container = $container;
	}

	public function initialize() {
		if ( empty( $this->registry_id ) ) {
			throw new \Exception( 'The registry must have an ID assigned to it.' );
		}
		do_action( 'woocommerce_ppcp_' . $this->registry_id . '_registration', $this, $this->container );
	}

	public function get_registered_integrations() {
		return $this->registry;
	}

	public function register( $integration ) {
		if ( empty( $this->registry[ $integration->id ] ) ) {
			$this->registry[ $integration->id ] = $integration;
		}
	}

	public function get( $id ) {
		return isset( $this->registry[ $id ] ) ? $this->registry[ $id ] : null;
	}

}