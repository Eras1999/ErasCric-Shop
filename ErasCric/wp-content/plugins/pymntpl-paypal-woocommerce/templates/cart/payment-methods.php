<?php
/**
 * @var \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway[] $payment_methods
 * @var bool                                                                 $below_add_to_cart
 */

?>
<div class="wc-ppcp-cart-payments__container <?php echo $below_add_to_cart ? 'below' : 'above' ?>">
    <ul class="wc-ppcp-cart-payment__methods">
		<?php if ( $below_add_to_cart ): ?>
            <li class="wc-ppcp-cart-payment-method or">
                <p class="wc-ppcp-cart-or">
                    &mdash;&nbsp;<?php esc_html_e( 'or', 'pymntpl-paypal-woocommerce' ) ?>&nbsp;&mdash;
                </p>
            </li>
		<?php endif;
		foreach ( $payment_methods as $payment_method ):?>
            <li class="wc-ppcp-cart-payment-method payment_method_<?php echo esc_attr( $payment_method->id ) ?>">
				<?php $payment_method->cart_fields(); ?>
            </li>
		<?php endforeach; ?>
		<?php if ( ! $below_add_to_cart ): ?>
            <li class="wc-ppcp-cart-payment-method or">
                <p class="wc-ppcp-cart-or">
                    &mdash;&nbsp;<?php esc_html_e( 'or', 'pymntpl-paypal-woocommerce' ) ?>&nbsp;&mdash;
                </p>
            </li>
		<?php endif; ?>
    </ul>
</div>
