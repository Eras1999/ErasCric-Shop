<?php

/**
 * @var \PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi $assets
 */
$user = wp_get_current_user();
//$signed_up = get_option( 'wc_ppcp_admin_signup', false );
?>
<div class="wc-ppcp-main__page">
    <div class="wc-ppcp-main__container">
		<?php include __DIR__ . '/html-main-navigation.php' ?>
        <div class="wc-ppcp-welcome__content">
            <div class="wc-ppcp-main__row cards-container">
                <div class="wc-ppcp-main__card">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ppcp_api' ) ) ?>">
                        <div class="wc-ppcp-main-card__content">
                            <h3><?php esc_html_e( 'Settings', 'pymntpl-paypal-woocommerce' ) ?></h3>
                            <div class="icon-container">
                                <!--<span class="dashicons dashicons-admin-generic"></span>-->
                                <img class="icon" src="<?php echo esc_url( $assets->assets_url( 'assets/img/settings.svg' ) ) ?>"/>
                            </div>
                            <div class="card-header">
                                <p><?php esc_html_e( 'Connect your PayPal account, enable payment methods, and customize the plugin settings to fit your business needs.', 'pymntpl-paypal-woocommerce' ) ?></p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="wc-ppcp-main__card">
                    <a target="_blank" href="https://docs.paymentplugins.com/wc-paypal/config">
                        <div class="wc-ppcp-main-card__content">
                            <h3><?php esc_html_e( 'Documentation', 'pymntpl-paypal-woocommerce' ) ?></h3>
                            <div class="icon-container documentation">
                                <!--<span class="dashicons dashicons-admin-users"></span>-->
                                <img class="icon" src="<?php echo esc_url( $assets->assets_url( 'assets/img/documentation.svg' ) ) ?>"/>
                            </div>
                            <div class="card-header">
                                <p>
									<?php esc_html_e( 'Want in depth documentation?', 'pymntpl-paypal-woocommerce' ) ?>
                                    <br/>
									<?php esc_html_e( 'Our config guide and API docs are a great place to start.', 'pymntpl-paypal-woocommerce' ) ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="wc-ppcp-main__card">
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=wc-ppcp-main&section=support' ) ) ?>">
                        <div class="wc-ppcp-main-card__content">
                            <h3><?php esc_html_e( 'Support', 'pymntpl-paypal-woocommerce' ) ?></h3>
                            <div class="icon-container support">
                                <!--<span class="dashicons dashicons-admin-users"></span>-->
                                <img class="icon" src="<?php echo esc_url( $assets->assets_url( 'assets/img/support.svg' ) ) ?>"/>
                            </div>
                            <div class="card-header">
                                <p><?php esc_html_e( 'Have a question?', 'pymntpl-paypal-woocommerce' ) ?>
                                    <br/>
									<?php esc_html_e( 'Our support team is ready to assist you.', 'pymntpl-paypal-woocommerce' ) ?>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
				<?php foreach ( $plugins as $key => $plugin ): ?>
					<?php if ( $plugin->authorized && ! $plugin->active ): ?>
                        <div class="wc-ppcp-main__card <?php echo esc_attr( $key ) ?> plugin-card-<?php echo esc_attr( $plugin->slug ) ?>">
                            <div class="wc-ppcp-main-card__content">
                                <h3><?php echo esc_html( $plugin->title ) ?></h3>
                                <div class="icon-container <?php echo esc_attr( $key ) ?>-plugin-icon">
                                    <img class="icon" src="<?php echo esc_url( $plugin->urls->icon ) ?>"/>
                                </div>
                                <div class="card-header">
                                    <p>
										<?php echo esc_html( $plugin->tagline ) ?>
                                    </p>
                                    <form id="plugin-filter">
                                        <a class="<?php echo esc_attr( $plugin->installed ) ? 'activate-now' : 'install-now' ?> button"
                                           href="<?php echo $plugin->installed ? esc_url( $plugin->urls->activate ) : esc_url( $plugin->urls->install ) ?>" data-slug="<?php echo esc_attr( $plugin->slug ) ?>"><?php $plugin->installed ? esc_html_e( 'Activate',
												'pymntpl-paypal-woocommerce' )
												: esc_html_e( 'Install Now', 'pymntpl-paypal-woocommerce' ) ?></a>
                                    </form>
                                </div>
                            </div>
                        </div>
					<?php endif; ?>
				<?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
