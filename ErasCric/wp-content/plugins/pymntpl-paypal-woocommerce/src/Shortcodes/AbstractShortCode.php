<?php

namespace PaymentPlugins\WooCommerce\PPCP\Shortcodes;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Container\Container;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\TemplateLoader;

abstract class AbstractShortCode {

	public $id;

	protected $assets;

	protected $assets_data;

	protected $templates;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Shortcodes\ShortcodeAttributes
	 */
	protected $attributes;

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways
	 */
	protected $payment_gateways;

	public function __construct( AssetsApi $assets, TemplateLoader $templates ) {
		$this->assets    = $assets;
		$this->templates = $templates;
	}

	public function get_id() {
		return $this->id;
	}

	public function set_payment_gateways( $payment_gateways ) {
		$this->payment_gateways = $payment_gateways;
	}

	public function set_assets_data( AssetDataApi $assets_data ) {
		$this->assets_data = $assets_data;
	}

	public function set_attributes( ShortcodeAttributes $attributes ) {
		$this->attributes = $attributes;
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi $data
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler      $context
	 *
	 * @return void
	 */
	public function add_shortcode_script_data( $data, $context ) {
	}

	/**
	 * Given an array of attributes, parse them into their desired format.
	 *
	 * @return void
	 */
	public function parse_attributes( $attributes ) {
		return $attributes;
	}

	public function get_supported_pages() {
		return [];
	}

	public function is_supported_page( ContextHandler $context ) {
		return true;
	}

	public function initialize_properties(Container $container){

	}

	public function before_render() {
	}

	abstract public function render();

}