<?php


namespace PaymentPlugins\PayPalSDK;

use PaymentPlugins\PayPalSDK\Service\BaseServiceFactory;
use PaymentPlugins\PayPalSDK\Service\BillingAgreementService;
use PaymentPlugins\PayPalSDK\Service\BillingAgreementTokenService;
use PaymentPlugins\PayPalSDK\Service\OAuthTokenService;
use PaymentPlugins\PayPalSDK\Service\OrderService;
use PaymentPlugins\PayPalSDK\Service\PartnerService;
use PaymentPlugins\PayPalSDK\Service\PaymentAuthorizationService;
use PaymentPlugins\PayPalSDK\Service\PaymentCaptureService;
use PaymentPlugins\PayPalSDK\Service\PaymentService;
use PaymentPlugins\PayPalSDK\Service\TrackingService;
use PaymentPlugins\PayPalSDK\Service\WebhookService;
use PaymentPlugins\PayPalSDK\Service\PaymentTokenService;

/**
 * Class PayPalClient
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property OrderService $orders
 * @property OAuthTokenService $auth
 * @property PartnerService $partner
 * @property PaymentCaptureService $captures
 * @property PaymentAuthorizationService $authorizations
 * @property BillingAgreementService $billingAgreements
 * @property BillingAgreementTokenService $billingAgreementTokens
 * @property WebhookService $webhooks
 * @property PaymentTokenService $paymentTokens
 * @property TrackingService $tracking
 */
class PayPalClient extends Client\BaseHttpClient {

	/**
	 * @var BaseServiceFactory
	 */
	private $serviceFactory;

	public function __get( $name ) {
		if ( ! $this->serviceFactory ) {
			$this->serviceFactory = new BaseServiceFactory( $this );
		}

		return $this->serviceFactory->{$name};
	}

	public function environment( $env ) {
		$this->environment = $env;

		return $this;
	}

	public function getEnvironment() {
		return $this->environment;
	}

}