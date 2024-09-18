<?php


namespace PaymentPlugins\WooCommerce\PPCP;


use PaymentPlugins\PayPalSDK\Capture;
use PaymentPlugins\PayPalSDK\Order;
use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\PayPalSDK\PatchRequest;
use PaymentPlugins\PayPalSDK\PurchaseUnit;
use PaymentPlugins\PayPalSDK\ShippingAddress;
use PaymentPlugins\WooCommerce\PPCP\Cache\CacheInterface;
use PaymentPlugins\WooCommerce\PPCP\Exception\RetryException;
use PaymentPlugins\WooCommerce\PPCP\Factories\CoreFactories;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;
use PaymentPlugins\WooCommerce\PPCP\Utilities\NumberUtil;
use PaymentPlugins\WooCommerce\PPCP\Utilities\OrderFilterUtil;
use PaymentPlugins\WooCommerce\PPCP\Utilities\OrderLock;
use PaymentPlugins\WooCommerce\PPCP\Utilities\PayPalFee;

class PaymentHandler {

	public $client;

	private $factories;

	private $cache;

	/**
	 * @var AbstractGateway
	 */
	protected $payment_method;

	private $current_status = [];

	private $use_billing_agreement = false;

	public function __construct( WPPayPalClient $client, CoreFactories $factories, CacheInterface $cache ) {
		$this->client    = $client;
		$this->factories = $factories;
		$this->cache     = $cache;
	}

	public function set_payment_method( AbstractGateway $payment_method ) {
		$this->payment_method = $payment_method;
	}

	public function set_use_billing_agreement( bool $bool ) {
		$this->use_billing_agreement = $bool;
	}

	public function process_payment( \WC_Order $order ) {
		$this->set_processing( 'payment' );
		$this->factories->initialize( $order );
		$paypal_order = null;
		$needs_update = false;
		try {
			if ( $this->use_billing_agreement ) {
				$args = $this->get_create_order_params( $order );
				$this->payment_method->logger->info(
					sprintf( 'Creating PayPal order with billing agreement via %s. Order ID: %s. Args: %s', __METHOD__, $order->get_id(), print_r( $args->toArray(), true ) ),
					'payment'
				);

				$paypal_order = $this->client->orderMode( $order )->orders->create( $args );
			} else {
				$paypal_order_id = $this->get_paypal_order_id_from_request();
				if ( ! $paypal_order_id ) {
					$paypal_order_id = $this->cache->get( Constants::PAYPAL_ORDER_ID );
					// If there isn't an existing PayPal order ID or this payment method is using the Place Order
					// button, create a PayPal order.
					if ( ! $paypal_order_id || $this->payment_method->is_place_order_button() ) {
						$args = $this->get_create_order_params( $order );

						$this->payment_method->logger->info(
							sprintf( 'Creating PayPal order via %s. Order ID: %s. Args: %s', __METHOD__, $order->get_id(), print_r( $args->toArray(), true ) ),
							'payment'
						);

						$paypal_order = $this->client->orderMode( $order )->orders->create( $args );
					}
				}
				if ( ! $paypal_order ) {
					$needs_update = true;
					$paypal_order = $this->client->orderMode( $order )->orders->retrieve( $paypal_order_id );
					$this->validate_paypal_order( $paypal_order, $order );
				}
			}
			if ( is_wp_error( $paypal_order ) ) {
				throw new \Exception( $paypal_order->get_error_message() );
			}
			$paypal_order_id = $paypal_order->getId();
			if ( ! $paypal_order->isComplete() ) {
				if ( ! $this->use_billing_agreement && $needs_update ) {
					// update the order, so it has the most recent order data.
					$response = $this->client->orders->update( $paypal_order->getId(), $this->get_update_order_params( $order, $paypal_order ) );
					if ( is_wp_error( $response ) ) {
						throw new \Exception( $response->get_error_message() );
					}
				}
				// only try to process payment if this order is the result of a billing agreement (subscription, pre-order etc)
				// or the order has been approved.
				if ( ( $this->use_billing_agreement && $paypal_order->isCreated() ) || $paypal_order->getStatus() === Order::APPROVED ) {
					if ( Order::CAPTURE === $paypal_order->intent ) {
						$this->payment_method->logger->info( sprintf( 'Capturing payment for PayPal order %s via %s. Order ID: %s', $paypal_order->getId(), __METHOD__, $order->get_id() ), 'payment' );
						OrderLock::set_order_lock( $order );
						$paypal_order = $this->client->orders->capture( $paypal_order->getId(), $this->get_payment_source( $order ) );
					} else {
						$this->payment_method->logger->info( sprintf( 'Authorizing payment for PayPal order %s via %s. Order ID: %s', $paypal_order->getId(), __METHOD__, $order->get_id() ), 'payment' );

						$paypal_order = $this->client->orders->authorize( $paypal_order->getId(), $this->get_payment_source( $order ) );
					}
				}
			}
			$result = new PaymentResult( $paypal_order, $order, $this->payment_method );
			$result->set_paypal_order_id( $paypal_order_id );
			$result->set_environment( $this->client->getEnvironment() );

			// If the order doesn't need approval, then we can proceed with processing it.
			if ( ! $result->needs_approval() ) {
				if ( $result->success() ) {
					$this->payment_complete( $order, $result );
				} else {
					if ( $result->already_captured() || $result->already_authorized() ) {
						$paypal_order = $this->client->orders->retrieve( $paypal_order_id );
						$result->initialize( $paypal_order );
						$this->payment_complete( $order, $result );
					} else {
						$order->update_status( 'failed' );
						$order->add_order_note( sprintf( __( 'Error processing payment. Reason: %s', 'pymntpl-paypal-woocommerce' ),
							$result->get_error_message() ) );
					}
				}
			}

			OrderLock::release_order_lock( $order );

			return $result;
		} catch ( RetryException $e ) {
			return $this->process_payment( $order );
		} catch ( \Exception $e ) {
			return new PaymentResult( false, $order, $this->payment_method, $e->getMessage() );
		}
	}

