<?php

namespace PaymentPlugins\PPCP\FunnelKit;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\PPCP\FunnelKit\Checkout\FieldMappings;
use PaymentPlugins\PPCP\FunnelKit\Upsell\PaymentGatewaysController;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Cache\CacheHandler;
use PaymentPlugins\WooCommerce\PPCP\Config;
use PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage;
use PaymentPlugins\WooCommerce\PPCP\PaymentButtonController;
use PaymentPlugins\WooCommerce\PPCP\PaymentHandler;
use PaymentPlugins\PPCP\FunnelKit\Upsell\PaymentGateways\PayPal;

class Package extends AbstractPackage {

	public $id = 'funnelkit';

	const ASSETS = 'funnelkitAssets';

	public function initialize() {
		if ( $this->is_upsell_active() ) {
			$this->container->get( Upsell\FunnelKitIntegration::class );
			$this->container->get( PaymentGatewaysController::class );
		}
		if ( $this->is_checkout_active() ) {
			$this->container->get( Checkout\ExpressIntegration::class );
			$this->container->get( FieldMappings::class );
		}
		if ( $this->is_cart_active() ) {
			$this->container->get( Cart\CartIntegration::class );
		}
	}

	public function register_dependencies() {
		if ( $this->is_upsell_active() ) {
			$this->register_upsell();
		}
		if ( $this->is_checkout_active() ) {
			$this->register_checkout();
		}
		if ( $this->is_cart_active() ) {
			$this->register_cart();
		}
		$this->container->register( self::ASSETS, function ( $container ) {
			return new AssetsApi( new Config( $this->version, dirname( __FILE__ ) ) );
		} );
	}

	private function register_upsell() {
		$this->container->register( Upsell\FunnelKitIntegration::class, function ( $container ) {
			return new Upsell\FunnelKitIntegration(
				true,
				$container->get( PayPalClient::class ),
				$container->get( self::ASSETS )
			);
		} );
		$this->container->register( Upsell\PaymentGatewaysRegistry::class, function ( $container ) {
			return new Upsell\PaymentGatewaysRegistry( $container );
		} );
		$this->container->register( PaymentGatewaysController::class, function ( $container ) {
			return new PaymentGatewaysController( $container->get( Upsell\PaymentGatewaysRegistry::class ) );
		} );
		$this->container->register( PayPal::class, function ( $container ) {
			return new PayPal( $container->get( self::ASSETS ), $container->get( PaymentHandler::class ), WFOCU_Core()->log );
		} );
	}

	private function register_checkout() {
		$this->container->register( Checkout\ExpressIntegration::class, function ( $container ) {
			return new Checkout\ExpressIntegration( $container->get( self::ASSETS ) );
		} );
		$this->container->register( FieldMappings::class, function ( $container ) {
			return new FieldMappings( $container->get( CacheHandler::class ) );
		} );
	}

	private function register_cart() {
		$this->container->register( Cart\CartIntegration::class, function ( $container ) {
			$cart = new Cart\CartIntegration( $container->get( PaymentButtonController::class ) );
			$cart->initialize();

			return $cart;
		} );
	}

	public function is_active() {
		return $this->is_upsell_active() || $this->is_checkout_active() || $this->is_cart_active();
	}

	public function is_upsell_active() {
		return function_exists( 'WFOCU_Core' );
	}

	public function is_checkout_active() {
		return class_exists( 'WFACP_Core' );
	}

	public function is_cart_active() {
		return class_exists( '\FKCart\Plugin' );
	}

}