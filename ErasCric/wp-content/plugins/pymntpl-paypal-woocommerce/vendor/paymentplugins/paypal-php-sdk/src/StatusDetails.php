<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class StatusDetails
 * @package PaymentPlugins\PayPalSDK
 * @property string $reason
 *
 */
class StatusDetails extends AbstractObject {

	public function getReason() {
		return $this->reason;
	}

	public function setReason( $reason ) {
		$this->reason = $reason;

		return $this;
	}
}