<?php

namespace PaymentPlugins\WooCommerce\PPCP\Utilities;

use PaymentPlugins\PayPalSDK\V1\Tracker;

class ShippingUtil {

	public static function get_carriers() {
		return apply_filters( 'wc_ppcp_get_shipping_carriers', [
			Tracker::ARAMEX               => __( 'Aramex', 'pymntpl-paypal-woocommerce' ),
			Tracker::B_TWO_C_EUROPE       => __( 'B2C Europe', 'pymntpl-paypal-woocommerce' ),
			Tracker::CJ_LOGISTICS         => __( 'CJ Logistics', 'pymntpl-paypal-woocommerce' ),
			Tracker::CORREOS_EXPRESS      => __( 'Correos Express', 'pymntpl-paypal-woocommerce' ),
			Tracker::DHL_ACTIVE_TRACING   => __( 'DHL Active Tracing', 'pymntpl-paypal-woocommerce' ),
			Tracker::DHL_BENELUX          => __( 'DHL Benelux', 'pymntpl-paypal-woocommerce' ),
			Tracker::DHL_GLOBAL_MAIL      => __( 'DHL ecCommerce US', 'pymntpl-paypal-woocommerce' ),
			Tracker::DHL_GLOBAL_MAIL_ASIA => __( 'DHL eCommerce Asia', 'pymntpl-paypal-woocommerce' ),
			Tracker::DHL                  => __( 'DHL Express', 'pymntpl-paypal-woocommerce' ),
			Tracker::DHL_GLOBAL_ECOMMERCE => __( 'DHL Global eCommerce', 'pymntpl-paypal-woocommerce' ),
			Tracker::DHL_PACKET           => __( 'DHL Packet', 'pymntpl-paypal-woocommerce' ),
			Tracker::DPD                  => __( 'DPD Global', 'pymntpl-paypal-woocommerce' ),
			Tracker::DPD_LOCAL            => __( 'DPD Local', 'pymntpl-paypal-woocommerce' ),
			Tracker::DPD_LOCAL_REF        => __( 'DPD Local Reference', 'pymntpl-paypal-woocommerce' ),
			Tracker::DPE_EXPRESS          => __( 'DPE Express', 'pymntpl-paypal-woocommerce' ),
			Tracker::DPEX                 => __( 'DPEX Hong Kong', 'pymntpl-paypal-woocommerce' ),
			Tracker::DTDC_EXPRESS         => __( 'DTDC Express Global', 'pymntpl-paypal-woocommerce' ),
			Tracker::ESHOPWORLD           => __( 'EShopWorld', 'pymntpl-paypal-woocommerce' ),
			Tracker::FEDEX                => __( 'FedEx', 'pymntpl-paypal-woocommerce' ),
			Tracker::FLYT_EXPRESS         => __( 'FLYT Express', 'pymntpl-paypal-woocommerce' ),
			Tracker::GLS                  => __( 'GLS', 'pymntpl-paypal-woocommerce' ),
			Tracker::IMX                  => __( 'IMX France', 'pymntpl-paypal-woocommerce' ),
			Tracker::INT_SUER             => __( 'International SEUR', 'pymntpl-paypal-woocommerce' ),
			Tracker::LANDMARK_GLOBAL      => __( 'Landmark Global', 'pymntpl-paypal-woocommerce' ),
			Tracker::MATKAHUOLTO          => __( 'Matkahuoloto', 'pymntpl-paypal-woocommerce' ),
			Tracker::OMNIPARCEL           => __( 'Omni Parcel', 'pymntpl-paypal-woocommerce' ),
			Tracker::ONE_WORLD            => __( 'One World', 'pymntpl-paypal-woocommerce' ),
			Tracker::OTHER                => __( 'Other', 'pymntpl-paypal-woocommerce' ),
			Tracker::POSTI                => __( 'Posti', 'pymntpl-paypal-woocommerce' ),
			Tracker::RABEN_GROUP          => __( 'Raben Group', 'pymntpl-paypal-woocommerce' ),
			Tracker::SF_EXPRESS           => __( 'SF EXPRESS', 'pymntpl-paypal-woocommerce' ),
			Tracker::SKYNET_Worldwide     => __( 'SkyNet Worldwide Express', 'pymntpl-paypal-woocommerce' ),
			Tracker::SPREADEL             => __( 'Spreadel', 'pymntpl-paypal-woocommerce' ),
			Tracker::TNT                  => __( 'TNT Global', 'pymntpl-paypal-woocommerce' ),
			Tracker::UPS                  => __( 'UPS', 'pymntpl-paypal-woocommerce' ),
			Tracker::UPS_MI               => __( 'UPS Mail Innovations', 'pymntpl-paypal-woocommerce' ),
			Tracker::WEBINTERPRET         => __( 'WebInterpret', 'pymntpl-paypal-woocommerce' )
		] );
	}

	public static function get_shipping_statuses() {
		return [
			Tracker::SHIPPED   => __( 'Shipped', 'pymntpl-paypal-woocommerce' ),
			Tracker::ON_HOLD   => __( 'On Hold', 'pymntpl-paypal-woocommerce' ),
			Tracker::DELIVERED => __( 'Delivered', 'pymntpl-paypal-woocommerce' ),
			Tracker::CANCELLED => __( 'Canceled', 'pymntpl-paypal-woocommerce' )
		];
	}

	public static function get_tracking_types() {
		return [
			Tracker::CARRIER_PROVIDED     => __( 'Carrier Provided', 'pymntpl-paypal-woocommerce' ),
			Tracker::E2E_PARTNER_PROVIDED => __( 'Marketplace', 'pymntpl-paypal-woocommerce' ),
		];
	}

}