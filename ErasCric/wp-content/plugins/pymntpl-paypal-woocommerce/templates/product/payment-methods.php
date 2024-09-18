<?php
/**
 * @var \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway[] $payment_methods
 * @var bool $below_add_to_cart
 */
?>
<div class="wc-ppcp-product-payments__container <?php echo esc_html( $position ) ?>">
    <ul class="wc-ppcp-product-payment__methods">
		<?php foreach ( $payment_methods as $payment_method ): ?>
            <li class="wc-ppcp-product-payment-method payment_method_<?php echo esc_attr( $payment_method->id ) ?>">
				<?php $payment_method->product_fields(); ?>
            </li>
		<?php endforeach; ?>
    </ul>
</div>
