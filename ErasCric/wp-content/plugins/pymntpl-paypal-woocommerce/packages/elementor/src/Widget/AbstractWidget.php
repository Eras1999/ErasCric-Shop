<?php

namespace PaymentPlugins\PPCP\Elementor\Widget;

use Elementor\Widget_Base;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\PaymentMethodRegistry;
use PaymentPlugins\WooCommerce\PPCP\TemplateLoader;

abstract class AbstractWidget extends Widget_Base {

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->initialize();
	}

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi
	 */
	protected $assets;

	protected $payment_method_registry;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi
	 */
	protected $asset_data;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\ContextHandler
	 */
	protected $context;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\TemplateLoader
	 */
	protected $template_loader;

	protected $widget_name;

	protected $frontend = false;

	protected $gateway;

	protected function initialize() {
		$container = Main::container();
		$this->set_assets( $container->get( AssetsApi::class ) );
		$this->set_payment_method_registry( $container->get( PaymentMethodRegistry::class ) );
		$this->set_asset_data( $container->get( AssetDataApi::class ) );
		$this->set_context_handler( $container->get( ContextHandler::class ) );
		$this->set_template_loader( $container->get( TemplateLoader::class ) );
	}

	public function set_assets( $assets ) {
		$this->assets = $assets;

		return $this;
	}

	public function set_payment_method_registry( $registry ) {
		$this->payment_method_registry = $registry;
	}

	public function set_asset_data( $asset_data ) {
		$this->asset_data = $asset_data;
	}

	public function set_context_handler( $context ) {
		$this->context = $context;
	}

	public function set_template_loader( $loader ) {
		$this->template_loader = $loader;
	}

	public function get_name() {
		return $this->widget_name;
	}

	public function register_scripts() {
	}

	public function get_script_dependencies( $context, $registry ) {
		return [];
	}

	public function before_render() {
		$this->frontend = true;
	}

	protected function get_paypal_editor_script() {
		return add_query_arg( [
			'client-id'      => 'sb',
			'components'     => 'buttons',
			'enable-funding' => 'paylater,venmo'
		], 'https://www.paypal.com/sdk/js' );
	}

	protected function get_gateway() {
		return $this->payment_method_registry->get( 'ppcp' );
	}

	public function is_frontend_request() {
		return $this->frontend;
	}

}