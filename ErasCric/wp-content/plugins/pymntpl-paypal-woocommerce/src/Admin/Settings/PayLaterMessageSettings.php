<?php


namespace PaymentPlugins\WooCommerce\PPCP\Admin\Settings;


use PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi;
use PaymentPlugins\WooCommerce\PPCP\Assets\AssetsApi;
use PaymentPlugins\WooCommerce\PPCP\ContextHandler;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\ProductSettings;
use PaymentPlugins\WooCommerce\PPCP\Utils;

class PayLaterMessageSettings extends AbstractSettings {

	public $id = 'ppcp_paylater_message';

	protected $tab_label_priority = 40;

	private $supported_currencies = [ 'USD', 'GBP', 'EUR', 'AUD' ];

	private $asset_data = [];

	public function __construct( ...$args ) {
		$this->tab_label = __( 'Pay Later Messaging', 'pymntpl-paypal-woocommerce' );
		parent::__construct( ...$args );
		add_action( 'wc_ppcp_add_script_data', [ $this, 'add_script_data' ], 10, 2 );
		add_action( 'woocommerce_review_order_after_order_total', [ $this, 'handle_after_order_total' ] );
		add_action( 'woocommerce_cart_totals_after_order_total', [ $this, 'handle_cart_totals' ] );
		//add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'handle_add_to_cart' ] );
		add_action( 'woocommerce_single_product_summary', [ $this, 'render_above_product_price' ], 8 );
		add_action( 'woocommerce_single_product_summary', [ $this, 'render_below_product_price' ], 15 );
		add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'render_after_add_to_cart' ], 5 );
		add_filter( 'wc_ppcp_script_dependencies', [ $this, 'add_script_handles' ], 10, 3 );
		add_filter( 'wc_ppcp_product_form_fields', [ $this, 'get_product_form_fields' ] );
		add_action( 'woocommerce_shop_loop', [ $this, 'add_shop_loop_data' ] );
		add_action( 'woocommerce_after_shop_loop_item_title', [ $this, 'render_shop_message_container' ], 20 );
		add_action( 'woocommerce_after_shop_loop_item', [ $this, 'render_shop_message_container' ], 20 );
	}

	public function init_form_fields() {
		$this->form_fields = [
			'description'            => [
				'type'        => 'paypal_description',
				'description' => __( 'Pay Later messaging allows you to show your customers how much their financed payment will be using Pay Later.',
					'pymntpl-paypal-woocommerce' )
			],
			'checkout_page'          => [
				'type'  => 'title',
				'title' => __( 'Checkout Page Settings', 'pymntpl-paypal-woocommerce' )
			],
			'checkout_enabled'       => [
				'title'       => __( 'Enabled', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'Enable this option to display the Pay Later messaging on your checkout page.',
					'pymntpl-paypal-woocommerce' )
			],
			'checkout_location'      => [
				'title'       => __( 'Location', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'shop_table',
				'options'     => [
					'shop_table'   => __( 'Below order total', 'pymntpl-paypal-woocommerce' ),
					'above_button' => __( 'Above PayPal button', 'pymntpl-paypal-woocommerce' ),
				],
				'description' => __( 'Location where the Pay Later message appears on the checkout page', 'pymntpl-paypal-woocommerce' ),
			],
			'checkout_layout'        => [
				'title'   => __( 'Layout', 'pymntpl-paypal-woocommerce' ),
				'type'    => 'select',
				'default' => 'text',
				'options' => [
					'text' => __( 'Text', 'pymntpl-paypal-woocommerce' ),
					'flex' => __( 'Flex', 'pymntpl-paypal-woocommerce' )
				]
			],
			'checkout_text_color'    => [
				'title'             => __( 'Text Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'black',
				'options'           => [
					'black'      => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'      => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'monochrome' => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'  => __( 'Grayscale', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'checkout_layout=text'
				]
			],
			'checkout_text_size'     => [
				'title'             => __( 'Text Size', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '12',
				'options'           => [
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16'
				],
				'custom_attributes' => [
					'data-show-if' => 'checkout_layout=text'
				]
			],
			'checkout_logo'          => [
				'title'             => __( 'Logo Type', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'options'           => [
					'primary'     => __( 'Primary', 'pymntpl-paypal-woocommerce' ),
					'alternative' => __( 'Alternative', 'pymntpl-paypal-woocommerce' ),
					'inline'      => __( 'Inline', 'pymntpl-paypal-woocommerce' ),
					'none'        => __( 'None', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'checkout_layout=text'
				],
				'desc_tip'          => true,
				'description'       => __( 'This is the PayPal logo that appears in the Pay Later messaging.', 'pymntpl-paypal-woocommerce' )
			],
			'checkout_logo_position' => [
				'title'             => __( 'Logo Position', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'left',
				'options'           => [
					'left'  => __( 'Left', 'pymntpl-paypal-woocommerce' ),
					'right' => __( 'Right', 'pymntpl-paypal-woocommerce' ),
					'top'   => __( 'Top', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => __( '', 'pymntpl-paypal-woocommerce' ),
				'description'       => __( '', 'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'checkout_layout=text'
				]
			],
			'checkout_flex_color'    => [
				'title'             => __( 'Background Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'blue',
				'options'           => [
					'blue'            => __( 'Blue', 'pymntpl-paypal-woocommerce' ),
					'black'           => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'           => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'white-no-border' => __( 'White no border', 'pymntpl-paypal-woocommerce' ),
					'gray'            => __( 'Gray', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'       => __( 'Grayscale', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => true,
				'description'       => __( 'Sets the color of the message background for flex layout messages.',
					'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'checkout_layout=flex'
				]
			],
			'checkout_flex_ratio'    => [
				'title'             => __( 'Flex Ratio', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '8x1',
				'options'           => [
					'1x1'  => '1x1',
					'1x4'  => '1x4',
					'8x1'  => '8x1',
					'20x1' => '20x1'
				],
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=flex'
				],
				'desc_tip'          => true,
				'description'       => __( 'The width x height ratio of the flex layout.', 'pymntpl-paypal-woocommerce' )
			],
			'cart_page'              => [
				'type'  => 'title',
				'title' => __( 'Cart Page Settings', 'pymntpl-paypal-woocommerce' )
			],
			'cart_enabled'           => [
				'title'       => __( 'Enabled', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'Enable this option to display the Pay Later messaging on your cart page.', 'pymntpl-paypal-woocommerce' )
			],
			'cart_location'          => [
				'title'       => __( 'Location', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'shop_table',
				'options'     => [
					'shop_table'   => __( 'Below cart total', 'pymntpl-paypal-woocommerce' ),
					'above_button' => __( 'Above PayPal button', 'pymntpl-paypal-woocommerce' ),
				],
				'description' => __( 'Location where the Pay Later message appears on the cart page', 'pymntpl-paypal-woocommerce' ),
			],
			'cart_layout'            => [
				'title'   => __( 'Layout', 'pymntpl-paypal-woocommerce' ),
				'type'    => 'select',
				'default' => 'text',
				'options' => [
					'text' => __( 'Text', 'pymntpl-paypal-woocommerce' ),
					'flex' => __( 'Flex', 'pymntpl-paypal-woocommerce' )
				]
			],
			'cart_text_color'        => [
				'title'             => __( 'Text Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'black',
				'options'           => [
					'black'      => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'      => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'monochrome' => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'  => __( 'Grayscale', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=text'
				]
			],
			'cart_text_size'         => [
				'title'             => __( 'Text Size', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '12',
				'options'           => [
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16'
				],
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=text'
				]
			],
			'cart_logo'              => [
				'title'             => __( 'Logo Type', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'options'           => [
					'primary'     => __( 'Primary', 'pymntpl-paypal-woocommerce' ),
					'alternative' => __( 'Alternative', 'pymntpl-paypal-woocommerce' ),
					'inline'      => __( 'Inline', 'pymntpl-paypal-woocommerce' ),
					'none'        => __( 'None', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=text'
				],
				'desc_tip'          => true,
				'description'       => __( 'This is the PayPal logo that appears in the Pay Later messaging.', 'pymntpl-paypal-woocommerce' )
			],
			'cart_logo_position'     => [
				'title'             => __( 'Logo Position', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'left',
				'options'           => [
					'left'  => __( 'Left', 'pymntpl-paypal-woocommerce' ),
					'right' => __( 'Right', 'pymntpl-paypal-woocommerce' ),
					'top'   => __( 'Top', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => __( '', 'pymntpl-paypal-woocommerce' ),
				'description'       => __( '', 'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=text'
				]
			],
			'cart_flex_color'        => [
				'title'             => __( 'Background Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'blue',
				'options'           => [
					'blue'            => __( 'Blue', 'pymntpl-paypal-woocommerce' ),
					'black'           => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'           => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'white-no-border' => __( 'White no border', 'pymntpl-paypal-woocommerce' ),
					'gray'            => __( 'Gray', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'       => __( 'Grayscale', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => true,
				'description'       => __( 'Sets the color of the message background for flex layout messages.',
					'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=flex'
				]
			],
			'cart_flex_ratio'        => [
				'title'             => __( 'Flex Ratio', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '8x1',
				'options'           => [
					'1x1'  => '1x1',
					'1x4'  => '1x4',
					'8x1'  => '8x1',
					'20x1' => '20x1'
				],
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=flex'
				],
				'desc_tip'          => true,
				'description'       => __( 'The width x height ratio of the flex layout.', 'pymntpl-paypal-woocommerce' )
			],
			'product_page'           => [
				'type'  => 'title',
				'title' => __( 'Product Page Settings', 'pymntpl-paypal-woocommerce' )
			],
			'product_enabled'        => [
				'title'       => __( 'Enabled', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'Enable this option to display the Pay Later messaging on your product pages.',
					'pymntpl-paypal-woocommerce' )
			],
			'product_layout'         => [
				'title'   => __( 'Layout', 'pymntpl-paypal-woocommerce' ),
				'type'    => 'select',
				'default' => 'text',
				'options' => [
					'text' => __( 'Text', 'pymntpl-paypal-woocommerce' ),
					'flex' => __( 'Flex', 'pymntpl-paypal-woocommerce' )
				]
			],
			'product_text_color'     => [
				'title'             => __( 'Text Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'black',
				'options'           => [
					'black'      => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'      => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'monochrome' => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'  => __( 'Grayscale', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'product_layout=text'
				]
			],
			'product_text_size'      => [
				'title'             => __( 'Text Size', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '12',
				'options'           => [
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16'
				],
				'custom_attributes' => [
					'data-show-if' => 'product_layout=text'
				]
			],
			'product_logo'           => [
				'title'             => __( 'Logo Type', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'options'           => [
					'primary'     => __( 'Primary', 'pymntpl-paypal-woocommerce' ),
					'alternative' => __( 'Alternative', 'pymntpl-paypal-woocommerce' ),
					'inline'      => __( 'Inline', 'pymntpl-paypal-woocommerce' ),
					'none'        => __( 'None', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'product_layout=text'
				],
				'desc_tip'          => true,
				'description'       => __( 'This is the PayPal logo that appears in the Pay Later messaging.', 'pymntpl-paypal-woocommerce' )
			],
			'product_logo_position'  => [
				'title'             => __( 'Logo Position', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'left',
				'options'           => [
					'left'  => __( 'Left', 'pymntpl-paypal-woocommerce' ),
					'right' => __( 'Right', 'pymntpl-paypal-woocommerce' ),
					'top'   => __( 'Top', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => __( '', 'pymntpl-paypal-woocommerce' ),
				'description'       => __( '', 'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'product_layout=text'
				]
			],
			'product_flex_color'     => [
				'title'             => __( 'Background Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'blue',
				'options'           => [
					'blue'            => __( 'Blue', 'pymntpl-paypal-woocommerce' ),
					'black'           => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'           => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'white-no-border' => __( 'White no border', 'pymntpl-paypal-woocommerce' ),
					'gray'            => __( 'Gray', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'       => __( 'Grayscale', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => true,
				'description'       => __( 'Sets the color of the message background for flex layout messages.',
					'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'product_layout=flex'
				]
			],
			'product_flex_ratio'     => [
				'title'             => __( 'Flex Ratio', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '8x1',
				'options'           => [
					'1x1'  => '1x1',
					'1x4'  => '1x4',
					'8x1'  => '8x1',
					'20x1' => '20x1'
				],
				'custom_attributes' => [
					'data-show-if' => 'product_layout=flex'
				],
				'desc_tip'          => true,
				'description'       => __( 'The width x height ratio of the flex layout.', 'pymntpl-paypal-woocommerce' )
			],
			'product_location'       => [
				'title'       => __( 'Message Location', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'below_price',
				'options'     => array(
					'above_price'       => __( 'Above Price', 'pymntpl-paypal-woocommerce' ),
					'below_price'       => __( 'Below Price', 'pymntpl-paypal-woocommerce' ),
					'below_add_to_cart' => __( 'Below Add to Cart', 'pymntpl-paypal-woocommerce' )
				),
				'desc_tip'    => true,
				'description' => __( 'This option controls the location in which the messaging for the payment method will appear.', 'pymntpl-paypal-woocommerce' )
			],
			'minicart_page'          => [
				'type'  => 'title',
				'title' => __( 'Mini Cart Settings', 'pymntpl-paypal-woocommerce' )
			],
			'minicart_enabled'       => [
				'title'       => __( 'Enabled', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'Enable this option to display the Pay Later messaging on your cart page.', 'pymntpl-paypal-woocommerce' )
			],
			'minicart_location'      => [
				'title'       => __( 'Location', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'shop_table',
				'options'     => [
					'cart_total' => __( 'Below cart total', 'pymntpl-paypal-woocommerce' ),
					'checkout'   => __( 'Below checkout button', 'pymntpl-paypal-woocommerce' ),
				],
				'description' => __( 'Location where the Pay Later message appears on the mini-cart.', 'pymntpl-paypal-woocommerce' ),
			],
			'minicart_layout'        => [
				'title'   => __( 'Layout', 'pymntpl-paypal-woocommerce' ),
				'type'    => 'select',
				'default' => 'text',
				'options' => [
					'text' => __( 'Text', 'pymntpl-paypal-woocommerce' ),
					'flex' => __( 'Flex', 'pymntpl-paypal-woocommerce' )
				]
			],
			'minicart_text_color'    => [
				'title'             => __( 'Text Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'black',
				'options'           => [
					'black'      => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'      => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'monochrome' => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'  => __( 'Grayscale', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=text'
				]
			],
			'minicart_text_size'     => [
				'title'             => __( 'Text Size', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '12',
				'options'           => [
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16'
				],
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=text'
				]
			],
			'minicart_logo'          => [
				'title'             => __( 'Logo Type', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'options'           => [
					'primary'     => __( 'Primary', 'pymntpl-paypal-woocommerce' ),
					'alternative' => __( 'Alternative', 'pymntpl-paypal-woocommerce' ),
					'inline'      => __( 'Inline', 'pymntpl-paypal-woocommerce' ),
					'none'        => __( 'None', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=text'
				],
				'desc_tip'          => true,
				'description'       => __( 'This is the PayPal logo that appears in the Pay Later messaging.', 'pymntpl-paypal-woocommerce' )
			],
			'minicart_logo_position' => [
				'title'             => __( 'Logo Position', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'left',
				'options'           => [
					'left'  => __( 'Left', 'pymntpl-paypal-woocommerce' ),
					'right' => __( 'Right', 'pymntpl-paypal-woocommerce' ),
					'top'   => __( 'Top', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => __( '', 'pymntpl-paypal-woocommerce' ),
				'description'       => __( '', 'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=text'
				]
			],
			'minicart_flex_color'    => [
				'title'             => __( 'Background Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'blue',
				'options'           => [
					'blue'            => __( 'Blue', 'pymntpl-paypal-woocommerce' ),
					'black'           => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'           => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'white-no-border' => __( 'White no border', 'pymntpl-paypal-woocommerce' ),
					'gray'            => __( 'Gray', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'       => __( 'Grayscale', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => true,
				'description'       => __( 'Sets the color of the message background for flex layout messages.',
					'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=flex'
				]
			],
			'minicart_flex_ratio'    => [
				'title'             => __( 'Flex Ratio', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '8x1',
				'options'           => [
					'1x1'  => '1x1',
					'1x4'  => '1x4',
					'8x1'  => '8x1',
					'20x1' => '20x1'
				],
				'custom_attributes' => [
					'data-show-if' => 'cart_layout=flex'
				],
				'desc_tip'          => true,
				'description'       => __( 'The width x height ratio of the flex layout.', 'pymntpl-paypal-woocommerce' )
			],
			'shop_page'              => [
				'type'  => 'title',
				'title' => __( 'Shop Page Settings', 'pymntpl-paypal-woocommerce' )
			],
			'shop_enabled'           => [
				'title'       => __( 'Enabled', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'Enable this option to display the Pay Later messaging on your shop/category page.', 'pymntpl-paypal-woocommerce' )
			],
			'shop_location'          => [
				'title'       => __( 'Location', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'below_price',
				'options'     => [
					'below_price'       => __( 'Below product price', 'pymntpl-paypal-woocommerce' ),
					'below_add_to_cart' => __( 'Below Add to cart button', 'pymntpl-paypal-woocommerce' ),
				],
				'description' => __( 'Location where the Pay Later message appears on the shop/category page', 'pymntpl-paypal-woocommerce' ),
			],
			'shop_layout'            => [
				'title'   => __( 'Layout', 'pymntpl-paypal-woocommerce' ),
				'type'    => 'select',
				'default' => 'text',
				'options' => [
					'text' => __( 'Text', 'pymntpl-paypal-woocommerce' ),
					'flex' => __( 'Flex', 'pymntpl-paypal-woocommerce' )
				]
			],
			'shop_text_color'        => [
				'title'             => __( 'Text Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'black',
				'options'           => [
					'black'      => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'      => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'monochrome' => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'  => __( 'Grayscale', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'shop_layout=text'
				]
			],
			'shop_text_size'         => [
				'title'             => __( 'Text Size', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '12',
				'options'           => [
					'10' => '10',
					'11' => '11',
					'12' => '12',
					'13' => '13',
					'14' => '14',
					'15' => '15',
					'16' => '16'
				],
				'custom_attributes' => [
					'data-show-if' => 'shop_layout=text'
				]
			],
			'shop_logo'              => [
				'title'             => __( 'Logo Type', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'options'           => [
					'primary'     => __( 'Primary', 'pymntpl-paypal-woocommerce' ),
					'alternative' => __( 'Alternative', 'pymntpl-paypal-woocommerce' ),
					'inline'      => __( 'Inline', 'pymntpl-paypal-woocommerce' ),
					'none'        => __( 'None', 'pymntpl-paypal-woocommerce' ),
				],
				'custom_attributes' => [
					'data-show-if' => 'shop_layout=text'
				],
				'desc_tip'          => true,
				'description'       => __( 'This is the PayPal logo that appears in the Pay Later messaging.', 'pymntpl-paypal-woocommerce' )
			],
			'shop_logo_position'     => [
				'title'             => __( 'Logo Position', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'left',
				'options'           => [
					'left'  => __( 'Left', 'pymntpl-paypal-woocommerce' ),
					'right' => __( 'Right', 'pymntpl-paypal-woocommerce' ),
					'top'   => __( 'Top', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => __( '', 'pymntpl-paypal-woocommerce' ),
				'description'       => __( '', 'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'shop_layout=text'
				]
			],
			'shop_flex_color'        => [
				'title'             => __( 'Background Color', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => 'blue',
				'options'           => [
					'blue'            => __( 'Blue', 'pymntpl-paypal-woocommerce' ),
					'black'           => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'           => __( 'White', 'pymntpl-paypal-woocommerce' ),
					'white-no-border' => __( 'White no border', 'pymntpl-paypal-woocommerce' ),
					'gray'            => __( 'Gray', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
					'grayscale'       => __( 'Grayscale', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'          => true,
				'description'       => __( 'Sets the color of the message background for flex layout messages.',
					'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'shop_layout=flex'
				]
			],
			'shop_flex_ratio'        => [
				'title'             => __( 'Flex Ratio', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'default'           => '8x1',
				'options'           => [
					'1x1'  => '1x1',
					'1x4'  => '1x4',
					'8x1'  => '8x1',
					'20x1' => '20x1'
				],
				'custom_attributes' => [
					'data-show-if' => 'shop_layout=flex'
				],
				'desc_tip'          => true,
				'description'       => __( 'The width x height ratio of the flex layout.', 'pymntpl-paypal-woocommerce' )
			],
		];
	}

	public function get_admin_script_dependencies() {
		$this->assets->register_script( 'wc-ppcp-paylater-message-settings', 'build/js/paylater-message-settings.js' );

		return [ 'wc-ppcp-paylater-message-settings' ];
	}

	/**
	 * @param AssetDataApi                                    $data_api
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler $context_handler
	 */
	public function add_script_data( AssetDataApi $data_api, $context_handler ) {
		$data     = [];
		$context  = $context_handler->get_context();
		$currency = get_woocommerce_currency();
		if ( $context_handler->is_order_pay() ) {
			$order = $context_handler->get_order_from_query();
			if ( $order ) {
				$currency = $order->get_currency();
			}
		}
		if ( $context_handler->is_product() ) {
			$settings = new ProductSettings( Utils::get_queried_product_id() );
			$data     = [
				'enabled'          => \in_array( $currency, $this->supported_currencies ) && wc_string_to_bool( $settings->get_option( 'paylater_message_enabled' ) ),
				'checkout'         => $this->get_context_options( 'checkout' ),
				'cart'             => $this->get_context_options( 'cart' ),
				'product'          => $this->get_context_options( 'product' ),
				'shop'             => $this->get_context_options( 'shop' ),
				'checkoutLocation' => $this->get_option( 'checkout_location' ),
				'cartLocation'     => $this->get_option( 'cart_location' )
			];
		} else {
			if ( wc_string_to_bool( $this->get_option( "{$context}_enabled" ) ) ) {
				$data = [
					'enabled'          => \in_array( $currency, $this->supported_currencies ),
					'checkout'         => $this->get_context_options( 'checkout' ),
					'cart'             => $this->get_context_options( 'cart' ),
					'product'          => $this->get_context_options( 'product' ),
					'shop'             => $this->get_context_options( 'shop' ),
					'checkoutLocation' => $this->get_option( 'checkout_location' ),
					'cartLocation'     => $this->get_option( 'cart_location' )
				];
			}
		}
		if ( wc_string_to_bool( $this->get_option( 'minicart_enabled' ) ) ) {
			$data['minicart']         = $this->get_context_options( 'minicart' );
			$data['miniCartLocation'] = $this->get_option( 'minicart_location' );
		}
		if ( $context_handler->is_shop() && $this->is_shop_section_enabled() ) {
			$data['shop']['msgLocation']   = $this->get_option( 'shop_location' );
			$data['shop']['product_types'] = [ 'simple', 'grouped', 'variable' ];
			$data['shop']['products']      = isset( $this->asset_data['products'] ) ? $this->asset_data['products'] : [];
		}
		if ( $data ) {
			$data_api->add( 'payLaterMessage', $data );
		}
	}

	public function get_context_options( $context ) {
		$data = [
			'style' => [
				'layout' => $this->get_option( "{$context}_layout" ),
				'logo'   => [
					'type'     => $this->get_option( "{$context}_logo" ),
					'position' => $this->get_option( "{$context}_logo_position" )
				],
				'text'   => [
					'color' => $this->get_option( "{$context}_text_color" ),
					'size'  => $this->get_option( "{$context}_text_size" ),
				],
				'color'  => $this->get_option( "{$context}_flex_color" ),
				'ratio'  => $this->get_option( "{$context}_flex_ratio" )
			]
		];
		if ( $data['style']['layout'] === 'text' ) {
			unset( $data['style']['color'] );
			unset( $data['style']['ratio'] );
		} else {
			unset( $data['style']['text'] );
			unset( $data['style']['logo'] );
		}

		return apply_filters( 'wc_ppcp_paylater_message_get_context_options', $data, $context, $this );
	}

	public function handle_after_order_total() {
		if ( $this->is_checkout_section_enabled() && 'shop_table' === $this->get_option( 'checkout_location' ) ) {
			?>
            <tr class="wc-ppcp-paylater-msg__container" style="display: none">
                <td colspan="2">
                    <div id="wc-ppcp-paylater-msg-checkout"></div>
                </td>
            </tr>
			<?php
		}
	}

	public function handle_cart_totals() {
		if ( $this->is_cart_section_enabled() && 'shop_table' === $this->get_option( 'cart_location' ) ) {
			?>
            <tr class="wc-ppcp-paylater-msg__container" style="display: none">
                <td colspan="2">
                    <div id="wc-ppcp-paylater-msg-cart"></div>
                </td>
            </tr>
			<?php
		}
	}

	public function render_above_product_price() {
		if ( $this->get_option( 'product_location' ) === 'above_price' ) {
			$this->render_product_messaging();
		}
	}

	public function render_below_product_price() {
		if ( $this->get_option( 'product_location' ) === 'below_price' ) {
			$this->render_product_messaging();
		}
	}

	public function render_after_add_to_cart() {
		if ( $this->get_option( 'product_location' ) === 'below_add_to_cart' ) {
			$this->render_product_messaging();
		}
	}

	public function render_product_messaging() {
		global $product;
		$payment_methods = WC()->payment_gateways()->payment_gateways();
		$payment_method  = isset( $payment_methods['ppcp'] ) ? $payment_methods['ppcp'] : null;
		if ( $product && $payment_method ) {
			$setting = new ProductSettings( $product );
			if ( wc_string_to_bool( $setting->get_option( 'paylater_message_enabled' ) ) ) {
				?>
                <div class="wc-ppcp-paylater-msg__container" style="display: none">
                    <div id="wc-ppcp-paylater-msg-product"></div>
                </div>
				<?php
			}
		}
	}

	public function handle_add_to_cart() {
		$this->render_product_messaging();
	}

	/**
	 * @param array                                           $handles
	 * @param AssetsApi                                       $assets_api
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler $context
	 */
	public function add_script_handles( $handles, $assets_api, $context ) {
		if ( $context->is_product() && $this->is_product_section_enabled() ) {
			$assets_api->register_script( 'wc-ppcp-paylater-msg-product', 'build/js/paylater-message-product.js' );
			$handles[] = 'wc-ppcp-paylater-msg-product';
		} elseif ( $context->is_cart() && $this->is_cart_section_enabled() ) {
			$assets_api->register_script( 'wc-ppcp-paylater-msg-cart', 'build/js/paylater-message-cart.js' );
			$handles[] = 'wc-ppcp-paylater-msg-cart';
		} elseif ( $context->is_checkout() && $this->is_checkout_section_enabled() ) {
			$assets_api->register_script( 'wc-ppcp-paylater-msg-checkout', 'build/js/paylater-message-checkout.js' );
			$handles[] = 'wc-ppcp-paylater-msg-checkout';
		} elseif ( $context->is_shop() && $this->is_shop_section_enabled() ) {
			$assets_api->register_script( 'wc-ppcp-paylater-msg-shop', 'build/js/paylater-message-shop.js' );
			$handles[] = 'wc-ppcp-paylater-msg-shop';
		}

		if ( ! $context->is_cart() && ! $context->is_checkout() && ! $context->is_order_received() ) {
			// if messaging on minicart is enabled
			if ( wc_string_to_bool( $this->get_option( 'minicart_enabled' ) ) ) {
				$assets_api->register_script( 'wc-ppcp-paylater-msg-minicart', 'build/js/paylater-message-minicart.js' );
				$handles[] = 'wc-ppcp-paylater-msg-minicart';
			}
		}

		return $handles;
	}

	public function get_product_form_fields( $fields ) {
		return $fields + [
				'paylater_message_enabled' => [
					'title'   => __( 'Pay Later Message Enabled', 'pymntpl-paypal-woocommerce' ),
					'type'    => 'checkbox',
					'value'   => 'yes',
					'default' => $this->get_option( 'product_enabled' )
				],
			];
	}

	protected function is_checkout_section_enabled() {
		return wc_string_to_bool( $this->get_option( 'checkout_enabled' ) );
	}

	protected function is_cart_section_enabled() {
		return wc_string_to_bool( $this->get_option( 'cart_enabled' ) );
	}

	protected function is_product_section_enabled() {
		$settings = new ProductSettings( Utils::get_queried_product_id() );

		return wc_string_to_bool( $settings->get_option( 'paylater_message_enabled' ) );
	}

	protected function is_shop_section_enabled() {
		return wc_string_to_bool( $this->get_option( 'shop_enabled' ) );
	}

	public function add_shop_loop_data() {
		if ( Main::container()->get( ContextHandler::class )->is_shop() ) {
			if ( $this->is_shop_section_enabled() ) {
				global $product;
				if ( $product && \is_object( $product ) ) {
					$this->asset_data['products'][] = [
						'id'           => $product->get_id(),
						'total'        => wc_get_price_to_display( $product ),
						'product_type' => $product->get_type()
					];
				}
			}
		}
	}

	public function render_shop_message_container() {
		global $product;
		if ( $product && $this->is_shop_section_enabled() ) {
			$location = $this->get_option( 'shop_location' );
			$content  = '<div class="wc-ppcp-paylater-msg-shop-container" id="wc-ppcp-paylater-msg-' . $product->get_id() . '" style="display: none"></div>';
			//phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
            if ( doing_action( 'woocommerce_after_shop_loop_item_title' ) && $location === 'below_price' ) {
				echo $content;
			} elseif ( doing_action( 'woocommerce_after_shop_loop_item' ) && $location === 'below_add_to_cart' ) {
				echo $content;
			}
			//phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

}