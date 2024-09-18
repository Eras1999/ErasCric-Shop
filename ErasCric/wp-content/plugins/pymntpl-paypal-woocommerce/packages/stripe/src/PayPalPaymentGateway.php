<?php

namespace PaymentPlugins\PPCP\Stripe;

class PayPalPaymentGateway extends \PaymentPlugins\WooCommerce\PPCP\Payments\Gateways\PayPalGateway {

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Package\PackageRegistry
	 */
	private $packages;

	public function __construct( $packages, ...$args ) {
		$this->packages = $packages;
		parent::__construct( ...$args );
	}

	public function init_supports() {
		parent::init_supports();
		$checkout_wc = $this->packages->get( 'checkoutwc' );
		if ( ! $checkout_wc->is_active() ) {
			$this->supports[] = 'wc_stripe_banner_checkout';
		}
		$this->supports[] = 'wc_stripe_cart_checkout';
		$this->supports[] = 'wc_stripe_product_checkout';
	}

	public function banner_checkout_enabled() {
		return $this->is_section_enabled( 'express_checkout' );
	}

	public function cart_checkout_enabled() {
		return $this->is_section_enabled( 'cart' );
	}

	public function product_checkout_enabled() {
		return $this->is_section_enabled( 'product' );
	}

	public function get_express_checkout_script_handles() {
		$handles = [];
		if ( $this->banner_checkout_enabled() ) {
			$this->assets->register_script( 'wc-ppcp-checkout-express', 'build/js/paypal-express-checkout.js' );
			$handles[] = 'wc-ppcp-checkout-express';
		}

		return $handles;
	}

	public function get_product_script_handles() {
		$handles = parent::get_product_script_handles();
		$this->assets->register_script( 'wc-ppcp-stripe-compatibility', 'packages/stripe/build/stripe-compatibility.js' );
		$handles[] = 'wc-ppcp-stripe-compatibility';

		return $handles;
	}

	public function get_cart_script_handles() {
		$handles = parent::get_cart_script_handles();
		$this->assets->register_script( 'wc-ppcp-stripe-compatibility', 'packages/stripe/build/stripe-compatibility.js' );
		$handles[] = 'wc-ppcp-stripe-compatibility';

		return $handles;
	}

	public function get_checkout_script_handles() {
		$handles = parent::get_checkout_script_handles();
		$this->assets->register_script( 'wc-ppcp-stripe-compatibility', 'packages/stripe/build/stripe-compatibility.js' );
		$handles[] = 'wc-ppcp-stripe-compatibility';

		return $handles;
	}

	public function get_payment_method_data( $context = 'checkout' ) {
		$data        = parent::get_payment_method_data( $context );
		$checkout_wc = $this->packages->get( 'checkoutwc' );
		if ( ! $checkout_wc->is_active() ) {
			$data['expressElement'] = '.banner_payment_method_ppcp';
		}

		return $data;
	}

}