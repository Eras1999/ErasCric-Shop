<?php

defined( 'ABSPATH' ) || exit();

if ( \function_exists( 'WC' ) ) {
	$container = \PaymentPlugins\WooCommerce\PPCP\Main::container();
	$settings  = $container->get( \PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings::class );
	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Logger $logger
	 */
	$logger = $container->get( \PaymentPlugins\WooCommerce\PPCP\Logger::class );
	/**
	 * @var \PaymentPlugins\PayPalSDK\PayPalClient $client
	 */
	$client       = $container->get( \PaymentPlugins\PayPalSDK\PayPalClient::class );
	$environments = [ 'production', 'sandbox' ];
	foreach ( $environments as $environment ) {
		$webhook_id = $settings->get_webhook_id( $environment );
		if ( $webhook_id ) {
			// update the events that the webhook is subscribed to
			$webhook = $client->environment( $environment )->webhooks->retrieve( $webhook_id );
			if ( ! is_wp_error( $webhook ) ) {
				// de-duplicate event names just in case
				$keys = [];
				foreach ( $webhook->getEventTypes()->toArray() as $event ) {
					$keys[] = $event['name'];
				}
				$keys[]   = 'PAYMENT.CAPTURE.DENIED';
				$keys     = array_values( array_unique( $keys ) );
				$events   = array_map( function ( $name ) {
					return [ 'name' => $name ];
				}, $keys );
				$patches  = [
					[
						'op'    => \PaymentPlugins\PayPalSDK\PatchRequest::REPLACE,
						'path'  => '/event_types',
						'value' => $events
					]
				];
				$response = $client->environment( $environment )->webhooks->update( $webhook_id, $patches );
				if ( is_wp_error( $response ) ) {
					$logger->error( sprintf( 'Error updating webhook. ID: %1$s. Reason: %2$s', $webhook_id, $response->get_error_message() ) );
				} else {
					$logger->info( sprintf( 'Webhook %1$s updated.', $webhook_id ) );
				}
			} else {
				$logger->error( sprintf( 'Error retrieving webhook %s.', $webhook_id ) );
			}
		}
	}
}