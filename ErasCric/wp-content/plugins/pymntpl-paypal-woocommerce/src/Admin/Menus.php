<?php


namespace PaymentPlugins\WooCommerce\PPCP\Admin;


class Menus {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
	}

	public function add_menu() {
		add_submenu_page( 'woocommerce', '', __( 'PayPal by Payment Plugins', 'pymntpl-paypal-woocommerce' ), 'manage_woocommerce', 'wc-ppcp-main', [ $this, 'output' ] );
	}

	public function output() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['section'] ) ) {
			$section = sanitize_text_field( wp_unslash( $_GET['section'] ) );
			// phpcs:enable WordPress.Security.NonceVerification.Recommended
			do_action( 'wc_ppcp_admin_section_' . $section );
		} else {
			do_action( 'wc_ppcp_admin_section_main' );
		}
	}

}