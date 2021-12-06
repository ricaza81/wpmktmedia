<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output text element
 *
 * @var $text           string
 * @var $size           int Text size
 * @var $size_tablets   int Text size for tablets
 * @var $size_mobiles   int Text size for mobiles
 * @var $link           string Link
 * @var $icon           string FontAwesome or Material icon
 * @var $font           string Font Source
 * @var $color          string Custom text color
 * @var $design_options array
 * @var $_atts['class'] string
 * @var $id             string
 */

global $us_grid_object_type;

$_atts['class'] = 'w-text';
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( $us_elm_context == 'header' AND empty( $wrap ) ) {
	$_atts['class'] .= ' nowrap';
}

// Fallback since version 7.1
if ( ! empty( $align ) ) {
	$_atts['class'] .= ' align_' . $align;
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Apply filters to text
$text = us_replace_dynamic_value( $text, $us_elm_context, $us_grid_object_type );
$text = strip_tags( $text, '<strong><br>' );
$text = wptexturize( $text );

// Link
if ( $link_type === 'none' ) {
	$link_atts = array();
} elseif ( $link_type === 'post' ) {

	// Terms of selected taxonomy in Grid
	if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
		global $us_grid_term;
		$link_atts['href'] = get_term_link( $us_grid_term );
	} else {
		$link_atts['href'] = apply_filters( 'the_permalink', get_permalink() );
	}

} elseif ( $link_type === 'elm_value' AND ! empty( $text ) ) {
	if ( is_email( $text ) ) {
		$link_atts['href'] = 'mailto:' . $text;
	} elseif ( strpos( $text, '+' ) === 0 ) {
		$link_atts['href'] = 'tel:' . $text;
	} else {
		$link_atts['href'] = esc_url( $text );
	}
} elseif ( $link_type === 'custom' ) {
	$link_atts = us_generate_link_atts( $link );
} elseif ( $link_type === 'onclick' ) {
	$onclick_code = ! empty( $onclick_code ) ? $onclick_code : 'return false';
	$link_atts['href'] = '#';
	$link_atts['onclick'] = esc_js( trim( $onclick_code ) );
} else {
	$link_atts = us_generate_link_atts( 'url:{{' . $link_type . '}}|||' );
}

if ( ! empty( $link_atts['href'] ) ) {
	$link_tag = 'a';

	// Force "Open in a new tab" attributes
	if ( empty( $link_atts['target'] ) AND $link_new_tab ) {
		$link_atts['target'] = '_blank';
		$link_atts['rel'] = 'noopener nofollow';
	}

	// Add placeholder aria-label for Accessibility
	if ( $text === '' AND ! empty( $icon ) ) {
		$link_atts['aria-label'] = $icon;
	}

} else {
	$link_tag = 'span';
}

$link_atts['class'] = 'w-text-h';

// Output the element
$output = '<' . $tag . us_implode_atts( $_atts ) . '>';
$output .= '<' . $link_tag . us_implode_atts( $link_atts ) . '>';

if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= '<span class="w-text-value">' . $text . '</span>';

$output .= '</' . $link_tag . '>';
$output .= '</' . $tag . '>';

echo $output;
