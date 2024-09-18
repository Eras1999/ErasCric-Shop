<?php

namespace PaymentPlugins\PPCP\FunnelKit\Upsell;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\PPCP\FunnelKit\Upsell\Rest\Routes\UpsellOrderRoute;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Integrations\PluginIntegrationsRegistry;
use PaymentPlugins\WooCommerce\PPCP\Integrations\PluginIntegrationType;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

class FunnelKitIntegration implements PluginIntegrationType {

	public $id = 'funnelkit_upsell';

	private $active;

	private $client;

	private $assets;

	public function __construct( $active, WPPayPalClient $client, AssetsApi $assets ) {
		$this->active = $active;
		$this->client = $client;
		$this->assets = $assets;
		$this->initialize();
	}

	public function is_active() {
		return $this->active;
	}

	public function initialize() {
		add_filter( 'woocommerce_ppcp_plugin_integration_registration', [ $this, 'register' ] );
		add_filter( 'wc_ppcp_process_payment_result', [ $this, 'process_payment' ], 10, 3 );
		add_filter( 'wc_ppcp_get_rest_routes', [ $this, 'add_rest_routes' ], 10, 2 );
		add_action( 'woocommerce_api_wc_ppcp_funnelkit_return', [ $this, 'handle_return_request' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_admin_scripts' ], 110 );
	}

	public function register( PluginIntegrationsRegistry $registry ) {
		$registry->register( $this );
	}

	/**
	 * @param                                                                    $result
	 * @param \WC_Order                                                          $order
	 * @param \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway $payment_method
	 *
	 * @return mixed
	 */
	public function process_payment( $result, \WC_Order $order, AbstractGateway $payment_method ) {
		$funnels_payment_method = WFOCU_Core()->gateways->get_integration( $payment_method->id );
		if ( $funnels_payment_method->should_tokenize() ) {
			$billing_token = $payment_method->get_billing_token_from_request();
			if ( $billing_token ) {
				$billing_agreement = $this->client->billingAgreements->create( [ 'token_id' => $billing_token ] );
				if ( is_wp_error( $billing_agreement ) ) {
					return $billing_agreement;
				}
				$token = $payment_method->get_payment_method_token_instance();
				$token->initialize_from_payer( $billing_agreement->payer->payer_info );
				$order->set_payment_method_title( $token->get_payment_method_title() );
				$order->update_meta_data( Constants::BILLING_AGREEMENT_ID, $billing_agreement->id );
				$order->update_meta_data( Constants::PPCP_ENVIRONMENT, $this->client->getEnvironment() );
				$order->update_meta_data( Constants::PAYER_ID, $token->get_payer_id() );
				$order->save();
				$payment_method->payment_handler->set_use_billing_agreement( true );
				$result = false;
			}
		}

		return $result;
	}

	public function add_rest_routes( $routes, $container ) {
		$routes['funnelkit/upsell/order'] = new UpsellOrderRoute(
			$container->get( PayPalClient::class ),
			$container->get( Logger::class )
		);

		return $routes;
	}

	public function handle_return_request() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$order_id  = isset( $_GET['order_id'] ) ? absint( wc_clean( wp_unslash( $_GET['order_id'] ) ) ) : null;
		$order_key = isset( $_GET['order_key'] ) ? wc_clean( wp_unslash( $_GET['order_key'] ) ) : null;
		$token     = isset( $_GET['token'] ) ? wc_clean( wp_unslash( $_GET['token'] ) ) : null;
		if ( $order_id && $order_key && $token ) {
			$order = wc_get_order( $order_id );
			if ( $order->key_is_valid( $order_key ) ) {
				$paypal_order = $this->client->orderMode( $order )->orders->retrieve( $token );
				if ( ! is_wp_error( $paypal_order ) ) {
					add_filter( 'wfocu_valid_state_for_data_setup', '__return_true' );
					WFOCU_Core()->template_loader->set_offer_id( WFOCU_Core()->data->get_current_offer() );
					WFOCU_Core()->template_loader->maybe_setup_offer();
					WFOCU_Core()->data->set( '_upsell_package', $order->get_meta( '_upsell_package' ) );

					$payment_method = WFOCU_Core()->gateways->get_integration( $order->get_payment_method() );
					$payment_method->set_paypal_order( $paypal_order );

					if ( $payment_method->process_charge( $order ) ) {
						$data = WFOCU_Core()->process_offer->_handle_upsell_charge( true );
					} else {
						$data = WFOCU_Core()->process_offer->_handle_upsell_charge( false );
					}
					$order->delete_meta_data( '_upsell_package' );
					$order->save();
					wp_safe_redirect( $data['redirect_url'] );
					exit;
				}
			}
		}
	}

	public function add_admin_scripts() {
		if ( class_exists( 'WFOCU_Common' ) ) {
			if ( \WFOCU_Common::is_load_admin_assets( 'all' ) ) {
				// enqueue admin scripts
				$this->assets->enqueue_script( 'wc-ppcp-funnelkit-admin', 'build/funnelkit-upsell-admin.js' );
			}
		}
	}

}