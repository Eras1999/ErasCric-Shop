<?php

/**
 * Plugin Name: Prime Slider
 * Plugin URI: https://primeslider.pro/
 * Description: Prime Slider is a packed of elementor widget that gives you some awesome header and slider combination for your website.
 * Version: 3.14.9
 * Author: BdThemes
 * Author URI: https://bdthemes.com/
 * Text Domain: bdthemes-prime-slider
 * Domain Path: /languages
 * License: GPL3
 * Elementor requires at least: 3.0.0
 * Elementor tested up to: 3.21.8
 */

// Some pre define value for easy use

if ( ! defined( 'BDTPS_CORE_VER' ) ) {
	define( 'BDTPS_CORE_VER', '3.14.9' );
}
if ( ! defined( 'BDTPS_CORE__FILE__' ) ) {
	define( 'BDTPS_CORE__FILE__', __FILE__ );
}


if ( ! function_exists( '_is_pro_pro_installed' ) ) {

	function _is_pro_pro_installed() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$file_path         = 'bdthemes-prime-slider/bdthemes-prime-slider.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}

if ( ! function_exists( '_is_ps_pro_activated' ) ) {

	function _is_ps_pro_activated() {

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$file_path = 'bdthemes-prime-slider/bdthemes-prime-slider.php';

		if ( is_plugin_active( $file_path ) ) {
			return true;
		}

		return false;
	}
}

// Helper function here
include dirname( __FILE__ ) . '/includes/helper.php';
require_once BDTPS_CORE_INC_PATH . 'class-pro-widget-map.php';
include dirname( __FILE__ ) . '/includes/utils.php';

/**
 * Check the elementor installed or not
 */
if ( ! function_exists( '_is_elementor_installed' ) ) {
	function _is_elementor_installed() {
		$file_path         = 'elementor/elementor.php';
		$installed_plugins = get_plugins();
		return isset( $installed_plugins[ $file_path ] );
	}
}


/**
 * Plugin load here correctly
 * Also loaded the language file from here
 */
function prime_slider_load_plugin() {
	load_plugin_textdomain( 'bdthemes-prime-slider', false, basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'prime_slider_fail_load' );
		return;
	}

	// Filters for developer
	require BDTPS_CORE_PATH . 'includes/prime-slider-filters.php';
	// Prime Slider widget and assets loader
	require BDTPS_CORE_PATH . 'loader.php';
}

add_action( 'plugins_loaded', 'prime_slider_load_plugin' );
/**
 * Check Elementor installed and activated correctly
 */
function prime_slider_fail_load() {
	$screen = get_current_screen();
	if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
		return;
	}
	$plugin = 'elementor/elementor.php';

	if ( _is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
		$admin_message  = '<p>' . esc_html__( 'Ops! Prime Slider not working because you need to activate the Elementor plugin first.', 'bdthemes-prime-slider' ) . '</p>';
		$admin_message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $activation_url, esc_html__( 'Activate Elementor Now', 'bdthemes-prime-slider' ) ) . '</p>';
	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		$install_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
		$admin_message = '<p>' . esc_html__( 'Ops! Prime Slider not working because you need to install the Elementor plugin', 'bdthemes-prime-slider' ) . '</p>';
		$admin_message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $install_url, esc_html__( 'Install Elementor Now', 'bdthemes-prime-slider' ) ) . '</p>';
	}

	echo '<div class="error">' . wp_kses_post( $admin_message ) . '</div>';
}

/**
 * Review Automation Integration
 */

if ( ! function_exists( 'rc_ps_lite_plugin' ) ) {
	function rc_ps_lite_plugin() {

		require_once BDTPS_CORE_INC_PATH . 'feedback-hub/start.php';

		rc_dynamic_init( array(
			'sdk_version'  => '1.0.0',
			'plugin_name'  => 'Prime Slider',
			'plugin_icon'  => BDTPS_CORE_ASSETS_URL . 'images/logo.png',
			'slug'         => 'prime_slider_options',
			'menu'         => array(
				'slug' => 'prime_slider_options',
			),
			'review_url'   => 'https://bdt.to/prime-slider-elementor-addons-review',
			'plugin_title' => 'Yay! Great that you\'re using Prime Slider',
			'plugin_msg'   => '<p>Loved using Prime Slider on your website? Share your experience in a review and help us spread the love to everyone right now. Good words will help the community.</p>',
		) );

	}
	add_action( 'admin_init', 'rc_ps_lite_plugin' );
}


/**
 * SDK Integration
 */

if ( ! function_exists( 'dci_plugin_prime_slider' ) ) {
	function dci_plugin_prime_slider() {

		// Include DCI SDK.
		require_once dirname( __FILE__ ) . '/dci/start.php';

		dci_dynamic_init( array(
			'sdk_version'  => '1.1.0',
			'product_id'   => 2,
			'plugin_name'  => 'Prime Slider', // make simple, must not empty
			'plugin_title' => 'Love using Prime Slider? Congrats 🎉 ( Never miss an Important Update )', // You can describe your plugin title here
			'plugin_icon'  => BDTPS_CORE_ASSETS_URL . 'images/logo.png',
			'api_endpoint' => 'https://analytics.bdthemes.com/wp-json/dci/v1/data-insights',
			'menu'         => array(
				'slug' => 'prime_slider_options',
			),
			'public_key'   => 'pk_DktcEizxpygp4RjRkhYtVrtseZPaHrtr',
			'is_premium'   => false,
			'plugin_msg'   => '<p>Be Top-contributor by sharing non-sensitive plugin data and create an impact to the global WordPress community today! You can receive valuable emails periodically.</p>',
		) );

	}
	add_action( 'admin_init', 'dci_plugin_prime_slider' );
}