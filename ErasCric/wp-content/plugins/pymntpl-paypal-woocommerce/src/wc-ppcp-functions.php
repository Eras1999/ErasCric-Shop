<?php

/**
 * @since 1.0.40
 * @return mixed|\PaymentPlugins\WooCommerce\PPCP\Container\Container
 */
function wc_ppcp_get_container() {
	return \PaymentPlugins\WooCommerce\PPCP\Main::container();
}