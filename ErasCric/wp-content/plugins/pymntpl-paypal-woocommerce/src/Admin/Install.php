<?php


namespace PaymentPlugins\WooCommerce\PPCP\Admin;

use PaymentPlugins\WooCommerce\PPCP\Constants;

/**
 * Class Install
 *
 * @package PaymentPlugins\WooCommerce\PPCP\Admin
 */
class Install {

	private $version;

	/**
	 * Install constructor.
	 *
	 * @param string $plugin_name
	 */
	public function __construct( $plugin_name, $version ) {
		$this->version = $version;
		register_activation_hook( $plugin_name, [ $this, 'install' ] );
		add_filter( 'plugin_action_links_' . $plugin_name, [ $this, 'add_links' ] );
		add_action( 'admin_init', [ $this, 'initialize' ] );
	}

	public function initialize() {
		if ( get_option( Constants::INSTALL_PROCESS, 'no' ) === 'yes' ) {
			// redirect to the API Settings page if this is the first time the merchant is installing the plugin.
			delete_option( Constants::INSTALL_PROCESS );
			wp_safe_redirect( admin_url( 'admin.php?page=wc-ppcp-main&wc_ppcp_init' ) );
		}
	}

	public function install() {
		if ( ! get_option( Constants::VERSION_KEY, null ) ) {
			// only add this option if this is the first time the merchant is activating the plugin
			update_option( Constants::INSTALL_PROCESS, 'yes' );
		}
		update_option( Constants::VERSION_KEY, $this->version );
	}

	public function add_links( $links ) {
		return $links + [
				'settings' => sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ppcp_api' ), esc_html__( 'Settings', 'pymntpl-paypal-woocommerce' ) )
			];
	}

}