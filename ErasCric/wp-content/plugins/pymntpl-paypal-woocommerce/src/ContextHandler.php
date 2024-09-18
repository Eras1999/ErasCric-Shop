<?php

namespace PaymentPlugins\WooCommerce\PPCP;

/**
 * Keeps track of the page "context"
 */
class ContextHandler {

	const CHECKOUT = 'checkout';

	const CART = 'cart';

	const ORDER_PAY = 'order_pay';

	const ADD_PAYMENT_METHOD = 'add_payment_method';

	const PRODUCT = 'product';

	const ORDER_RECEIVED = 'order_received';

	const SHOP = 'shop';

	const ACCOUNT = 'shop';

	/**
	 * @var string
	 */
	private $context;

	public function __construct() {
		add_action( 'wp', [ $this, 'initialize' ] );
		if ( is_ajax() ) {
			add_action( 'woocommerce_before_calculate_totals', [ $this, 'initialize' ] );
		}
	}

	public function set_context( $context ) {
		$this->context = $context;
	}

	public function get_context() {
		return $this->context;
	}

	public function initialize() {
		global $post;
		if ( ! $this->context ) {
			if ( is_checkout() ) {
				if ( is_checkout_pay_page() ) {
					$this->context = self::ORDER_PAY;
				} elseif ( is_order_received_page() ) {
					$this->context = self::ORDER_RECEIVED;
				} else {
					$this->context = self::CHECKOUT;
				}
			} elseif ( is_add_payment_method_page() ) {
				$this->context = self::ADD_PAYMENT_METHOD;
			} elseif ( is_cart() ) {
				$this->context = self::CART;
			} elseif ( is_product() || ( $post && ! empty( $post->post_content ) && strstr( $post->post_content, '[product_page' ) ) ) {
				$this->context = self::PRODUCT;
			} elseif ( is_shop() ) {
				$this->context = self::SHOP;
			} elseif ( is_account_page() ) {
				$this->context = self::ACCOUNT;
			}
			do_action( 'wc_ppcp_initialize_page_context', $this );
		}
	}

	public function is_frontend() {
		return ! is_admin() && ! defined( 'DOING_CRON' );
	}

	private function is_context( $page ) {
		$this->initialize();

		return $this->context === $page;
	}

	public function is_checkout() {
		return $this->is_context( self::CHECKOUT );
	}

	public function is_add_payment_method() {
		return $this->is_context( self::ADD_PAYMENT_METHOD );
	}

	public function is_cart() {
		return $this->is_context( self::CART );
	}

	public function is_product() {
		return $this->is_context( self::PRODUCT );
	}

	public function is_order_pay() {
		return $this->is_context( self::ORDER_PAY );
	}

	public function is_order_received() {
		return $this->is_context( self::ORDER_RECEIVED );
	}

	public function is_shop() {
		return $this->is_context( self::SHOP );
	}

	public function has_context( $contexts = [] ) {
		if ( is_string( $contexts ) ) {
			$contexts = [ $contexts ];
		}

		return \in_array( $this->context, $contexts );
	}

	public function get_order_from_query(){
		global $wp;
		return wc_get_order( absint( $wp->query_vars['order-pay'] ));
	}

}