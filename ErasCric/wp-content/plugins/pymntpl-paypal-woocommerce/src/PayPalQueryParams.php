<?php

namespace PaymentPlugins\WooCommerce\PPCP;

use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;

/**
 * @property string $intent
 * @property string $vault
 * @property string $commit
 * @property array  $components
 * @property string $currency
 * @property string $enableFunding;
 * @protected string $locale
 */
class PayPalQueryParams {

	const QUERY_PARAMS = 'queryParams';

	private $params = [
		'client-id'                   => 'sb',
		'intent'                      => '',
		'vault'                       => 'false',
		'commit'                      => 'true',
		'components'                  => [ 'buttons', 'messages' ],
		'currency'                    => '',
		'enable-funding'              => [ 'paylater' ],
		'data-partner-attribution-id' => 'PaymentPlugins_PCP'
	];

	private $asset_data;

	protected $api_settings;

	public $flow = 'checkout';

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\ContextHandler
	 */
	protected $context_handler;

	public function __construct( AssetDataApi $asset_data, APISettings $api_settings ) {
		$this->asset_data   = $asset_data;
		$this->api_settings = $api_settings;
		$this->initialize();
	}

	public function __set( string $name, $value ): void {
		switch ( $name ) {
			case 'enableFunding';
				$name = 'enable-funding';
				break;
		}
		$this->params[ $name ] = $value;
	}

	public function __get( string $name ) {
		return isset( $this->params[ $name ] ) ? $this->params[ $name ] : '';
	}

	private function initialize() {
		add_action( 'wc_ppcp_add_script_data', [ $this, 'add_script_data' ], 10 );
		add_filter( 'wc_ppcp_cart_data', [ $this, 'add_cart_data' ], 10 );
		add_filter( 'wc_ppcp_post_cart/refresh', [ $this, 'add_cart_data' ], 10 );
	}

	public function add_script_data() {
		$this->initialize_paypal_flow();
		$this->initialize_query_params();
		$this->asset_data->add( self::QUERY_PARAMS, $this->prepare_query_params() );
	}

	public function add_cart_data( $data ) {
		$this->initialize_paypal_flow();
		$this->initialize_query_params();
		$data[ self::QUERY_PARAMS ] = $this->prepare_query_params();

		return $data;
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler $context_handler
	 *
	 * @return mixed|string|void
	 */
	protected function initialize_paypal_flow() {
		if ( $this->flow !== 'vault' ) {
			$this->flow = apply_filters( 'wc_ppcp_get_paypal_flow', $this->flow, $this->context_handler );
		}

		return $this->flow;
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler $context
	 */
	protected function initialize_query_params() {
		$this->params['client-id'] = $this->api_settings->get_client_id();
		$this->currency            = get_woocommerce_currency();
		$this->intent              = 'capture';

		if ( $this->context_handler->is_order_pay() ) {
			$order = Utils::get_order_from_query_vars();
			if ( $order ) {
				$this->currency = $order->get_currency();
			}
		}

		if ( $this->flow == 'vault' ) {
			$this->intent = 'tokenize';
			$this->vault  = 'true';
		}

		do_action( 'wc_ppcp_paypal_query_params', $this, $this->context_handler );
	}

	protected function prepare_query_params() {
		$params = [];
		foreach ( $this->params as $key => $value ) {
			if ( \is_array( $value ) ) {
				$value = implode( ',', $value );
			}
			$params[ $key ] = $value;
		}

		return $params;
	}

	public function set_context_handler( $context ) {
		$this->context_handler = $context;
	}

	public function add_enabled_funding( $type ) {
		$this->params['enable-funding'][] = $type;
	}

}