<?php


namespace PaymentPlugins\PPCP\Blocks\Payments\Gateways;


use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;
use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;

/**
 * Class AbstractGateway
 *
 * @package PaymentPlugins\PPCP\Blocks\Payments\Gateways
 */
class AbstractGateway extends AbstractPaymentMethodType {

	protected $client;

	protected $assets_api;

	public function __construct( PayPalClient $client, AssetsApi $assets_api ) {
		$this->client     = $client;
		$this->assets_api = $assets_api;
	}

	public function initialize() {
		$this->settings = \get_option( "woocommerce_{$this->name}_settings", [] );
	}

	public function is_active() {
		return wc_string_to_bool( $this->get_setting( 'enabled', 'no' ) );
	}

	public function get_payment_method_data() {
		return [
			'title'    => $this->get_setting( 'title_text' ),
			'features' => $this->get_supported_features(),
			'icons'    => $this->get_payment_method_icons()
		];
	}

	public function get_supported_features() {
		return [
			'tokenization',
			'subscriptions',
			'products',
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
			'subscription_payment_method_change_customer'
		];
	}

	public function get_payment_method_icons() {
		return [];
	}

	protected function is_redirect_with_order() {
		return isset( $_REQUEST['_ppcp_order_review'] );
	}

}