<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Webhook
 * @package PaymentPlugins\PayPalSDK
 * @property string $id
 * @property string $url
 * @property Collection $event_types
 * @property Collection $links
 */
class Webhook extends AbstractObject {
	/**
	 * @param string $id
	 */
	public function setId( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @param string $url
	 */
	public function setUrl( $url ) {
		$this->url = $url;

		return $this;
	}

	/**
	 * @param Collection $event_types
	 */
	public function setEventTypes( $event_types ) {
		$this->event_types = $event_types;

		return $this;
	}

	/**
	 * @param Collection $links
	 */
	public function setLinks( $links ) {
		$this->links = $links;

		return $this;
	}


	public function getId() {
		return $this->id;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getEventTypes() {
		return $this->event_types;
	}

	public function getLinks() {
		return $this->links;
	}
}