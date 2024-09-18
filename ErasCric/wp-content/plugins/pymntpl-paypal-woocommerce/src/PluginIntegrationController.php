<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\Container\Container;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\Integrations\PluginIntegrationsRegistry;
use PaymentPlugins\WooCommerce\PPCP\Integrations\WooCommercePreOrders;
use PaymentPlugins\WooCommerce\PPCP\Integrations\WooCommerceSubscriptions;

class PluginIntegrationController {

	private $registry;

	private $container;

	private $plugins;

	public function __construct( PluginIntegrationsRegistry $registry, Container $container ) {
		$this->container = $container;
		$this->registry  = $registry;
		add_action( 'woocommerce_ppcp_plugin_integration_registration', [ $this, 'add_registry_integrations' ] );

		$this->register_integrations();
	}

	private function register_integrations() {
		$this->plugins = apply_filters( 'wc_ppcp_plugin_integrations', [
			[
				WooCommerceSubscriptions::class,
				function ( $container ) {
					return new WooCommerceSubscriptions(
						$container->get( PayPalClient::class ),
						$container->get( CoreFactories::class ),
						$container->get( Logger::class )
					);
				}
			],
			[
				WooCommercePreOrders::class,
				function ( $container ) {
					return new WooCommercePreOrders(
						$container->get( PayPalClient::class ),
						$container->get( CoreFactories::class ),
						$container->get( Logger::class )
					);
				}
			]
		], $this, $this->container );
		foreach ( $this->plugins as $entry ) {
			$this->container->register( ...$entry );
		}
		$this->registry->initialize();
	}

	public function add_registry_integrations() {
		foreach ( $this->plugins as $plugin ) {
			$integration = $this->container->get( $plugin[0] );
			$this->registry->register( $integration );
			if ( $integration->is_active() ) {
				$integration->initialize();
			}
		}
	}

	public function process_payment( \WC_Order $order ) {
		foreach ( $this->registry->get_active_integrations() as $integration ) {
			if ( ( $result = $integration->process_payment( $order ) ) ) {
				return $result;
			}
		}

		return false;
	}

	public function get_integration( $id ) {
		return $this->registry->get( $id );
	}

}