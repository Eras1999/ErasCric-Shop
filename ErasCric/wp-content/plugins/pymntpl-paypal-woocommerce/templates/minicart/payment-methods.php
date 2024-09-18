<?php

/**
 * @var \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway[] $payment_methods
 */
foreach ( $payment_methods as $payment_method ):?>
    <a id="wc-ppcp-minicart-<?php echo esc_attr( $payment_method->id ) ?>" class="wc-ppcp-minicart-<?php echo esc_attr( $payment_method->id ) ?>"></a>
<?php endforeach; ?>

