<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_column
 *
 * Overloaded by UpSolution custom implementation.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode           string Current shortcode name
 * @var $shortcode_base      string The original called shortcode name (differs if called an alias)
 * @var $content             string Shortcode's inner content
 * @var $classes             string Extend class names
 * @var $design_css_class    string Custom design css class
 *
 * @var $width               string Width in format: 1/2 (is set by WPBakery Page Builder renderer)
 * @var $text_color          string Text color
 * @var $animate             string Animation type: '' / 'fade' / 'afc' / 'afl' / 'afr' / 'afb' / 'aft' / 'hfc' / 'wfc'
 * @var $animate_delay       float Animation delay (in seconds)
 * @var $el_id               string element ID
 * @var $el_class            string Additional class
 * @var $offset              string WPBakery Page Builder classes for responsive behaviour
 * @var $css                 string Custom CSS
 * @var $us_bg_overlay_color string
 */

if ( function_exists( 'wpb_translateColumnWidthToSpan' ) ) {
	$width = wpb_translateColumnWidthToSpan( $width );

} elseif ( function_exists( 'us_wpb_translateColumnWidthToSpan' ) ) {
	$width = us_wpb_translateColumnWidthToSpan( $width );
}

if ( function_exists( 'vc_column_offset_class_merge' ) ) {
	$width = vc_column_offset_class_merge( $offset, $width );

} elseif ( function_exists( 'us_vc_column_offset_class_merge' ) ) {
	$width = us_vc_column_offset_class_merge( $offset, $width );
}

// Dev note: "width" classes should be the first for correct work of "columns_stacking_width" option
$_atts['class'] = $width . ' wpb_column vc_column_container';
$_atts['class'] .= isset( $classes ) ? $classes : '';

$inner_atts['class'] = 'vc_column-inner';

// Move us_custom_* class to other container
if ( ! empty( $design_css_class ) ) {
	$_atts['class'] = str_replace( ' ' . $design_css_class, '', $_atts['class'] );
	$inner_atts['class'] .= ' ' . $design_css_class;
}

// When bg color or border is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, array( 'background-color', 'background-image' ) ) ) {
	$_atts['class'] .= ' has-fill';
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$_atts['class'] .= ' has_text_color';
}

// Animated Column
if ( ! empty( $animate ) AND ! us_amp() ) {
	$_atts['class'] .= ' animate_' . $animate;
	if ( ! empty( $animate_delay ) ) {
		$_atts['style'] = 'animation-delay:' . floatval( $animate_delay ) . 's';
	}
}

// Stretched Column
if ( $stretch ) {
	$_atts['class'] .= ' stretched';
}

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Background Overlay
$bg_overlay_html = '';
if ( ! empty( $us_bg_overlay_color ) ) {
	$_atts['class'] .= ' with_overlay';
	$bg_overlay_html = '<div class="vc_column-overlay" style="background:' . us_get_color( $us_bg_overlay_color, /* Gradient */ TRUE ) . '"></div>';
}

// Link
$link_html = '';
if ( $link_atts = us_generate_link_atts( $link ) ) {
	$_atts['class'] .= ' has-link';
	$link_atts['class'] = 'vc_column-link smooth-scroll';

	// Add aria-label, if title is empty to avoid accessibility issues
	if ( empty( $link_atts['title'] ) ) {
		$link_atts['aria-label'] = us_translate( 'Link' );
	}
	$link_html = '<a ' . us_implode_atts( $link_atts ) . '></a>';
}

// Sticky Column
$wrapper_inline_css = '';
if ( $sticky ) {
	$inner_atts['class'] .= ' type_sticky';
	$wrapper_inline_css = us_prepare_inline_css( array( 'top' => $sticky_pos_top ) );
}

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= '<div ' . us_implode_atts( $inner_atts ) . '>';
$output .= $bg_overlay_html;
$output .= '<div class="wpb_wrapper"' . $wrapper_inline_css . '>' . do_shortcode( $content ) . '</div>';
$output .= $link_html;
$output .= '</div></div>';

echo $output;
