<?php

defined( 'ABSPATH' ) || exit();

if ( \function_exists( 'WC' ) ) {
	$container = \PaymentPlugins\WooCommerce\PPCP\Main::container();
	$logger    = $container->get( \PaymentPlugins\WooCommerce\PPCP\Logger::class );
	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings $advanced_settings
	 */
	$advanced_settings = \PaymentPlugins\WooCommerce\PPCP\Main::container()->get( \PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings::class );
	if ( $advanced_settings ) {
		$capture_on_complete = wc_string_to_bool( $advanced_settings->get_option( 'capture_on_complete' ) );
		if ( $capture_on_complete ) {
			$advanced_settings->update_option( 'capture_status', 'completed' );
		} else {
			$advanced_settings->update_option( 'capture_status', 'manual' );
		}
		$logger->info( 'Version 1.0.12 update complete.' );
	}
}