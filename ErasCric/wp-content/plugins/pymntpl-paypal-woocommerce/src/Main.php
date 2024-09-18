<?php


namespace PaymentPlugins\WooCommerce\PPCP;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\Admin\Install;
use PaymentPlugins\WooCommerce\PPCP\Admin\Menus;
use PaymentPlugins\WooCommerce\PPCP\Admin\MetaBoxes\Order as OrderMetaBox;
use PaymentPlugins\WooCommerce\PPCP\Admin\MetaBoxes\ProductData as ProductDataMetaBox;
use PaymentPlugins\WooCommerce\PPCP\Admin\PageController;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\PayLaterMessageSettings;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\SettingsApi;
use PaymentPlugins\WooCommerce\PPCP\Admin\Update;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\Cache\CacheHandler;
use PaymentPlugins\WooCommerce\PPCP\Cache\CacheInterface;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\Integrations\PluginIntegrationsRegistry;
use PaymentPlugins\WooCommerce\PPCP\Package\PackageController;
use PaymentPlugins\WooCommerce\PPCP\Package\PackageRegistry;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\PayPalGateway;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Rest\RestController;
use PaymentPlugins\WooCommerce\PPCP\Shortcodes\CartPayLaterMessage;
use PaymentPlugins\WooCommerce\PPCP\Shortcodes\CartPaymentButtons;
use PaymentPlugins\WooCommerce\PPCP\Shortcodes\ProductPayLaterMessage;
use PaymentPlugins\WooCommerce\PPCP\Shortcodes\ProductPaymentButtons;
use PaymentPlugins\WooCommerce\PPCP\Shortcodes\ShortCodesController;
use PaymentPlugins\WooCommerce\PPCP\Shortcodes\ShortCodesRegistry;

/**
 * Main class that loads all functionality.
 *
 * Class Main
 *
 * @package PaymentPlugins\WooCommerce\PPCP
 */
class Main {

	private $container;

	private $version;

	private $path;

	private $plugin_name;

	private $file;

	public static function container() {
		static $container;
		if ( ! $container ) {
			$container = new \PaymentPlugins\WooCommerce\PPCP\Container\Container();
		}

		return $container;
	}

	/**
	 * Main constructor.
	 *
	 * @param string $version
	 * @param string $file
	 */
	public function __construct( $version, $file ) {
		$this->file        = $file;
		$this->path        = plugin_dir_path( $file );
		$this->plugin_name = plugin_basename( $file );
		$this->version     = $version;
		$this->container   = self::container();
		add_action( 'woocommerce_init', [ $this, 'register_woocommerce_dependencies' ], 10 );
		add_action( 'woocommerce_init', [ $this, 'initialize' ], 15 );
		add_action( 'plugins_loaded', [ $this, 'do_plugins_loaded' ] );
		$this->register();
		$this->container->get( Install::class );
		$this->container->get( Update::class );
		$this->container->get( PackageController::class );
	}

	public function initialize() {
		$this->container->get( PaymentGateways::class );
		$this->container->get( RestApi::class );
		$this->container->get( PluginIntegrationController::class );
		$this->container->get( OrderStatusController::class );
		$this->container->get( PaymentButtonController::class );
		$this->container->get( Conversion\Controller::class );
		$this->container->get( WebhookEventReceiver::class );
		$this->container->get( OrderApplicationUrlHandler::class );
		$this->container->get( RefundsManager::class );
		$this->container->get( PayPalQueryParams::class );
		$this->container->get( ContextHandler::class );
		$this->container->get( AjaxFrontendHandler::class );
		$this->container->get( ShortCodesController::class );
		$this->container->get( Messages::class );

		if ( is_admin() ) {
			$this->container->get( SettingsApi::class );
			$this->container->get( Menus::class );
			$this->container->get( PageController::class );
			$this->container->get( OrderMetaBox::class );
			$this->container->get( ProductDataMetaBox::class );
		} else {
			$this->container->get( SettingsRegistry::class );
		}

		do_action( 'wc_ppcp_loaded' );
	}

