<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class WebhookEvent
 *
 * @package PaymentPlugins\PayPalSDK
 *
 * @property string     $id
 * @property string     $create_time
 * @property string     $resource_type
 * @property string     $event_version
 * @property string     $event_type
 * @property string     $summary
 * @property string     $resource_version
 * @property array      $resource
 * @property Collection $links
 */
class WebhookEvent extends AbstractObject {

	protected function convertToPayPalObject( $prop, $key, $props = [] ) {
		if ( $key === 'resource' && isset( $props['resource_type'] ) ) {
			return Utils::convertToPayPalObject( $prop, $props['resource_type'] );
		}

		return parent::convertToPayPalObject( $prop, $key );
	}

	/**
	 * @return string
	 */
	public function getId(): string {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId( string $id ): void {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getCreateTime(): string {
		return $this->create_time;
	}

	/**
	 * @param string $create_time
	 */
	public function setCreateTime( string $create_time ): void {
		$this->create_time = $create_time;
	}

	/**
	 * @return string
	 */
	public function getResourceType(): string {
		return $this->resource_type;
	}

	/**
	 * @param string $resource_type
	 */
	public function setResourceType( string $resource_type ): void {
		$this->resource_type = $resource_type;
	}

	/**
	 * @return string
	 */
	public function getEventVersion(): string {
		return $this->event_version;
	}

	/**
	 * @param string $event_version
	 */
	public function setEventVersion( string $event_version ): void {
		$this->event_version = $event_version;
	}

	/**
	 * @return string
	 */
	public function getEventType(): string {
		return $this->event_type;
	}

	/**
	 * @param string $event_type
	 */
	public function setEventType( string $event_type ): void {
		$this->event_type = $event_type;
	}

	/**
	 * @return string
	 */
	public function getSummary(): string {
		return $this->summary;
	}

	/**
	 * @param string $summary
	 */
	public function setSummary( string $summary ): void {
		$this->summary = $summary;
	}

	/**
	 * @return string
	 */
	public function getResourceVersion(): string {
		return $this->resource_version;
	}

	/**
	 * @param string $resource_version
	 */
	public function setResourceVersion( string $resource_version ): void {
		$this->resource_version = $resource_version;
	}

	/**
	 * @return array
	 */
	public function getResource(): array {
		return $this->resource;
	}

	/**
	 * @param array $resource
	 */
	public function setResource( array $resource ): void {
		$this->resource = $resource;
	}

	/**
	 * @return Collection
	 */
	public function getLinks(): Collection {
		return $this->links;
	}

	/**
	 * @param Collection $links
	 */
	public function setLinks( Collection $links ): void {
		$this->links = $links;
	}

}