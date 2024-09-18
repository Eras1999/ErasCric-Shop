<?php

namespace PaymentPlugins\WooCommerce\PPCP\Admin;

use PaymentPlugins\WooCommerce\PPCP\Constants;

/**
 * Class that controls updates across versions
 */
class Update {

	private $version = '';

	/**
	 * @var
	 */
	private $current_version;

	private $update_path;

	private $updates = [
		'1.0.12',
		'1.0.13',
		'1.0.27'
	];

	public function __construct( $version, $update_path ) {
		$this->version     = $version;
		$this->update_path = $update_path;
		$this->initialize();
	}

	private function initialize() {
		add_action( 'init', [ $this, 'do_update' ] );
	}

	public function do_update() {
		$current_version = $this->get_current_version();
		if ( version_compare( $current_version, $this->version, '<' ) ) {
			foreach ( $this->updates as $version ) {
				if ( version_compare( $current_version, $version, '<' ) ) {
					$file = $this->update_path . '/' . $version . '.php';
					if ( file_exists( $file ) ) {
						include_once $file;
					}
				}
			}
			$this->update_current_version( $this->version );
		}
	}

	public function update_current_version( $version ) {
		update_option( Constants::VERSION_KEY, $version );
	}

	/**
	 * Returns the version stored in the db
	 */
	public function get_current_version() {
		if ( ! $this->current_version ) {
			$this->current_version = get_option( Constants::VERSION_KEY, $this->version );
		}

		return $this->current_version;
	}

}