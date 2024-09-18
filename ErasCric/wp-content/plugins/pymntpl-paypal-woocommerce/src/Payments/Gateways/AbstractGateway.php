<?php


namespace PaymentPlugins\WooCommerce\PPCP\Payments\Gateways;


use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\PaymentHandler;
use PaymentPlugins\WooCommerce\PPCP\PaymentResult;
use PaymentPlugins\WooCommerce\PPCP\PluginIntegrationController;
use PaymentPlugins\WooCommerce\PPCP\RefundsManager;
use PaymentPlugins\WooCommerce\PPCP\TemplateLoader;
use PaymentPlugins\WooCommerce\PPCP\Tokens\AbstractToken;
use PaymentPlugins\WooCommerce\PPCP\Traits\Settings as SettingsTrait;
use PaymentPlugins\WooCommerce\PPCP\Utilities\OrderLock;
use PaymentPlugins\WooCommerce\PPCP\Utilities\PayPalFee;

/**
 * Class AbstractGateway
 *
 * @package PaymentPlugins\WooCommerce\PPCP\Payments\Gateways
 */
abstract class AbstractGateway extends \WC_Payment_Gateway {

	use SettingsTrait;

	/**
	 * @var PaymentHandler
	 */
	public $payment_handler;

	/**
	 * @var Logger
	 */
	public $logger;

	/**
	 * @var AssetsApi
	 */
	public $assets;

	/**
	 * @var TemplateLoader
	 */
	public $template_loader;

	/**
	 * @var PluginIntegrationController
	 */
	public $integration_controller;

	protected $template;

	protected $token_class;

	protected $paypal_flow;

	public function __construct( $payment_handler, $logger, $assets, $template_loader ) {
		$this->payment_handler = $payment_handler;
		$this->payment_handler->set_payment_method( $this );
		$this->logger          = $logger;
		$this->assets          = $assets;
		$this->template_loader = $template_loader;
		$this->has_fields      = true;
		$this->init_form_fields();
		$this->init_settings();
		$this->init_hooks();
		$this->init_supports();
		$this->title       = $this->get_option( 'title_text' );
		$this->description = $this->get_option( 'description' );
	}

	protected function init_hooks() {
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		add_filter( 'wc_ppcp_admin_nav_tabs', [ $this, 'add_navigation_tab' ] );
	}

	protected function init_supports() {
		$this->supports = [
			'tokenization',
			'products',
			'subscriptions',
			'add_payment_method',
			'subscription_cancellation',
			'multiple_subscriptions',
			'subscription_amount_changes',
			'subscription_date_changes',
			'default_credit_card_form',
			'refunds',
			'pre-orders',
			'subscription_payment_method_change_admin',
			'subscription_reactivation',
			'subscription_suspension',
			'subscription_payment_method_change_customer',
		];
	}

	public function get_admin_script_dependencies() {
		return [];
	}

	public function get_checkout_script_handles() {
		return [];
	}

	public function get_cart_script_handles() {
		return [];
	}

	public function get_product_script_handles() {
		return [];
	}

