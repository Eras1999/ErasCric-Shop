<?php

namespace PaymentPlugins\WooCommerce\PPCP\Package;

class PackageController {

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Package\PackageRegistry
	 */
	private $package_registry;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage[]
	 */
	private $packages;

	public function __construct( PackageRegistry $package_registry ) {
		$this->package_registry = $package_registry;
		$this->initialize();
	}

	public function set_packages( $packages ) {
		$this->packages = $packages;
	}

	private function initialize() {
		add_action( 'plugins_loaded', [ $this->package_registry, 'initialize' ] );
		add_action( 'woocommerce_ppcp_packages_registration', [ $this, 'register_packages' ], 10, 2 );
		add_action( 'woocommerce_init', [ $this, 'initialize_packages' ], 12 );
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\Package\PackageRegistry $package_registry
	 * @param \PaymentPlugins\WooCommerce\PPCP\Container\Container     $container
	 */
	public function register_packages( $package_registry, $container ) {
		foreach ( $this->packages as $package_clazz ) {
			$package_registry->register( $container->get( $package_clazz ) );
		}
		foreach ( $package_registry->get_active_packages() as $package ) {
			$package->set_packages( $package_registry );
			if ( $package->is_active() ) {
				$package->register_dependencies();
			}
		}
	}

	public function initialize_packages() {
		foreach ( $this->package_registry->get_active_packages() as $package ) {
			$package->initialize();
		}
	}

}