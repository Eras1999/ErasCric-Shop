<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Google Tag Manager for WordPress
 *
 * @version 1.10
 *
 * @slug duracelltomi-google-tag-manager
 *
 * @url https://wordpress.org/plugins/duracelltomi-google-tag-manager/
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "duracelltomi-google-tag-manager";

$name = "Google Tag Manager for WordPress";

$available = defined( 'GTM4WP_PATH' );

$tinvwl_integrations = is_array( $tinvwl_integrations ) ? $tinvwl_integrations : [];

$tinvwl_integrations[ $slug ] = array(
	'name'      => $name,
	'available' => $available,
);

if ( ! tinv_get_option( 'integrations', $slug ) ) {
	return;
}

if ( ! $available ) {
	return;
}

// Google Tag Manager for WordPress compatibility.
if ( ! function_exists( 'tinvwl_wishlist_item_meta_post_gtm4wp' ) ) {

	/**
	 * Set description for meta for WooCommerce - Google Tag Manager for WordPress
	 *
	 * @param array $meta Meta array.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return array
	 */
	function tinvwl_wishlist_item_meta_post_gtm4wp( $item_data, $product_id, $variation_id ) {

		foreach ( array_keys( $item_data ) as $key ) {
			if ( strpos( $key, 'gtm4wp_' ) === 0 ) {
				unset( $item_data[ $key ] );
			}
		}

		return $item_data;
	}

	add_filter( 'tinvwl_wishlist_item_meta_post', 'tinvwl_wishlist_item_meta_post_gtm4wp', 10, 3 );
}
