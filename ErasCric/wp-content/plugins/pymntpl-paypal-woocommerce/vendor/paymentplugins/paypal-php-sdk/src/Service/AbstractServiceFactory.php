<?php


namespace PaymentPlugins\PayPalSDK\Service;


abstract class AbstractServiceFactory {

	protected $instances = array();

	private $client;

	public function __construct( $httpClient ) {
		$this->client = $httpClient;
	}

	public function __get( $key ) {
		$clazz = $this->getClass( $key );
		if ( ! $clazz ) {
			\trigger_error( sprintf( '%s is not a valid classname entry', $key ) );
		} else {
			$instance = isset( $this->instances[ $key ] ) ? $this->instances[ $key ] : null;
			if ( ! $instance ) {
				$this->instances[ $key ] = new $clazz( $this->client );
			}

			return $this->instances[ $key ];
		}

		return null;
	}

	/**
	 * @param $classname
	 *
	 * @return mixed
	 */
	public abstract function getClass( $classname );

}