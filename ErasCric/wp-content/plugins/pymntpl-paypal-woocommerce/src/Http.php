<?php

namespace PaymentPlugins\WooCommerce\PPCP;

use PaymentPlugins\PayPalSDK\Client\BaseHttpClient;

class Http {

	private $http;

	public function __construct() {
		$this->http = new \WP_Http();
	}

	public function request( $method, $url, $options ) {
		$args = [
			'method'     => $method,
			'headers'    => $options['headers'],
			'timeout'    => BaseHttpClient::DEFAULT_TIMEOUT,
			'user-agent' => ''
		];
		if ( $method === 'GET' && isset( $options['query'] ) ) {
			$url = add_query_arg( $options['query'], $url );
		}
		if ( isset( $options['json'] ) ) {
			$args['body'] = \json_encode( $options['json'] );
		} elseif ( isset( $options['form_params'] ) ) {
			$args['body'] = \http_build_query( $options['form_params'], '', '&' );
		}

		return $this->http->request( $url, $args );
	}

}