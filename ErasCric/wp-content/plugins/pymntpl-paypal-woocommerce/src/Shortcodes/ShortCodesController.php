<?php

namespace PaymentPlugins\WooCommerce\PPCP\Shortcodes;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;

class ShortCodesController {

	private $registry;

	public function __construct( ShortCodesRegistry $registry ) {
		$this->registry = $registry;
		$this->initialize();
	}

	private function initialize() {
		add_action( 'woocommerce_ppcp_shortcodes_registration', [ $this, 'register_shortcodes' ], 10, 2 );
		$this->registry->initialize();
	}

	public function register_shortcodes( $registry, $container ) {
		foreach ( $this->get_shortcodes() as $clazz ) {
			$instance = $container->get( $clazz );
			$instance->set_payment_gateways( $container->get( PaymentGateways::class ) );
			$instance->set_assets_data( $container->get( AssetDataApi::class ) );
			$instance->initialize_properties( $container );
			$registry->register( $instance );
			add_shortcode( $instance->get_id(), function ( $attrs ) use ( $instance, $container ) {
				$attrs = ! \is_array( $attrs ) ? [] : $attrs;
				if ( $instance->is_supported_page( $container->get( ContextHandler::class ) ) ) {
					add_action( 'wc_ppcp_add_script_data', [ $instance, 'add_shortcode_script_data' ], 10, 2 );
					$instance->set_attributes( new ShortcodeAttributes( $instance->parse_attributes( $attrs ) ) );
					$instance->before_render();
					\ob_start();
					$instance->render();

					return \ob_get_clean();
				}
			} );
		}
	}

	private function get_shortcodes() {
		return [
			ProductPaymentButtons::class,
			CartPaymentButtons::class,
			ProductPayLaterMessage::class,
			CartPayLaterMessage::class
		];
	}

}