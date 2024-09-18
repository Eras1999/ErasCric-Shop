<?php

namespace PaymentPlugins\WooCommerce\PPCP\Admin\Settings;

use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Utilities\LocaleUtil;

class AdvancedSettings extends AbstractSettings {

	public $id = 'ppcp_advanced';

	protected $tab_label_priority = 20;

	public function __construct( ...$args ) {
		$this->tab_label = __( 'Advanced Settings', 'pymntpl-paypal-woocommerce' );
		parent::__construct( ...$args );
		add_action( 'wc_ppcp_paypal_query_params', [ $this, 'add_query_params' ] );
		add_filter( 'wc_ppcp_add_payment_method_data', [ $this, 'add_payment_method_data' ], 10, 2 );
	}

	public function init_form_fields() {
		$this->form_fields = [
			'title'                              => [
				'type'  => 'title',
				'title' => __( 'Advanced Settings', 'pymntpl-paypal-woocommerce' ),
			],
			'display_name'                       => [
				'title'       => __( 'Display Name', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'text',
				'default'     => get_option( 'blogname' ),
				'desc_tip'    => true,
				'description' => __( 'This is the business name that is displayed in the PayPal popup.', 'pymntpl-paypal-woocommerce' ),
			],
			'order_prefix'                       => [
				'title'       => __( 'Order Prefix', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'text',
				'default'     => '',
				'desc_tip'    => true,
				'description' => __( 'If you\'re using the same PayPal account on multiple sites we recommend adding an order prefix to prevent invoice duplicates in PayPal.', 'pymntpl-paypal-woocommerce' )
			],
			'locale'                             => [
				'title'       => __( 'Locale Setting', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'site',
				'options'     => [
					'auto' => __( 'Auto', 'pymntpl-paypal-woocommerce' ),
					'site' => __( 'Site language', 'pymntpl-paypal-woocommerce' ),
				],
				'desc_tip'    => true,
				'description' => __( 'If set to auto, PayPal will determine the language to render the PayPal buttons in based on the customer\'s IP address and browser settings. If set to site, the plugin will use the 
				WordPress site locale setting.', 'pymntpl-paypal-woocommerce' ),
			],
			'refund_cancel'                      => array(
				'title'       => __( 'Refund On Cancel', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'If enabled, the plugin will process a payment cancellation or refund within PayPal when the order\'s status is set to cancelled.',
					'pymntpl-paypal-woocommerce' )
			),
			'capture_status'                     => [
				'title'       => __( 'Capture Status', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'completed',
				'options'     => [
					'completed'  => __( 'Completed', 'pymntpl-paypal-woocommerce' ),
					'processing' => __( 'Processing', 'pymntpl-paypal-woocommerce' ),
					'manual'     => __( 'Manual', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'    => true,
				'description' => __( 'For orders that are authorized, when the order is set to this status, it will trigger a capture. When set to manual, the payment must be manually captured.', 'pymntpl-paypal-woocommerce' ),
			],
			'cart_location'                      => [
				'title'       => __( 'Cart Button Location', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'below',
				'options'     => [
					'below' => __( 'Below checkout button', 'pymntpl-paypal-woocommerce' ),
					'above' => __( 'Above checkout button', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'    => true,
				'description' => __( 'The location of the payment buttons in relation to the Proceed to checkout button.', 'pymntpl-paypal-woocommerce' )
			],
			'minicart_location'                  => [
				'title'       => __( 'Mini-Cart Button Location', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'below',
				'options'     => [
					'below' => __( 'Below checkout button', 'pymntpl-paypal-woocommerce' ),
					'above' => __( 'Above checkout button', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'    => true,
				'description' => __( 'The location of the payment buttons in relation to the mini-cart checkout button.', 'pymntpl-paypal-woocommerce' )
			],
			'validate_checkout'                  => [
				'title'       => __( 'Validate Checkout Fields', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'Enable this option if you want the PayPal plugin to validate the checkout page required fields when the PayPal button is clicked. If required fields are missing,
				a notice will be shown to the customer on the checkout page. PayPal has the ability to provide the billing and shipping address info automatically so we recommend leaving this option off. 
				The checkout fields won\'t be validated if your customer clicks the express checkout PayPal buttons since the fields will be populated by PayPal automatically.', 'pymntpl-paypal-woocommerce' )
			],
			Constants::SHIPPING_ADDRESS_DISABLED => [
				'title'       => __( 'Disable PayPal Shipping Address', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'value'       => 'yes',
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'If enabled, the customer will not be able to change their shipping address in the PayPal popup. This option has no effect on express checkout on the checkout page.', 'pymntpl-paypal-woocommerce' )
			]
		];
	}

	public function is_refund_on_cancel() {
		return wc_string_to_bool( $this->get_option( 'refund_cancel', 'no' ) );
	}

	public function is_capture_on_complete() {
		return $this->get_option( 'capture_status', 'completed' ) === 'completed';
	}

	public function is_capture_on_processing() {
		return $this->get_option( 'capture_status', 'completed' ) === 'processing';
	}

	public function is_manual_capture() {
		return $this->get_option( 'capture_status', 'completed' ) === 'manual';
	}

	/**
	 * @since 1.0.37
	 * @return void
	 */
	public function is_shipping_address_disabled() {
		return wc_string_to_bool( $this->get_option( Constants::SHIPPING_ADDRESS_DISABLED, 'no' ) );
	}

	/**
	 * @since 1.0.40
	 * @return bool
	 */
	public function is_checkout_validation_enabled() {
		return \wc_string_to_bool( $this->get_option( 'validate_checkout', 'no' ) );
	}

	public function get_frontend_script_data() {
		return [
			'miniCartLocation' => $this->get_option( 'minicart_location' )
		];
	}

	public function is_site_locale() {
		return $this->get_option( 'locale' ) === 'site';
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\PayPalQueryParams $params
	 *
	 * @return void
	 */
	public function add_query_params( $params ) {
		if ( $this->is_site_locale() ) {
			$locale = LocaleUtil::get_site_locale();
			if ( LocaleUtil::is_locale_supported( $locale ) ) {
				$params->locale = $locale;
			}
		}
	}

	/**
	 * @param array                                           $data
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler $context
	 *
	 * @return void
	 */
	public function add_payment_method_data( $data, $context ) {
		if ( $context->is_checkout() ) {
			$data['paypalAddressDisabled']     = $this->is_shipping_address_disabled();
			$data['checkoutValidationEnabled'] = $this->is_checkout_validation_enabled();
		}

		return $data;
	}

}