<?php


namespace PaymentPlugins\WooCommerce\PPCP\Integrations;

use PaymentPlugins\WooCommerce\PPCP\Registry\BaseRegistry;

/**
 * Integration registry for 3rd party plugins that this plugin integrates with.
 *
 * Class IntegrationsRegistry
 * @package PaymentPlugins\WooCommerce\PPCP\Integrations
 */
class PluginIntegrationsRegistry extends BaseRegistry {

	protected $registry_id = 'plugin_integration';

	public function get_active_integrations() {
		return array_filter( $this->get_registered_integrations(), function ( $integration ) {
			return $integration->is_active();
		} );
	}
}