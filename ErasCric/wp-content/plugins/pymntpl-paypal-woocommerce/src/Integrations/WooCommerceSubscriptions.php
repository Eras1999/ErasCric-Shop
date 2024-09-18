<?php


namespace PaymentPlugins\WooCommerce\PPCP\Integrations;

use PaymentPlugins\PayPalSDK\Order;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\Logger;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\PaymentHandler;
use PaymentPlugins\WooCommerce\PPCP\PaymentResult;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;
use PaymentPlugins\WooCommerce\PPCP\Rest\Routes\CartCheckout;
use PaymentPlugins\WooCommerce\PPCP\Tokens\AbstractToken;
use PaymentPlugins\WooCommerce\PPCP\Utilities\PayPalFee;
use PaymentPlugins\WooCommerce\PPCP\Utils;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

/**
 * Class WooCommerceSubscriptions
 *
 * @package PaymentPlugins\WooCommerce\PPCP\Integrations
 */
class WooCommerceSubscriptions implements PluginIntegrationType {

	public $id = 'woocommerce_subscriptions';

	private $client;

	private $factories;

	private $log;

	public function __construct( WPPayPalClient $client, CoreFactories $factories, Logger $log ) {
		$this->client    = $client;
		$this->factories = $factories;
		$this->log       = $log;
	}

	public function is_active() {
		return function_exists( 'wcs_is_subscription' );
	}

	public function initialize() {
		add_filter( 'wc_ppcp_process_payment_result', [ $this, 'process_payment' ], 10, 3 );
		add_filter( 'wc_ppcp_get_paypal_flow', [ $this, 'get_paypal_flow' ], 10, 2 );
		add_filter( 'wc_ppcp_get_formatted_cart_item', [ $this, 'get_formatted_cart_item' ], 10, 2 );
		add_action( 'wc_ppcp_rest_handle_checkout_validation', [ $this, 'handle_checkout_validation' ] );
		add_action( 'woocommerce_scheduled_subscription_payment_ppcp', [ $this, 'scheduled_subscription_payment' ], 10, 2 );
		add_filter( 'woocommerce_subscription_payment_meta', [ $this, 'add_subscription_payment_meta' ], 10, 2 );
		add_filter( 'woocommerce_subscription_failing_payment_method_updated_ppcp', [ $this, 'update_failing_payment_method' ], 10, 2 );
	}

	/**
	 * @param mixed           $result
	 * @param \WC_Order       $order
	 * @param AbstractGateway $payment_method
	 */
	public function process_payment( $result, \WC_Order $order, AbstractGateway $payment_method ) {
		if ( $this->is_change_payment_method_request() && \wcs_is_subscription( $order ) ) {
			return $this->process_change_payment_method_request( $order, $payment_method );
		} elseif ( wcs_order_contains_subscription( $order ) || wcs_order_contains_renewal( $order ) ) {
			// Order contains a subscription. Create the billing agreement
			$billing_token = $payment_method->get_billing_token_from_request();
			if ( ! $billing_token ) {
				// There is no billing token so create one and redirect to approval page.
				$this->factories->initialize( $order );
				$this->factories->billingAgreement->set_needs_shipping( false );
				$params = $this->factories->billingAgreement->from_order();
				$token  = $this->client->orderMode( $order )->billingAgreementTokens->create( $params );
				if ( is_wp_error( $token ) ) {
					return $token;
				}

				return [
					'result'   => 'success',
					'redirect' => $token->getApprovalUrl()
				];
			}

			$this->log->info( sprintf( 'Creating billing agreement via %s. Billing agreement token: %s. Order ID: %s', __METHOD__, $billing_token, $order->get_id() ), 'payment' );


			$billing_agreement = $this->client->billingAgreements->create( [ 'token_id' => $billing_token ] );
			if ( is_wp_error( $billing_agreement ) ) {
				return $billing_agreement;
			}

			$this->log->info( sprintf( 'Billing agreement %s created via %s. Billing agreement token: %s. Order ID: %s', $billing_agreement->id, __METHOD__, $billing_token, $order->get_id() ), 'payment' );

			$token = $payment_method->get_payment_method_token_instance();
			$token->initialize_from_payer( $billing_agreement->payer->payer_info );
			$order->set_payment_method_title( $token->get_payment_method_title() );
			$order->update_meta_data( Constants::BILLING_AGREEMENT_ID, $billing_agreement->id );
			$order->update_meta_data( Constants::PPCP_ENVIRONMENT, $this->client->getEnvironment() );
			$order->update_meta_data( Constants::PAYER_ID, $token->get_payer_id() );
			$order->save();
			$this->save_order_meta( $order, $token );
			$payment_method->payment_handler->set_use_billing_agreement( true );
			if ( 0 == $order->get_total() ) {
				if ( $payment_method->get_option( 'intent' ) === 'capture' ) {
					$order->payment_complete();
				} else {
					$order->update_status( apply_filters( 'wc_ppcp_authorized_order_status', $payment_method->get_option( 'authorize_status', 'on-hold' ) ) );
				}
				$result = true;
			} else {
				$result = false;
			}
		}

		return $result;
	}

