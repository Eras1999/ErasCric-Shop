<?php


namespace PaymentPlugins\PayPalSDK\Service;


use PaymentPlugins\PayPalSDK\Order;

/**
 * Service class that wraps all API calls to the Orders API.
 *
 * Class OrderService
 *
 * @package PaymentPlugins\PayPalSDK\Service
 */
class OrderService extends BaseService {

	protected $path = 'v2/checkout';

	/**
	 * @param       $params
	 * @param array $options
	 *
	 * @return Order
	 */
	public function create( $params, $options = array() ) {
		return $this->post( $this->buildPath( '/orders' ), Order::class, $params, $options );
	}

	/**
	 * @param       $id
	 * @param       $params
	 * @param array $options
	 *
	 * @return Order
	 */
	public function update( $id, $params, $options = array() ) {
		return $this->patch( $this->buildPath( '/orders/%s', $id ), Order::class, $params, $options );
	}

	/**
	 * @param      $id
	 * @param null $options
	 *
	 * @return Order
	 */
	public function retrieve( $id, $options = null ) {
		return $this->get( $this->buildPath( '/orders/%s', $id ), Order::class, null, $options );
	}

	/**
	 * @param       $id
	 * @param array $params
	 * @param array $options
	 *
	 * @return Order
	 */
	public function authorize( $id, $params = [], $options = [] ) {
		return $this->post( $this->buildPath( '/orders/%s/authorize', $id ), Order::class, $params, $options );
	}

	/**
	 * @param       $id
	 * @param array $params
	 * @param array $options
	 *
	 * @return Order
	 */
	public function capture( $id, $params = [], $options = [] ) {
		return $this->post( $this->buildPath( '/orders/%s/capture', $id ), Order::class, $params, $options );
	}

}