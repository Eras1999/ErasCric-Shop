<?php


namespace PaymentPlugins\WooCommerce\PPCP\Admin\MetaBoxes;


use PaymentPlugins\WooCommerce\PPCP\ProductSettings;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;

class ProductData {

	private $assets_api;

	public function __construct( AssetsApi $assets_api ) {
		$this->assets_api = $assets_api;
		$this->initialize();
	}

	private function initialize() {
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_data_tabs' ] );
		add_action( 'woocommerce_product_data_panels', [ $this, 'render_panel' ] );
		add_action( 'woocommerce_admin_process_product_object', [ $this, 'save' ] );
	}

	public function add_data_tabs( $tabs ) {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$tabs['ppcp'] = [
				'label'    => __( 'PayPal Settings', 'pymntpl-paypal-woocommerce' ),
				'target'   => 'ppcp_product_data',
				'class'    => [ 'hide_if_external' ],
				'priority' => 100,
			];
		}

		return $tabs;
	}

	public function render_panel() {
		global $product_object;
		$this->assets_api->enqueue_script( 'wc-ppcp-product-metabox', 'build/js/admin-product-metabox.js', [
			'wc-ppcp-admin-commons'
		] );
		$this->assets_api->enqueue_style( 'wc-ppcp-admin', 'build/css/admin.css' );
		WC()->payment_gateways();
		$settings = new ProductSettings( $product_object );

		include __DIR__ . '/Views/html-product-data.php';
	}

	public function save( \WC_Product $product ) {
		foreach ( WC()->payment_gateways()->payment_gateways() as $payment_method ) {
			if ( $payment_method instanceof AbstractGateway ) {
				$setting    = new ProductSettings( $product );
				$change_key = "wc_{$setting->id}_options_change";
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( isset( $_POST[ $change_key ] ) && 'yes' === $_POST[ $change_key ] && $setting->form_fields ) {
					$setting->process_admin_options();
				}
			}
		}
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['_ppcp_button_position'] ) ) {
			$product->update_meta_data( '_ppcp_button_position', wc_clean( wp_unslash( $_POST['_ppcp_button_position'] ) ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

}