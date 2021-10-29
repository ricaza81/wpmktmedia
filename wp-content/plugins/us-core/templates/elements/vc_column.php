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
 * @var $el_id               string element ID
 * @var $el_class            string Additional class
 * @var $css                 string Custom CSS
 * @var $us_bg_overlay_color string
 */
 
// Enable new columns layout only if Live Builde is enabled too
$grid_columns_layout = ( us_get_option( 'live_builder' ) AND us_get_option( 'grid_columns_layout' ) );

if ( $grid_columns_layout ) {
	$_atts['class'] = 'wpb_column vc_column_container';
	$_atts['class'] .= isset( $classes ) ? $classes : '';
	$inner_atts['class'] = 'vc_column-inner';

} else {

	// Fallback of specific class for WPBakery columns system
	$width = isset( $atts['width'] ) ? $atts['width'] : '1/1';
	$offset = isset( $atts['offset'] ) ? $atts['offset'] : '';

	if ( function_exists( 'wpb_translateColumnWidthToSpan' ) ) {
		$width_class = wpb_translateColumnWidthToSpan( $width );

	} elseif ( function_exists( 'us_wpb_translateColumnWidthToSpan' ) ) {
		$width_class = us_wpb_translateColumnWidthToSpan( $width );
	}
	if ( function_exists( 'vc_column_offset_class_merge' ) ) {
		$width_class = vc_column_offset_class_merge( $offset, $width_class );

	} elseif ( function_exists( 'us_vc_column_offset_class_merge' ) ) {
		$width_class = us_vc_column_offset_class_merge( $offset, $width_class );
	}

	// Dev note: "width" classes should be the first for correct work of "columns_stacking_width" option
	$_atts['class'] = $width_class . ' wpb_column vc_column_container';
	$_atts['class'] .= isset( $classes ) ? $classes : '';
	$inner_atts['class'] = 'vc_column-inner';

	// Move us_custom_* class to other container
	if ( ! empty( $design_css_class ) ) {
		$_atts['class'] = str_replace( ' ' . $design_css_class, '', $_atts['class'] );
		$inner_atts['class'] .= ' ' . $design_css_class;
	}

	// Move "us_animate_this" class to other container
	if ( strpos( $_atts['class'], 'us_animate_this' ) !== FALSE ) {
		$_atts['class'] = str_replace( 'us_animate_this', '', $_atts['class'] );
		$inner_atts['class'] .= ' us_animate_this';
	}
}

// When bg color or border is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, array( 'background-color', 'background-image' ) ) ) {
	$_atts['class'] .= ' has_bg_color';
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$_atts['class'] .= ' has_text_color';
}

// Fallback for the old "animate" attribute (for versions before 8.0)
if ( ! us_amp() AND ! us_design_options_has_property( $css, 'animation-name' ) AND ! empty( $atts['animate'] ) ) {
	$_atts['class'] .= ' us_animate_' . $atts['animate'];
	if ( ! empty( $atts['animate_delay'] ) ) {
		$_atts['style'] = 'animation-delay:' . (float) $atts['animate_delay'] . 's';
	}
}

// Stretched Column
if ( $stretch ) {
	$_atts['class'] .= ' stretched';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Background Overlay
$bg_overlay_html = '';
if ( ! empty( $us_bg_overlay_color ) OR apply_filters( 'usb_is_preview_page', NULL ) ) {
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
	$link_html = '<a' . us_implode_atts( $link_atts ) . '></a>';
}

// Sticky Column
$wpb_wrapper_style = '';
if ( $sticky ) {
	if ( $grid_columns_layout ) {
		$_atts['class'] .= ' type_sticky';
		$inner_atts['style'] = 'top:' . $sticky_pos_top;
	} else {
		$inner_atts['class'] .= ' type_sticky';
		$wpb_wrapper_style = ' style="top:' . esc_attr( $sticky_pos_top ) . '"';
	}
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';

if ( $grid_columns_layout ) {
	$output .= $bg_overlay_html;
}

$output .= '<div' . us_implode_atts( $inner_atts ) . '>';

// Additional legacy <div>
if ( ! $grid_columns_layout ) {
	$output .= $bg_overlay_html;
	$output .= '<div class="wpb_wrapper"' . $wpb_wrapper_style . '>';
}

$output .= do_shortcode( $content );

if ( ! $grid_columns_layout ) {
	$output .= '</div>';
}

$output .= '</div>';
$output .= $link_html;
$output .= '</div>';

echo $output;
