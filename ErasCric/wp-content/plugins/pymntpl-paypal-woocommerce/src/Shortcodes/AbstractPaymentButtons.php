<?php

namespace PaymentPlugins\WooCommerce\PPCP\Shortcodes;

class AbstractPaymentButtons extends AbstractShortCode {

	protected $gateway_id = 'ppcp';

	/**
	 * @return \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\AbstractGateway
	 */
	public function get_gateway() {
		return $this->payment_gateways->get_payment_method_registry()->get( $this->gateway_id );
	}

	public function parse_attributes( $attributes ) {
		$defaults = [
			'layout'  => 'vertical',
			'funding' => 'paypal',
			'label'   => $this->get_gateway()->get_option( 'button_label' ),
			'shape'   => $this->get_gateway()->get_option( 'button_shape', 'rect' ),
			'height'  => $this->get_gateway()->get_option( 'button_height' )
		];

		$attributes            = \wp_parse_args( $attributes, $defaults );
		$attributes['funding'] = explode( ',', $attributes['funding'] );
		$attributes['height']  = (int) $attributes['height'];

		return $attributes;
	}

	public function add_shortcode_script_data( $data_api, $context ) {
		if ( $data_api->exists( 'ppcp_data' ) ) {
			$data            = $data_api->get( 'ppcp_data' );
			$data['funding'] = [];
			if ( $this->attributes->has( 'funding' ) ) {
				foreach ( $this->attributes->get( 'funding' ) as $type ) {
					$data['funding'][] = $type;
					$key               = $type . '_sections';
					if ( $type === 'card' ) {
						$key = 'credit_card_sections';
					}
					if ( $type === 'paypal' ) {
						$data['buttons'][ $type ]['label'] = $this->attributes->get( 'label' );
					}
					$data['buttons'][ $type ]['height'] = $this->attributes->get( 'height' );
					$data['buttons'][ $type ]['shape']  = $this->attributes->get( 'shape' );
					$data[ $key ]                       = array_merge( $data[ $key ], $this->get_supported_pages() );
				}
			}
			$data_api->add( 'ppcp_data', $data );
		}
	}

	public function render() {
		// TODO: Implement render() method.
	}

}