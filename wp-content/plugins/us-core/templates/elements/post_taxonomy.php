<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Taxonomy element
 *
 * @var $taxonomy_name string Taxonomy name
 * @var $link string Link type: 'post' / 'archive' / 'custom' / 'none'
 * @var $custom_link array
 * @var $color string Custom color
 * @var $icon string Icon name
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
} elseif (
	(
		empty( $taxonomy_name )
		OR ! taxonomy_exists( $taxonomy_name )
		OR ! is_object_in_taxonomy( get_post_type(), $taxonomy_name )
	)
	AND ! usb_is_preview_page()
) {
	return;
}

// In Live Builder for Page Block / Content template show placeholder for shortcode
if ( usb_is_preview_page_for_template() AND $us_elm_context == 'shortcode' ) {
	$terms = get_terms(
		array(
			'taxonomy' => $taxonomy_name,
			'hide_empty' => FALSE,
			'number' => 1,
		)
	);
} else {
	$terms = get_the_terms( get_the_ID(), $taxonomy_name );
}

if ( ! is_array( $terms ) OR count( $terms ) == 0 ) {

	// Output empty container for USBuilder
	if ( usb_is_preview_page() ) {
		echo '<div class="w-post-elm"></div>';
	}
	return;
}

// Sorting terms from woocommerce
if ( strpos( $taxonomy_name, 'pa_' ) === 0 ) {
	$_terms = array();
	foreach ( $terms as $term ) {
		if ( ( $order = get_term_meta( $term->term_id, 'order', TRUE ) ) == FALSE OR ! is_numeric( $order ) ) {
			$order = count( $_terms ) + 1;
		}
		$_terms[ $order ] = $term;
	}
	ksort( $_terms );
	$terms = $_terms;
}

$_atts['class'] = 'w-post-elm post_taxonomy';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' style_' . $style;
if ( $color_link ) {
	$_atts['class'] .= ' color_link_inherit';
}

if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

// Link
if ( $link === 'post' ) {
	$link_atts['href'] = apply_filters( 'the_permalink', get_permalink() );

	// Force opening in a new tab for "Link" post format
	if ( get_post_format() == 'link' ) {
		$link_atts['target'] = '_blank';
		$link_atts['rel'] = 'noopener';
	}
} elseif ( $link === 'custom' ) {
	$link_atts = us_generate_link_atts( $custom_link );
} else {
	$link_atts = array();
}

$text_before = ( trim( $text_before ) != '' ) ? '<span class="w-post-elm-before">' . trim( $text_before ) . ' </span>' : '';

// Set Button Style class
$btn_atts = array();
if ( $style == 'badge' ) {
	$btn_atts['class'] = 'w-btn us-btn-style_' . $btn_style;
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= $text_before;
if ( $style == 'badge' AND count( $terms ) > 1 ) {
	$output .= '<div class="w-post-elm-list">';
}

$i = 1;
foreach ( $terms as $term ) {
	if ( $link === 'archive' ) {
		$link_atts['href'] = get_term_link( $term );

		// Output "rel" attribute for Posts tags
		if ( $taxonomy_name == 'post_tag' ) {
			$link_atts['rel'] = 'tag';
		}
	}
	if ( ! empty( $link_atts['href'] ) ) {
		$output .= '<a' . us_implode_atts( $btn_atts + $link_atts ) . '>';

		// Span if set button for correct display buttons styles
		if ( $style == 'badge' ) {
			$output .= '<span class="w-btn-label">';
		}

		$output .= $term->name;

		if ( $style == 'badge' ) {
			$output .= '</span>';
		}
		$output .= '</a>';

	} else {
		$output .= '<span' . us_implode_atts( $btn_atts ) . '>' . $term->name . '</span>';
	}
	// Output comma after anchor except the last one
	if ( $style != 'badge' AND $i != count( $terms ) ) {
		$output .= $separator;
	}
	$i++;
}

if ( $style == 'badge' AND count( $terms ) > 1 ) {
	$output .= '</div>';
}
$output .= '</div>';

echo $output;
