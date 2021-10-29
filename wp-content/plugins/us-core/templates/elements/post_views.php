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

if ( ! function_exists( 'pvc_get_post_views' ) ) {
	return;
}

global $us_grid_object_type;

if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	return;
} elseif ( $us_elm_context == 'shortcode' AND ( is_tax() OR is_tag() OR is_category() ) ) {
	return;
}

// CSS classes & ID
$classes = isset( $classes ) ? $classes : '';
$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Text before value
$text_before = ( trim( $text_before ) != '' ) ? '<span class="w-post-elm-before">' . trim( $text_before ) . ' </span>' : '';

// Text after value
$text_after = ( trim( $text_after ) != '' ) ? '<span class="w-post-elm-after"> ' . trim( $text_after ) . '</span>' : '';

// Get the value
$value = pvc_get_post_views();
$value = intval( $value );
if ( $result_thousand_short AND $value > 999 ) {
	$value = number_format( floor( $value / 1000 ), 0, '', $result_thousand_separator );
	$value .= 'K';
} else {
	$value = number_format( $value, 0, '', $result_thousand_separator );
}

// Output the element
$output = '<div class="w-post-elm post_views' . $classes . '"' . $el_id . '>';
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
