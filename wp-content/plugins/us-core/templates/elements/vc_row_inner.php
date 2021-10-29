<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: vc_row_inner
 *
 * Overloaded by UpSolution custom implementation.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode         string Current shortcode name
 * @var $shortcode_base    string The original called shortcode name (differs if called an alias)
 * @var $content           string Shortcode's inner content
 *
 * @var $content_placement string Columns Content Position: 'top' / 'middle' / 'bottom'
 * @var $gap               string gap class for columns
 * @var $el_id             string
 * @var $el_class          string
 * @var $disable_element   string
 * @var $css               string
 * @var $classes           string Extend class names
 */

$atts = us_shortcode_atts( $atts, $shortcode_base );
$class_name = isset( $classes ) ? $classes : '';

if ( $disable_element === 'yes' ) {
	if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
		$class_name .= ' vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
	} else {
		return '';
	}
}

$class_name .= ( $columns_type ) ? ' type_boxes' : ' type_default';

if ( ! empty( $content_placement ) ) {
	$class_name .= ' valign_' . $content_placement;
}

// Prepare extra styles for columns gap
$cols_gap_styles = '';
$gap = trim( $gap );
if ( ! empty( $gap ) ) {
	$gap = trim( strip_tags( $gap ) );
	$gap_class = 'gap-' . str_replace( array( '.', ',', ' ' ), '-', $gap );
	$class_name .= ' ' . $gap_class;

	$cols_gap_styles = '<style>';
	if ( $columns_type ) {
		$cols_gap_styles .= '.g-cols.' . $gap_class . '{margin:0 -' . $gap . '}';
	} else {
		$cols_gap_styles .= '.g-cols.' . $gap_class . '{margin:0 calc(-1.5rem - ' . $gap . ')}';
	}
	$cols_gap_styles .= '.' . $gap_class . ' > .vc_column_container {padding:' . $gap . '}';
	$cols_gap_styles .= '</style>';
}

if ( ! empty( $columns_reverse ) ) {
	$class_name .= ' reversed';
}

// Preserving additional class for inner VC rows
if ( $shortcode_base == 'vc_row_inner' ) {
	$class_name .= ' vc_inner';
}

// Additional class set by a user in a shortcode attributes
if ( ! empty( $el_class ) ) {
	$class_name .= ' ' . $el_class;
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$class_name .= ' has_text_color';
}

$class_name = apply_filters( 'vc_shortcodes_css_class', $class_name, $shortcode_base, $atts );

// Output the element
$output = '<div class="g-cols wpb_row ' . $class_name . '"';
if ( ! empty( $el_id ) ) {
	$output .= ' id="' . $el_id . '"';
}
$output .= '>';
$output .= $cols_gap_styles;
$output .= do_shortcode( $content );
$output .= '</div>';

echo $output;
