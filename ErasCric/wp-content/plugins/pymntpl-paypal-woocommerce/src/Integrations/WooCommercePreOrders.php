<?php


namespace PaymentPlugins\WooCommerce\PPCP\Integrations;


use PaymentPlugins\PayPalSDK\Order;
use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\PaymentHandler;
use PaymentPlugins\WooCommerce\PPCP\PaymentResult;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\CartCheckout;
use PaymentPlugins\WooCommerce\PPCP\Utilities\PayPalFee;
use PaymentPlugins\WooCommerce\PPCP\Utils;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

class WooCommercePreOrders implements PluginIntegrationType {

	public $id = 'woocommerce_preorders';

	private $client;

	private $factories;

	private $log;

	public function __construct( WPPayPalClient $client, CoreFactories $factories, Logger $log ) {
		$this->client    = $client;
		$this->factories = $factories;
		$this->log       = $log;
	}

	public function is_active() {
		return class_exists( 'WC_Pre_Orders' );
	}

	public function initialize() {
		add_filter( 'wc_ppcp_get_paypal_flow', [ $this, 'get_paypal_flow' ], 10, 2 );
		add_filter( 'wc_ppcp_process_payment_result', [ $this, 'process_payment' ], 10, 3 );
		add_action( 'wc_pre_orders_process_pre_order_completion_payment_ppcp', [ $this, 'process_order_completion_payment' ] );
		add_action( 'wc_ppcp_rest_handle_checkout_validation', [ $this, 'handle_checkout_validation' ] );
	}

	/**
	 * @param                                                 $flow
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler $context
	 *
	 * @return mixed|string
	 */
	public function get_paypal_flow( $flow, $context ) {
		if ( $flow == Constants::VAULT ) {
			return $flow;
		}
		if ( $context->has_context( [ $context::CART, $context::CHECKOUT, $context::SHOP ] ) ) {
			if ( \WC_Pre_Orders_Cart::cart_contains_pre_order() ) {
				$flow = Constants::VAULT;
			}
		} elseif ( $context->is_order_pay() ) {
			$order = Utils::get_order_from_query_vars();
			if ( \WC_Pre_Orders_Order::order_contains_pre_order( $order ) ) {
				$flow = Constants::VAULT;
			}
		} elseif ( $context->is_product() ) {
			global $product;
			if ( is_a( $product, 'WC_Product' ) && \WC_Pre_Orders_Product::product_can_be_pre_ordered( $product ) ) {
				$flow = Constants::VAULT;
			}
		}

		return $flow;
	}

	/**
	 * @param mixed           $result
	 * @param \WC_Order       $order
	 * @param AbstractGateway $payment_method
	 */
	public function process_payment( $result, \WC_Order $order, AbstractGateway $payment_method ) {
		if ( \WC_Pre_Orders_Order::order_contains_pre_order( $order ) && \WC_Pre_Orders_Order::order_requires_payment_tokenization( $order ) ) {
			$billing_token = $payment_method->get_billing_token_from_request();
			if ( $billing_token ) {
				$billing_agreement = $this->client->billingAgreements->create( [ 'token_id' => $billing_token ] );
				if ( is_wp_error( $billing_agreement ) ) {
					return $billing_agreement;
				}
				$token = $payment_method->get_payment_method_token_instance();
				$token->initialize_from_payer( $billing_agreement->payer->payer_info );
				$order->set_payment_method_title( $token->get_payment_method_title() );
				$order->update_meta_data( Constants::BILLING_AGREEMENT_ID, $billing_agreement->id );
				$order->update_meta_data( Constants::PPCP_ENVIRONMENT, $this->client->getEnvironment() );
				$order->update_meta_data( Constants::PAYER_ID, $token->get_payer_id() );
				$order->save();
				$payment_method->payment_handler->set_use_billing_agreement( true );
				\WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );
				$result = true;
			} else {
				$this->factories->initialize( $order );
				$this->factories->billingAgreement->set_needs_shipping( false );
				$params = $this->factories->billingAgreement->from_order( $payment_method );
				$token  = $this->client->orderMode( $order )->billingAgreementTokens->create( $params );
				if ( is_wp_error( $token ) ) {
					return $token;
				}

				return [
					'result'   => 'success',
					'redirect' => $token->getApprovalUrl()
				];
			}
		}

		return $result;
	}

	public function process_order_completion_payment( \WC_Order $order ) {
		/**
		 * @var PaymentHandler $payment_handler
		 */
		$payment_handler = Main::container()->get( PaymentHandler::class );
		$this->factories->initialize( $order );

		$payment_method = WC()->payment_gateways()->payment_gateways()[ $order->get_payment_method() ];
		try {
			$params       = apply_filters( 'wc_ppcp_preorder_order_params', $payment_handler->get_create_order_params( $order ), $order, $payment_handler );
			$paypal_order = $this->client->orders->create( $params );
			if ( is_wp_error( $paypal_order ) ) {
				throw new \Exception( $paypal_order->get_error_message() );
			}
			$payment_source = [
				'payment_source' => $this->factories->paymentSource->from_order()
			];
			if ( Order::CAPTURE == $paypal_order->intent ) {
				$paypal_order = $this->client->orders->capture( $paypal_order->id, $payment_source );
			} else {
				$paypal_order = $this->client->orders->authorize( $paypal_order->id, $payment_source );
			}
			if ( is_wp_error( $paypal_order ) ) {
				throw new \Exception( $paypal_order->get_error_message() );
			}
			$result = new PaymentResult( $paypal_order, $order, null );
			if ( $result->success() ) {
				if ( $result->is_captured() ) {
					PayPalFee::add_fee_to_order( $order, $result->get_capture()->getSellerReceivableBreakdown(), false );
					$order->payment_complete( $result->get_capture_id() );
				} else {
					$order->update_meta_data( Constants::AUTHORIZATION_ID, $result->get_authorization_id() );
					$order->set_status( apply_filters( 'wc_ppcp_authorized_preorder_status', $payment_method->get_option( 'authorize_status', 'on-hold' ), $order, $paypal_order, $this ) );
				}
				$payment_handler->save_order_meta_data( $order, $paypal_order );
				$payment_handler->add_payment_complete_message( $order, $result );
			} else {
				throw new \Exception( $result->get_error_message() );
			}
		} catch ( \Exception $e ) {
			$order->update_status( 'failed' );
			$order->add_order_note( sprintf( __( 'Payment for pre-order failed. Reason: %s', 'pymntpl-paypal-woocommerce' ), $e->getMessage() ) );
		}
	}

	/**
	 * If the cart contains a pre-order and shipping is required, redirect to the checkout page
	 * so the customer can select their shipping method
	 *
	 * @param CartCheckout $route
	 */
	public function handle_checkout_validation( $route ) {
		if ( in_array( $route->request->get_param( 'context' ), [ 'product', 'cart' ] ) ) {
			$key = "{$route->payment_method->id}_billing_token";
			if ( \WC_Pre_Orders_Cart::cart_contains_pre_order() && isset( $route->request[ $key ] ) ) {
				if ( WC()->cart->needs_shipping() ) {
					wc_add_notice( __( 'Please select a shipping method for your order.', 'pymntpl-paypal-woocommerce' ), 'notice' );
					wp_send_json(
						[
							'result'   => 'success',
							'redirect' => $route->get_order_review_url( [ $key => $route->request->get_param( $key ) ] ),
							'reload'   => false,
						],
						200
					);
				}
			}
		}
	}

}