<?php
/**
 * @var WC_Payment_Gateway_Stripe[] $payment_methods
 * @var array                       $available_gateways
 * @version 1.0.12
 *
 */

?>
<div class="wc-ppcp-express-checkout" <?php if ( count( $available_gateways ) == 0 ){ ?>style="display: none"<?php } ?>>
    <fieldset>
        <legend class="express-title"><?php esc_html_e( 'Express Checkout', 'pymntpl-paypal-woocommerce' ) ?></legend>
        <div class="wc_ppcp_express_checkout_gateways">
			<?php foreach ( $payment_methods as $gateway ): ?>
                <div class="wc-ppcp-express-checkout-gateway express_payment_method_<?php echo esc_attr( $gateway->id ) ?>">
					<?php $gateway->express_checkout_fields() ?>
                </div>
			<?php endforeach; ?>
        </div>
    </fieldset>
    <span class="express-divider"><?php esc_html_e( 'OR', 'pymntpl-paypal-woocommerce' ) ?></span>
</div>