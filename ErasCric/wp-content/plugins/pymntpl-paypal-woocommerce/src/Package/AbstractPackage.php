<?php

namespace PaymentPlugins\WooCommerce\PPCP\Package;

abstract class AbstractPackage implements PackageInterface {

	public $id;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Container\Container
	 */
	protected $container;

	protected $version;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Package\PackageRegistry
	 */
	public $packages;

	public function __construct( $container, $version ) {
		$this->container = $container;
		$this->version   = $version;
	}

	public function set_packages( $packages ) {
		$this->packages = $packages;
	}

}