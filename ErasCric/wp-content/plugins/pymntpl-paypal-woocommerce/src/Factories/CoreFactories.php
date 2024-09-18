<?php

namespace PaymentPlugins\WooCommerce\PPCP\Factories;

use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\AdvancedSettings;
use PaymentPlugins\WooCommerce\PPCP\Main;

/**
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\OrderFactory                 $order
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\BreakdownFactory             $breakdown
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\PayerFactory                 $payer
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\AddressFactroy               $address
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\ShippingFactory              $shipping
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\ShippingOptionsFactory       $shippingOptions
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\ItemsFactory                 $items
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\ApplicationContextFactory    $applicationContext
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\PurchaseUnitFactory          $purchaseUnit
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\NameFactory                  $name
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\PaymentSourceFactory         $paymentSource
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\BillingAgreementTokenFactory $billingAgreement
 * @property \PaymentPlugins\WooCommerce\PPCP\Factories\RefundFactory                $refunds
 */
class CoreFactories {

	protected $factories = [];

	private $cart;

	private $customer;

	private $order;

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\Container\Container $container
	 *
	 * @throws \Exception
	 */
	public function register( $container ) {
		$this->factories = [
			'order'              => new OrderFactory( $this ),
			'breakdown'          => new BreakdownFactory( $this ),
			'payer'              => new PayerFactory( $this ),
			'address'            => new AddressFactroy( $this ),
			'shipping'           => new ShippingFactory( $this ),
			'shippingOptions'    => new ShippingOptionsFactory( $this ),
			'items'              => new ItemsFactory( $this ),
			'applicationContext' => new ApplicationContextFactory( $container->get( AdvancedSettings::class ), $this ),
			'purchaseUnit'       => new PurchaseUnitFactory( $container->get( AdvancedSettings::class ), $this ),
			'name'               => new NameFactory( $this ),
			'paymentSource'      => new PaymentSourceFactory( $this ),
			'billingAgreement'   => new BillingAgreementTokenFactory( $this ),
			'refunds'            => new RefundFactory( $container->get( AdvancedSettings::class ), $this )
		];
	}

	public function initialize( ...$args ) {
		foreach ( $args as $arg ) {
			if ( $arg instanceof \WC_Cart ) {
				$this->set_cart( $arg );
			} elseif ( $arg instanceof \WC_Customer ) {
				$this->set_customer( $arg );
			} elseif ( $arg instanceof \WC_Order ) {
				$this->set_order( $arg );
			}
		}

		return $this;
	}

	public function __get( $key ) {
		$factory = $this->get_factory( $key );
		if ( $factory ) {
			$factory->set_cart( $this->cart );
			$factory->set_customer( $this->customer );
			$factory->set_order( $this->order );
			if ( $this->order ) {
				$factory->set_currency( $this->order->get_currency() );
			} else {
				$factory->set_currency( get_woocommerce_currency() );
			}
		}

		return $factory;
	}

	/**
	 * @param $key
	 *
	 * @return \PaymentPlugins\WooCommerce\PPCP\Factories\AbstractFactory|null
	 */
	private function get_factory( $key ) {
		if ( isset( $this->factories[ $key ] ) ) {
			return $this->factories[ $key ];
		}
		\trigger_error( 'Invalid factory key ' . esc_html( $key ) );

		return null;
	}

	public function set_order( $order ) {
		$this->order = $order;

		return $this;
	}

	public function set_customer( $customer ) {
		$this->customer = $customer;

		return $this;
	}

	public function set_cart( $cart ) {
		$this->cart = $cart;

		return $this;
	}

}