	public function get_express_checkout_script_handles() {
		return [];
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler $context
	 *
	 * @return array
	 */
	public function get_payment_method_data( $context ) {
		return [];
	}

	public function add_section_enabled( $key ) {
		$sections = $this->get_option( 'sections', [] );
		if ( ! in_array( $key, $sections ) ) {
			$sections[] = $key;
		}
		$this->settings['sections'] = $sections;
	}

	public function is_section_enabled( $key ) {
		return in_array( $key, $this->get_option( 'sections', [] ) );
	}

	public function is_cart_section_enabled() {
		return $this->is_section_enabled( 'cart' );
	}

	public function is_product_section_enabled( $product ) {
		return $this->is_section_enabled( 'product' );
	}

	public function is_express_section_enabled() {
		return $this->is_section_enabled( 'express_checkout' );
	}

	public function is_minicart_section_enabled() {
		return $this->is_section_enabled( 'minicart' );
	}

	public function payment_fields() {
		$this->render_html_data();
		if ( ( $description = $this->get_description() ) ) {
			echo '<p>' . wp_kses_post( wptexturize( $description ) ) . '</p>';
		}
		printf( '<input type="hidden" id="%1$s" name="%1$s"/>', esc_attr( $this->id . '_paypal_order_id' ) );
		printf( '<input type="hidden" id="%1$s" name="%1$s"/>', esc_attr( $this->id . '_billing_token' ) );
		$client = $this->payment_handler->client;
		$this->template_loader->load_template( "checkout/{$this->template}", [
			'gateway'   => $this,
			'assets'    => $this->assets,
			'connected' => $client->getAPISettings()->is_connected()
		] );
	}

	public function cart_fields() {
		$this->render_html_data( 'cart' );
		printf( '<input type="hidden" id="%1$s" name="%1$s"/>', esc_attr( $this->id . '_paypal_order_id' ) );
		$this->template_loader->load_template( "cart/{$this->template}", [
			'gateway' => $this
		] );
	}

	public function product_fields() {
		$this->render_html_data( 'product' );
		printf( '<input type="hidden" id="%1$s" name="%1$s"/>', esc_attr( $this->id . '_paypal_order_id' ) );
		$this->template_loader->load_template( "product/{$this->template}", [
			'gateway' => $this
		] );
	}

	public function express_checkout_fields() {
	}

	public function process_payment( $order_id ) {
		$order  = wc_get_order( $order_id );
		$result = apply_filters( 'wc_ppcp_process_payment_result', false, $order, $this );
		if ( $result ) {
			if ( is_wp_error( $result ) ) {
				wc_add_notice( $result->get_error_message(), 'error' );

				return ( new PaymentResult( $result, $order, $this ) )->get_failure_response();
			} elseif ( $result === false && wc_notice_count( 'error' ) > 0 ) {
				return ( new PaymentResult( false, $order, $this ) )->get_failure_response();
			} elseif ( \is_array( $result ) ) {
				return $result;
			}

			return ( new PaymentResult( null, $order, $this ) )->get_success_response();
		} else {
			$result = $this->payment_handler->process_payment( $order );
			if ( ! $result->success() ) {
				if ( ! $result->needs_approval() ) {
					$result->set_error_message( sprintf( __( 'There was an error processing your payment. Reason: %s', 'pymntpl-paypal-woocommerce' ), $result->get_error_message() ) );
					wc_add_notice( $result->get_error_message(), 'error' );
				}

				return $result->get_failure_response();
			} else {
				if ( $result->needs_approval() ) {
					return $result->get_approval_response();
				}
				WC()->cart->empty_cart();

				return $result->get_success_response();
			}
		}
	}

	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		try {
			$order  = wc_get_order( $order_id );
			$result = $this->payment_handler->process_refund( $order, $amount, $reason );
			if ( is_wp_error( $result ) ) {
				$msg = sprintf( __( 'Error processing refund. Reason: %s', 'pymntpl-paypal-woocommerce' ), $result->get_error_message() );
				$order->add_order_note( $msg );
				$this->logger->info( $msg );
				throw new \Exception( $msg );
			} else {
				/**
				 * @var \WC_Order_Refund $refund
				 */
				$refund = Main::container()->get( RefundsManager::class )->refund;
				if ( $refund ) {
					$refund->update_meta_data( Constants::PAYPAL_REFUND, $result->id );
				}
				OrderLock::set_order_lock( $order, MINUTE_IN_SECONDS );
				PayPalFee::update_net_from_refund( $result, $order, true );
				$order->add_order_note(
					sprintf(
						__( 'Order refunded in PayPal. Amount: %1$s. Refund ID: %2$s', 'pymntpl-paypal-woocommerce' ),
						wc_price( $amount, [ 'currency' => $order->get_currency() ] ),
						$result->id
					)
				);
			}
		} catch ( \Exception $e ) {
			return new \WP_Error( 'refund-error', $e->getMessage() );
		}

		return true;
	}

	/**
	 * Render data that can change based on inputs provided by the user.
	 *
	 * @param string $context
	 */
	protected function render_html_data( $context = 'checkout' ) {
		$data = wp_json_encode( apply_filters( 'wc_ppcp_get_payment_method_data', [], $context, $this ) );
		$data = function_exists( 'wc_esc_json' ) ? wc_esc_json( $data ) : _wp_specialchars( $data, ENT_QUOTES, 'UTF-8', true );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<input type="hidden" class="wc-ppcp-payment-method-data" data-payment-method-data="%s"/>', $data );
	}

	/**
	 * @return AbstractToken
	 */
	public function get_payment_method_token_instance() {
		$token = new $this->token_class();
		$token->set_format( $this->get_option( 'payment_format' ) );

		return $token;
	}

	public function get_product_form_fields( $fields ) {
		return $fields;
	}

	public function get_billing_token_from_request() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		return isset( $_POST["{$this->id}_billing_token"] ) ? sanitize_text_field( wp_unslash( $_POST["{$this->id}_billing_token"] ) ) : null;
	}

	public function get_transaction_url( $order ) {
		$this->view_transaction_url = 'https://www.paypal.com/activity/payment/%s';
		if ( $order->get_meta( Constants::PPCP_ENVIRONMENT ) === 'sandbox' ) {
			$this->view_transaction_url = 'https://www.sandbox.paypal.com/activity/payment/%s';
		}

		return parent::get_transaction_url( $order );
	}

	public function is_place_order_button() {
		return true;
	}

}