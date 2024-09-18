<?php


namespace PaymentPlugins\WooCommerce\PPCP\Rest\Routes;


use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

class BillingAgreementRoute extends AbstractRoute {

	private $client;

	public function __construct( WPPayPalClient $client ) {
		$this->client = $client;
	}

	public function get_path() {
		return 'billing-agreements/(?P<id>[\w-]+)';
	}

	public function get_routes() {
		return [
			[
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => [ $this, 'handle_request' ]
			]
		];
	}

	public function handle_get_request( \WP_REST_Request $request ) {
		$billing_agreement_id = $request['id'];
		$billing_agreement    = $this->client->billingAgreements->retrieve( $billing_agreement_id );

		return $billing_agreement;
	}
}