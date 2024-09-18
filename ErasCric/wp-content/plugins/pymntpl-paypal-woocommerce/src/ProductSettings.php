<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;

class ProductSettings extends \WC_Settings_API {

	private $product_id = 0;

	public $id = 'ppcp';

	public function __construct( $product_id ) {
		if ( \is_numeric( $product_id ) && $product_id > 0 ) {
			$this->product_id = $product_id;
		} elseif ( $product_id instanceof \WC_Product ) {
			$this->product_id = $product_id->get_id();
		} elseif ( $product_id instanceof \WP_Post ) {
			$this->product_id = $product_id->ID;
		}

		$this->init_form_fields();
		$this->init_settings();
	}

	public function init_settings() {
		if ( $this->product_id > 0 ) {
			$this->settings = get_post_meta( $this->product_id, $this->get_option_key(), true );
		}

		if ( ! is_array( $this->settings ) || empty( $this->settings ) ) {
			$form_fields    = $this->get_form_fields();
			$this->settings = array_merge( array_fill_keys( array_keys( $form_fields ), '' ), wp_list_pluck( $form_fields, 'default' ) );
		}
	}

	public function init_form_fields() {
		$this->form_fields = apply_filters( 'wc_ppcp_product_form_fields', [
			'title' => [
				'type'  => 'title',
				'title' => __( 'PayPal Settings', 'pymntpl-paypal-woocommerce' )
			],
		], $this );
	}

	public function process_admin_options() {
		$post_data = $this->get_post_data();

		foreach ( $this->get_form_fields() as $key => $field ) {
			if ( 'title' !== $this->get_field_type( $field ) ) {
				try {
					$this->settings[ $key ] = $this->get_field_value( $key, $field, $post_data );
				} catch ( Exception $e ) {
				}
			}
		}
		update_post_meta( $this->product_id, $this->get_option_key(), $this->settings );
	}

}