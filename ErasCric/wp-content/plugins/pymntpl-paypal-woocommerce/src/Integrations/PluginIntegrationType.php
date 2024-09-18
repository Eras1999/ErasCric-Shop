<?php


namespace PaymentPlugins\WooCommerce\PPCP\Integrations;


use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;

interface PluginIntegrationType {

	public function is_active();

	public function initialize();
}