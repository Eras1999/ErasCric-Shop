<?php
/**
 * @var \PaymentPlugins\WooCommerce\PPCP\Admin\Settings\ProductSettings[] $settings
 * @var \WC_Product                                                       $product_object
 */

?>
<div id="ppcp_product_data" class="panel woocommerce_options_panel hidden">
    <input type="hidden" name="wc_<?php echo esc_attr( $settings->id ) ?>_options_change" value="no"/>
    <div class="wc-ppcp-options-panel" data-payment-method="<?php echo esc_attr( $settings->id ) ?>">
		<?php $settings->admin_options(); ?>
    </div>
    <div class="options_group">
		<?php woocommerce_wp_select( [
				'id'          => '_ppcp_button_position',
				'value'       => ( ( $position = $product_object->get_meta( '_ppcp_button_position' ) ) ? $position : 'bottom' ),
				'label'       => __( 'Button Position', 'pymntpl-paypal-woocommerce' ),
				'options'     => [
					'bottom' => __( 'Below add to cart', 'pymntpl-paypal-woocommerce' ),
					'top'    => __( 'Above add to cart', 'pymntpl-paypal-woocommerce' ),
				],
				'desc_tip'    => true,
				'description' => __(
					'The location of the payment buttons in relation to the Add to Cart button.',
					'pymntpl-paypal-woocommerce'
				)
			]
		) ?>
    </div>
</div>