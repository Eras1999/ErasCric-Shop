<?php

namespace PaymentPlugins\PayPalSDK;

/**
 * @property string $id
 * @property string $type
 */
class Token extends AbstractObject {

	const BILLING_AGREEMENT = 'BILLING_AGREEMENT';

	const PAYMENT_METHOD_TOKEN = 'PAYMENT_METHOD_TOKEN';

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId( $id ) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType( $type ) {
		$this->type = $type;

		return $this;
	}

}