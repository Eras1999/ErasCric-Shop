<?php

/**
 * @var \PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi $assets
 */
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$page    = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
$section = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : '';
// phpcs:enable WordPress.Security.NonceVerification.Recommended
?>
<div class="navigation">
    <div class="wc-ppcp-main__logo">
        <img src="<?php echo esc_url( $assets->assets_url( 'assets/img/paymentplugins.svg' ) ) ?>"/>
    </div>
    <div class="navigation-tabs">
        <a class="<?php if ( $page === 'wc-ppcp-main' && ! $section ) { ?>active<?php } ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-ppcp-main' ) ) ?>">
			<?php esc_html_e( 'Main Page', 'pymntpl-paypal-woocommerce' ) ?>
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ppcp_api' ) ) ?>">
			<?php esc_html_e( 'Settings', 'pymntpl-paypal-woocommerce' ) ?>
        </a>
        <a target="_blank" href="https://docs.paymentplugins.com/wc-paypal/config">
			<?php esc_html_e( 'Documentation', 'pymntpl-paypal-woocommerce' ) ?>
        </a>
        <a class="<?php if ( $section === 'support' ) { ?>active<?php } ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wc-ppcp-main&section=support' ) ) ?>">
			<?php esc_html_e( 'Support', 'pymntpl-paypal-woocommerce' ) ?>
        </a>
    </div>
    <div class="paypal-logo">
        <a target="_blank" href="https://paypal.com">
            <img src="<?php echo esc_url( $assets->assets_url( 'assets/img/paypal_logo.svg' ) ) ?>"/>
        </a>
    </div>
</div>