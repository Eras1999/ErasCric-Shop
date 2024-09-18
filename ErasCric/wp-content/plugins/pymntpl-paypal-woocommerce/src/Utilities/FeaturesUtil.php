<?php

namespace PaymentPlugins\WooCommerce\PPCP\Utilities;

class FeaturesUtil {

	public static function is_custom_order_tables_enabled() {
		return class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' )
		       && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
	}

}