<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * WooCommerce Product ordering
 */
if ( ! class_exists( 'woocommerce' ) ) {
	return;
}

$_atts['class'] = 'w-post-elm product_ordering';
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Output the element
echo '<div' . us_implode_atts( $_atts ) . '>';
if ( function_exists( 'woocommerce_catalog_ordering' ) ) {
	woocommerce_catalog_ordering();
}
echo '</div>';
