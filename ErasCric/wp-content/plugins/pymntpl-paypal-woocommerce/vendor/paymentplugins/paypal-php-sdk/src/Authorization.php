<?php


namespace PaymentPlugins\PayPalSDK;

/**
 * Class Authorization
 *
 * @package PaymentPlugins\PayPalSDK
 * @property string           $id
 * @property string           $status
 * @property StatusDetails    $status_details
 * @property Amount           $amount
 * @property string           $invoice_id
 * @property string           $custom_id
 * @property SellerProtection $seller_protection,
 * @property string           $expiration_time
 * @property Collection       $links
 * @property string           $create_time
 * @property string           $update_time
 */
class Authorization extends AbstractObject {

	const CREATED = 'CREATED';

	const CAPTURED = 'CAPTURED';

	public function isCreated() {
		return $this->status === self::CREATED;
	}

	public function isCaptured() {
		return $this->status === self::CAPTURED;
	}

}