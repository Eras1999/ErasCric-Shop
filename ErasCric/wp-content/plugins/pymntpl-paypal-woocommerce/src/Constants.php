<?php


namespace PaymentPlugins\WooCommerce\PPCP;


class Constants {

	const PARTNER_ID = 'PaymentPlugins_PCP';

	const AUTHORIZATION_ID = '_ppcp_authorization_id';

	const ORDER_ID = '_ppcp_paypal_order_id';

	const PPCP_ENVIRONMENT = '_ppcp_environment';

	const ENVIRONMENT = 'environment';

	const BILLING_AGREEMENT_ID = '_billing_agreement_id';

	const CHECKOUT = 'checkout';

	const VAULT = 'vault';

	const PAYER_ID = '_paypal_payer_id';

	const PAYPAL_FEE = '_paypal_fee';

	const PAYPAL_NET = '_paypal_net';

	const PAYPAL_REFUND = '_paypal_refund';

	const ADDRESS_MAPPINGS = [
		'address_1' => 'address_line_1',
		'address_2' => 'address_line_2',
		'state'     => 'admin_area_1',
		'city'      => 'admin_area_2',
		'postcode'  => 'postal_code',
		'country'   => 'country_code'
	];

	const VERSION_KEY = 'wc_ppcp_version';

	const INSTALL_PROCESS = 'wc_ppcp_install_process';

	const SHIPPING_OPTION_REGEX = '/^([\w]+)\:(.+)$/';

	const PPCP_CACHE_KEY = 'wc_ppcp_cache';

	const PPCP_ORDER_SESSION_KEY = 'wc_ppcp_order';

	const BILLING_ADDRESS_PROVIDED = 'billing_address_provided';

	const CAN_UPDATE_ORDER_DATA = 'can_update_order_data';

	const DISPUTE_STATUS = '_ppcp_dispute_status';

	const PAYPAL_ORDER_ID = '_ppcp_order_id';

	const PAYPAL_TRACKING_NUMBER = '_ppcp_tracking_number';

	const PRODUCTION = 'production';

	const SANDBOX = 'sandbox';

	const SHIPPING_ADDRESS_DISABLED = 'shipping_address_disabled';

	const SHIPPING_PREFERENCE = 'shipping_preference';

	/**
	 * @since 1.0.46
	 */
	const CAPTURE_STATUS = '_paypal_capture_status';

}