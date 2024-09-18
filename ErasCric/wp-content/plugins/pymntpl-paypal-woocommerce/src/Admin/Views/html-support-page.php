<?php
/**
 * @var \PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi $assets
 */

?>
<div class="wc-ppcp-support__page">
    <div class="wc-ppcp-main__container">
		<?php include __DIR__ . '/html-main-navigation.php' ?>
        <div class="wc-ppcp-main__header">
            <div class="description">
                <h1><?php esc_html_e( 'We\'re here to help you.', 'pymntpl-paypal-woocommerce' ) ?></h1>
                <p>
					<?php esc_html_e( 'Have a question? Need some help? Please submit a ticket and one of our support specialists will get back to you.', 'pymntpl-paypal-woocommerce' ) ?>
                    <br/>
					<?php esc_html_e( 'Note: we commit to a 3 day turnaround with free support.', 'pymntpl-paypal-woocommerce' ) ?>
                </p>
            </div>
            <!--<div class="wc-ppcp-welcome-header__design"></div>-->
        </div>
        <div class="wc-ppcp-support__content">
            <div class="wc-ppcp-main__row justify-content-start">
                <div class="wc-ppcp-main__card">
                    <div class="wc-ppcp-main-card__content support">
                        <div class="icon-container support">
                            <!--<span class="dashicons dashicons-admin-users"></span>-->
                            <img class="icon" src="<?php echo esc_url( $assets->assets_url( 'assets/img/support.svg' ) ) ?>"/>
                        </div>
                        <div class="card-header">
                            <p>
								<?php esc_html_e( 'While we commit to a 3 day turnaround, most tickets receive a response within 24 hrs.', 'pymntpl-paypal-woocommerce' ) ?>
                            </p>
                            <p>
								<?php esc_html_e( 'Click the Create Ticket button and enter all the required information.', 'pymntpl-paypal-woocommerce' ) ?>
                                <br/><br/>
                            </p>
                            <button id="paypalSupportButton" class="wc-ppcp-card-button"><?php esc_html_e( 'Create Ticket', 'pymntpl-paypal-woocommerce' ) ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>