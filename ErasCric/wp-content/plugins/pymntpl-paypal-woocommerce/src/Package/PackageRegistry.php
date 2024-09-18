<?php

namespace PaymentPlugins\WooCommerce\PPCP\Package;

use PaymentPlugins\WooCommerce\PPCP\Registry\BaseRegistry;

class PackageRegistry extends BaseRegistry {

	protected $registry_id = 'packages';

	public function get_active_packages() {
		return array_filter( $this->registry, function ( $integration ) {
			return $integration->is_active();
		} );
	}

}