	public function get_payment_source( \WC_Order $order ) {
		if ( $this->use_billing_agreement ) {
			$this->factories->initialize( $order );

			return [ 'payment_source' => $this->factories->paymentSource->from_order() ];
		}

		return [];
	}

	/**
	 * @param \WC_Order     $order
	 * @param PaymentResult $result
	 */
	public function payment_complete( \WC_Order $order, PaymentResult $result ) {
		if ( $result->is_captured() ) {
			PayPalFee::add_fee_to_order( $order, $result->get_capture()->getSellerReceivableBreakdown(), false );
			$capture = $result->get_capture();
			if ( $capture->getStatus() === Capture::PENDING ) {
				$order->update_meta_data( Constants::CAPTURE_STATUS, Capture::PENDING );
				$order->set_transaction_id( $capture->getId() );
				$order->update_status( apply_filters( 'wc_ppcp_capture_pending_order_status', 'on-hold', $order, $result ),
					sprintf( __( 'PayPal capture status is pending. Reason: %1$s. Payment will complete when capture.completed webhook is received.', 'pymntpl-paypal-woocommerce' ),
						isset( $capture->status_details ) ? $capture->getStatusDetails()->getReason() : 'N/A' ) );
			} else {
				$order->payment_complete( $result->get_capture_id() );
			}
		} else {
			$order->update_meta_data( Constants::AUTHORIZATION_ID, $result->get_authorization_id() );
			$order->set_status( apply_filters( 'wc_ppcp_authorized_order_status', $this->payment_method->get_option( 'authorize_status', 'on-hold' ), $order, $result->get_paypal_order(), $this ) );
		}
		$this->add_payment_complete_message( $order, $result );
		$this->save_order_meta_data( $order, $result->paypal_order );
		do_action( 'wc_ppcp_order_payment_complete', $order, $result, $this );
	}

	public function add_payment_complete_message( \WC_Order $order, PaymentResult $result ) {
		$order->add_order_note( sprintf( __( 'PayPal order %s created. %s', 'pymntpl-paypal-woocommerce' ),
			$result->paypal_order->id, $result->is_captured() ? sprintf( __( 'Capture ID: %s', 'pymntpl-paypal-woocommerce' ), $result->get_capture_id() ) : sprintf( __( 'Authorization ID: %s', 'pymntpl-paypal-woocommerce' ), $result->get_authorization_id() ) ) );
	}

	public function save_order_meta_data( \WC_Order $order, Order $paypal_order ) {
		$token = $this->get_payment_method_token_from_paypal_order( $paypal_order );
		$order->set_payment_method_title( $token->get_payment_method_title() );
		$order->update_meta_data( Constants::ORDER_ID, $paypal_order->id );
		$order->update_meta_data( Constants::PPCP_ENVIRONMENT, $this->client->getEnvironment() );
		$order->update_meta_data( Constants::PAYER_ID, $paypal_order->payer->payer_id );
		try {
			do_action( 'wc_ppcp_save_order_meta_data', $order, $paypal_order, $this->payment_method );
		} catch ( \Exception $e ) {
			$this->payment_method->logger->info( sprintf( 'Error saving order data. Error: %s', $e->getMessage() ) );
		}
		$order->save();
	}

