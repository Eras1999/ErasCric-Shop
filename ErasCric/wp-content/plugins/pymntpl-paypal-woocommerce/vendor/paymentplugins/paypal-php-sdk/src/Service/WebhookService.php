<?php


namespace PaymentPlugins\PayPalSDK\Service;

use PaymentPlugins\PayPalSDK\Webhook;

/**
 * Class WebhookService
 *
 * @package PaymentPlugins\PayPalSDK\Service
 */
class WebhookService extends BaseService {

	protected $path = 'v1/notifications';

	/**
	 * Create a webhook
	 *
	 * @param       $params
	 * @param array $options
	 *
	 * @return Webhook
	 */
	public function create( $params, $options = [] ) {
		return $this->post( $this->buildPath( '/webhooks' ), Webhook::class, $params, $options );
	}

	/**
	 * @param $id
	 * @param $options
	 *
	 * @return Webhook
	 */
	public function retrieve( $id, $options = [] ) {
		return $this->get( $this->buildPath( '/webhooks/%s', $id ), Webhook::class, null, $options );
	}

	public function delete( $id, $options = [] ) {
		return $this->request( 'delete', $this->buildPath( '/webhooks/%s', $id ), null, null, $options );
	}

	public function update( $id, $params, $options = [] ) {
		return $this->patch( $this->buildPath( '/webhooks/%s', $id ), Webhook::class, $params, $options );
	}

	/**
	 * Verify the webhook signature
	 *
	 * @param       $params
	 * @param array $options
	 *
	 * @return mixed|object
	 */
	public function verifySignature( $params, $options = [] ) {
		return $this->post( $this->buildPath( '/verify-webhook-signature' ), null, $params, $options );
	}

}