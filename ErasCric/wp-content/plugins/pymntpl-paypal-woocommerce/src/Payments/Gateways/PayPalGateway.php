<?php


namespace PaymentPlugins\WooCommerce\PPCP\Payments\Gateways;

use PaymentPlugins\WooCommerce\PPCP\PayPalQueryParams;
use PaymentPlugins\WooCommerce\PPCP\ProductSettings;
use PaymentPlugins\WooCommerce\PPCP\Tokens\PayPalToken;
use PaymentPlugins\WooCommerce\PPCP\Utils;

/**
 * Class PayPal
 *
 * @package PaymentPlugins\WooCommerce\PPCP\Payments\Gateways
 */
class PayPalGateway extends AbstractGateway {

	public $id = 'ppcp';

	protected $template = 'paypal.php';

	protected $token_class = PayPalToken::class;

	protected $tab_label_priority = 30;

	public function __construct( ...$args ) {
		parent::__construct( ...$args );
		$this->method_title       = __( 'PayPal Gateway By Payment Plugins', 'pymntpl-paypal-woocommerce' );
		$this->tab_label          = __( 'PayPal Settings', 'pymntpl-paypal-woocommerce' );
		$this->icon               = $this->assets->assets_url( 'assets/img/paypal_logo.svg' );
		$this->method_description = __( 'Offer PayPal, PayLater, Venmo, and Credit Cards', 'pymntpl-paypal-woocommerce' );
		$this->order_button_text  = $this->get_option( 'order_button_text' );
	}

	protected function init_hooks() {
		parent::init_hooks();
		add_action( 'wc_ppcp_paypal_query_params', [ $this, 'add_query_params' ], 10, 2 );
		add_action( 'wc_ppcp_product_form_fields', [ $this, 'get_product_form_fields' ] );
	}

