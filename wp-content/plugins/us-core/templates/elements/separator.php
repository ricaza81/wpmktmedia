<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_separator
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 * @param $size				 	 string Separator Height: 'small' / 'medium' / 'large' / 'huge' / 'custom'
 * @param $height  				 string Separator custom height
 * @param $show_line			 bool Show the line in the middle?
 * @param $line_width			 string Separator type: 'default' / 'fullwidth' / 'short'
 * @param $thick				 string Line thickness: '1' / '2' / '3' / '4' / '5'
 * @param $style				 string Line style: 'solid' / 'dashed' / 'dotted' / 'double'
 * @param $color				 string Color style: 'border' / 'primary' / 'secondary' / 'custom'
 * @param $bdcolor				 string Border color value
 * @param $icon					 string Icon
 * @param $text					 string Title
 * @param $title_tag			 string Title HTML tag: 'h1' / 'h2'/ 'h3'/ 'h4'/ 'h5'/ 'h6'/ 'div'
 * @param $title_size			 string Font Size
 * @param $align				 string Alignment
 * @param $link					 string Link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $el_class				 string Extra class name
 * @param $breakpoint_1_width	 string Screen Width breakpoint 1
 * @param $breakpoint_1_height	 string Separator custom height 1
 * @param $breakpoint_2_width	 string Screen Width breakpoint 2
 * @param $breakpoint_2_height	 string Separator custom height 2
 */

$_atts['class'] = 'w-separator';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' size_' . $size;

// When some values are set in Design options, add the specific classes
if ( us_design_options_has_property( $css, 'font-size' ) ) {
	$_atts['class'] .= ' has_font_size';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Link
$link_opener = $link_closer = '';
$link_atts = us_generate_link_atts( $link );
if ( ! empty( $link_atts['href'] ) ) {
	$link_atts['class'] = 'smooth-scroll';
	$link_opener = '<a' . us_implode_atts( $link_atts ) . '>';
	$link_closer = '</a>';
}

// Generate separator icon and title
$inner_html = '';
if ( $show_line ) {
	$_atts['class'] .= ' with_line';
	$_atts['class'] .= ' width_' . $line_width;
	$_atts['class'] .= ' thick_' . $thick;
	$_atts['class'] .= ' style_' . $style;
	$_atts['class'] .= ' color_' . $color;
	$_atts['class'] .= ' align_' . $align;

	if ( ! empty( $text ) ) {
		$_atts['class'] .= ' with_text';

		// Apply filters to text
		$text = us_replace_dynamic_value( $text );
		$text = strip_tags( $text, '<strong><br>' );
		$text = wptexturize( $text );

		$inner_html .= '<' . $title_tag . ' class="w-separator-text">';
		$inner_html .= $link_opener;
		$inner_html .= us_prepare_icon_tag( $icon );
		$inner_html .= '<span>' . $text . '</span>';
		$inner_html .= $link_closer;
		$inner_html .= '</' . $title_tag . '>';
	} else {
		$inner_html .= us_prepare_icon_tag( $icon );
	}

	if ( $inner_html != '' ) {
		$_atts['class'] .= ' with_content';
	}

	$inner_html = '<div class="w-separator-h">' . $inner_html . '</div>';
}

// Add custom height via inline style attribute
if ( $size == 'custom' ) {
	$_atts['style'] = 'height:' . $height;
}

// Set element index to apply <style> for responsive CSS
$responsive_styles = '';
if ( $size == 'custom' AND $breakpoint_1_height != '' OR $breakpoint_2_height != '' ) {

	// Generate unique ID to apply responsive styles to the current element only
	if ( empty( $_atts['id'] ) ) {
		$_atts['id'] = us_uniqid();
	}

	$responsive_styles = '<style>';
	if ( $breakpoint_1_height != '' AND $breakpoint_1_height != $height ) {
		$responsive_styles .= '@media(max-width:' . ( (int) $breakpoint_1_width - 1 ) . 'px){ #' . $_atts['id'] . '{height:' . esc_attr( $breakpoint_1_height ) . '!important}}';
	}
	if ( $breakpoint_2_height != '' AND $breakpoint_2_height != $height ) {
		$responsive_styles .= '@media(max-width:' . ( (int) $breakpoint_2_width - 1 ) . 'px){ #' . $_atts['id'] . '{height:' . esc_attr( $breakpoint_2_height ) . '!important}}';
	}
	$responsive_styles .= '</style>';
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= $responsive_styles;
$output .= $inner_html;
$output .= '</div>';

echo $output;
