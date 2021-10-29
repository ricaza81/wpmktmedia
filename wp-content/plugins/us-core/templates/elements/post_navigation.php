<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Prev/Next navigation
 */

global $us_grid_object_type;

// Cases when the element shouldn't be shown
if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	return;
} elseif ( $us_elm_context == 'shortcode' AND is_archive() ) {
	return;
}

$prevnext = us_get_post_prevnext( $invert, $in_same_term, $taxonomy );

if ( empty( $prevnext ) ) {
	return;
}

$classes = isset( $classes ) ? $classes : '';
$classes .= ' layout_' . $layout;
$classes .= ( $invert ) ? ' inv_true' : ' inv_false';

$classes .= ( ! empty( $el_class ) ) ? ( ' ' . $el_class ) : '';
$el_id = ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) ? ( ' id="' . esc_attr( $el_id ) . '"' ) : '';

// Output the element
$output = '<div class="w-post-elm post_navigation' . $classes . '"' . $el_id . '>';

$item_order = 'first';

foreach ( $prevnext as $key => $item ) {
	if ( ! empty( $prevnext[ $key ] ) ) {
		$tnail_id = get_post_thumbnail_id( $item['id'] );
		if ( $tnail_id ) {
			$image = '<img src="' . wp_get_attachment_image_url( $tnail_id, 'medium' ) . '" loading="lazy" alt="" width="150" height="150">';
		}
		if ( ! $tnail_id OR empty( $image ) ) {
			$image = us_get_img_placeholder( 'thumbnail' );
		}

		$output .= '<a class="post_navigation-item order_' . $item_order . ' to_' . $key . '"';
		$output .= ' href="' . esc_url( $item['link'] ) . '" title="' . esc_attr( $item['title'] ) . '">';
		if ( $layout == 'sided' ) {
			$output .= '<div class="post_navigation-item-img">' . $image . '</div>';
		}
		$output .= '<div class="post_navigation-item-arrow"></div>';
		if ( $layout == 'simple' ) {
			$meta_text = ( $key == 'prev' ) ? $prev_post_text : $next_post_text;
			$output .= '<div class="post_navigation-item-meta">' . strip_tags( $meta_text ) . '</div>';
		}
		$output .= '<div class="post_navigation-item-title"><span>' . $item['title'] . '</span></div>';
		$output .= '</a>';
	} else {
		$output .= '<div class="post_navigation-item order_' . $item_order . ' to_' . $key . '"></div>';
	}

	$item_order = 'second';
}

$output .= '</div>';

echo $output;
