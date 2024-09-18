<?php


namespace PaymentPlugins\WooCommerce\PPCP\Assets;


use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;
use PaymentPlugins\WooCommerce\PPCP\Utils;

class AssetDataApi {

	private $data = [];

	private $handle = 'wc-ppcp-frontend-commons';

	public function __construct( $handle = null ) {
		if ( $handle ) {
			$this->handle = $handle;
		}
		add_action( 'wp_print_footer_scripts', [ $this, 'do_asset_data' ], 1 );
	}

	public function add( $key, $data ) {
		$this->data[ $key ] = $data;
	}

	public function get( $key ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
	}

	public function remove( $key ) {
		unset( $this->data[ $key ] );
	}

	public function get_data() {
		return $this->data;
	}

	public function has_data() {
		return ! empty( $this->data );
	}

	public function exists( $key ) {
		return \array_key_exists( $key, $this->data );
	}

	public function print_script_data( $data, $name ) {
		$data = rawurlencode( wp_json_encode( $data ) );
		wp_add_inline_script(
			$this->handle,
			"var $name = $name || JSON.parse( decodeURIComponent( '"
			. esc_js( $data )
			. "' ) );",
			'before'
		);
	}

	public function print_data( $name, $data ) {
		$data = rawurlencode( wp_json_encode( $data ) );
		echo "<script id=\"$name\">
				window['$name'] = JSON.parse( decodeURIComponent( '" . esc_js( $data ) . "' ) );
		</script>";
	}

	/**
	 * Outputs all asset data
	 */
	public function do_asset_data() {
		if ( wp_script_is( $this->handle, 'enqueued' ) ) {
			foreach ( $this->get_default_values() as $key => $data ) {
				$this->add( $key, $data );
			}
			/**
			 * @var ContextHandler $context_handler
			 */
			$context_handler = Main::container()->get( ContextHandler::class );
			do_action( 'wc_ppcp_add_script_data', $this, $context_handler );
			$this->print_data( 'wcPPCPSettings', $this->data );
		}
	}

	public function trigger_add_script_data( ...$args ) {
		do_action( 'wc_ppcp_add_script_data', $this, ...$args );
	}

	public function get_default_values() {
		$data = [];
		global $product;
		global $wp;
		if ( WC()->cart ) {
			$data['cart'] = Utils::get_cart_data( WC()->cart );
		}
		if ( $product && \is_object( $product ) ) {
			if ( $product instanceof \WP_Post && $product->post_type === 'product' ) {
				$product = wc_get_product( $product->ID );
			}
			$data['product'] = Utils::get_product_data( $product );
		}
		if ( isset( $wp->query_vars['order-pay'] ) ) {
			$order = Utils::get_order_from_query_vars();
			if ( $order ) {
				$data['order'] = Utils::get_order_data( $order );
			}
		}

		return $data;
	}

}