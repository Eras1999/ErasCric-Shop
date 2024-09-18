<?php

namespace PaymentPlugins\PPCP\Stripe;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Package\AbstractPackage;
use PaymentPlugins\WooCommerce\PPCP\Package\PackageInterface;
use PaymentPlugins\WooCommerce\PPCP\PaymentHandler;
use PaymentPlugins\WooCommerce\PPCP\TemplateLoader;

class Package extends AbstractPackage {

	public $id = 'stripe';

	public function initialize() {
		$this->container->get( PaymentButtonController::class );
		$this->container->get( AdvancedSettings::class );
	}

	public function register_dependencies() {
		$this->container->register( PaymentButtonController::class, function ( $container ) {
			return new PaymentButtonController( $container, $container->get( \PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings::class ) );
		} );
		$this->container->register( PayPalPaymentGateway::class, function ( $container ) {
			return new PayPalPaymentGateway(
				$this->packages,
				$container->get( PaymentHandler::class ),
				$container->get( Logger::class ),
				$container->get( AssetsApi::class ),
				$container->get( TemplateLoader::class )
			);
		} );
		$this->container->register( AdvancedSettings::class, function () {
			new AdvancedSettings();
		} );
	}

	public function is_active() {
		return function_exists( 'stripe_wc' );
	}

}