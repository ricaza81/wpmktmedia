<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Vertical Wrapper
 */

$_atts['class'] = 'w-vwrapper';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' align_' . $alignment;
$_atts['class'] .= ' valign_' . $valign;

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}
if ( trim( $inner_items_gap ) != '0.7rem' ) {
	$_atts['style'] = '--vwrapper-gap:' . $inner_items_gap;
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= do_shortcode( $content );
$output .= '</div>';

echo $output;
