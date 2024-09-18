<?php

namespace PaymentPlugins\PPCP\Elementor\Widget;

use Elementor\Controls_Manager;
use PaymentPlugins\WooCommerce\PPCP\Admin\Settings\PayLaterMessageSettings;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\Payments\PaymentGateways;

class ProductPayLaterMessageWidget extends AbstractWidget {

	/**
	 * @var \PaymentPlugins\WooCommerce\PPCP\Admin\Settings\PayLaterMessageSettings
	 */
	private $settings;

	protected function initialize() {
		parent::initialize();
		$container      = Main::container();
		$this->settings = $container->get( PayLaterMessageSettings::class );
	}

	public function get_name() {
		return 'ppcp_product_paylater_msg';
	}

	public function get_title() {
		return esc_html__( 'PayPal Product Pay Later Messaging', 'pymntpl-paypal-woocommerce' );
	}

	public function get_keywords() {
		return [ 'paypal', 'paylater' ];
	}

	public function get_icon() {
		return 'eicon-paypal-button';
	}

	public function register_controls() {
		$this->start_controls_section( 'paylater_options', [
			'label' => __( 'Pay Later Options', 'pymntpl-paypal-woocommerce' )
		] );
		$this->add_control( 'enabled', [
			'label'   => __( 'Enabled', 'pymntpl-paypal-woocommerce' ),
			'type'    => Controls_Manager::SWITCHER,
			'default' => $this->settings->get_option( 'product_enabled' )
		] );
		$this->add_control( 'layout', [
			'label'   => __( 'Layout', 'pymntpl-paypal-woocommerce' ),
			'type'    => Controls_Manager::SELECT,
			'default' => $this->settings->get_option( 'product_layout' ),
			'options' => [
				'text' => __( 'Text', 'pymntpl-paypal-woocommerce' ),
				'flex' => __( 'Flex', 'pymntpl-paypal-woocommerce' )
			]
		] );
		$this->add_control( 'text_color', [
			'label'   => __( 'Text Color', 'pymntpl-paypal-woocommerce' ),
			'type'    => Controls_Manager::SELECT,
			'default' => $this->settings->get_option( 'product_text_color' ),
			'options' => [
				'black'      => __( 'Black', 'pymntpl-paypal-woocommerce' ),
				'white'      => __( 'White', 'pymntpl-paypal-woocommerce' ),
				'monochrome' => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
				'grayscale'  => __( 'Grayscale', 'pymntpl-paypal-woocommerce' ),
			],
		] );
		$this->add_control( 'text_size', [
			'label'   => __( 'Text Size', 'pymntpl-paypal-woocommerce' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '12',
			'options' => [
				'10' => '10',
				'11' => '11',
				'12' => '12',
				'13' => '13',
				'14' => '14',
				'15' => '15',
				'16' => '16'
			],
		] );
		$this->add_control( 'logo_type', [
			'label'   => __( 'Logo Type', 'pymntpl-paypal-woocommerce' ),
			'type'    => Controls_Manager::SELECT,
			'default' => $this->settings->get_option( 'product_logo' ),
			'options' => [
				'primary'     => __( 'Primary', 'pymntpl-paypal-woocommerce' ),
				'alternative' => __( 'Alternative', 'pymntpl-paypal-woocommerce' ),
				'inline'      => __( 'Inline', 'pymntpl-paypal-woocommerce' ),
				'none'        => __( 'None', 'pymntpl-paypal-woocommerce' ),
			],
		] );
		$this->add_control( 'logo_position', [
			'label'     => __( 'Logo Position', 'pymntpl-paypal-woocommerce' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => $this->settings->get_option( 'product_logo_position' ),
			'options'   => [
				'left'  => __( 'Left', 'pymntpl-paypal-woocommerce' ),
				'right' => __( 'Right', 'pymntpl-paypal-woocommerce' ),
				'top'   => __( 'Top', 'pymntpl-paypal-woocommerce' )
			],
			'condition' => [
				'layout' => 'text'
			]
		] );
		$this->add_control( 'flex_color', [
			'label'     => __( 'Background Color', 'pymntpl-paypal-woocommerce' ),
			'type'      => 'select',
			'default'   => $this->settings->get_option( 'product_flex_color' ),
			'options'   => [
				'blue'            => __( 'Blue', 'pymntpl-paypal-woocommerce' ),
				'black'           => __( 'Black', 'pymntpl-paypal-woocommerce' ),
				'white'           => __( 'White', 'pymntpl-paypal-woocommerce' ),
				'white-no-border' => __( 'White no border', 'pymntpl-paypal-woocommerce' ),
				'gray'            => __( 'Gray', 'pymntpl-paypal-woocommerce' ),
				'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
				'monochrome'      => __( 'Monochrome', 'pymntpl-paypal-woocommerce' ),
				'grayscale'       => __( 'Grayscale', 'pymntpl-paypal-woocommerce' )
			],
			'condition' => [
				'layout' => 'flex'
			]
		] );
		$this->add_control( 'flex_ratio', [
			'label'     => __( 'Flex Ratio', 'pymntpl-paypal-woocommerce' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => $this->settings->get_option( 'product_flex_ratio' ),
			'options'   => [
				'1x1'  => '1x1',
				'1x4'  => '1x4',
				'8x1'  => '8x1',
				'20x1' => '20x1'
			],
			'condition' => [
				'layout' => 'flex'
			]
		] );
		$this->end_controls_section();
	}

	protected function render() {
		$this->render_message_html();
	}

	protected function content_template() {
		$this->render_message_html();
		?>
        <script>
            function renderMessage() {
                debugger;
                var settings = JSON.parse('{{{JSON.stringify(settings)}}}');
                var options = {
                    amount: 100,
                    currency: 'USD',
                    placement: 'product',
                    style: {
                        layout: settings.layout,
                        logo: {
                            type: settings.logo_type,
                            position: settings.logo_position
                        },
                        text: {
                            color: settings.text_color,
                            size: settings.text_size
                        },
                        color: settings.flex_color,
                        ratio: settings.flex_ratio
                    }
                }
                if (options.style.layout === 'text') {
                    delete options.style.color;
                    delete options.style.ratio;
                } else {
                    delete options.style.text;
                    delete options.style.logo;
                }
                var message = paypal.Messages(options);
                message.render(document.getElementById('wc-ppcp-paylater-msg-product'));
            }

            if (!window.paypal) {
                var script = document.createElement('script');
                script.src = '<?php echo esc_url( $this->get_paypal_editor_script() )?>';
                script.onload = renderMessage;
                document.body.appendChild(script);
            } else {
                renderMessage();
            }
        </script>
		<?php
	}

	private function render_message_html() {
		?>
        <div class="wc-ppcp-paylater-msg__container">
            <div id="wc-ppcp-paylater-msg-product"></div>
        </div>
		<?php
	}

	public function get_script_depends() {
		$payment_gateways = Main::container()->get( PaymentGateways::class );
		$this->assets->register_script( 'wc-ppcp-paylater-msg-product', 'build/js/paylater-message-product.js' );
		$handles[] = 'wc-ppcp-paylater-msg-product';
		$payment_gateways->add_scripts( $handles );
		add_action( 'wc_ppcp_add_script_data', [ $this, 'add_script_data' ], 100, 2 );

		return $handles;
	}

	/**
	 * @param \PaymentPlugins\WooCommerce\PPCP\Assets\AssetDataApi $data_api
	 * @param \PaymentPlugins\WooCommerce\PPCP\ContextHandler      $context
	 *
	 * @return void
	 */
	public function add_script_data( $data_api, $context ) {
		if ( $context->is_product() && $this->is_frontend_request() ) {
			$data = $data_api->get( 'payLaterMessage' );
			if ( ! $data ) {
				$data = [];
			}
			$settings        = $this->get_settings_for_display();
			$data['enabled'] = wc_string_to_bool( $settings['enabled'] );
			if ( $data['enabled'] ) {
				$data['product']          = $this->settings->get_context_options( 'product' );
				$data['product']['style'] = array_merge( $data['product']['style'], [
					'layout' => $settings['layout'],
					'logo'   => [
						'type'     => $settings['logo_type'],
						'position' => $settings['logo_position']
					],
					'text'   => [
						'color' => $settings['text_color'],
						'size'  => $settings['text_size']
					],
					'color'  => $settings['flex_color'],
					'ratio'  => $settings['flex_ratio']
				] );
				if ( $data['product']['style']['layout'] === 'text' ) {
					unset( $data['product']['style']['color'] );
					unset( $data['product']['style']['ratio'] );
				} else {
					unset( $data['product']['style']['text'] );
					unset( $data['product']['style']['logo'] );
				}
				$data_api->add( 'payLaterMessage', $data );
			}
		}
	}

}