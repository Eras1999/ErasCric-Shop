<?php

namespace PaymentPlugins\WooCommerce\PPCP\Admin;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;

class PageController {

	private $assets;

	private $data_api;

	private $plugins;

	private $plugin_slugs = [];

	public function __construct( AssetsApi $assets, AssetDataApi $data_api ) {
		$this->assets   = $assets;
		$this->data_api = $data_api;
		$this->initialize();
	}

	private function initialize() {
		add_action( 'admin_enqueue_scripts', [ $this, 'add_assets' ] );
		add_action( 'wc_ppcp_admin_section_main', [ $this, 'render_main_page' ] );
		add_action( 'wc_ppcp_admin_section_support', [ $this, 'render_support_page' ] );

		$this->plugin_slugs = [
			'stripe' => [
				'title'   => __( 'Payment Plugins for Stripe WooCommerce', 'pymntpl-paypal-woocommerce' ),
				'tagline' => __( 'Offer Apple Pay, Google Pay, and buy now pay later options like Affirm, Afterpay and Klarna. Our Stripe
				plugin is the highest rated for WooCommerce.', 'pymntpl-paypal-woocommerce' ),
				'slug'    => 'woo-stripe-payment',
				'class'   => 'PaymentPlugins\Stripe\PluginValidation'
			]
		];
	}

	public function add_assets() {
		if ( $this->is_main_page() ) {
			$this->assets->enqueue_style( 'wc-ppcp-main', 'build/css/admin-main.css' );
			wp_enqueue_script( 'updates' );
		}
	}

	private function is_main_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['page'] ) && $_GET['page'] === 'wc-ppcp-main';
	}

	private function is_initialize_install() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return $this->is_main_page() && isset( $_GET['wc_ppcp_init'] );
	}

	public function render_main_page() {
		$assets = $this->assets;
		if ( $this->is_initialize_install() ) {
			wp_enqueue_style( 'woocommerce_admin_styles' );
			$this->assets->enqueue_script( 'wc-ppcp-admin-install', 'build/js/admin-install.js', [ 'wc-backbone-modal' ] );
			include_once __DIR__ . '/Views/html-activation-tmpl.php';
		}
		$plugins = $this->get_suggested_plugins();

		include_once __DIR__ . '/Views/html-main-page.php';
	}

	public function render_support_page() {
		$user = wp_get_current_user();
		$this->data_api->print_data( 'wcPPCPSupportParams', [
			'report' => $this->get_status_report(),
			'name'   => $user->get( 'first_name' ) . ' ' . $user->get( 'last_name' ),
			'email'  => $user->get( 'user_email' )
		] );
		$this->assets->enqueue_script( 'wc-ppcp-admin-commons', 'build/js/admin-commons.js' );
		$this->assets->enqueue_script( 'wc-ppcp-help-widget', 'build/js/help-widget.js' );
		$assets = $this->assets;
		include_once __DIR__ . '/Views/html-support-page.php';
	}

	private function get_status_report() {
		$report = wc()->api->get_endpoint_data( '/wc/v3/system_status' );
		if ( ! is_wp_error( $report ) ) {
			unset( $report['subscriptions']['payment_gateway_feature_support'] );
		} else {
			$report = array();
		}

		return $report;
	}

	private function get_suggested_plugins() {
		$plugins = new \stdClass();
		foreach ( $this->plugin_slugs as $key => $values ) {
			$values          = (object) $values;
			$plugins->{$key} = (object) [
				'slug'       => $values->slug,
				'title'      => $values->title,
				'tagline'    => $values->tagline,
				'authorized' => current_user_can( 'install_plugins' ),
				'active'     => $this->is_plugin_active( $values->class ),
				'installed'  => $this->is_plugin_installed( $values->slug ),
				'urls'       => (object) [
					'icon'     => $this->assets->assets_url( 'assets/img/plugins/' . $key . '.svg' ),
					'install'  => wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $values->slug ), 'install-plugin_' . $values->slug ),
					'activate' => add_query_arg(
						array(
							'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $this->get_plugin_file( $values->slug ) ),
							'action'   => 'activate',
							'plugin'   => $this->get_plugin_file( $values->slug ),
						),
						network_admin_url( 'plugins.php' )
					)
				]
			];
		}

		return $plugins;
	}

	private function is_plugin_active( $class ) {
		return \class_exists( $class );
	}

	private function is_plugin_installed( $slug ) {
		$result = $this->get_plugin_file( $slug );

		return ! empty( $result );
	}

	private function get_plugin_file( $slug ) {
		if ( ! $this->plugins ) {
			$this->plugins = get_plugins();
		}
		foreach ( $this->plugins as $key => $plugin ) {
			if ( strpos( $key, $slug ) !== false ) {
				return $key;
			}
		}

		return '';
	}

}