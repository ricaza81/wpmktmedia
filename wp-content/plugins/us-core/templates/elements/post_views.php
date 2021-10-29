<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Post Views Counter
 *
 * @var $us_elm_context string Item context
 * @var $classes string Custom classes
 * @var $hide_empty bool Hide this element if its value is empty
 * @var $text_before string Text before value output
 * @var $text_after string Text after value output
 * @var $el_id string Item Id
 * @var $result_format bool Use "K" shorthand for thousands
 * @var $result_format_separator string Thousand separator
 * @var $icon string Icon
 *
 */

// Determines the called shortcode for the US Builder page
$is_builder_preview_page = apply_filters( 'usb_is_preview_page', NULL );

if ( ! function_exists( 'pvc_get_post_views' ) AND ! $is_builder_preview_page ) {
	return;
}

global $us_grid_object_type;

if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	return;
} elseif ( $us_elm_context == 'shortcode' AND ( is_tax() OR is_tag() OR is_category() ) ) {
	return;
}

$_atts['class'] = 'w-post-elm post_views';
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

// Text before value
$text_before = ( trim( $text_before ) != '' ) ? '<span class="w-post-elm-before">' . trim( $text_before ) . ' </span>' : '';

// Text after value
$text_after = ( trim( $text_after ) != '' ) ? '<span class="w-post-elm-after"> ' . trim( $text_after ) . '</span>' : '';

// Get the value
$value = ! $is_builder_preview_page
	? pvc_get_post_views()
	: 0;
$value = (int) $value;
if ( $result_thousand_short AND $value > 999 ) {
	$value = number_format( floor( $value / 1000 ), 0, '', $result_thousand_separator );
	$value .= 'K';
} else {
	$value = number_format( $value, 0, '', $result_thousand_separator );
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}
if ( $text_before ) {
	$output .= $text_before;
}
$output .= $value;
if ( $text_after ) {
	$output .= $text_after;
}
$output .= '</div>';

echo $output;
