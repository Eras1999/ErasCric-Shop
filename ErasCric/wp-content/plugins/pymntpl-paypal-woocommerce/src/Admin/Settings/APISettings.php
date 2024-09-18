<?php

namespace PaymentPlugins\WooCommerce\PPCP\Admin\Settings;

use PaymentPlugins\PayPalSDK\PayPalClient;
use PaymentPlugins\WooCommerce\PPCP\Constants;
use PaymentPlugins\WooCommerce\PPCP\Main;
use PaymentPlugins\WooCommerce\PPCP\Utils;
use PaymentPlugins\WooCommerce\PPCP\WPPayPalClient;

/**
 * Class APISettings
 *
 * @package PaymentPlugins\WooCommerce\PPCP\Admin\Settings
 */
class APISettings extends AbstractSettings {

	public $id = 'ppcp_api';

	public function __construct( ...$args ) {
		$this->tab_label = __( 'API Settings', 'pymntpl-paypal-woocommerce' );
		parent::__construct( ...$args );
	}

	public function init_form_fields() {
		$this->form_fields = [
			'title'                     => [
				'type'  => 'title',
				'title' => __( 'API Settings', 'pymntpl-paypal-woocommerce' ),
			],
			'title_description'         => [
				'type'        => 'paypal_description',
				'description' => __( '',
					'pymntpl-paypal-woocommerce' )
			],
			'environment'               => [
				'type'        => 'select',
				'title'       => __( 'Environment', 'pymntpl-paypal-woocommerce' ),
				'class'       => 'wc-enhanced-select',
				'default'     => 'production',
				'options'     => [
					'sandbox'    => __( 'sandbox', 'pymntpl-paypal-woocommerce' ),
					'production' => __( 'production', 'pymntpl-paypal-woocommerce' )
				],
				'desc_tip'    => true,
				'description' => __( 'This option determines whether you are processing real transactions or test transactions.', 'pymntpl-paypal-woocommerce' )
			],
			'connect_sandbox'           => [
				'title'             => __( 'Connect to PayPal', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'paypal_button',
				'class'             => 'button-secondary connect-paypal-account',
				'label'             => __( 'Click to Connect', 'pymntpl-paypal-woocommerce' ),
				'description'       => '',
				'environment'       => 'sandbox',
				'custom_attributes' => [
					'data-show-if' => 'environment=sandbox'
				],
			],
			'connect_production'        => [
				'title'             => __( 'Connect to PayPal', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'paypal_button',
				'class'             => 'button-secondary connect-paypal-account',
				'label'             => __( 'Click to Connect', 'pymntpl-paypal-woocommerce' ),
				'description'       => '',
				'environment'       => 'production',
				'custom_attributes' => [
					'data-show-if' => 'environment=production'
				],
			],
			'create_webhook_sandbox'    => [
				'title'             => __( 'Create Webhook', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'webhook_button',
				'class'             => 'button-secondary create-webhook',
				'label'             => __( 'Create Webhook', 'pymntpl-paypal-woocommerce' ),
				'description'       => '',
				'custom_attributes' => [
					'data-show-if' => 'environment=sandbox'
				],
			],
			'create_webhook_production' => [
				'title'             => __( 'Create Webhook', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'webhook_button',
				'class'             => 'button-secondary create-webhook',
				'label'             => __( 'Create Webhook', 'pymntpl-paypal-woocommerce' ),
				'description'       => '',
				'custom_attributes' => [
					'data-show-if' => 'environment=production'
				],
			],
			'client_id_sandbox'         => [
				'title'             => __( 'Sandbox Client ID', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'text',
				'default'           => '',
				'custom_attributes' => [
					'data-show-if' => 'environment=sandbox'
				],
				'desc_tip'          => true,
				'description'       => __( 'The Client ID is how PayPal identifies your account.', 'pymntpl-paypal-woocommerce' )
			],
			'secret_key_sandbox'        => [
				'title'             => __( 'Sandbox Secret Key', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'password',
				'default'           => '',
				'custom_attributes' => [
					'data-show-if' => 'environment=sandbox'
				],
				'desc_tip'          => true,
				'description'       => __( 'The secret key is how PayPal authenticates your account.', 'pymntpl-paypal-woocommerce' )
			],
			'client_id_production'      => [
				'title'             => __( 'Production Client ID', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'text',
				'default'           => '',
				'custom_attributes' => [
					'data-show-if' => 'environment=production'
				],
				'desc_tip'          => true,
				'description'       => __( 'The Client ID is how PayPal identifies your account.', 'pymntpl-paypal-woocommerce' )
			],
			'secret_key_production'     => [
				'title'             => __( 'Production Secret Key', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'password',
				'default'           => '',
				'custom_attributes' => [
					'data-show-if' => 'environment=production'
				],
				'desc_tip'          => true,
				'description'       => __( 'The secret key is how PayPal authenticates your account.', 'pymntpl-paypal-woocommerce' )
			],
			'webhook_url_sandbox'       => [
				'title'             => __( 'Webhook URL', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'clipboard',
				'default'           => \rest_url( 'wc-ppcp/v1/webhook/sandbox' ),
				'custom_attributes' => [
					'data-show-if' => 'environment=sandbox'
				],
			],
			'webhook_id_sandbox'        => [
				'title'             => __( 'Sandbox Webhook ID', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'text',
				'default'           => '',
				'custom_attributes' => [
					'data-show-if' => 'environment=sandbox'
				],
				'desc_tip'          => true,
				'description'       => __( 'The webhook ID represents the configured webhook in your PayPal account. If the plugin is unable to create the webhook 
				automatically, you can manually create it and enter the ID here.',
					'pymntpl-paypal-woocommerce' )
			],
			'webhook_url_production'    => [
				'title'             => __( 'Webhook URL', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'clipboard',
				'default'           => \rest_url( 'wc-ppcp/v1/webhook/production' ),
				'custom_attributes' => [
					'data-show-if' => 'environment=production'
				],
			],
			'webhook_id_production'     => [
				'title'             => __( 'Production Webhook ID', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'text',
				'default'           => '',
				'custom_attributes' => [
					'data-show-if' => 'environment=production'
				],
				'desc_tip'          => true,
				'description'       => __( 'The webhook ID represents the configured webhook in your PayPal account. If the plugin is unable to create the webhook 
				automatically, you can manually create it and enter the ID here.',
					'pymntpl-paypal-woocommerce' )
			],
			'debug'                     => [
				'title'       => __( 'Debug Enabled', 'pymntpl-paypal-woocommerce' ),
				'type'        => 'checkbox',
				'default'     => 'yes',
				'value'       => 'yes',
				'desc_tip'    => true,
				'description' => __( 'When enabled, valuable debugging information will be captured and stored in the WooCommerce logs.', 'pymntpl-paypal-woocommerce' )
			],
			'debug_payment'             => [
				'title'             => __( 'Debug Payment Process', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'checkbox',
				'default'           => 'no',
				'value'             => 'yes',
				'desc_tip'          => true,
				'description'       => __( 'When enabled, detailed debug data for payments will be added to the log.', 'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'debug=true'
				],
			],
			'debug_webhook'             => [
				'title'             => __( 'Debug Webhook', 'pymntpl-paypal-woocommerce' ),
				'type'              => 'checkbox',
				'default'           => 'no',
				'value'             => 'yes',
				'desc_tip'          => true,
				'description'       => __( 'When enabled, detailed debug data for webhooks will be added to the log.', 'pymntpl-paypal-woocommerce' ),
				'custom_attributes' => [
					'data-show-if' => 'debug=true'
				],
			]
		];
		$environments      = [ 'production', 'sandbox' ];
		foreach ( $environments as $env ) {
			if ( $this->is_connected( $env ) ) {
				$this->form_fields["connect_{$env}"]['description'] = sprintf( '<label class="wc-ppcp-connect__status show_if_%1$s hide_if_%2$s">',
						$env,
						$env == 'production' ?
							'sandbox' : 'production' ) . __( 'Status', 'pymntpl-paypal-woocommerce' ) . ':&nbsp;<span>' . __( 'Connected', 'pymntpl-paypal-woocommerce' ) .
				                                                      '</span><span class="dashicons dashicons-yes"></span></label>';
				$this->form_fields["connect_{$env}"]['label']       = __( 'Delete Connection', 'pymntpl-paypal-woocommerce' );
				$this->form_fields["connect_{$env}"]['class']       = str_replace( 'connect-paypal-account', 'delete-connected-account', $this->form_fields["connect_{$env}"]['class'] );
			}
			if ( ( $id = $this->get_webhook_id( $env ) ) ) {
				$this->form_fields["create_webhook_{$env}"]['description'] = '<label class="wc-ppcp-connect__status">' . __( 'Status', 'pymntpl-paypal-woocommerce' ) . ':&nbsp;<span>'
				                                                             . __( 'Created', 'pymntpl-paypal-woocommerce' ) .
				                                                             '</span><span class="dashicons dashicons-yes"></span></label>';
			}
		}
	}

	public function process_admin_options() {
		parent::process_admin_options();
		/**
		 * @var WPPayPalClient $client
		 */
		$client  = Main::container()->get( PayPalClient::class );
		$changed = false;
		// Wasn't connected, but now is
		foreach ( [ 'production', 'sandbox' ] as $env ) {
			if ( $this->is_connected( $env ) && ! $this->get_option( "access_token_$env" ) ) {
				$client->environment( $env )->refreshAccessToken();
				$changed = true;
			}
		}
		if ( $changed ) {
			$this->init_form_fields();
		}
	}

	public function get_admin_script_dependencies() {
		$this->assets->register_script( 'wc-ppcp-api-settings', 'build/js/api-settings.js' );

		return [ 'wc-ppcp-api-settings' ];
	}

	public function get_settings_script_data() {
		return [
			'messages'  => [
				'connecting'              => __( 'Configuring connection to PayPal...', 'pymntpl-paypal-woocommerce' ),
				'deleteAccount'           => __( 'Delete Connection', 'pymntpl-paypal-woocommerce' ),
				'confirmDeleteConnection' => __( 'Press OK to continue deleting your connection data.', 'pymntpl-paypal-woocommerce' )
			],
			'connected' => [
				'sandbox'    => $this->is_connected( 'sandbox' ),
				'production' => $this->is_connected( 'production' )
			]
		];
	}

	public function admin_options() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['_connect'] ) && $_GET['_connect'] === 'success' ) {
			\WC_Admin_Settings::add_message( __( 'Your PayPal account has been connected.', 'pymntpl-paypal-woocommerce' ) );
		}
		parent::admin_options();
	}

	public function get_environment() {
		return $this->get_option( 'environment' );
	}

	public function get_client_id( $environment = '' ) {
		$environment = ! $environment ? $this->get_environment() : $environment;

		return $this->get_option( "client_id_{$environment}" );
	}

	public function is_connected( $environment = null ) {
		if ( null === $environment ) {
			$environment = $this->get_environment();
		}
		$config = [ $this->get_option( "client_id_$environment" ), $this->get_option( "secret_key_$environment" ) ];

		return count( array_filter( $config ) ) == 2;
	}

	public function get_webhook_id( $environment ) {
		return $this->get_option( "webhook_id_{$environment}", null );
	}

	public function set_webhook_id( $id, $environment ) {
		$this->update_option( "webhook_id_{$environment}", $id );
	}

	public function debug_webhook_enabled() {
		return \wc_string_to_bool( $this->get_option( 'debug_webhook' ) );
	}

	public function debug_payment_enabled() {
		return \wc_string_to_bool( $this->get_option( 'debug_payment' ) );
	}

	private function generate_connect_link( $environment ) {
		if ( ( $url = get_transient( 'wc_ppcp_connection_url_' . $environment ) ) ) {
			return $url;
		}
		$client       = Main::container()->get( PayPalClient::class );
		$sellar_nonce = Utils::random_string();
		$tracking_id  = Utils::random_string( 32 );
		$args         = [
			'environment' => $environment,
			'request'     => [
				'tracking_id'             => $tracking_id,
				'partner_config_override' => [
					'return_url' => add_query_arg( [
						'redirectUrl' => urlencode( add_query_arg( [
							'_connect_nonce' => wp_create_nonce( 'wc-ppcp-connect' ),
							'environment'    => $environment
						], admin_url( 'admin.php?page=wc-settings&tab=checkout&section=ppcp_api' ) ) ),
					], $client->connect->getRedirectUrl() )
				],
				'operations'              => [
					[
						'operation'                  => 'API_INTEGRATION',
						'api_integration_preference' => [
							'rest_api_integration' => [
								'integration_method'  => 'PAYPAL',
								'integration_type'    => 'FIRST_PARTY',
								'first_party_details' => [
									'features'     => [
										'PAYMENT',
										'REFUND',
										'TRACKING_SHIPMENT_READWRITE'
									],
									'seller_nonce' => $sellar_nonce
								],
							],
						]
					]
				],
				'products'                => [
					'EXPRESS_CHECKOUT',
				],
				'legal_consents'          => [
					[
						'type'    => 'SHARE_DATA_CONSENT',
						'granted' => true
					]
				]
			]
		];

		// request the action_url
		$response = $client->connect->createLinks( $args );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$this->update_option( "connect_params_$environment", [
			'sellar_nonce' => $sellar_nonce,
			'merchantId'   => $response->merchantId,
			'trackingId'   => $tracking_id
		] );
		$url = '';
		foreach ( $response->links as $link ) {
			if ( $link->rel === 'action_url' ) {
				$url = add_query_arg( 'displayMode', 'minibrowser', $link->href );
				set_transient( "wc_ppcp_connection_url_$environment", $url, 5 * MINUTE_IN_SECONDS );
				break;
			}
		}

		return $url;
	}

	public function generate_paypal_button_html( $key, $data ) {
		$connect_url = '';
		$field_key   = $this->get_field_key( $key );
		$data        = wp_parse_args(
			$data,
			[
				'title'       => '',
				'class'       => '',
				'style'       => '',
				'description' => '',
				'desc_tip'    => false,
				'id'          => 'wc-ppcp-button_' . $key,
				'disabled'    => false,
				'css'         => '',
				'environment' => 'sandbox'
			]
		);
		if ( ! $this->is_connected( $data['environment'] ) ) {
			$connect_url = $this->generate_connect_link( $data['environment'] );
			if ( is_wp_error( $connect_url ) ) {
				$data['disabled']    = true;
				$connect_url         = '';
				$data['description'] = __( 'The PayPal connect feature is not available at this time. Please manually enter your client ID and secret key.', 'pymntpl-paypal-woocommerce' );
			}
		}
		ob_start();
		include $this->assets->config->get_path( 'src/Admin/Views/html-paypal-button.php' );

		return ob_get_clean();
	}

	public function generate_webhook_button_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$data      = wp_parse_args(
			$data,
			[
				'title'       => '',
				'class'       => '',
				'style'       => '',
				'description' => '',
				'desc_tip'    => false,
				'id'          => 'wc-ppcp-button_' . $key,
				'disabled'    => false,
				'css'         => '',
				'environment' => 'sandbox'
			]
		);
		ob_start();
		?>
        <tr valign="top">
            <th scope="row" class="titledesc"><label
                        for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?><?php echo $this->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <label for="<?php echo esc_attr( $field_key ); ?>">
                        <button <?php disabled( $data['disabled'], true ); ?> target="_blank" class="<?php echo esc_attr( $data['class'] ); ?> wc-ppcp__button" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>"
                                                                              value="<?php echo esc_attr( $field_key ); ?>" <?php echo $this->get_custom_attribute_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo wp_kses_post( $data['label'] ); ?></button>
                    </label><br/>
					<?php echo $this->get_description_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </fieldset>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

}