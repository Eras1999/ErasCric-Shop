<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin;


use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\APISettings;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

class AdminWebhookCreate extends \PaymentPlugins\WooCommerce\PPCP\Rest\Routes\Admin\AbstractRoute {

	private $client;

	private $api_settings;

	public function __construct( WPPayPalClient $client, APISettings $api_settings ) {
		$this->client       = $client;
		$this->api_settings = $api_settings;
	}

	public function get_path() {
		return 'webhook';
	}

	public function get_routes() {
		return [
			[
				'methods'              => \WP_REST_Server::EDITABLE,
				'callback'             => [ $this, 'handle_request' ],
				'permissions_callback' => [ $this, 'get_admin_permission_check' ],
				'args'                 => [
					'environment' => [
						'required' => true
					]
				]
			]
		];
	}

	public function handle_put_request( \WP_REST_Request $request ) {
		$environment = $request['environment'];
		$url         = \rest_url( 'wc-ppcp/v1/webhook/' . $environment );
		$parsed_url  = \wp_parse_url( $url );
		if ( preg_match( '/^https?:\/\/localhost/', $url ) ) {
			throw new \Exception( __( 'Webhooks cannot be configured on localhost.', 'pymntpl-paypal-woocommerce' ), 422 );
		}
		if ( $parsed_url['scheme'] === 'http' ) {
			throw new \Exception( __( 'PayPal requires https when creating a webhook.', 'pymntpl-paypal-woocommerce' ), 422 );
		}

		if ( ! $this->api_settings->is_connected( $environment ) ) {
			throw new \Exception( sprintf( __( 'You must connect %s before your webhook can be created.', 'pymntpl-paypal-woocommerce' ),
				$environment === 'sandbox' ? __( 'sandbox', 'pymntpl-paypal-woocommerce' ) : __( 'production', 'pymntpl-paypal-woocommerce' ) ), 422 );
		}
		if ( ( $id = $this->api_settings->get_webhook_id( $environment ) ) ) {
			// delete the existing webhook first
			$this->client->webhooks->delete( $id );
		}
		$webhook = $this->client->webhooks->create( apply_filters( 'wc_ppcp_create_webhook_params', [
			'url'         => $url,
			'event_types' => [
				[ 'name' => 'PAYMENT.CAPTURE.COMPLETED' ],
				[ 'name' => 'PAYMENT.CAPTURE.DENIED' ],
				[ 'name' => 'PAYMENT.CAPTURE.REFUNDED' ],
				[ 'name' => 'CHECKOUT.ORDER.APPROVED' ],
				[ 'name' => 'CHECKOUT.ORDER.PROCESSED' ],
				[ 'name' => 'CUSTOMER.DISPUTE.CREATED' ],
				[ 'name' => 'CUSTOMER.DISPUTE.RESOLVED' ]
			]
		] ) );
		if ( ! is_wp_error( $webhook ) ) {
			$this->api_settings->set_webhook_id( $webhook->id, $environment );

			return [
				'id'          => $webhook->id,
				'environment' => $environment,
				'message'     => __( 'Your webhook has been created in PayPal.', 'pymntpl-paypal-woocommerce' )
			];
		} else {
			return $webhook;
		}
	}

}