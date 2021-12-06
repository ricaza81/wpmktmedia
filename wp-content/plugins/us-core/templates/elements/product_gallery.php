<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WooCommerce Product gallery
 *
 * $type
 *
 */

global $product;
if (
	( ! class_exists( 'woocommerce' ) OR ! $product )
	AND ! usb_is_preview_page()
) {
	return;
}

$_atts['class'] = 'w-post-elm product_gallery';
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

// Output the element
echo '<div' . us_implode_atts( $_atts ) . '>';

// In Live Builder for Page Block / Content template show a placeholder
if ( usb_is_preview_page_for_template() ) {
	echo us_get_img_placeholder();
} else {
	wc_get_template( 'single-product/product-image.php' );
}

echo '</div>';
