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

$_atts['class'] = 'wpb_text_column';
$_atts['class'] .= isset( $classes ) ? $classes : '';

// When some values are set in Design options, add the specific classes
if ( us_design_options_has_property( $css, 'color' ) ) {
	$_atts['class'] .= ' has_text_color';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Add specific classes, when "Show More" is enabled
if ( $show_more_toggle AND ! us_amp() ) {
	$_atts['class'] .= ' with_show_more_toggle';
	$_atts['data-toggle-height'] = $show_more_toggle_height;
}

// Output the element
$output = '<div'. us_implode_atts( $_atts ) .'>';
$output .= '<div class="wpb_wrapper">';
$output .= apply_filters( 'widget_text_content', $content );
$output .= '</div>';

if ( $show_more_toggle AND ! us_amp() ) {
	$output .= '<div class="toggle-links align_' . $show_more_toggle_alignment . '">';
	$output .= '<a href="javascript:void(0)" class="toggle-show-more">' . strip_tags( $show_more_toggle_text_more ) . '</a>';
	$output .= '<a href="javascript:void(0)" class="toggle-show-less">' . strip_tags( $show_more_toggle_text_less ) . '</a>';
	$output .= '</div>';
}

$output .= '</div>';

echo $output;
