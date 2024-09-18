<?php

namespace PaymentPlugins\WooCommerce\PPCP\Payments;

use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Config;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\Messages;
use PaymentPlugins\WooCommerce\PPCP\PaymentMethodRegistry;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\PayPal;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\PayPalGateway;
use PaymentPlugins\WooCommerce\PPCP\Container\Container;
use PaymentPlugins\WooCommerce\PPCP\Utils;

class PaymentGateways {

	private $payment_method_registry;

	private $assets;

	private $asset_data;

	private $api_settings;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\ContextHandler
	 */
	private $context_handler;

	public function __construct( PaymentMethodRegistry $payment_method_registry, AssetsApi $assets, AssetDataApi $asset_data, APISettings $api_settings ) {
		$this->payment_method_registry = $payment_method_registry;
		$this->assets                  = $assets;
		$this->asset_data              = $asset_data;
		$this->api_settings            = $api_settings;
		add_action( 'wc_ppcp_loaded', [ $this->payment_method_registry, 'initialize' ] );
		add_action( 'woocommerce_ppcp_payment_methods_registration', [ $this, 'register_payment_methods' ], 10, 2 );
		add_filter( 'woocommerce_payment_gateways', [ $this, 'initialize_gateways' ] );
		add_filter( 'wc_ppcp_admin_script_dependencies', [ $this, 'add_admin_script_dependencies' ], 10, 2 );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
		add_action( 'wp_print_scripts', [ $this, 'add_minicart_scripts' ] );
		add_filter( 'woocommerce_available_payment_gateways', [ $this, 'get_available_payment_gateways' ] );
		add_action( 'woocommerce_before_mini_cart', [ $this, 'add_minicart_scripts' ] );
	}

	public function set_page_context( ContextHandler $context_handler ) {
		$this->context_handler = $context_handler;
	}

	public function register_payment_methods( PaymentMethodRegistry $registry, Container $container ) {
		$this->payment_method_registry->register( $container->get( PayPalGateway::class ) );
	}

	public function initialize_gateways( $gateways = [] ) {
		foreach ( $this->payment_method_registry->get_registered_integrations() as $payment_method ) {
			$gateways[] = $payment_method;
		}

		return $gateways;
	}

	public function has_gateway( $id ) {
		return $this->payment_method_registry->has_payment_method( $id );
	}

	/**
	 * @param $id
	 *
	 * @return AbstractGateway
	 */
	public function get_gateway( $id ) {
		return $this->payment_method_registry->get_payment_method( $id );
	}

	public function get_cart_payment_gateways() {
		return $this->payment_method_registry->get_cart_payment_gateways();
	}

	public function get_product_payment_gateways() {
		return $this->payment_method_registry->get_product_payment_gateways( Utils::get_queried_product_id() );
	}

	public function get_express_payment_gateways() {
		return $this->payment_method_registry->get_express_payment_gateways();
	}

	public function get_minicart_payment_gateways() {
		$this->initialize_gateways();

		return $this->payment_method_registry->get_minicart_payment_gateways();
	}

	/**
	 * @param $payment_methods
	 *
	 * @since 1.0.16
	 * @return array
	 */
	public function filter_by_available( $payment_methods ) {
		$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

		return array_filter( $payment_methods, function ( $payment_method ) use ( $available_gateways ) {
			return isset( $available_gateways[ $payment_method->id ] );
		} );
	}

	public function add_admin_script_dependencies( $handles, $section ) {
		return array_merge( $handles, $this->payment_method_registry->add_admin_script_dependencies( $section ) );
	}

	public function load_scripts() {
		$handles = [];
		if ( $this->context_handler->has_context( [ 'checkout', 'add_payment_method', 'order_pay' ] ) || apply_filters( 'wc_ppcp_checkout_scripts', false ) ) {
			$handles = $this->payment_method_registry->add_checkout_script_dependencies();
			$handles = array_merge( $handles, $this->payment_method_registry->add_express_checkout_script_dependencies() );

			if ( $this->payment_method_registry->get_active_integrations() ) {
				$this->assets->enqueue_style( 'wc-ppcp-style', 'build/css/styles.css' );
			}
		} elseif ( $this->context_handler->is_cart() ) {
			$this->payment_method_registry->initialize();
			$handles = $this->payment_method_registry->add_cart_script_dependencies();
		} elseif ( $this->context_handler->is_product() ) {
			$this->payment_method_registry->initialize();
			$handles = $this->payment_method_registry->add_product_script_dependencies();
		} elseif ( $this->context_handler->is_shop() ) {
			$handles = $this->payment_method_registry->add_shop_script_dependencies();
		}
		$this->add_scripts( $handles );
	}

	/**
	 * Adds all required scripts and data that are required for payment buttons to function
	 * on a product page.
	 *
	 * @throws \Exception
	 */
	public function add_product_scripts() {
		$handles = $this->payment_method_registry->add_product_script_dependencies();
		$this->add_scripts( $handles );
	}

	public function add_scripts( $handles ) {
		if ( ! is_admin() && ( $handles = apply_filters( 'wc_ppcp_script_dependencies', $handles, $this->assets, $this->context_handler, $this ) ) ) {
			// enqueue required script here

			if ( wp_script_is( 'wc-ppcp-frontend-commons' ) ) {
				foreach ( $handles as $handle ) {
					wp_enqueue_script( $handle );
				}
			} else {
				$this->assets->enqueue_script( 'wc-ppcp-frontend-commons', 'build/js/frontend-commons.js', $handles );
				$this->assets->enqueue_style( 'wc-ppcp-style', 'build/css/styles.css' );

				$this->asset_data->add( 'generalData', $this->get_general_asset_data() );
				$this->asset_data->add( 'errorMessages', Main::container()->get( Messages::class )->get_messages() );
				$this->asset_data->add( 'i18n', [
					'locale'        => wp_json_encode( WC()->countries->get_country_locale() ),
					'locale_fields' => wp_json_encode( WC()->countries->get_country_locale_field_selectors() )
				] );
				$this->payment_method_registry->add_payment_method_data( $this->asset_data, $this->context_handler );
			}
		}
	}

	private function get_general_asset_data() {
		return apply_filters( 'wc_ppcp_general_asset_data', [
			'clientId'    => $this->api_settings->get_client_id(),
			'environment' => $this->api_settings->get_environment(),
			'partner_id'  => Constants::PARTNER_ID,
			'page'        => $this->context_handler->get_context(),
			'version'     => Main::container()->get( Config::class )->version()
		] );
	}

	public function get_payment_method_registry() {
		return $this->payment_method_registry;
	}

	/**
	 * @param \WC_Payment_Gateway[] $gateways
	 *
	 * @return mixed
	 */
	public function get_available_payment_gateways( $gateways ) {
		if ( $this->context_handler && $this->context_handler->is_add_payment_method() ) {
			unset( $gateways['ppcp'] );
		}

		return $gateways;
	}

	public function add_minicart_scripts() {
		if ( $this->context_handler ) {
			if ( ! $this->context_handler->is_checkout() && ! $this->context_handler->is_cart() && ! $this->context_handler->is_order_pay() && ! $this->context_handler->is_order_received() ) {
				$handles = $this->payment_method_registry->add_minicart_script_dependencies();
				if ( wp_script_is( 'wc-ppcp-frontend-commons' ) ) {
					foreach ( $handles as $handle ) {
						wp_enqueue_script( $handle );
					}
				} else {
					$this->add_scripts( $handles );
				}
			}
		}
	}

	/**
	 * @return \PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings
	 */
	public function get_api_settings() {
		return $this->api_settings;
	}

}