	private function save_order_meta( \WC_Order $order, AbstractToken $token ) {
		foreach ( wcs_get_subscriptions_for_order( $order ) as $subscription ) {
			$subscription->set_payment_method_title( $token->get_payment_method_title() );
			$subscription->update_meta_data( Constants::PPCP_ENVIRONMENT, $order->get_meta( Constants::PPCP_ENVIRONMENT ) );
			$subscription->update_meta_data( Constants::BILLING_AGREEMENT_ID, $order->get_meta( Constants::BILLING_AGREEMENT_ID ) );
			$subscription->update_meta_data( Constants::PAYER_ID, $order->get_meta( Constants::PAYER_ID ) );
			$subscription->save();
		}
	}

	private function process_change_payment_method_request( \WC_Order $order, AbstractGateway $payment_method ) {
		// create billing agreement and associate to the subscription
		$billing_token = $payment_method->get_billing_token_from_request();
		try {
			if ( $billing_token ) {
				$billing_agreement = $this->client->billingAgreements->create( [ 'token_id' => $billing_token ] );
				if ( is_wp_error( $billing_agreement ) ) {
					throw new \Exception( $billing_agreement->get_error_message() );
				}
				// save the payment method info to the subscription
				$token = $payment_method->get_payment_method_token_instance();
				$token->initialize_from_payer( $billing_agreement->payer->payer_info );
				$order->set_payment_method_title( $token->get_payment_method_title() );
				$order->update_meta_data( Constants::BILLING_AGREEMENT_ID, $billing_agreement->id );
				$order->update_meta_data( Constants::PAYER_ID, $token->get_payer_id() );
				$order->save();
			} else {
				// There is no billing token so create one and redirect to approval page.
				$this->factories->initialize( $order );
				$this->factories->billingAgreement->set_needs_shipping( false );
				$params                                               = $this->factories->billingAgreement->from_order();
				$params['plan']['merchant_preferences']['return_url'] = add_query_arg( [
					'change_payment_method' => $order->get_id()
				], $params['plan']['merchant_preferences']['return_url'] );

				$params['plan']['merchant_preferences']['cancel_url'] = add_query_arg( [ 'change_payment_method' => $order->get_id(), '_wpnonce' => wp_create_nonce() ], $order->get_checkout_payment_url() );

				$token = $this->client->orderMode( $order )->billingAgreementTokens->create( $params );
				if ( is_wp_error( $token ) ) {
					throw new \Exception( ( $token->get_error_message() ) );
				}

				return [
					'result'   => 'success',
					'redirect' => $token->getApprovalUrl()
				];
			}
		} catch ( \Exception $e ) {
			return new \WP_Error( sprintf( __( 'Error saving payment method for subscription. Reason: %s', 'pymntpl-paypal-woocommerce' ), $e->getMessage() ) );
		}

		return [ 'result' => 'success', 'redirect' => wc_get_page_permalink( 'myaccount' ) ];
	}

	private function is_change_payment_method_request() {
		return did_action( 'woocommerce_subscriptions_pre_update_payment_method' )
		       || \WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment;
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
		if ( $context->is_cart() || $context->is_checkout() || $context->is_shop() ) {
			if ( \WC_Subscriptions_Cart::cart_contains_subscription() || \wcs_cart_contains_renewal() ) {
				$flow = Constants::VAULT;
			} elseif ( \WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment ) {
				$flow = Constants::VAULT;
			}
		} elseif ( $context->is_order_pay() ) {
			$order = Utils::get_order_from_query_vars();
			if ( \WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment || \wcs_order_contains_subscription( $order ) ) {
				$flow = Constants::VAULT;
			}
		} elseif ( $context->is_product() ) {
			global $product;
			if ( \WC_Subscriptions_Cart::cart_contains_subscription() ) {
				$flow = Constants::VAULT;
			} elseif ( is_a( $product, 'WC_Product' ) && \WC_Subscriptions_Product::is_subscription( $product ) ) {
				$flow = Constants::VAULT;
			}
		}

		return $flow;
	}

