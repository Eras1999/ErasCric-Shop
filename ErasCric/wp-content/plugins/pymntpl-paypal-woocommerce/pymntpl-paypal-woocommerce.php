<?php
/**
 * Plugin Name: Payment Plugins for PayPal WooCommerce
 * Plugin URI: https://docs.paymentplugins.com/wc-paypal/config/
 * Description: Accept PayPal on your WooCommerce site.
 * Version: 1.0.47
 * Author: Payment Plugins, support@paymentplugins.com
 * Text Domain: pymntpl-paypal-woocommerce
 * Domain Path: /i18n/languages/
 * Tested up to: 6.5
 * Requires at least: 4.7
 * Requires PHP: 7.1
 * WC requires at least: 3.4
 * WC tested up to: 8.9
 */

defined( 'ABSPATH' ) || exit;

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

\PaymentPlugins\WooCommerce\PPCP\PluginValidation::is_valid( function () {
	new \PaymentPlugins\WooCommerce\PPCP\Main( '1.0.47', __FILE__ );
} );
