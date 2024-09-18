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
                <a href="<?php echo esc_url( $connect_url ) ?>" <?php disabled( $data['disabled'], true ); ?> target="_blank" class="<?php echo esc_attr( $data['class'] ); ?> wc-ppcp__button" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>"
                   value="<?php echo esc_attr($field_key); ?>" <?php echo $this->get_custom_attribute_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-paypal-button="true"><?php echo wp_kses_post( $data['label'] ); ?></a>
            </label><br/>
			<?php echo $this->get_description_html( $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </fieldset>
    </td>
</tr>
