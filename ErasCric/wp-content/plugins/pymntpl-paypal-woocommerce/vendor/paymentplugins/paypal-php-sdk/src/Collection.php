<?php


namespace PaymentPlugins\PayPalSDK;


use Exception;
use SebastianBergmann\CodeCoverage\Util;
use Traversable;

/**
 * Class Collection
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property array $data
 */
class Collection extends AbstractObject implements \Countable, \ArrayAccess {

	public function initProps( $props ) {
		foreach ( $props as $prop ) {
			$this->_values[] = Utils::convertToPayPalObject( $prop );
		}
	}

	#[\ReturnTypeWillChange]
	public function count() {
		return \count( $this->_values );
	}

	public function add( $value ) {
		$this->_values[] = Utils::convertToPayPalObject( $value );

		return $this;
	}

	public function get( $index ) {
		if ( ! isset( $this->_values[ $index ] ) ) {
			throw new \OutOfBoundsException( 'Index ' . $index . ' does not exist' );
		}

		return $this->_values[ $index ];
	}

	public function offsetExists( $offset ) {
		return isset( $this->_values[ $offset ] );
	}

	public function offsetGet( $offset ) {
		return $this->_values[ $offset ];
	}

	public function offsetSet( $offset, $value ) {
		$this->_values[ $offset ] = $value;
	}

	public function offsetUnset( $offset ) {
		unset( $this->_values[ $offset ] );
	}

	public function getValues() {
		return $this->_values;
	}

}