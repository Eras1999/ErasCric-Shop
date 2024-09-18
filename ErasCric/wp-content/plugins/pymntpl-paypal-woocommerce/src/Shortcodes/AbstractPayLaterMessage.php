<?php

namespace PaymentPlugins\WooCommerce\PPCP\Shortcodes;

use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\PayLaterMessageSettings;
use PaymentPlugins\WooCommerce\PPCP\Container\Container;

class AbstractPayLaterMessage extends AbstractShortCode {

	/**
	 * @var PayLaterMessageSettings
	 */
	protected $settings;

	protected $style_key;

	public function initialize_properties( Container $container ) {
		$this->settings = $container->get( PayLaterMessageSettings::class );
	}

	public function parse_attributes( $attributes ) {
		$defaults = $this->settings->get_context_options( $this->style_key )['style'];

		$attributes['layout']           = $attributes['layout'] ?? $defaults['layout'];
		$attributes['logo']['type']     = $attributes['logo_type'] ?? $defaults['logo']['type'];
		$attributes['logo']['position'] = $attributes['logo_position'] ?? $defaults['logo']['position'];
		$attributes['text']['color']    = $attributes['text_color'] ?? $defaults['text']['color'];
		$attributes['text']['size']     = $attributes['text_size'] ?? $defaults['text']['size'];
		$attributes['color']            = $attributes['flex_color'] ?? $this->settings->get_option( 'product_flex_color' );
		$attributes['ratio']            = $attributes['flex_ratio'] ?? $this->settings->get_option( 'product_flex_ratio' );

		return $attributes;
	}

	public function add_shortcode_script_data( $data_api, $context ) {
		$data = $data_api->get( 'payLaterMessage' );
		if ( ! $data ) {
			$data = [];
		}
		$data['enabled']                   = true;
		$data['isShortcode']               = true;
		$data[ $this->style_key ]['style'] = [
			'layout' => $this->attributes->get( 'layout' ),
			'logo'   => $this->attributes->get( 'logo' ),
			'text'   => $this->attributes->get( 'text' ),
			'color'  => $this->attributes->get( 'color' ),
			'ratio'  => $this->attributes->get( 'ratio' )
		];
		if ( $data[ $this->style_key ]['style']['layout'] === 'text' ) {
			unset( $data[ $this->style_key ]['style']['color'] );
			unset( $data[ $this->style_key ]['style']['ratio'] );
		} else {
			unset( $data[ $this->style_key ]['style']['text'] );
			unset( $data[ $this->style_key ]['style']['logo'] );
		}
		$data_api->add( 'payLaterMessage', $data );
	}

	public function render() {
	}

}