	public function get_paypal_order_id_from_request() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		return isset( $_POST[ $this->payment_method->id . '_paypal_order_id' ] )
			? sanitize_text_field( wp_unslash( $_POST[ $this->payment_method->id . '_paypal_order_id' ] ) )
			: null;
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @return \PaymentPlugins\PayPalSDK\Order
	 */
	public function get_create_order_params( \WC_Order $order ) {
		$this->factories->initialize( $order );
		$paypal_order = $this->factories->order->from_order( $this->payment_method->get_option( 'intent' ) );
		OrderFilterUtil::filter_order( $paypal_order );
		/**
		 * @var PurchaseUnit $purchase_unit
		 */
		$purchase_unit = $paypal_order->getPurchaseUnits()->get( 0 );
		if ( ! $purchase_unit->getAmount()->amountEqualsBreakdown() ) {
			unset( $purchase_unit->getAmount()->breakdown );
			unset( $purchase_unit->items );
		}

		return apply_filters( 'wc_ppcp_create_order_params', $paypal_order, $order, $this );
	}

	protected function get_update_order_params( \WC_Order $order, Order $paypal_order ) {
		$this->factories->initialize( $order );
		$patches = [];
		/**
		 * @var PurchaseUnit $pu
		 */
		$pu = $this->factories->purchaseUnit->from_order();
		/**
		 * @var PurchaseUnit $purchase_unit
		 */
		foreach ( $paypal_order->purchase_units as $purchase_unit ) {
			if ( $purchase_unit->getReferenceId() ) {
				$pu->setReferenceId( $purchase_unit->getReferenceId() );
			} else {
				$pu->setReferenceId( 'default' );
			}
			$pu->patch();
			$pu->addPatchRequest( '', PatchRequest::REPLACE );

			$patches = array_merge( $patches, $pu->getPatchRequests() );
		}

		/**
		 * @param array                                           $patches
		 * @param \WC_Order                                       $order
		 * @param Order                                           $paypal_order
		 * @param \PaymentPlugins\WooCommerce\PPCP\PaymentHandler $this
		 */
		return apply_filters( 'wc_ppcp_get_update_order_params', $patches, $order, $paypal_order, $this );
	}

	public function get_payment_method() {
		return $this->payment_method;
	}

	/**
	 * @param \WC_Order $order
	 * @param           $amount
	 * @param string    $reason
	 */
	public function process_refund( \WC_Order $order, $amount, $reason = '' ) {
		$id = $order->get_transaction_id();
		if ( empty( $id ) ) {
			// transaction is empty so check if there is an authorization ID.
			$auth_id = $order->get_meta( Constants::AUTHORIZATION_ID );
			if ( ! $auth_id ) {
				throw new \Exception( __( 'To process a refund, there must be a transaction id associated with the order.',
					'pymntpl-paypal-woocommerce' ) );
			} else {
				throw new \Exception( __( 'This payment has a status of Authorize. Only captured payments can be refunded.',
					'pymntpl-paypal-woocommerce' ) );
			}
		}
		$refunds = $order->get_refunds();
		/**
		 * @var \WC_Order_Refund $a
		 * @var \WC_Order_Refund $b
		 */
		if ( \is_array( $refunds ) ) {
			usort( $refunds, function ( $a, $b ) {
				return $a->get_id() < $b->get_id() ? 1 : - 1;
			} );
		}

		return $this->client->orderMode( $order )->captures->refund( $id, $this->factories->refunds->from_refund( $refunds[0], $amount, $reason ) );
	}

	public function process_capture( \WC_Order $order, $amount = '' ) {
		$auth_id = $order->get_meta( Constants::AUTHORIZATION_ID );
		$result  = false;
		if ( $auth_id ) {
			$authorization = $this->client->orderMode( $order )->authorizations->retrieve( $auth_id );
			if ( ! is_wp_error( $authorization ) && ! $authorization->isCaptured() ) {
				OrderLock::set_order_lock( $order );
				$amount = $amount ? $amount : $order->get_total();
				$result = $this->client->orderMode( $order )->authorizations->capture( $auth_id, [
					'amount' => [
						'value'         => NumberUtil::round_incl_currency( $amount, $order->get_currency() ),
						'currency_code' => $order->get_currency()
					]
				] );
				if ( is_wp_error( $result ) ) {
					OrderLock::release_order_lock( $order );
					$order->add_order_note( sprintf( __( 'Error capturing payment. Reason: %s', 'pymntpl-paypal-woocommerce' ),
						$result->get_error_message() ) );
				} else {
					PayPalFee::add_fee_to_order( $order, $result->seller_receivable_breakdown );
					$order->add_order_note( sprintf( __( 'Payment captured in PayPal. Capture ID: %s Amount: %s', 'pymntpl-paypal-woocommerce' ),
						$result->id,
						wc_price( $amount, [ 'currency' => $order->get_currency() ] ) ) );
					$order->set_transaction_id( $result->id );
					if ( ! $this->is_processing( 'capture' ) ) {
						// set status to on hold so that when $order->payment_complete() is called, it
						// passes the $this->has_status() check.
						$order->set_status( 'on-hold' );
						$this->set_processing( 'capture' );
						$order->payment_complete();
					}
					$order->save();
				}
			}
		}

		return $result;
	}

