<?php


namespace PaymentPlugins\WooCommerce\PPCP\Traits;

/**
 * Trait Settings
 *
 * @package PaymentPlugins\WooCommerce\PPCP\Traits
 */
trait Settings {

	protected $tab_label;

	protected $tab_label_priority = 10;

	public function get_tab_label() {
		return $this->tab_label;
	}

	public function add_navigation_tab( $tabs ) {
		$tabs[ $this->id ] = [
			'label'    => $this->get_tab_label(),
			'priority' => $this->tab_label_priority
		];

		return $tabs;
	}

	public function admin_options() {
		?>
        <div class="wc-ppcp-settings-container <?php echo esc_attr( $this->id ) ?>">
			<?php
			$this->admin_menu();
			parent::admin_options()
			?>
        </div>
		<?php
	}

	public function admin_menu() {
		global $current_section;
		$tabs = apply_filters( 'wc_ppcp_admin_nav_tabs', [] );
		\uasort( $tabs, function ( $a, $b ) {
			return $a['priority'] - $b['priority'];
		} );
		?>
        <div class="wc-ppcp-settings-logo">
            <img class="paymentplugins-logo" src="<?php echo esc_url( $this->assets->assets_url( 'assets/img/paymentplugins.svg' ) ) ?>"/>
            <span><?php esc_html_e( 'for', 'pymntpl-paypal-woocommerce' ) ?></span>
            <img class="paypal-logo" src="<?php echo esc_url( $this->assets->assets_url( 'assets/img/paypal_logo.svg' ) ) ?>"/>
        </div>
        <div class="wc-ppcp-settings-navigation nav-tab-wrapper">
			<?php foreach ( $tabs as $id => $tab ): ?>
                <a href="admin.php?page=wc-settings&tab=checkout&section=<?php echo esc_attr( $id ) ?>"
                   class="nav-tab <?php if ( $current_section == $id ) { ?>nav-tab-active<?php } ?>">
					<?php echo esc_attr( $tab['label'] ) ?>
                </a>
			<?php endforeach; ?>
        </div>
		<?php
	}

	public function generate_paypal_description_html( $key, $data ) {
		$data = wp_parse_args(
			$data,
			[
				'class'       => '',
				'style'       => '',
				'desc_tip'    => false,
				'description' => '',
			]
		);

		ob_start();
		?>
        <p class="<?php echo esc_attr( $data['class'] ); ?>"><?php echo wp_kses_post( $data['description'] ); ?></p>
		<?php

		return ob_get_clean();
	}

	public function generate_smartbutton_demo_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$value     = $this->get_option( $key, $data['default'] );
		$data      = wp_parse_args( $data, [
			'title'             => '',
			'id'                => 'ppcp_smartbutton',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => [
				'order' => \implode( ',', $value )
			]
		] );
		ob_start();
		?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?><?php echo $this->get_tooltip_html( $data );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <label for="<?php echo esc_attr( $field_key ); ?>">
						<?php echo $this->get_description_html( $data );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <ul id="<?php echo esc_attr( $data['id'] ); ?>" <?php echo $this->get_custom_attribute_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>></ul>
						<?php foreach ( $value as $source ): ?>
                            <input type="hidden" name="woocommerce_ppcp_buttons_order[]" value="<?php echo esc_attr( $source ) ?>"/>
						<?php endforeach; ?>
                </fieldset>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

	public function generate_slider_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = [
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => [],
			'select_buttons'    => false,
			'options'           => [],
		];

		$data  = wp_parse_args( $data, $defaults );
		$value = $this->get_option( $key, $data['default'] );
		ob_start();
		?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?><?php echo $this->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?></label>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <div type="submit" class="wc-ppcp-slider <?php echo esc_attr( $data['class'] ); ?>"
                         style="<?php echo esc_attr( $data['css'] ); ?>"
                         value="<?php echo $field_key; ?>" <?php echo $this->get_custom_attribute_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
                        <span class="wc-ppcp-slider-val"><?php echo esc_html( $value ) ?>px</span>
                    </div>
                    <input type="hidden" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>"
                           value="<?php echo esc_attr( $value ) ?>"/>
					<?php echo $this->get_description_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>
                </fieldset>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

	public function generate_clipboard_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$defaults  = [
			'title'             => '',
			'type'              => 'clipboard',
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'desc_tip'          => false,
			'value'             => '',
			'custom_attributes' => []
		];

		$data  = wp_parse_args( $data, $defaults );
		$value = $this->get_option( $key, $data['default'] );
		ob_start();
		?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?><?php echo $this->get_tooltip_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?></label>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text">
                        <span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <div class="wc-ppcp-clipboard-container"
                         id="<?php echo esc_attr( $field_key ); ?>" <?php echo $this->get_custom_attribute_html( $data );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                        <div data-clipboard="<?php echo esc_attr( $value ) ?>">
							<?php echo esc_html( $value ) ?>
                            <span class="wc-ppcp-clipboard dashicons dashicons-clipboard"></span>
                            <span class="wc-ppcp-clipboard-hover">
                                <?php esc_html_e( 'Click to copy', 'pymntpl-paypal-woocommerce' ) ?>
                            </span>
                        </div>
                    </div>
                </fieldset>
            </td>
        </tr>
		<?php
		return ob_get_clean();
	}

}