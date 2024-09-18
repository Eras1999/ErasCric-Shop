<?php
/**
 *
 */

?>
<script type="text/template" id="tmpl-wc-ppcp-activation">
    <div class="wc-backbone-modal wc-ppcp-modal">
        <div class="wc-backbone-modal-content">
            <section class="wc-backbone-modal-main" role="main">
                <header class="wc-backbone-modal-header">
                    <!--<h1><?php /*esc_html_e( 'Quick Start:', 'pymntpl-paypal-woocommerce' ); */ ?></h1>-->
                    <div class="wc-ppcp-modal-logo">
                        <img src="<?php echo esc_url( $assets->assets_url( 'assets/img/paypal_logo.svg' ) ) ?>"/>
                        <span><?php esc_html_e( 'By Payment Plugins', 'pymntpl-paypal-woocommerce' ); ?></span>
                    </div>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text">Close modal panel</span>
                    </button>
                </header>
                <article>
                    <div class="wc-ppcp-modal-content">
                        <p>
							<?php
							esc_html_e( 'Thank you for installing PayPal WooCommerce! Here are a few helpful tips that will ensure you can start accepting payments quickly and easily.', 'pymntpl-paypal-woocommerce' );
							?>
                        </p>
                        <ul class="wc-modal-steps">
                            <li>
                                <label><?php esc_html_e( 'Connecting The Plugin', 'pymntpl-paypal-woocommerce' ); ?></label>
                                <div>
									<?php
									printf( esc_html__( 'In order to start processing payments in production or sandbox, you must connect the plugin. You can either
                                    connect by manually entering your client ID and secret, or using our automated connect integration. Click %1$shere%2$s to
                                    navigate to the API Settings page where you can connect.', 'pymntpl-paypal-woocommerce' ), '<a target="_blank" href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ppcp_api' ) ) . '">', '</a>' );
									?>
                                </div>
                            </li>
                            <li>
                                <label><?php esc_html_e( 'Enable Reference Transactions', 'pymntpl-paypal-woocommerce' ) ?></label>
                                <div>
									<?php
									printf( esc_html__( 'Reference transactions are used to process things like subscriptions and pre-orders. If you don\'t currently have
                                    reference transactions enabled on your production account, you will need to submit a request to PayPal. Reference transactions are enabled by default on sandbox accounts. %1$sReference transaction request instructions.%2$s' ),
										'<a target="_blank" href="https://docs.paymentplugins.com/wc-paypal/config/#/reference_transactions">', '</a>' );
									?>
                                </div>
                            </li>
                            <li>
                                <label><?php esc_html_e( 'Enable Customer Phone Number', 'pymntpl-paypal-woocommerce' ); ?></label>
                                <div>
									<?php esc_html_e( 'If you require a billing phone number during checkout, we recommend you enable the contact phone number option in your PayPal account. When enabled, PayPal will
                                    provide the customer\'s phone number, which the plugin will use to populate the checkout page. It will improve conversion rates since the customer won\'t have to enter their phone number.', 'pymntp-paypal-woocommerce' ); ?>
									<?php printf( esc_html__( '%1$sEnable phone number instructions%2$s', '' ), '<a target="_blank" href="https://docs.paymentplugins.com/wc-paypal/config/#/billing_phone">', '</a>' ) ?>
                                </div>
                            </li>
                            <li>
                                <label><?php esc_html_e( 'Enable Billing Address', 'pymntpl-paypal-woocommerce' ); ?></label>
                                <div>
									<?php esc_html_e( 'If you sell digital goods that don\'t require shipping, we recommend you enable the billing address option. When enabled on your account, the customer\'s billing address will be provided by PayPal. This
			                        allows the plugin to auto-populate your checkout page billing address fields.', 'pymntp-paypal-woocommerce' ); ?>
									<?php printf( esc_html__( '%1$sEnable billing address instructions%2$s', '' ), '<a target="_blank" href="https://docs.paymentplugins.com/wc-paypal/config/#/billing_address_request">', '</a>' ) ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                </article>
            </section>
        </div>
    </div>
    <div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
