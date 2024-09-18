<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin;


abstract class AbstractRoute extends \PaymentPlugins\WooCommerce\PPCP\Rest\Routes\AbstractRoute {

	public function get_namespace() {
		return parent::get_namespace() . '/admin';
	}

	public function get_admin_permission_check() {
		if ( ! wc_rest_check_manager_permissions( 'settings' ) ) {
			return new \WP_Error( 'wc_ppcp_no_permission', __( 'You do not have permission to access this resource.', 'pymntpl-paypal-woocommerce' ), [ 'status' => rest_authorization_required_code() ] );
		}

		return true;
	}

}