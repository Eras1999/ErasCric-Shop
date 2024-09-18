<?php

namespace PaymentPlugins\WooCommerce\PPCP;

class PluginValidation {

	public static function is_valid( $callback ) {
		try {
			self::assert_php_version();

			$callback();
		} catch ( \Exception $e ) {
			self::add_admin_notice( $e->getMessage() );
		}
	}

	private static function assert_php_version() {
		if ( version_compare( phpversion(), '7.1', '<' ) ) {
			throw new \Exception( sprintf( __( 'Payment Plugins for PayPal WooCommerce requires PHP 7.1 or greater.', 'pymntpl-paypal-woocommerce' ) ) );
		}
	}

	private static function add_admin_notice( $msg ) {
		add_action( 'admin_notices', function () use ( $msg ) {
			?>
            <div class="notice notice-error woocommerce-message">
                <h4>
					<?php echo esc_html( $msg ) ?>
                </h4>
            </div>
			<?php
		} );
	}

}