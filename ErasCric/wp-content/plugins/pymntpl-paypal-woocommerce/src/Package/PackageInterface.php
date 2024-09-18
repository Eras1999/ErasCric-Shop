<?php

namespace PaymentPlugins\WooCommerce\PPCP\Package;

interface PackageInterface {

	public function is_active();

	public function initialize();

	public function register_dependencies();


}