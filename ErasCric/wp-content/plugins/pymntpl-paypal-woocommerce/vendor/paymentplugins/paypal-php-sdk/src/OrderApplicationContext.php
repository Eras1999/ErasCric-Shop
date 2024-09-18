<?php

namespace PaymentPlugins\PayPalSDK;

/**
 * @property stsring $brand_name
 * @property string  $locale
 * @property string  $landing_page
 * @property string  $shipping_preference
 * @property string  $user_action
 * @property string  $return_url
 * @property string  $cancel_url
 * @propery $payment_method
 */
class OrderApplicationContext extends AbstractObject {

	const LOGIN = 'LOGIN';

	const BILLING = 'BILLING';

	const NO_PREFERENCE = 'NO_PREFERENCE';

	const GET_FROM_FILE = 'GET_FROM_FILE';

	const NO_SHIPPING = 'NO_SHIPPING';

	const SET_PROVIDED_ADDRESS = 'SET_PROVIDED_ADDRESS';

	const CONTINUE = 'CONTINUE';

	const PAY_NOW = 'PAY_NOW';

	/**
	 * @return \PaymentPlugins\PayPalSDK\stsring
	 */
	public function getBrandName(): stsring {
		return $this->brand_name;
	}

	/**
	 * @param \PaymentPlugins\PayPalSDK\stsring $brand_name
	 */
	public function setBrandName( $brand_name ) {
		$this->brand_name = $brand_name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLocale(): string {
		return $this->locale;
	}

	/**
	 * @param string $locale
	 */
	public function setLocale( $locale ) {
		$this->locale = $locale;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLandingPage(): string {
		return $this->landing_page;
	}

	/**
	 * @param string $landing_page
	 */
	public function setLandingPage( $landing_page ) {
		$this->landing_page = $landing_page;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getShippingPreference(): string {
		return $this->shipping_preference;
	}

	/**
	 * @param string $shipping_preference
	 */
	public function setShippingPreference( $shipping_preference ) {
		$this->shipping_preference = $shipping_preference;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserAction(): string {
		return $this->user_action;
	}

	/**
	 * @param string $user_action
	 */
	public function setUserAction( $user_action ) {
		$this->user_action = $user_action;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getReturnUrl(): string {
		return $this->return_url;
	}

	/**
	 * @param string $return_url
	 */
	public function setReturnUrl( $return_url ) {
		$this->return_url = $return_url;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCancelUrl(): string {
		return $this->cancel_url;
	}

	/**
	 * @param string $cancel_url
	 */
	public function setCancelUrl( $cancel_url ) {
		$this->cancel_url = $cancel_url;

		return $this;
	}


}