	/**
	 * @param float     $amount
	 * @param \WC_Order $order
	 */
	public function scheduled_subscription_payment( $amount, \WC_Order $order ) {
		/**
		 * @var PaymentHandler $payment_handler
		 */
		$payment_handler = Main::container()->get( PaymentHandler::class );
		$this->factories->initialize( $order );

		$payment_method = WC()->payment_gateways()->payment_gateways()[ $order->get_payment_method() ];
		try {
			$params       = apply_filters( 'wc_ppcp_renewal_order_params', $payment_handler->get_create_order_params( $order ), $order, $payment_handler );
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
					$order->set_status( apply_filters( 'wc_ppcp_authorized_renewal_order_status', $payment_method->get_option( 'authorize_status', 'on-hold' ), $order, $paypal_order, $this ) );
				}
				$payment_handler->save_order_meta_data( $order, $paypal_order );
				$payment_handler->add_payment_complete_message( $order, $result );
			} else {
				throw new \Exception( $result->get_error_message() );
			}
		} catch ( \Exception $e ) {
			$order->update_status( 'failed' );
			$order->add_order_note( sprintf( __( 'Recurring payment failed. Reason: %s', 'pymntpl-paypal-woocommerce' ), $e->getMessage() ) );
		}
	}

	/**
	 * @param array            $payment_meta
	 * @param \WC_Subscription $subscription
	 */
	public function add_subscription_payment_meta( $payment_meta, $subscription ) {
		$payment_meta['ppcp'] = [
			'post_meta' => [
				Constants::BILLING_AGREEMENT_ID => [
					'value' => $subscription->get_meta( Constants::BILLING_AGREEMENT_ID ),
					'label' => __( 'Billing Agreement ID', 'pymntpl-paypal-woocommerce' ),
				]
			]
		];

		return apply_filters( 'wc_ppcp_add_subscription_payment_meta', $payment_meta, $subscription );
	}

	/**
	 * @param array      $data
	 * @param array|null $cart_item
	 *
	 * @return mixed
	 */
	public function get_formatted_cart_item( $data, $cart_item ) {
		if ( $cart_item && \WC_Subscriptions_Product::is_subscription( $cart_item['data'] ) ) {
			if ( \WC_Subscriptions_Product::get_trial_length( $cart_item['data'] ) > 0 ) {
				$data['unit_amount']['value'] = 0;
			}
		}

		return $data;
	}

	/**
	 * @param \WC_Subscription $subscription
	 * @param \WC_Order        $renewal_order
	 */
	public function update_failing_payment_method( $subscription, $renewal_order ) {
		$billing_agreement = $renewal_order->get_meta( Constants::BILLING_AGREEMENT_ID );
		if ( $billing_agreement ) {
			$result = $this->client->orderMode( $renewal_order )->billingAgreements->retrieve( $billing_agreement );
			if ( ! is_wp_error( $result ) ) {
				$payment_method = WC()->payment_gateways()->payment_gateways()[ $renewal_order->get_payment_method() ];
				$token          = $payment_method->get_payment_method_token_instance();
				$token->initialize_from_payer( $result->payer->payer_info );
				$subscription->set_payment_method_title( $token->get_payment_method_title() );
			}
			$subscription->update_meta_data( Constants::BILLING_AGREEMENT_ID, $billing_agreement );
			$subscription->save();
		}
	}

	/**
	 * If the cart contains a subscription and shipping is required, redirect to the checkout page
	 * so the customer can select their shipping method
	 *
	 * @param CartCheckout $route
	 */
	public function handle_checkout_validation( $route ) {
		if ( in_array( $route->request->get_param( 'context' ), [ 'product', 'cart' ] ) ) {
			$key = "{$route->payment_method->id}_billing_token";
			if ( \WC_Subscriptions_Cart::cart_contains_subscription() && isset( $route->request[ $key ] ) ) {
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