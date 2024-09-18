<?php


namespace PaymentPlugins\WooCommerce\PPCP\Registry;


interface RegistryInterface {

	public function get( $id );

	public function initialize();

	public function get_registered_integrations();

	/**
	 * @param mixed $integration
	 *
	 * @return mixed
	 */
	public function register( $integration );

}