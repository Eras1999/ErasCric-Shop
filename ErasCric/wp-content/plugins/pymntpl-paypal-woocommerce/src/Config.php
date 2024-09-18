<?php


namespace PaymentPlugins\WooCommerce\PPCP;


class Config {

	private $version;

	private $plugin_path;

	private $url;

	/**
	 * Config constructor.
	 *
	 * @param string $version
	 * @param string $plugin_path
	 */
	public function __construct( string $version, string $plugin_path ) {
		$this->version     = $version;
		$this->plugin_path = dirname( $plugin_path );
		$this->url         = plugin_dir_url( $plugin_path );
	}

	public function get_path( $relative_path = '' ) {
		return trailingslashit( $this->plugin_path ) . $relative_path;
	}

	public function get_url() {
		return $this->url;
	}

	public function version() {
		return $this->version;
	}

}