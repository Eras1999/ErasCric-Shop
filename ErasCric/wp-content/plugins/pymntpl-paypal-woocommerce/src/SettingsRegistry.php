<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AbstractSettings;
use PaymentPlugins\WooCommerce\PPCP\Container\Container;

class SettingsRegistry extends Registry\BaseRegistry {

	protected $registry_id = 'settings';

	/**
	 * SettingsRegistry constructor.
	 *
	 * @param array     $settings
	 * @param Container $container
	 *
	 * @throws \Exception
	 */
	public function __construct( array $settings, Container $container ) {
		parent::__construct( $container );
		foreach ( $settings as $setting ) {
			$this->register( $setting );
		}
		$this->initialize();
		add_action( 'wc_ppcp_add_script_data', [ $this, 'add_script_data' ], 10, 3 );
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi $asset_data
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler      $context_handler
	 */
	public function add_script_data( $asset_data, $context_handler ) {
		foreach ( $this->get_registered_integrations() as $id => $setting ) {
			$asset_data->add( $id, $setting->get_frontend_script_data() );
		}
	}

	public function get_script_handles_for_admin( $section = '' ) {
		$handles = [];
		foreach ( $this->get_registered_integrations() as $id => $setting ) {
			if ( $section && $id === $section ) {
				$handles = array_merge( $handles, $setting->get_admin_script_dependencies() );
			}
		}

		return array_unique( array_filter( $handles ) );
	}

}