	public function init_form_fields() {
		$this->form_fields = [
			'enabled'                       => [
				'title'       => __( 'Enabled', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'Enable this option to offer PayPal on your site.', 'pymntpl-paypal-woocommerce' )
			],
			'title_text'                    => [
				'title'       => __( 'Title', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'text',
				'default'     => __( 'PayPal', 'pymntpl-paypal-woocommerce' ),
				'desc_tip'    => true,
				'description' => __( 'This is the title of the payment gateway which appears on the checkout page.', 'pymntpl-paypal-woocommerce' )
			],
			'description'                   => [
				'title'       => __( 'Description', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'text',
				'default'     => '',
				'desc_tip'    => true,
				'description' => __( 'This is the description that appears when the payment gateway is selected on the checkout page.', 'pymntpl-paypal-woocommerce' )
			],
			'intent'                        => [
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'title'       => __( 'Transaction Type', 'pymntpl-paypal-woocommerce' ),
				'default'     => 'capture',
				'options'     => [
					'capture'   => __( 'Capture', 'pymntpl-paypal-woocommerce' ),
					'authorize' => __( 'Authorize', 'pymntpl-paypal-woocommerce' ),
				],
				'desc_tip'    => true,
				'description' => __(
					'If set to capture, funds will be captured immediately during checkout. Authorized transactions put a hold on the customer\'s funds but
						no payment is taken until the charge is captured. Authorized charges can be captured on the Admin Order page.',
					'pymntpl-paypal-woocommerce'
				),
			],
			'authorize_status'              => [
				'type'              => 'select',
				'class'             => 'wc-enhanced-select',
				'title'             => __( 'Authorized Order Status', 'pymntpl-paypal-woocommerce' ),
				'default'           => 'wc-on-hold',
				'options'           => function_exists( 'wc_get_order_statuses' )
					? wc_get_order_statuses()
					: [
						'wc-pending'    => _x( 'Pending payment', 'Order status', 'woocommerce' ),
						'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
						'wc-on-hold'    => _x( 'On hold', 'Order status', 'woocommerce' ),
						'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
						'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
						'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
						'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
					],
				'custom_attributes' => [
					'data-show-if' => 'intent=authorize'
				],
				'desc_tip'          => true,
				'description'       => __( 'If the transaction is authorized, this is the status applied to the order.', 'pymntpl-paypal-woocommerce' )
			],
			'billing_agreement_description' => [
				'title'       => __( 'Billing Agreement Description', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'text',
				'default'     => __( 'Billing Agreement', 'pymntpl-paypal-woocommerce' ),
				'desc_tip'    => true,
				'description' => __( 'This is the billing agreement description that appears in the PayPal popup.', 'pymntpl-paypal-woocommerce' )
			],
			'sections'                      => [
				'title'             => __( 'PayPal Payment Sections', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'default'           => [ 'cart', 'checkout', 'order_pay' ],
				'options'           => $this->get_payment_section_options( 'paypal' ),
				'sanitize_callback' => function ( $value ) {
					return ! is_array( $value ) ? [] : $value;
				},
				'desc_tip'          => true,
				'description'       => __( 'These are the sections that the PayPal payment button will appear. If PayPal is enabled, the button will show on the checkout page by default.', 'pymntpl-paypal-woocommerce' )
			],
			'payment_format'                => [
				'title'       => __( 'Payment Method Format', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'select',
				'default'     => 'name_email',
				'options'     => wp_list_pluck( $this->get_payment_method_token_instance()->get_payment_method_formats(), 'example' ),
				'desc_tip'    => true,
				'description' => __( 'This option controls how the PayPal payment method appears on the frontend.', 'pymntpl-paypal-woocommerce' )
			],
			'checkout_placement'            => [
				'title'             => __( 'Checkout page Button Placement', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'select',
				'options'           => [
					'place_order'    => __( 'Place Order Button', 'pymntpl-paypal-woocommerce' ),
					'payment_method' => __( 'In payment gateway section', 'pymntpl-paypal-woocommerce' )
				],
				'default'           => 'place_order',
				'desc_tip'          => true,
				'description'       => __( 'You can choose to render the PayPal button in either the payment method section of the checkout page or where the Place Order button is rendered.', 'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'use_place_order=false'
				]
			],
			'show_popup_icon'               => [
				'title'             => __( 'Show Popup Icon and Text', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'checkbox',
				'value'             => 'yes',
				'default'           => 'yes',
				'description'       => __( 'If enabled, the PayPal payment gateway will render a popup icon indicating to the customer that they must click the PayPal button.', 'pymntpl-paypal-woocommerce' ),
				'desc_tip'          => true,
				'custom_attributes' => [
					'data-show-if' => 'checkout_placement=place_order'
				],
			],
			'use_place_order'               => [
				'title'       => __( 'Use Place Order Button', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'If enabled, the plugin will use the Place Order button on the checkout page rather than rendering the PayPal buttons. This option does not affect express checkout.',
					'pymntpl-paypal-woocommerce' )
			],
			'order_button_text'             => [
				'title'             => __( 'Button Text', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'text',
				'default'           => esc_html__( 'Pay with PayPal', 'pymntpl-paypal-woocommerce' ),
				'desc_tip'          => true,
				'description'       => __( 'The text for the Place Order button when PayPal is selected. Leave blank to use the default WooCommerce text.',
					'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'use_place_order=true'
				],

			],
			'button_options'                => [
				'type'  => 'title',
				'title' => __( 'PayPal Button Design', 'pymntpl-paypal-woocommerce' )
			],
			'paypal_button_color'           => [
				'type'    => 'select',
				'title'   => __( 'Button Color', 'pymntpl-paypal-woocommerce' ),
				'class'   => 'wc-ppcp-smartbutton-option wc-enhanced-select',
				'default' => 'gold',
				'options' => [
					'gold'   => __( 'Gold', 'pymntpl-paypal-woocommerce' ),
					'blue'   => __( 'Blue', 'pymntpl-paypal-woocommerce' ),
					'silver' => __( 'Silver', 'pymntpl-paypal-woocommerce' ),
					'black'  => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'  => __( 'White', 'pymntpl-paypal-woocommerce' )
				],
			],
			'button_shape'                  => [
				'type'    => 'select',
				'title'   => __( 'Button Shape', 'pymntpl-paypal-woocommerce' ),
				'class'   => 'wc-ppcp-smartbutton-option wc-enhanced-select',
				'default' => 'rect',
				'options' => [
					'pill' => __( 'Pill', 'pymntpl-paypal-woocommerce' ),
					'rect' => __( 'Rectangle', 'pymntpl-paypal-woocommerce' ),
				],
			],
			'button_label'                  => [
				'type'    => 'select',
				'title'   => __( 'Button Label', 'pymntpl-paypal-woocommerce' ),
				'class'   => 'wc-ppcp-smartbutton-option wc-enhanced-select',
				'default' => 'paypal',
				'options' => [
					'paypal'   => __( 'Standard', 'pymntpl-paypal-woocommerce' ),
					'checkout' => __( 'Checkout', 'pymntpl-paypal-woocommerce' ),
					'buynow'   => __( 'Buy Now', 'pymntpl-paypal-woocommerce' ),
					'pay'      => __( 'Pay', 'pymntpl-paypal-woocommerce' )
				],
			],
			'button_height'                 => [
				'type'              => 'slider',
				'title'             => __( 'Button Height', 'pymntpl-paypal-woocommerce' ),
				'default'           => 40,
				'custom_attributes' => [
					'data-height-min'  => 25,
					'data-height-max'  => 55,
					'data-height-step' => 1,
				]
			],
			'buttons_order'                 => [
				'type'              => 'smartbutton_demo',
				'title'             => __( 'Demo', 'pymntpl-paypal-woocommerce' ),
				'id'                => 'ppcp_button_demo',
				'default'           => [ 'paypal', 'paylater', 'venmo', 'card' ],
				'description'       => __( 'Enabled payment methods are listed below and can be sorted to control their display order on the frontend.', 'pymntpl-paypal-woocommerce' ),
				'sanitize_callback' => function ( $value ) {
					return ! is_array( $value ) ? [] : $value;
				},
			],
			'venmo_title'                   => [
				'type'  => 'title',
				'title' => __( 'Venmo Options', 'pymntpl-paypal-woocommerce' )
			],
			'venmo_enabled'                 => [
				'type'        => 'checkbox',
				'title'       => __( 'Venmo', 'pymntpl-paypal-woocommerce' ),
				'class'       => 'wc-ppcp-smartbutton-option',
				'value'       => 'yes',
				'default'     => 'no',
				'desc_tip'    => false,
				'description' => __( 'If enabled, you will be able to offer Venmo. Venmo is a mobile payment method and the Venmo app must be installed on the customer\'s device in order to show.', 'pymntpl-paypal-woocommerce' )

			],
			'venmo_sections'                => [
				'title'             => __( 'Venmo Payment Sections', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'default'           => [ 'checkout', 'order_pay' ],
				'options'           => $this->get_payment_section_options( 'venmo' ),
				'custom_attributes' => [
					'data-show-if' => 'venmo_enabled=true'
				],
				'sanitize_callback' => function ( $value ) {
					return ! is_array( $value ) ? [] : $value;
				},
				'desc_tip'          => true,
				'description'       => __( 'These are the sections that the Venmo payment button will appear.', 'pymntpl-paypal-woocommerce' )
			],
			'paylater_title'                => [
				'type'  => 'title',
				'title' => __( 'Pay Later Options', 'pymntpl-paypal-woocommerce' )
			],
			'paylater_enabled'              => [
				'type'        => 'checkbox',
				'title'       => __( 'Pay Later', 'pymntpl-paypal-woocommerce' ),
				'class'       => 'wc-ppcp-smartbutton-option',
				'value'       => 'yes',
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'If enabled, you will be able to offer PayPal\s Pay Later option.', 'pymntpl-paypal-woocommerce' )
			],
			'paylater_button_color'         => [
				'type'              => 'select',
				'title'             => __( 'Pay Later Button Color', 'pymntpl-paypal-woocommerce' ),
				'class'             => 'wc-ppcp-smartbutton-option wc-enhanced-select',
				'custom_attributes' => [
					'data-show-if' => 'paylater_enabled=true'
				],
				'default'           => 'gold',
				'options'           => [
					'gold'   => __( 'Gold', 'pymntpl-paypal-woocommerce' ),
					'blue'   => __( 'Blue', 'pymntpl-paypal-woocommerce' ),
					'silver' => __( 'Silver', 'pymntpl-paypal-woocommerce' ),
					'black'  => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white'  => __( 'White', 'pymntpl-paypal-woocommerce' )
				],
			],
			'paylater_sections'             => [
				'title'             => __( 'Pay Later Payment Sections', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'default'           => [ 'cart', 'checkout', 'order_pay' ],
				'options'           => $this->get_payment_section_options( 'paylater' ),
				'custom_attributes' => [
					'data-show-if' => 'paylater_enabled=true'
				],
				'sanitize_callback' => function ( $value ) {
					return ! is_array( $value ) ? [] : $value;
				},
				'desc_tip'          => true,
				'description'       => __( 'These are the sections that the PayPal payment buttons will appear.', 'pymntpl-paypal-woocommerce' )
			],
			'credit_card_title'             => [
				'type'  => 'title',
				'title' => __( 'Credit Card Options' )
			],
			'card_enabled'                  => [
				'type'        => 'checkbox',
				'title'       => __( 'Card Enabled', 'pymntpl-paypal-woocommerce' ),
				'value'       => 'yes',
				'default'     => 'no',
				'class'       => 'wc-ppcp-smartbutton-option',
				'desc_tip'    => true,
				'description' => __( 'If enabled, the card button will be included.', 'pymntpl-paypal-woocommerce' )
			],
			'card_button_color'             => [
				'type'              => 'select',
				'title'             => __( 'Card Button Color', 'pymntpl-paypal-woocommerce' ),
				'class'             => 'wc-ppcp-smartbutton-option wc-enhanced-select',
				'default'           => 'gold',
				'custom_attributes' => [
					'data-show-if' => 'card_enabled=true'
				],
				'options'           => [
					'black' => __( 'Black', 'pymntpl-paypal-woocommerce' ),
					'white' => __( 'White', 'pymntpl-paypal-woocommerce' )
				],
			],
			'card_tagline_enabled'          => [
				'type'              => 'checkbox',
				'title'             => __( 'Tagline enabled', 'pymntpl-paypal-woocommerce' ),
				'class'             => 'wc-ppcp-smartbutton-option',
				'default'           => 'no',
				'value'             => 'yes',
				'desc_tip'          => true,
				'description'       => __( 'If enabled, the card button tagline will be rendered beneath the button.', 'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'card_enabled=true'
				],
			],
			'credit_card_sections'          => [
				'title'             => __( 'Credit Card Payment Sections', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'default'           => [ 'cart', 'checkout', 'order_pay' ],
				'options'           => $this->get_payment_section_options( 'card' ),
				'custom_attributes' => [
					'data-show-if' => 'card_enabled=true'
				],
				'sanitize_callback' => function ( $value ) {
					return ! is_array( $value ) ? [] : $value;
				},
				'desc_tip'          => true,
				'description'       => __( 'These are the sections that the PayPal payment buttons will appear.', 'pymntpl-paypal-woocommerce' )
			]
		];
	}

	public function get_admin_script_dependencies() {
		$src = add_query_arg( [
			'client-id'      => 'sb',
			'components'     => 'buttons',
			'enable-funding' => 'paylater,venmo'
		], 'https://www.paypal.com/sdk/js' );
		wp_register_script( 'wc-ppcp-smartbuttons', $src, [], null, true );
		$this->assets->register_script( 'wc-ppcp-settings', 'build/js/paypal-settings.js', [
			'jquery-ui-sortable',
			'jquery-ui-widget',
			'jquery-ui-core'
		] );

		return [ 'wc-ppcp-settings', 'wc-ppcp-smartbuttons', 'jquery-ui-slider' ];
	}

	public function get_checkout_script_handles() {
		if ( ! $this->is_place_order_button() ) {
			$this->assets->register_script( 'wc-ppcp-checkout-gateway', 'build/js/paypal-checkout.js' );

			return [ 'wc-ppcp-checkout-gateway' ];
		}

		return [];
	}

	public function get_express_checkout_script_handles() {
		$this->assets->register_script( 'wc-ppcp-checkout-express', 'build/js/paypal-express-checkout.js' );

		return [ 'wc-ppcp-checkout-express' ];
	}

	public function get_cart_script_handles() {
		$this->assets->register_script( 'wc-ppcp-cart-gateway', 'build/js/paypal-cart.js' );

		return [ 'wc-ppcp-cart-gateway' ];
	}

	public function get_minicart_script_handles() {
		$this->assets->register_script( 'wc-ppcp-minicart-gateway', 'build/js/paypal-minicart.js' );

		return [ 'wc-ppcp-minicart-gateway' ];
	}

	public function get_product_script_handles() {
		$this->assets->register_script( 'wc-ppcp-product-gateway', 'build/js/paypal-product.js' );

		return [ 'wc-ppcp-product-gateway' ];
	}

	public function express_checkout_fields() {
		?>
        <div id="wc-ppcp-express-button"></div>
		<?php
	}

	public function get_funding_types() {
		return [ 'paypal', 'paylater', 'card', 'venmo' ];
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler $context
	 *
	 * @return array
	 */
	public function get_payment_method_data( $context ) {
		$data = [
			'funding'              => array_values( array_filter( $this->get_funding_types(), function ( $source ) {
				if ( $source === 'paypal' ) {
					return true;
				}

				return wc_string_to_bool( $this->get_option( "{$source}_enabled" ) );
			} ) ),
			'buttons_order'        => $this->get_option( 'buttons_order' ),
			'buttonPlacement'      => $this->get_option( 'checkout_placement' ),
			'buttons'              => [
				'paypal'   => [
					'layout' => 'vertical',
					'label'  => $this->get_option( 'button_label' ),
					'shape'  => $this->get_option( 'button_shape' ),
					'height' => (int) $this->get_option( 'button_height' ),
					'color'  => $this->get_option( 'paypal_button_color' )
				],
				'paylater' => [
					'layout' => 'vertical',
					'label'  => $this->get_option( 'button_label' ),
					'shape'  => $this->get_option( 'button_shape' ),
					'height' => (int) $this->get_option( 'button_height' ),
					'color'  => $this->get_option( 'paylater_button_color' )
				],
				'card'     => [
					'layout'  => 'vertical',
					'label'   => $this->get_option( 'button_label' ),
					'shape'   => $this->get_option( 'button_shape' ),
					'height'  => (int) $this->get_option( 'button_height' ),
					'color'   => $this->get_option( 'card_button_color' ),
					'tagline' => wc_string_to_bool( $this->get_option( 'card_tagline_enabled' ) )
				],
				'venmo'    => [
					'layout' => 'vertical',
					'shape'  => $this->get_option( 'button_shape' ),
					'height' => (int) $this->get_option( 'button_height' )
				]
			],
			'paypal_sections'      => array_merge( $this->get_option( 'sections', [] ), [ 'checkout' ] ),
			'paylater_sections'    => $this->get_option( 'paylater_sections', [] ),
			'credit_card_sections' => $this->get_option( 'credit_card_sections', [] ),
			'venmo_sections'       => $this->get_option( 'venmo_sections', [] ),
			'placeOrderEnabled'    => $this->is_place_order_button()
		];
		if ( $context->is_product() ) {
			$settings                        = new ProductSettings( Utils::get_queried_product_id() );
			$data['funding']                 = array_values( array_filter( [ 'paypal', 'paylater', 'card' ], function ( $type ) use ( $settings ) {
				return wc_string_to_bool( $settings->get_option( "{$type}_enabled" ) );
			} ) );
			$data['product']['button_width'] = $settings->get_option( 'width' );
		} elseif ( $context->is_order_pay() ) {
			$data['paypal_sections'] = array_merge( $data['paypal_sections'], [ 'order_pay' ] );
		}

		return $data;
	}

	public function is_product_section_enabled( $product ) {
		$setting = new ProductSettings( $product );
		$values  = [
			wc_string_to_bool( $setting->get_option( 'paypal_enabled' ) ),
			wc_string_to_bool( $setting->get_option( 'paylater_enabled' ) ),
			wc_string_to_bool( $setting->get_option( 'card_enabled' ) )
		];

		return count( array_filter( $values ) ) > 0;
	}

	public function get_product_form_fields( $fields ) {
		return $fields + [
				'paypal_enabled'   => [
					'title'   => __( 'PayPal Enabled', 'pymntpl-paypal-woocommerce' ),
					'type'    => 'checkbox',
					'default' => in_array( 'product', $this->get_option( 'sections' ) ) ? 'yes' : 'no'
				],
				'intent'           => [
					'type'        => 'select',
					'class'       => 'wc-enhanced-select',
					'title'       => __( 'Transaction Type', 'pymntpl-paypal-woocommerce' ),
					'default'     => $this->get_option( 'intent' ),
					'value'       => 'yes',
					'options'     => [
						'capture'   => __( 'Capture', 'pymntpl-paypal-woocommerce' ),
						'authorize' => __( 'Authorize', 'pymntpl-paypal-woocommerce' ),
					],
					'desc_tip'    => true,
					'description' => __(
						'If set to capture, funds will be captured immediately during checkout. Authorized transactions put a hold on the customer\'s funds but
						no payment is taken until the charge is captured. Authorized charges can be captured on the Admin Order page.',
						'pymntpl-paypal-woocommerce' )
				],
				'paylater_enabled' => [
					'title'   => __( 'Pay Later Enabled', 'pymntpl-paypal-woocommerce' ),
					'type'    => 'checkbox',
					'value'   => 'yes',
					'default' => wc_string_to_bool( $this->get_option( 'paylater_enabled' ) ) && in_array( 'product', $this->get_option( 'paylater_sections', [] ) ) ? 'yes' : 'no'
				],
				'card_enabled'     => [
					'title'   => __( 'Credit Card Enabled', 'pymntpl-paypal-woocommerce' ),
					'type'    => 'checkbox',
					'value'   => 'yes',
					'default' => wc_string_to_bool( $this->get_option( 'card_enabled' ) ) && in_array( 'product', $this->get_option( 'credit_card_sections', [] ) ) ? 'yes' : 'no'
				],
				'width'            => [
					'title'       => __( 'Button Width', 'pymntpl-paypal-woocommerce' ),
					'type'        => 'select',
					'default'     => 'add_to_cart',
					'options'     => [
						'add_to_cart' => __( 'Match add to cart button width', 'pymntpl-paypal-woocommerce' ),
						'full_width'  => __( 'Match width of container', 'pymntpl-paypal-woocommerce' )
					],
					'desc_tip'    => true,
					'description' => __( 'This option allows you to control the width of the payment buttons.', 'pymntpl-paypal-woocommerce' )
				]
			];
	}

	public function validate_fields() {
		/*if ( ! isset( $_POST[ $this->id . '_paypal_order_id' ] ) ) {
			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( sprintf( __( 'There was an error processing your payment. Reason: %s', 'pymntpl-paypal-woocommerce' ), 'A PayPal order ID was not provided.' ), 'error' );
			}
		}*/
	}

	protected function get_payment_section_options( $type = 'paypal' ) {
		$options = [
			'product'          => __( 'Product Page', 'pymntpl-paypal-woocommerce' ),
			'cart'             => __( 'Cart Page', 'pymntpl-paypal-woocommerce' ),
			'checkout'         => __( 'Checkout Page', 'pymntpl-paypal-woocommerce' ),
			'express_checkout' => __( 'Express Checkout', 'pymntpl-paypal-woocommerce' ),
			'order_pay'        => __( 'Order Pay', 'pymntpl-paypal-woocommerce' ),
			'minicart'         => __( 'Minicart', 'pymntpl-paypal-woocommerce' )
		];
		if ( $type === 'paypal' ) {
			// If PayPal is enabled, it' always enabled on checkout
			unset( $options['checkout'] );
		} elseif ( $type === 'venmo' ) {
			unset( $options['product'], $options['cart'] );
		}

		return apply_filters( 'wc_ppcp_get_payment_section_options', $options, $type, $this );
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\PayPalQueryParams $query_params
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler    $context
	 */
	public function add_query_params( PayPalQueryParams $query_params, $context ) {
		if ( $query_params->flow === 'checkout' ) {
			if ( $context->is_product() ) {
				$setting              = new ProductSettings( Utils::get_queried_product_id() );
				$query_params->intent = $setting->get_option( 'intent' );
			} else {
				$query_params->intent = $this->get_option( 'intent' );
				$query_params->vault  = 'false';
				if ( wc_string_to_bool( $this->get_option( "venmo_enabled" ) ) ) {
					$query_params->add_enabled_funding( 'venmo' );
				}
			}
		}
	}

	/**
	 * @since 1.0.19
	 * @return bool
	 */
	public function is_show_popup_icon_enabled() {
		return ( $this->get_option( 'checkout_placement', 'place_order' ) === 'place_order'
		         || $this->is_place_order_button() )
		       && wc_string_to_bool( $this->get_option( 'show_popup_icon', 'yes' ) );
	}

	/**
	 * @since 1.0.38
	 * @return bool
	 */
	public function is_place_order_button() {
		return \wc_string_to_bool( $this->get_option( 'use_place_order', 'no' ) );
	}

	public function get_order_button_text() {
		$text = $this->order_button_text;
		if ( ! $text ) {
			$text = apply_filters( 'woocommerce_order_button_text', __( 'Place order', 'pymntpl-paypal-woocommerce' ) );
		}

		return $text;
	}

}