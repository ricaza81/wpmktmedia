<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Date element
 *
 * @var $type string Date type: 'published' / 'modified'
 * @var $format string Date format selected from preset
 * @var $format_custom string Date custom format
 * @var $icon string Icon name
 * @var $tag string 'h1' / 'h2' / 'h3' / 'h4' / 'h5' / 'h6' / 'p' / 'div'
 * @var $color string Custom color
 * @var $design_options array
 *
 * @var $classes string
 * @var $id string
 */

global $us_grid_object_type;

// Cases when the element shouldn't be shown
if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	return;

} elseif ( $us_elm_context == 'shortcode' AND is_archive() ) {
	return;
}

$_atts['class'] = 'w-post-elm post_date';
$_atts['class'] .= isset( $classes ) ? $classes : '';

// Classes for Google structured data
$_atts['class'] .= ' entry-date';
if ( $type == 'modified' ) {
	$_atts['class'] .= ' updated';
} else {
	$_atts['class'] .= ' published';
}

if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

$tag = 'time';

$smart_date = FALSE;

// Generate date format
if ( $format == 'default' ) {
	$format = get_option( 'date_format' );
} elseif ( $format == 'custom' ) {
	$format = $format_custom;
} elseif ( $format == 'smart' ) {
	$format = 'U';
	$smart_date = TRUE;
}

if ( $type == 'modified' ) {
	$date = get_the_modified_date( $format );

	$_atts['datetime'] = get_the_modified_date( 'c' ); // needed datetime attribute for <time> tag
	if ( $smart_date ) {
		$_atts['title'] = sprintf( us_translate( '%1$s at %2$s' ), get_the_modified_date( 'j F Y' ), get_the_modified_date( 'H:i:s e' ) );
	}
} else {
	$date = get_the_date( $format );

	$_atts['datetime'] = get_the_date( 'c' ); // needed datetime attribute for <time> tag
	if ( $smart_date ) {
		$_atts['title'] = sprintf( us_translate( '%1$s at %2$s' ), get_the_date( 'j F Y' ), get_the_date( 'H:i:s e' ) );
	}
}

// Generate date in smart format
if ( $smart_date ) {
	$date = us_get_smart_date( $date );
}

// Add Schema.org markup
if ( us_get_option( 'schema_markup' ) AND $us_elm_context == 'shortcode' ) {
	$_atts['itemprop'] = ( $type == 'modified' ) ? 'dateModified' : 'datePublished';
}

$text_before = ( trim( $text_before ) != '' ) ? '<span class="w-post-elm-before">' . trim( $text_before ) . ' </span>' : '';

// Output the element
$output = '<' . $tag . us_implode_atts( $_atts ) . '>';
if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= $text_before;
$output .= $date;
$output .= '</' . $tag . '>';

echo $output;
