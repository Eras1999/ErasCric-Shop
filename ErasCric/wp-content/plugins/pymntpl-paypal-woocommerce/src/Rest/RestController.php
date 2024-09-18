<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest;


use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\PayPalSDK\Utils;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\Cache\CacheHandler;
use PaymentPlugins\WooCommerce\PPCP\Container\Container;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\AbstractRoute;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin\AdminOrder;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin\AdminOrderTracking;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin\AdminWebhookCreate;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\BillingAgreementRoute;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\BillingAgreementToken;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\CartCheckout;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\CartItem;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\CartOrder;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\CartRefresh;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\CartShipping;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin\ConnectAccount;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\OrderPay;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\WebhookRoute;

class RestController {

	/**
	 * @var AbstractRoute[]
	 */
	private $routes = [];

	private $container;

	public function __construct( Container $container ) {
		$this->container = $container;
		add_action( 'wc_ajax_wc_ppcp_frontend_request', [ $this, 'handle_ajax_request' ] );
		add_filter( 'wc_ppcp_general_asset_data', [ $this, 'add_asset_data' ] );
		$this->initialize();
	}

	public function initialize() {
		$cart_args    = [
			$this->container->get( PayPalClient::class ),
			$this->container->get( Logger::class ),
			$this->container->get( CoreFactories::class ),
			$this->container->get( CacheHandler::class )
		];
		$this->routes = [
			'connect/account'         => new ConnectAccount(
				$this->container->get( PayPalClient::class ),
				$this->container->get( APISettings::class ),
				$this->container->get( Logger::class )
			),
			'cart/item'               => new CartItem( ...$cart_args ),
			'cart/shipping'           => new CartShipping( ...$cart_args ),
			'cart/checkout'           => new CartCheckout( ...$cart_args ),
			'cart/refresh'            => new CartRefresh( $this->container->get( ContextHandler::class ) ),
			'cart/order'              => new CartOrder( $this->container->get( AdvancedSettings::class ), ...$cart_args ),
			'order/pay'               => new OrderPay(
				$this->container->get( CoreFactories::class ),
				$this->container->get( PayPalClient::class ),
				$this->container->get( Logger::class ) ),
			'billing-agreement/token' => new BillingAgreementToken(
				$this->container->get( PayPalClient::class ),
				$this->container->get( Logger::class ),
				$this->container->get( AdvancedSettings::class ),
				$this->container->get( CoreFactories::class )
			),
			'billing-agreement'       => new BillingAgreementRoute( $this->container->get( PayPalClient::class ) ),
			'webhook'                 => new WebhookRoute(
				$this->container->get( PayPalClient::class ),
				$this->container->get( APISettings::class ),
				$this->container->get( Logger::class )
			),
			'admin/order'             => new AdminOrder( $this->container->get( PayPalClient::class ) ),
			'admin/tracking'          => new AdminOrderTracking( $this->container->get( PayPalClient::class ) ),
			'admin/webhook'           => new AdminWebhookCreate(
				$this->container->get( PayPalClient::class ),
				$this->container->get( APISettings::class )
			)
		];
	}

	private function get_routes() {
		$this->routes = apply_filters( 'wc_ppcp_get_rest_routes', $this->routes, $this->container );

		return $this->routes;
	}

	public function register_rest_routes() {
		foreach ( $this->get_routes() as $route ) {
			register_rest_route( $route->get_namespace(), $route->get_path(), $this->get_route_args( $route->get_routes() ) );
		}
	}

	private function get_route_args( $args ) {
		if ( ! Utils::isList( $args ) ) {
			$args = [ $args ];
		}

		return array_map( function ( $arg ) {
			return array_merge( [
				'permission_callback' => '__return_true'
			], $arg );
		}, $args );
	}

	private function get_endpoint( $path ) {
		if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
			$endpoint = esc_url_raw( apply_filters( 'woocommerce_ajax_get_endpoint', add_query_arg( 'wc-ajax', 'wc_stripe_frontend_request', remove_query_arg( [
				'remove_item',
				'add-to-cart',
				'added-to-cart',
				'order_again',
				'_wpnonce'
			], home_url( '/', 'relative' ) ) ), 'wc_ppcp_frontend_request' ) );
		} else {
			$endpoint = \WC_AJAX::get_endpoint( 'wc_ppcp_frontend_request' );
		}

		return add_query_arg( 'path', '/' . trim( $path, '/' ), $endpoint );
	}

	public function handle_ajax_request() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['path'] ) ) {
			global $wp;
			$wp->set_query_var( 'rest_route', sanitize_text_field( wp_unslash( $_GET['path'] ) ) );
			\rest_api_loaded();
		}
	}

	public function add_asset_data( $data ) {
		foreach ( $this->get_routes() as $key => $route ) {
			$data['restRoutes'][ $key ] = [
				'namespace' => $route->get_namespace(),
				'url'       => $this->get_endpoint( $route->get_full_path() )
			];
		}
		$data['ajaxRestPath'] = $this->get_endpoint( '%s' );

		return $data;
	}

}