<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode attributes
 *
 * @var $el_class
 * @var $css_animation
 * @var $css
 * @var $content - shortcode content
 * @var $show_more_toggle - Hide part of a content with the "Show More" link
 * @var $show_more_toggle_height - Height of visible content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Column_text
 * @var $classes string Extend class names
 */

$classes = isset( $classes ) ? $classes : '';

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$classes .= ' has_text_color';
}
if ( ! empty( $el_class ) ) {
	$classes .= ' ' . $el_class;
}

$text_column_atts = array();

if ( $el_id != '' ) {
	$text_column_atts[ 'id' ] = esc_attr( $el_id );
}

// Add specific classes, when "Show More" is enabled
if ( $show_more_toggle AND ! us_amp() ) {
	$classes .= ' with_show_more_toggle';
	$text_column_atts[ 'data-toggle-height' ] = esc_attr( $show_more_toggle_height );
}

// Output the element
$output = '<div class="wpb_text_column' . $classes . '" '. us_implode_atts( $text_column_atts ) .'>';
$output .= '<div class="wpb_wrapper">' . apply_filters( 'widget_text_content', $content ) . '</div>';
if ( $show_more_toggle AND ! us_amp() ) {
	$output .= '<div class="toggle-links align_' . $show_more_toggle_alignment . '">';
	$output .= '<a href="javascript:void(0)" class="toggle-show-more">' . strip_tags( $show_more_toggle_text_more ) . '</a>';
	$output .= '<a href="javascript:void(0)" class="toggle-show-less">' . strip_tags( $show_more_toggle_text_less ) . '</a>';
	$output .= '</div>';
}
$output .= '</div>';

echo $output;
