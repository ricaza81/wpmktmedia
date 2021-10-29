<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Horizontal Wrapper
 */

$_atts['class'] = 'w-hwrapper';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' align_' . $alignment;
$_atts['class'] .= ' valign_' . $valign;
$_atts['class'] .= ( $wrap ) ? ' wrap' : '';

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

if ( trim( $inner_items_gap ) != '1.2rem' ) {
	$_atts['style'] = '--hwrapper-gap:' . $inner_items_gap;
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= do_shortcode( $content );
$output .= '</div>';

echo $output;
