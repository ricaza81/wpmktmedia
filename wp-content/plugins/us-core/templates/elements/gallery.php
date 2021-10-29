<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: Gallery
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 *
 */

$classes = isset( $classes ) ? $classes : '';

// Columns
$columns = intval( $columns );
if ( $columns != 1 ) {
	$classes .= ' cols_' . $columns;
}

// Link type
if ( $link == 'none' ) {
	$link_type = 'none';
} elseif ( $link == 'file' ) {
	$link_type = 'file';
} else {
	$link_type = 'attachment';
}
$classes .= ' link_' . $link_type;

// Masonry layout
if ( isset( $masonry ) AND $masonry == 'true' ) {
	$classes .= ' type_masonry';
	if ( $columns > 1 AND us_get_option( 'ajax_load_js', FALSE ) == FALSE ) {
		wp_enqueue_script( 'us-isotope' );
	}
}

// With titles
$with_titles = FALSE;
if ( isset( $meta ) AND $meta == 'true' ) {
	$with_titles = TRUE;
	$classes .= ' with_meta';
}

// With indents
if ( isset( $indents ) AND $indents == 'true' ) {
	$classes .= ' with_indents';
}

// Filter classes
$classes = apply_filters( 'us_gallery_listing_classes', $classes );

// Get images
$query_args = array(
	'include' => $ids,
	'post_status' => 'inherit',
	'post_type' => 'attachment',
	'post_mime_type' => 'image',
	'orderby' => 'post__in',
	'numberposts' => empty( $ids ) ? 5 : - 1,
);
if ( $orderby == 'rand' ) {
	$query_args['orderby'] = 'rand';
}
$attachments = get_posts( $query_args );
if ( ! is_array( $attachments ) OR empty( $attachments ) ) {
	return;
}

// Gallery shortcode usage in feeds
if ( is_feed() ) {
	$output = "\n";
	foreach ( $attachments as $attachment ) {
		$output .= wp_get_attachment_link( $attachment->ID, 'thumbnail', TRUE ) . "\n";
	}

	return $output;
}

// Output the element
$output = '<div class="w-gallery' . $classes . '"><div class="w-gallery-list">';

$item_tag_name = ( $link_type == 'none' ) ? 'div' : 'a';

foreach ( $attachments as $index => $attachment ) {

	// Use the Caption as title
	$title = trim( strip_tags( $attachment->post_excerpt ) );
	if ( empty( $title ) ) {
		// If no Caption, use the Alt
		$title = trim( strip_tags( get_post_meta( $attachment->ID, '_wp_attachment_image_alt', TRUE ) ) );
	}
	if ( empty( $title ) ) {
		// If no Alt, use the Title
		$title = trim( strip_tags( $attachment->post_title ) );
	}

	$output .= '<' . $item_tag_name . ' class="w-gallery-item order_' . ( $index + 1 );
	$output .= apply_filters( 'us_gallery_listing_item_classes', '' );
	$output .= '"';
	if ( $link_type == 'file' ) {
		$output .= ' href="' . wp_get_attachment_url( $attachment->ID ) . '" title="' . esc_attr( $title ) . '"';
	} elseif ( $link_type == 'attachment' ) {
		$output .= ' href="' . get_attachment_link( $attachment->ID ) . '" title="' . esc_attr( $title ) . '"';
	}
	$output .= '>';
	$output .= '<div class="w-gallery-item-img">';
	$output .= wp_get_attachment_image( $attachment->ID, $size );
	$output .= '</div>';
	if ( $with_titles ) {
		$output .= '<div class="w-gallery-item-meta">';
		if ( $title != '' ) {
			$output .= '<div class="w-gallery-item-title">' . $title . '</div>';
		}
		$output .= ( ! empty( $attachment->post_content ) ) ? '<div class="w-gallery-item-description">' . $attachment->post_content . '</div>' : '';
		$output .= '</div>';
	}
	$output .= '</' . $item_tag_name . '>';
}

$output .= "</div></div>";

echo $output;
