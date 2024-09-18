<?php


namespace PaymentPlugins\PPCP\Blocks;

use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\PPCP\Blocks\Payments\Gateways\PayPalGateway;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\PayLaterMessageSettings;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Config;
use PaymentPlugins\WooCommerce\PPCP\Container\Container;
use PaymentPlugins\PPCP\Blocks\Payments\Api as PaymentsApi;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage;
use PaymentPlugins\WooCommerce\PPCP\Rest\RestController;

class Package extends AbstractPackage {

	public $id = 'blocks';

	const ASSETS_API = 'blockAssets';

	/**
	 * Package constructor.
	 *
	 * @param Container $container
	 * @param string    $version
	 */
	public function __construct( Container $container, $version ) {
		$this->container = $container;
		$this->version   = $version;
	}

	public function register_dependencies() {
		$this->container->register( self::ASSETS_API, function ( $container ) {
			return new AssetsApi( new Config( $this->version, dirname( __FILE__ ) ) );
		} );
		$this->container->register( PaymentsApi::class, function ( $container ) {
			return new PaymentsApi(
				$container,
				$container->get( APISettings::class ),
				$container->get( RestController::class ),
				\Automattic\WooCommerce\Blocks\Package::container()->get( AssetDataRegistry::class )
			);
		} );
		$this->container->register( PayPalGateway::class, function ( $container ) {
			return new PayPalGateway(
				$container->get( PayPalClient::class ),
				$container->get( self::ASSETS_API )
			);
		} );
		$this->container->register( PayLaterMessaging::class, function ( $container ) {
			$instance = new PayLaterMessaging(
				$container->get( PayLaterMessageSettings::class ),
				$container->get( self::ASSETS_API ),
				\Automattic\WooCommerce\Blocks\Package::container()->get( AssetDataRegistry::class ) );
			$instance->set_context_handler( $this->container->get( ContextHandler::class ) );

			return $instance;
		} );
		$this->container->register( QueryParams::class, function ( $container ) {
			return new QueryParams(
				\Automattic\WooCommerce\Blocks\Package::container()->get( AssetDataRegistry::class ),
				$container->get( ContextHandler::class ),
				$container->get( APISettings::class )
			);
		} );
		$this->container->register( ContextHandler::class, function () {
			return new \PaymentPlugins\PPCP\Blocks\ContextHandler();
		} );
		$this->container->register( Rest\Controller::class, function () {
			return new Rest\Controller();
		} );
	}

	public function initialize() {
		$this->container->get( PaymentsApi::class );
		$this->container->get( PayLaterMessaging::class );
		$this->container->get( QueryParams::class );
		$this->container->get( Rest\Controller::class );
	}

	public function is_active() {
		if ( \class_exists( '\Automattic\WooCommerce\Blocks\Package' ) ) {
			if ( $this->is_core_plugin_build() ) {
				return true;
			}
			if ( \method_exists( '\Automattic\WooCommerce\Blocks\Package', 'feature' ) ) {
				$feature = \Automattic\WooCommerce\Blocks\Package::feature();
				if ( \method_exists( $feature, 'is_feature_plugin_build' ) ) {
					return $feature->is_feature_plugin_build();
				}
			}
		}

		return false;
	}

	public function is_core_plugin_build() {
		return \function_exists( 'WC' ) && \version_compare( '6.9.0', WC()->version, '<=' );
	}

}