	/**
	 * These are dependencies only registered when WooCommerce is active.
	 */
	public function register_woocommerce_dependencies() {
		// Settings
		$this->container->register( APISettings::class, function ( $container ) {
			return new APISettings( $container->get( 'adminAssets' ), $container->get( Logger::class ) );
		} );
		$this->container->register( AdvancedSettings::class, function ( $container ) {
			return new AdvancedSettings( $container->get( 'adminAssets' ), $container->get( Logger::class ) );
		} );
		$this->container->register( PayLaterMessageSettings::class, function ( $container ) {
			return new PayLaterMessageSettings( $container->get( 'adminAssets' ), $container->get( Logger::class ) );
		} );
		$this->container->register( SettingsRegistry::class, function ( $container ) {
			return new SettingsRegistry( [
				$container->get( APISettings::class ),
				$container->get( AdvancedSettings::class ),
				$container->get( PayLaterMessageSettings::class )
			], $container );
		} );
		$this->container->register( PaymentGateways::class, function ( $container ) {
			$payment_gateways = new PaymentGateways(
				$container->get( PaymentMethodRegistry::class ),
				$container->get( AssetsApi::class ),
				$container->get( AssetDataApi::class ),
				$container->get( APISettings::class ) );
			$payment_gateways->set_page_context( $container->get( ContextHandler::class ) );

			return $payment_gateways;
		} );
		$this->container->register( OrderStatusController::class, function ( $container ) {
			return new OrderStatusController( $container->get( AdvancedSettings::class ) );
		} );
		$this->container->register( PluginIntegrationController::class, function ( $container ) {
			return new PluginIntegrationController(
				$container->get( PluginIntegrationsRegistry::class ),
				$container );
		} );
		$this->container->register( PayPalClient::class, function ( $container ) {
			return new WPPayPalClient( $container->get( APISettings::class ), $container->get( Logger::class ) );
		} );
		$this->container->register( TemplateLoader::class, function ( $container ) {
			return new TemplateLoader( $container->get( Config::class ), 'pymntpl-paypal-woocommerce' );
		} );
		$this->container->register( PayPalGateway::class, function ( $container ) {
			return new PayPalGateway(
				$container->get( PaymentHandler::class ),
				$container->get( Logger::class ),
				$container->get( AssetsApi::class ),
				$container->get( TemplateLoader::class ) );
		} );
		$this->container->register( PaymentHandler::class, function ( $container ) {
			return new PaymentHandler(
				$container->get( PayPalClient::class ),
				$container->get( CoreFactories::class ),
				$container->get( CacheHandler::class )
			);
		} );
		$this->container->register( SettingsApi::class, function ( $container ) {
			return new SettingsApi( $container->get( SettingsRegistry::class ), $container->get( 'adminAssets' ), $container->get( 'adminData' ) );
		} );
		$this->container->register( 'adminAssets', function ( $container ) {
			return new AssetsApi( $container->get( Config::class ) );
		} );
		$this->container->register( 'adminData', function ( $container ) {
			return new AssetDataApi( 'wc-ppcp-admin-commons' );
		} );
		$this->container->register( AssetsApi::class, function ( $container ) {
			return new AssetsApi( $container->get( Config::class ), [
				'wc-ppcp-utils'    => 'build/js/utils.js',
				'wc-ppcp-product'  => 'build/js/product.js',
				'wc-ppcp-cart'     => 'build/js/cart.js',
				'wc-ppcp-minicart' => 'build/js/minicart.js'
			] );
		} );
		$this->container->register( AssetDataApi::class, function ( $container ) {
			return new AssetDataApi();
		} );
		$this->container->register( RestController::class, function ( $container ) {
			return new RestController( $container );
		} );
		$this->container->register( RestApi::class, function ( $container ) {
			return new RestApi( $container->get( RestController::class ) );
		} );
		$this->container->register( PluginIntegrationsRegistry::class, function ( $container ) {
			return new PluginIntegrationsRegistry( $container );
		} );
		$this->container->register( Messages::class, function ( $container ) {
			return new Messages();
		} );
		$this->container->register( PaymentButtonController::class, function ( $container ) {
			$instance = new PaymentButtonController(
				$container->get( PaymentGateways::class ),
				$container->get( AssetDataApi::class ),
				$container->get( TemplateLoader::class )
			);
			$instance->set_cart_location( $container->get( AdvancedSettings::class )->get_option( 'cart_location' ) );
			$instance->set_minicart_location( $container->get( AdvancedSettings::class )->get_option( 'minicart_location' ) );
			$instance->initialize();

			return $instance;
		} );
		$this->container->register( WebhookEventReceiver::class, function ( $container ) {
			return new WebhookEventReceiver(
				$container->get( PayPalClient::class ),
				$container->get( PaymentHandler::class ),
				$container->get( Logger::class )
			);
		} );
		$this->container->register( OrderApplicationUrlHandler::class, function ( $container ) {
			$handler = new OrderApplicationUrlHandler( $container->get( PaymentGateways::class ) );
			$handler->initialize();

			return $handler;
		} );
		$this->container->register( RefundsManager::class, function ( $container ) {
			return new RefundsManager();
		} );
		$this->container->register( PayPalQueryParams::class, function ( $container ) {
			$instance = new PayPalQueryParams( $container->get( AssetDataApi::class ), $container->get( APISettings::class ) );
			$instance->set_context_handler( $container->get( ContextHandler::class ) );

			return $instance;
		} );
		$this->container->register( ContextHandler::class, function ( $container ) {
			return new ContextHandler();
		} );
		$this->container->register( CoreFactories::class, function ( $container ) {
			$instance = new CoreFactories();
			$instance->register( $container );

			return $instance;
		} );
		$this->container->register( Conversion\Registry::class, function ( $container ) {
			return new Conversion\Registry( $container );
		} );
		$this->container->register( Conversion\Controller::class, function ( $container ) {
			return new Conversion\Controller( $container->get( Conversion\Registry::class ) );
		} );
		$this->container->register( AjaxFrontendHandler::class, function ( $container ) {
			return new AjaxFrontendHandler(
				$container->get( PayPalClient::class ),
				$container->get( PaymentGateways::class ),
				$container->get( CacheHandler::class )
			);
		} );
		$this->container->register( CacheHandler::class, function () {
			return new CacheHandler( Constants::PPCP_CACHE_KEY );
		} );
		$this->container->register( ShortCodesRegistry::class, function ( $container ) {
			return new ShortCodesRegistry( $container );
		} );
		$this->container->register( ShortCodesController::class, function ( $container ) {
			return new ShortCodesController( $container->get( ShortCodesRegistry::class ) );
		} );
		$this->container->register( ProductPaymentButtons::class, function ( $container ) {
			return new ProductPaymentButtons(
				$container->get( AssetsApi::class ),
				$container->get( TemplateLoader::class )
			);
		} );
		$this->container->register( CartPaymentButtons::class, function ( $container ) {
			return new CartPaymentButtons(
				$container->get( AssetsApi::class ),
				$container->get( TemplateLoader::class )
			);
		} );
		$this->container->register( ProductPayLaterMessage::class, function ( $container ) {
			return new ProductPayLaterMessage(
				$container->get( AssetsApi::class ),
				$container->get( TemplateLoader::class )
			);
		} );
		$this->container->register( CartPayLaterMessage::class, function ( $container ) {
			return new CartPayLaterMessage(
				$container->get( AssetsApi::class ),
				$container->get( TemplateLoader::class )
			);
		} );
	}