	/**
	 * Void an authorized payment
	 *
	 * @param \WC_Order $order
	 * @param bool      $manual
	 */
	public function process_void( \WC_Order $order, $manual = false ) {
		try {
			$authorization_id = $order->get_meta( Constants::AUTHORIZATION_ID );
			if ( ! $authorization_id ) {
				if ( ! $manual ) {
					return;
				}
				throw new \Exception( __( 'A valid authorization ID is required to perform a void.', 'pymntpl-paypal-woocommerce' ) );
			}
			// fetch the authorization object and verify that it can be voided.
			$authorization = $this->client->orderMode( $order )->authorizations->retrieve( $authorization_id );
			if ( is_wp_error( $authorization ) ) {
				throw new \Exception( $authorization->get_error_message() );
			}
			if ( $authorization->isCreated() ) {
				$result = $this->client->orderMode( $order )->authorizations->void( $authorization_id );
				if ( is_wp_error( $result ) ) {
					throw new \Exception( $authorization->get_error_message() );
				}
				if ( ! $order->has_status( 'cancelled' ) ) {
					$this->set_processing( 'void' );
					$order->update_status( 'cancelled', sprintf( __( 'PayPal authorization cancelled. Authorization ID: %s', 'pymntpl-paypal-woocommerce' ), $authorization_id ) );
				}
			}
		} catch ( \Exception $e ) {
			$order->add_order_note( sprintf( __( 'Error processing void. Reason: %s' ), $e->getMessage() ) );

			return new \WP_Error( 'void-error', $e->getMessage() );
		}

		return true;
	}

	/**
	 * @param \WC_Order $order
	 *
	 * @since 1.0.22
	 * @return void
	 * @throws \Exception
	 */
	public function process_order_cancellation( \WC_Order $order ) {
		$txn_id      = $order->get_transaction_id();
		$refund_args = [
			'amount'         => NumberUtil::round_incl_currency( $order->get_total(), $order->get_currency() ),
			'order_id'       => $order->get_id(),
			'reason'         => __( 'Order cancellation', 'pymntpl-paypal-woocommerce' ),
			'refund_payment' => false
		];
		if ( ! $txn_id ) {
			$result = $this->process_void( $order );
			if ( ! is_wp_error( $result ) ) {
				wc_create_refund( $refund_args );
				$order->add_order_note( sprintf( __( 'PayPal authorization cancelled. Authorization ID: %s', 'pymntpl-paypal-woocommerce' ), $order->get_meta( Constants::AUTHORIZATION_ID ) ) );
			}
		} else {
			$refund_args['refund_payment'] = true;
			wc_create_refund( $refund_args );
		}
	}

	public function set_processing( $status ) {
		$this->current_status = $status;
	}

	public function remove_processing() {
		$this->current_status = null;
	}

	public function is_processing( $status ) {
		if ( \is_array( $status ) ) {
			return in_array( $this->current_status, $status );
		}

		return $this->current_status === $status;
	}

	protected function get_payment_method_token_from_paypal_order( Order $order ) {
		$token = $this->payment_method->get_payment_method_token_instance();
		$token->initialize_from_payer( $order->payer );

		return $token;
	}

	public function get_cache() {
		return $this->cache;
	}

	/**
	 * @param \WP_Error|Order $paypal_order
	 * @param \WC_Order       $order
	 *
	 * @return void
	 */
	private function validate_paypal_order( $paypal_order, $order ) {
		// Only validate orders with a CREATED status because that means they haven't been approved yet.
		// An order with an APPROVED status means the customer clicked complete payment in the PayPal popup
		if ( $paypal_order instanceof Order && $paypal_order->isCreated() ) {
			$this->factories->initialize( $order );
			$new_order           = $this->factories->order->from_order( $this->payment_method->get_option( 'intent' ) );
			$shipping_preference = $this->cache->get( Constants::SHIPPING_PREFERENCE );
			/**
			 * If the shipping preference is GET_FROM_FILE then we know the PayPal order was created using the PayPal buttons.
			 * But if the PayPal order created from the WC_Order has shipping preference SET_PROVIDED_ADDRESS, then a new order
			 * should be created. This ensures the shipping address can't be edited on the PayPal redirect based payment page.
			 */
			if ( $shipping_preference === OrderApplicationContext::GET_FROM_FILE ) {
				if ( $new_order->getApplicationContext()->getShippingPreference() === OrderApplicationContext::SET_PROVIDED_ADDRESS ) {
					$this->cache->delete( Constants::PAYPAL_ORDER_ID );
					$this->cache->delete( Constants::SHIPPING_PREFERENCE );
					throw new RetryException( 'Create new order' );
				}
			}
		}
	}

}