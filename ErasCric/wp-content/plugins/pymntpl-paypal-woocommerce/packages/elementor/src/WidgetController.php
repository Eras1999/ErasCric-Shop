<?php

namespace PaymentPlugins\PPCP\Elementor;

use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\PaymentMethodRegistry;

class WidgetController {

	public function initialize() {
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
	}


	/**
	 * @param \Elementor\Widgets_Manager $widgets_mgr
	 *
	 * @return void
	 */
	public function register_widgets( $widgets_mgr ) {
		foreach ( $this->get_widgets() as $widget ) {
			if ( \class_exists( $widget ) ) {
				$widgets_mgr->register( new $widget() );
			}
		}
	}

	private function get_widgets() {
		return [
			'PaymentPlugins\PPCP\Elementor\Widget\CartPaymentButtonsWidget',
			'PaymentPlugins\PPCP\Elementor\Widget\ProductPayLaterMessageWidget',
			'PaymentPlugins\PPCP\Elementor\Widget\ProductPaymentButtonsWidget'
		];
	}

}