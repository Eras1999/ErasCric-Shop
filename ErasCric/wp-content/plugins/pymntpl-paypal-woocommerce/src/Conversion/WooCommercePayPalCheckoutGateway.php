<?php

namespace PaymentPlugins\WooCommerce\PPCP\Conversion;

/**
 * https://wordpress.org/plugins/woocommerce-gateway-paypal-express-checkout/
 */
class WooCommercePayPalCheckoutGateway extends GeneralPayPalPlugin {

	public $id = 'ppec_paypal';

	protected $payment_token_id = '_ppec_billing_agreement_id';

}