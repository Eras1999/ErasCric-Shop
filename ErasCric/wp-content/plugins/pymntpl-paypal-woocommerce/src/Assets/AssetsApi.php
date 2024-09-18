<?php


namespace PaymentPlugins\WooCommerce\PPCP\Assets;


use PaymentPlugins\WooCommerce\PPCP\Config;

class AssetsApi {

	public $config;

	public function __construct( Config $config, $default_scripts = [] ) {
		$this->config = $config;
		$this->initialize( $default_scripts );
	}

	private function initialize( $default_scripts ) {
		foreach ( $default_scripts as $handle => $relative_path ) {
			$this->register_script( $handle, $relative_path );
		}
	}

	public function assets_url( $relative_path = '' ) {
		$url = $this->config->get_url();
		preg_match( '/^(\.{2}\/)+/', $relative_path, $matches );
		if ( $matches ) {
			foreach ( range( 0, substr_count( $matches[0], '../' ) - 1 ) as $idx ) {
				$url = dirname( $url );
			}
			$relative_path = '/' . substr( $relative_path, strlen( $matches[0] ) );
		}

		return $url . $relative_path;
	}

	private function add_script( $type, $handle, $relative_path, $deps = [], $footer = true ) {
		$file    = str_replace( '.js', '.asset.php', $relative_path );
		$file    = $this->config->get_path( $file );
		$version = $this->config->version();
		if ( file_exists( $file ) ) {
			$data    = require $file;
			$deps    = isset( $data['dependencies'] ) ? $this->add_dependencies( $deps, $data['dependencies'] ) : [];
			$version = isset( $data['version'] ) ? $data['version'] : $version;
		}
		$deps = array_unique( apply_filters( 'wc_ppcp_register_script_dependencies', $deps, $handle ) );
		if ( $type === 'enqueue' ) {
			wp_enqueue_script( $handle, $this->assets_url( $relative_path ), $deps, $version, $footer );
		} else {
			wp_register_script( $handle, $this->assets_url( $relative_path ), $deps, $version, $footer );
		}
	}

	public function enqueue_script( $handle, $relative_path, $deps = [], $footer = true ) {
		$this->add_script( 'enqueue', $handle, $relative_path, $deps, $this->config->version(), $footer );
	}

	public function register_script( $handle, $relative_path, $deps = [], $footer = true ) {
		$this->add_script( 'register', $handle, $relative_path, $deps, $this->config->version(), $footer );
	}

	public function enqueue_style( $handle, $relative_path, $deps = [] ) {
		wp_enqueue_style( $handle, $this->assets_url( $relative_path ), $deps, $this->config->version() );
	}

	private function add_dependencies( $deps, $defaults ) {
		foreach ( $defaults as $dep ) {
			$deps[] = $dep;
		}

		return $deps;
	}

}