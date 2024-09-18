<?php

namespace PaymentPlugins\WooCommerce\PPCP\Admin\Settings;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Traits\Settings as SettingsTrait;

/**
 * Class AbstractSettings
 *
 * @package PaymentPlugins\WooCommerce\PPCP\Admin\Settings
 */
abstract class AbstractSettings extends \WC_Settings_API {

	use SettingsTrait;

	public $assets;

	protected $logger;

	public function __construct( AssetsApi $assets, Logger $logger ) {
		$this->assets = $assets;
		$this->logger = $logger;
		$this->init_form_fields();
		$this->init_settings();
		add_action( 'woocommerce_update_options_checkout_' . $this->id, [ $this, 'process_admin_options' ] );
		add_filter( 'wc_ppcp_admin_nav_tabs', [ $this, 'add_navigation_tab' ] );
	}

	public function get_settings_script_data() {
		return [];
	}

	public function get_frontend_script_data() {
		return [];
	}

	public function get_admin_script_dependencies() {
		return [];
	}

}