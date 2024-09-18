<?php

namespace PaymentPlugins\WooCommerce\PPCP;

use PaymentPlugins\WooCommerce\PPCP\Rest\RestController;

class RestApi {

	private $controller;

	public function __construct( RestController $controller ) {
		$this->controller = $controller;
		add_action( 'rest_api_init', [ $this->controller, 'register_rest_routes' ] );
		add_action( 'wp_ajax_wc_ppcp_admin_request', array( $this, 'process_admin_request' ) );
	}

	public static function get_admin_endpoint( $path ) {
		$url = admin_url( 'admin-ajax.php' );

		return add_query_arg( array( 'action' => 'wc_ppcp_admin_request', 'path' => '/' . trim( $path, '/' ) ), $url );
	}

	public function process_admin_request() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['path'] ) ) {
			global $wp;
			$wp->set_query_var( 'rest_route', sanitize_text_field( wp_unslash( $_GET['path'] ) ) );
			rest_api_loaded();
		}
	}

}