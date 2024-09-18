<?php


namespace PaymentPlugins\PayPalSDK;

use PaymentPlugins\PayPalSDK\V1\Address;

/**
 * Class BillingAgreementToken
 * @package PaymentPlugins\PayPalSDK
 *
 * @property string $description
 * @property string $token_id
 * @property string $token_status
 * @property string $experience_id
 * @property boolean $skip_shipping_address
 * @property boolean $immutable_shipping_address
 * @property string $external_selected_funding_instrument_type
 * @property array $accepted_legal_country_codes
 * @property Address $shipping_address
 * @property object $redirect_urls
 * @property array $plan_unit_list
 * @property PayerInfo $payer_info
 * @property Payee $owner
 * @property Collection $links
 */
class BillingAgreementToken extends AbstractObject {
	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription( $description ) {
		$this->description = $description;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTokenId() {
		return $this->token_id;
	}

	/**
	 * @param string $token_id
	 */
	public function setTokenId( $token_id ) {
		$this->token_id = $token_id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTokenStatus() {
		return $this->token_status;
	}

	/**
	 * @param string $token_status
	 */
	public function setTokenStatus( $token_status ) {
		$this->token_status = $token_status;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getExperienceId() {
		return $this->experience_id;
	}

	/**
	 * @param string $experience_id
	 */
	public function setExperienceId( $experience_id ) {
		$this->experience_id = $experience_id;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSkipShippingAddress() {
		return $this->skip_shipping_address;
	}

	/**
	 * @param bool $skip_shipping_address
	 */
	public function setSkipShippingAddress( $skip_shipping_address ) {
		$this->skip_shipping_address = $skip_shipping_address;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isImmutableShippingAddress() {
		return $this->immutable_shipping_address;
	}

	/**
	 * @param bool $immutable_shipping_address
	 */
	public function setImmutableShippingAddress( $immutable_shipping_address ) {
		$this->immutable_shipping_address = $immutable_shipping_address;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getExternalSelectedFundingInstrumentType() {
		return $this->external_selected_funding_instrument_type;
	}

	/**
	 * @param string $external_selected_funding_instrument_type
	 */
	public function setExternalSelectedFundingInstrumentType( $external_selected_funding_instrument_type ) {
		$this->external_selected_funding_instrument_type = $external_selected_funding_instrument_type;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getAcceptedLegalCountryCodes() {
		return $this->accepted_legal_country_codes;
	}

	/**
	 * @param array $accepted_legal_country_codes
	 */
	public function setAcceptedLegalCountryCodes( $accepted_legal_country_codes ) {
		$this->accepted_legal_country_codes = $accepted_legal_country_codes;

		return $this;
	}

	/**
	 * @return Address
	 */
	public function getShippingAddress() {
		return $this->shipping_address;
	}

	/**
	 * @param Address $shipping_address
	 */
	public function setShippingAddress( $shipping_address ) {
		$this->shipping_address = $shipping_address;

		return $this;
	}

	/**
	 * @return object
	 */
	public function getRedirectUrls() {
		return $this->redirect_urls;
	}

	/**
	 * @param object $redirect_urls
	 */
	public function setRedirectUrls( $redirect_urls ) {
		$this->redirect_urls = $redirect_urls;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getPlanUnitList() {
		return $this->plan_unit_list;
	}

	/**
	 * @param array $plan_unit_list
	 */
	public function setPlanUnitList( $plan_unit_list ) {
		$this->plan_unit_list = $plan_unit_list;

		return $this;
	}

	/**
	 * @return PayerInfo
	 */
	public function getPayerInfo() {
		return $this->payer_info;
	}

	/**
	 * @param PayerInfo $payer_info
	 */
	public function setPayerInfo( $payer_info ) {
		$this->payer_info = $payer_info;

		return $this;
	}

	/**
	 * @return Payee
	 */
	public function getOwner() {
		return $this->owner;
	}

	/**
	 * @param Payee $owner
	 */
	public function setOwner( $owner ) {
		$this->owner = $owner;

		return $this;
	}

	/**
	 * @return Collection
	 */
	public function getLinks() {
		return $this->links;
	}

	/**
	 * @param Collection $links
	 */
	public function setLinks( $links ) {
		$this->links = $links;

		return $this;
	}

	public function getApprovalUrl() {
		foreach ( $this->getLinks()->getValues() as $link ) {
			if ( $link->getRel() === 'approval_url' ) {
				return $link->getHref();
			}
		}

		return '';
	}
}