<?php

namespace PaymentPlugins\PayPalSDK\V1;

/**
 * @property string $transaction_id
 * @property string $tracking_number
 * @property string $tracking_number_type
 * @property string $status
 * @property string $shipment_date
 * @property string $carrier
 * @property string $carrier_name_other
 * @property boolean $notify_buyer
 * @property string $last_updated_time
 */
class Tracker extends \PaymentPlugins\PayPalSDK\AbstractObject {

	// Carriers

	const ARAMEX = 'ARAMEX';

	const B_TWO_C_EUROPE = 'B_TWO_C_EUROPE';

	const CJ_LOGISTICS = 'CJ_LOGISTICS';

	const CORREOS_EXPRESS = 'CORREOS_EXPRESS';

	const DHL_ACTIVE_TRACING = 'DHL_ACTIVE_TRACING';

	const DHL_BENELUX = 'DHL_BENELUX';

	const DHL_GLOBAL_MAIL = 'DHL_GLOBAL_MAIL';

	const DHL_GLOBAL_MAIL_ASIA = 'DHL_GLOBAL_MAIL_ASIA';

	const DHL = 'DHL';

	const DHL_GLOBAL_ECOMMERCE = 'DHL_GLOBAL_ECOMMERCE';

	const DHL_PACKET = 'DHL_PACKET';

	const DPD = 'DPD';

	const DPD_LOCAL = 'DPD_LOCAL';

	const DPD_LOCAL_REF = 'DPD_LOCAL_REF';

	const DPE_EXPRESS = 'DPE_EXPRESS';

	const DPEX = 'DPEX';

	const DTDC_EXPRESS = 'DTDC_EXPRESS';

	const ESHOPWORLD = 'ESHOPWORLD';

	const FEDEX = 'FEDEX';

	const FLYT_EXPRESS = 'FLYT_EXPRESS';

	const GLS = 'GLS';

	const IMX = 'IMX';

	const INT_SUER = 'INT_SUER';

	const LANDMARK_GLOBAL = 'LANDMARK_GLOBAL';

	const MATKAHUOLTO = 'MATKAHUOLTO';

	const OMNIPARCEL = 'OMNIPARCEL';

	const ONE_WORLD = 'ONE_WORLD';

	const OTHER = 'OTHER';

	const POSTI = 'POSTI';

	const RABEN_GROUP = 'RABEN_GROUP';

	const SF_EXPRESS = 'SF_EXPRESS';

	const SKYNET_Worldwide = 'SKYNET_Worldwide';

	const SPREADEL = 'SPREADEL';

	const TNT = 'TNT';

	const UPS = 'UPS';

	const UPS_MI = 'UPS_MI';

	const WEBINTERPRET = 'WEBINTERPRET';

	// Shipping Statuses

	const SHIPPED = 'SHIPPED';

	const ON_HOLD = 'ON_HOLD';

	const DELIVERED = 'DELIVERED';

	const CANCELLED = 'CANCELLED';

	// Tracking Number Types

	const CARRIER_PROVIDED = 'CARRIER_PROVIDED';

	const E2E_PARTNER_PROVIDED = 'E2E_PARTNER_PROVIDED';

	/**
	 * @return string
	 */
	public function getTransactionId() {
		return $this->transaction_id;
	}

	/**
	 * @param string $transaction_id
	 */
	public function setTransactionId( $transaction_id ) {
		$this->transaction_id = $transaction_id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTrackingNumber() {
		return $this->tracking_number;
	}

	/**
	 * @param string $tracking_number
	 */
	public function setTrackingNumber( $tracking_number ) {
		$this->tracking_number = $tracking_number;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTrackingNumberType() {
		return $this->tracking_number_type;
	}

	/**
	 * @param string $tracking_number_type
	 */
	public function setTrackingNumberType( $tracking_number_type ) {
		$this->tracking_number_type = $tracking_number_type;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus( $status ) {
		$this->status = $status;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getShipmentDate() {
		return $this->shipment_date;
	}

	/**
	 * @param string $shipment_date
	 */
	public function setShipmentDate( $shipment_date ) {
		$this->shipment_date = $shipment_date;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCarrier() {
		return $this->carrier;
	}

	/**
	 * @param string $carrier
	 */
	public function setCarrier( $carrier ) {
		$this->carrier = $carrier;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCarrierNameOther() {
		return $this->carrier_name_other;
	}

	/**
	 * @param string $carrier_name_other
	 */
	public function setCarrierNameOther( $carrier_name_other ) {
		$this->carrier_name_other = $carrier_name_other;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isNotifyBuyer() {
		return $this->notify_buyer;
	}

	/**
	 * @param bool $notify_buyer
	 */
	public function setNotifyBuyer( $notify_buyer ) {
		$this->notify_buyer = $notify_buyer;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastUpdatedTime() {
		return $this->last_updated_time;
	}

	/**
	 * @param string $last_updated_time
	 */
	public function setLastUpdatedTime( $last_updated_time ) {
		$this->last_updated_time = $last_updated_time;

		return $this;
	}


}