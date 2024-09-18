<?php


namespace PaymentPlugins\PayPalSDK\Client;


interface ClientInterface {

	const PRODUCTION = 'production';

	const SANDBOX = 'sandbox';

	public function request( $method, $uri, $options );

}