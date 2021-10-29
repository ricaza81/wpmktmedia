<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WooCommerce Product gallery
 *
 * $type
 *
 */
global $product;
if ( ! class_exists( 'woocommerce' ) OR ! $product ) {
	return;
}

$classes = isset( $classes ) ? $classes : '';
$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Output the element
echo '<div class="w-post-elm product_gallery' . $classes . '"' . $el_id . '>';
wc_get_template( 'single-product/product-image.php' );
echo '</div>';
