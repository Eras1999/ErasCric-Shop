<?php

namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;

use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;

class CartRefresh extends AbstractRoute {

	private $context;

	public function __construct( ContextHandler $context ) {
		$this->context = $context;
	}

	public function get_path() {
		return 'cart/refresh';
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'handle_request' ]
			]
		];
	}

	public function handle_post_request( \WP_REST_Request $request ) {
		$cart = WC()->cart;

		if ( $request['page'] === 'checkout' ) {
			wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );
			$this->context->set_context( $this->context::CHECKOUT );
		} else {
			wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
			$this->context->set_context( $this->context::CART );
		}

		return [
			'cart' => [
				'total'         => NumberUtil::round( $cart->total, 2 ),
				'needsShipping' => $cart->needs_shipping()
			]
		];
	}

}