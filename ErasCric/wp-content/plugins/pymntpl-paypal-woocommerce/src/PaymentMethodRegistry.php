<?php


namespace PaymentPlugins\WooCommerce\PPCP;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;

class PaymentMethodRegistry extends Registry\BaseRegistry {

	protected $registry_id = 'payment_methods';

	public function register( $integration ) {
		$this->registry[ $integration->id ] = $integration;
	}

	public function has_payment_method( $id ) {
		return isset( $this->registry[ $id ] );
	}

	public function get_payment_method( $id ) {
		return $this->has_payment_method( $id ) ? $this->registry[ $id ] : null;
	}

	public function add_admin_script_dependencies( $section ) {
		$handles = [];
		foreach ( $this->get_registered_integrations() as $id => $setting ) {
			if ( $section && $id === $section ) {
				$handles = array_merge( $handles, $setting->get_admin_script_dependencies() );
			}
		}

		return array_unique( array_filter( $handles ) );
	}

	public function get_active_integrations() {
		return array_filter( $this->get_registered_integrations(), function ( $payment_method ) {
			return $payment_method->is_available();
		} );
	}

	public function get_cart_payment_gateways() {
		$gateways = [];
		foreach ( $this->get_registered_integrations() as $payment_method ) {
			/**
			 * @var AbstractGateway $payment_method
			 */
			if ( $payment_method->is_cart_section_enabled() ) {
				$gateways[] = $payment_method;
			}
		}

		return apply_filters( 'wc_ppcp_cart_payment_gateways', $gateways );
	}

	public function get_product_payment_gateways( $product ) {
		$gateways = [];
		if ( $product ) {
			foreach ( $this->get_registered_integrations() as $payment_method ) {
				if ( $payment_method->is_product_section_enabled( $product ) && ! apply_filters( 'wc_ppcp_is_product_section_disabled', false, $product ) ) {
					$gateways[] = $payment_method;
				}
			}
		}

		return apply_filters( 'wc_ppcp_product_payment_gateways', $gateways, $product );
	}

	public function get_express_payment_gateways() {
		$gateways = [];
		foreach ( $this->get_registered_integrations() as $payment_method ) {
			/**
			 * @var AbstractGateway $payment_method
			 */
			if ( $payment_method->is_express_section_enabled() ) {
				$gateways[] = $payment_method;
			}
		}

		return apply_filters( 'wc_ppcp_express_checkout_payment_gateways', $gateways );
	}

	public function get_minicart_payment_gateways() {
		$gateways = [];
		foreach ( $this->get_registered_integrations() as $payment_method ) {
			/**
			 * @var AbstractGateway $payment_method
			 */
			if ( $payment_method->is_minicart_section_enabled() ) {
				$gateways[] = $payment_method;
			}
		}

		return apply_filters( 'wc_ppcp_minicart_payment_gateways', $gateways );
	}

	public function add_checkout_script_dependencies() {
		$handles = [];
		foreach ( $this->get_active_integrations() as $payment_method ) {
			$handles = array_merge( $handles, $payment_method->get_checkout_script_handles() );
		}

		return $handles;
	}

	public function add_cart_script_dependencies() {
		$handles = [];
		foreach ( $this->get_cart_payment_gateways() as $payment_method ) {
			$handles = array_merge( $handles, $payment_method->get_cart_script_handles() );
		}

		return $handles;
	}

	public function add_product_script_dependencies() {
		$handles = [];
		foreach ( $this->get_product_payment_gateways( Utils::get_queried_product_id() ) as $payment_method ) {
			$handles = array_merge( $handles, $payment_method->get_product_script_handles() );
		}

		return $handles;
	}

	public function add_express_checkout_script_dependencies() {
		$handles = [];
		foreach ( $this->get_express_payment_gateways() as $payment_method ) {
			$handles = array_merge( $handles, $payment_method->get_express_checkout_script_handles() );
		}

		return $handles;
	}

	public function add_minicart_script_dependencies() {
		$handles = [];
		foreach ( $this->get_minicart_payment_gateways() as $payment_method ) {
			/**
			 * @var AbstractGateway $payment_method
			 */
			$handles = array_merge( $handles, $payment_method->get_minicart_script_handles() );
		}

		return apply_filters( 'wc_ppcp_minicart_script_dependencies', $handles );
	}

	public function add_shop_script_dependencies() {
		return apply_filters( 'wc_ppcp_shop_script_dependencies', [] );
	}

	public function add_payment_method_data( AssetDataApi $asset_data, $context ) {
		foreach ( $this->get_active_integrations() as $payment_method ) {
			$data = apply_filters( "wc_{$payment_method->id}_add_payment_method_data", $payment_method->get_payment_method_data( $context ), $context );
			$asset_data->add( $payment_method->id . '_data', $data );
		}
	}

}