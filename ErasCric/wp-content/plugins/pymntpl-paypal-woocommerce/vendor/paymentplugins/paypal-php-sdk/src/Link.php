<?php


namespace PaymentPlugins\PayPalSDK;


/**
 * Class Link
 * @package PaymentPlugins\PayPalSDK
 *
 * @property string $href
 * @property string $rel
 * @property string $method
 */
class Link extends AbstractObject {
	/**
	 * @return string
	 */
	public function getHref() {
		return $this->href;
	}

	/**
	 * @param string $href
	 */
	public function setHref( $href ) {
		$this->href = $href;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRel() {
		return $this->rel;
	}

	/**
	 * @param string $rel
	 */
	public function setRel( $rel ) {
		$this->rel = $rel;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @param string $method
	 */
	public function setMethod( $method ) {
		$this->method = $method;

		return $this;
	}
}