	/**
	 * These are classes that don't have a dependency on WC.
	 */
	public function register() {
		$this->container->register( Config::class, function ( $container ) {
			return new Config( $this->version, dirname( __FILE__ ) );
		} );
		$this->container->register( PaymentMethodRegistry::class, function ( $container ) {
			return new PaymentMethodRegistry( $container );
		} );
		$this->container->register( Logger::class, function ( $container ) {
			return new Logger( 'wc-ppcp' );
		} );
		$this->container->register( Install::class, function ( $container ) {
			return new Install( $this->plugin_name, $this->version );
		} );
		$this->container->register( Update::class, function () {
			return new Update( $this->version, $this->path . 'updates' );
		} );
		$this->container->register( Menus::class, function () {
			return new Menus();
		} );
		$this->container->register( PageController::class, function ( $container ) {
			return new PageController( $container->get( 'adminAssets' ), $container->get( 'adminData' ) );
		} );
		$this->container->register( OrderMetaBox::class, function ( $container ) {
			return new OrderMetaBox( $container->get( 'adminAssets' ) );
		} );
		$this->container->register( ProductDataMetaBox::class, function ( $container ) {
			return new ProductDataMetaBox( $container->get( 'adminAssets' ) );
		} );

		// register packages
		$this->container->register( \PaymentPlugins\PPCP\Blocks\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\Blocks\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\CheckoutWC\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\CheckoutWC\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\Stripe\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\Stripe\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\FunnelKit\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\FunnelKit\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\MondialRelay\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\MondialRelay\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\Elementor\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\Elementor\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\WooCommerceExtraProductOptions\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\WooCommerceExtraProductOptions\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\WooCommerceShipStation\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\WooCommerceShipStation\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\WooCommerceGermanized\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\WooCommerceGermanized\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\WooCommerceProductAddons\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\WooCommerceProductAddons\Package( $container, $this->version );
		} );
		$this->container->register( \PaymentPlugins\PPCP\SW_WAPF\Package::class, function ( $container ) {
			return new \PaymentPlugins\PPCP\SW_WAPF\Package( $container, $this->version );
		} );


