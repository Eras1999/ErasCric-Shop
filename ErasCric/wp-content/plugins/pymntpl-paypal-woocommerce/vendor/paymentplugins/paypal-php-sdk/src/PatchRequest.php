<?php

namespace PaymentPlugins\PayPalSDK;

class PatchRequest {

	const ADD = 'add';

	const REPLACE = 'replace';

	private $instance;

	private $clone;

	private $patches = [];

	public function __construct( AbstractObject $instance ) {
		$this->instance = $instance;
		$this->clone    = clone $instance;
	}

	private function getPatchPath( $path ) {
		$path = ! empty( $path ) ? '/' . ltrim( $path, '/' ) : $path;

		return $this->instance->getPatchPath( $path );
	}

	public function request( $path, $operation = null ) {
		if ( empty( $path ) ) {
			$value = $this->instance->toArray();
		} else {
			$value = $this->getProperty( $path );
			if ( $value instanceof AbstractObject ) {
				$value = $value->toArray();
			}
		}

		return [
			'op'    => $operation ? $operation : $this->getOperation( $path ),
			'path'  => $this->getPatchPath( $path ),
			'value' => $value
		];
	}

	public function addRequest( $path, $operation ) {
		$this->patches[] = $this->request( $path, $operation );
	}

	private function getProperty( $path, $use_clone = false ) {
		$paths          = $this->explodePaths( $path );
		$current_object = ! $use_clone ? $this->instance : $this->clone;
		foreach ( $paths as $key ) {
			if ( ! isset( $current_object->{$key} ) ) {
				return null;
			} else {
				$current_object = $current_object->{$key};
			}
		}

		return $current_object;
	}

	private function getPath( $path ) {
		return ltrim( $path, '/' );
	}

	private function explodePaths( $path ) {
		return explode( '/', $this->getPath( $path ) );
	}

	private function getOperation( $path ) {
		$paths      = $this->explodePaths( $path );
		$used_paths = [];
		while ( ! empty( $paths ) ) {
			$used_paths[] = array_shift( $paths );
			$path         = implode( '/', $used_paths );
			$property     = $this->getProperty( $path, true );
			if ( ! $property ) {
				return self::ADD;
			}
		}

		return self::REPLACE;
	}

	public function add( $path, $value ) {
		$path  = ltrim( $path, '/' );
		$paths = explode( '/', $path );
		$key   = array_pop( $paths );
		if ( count( $paths ) == 0 ) {
			$this->{$key} = $value;
		} else {
			$path     = implode( '/', $paths );
			$property = $this->getProperty( $path );
			if ( $property == null ) {
				throw new \InvalidArgumentException( sprintf( '%s is not a valid property. Make sure there is an instance.', $path ) );
			}
			$property->{$key} = $value;
		}

		return $this;
	}

	public function getPatches() {
		return $this->patches;
	}

}