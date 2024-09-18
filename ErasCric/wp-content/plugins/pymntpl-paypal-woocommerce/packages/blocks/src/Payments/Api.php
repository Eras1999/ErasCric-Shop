<?php


namespace PaymentPlugins\PPCP\Blocks\Payments;


use Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry;
use Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry;
use PaymentPlugins\PPCP\Blocks\Package;
use PaymentPlugins\PPCP\Blocks\Payments\Gateways\PayPalExpressGateway;
use PaymentPlugins\PPCP\Blocks\Payments\Gateways\PayPalGateway;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\Container\Container;
use PaymentPlugins\WooCommerce\PPCP\Messages;
use PaymentPlugins\WooCommerce\PPCP\Rest\RestController;

class Api {

	private $container;

	private $api_settings;

	private $rest_controller;

	private $data_api;

	public function __construct( Container $container, APISettings $api_settings, RestController $rest_controller, AssetDataRegistry $data_api ) {
		$this->container       = $container;
		$this->api_settings    = $api_settings;
		$this->rest_controller = $rest_controller;
		$this->data_api        = $data_api;
		$this->initialize();
	}

	public function initialize() {
		add_filter( 'woocommerce_blocks_payment_method_type_registration', [ $this, 'register_payment_gateways' ] );
		add_action( 'woocommerce_blocks_checkout_enqueue_data', [ $this, 'add_checkout_payment_method_data' ] );
		add_action( 'woocommerce_blocks_cart_enqueue_data', [ $this, 'add_cart_payment_method_data' ] );
		add_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_after', [ $this, 'enqueue_assets' ] );
		add_action( 'woocommerce_blocks_enqueue_cart_block_scripts_after', [ $this, 'dequeue_cart_scripts' ] );
		add_action( 'woocommerce_blocks_enqueue_checkout_block_scripts_before', [ $this, 'dequeue_cart_scripts' ] );
		add_filter( 'woocommerce_payment_gateways', [ $this, 'add_payment_gateways' ] );
		add_action( 'woocommerce_rest_checkout_process_payment_with_context', array( $this, 'payment_with_context' ), 10 );
		add_filter( 'rest_dispatch_request', [ $this, 'process_rest_dispatch_request' ], 10, 2 );
	}

	public function register_payment_gateways( PaymentMethodRegistry $registry ) {
		$registry->register( $this->container->get( PayPalGateway::class ) );
	}

	public function add_cart_payment_method_data() {
		$this->add_payment_method_data( 'cart' );
	}

	public function add_checkout_payment_method_data() {
		$this->add_payment_method_data( 'checkout' );
	}

	public function add_payment_method_data( $context ) {
		if ( ! $this->data_api->exists( 'ppcpGeneralData' ) ) {
			$data = [
				'clientId'      => $this->api_settings->get_client_id(),
				'environment'   => $this->api_settings->get_environment(),
				'context'       => $context,
				'isAdmin'       => current_user_can( 'manage_woocommerce' ),
				'blocksVersion' => \Automattic\WooCommerce\Blocks\Package::get_version(),
				'i18n'          => wc_ppcp_get_container()->get( Messages::class )->get_messages()
			];
			$this->data_api->add( 'ppcpGeneralData', $this->rest_controller->add_asset_data( $data ) );
		}
	}

	public function enqueue_assets() {
		if ( wp_script_is( 'wc-ppcp-blocks-commons', 'registered' ) ) {
			$this->container->get( Package::ASSETS_API )->enqueue_style( 'wc-ppcp-blocks-styles', 'build/styles.css' );
		}
	}

	public function dequeue_cart_scripts() {
		wp_dequeue_script( 'wc-ppcp-minicart-gateway' );
	}

	public function add_payment_gateways( $gateways ) {
		if ( ! is_admin() && $this->is_rest_request() ) {
			$gateways['paymentplugins_ppcp_express'] = new PayPalExpressGateway();
		}

		return $gateways;
	}

	private function is_rest_request() {
		if ( method_exists( WC(), 'is_rest_api_request' ) ) {
			return WC()->is_rest_api_request();
		}

		return false;
	}

	/**
	 * @param \Automattic\WooCommerce\StoreApi\Payments\PaymentContext $payment_context
	 */
	public function payment_with_context( $payment_context ) {
		if ( $payment_context->get_payment_method_instance() instanceof PayPalExpressGateway ) {
			$payment_context->set_payment_method( 'ppcp' );
		}
	}

	public function process_rest_dispatch_request( $value, $request ) {
		if ( isset( $request['payment_method'] ) ) {
			if ( $request['payment_method'] === 'paymentplugins_ppcp_express' ) {
				$request->set_param( 'payment_method', 'ppcp' );
			}
		}

		return $value;
	}

}