		$this->container->register( PackageRegistry::class, function ( $container ) {
			return new PackageRegistry( $container );
		} );
		$this->container->register( PackageController::class, function ( $container ) {
			$package_controller = new PackageController( $container->get( PackageRegistry::class ) );
			$package_controller->set_packages( [
				\PaymentPlugins\PPCP\Blocks\Package::class,
				\PaymentPlugins\PPCP\CheckoutWC\Package::class,
				\PaymentPlugins\PPCP\Stripe\Package::class,
				\PaymentPlugins\PPCP\FunnelKit\Package::class,
				\PaymentPlugins\PPCP\MondialRelay\Package::class,
				\PaymentPlugins\PPCP\Elementor\Package::class,
				\PaymentPlugins\PPCP\WooCommerceExtraProductOptions\Package::class,
				\PaymentPlugins\PPCP\WooCommerceShipStation\Package::class,
				\PaymentPlugins\PPCP\WooCommerceGermanized\Package::class,
				\PaymentPlugins\PPCP\WooCommerceProductAddons\Package::class,
				\PaymentPlugins\PPCP\SW_WAPF\Package::class
			] );

			return $package_controller;
		} );
	}

	public function do_plugins_loaded() {
		include_once __DIR__ . '/wc-ppcp-functions.php';
		$this->load_text_domain();
		$this->declare_features();
	}

	public function load_text_domain() {
		load_plugin_textdomain( 'pymntpl-paypal-woocommerce', false, dirname( $this->plugin_name ) . '/i18n/languages' );
	}

	private function declare_features() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			add_action( 'before_woocommerce_init', function () {
				try {
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->file, true );
					\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', $this->file, true );
				} catch ( \Exception $e ) {
				}
			} );
		}
	}

	public function version() {
		return $this->version;
	}

}