<?php

namespace PaymentPlugins\PPCP\MondialRelay;

use PaymentPlugins\PayPalSDK\OrderApplicationContext;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;

class FrontendScripts {

	public function __construct() {
		add_filter( 'wc_ppcp_script_dependencies', [ $this, 'add_script_dependencies' ], 10, 3 );
	}

	/**
	 * @param array                                             $handles
	 * @param \PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi $assets
	 * @param ContextHandler                                    $context
	 *
	 * @return void
	 */
	public function add_script_dependencies( $handles, $assets, $context ) {
		if ( $context->is_checkout() && \in_array( 'wc-ppcp-checkout-gateway', $handles ) ) {
			// add Mondial script
			$assets->register_script( 'wc-ppcp-mondial-relay', 'packages/mondial-relay/build/mondial-relay.js' );
			$handles[] = 'wc-ppcp-mondial-relay';
		}

		return $handles;
	}

}