<?php


namespace PaymentPlugins\WooCommerce\PPCP\Admin\Settings;


use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\SettingsRegistry;

class SettingsApi {

	private $settings_registry;

	private $assets;

	private $asset_data;

	public function __construct( SettingsRegistry $settings_registry, AssetsApi $assets, AssetDataApi $asset_data ) {
		$this->settings_registry = $settings_registry;
		$this->assets            = $assets;
		$this->asset_data        = $asset_data;
		add_action( 'woocommerce_settings_checkout', [ $this, 'output' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'wc_ppcp_admin_script_dependencies', [ $this, 'register_admin_script_dependencies' ], 10, 2 );
	}

	public function register_admin_script_dependencies( $handles, $section ) {
		return array_merge( $handles, $this->settings_registry->get_script_handles_for_admin( $section ) );
	}

	public function output() {
		global $current_section;
		$this->add_script_data();
		$this->asset_data->do_asset_data();
		$settings = $this->settings_registry->get( $current_section );
		if ( $settings ) {
			$settings->admin_options();
		}
	}

	private function add_script_data() {
		$data = [];
		foreach ( $this->settings_registry->get_registered_integrations() as $settings ) {
			$data[ $settings->id ] = $settings->get_settings_script_data();
		}
		$this->asset_data->add( 'settings', $data );
		$this->asset_data->add( 'adminAjaxUrl', add_query_arg( array( 'action' => 'wc_ppcp_admin_request', 'path' => '/$path' ), admin_url( 'admin-ajax.php' ) ) );
	}

	public function enqueue_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if ( strpos( $screen_id, 'wc-settings' ) !== false ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$section = isset( $_REQUEST['section'] ) ? wc_clean( wp_unslash( $_REQUEST['section'] ) ) : '';
			if ( strpos( $section, 'ppcp' ) !== false ) {
				$handles = apply_filters( 'wc_ppcp_admin_script_dependencies', [], $section );
				$this->assets->enqueue_script( 'wc-ppcp-admin-commons', 'build/js/admin-commons.js', $handles );
				$this->assets->enqueue_style( 'wc-ppcp-admin', 'build/css/admin.css' );
			}
		}
	}

}