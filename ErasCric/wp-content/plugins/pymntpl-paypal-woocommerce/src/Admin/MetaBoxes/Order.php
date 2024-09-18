<?php


namespace PaymentPlugins\WooCommerce\PPCP\Admin\MetaBoxes;


use PaymentPlugins\PayPalSDK\V1\Tracker;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;
use PaymentPlugins\WooCommerce\PPCP\Utilities\PayPalFee;
use PaymentPlugins\WooCommerce\PPCP\Utilities\ShippingUtil;

class Order {

	private $assets_api;

	public function __construct( AssetsApi $assets_api ) {
		$this->assets_api = $assets_api;
		$this->initialize();
	}

	private function initialize() {
		add_action( 'woocommerce_order_item_add_action_buttons', [ $this, 'add_action_buttons' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ] );
		add_action( 'woocommerce_admin_order_totals_after_total', [ $this, 'fee_details' ] );
	}

	public function register_scripts() {
		$this->assets_api->register_script( 'wc-ppcp-admin-commons', 'build/js/admin-commons.js' );
	}

	public function add_action_buttons( \WC_Order $order ) {
		$payment_methods = WC()->payment_gateways()->payment_gateways();
		if ( $order->get_type() === 'shop_order' && ! $order->has_status( [ 'pending', 'cancelled', 'draft', 'failed' ] )
		     && isset( $payment_methods[ $order->get_payment_method() ] )
		) {
			$payment_method = $payment_methods[ $order->get_payment_method() ];
			$transaction_id = $order->get_transaction_id();
			if ( $payment_method instanceof AbstractGateway ) {
				$this->assets_api->enqueue_script( 'wc-ppcp-order-metabox', 'build/js/admin-order-metabox.js', [
					'wc-ppcp-admin-commons',
					'wc-backbone-modal'
				] );
				$this->assets_api->enqueue_style( 'wc-ppcp-admin', 'build/css/admin.css' );
				?>
                <button class="wc-ppcp-order-actions button button-secondary"
                        data-order="<?php echo esc_attr( $order->get_id() ) ?>"><?php esc_html_e( 'PayPal Actions', 'pymntpl-paypal-woocommerce' ) ?></button>
                <script type="text/template" id="tmpl-wc-ppcp-order-actions">
                    <div class="wc-backbone-modal wc-ppcp-paypal-actions-modal">
                        <div class="wc-backbone-modal-content">
                            <section class="wc-backbone-modal-main" role="main">
                                <header class="wc-backbone-modal-header">
                                    <h1><?php esc_html_e( 'PayPal Order', 'pymntpl-paypal-woocommerce' ) ?>&nbsp;#{{ data.order.id }}</h1>
                                    <button
                                            class="modal-close modal-close-link dashicons dashicons-no-alt">
                                        <span class="screen-reader-text">Close modal panel</span>
                                    </button>
                                </header>
                                <article class="wc-ppcp-actions__article">
                                    <nav class="wc-ppcp-actions-nav">
                                        <div data-section="transaction" class="wc-ppcp-nav-item selected">
                                            <a><?php esc_html_e( 'Transaction Actions', 'pymntpl-paypal-woocommerce' ) ?></a>
                                        </div>
                                        <#if(data.has_shipping){#>
                                        <div data-section="shipping" class="wc-ppcp-nav-item">
                                            <a><?php esc_html_e( 'Shipping Actions', 'pymntpl-paypal-woocommerce' ) ?></a>
                                        </div>
                                        <#}#>
                                    </nav>
                                    <div class="ppcp-order-actions-notices"></div>
                                    <div class="wc-ppcp-actions-container">
                                        <div data-section="transaction" class="wc-ppcp-actions__actions">
                                            <#if(data.can_capture){#>
                                            <div class="wc-ppcp-action-row">
                                                <div class="wc-ppcp-action-item">
                                                    <label><?php esc_html_e( 'Capture Amount', 'pymntpl-paypal-woocommerce' ) ?></label>
                                                    <input type="text" name="ppcp_capture_amount" id="ppcp_capture_amount" value="{{data.authorization.amount.value}}">
                                                </div>
                                            </div>
                                            <#}else{#>
											<?php esc_html_e( 'There are no transaction actions available.', 'pymntpl-paypal-woocommerce' ) ?>
                                            <#}#>
                                        </div>
                                        <#if(data.has_shipping){#>
                                        <div data-section="shipping" class="wc-ppcp-actions__actions shipping-section" style="display: none">
                                            <#if(data.can_capture){#>
                                            <div>
                                                <p><?php esc_html_e( 'Tracking cannot be added until the payment is captured.', 'pymntpl-paypal-woocommerce' ) ?></p>
                                            </div>
                                            <#}else{#>
                                            <p><?php esc_html_e( 'In this section, you can add tracking info to the PayPal transaction.', 'pymntpl-paypal-woocommerce' ) ?></p>
                                            <div class="wc-ppcp-action-row">
                                                <div class="wc-ppcp-action-item">
                                                    <label><?php esc_html_e( 'Tracking #', 'pymntpl-paypal-woocommerce' ) ?></label>
                                                    <input id="ppcp_tracking" type="text" value="{{data.tracker.tracking_number}}"/>
                                                </div>
                                                <!--<div class="wc-ppcp-action-item">
                                                    <label><?php /*esc_html_e( 'Tracking Type', 'pymntpl-paypal-woocommerce' ) */ ?></label>
                                                    <select id="ppcp_tracking_type" class="wc-enhanced-select">
														<?php /*foreach ( ShippingUtil::get_tracking_types() as $key => $value ): */ ?>
                                                            <option value="<?php /*echo $key */ ?>" <#if(data.tracker.tracking_number_type === "<?php /*echo $key */ ?>"){#>selected<#}#>><?php /*echo $value */ ?></option>
														<?php /*endforeach; */ ?>
                                                    </select>
                                                </div>-->
                                                <div class="wc-ppcp-action-item">
                                                    <label><?php esc_html_e( 'Shipping Status', 'pymntpl-paypal-woocommerce' ) ?></label>
                                                    <select id="ppcp_shipping_status" class="wc-enhanced-select" style="width: 100%">
														<?php foreach ( ShippingUtil::get_shipping_statuses() as $key => $status ): ?>
                                                            <option value="<?php echo esc_attr( $key ) ?>" <#if(data.tracker.status === "<?php echo esc_attr( $key ) ?>" ){#>selected<#}#>><?php echo esc_html( $status ) ?></option>
														<?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="wc-ppcp-action-item shipping-carrier">
                                                    <label><?php esc_html_e( 'Carrier', 'pymntpl-paypal-woocommerce' ) ?></label>
                                                    <select id="ppcp_carrier" class="wc-enhanced-select" style="width: 100%">
														<?php foreach ( ShippingUtil::get_carriers() as $key => $value ): ?>
                                                            <option value="<?php echo esc_attr( $key ) ?>" <#if(data.tracker.carrier === "<?php echo esc_attr( $key ) ?>" ){#>selected<#}#>><?php echo esc_html( $value ) ?></option>
														<?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="wc-ppcp-action-item carrier-other" data-show-if="<?php echo esc_html( Tracker::OTHER ) ?>"
                                                <#if(data.tracker.carrier !== "<?php echo esc_html( Tracker::OTHER ) ?>"){#>style="display: none"<#}#>>
                                                <label><?php esc_html_e( 'Carrier Name', 'pymntpl-paypal-woocommerce' ) ?></label>
                                                <input id="ppcp_carrier_other" type="text" value="{{data.tracker.carrier_name_other}}"/>
                                            </div>
                                            <div class="wc-ppcp-action-item">
                                                <label><?php esc_html_e( 'Notify Buyer', 'pymntpl-paypal-woocommerce' ) ?></label>
                                                <input id="ppcp_notify_buyer" type="checkbox"/>
                                            </div>
                                        </div>
                                        <#}#>
                                    </div>
                                    <#}#>
                        </div>
                        </article>
                        <footer>
                            <div class="inner">
                                <div data-section="transaction">
                                    <#if(data.can_capture){#>
                                    <button class="button button-secondary ppcp-capture" data-processing-text="<?php echo esc_attr_e( 'Processing...', 'pymntpl-paypal-woocommerce' ) ?>"><?php esc_html_e( 'Capture', 'pymntpl-paypal-woocommerce' ); ?></button>
                                    <button class="button button-secondary ppcp-void" data-processing-text="<?php echo esc_attr_e( 'Processing...', 'pymntpl-paypal-woocommerce' ) ?>"><?php esc_html_e( 'Void', 'pymntpl-paypal-woocommerce' ); ?></button>
                                    <#}#>
                                </div>
                                <div data-section="shipping" style="display: none">
                                    <button class="button button-secondary ppcp-shipping-submit" data-processing-text="<?php echo esc_attr_e( 'Processing...', 'pymntpl-paypal-woocommerce' ) ?>">
										<?php esc_html_e( 'Submit', 'pymntpl-paypal-woocommerce' ) ?>
                                    </button>
                                </div>
                            </div>
                        </footer>
                        </section>
                    </div>
                    </div>
                    <div class="wc-backbone-modal-backdrop modal-close"></div>
                </script>
				<?php
			}
		}
	}

	public function fee_details( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( Main::container()->get( PaymentGateways::class )->has_gateway( $order->get_payment_method() ) ) {
			$fee = PayPalFee::display_fee( $order );
			$net = PayPalFee::display_net( $order );
			if ( $fee && $net ) {
				?>
                <tr class="wc-ppcp-fee-row">
                    <td class="label wc-ppcp-fee"><?php esc_html_e( 'PayPal Fee', 'pymntpl-paypal-woocommerce' ) ?>:</td>
                    <td width="1%"></td>
                    <td><?php echo $fee //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></td>
                </tr>
                <tr class="wc-ppcp-net-row">
                    <td class="label wc-ppcp-net"><?php esc_html_e( 'Net payout', 'pymntpl-paypal-woocommerce' ) ?></td>
                    <td width="1%"></td>
                    <td class="total"><?php echo $net //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></td>
                </tr>
				<?php
			}
		}
	}

}