<?php


namespace PaymentPlugins\PPCP\Blocks\Payments\Gateways;

/**
 * Class PayPalGateway
 *
 * @package PaymentPlugins\PPCP\Blocks\Payments\Gateways
 */
class PayPalGateway extends AbstractGateway {

	protected $name = 'ppcp';

	public function get_payment_method_script_handles() {
		$this->assets_api->register_script( 'wc-ppcp-blocks-commons', 'build/blocks-commons.js' );
		$this->assets_api->register_script( 'wc-ppcp-blocks-paypal', 'build/paypal.js', [ 'wc-ppcp-blocks-commons' ] );
		$this->assets_api->register_script( 'wc-ppcp-blocks-checkout', 'build/checkout-block.js', [ 'wc-ppcp-blocks-commons' ] );

		return [ 'wc-ppcp-blocks-paypal', 'wc-ppcp-blocks-checkout' ];
	}

	public function get_payment_method_data() {
		$sources = [ 'paypal', 'paylater', 'card', 'venmo' ];
		$data    = [
			'payLaterEnabled'         => wc_string_to_bool( $this->get_setting( 'paylater_enabled', 'no' ) ),
			'cardEnabled'             => wc_string_to_bool( $this->get_setting( 'card_enabled', 'no' ) ),
			'venmoEnabled'            => wc_string_to_bool( $this->get_setting( 'venmo_enabled', 'no' ) ),
			'paypalSections'          => $this->get_setting( 'sections', [] ),
			'payLaterSections'        => $this->get_setting( 'paylater_sections', [] ),
			'creditCardSections'      => $this->get_setting( 'credit_card_sections', [] ),
			'venmoSections'           => $this->get_setting( 'venmo_sections', [] ),
			'buttonOrder'             => $this->get_setting( 'buttons_order' ),
			'placeOrderButtonEnabled' => wc_string_to_bool( $this->get_setting( 'use_place_order' ) ),
			'redirectIcon'            => $this->assets_api->assets_url( '../../assets/img/popup.svg' ),
			'i18n'                    => [
				'redirectText' => sprintf( esc_html__( 'After clicking "%1$s", you will be redirected to PayPal to complete your purchase securely.', 'pymntpl-paypal-woocommerce' ), $this->get_order_button_text() ),
				'buttonLabel'  => esc_html( $this->get_order_button_text() )
			],
			'buttons'                 => array_combine( $sources, array_map( function ( $source ) {
				if ( $source === 'venmo' ) {
					return [
						'layout' => 'vertical',
						'shape'  => $this->get_setting( 'button_shape' ),
						'height' => (int) $this->get_setting( 'button_height' )
					];
				}

				return [
					'layout' => 'vertical',
					'label'  => $this->get_setting( 'button_label' ),
					'shape'  => $this->get_setting( 'button_shape' ),
					'height' => (int) $this->get_setting( 'button_height' ),
					'color'  => $this->get_setting( "{$source}_button_color" )
				];
			}, $sources ) ),
		];

		if ( $this->is_redirect_with_order() ) {
			$redirect_data = json_decode( base64_decode( rawurldecode( wc_clean( $_REQUEST['_ppcp_order_review'] ) ) ), true );
			if ( isset( $redirect_data['paypal_order'] ) ) {
				$order = $this->client->orders->retrieve( $redirect_data['paypal_order'] );
				if ( ! is_wp_error( $order ) ) {
					$data['paymentData']  = [
						'order'   => $order,
						'orderId' => $order->getId()
					];
					$data['errorMessage'] = __( 'Some required fields are missing. Please review your details and then complete your order with PayPal.', 'pymntpl-paypal-woocommerce' );
					if ( function_exists( 'wc_clear_notices' ) ) {
						wc_clear_notices();
					}
				}
			}
		}

		return array_merge( parent::get_payment_method_data(), $data );
	}

	public function get_payment_method_icons() {
		return [
			[
				'id'  => 'paypal',
				'src' => $this->assets_api->assets_url( '../../assets/img/paypal_logo.svg' ),
				'alt' => 'PayPal'
			],
			[
				'id'  => 'paypal_simple',
				'src' => $this->assets_api->assets_url( '../../assets/img/paypal_simple.svg' ),
				'alt' => 'PayPal'
			]
		];
	}

	private function get_order_button_text() {
		$text = $this->get_setting( 'order_button_text', null );
		if ( ! $text ) {
			$text = __( 'Pay with PayPal', 'pymntpl-paypal-woocommerce' );
		}

		return $text;
	}

}