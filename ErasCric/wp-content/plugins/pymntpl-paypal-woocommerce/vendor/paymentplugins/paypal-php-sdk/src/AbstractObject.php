<?php


namespace PaymentPlugins\PayPalSDK;


abstract class AbstractObject implements \JsonSerializable, \ArrayAccess, \Iterator {

	protected $_values = array();

	protected $mappings = array();

	private $index;

	/**
	 * @var \PaymentPlugins\PayPalSDK\PatchRequest
	 */
	private $patchRequest;

	public function __construct( $props = [] ) {
		$this->initProps( $props );
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return $this->_values;
	}

	public function toArray() {
		$data = [];
		foreach ( $this->_values as $key => $value ) {
			if ( $value instanceof AbstractObject ) {
				$data[ $key ] = $value->toArray();
			} elseif ( Utils::isList( $value ) ) {
				$data[ $key ] = $value;
			} elseif ( \is_object( $value ) ) {
				$data[ $key ] = (array) $value;
			} else {
				$data[ $key ] = $value;
			}
		}

		return $data;
	}

	/**
	 * Given an array of props, convert those to an object
	 *
	 * @param $props
	 */
	protected function initProps( $props ) {
		// loop through all of the props and assign them
		if ( $props ) {
			foreach ( $props as $key => $prop ) {
				$this->_values[ $this->getMappedKey( $key ) ] = $this->convertToPayPalObject( $prop, $key, $props );
			}
		}
	}

	protected function convertToPayPalObject( $prop, $key, $props = [] ) {
		return Utils::convertToPayPalObject( $prop, $key );
	}

	/**
	 *
	 * Returns a mapped key if it exists.
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	private function getMappedKey( $key ) {
		return isset( $this->mappings[ $key ] ) ? $this->mappings[ $key ] : $key;
	}

	public function &__get( $name ) {
		$value = null;
		if ( ! isset( $this->_values[ $name ] ) ) {
			//\trigger_error( sprintf( 'Property %s of class %s does not exist.', $name, static::class ) );
		} else {
			$value = $this->_values[ $name ];
		}

		return $value;
	}

	public function __set( $name, $value ) {
		$this->_values[ $name ] = $value;
	}

	public function __isset( $name ) {
		return isset( $this->_values[ $name ] );
	}

	public function __unset( $name ) {
		unset( $this->_values[ $name ] );
	}

	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return isset( $this->{$offset} );
	}

	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->__get( $offset );
	}

	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		$this->__set( $offset, $value );
	}

	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		unset( $this->{$offset} );
	}

	#[\ReturnTypeWillChange]
	public function current() {
		return \current( $this->_values );
	}

	#[\ReturnTypeWillChange]
	public function next() {
		\next( $this->_values );
	}

	#[\ReturnTypeWillChange]
	public function key() {
		return \key( $this->_values );
	}

	#[\ReturnTypeWillChange]
	public function valid() {
		return \key( $this->_values ) !== null;
	}

	#[\ReturnTypeWillChange]
	public function rewind() {
		reset( $this->_values );
	}

	public function __clone() {
		foreach ( $this->_values as $key => $value ) {
			$this->_values[ $key ] = $value instanceof AbstractObject ? clone $value : $value;
		}
	}

	/**
	 * @return \PaymentPlugins\PayPalSDK\AbstractObject $this
	 */
	public function patch() {
		$this->patchRequest = new PatchRequest( $this );

		return $this;
	}

	public function getPatchRequest( $path = '', $operation = null ) {
		if ( ! $this->patchRequest ) {
			$this->patch();
		}

		return $this->patchRequest->request( $path, $operation );
	}

	public function addPatchRequest( $path, $operation = null ) {
		$this->patchRequest->addRequest( $path, $operation );

		return $this;
	}

	public function getPatchRequests() {
		return $this->patchRequest->getPatches();
	}

	public function getPatchPath( $path ) {
		return '';
	}

	/**
	 * @param mixed $property
	 *
	 * @return $this
	 */
	public function remove( $property ) {
		if ( ! Utils::isList( $property ) ) {
			$property = [ $property ];
		}
		foreach ( $property as $key ) {
			if ( isset( $this->{$key} ) ) {
				unset( $this->{$key} );
			}
		}

		return $this;
	}

}