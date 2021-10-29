<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Content element
 *
 * @var $us_elm_context string: 'shortcode' / 'grid' / 'header'
 * @var $type string Show: 'excerpt_only' / 'excerpt_content' / 'part_content' / 'full_content'
 * @var $length int Amount of words
 * @var $design_options array
 * @var bool $show_more_toggle
 * @var string $show_more_toggle_height
 *
 * @var $classes string
 * @var $id string
 */

// Do not output on admin pages, except ajax requests
if ( is_admin() AND ! wp_doing_ajax() ) {
	return;
}

// Do not output when used in grid for posts with "Link" format
if ( $us_elm_context === 'grid' AND get_post_format() === 'link' ) {
	return;
}

// Do not output when used as shortcode with Excerpt on Search Results page
if (
	$us_elm_context === 'shortcode'
	AND in_array( $type, array( 'excerpt_content', 'excerpt_only' ) )
	AND is_search()
) {
	return;
}

// Calculate amount of usage the element with full content to avoid infinite recursion
global $us_full_content_stack;
if ( isset( $us_full_content_stack ) AND $us_full_content_stack > 10 AND $type == 'full_content' ) {
	die( '<h5 style="text-align:center; margin-top:20vh; padding:5%;">Post Content outputs itself infinitely. Fix layout of this page.</h5>' );
}

// Find Post Image element with media preview in Page Block
global $us_page_block_ids;
$strip_from_the_content = FALSE;
if ( ! empty( $us_page_block_ids ) ) {
	$page_block = get_post( $us_page_block_ids[0] );

	// Find Post Image element
	if ( preg_match( '~\[us_post_image.+media_preview="1".+?\]~', $page_block->post_content ) ) {
		$strip_from_the_content = TRUE;
	}
}

us_add_to_page_block_ids( get_the_ID() );

if ( $type == 'full_content' ) {
	$us_full_content_stack = ( empty( $us_full_content_stack ) ) ? 1 : $us_full_content_stack + 1;
}

// Default case
$the_content = '';

global $us_grid_object_type;

// Get term description as "Excerpt" for Grid terms
if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	global $us_grid_term;
	$the_content = $us_grid_term->description;

	// Limit the amount of words for the Excerpt
	if ( intval( $excerpt_length ) > 0 ) {
		$the_content = wp_trim_words( $the_content, intval( $excerpt_length ) );
	}

	// Get term description as "Excerpt" for archive pages
} elseif ( $us_elm_context == 'shortcode' AND ( is_category() OR is_tag() OR is_tax() ) ) {
	$the_content = do_shortcode( term_description() );

	// Post excerpt is not empty
} elseif ( in_array( $type, array( 'excerpt_content', 'excerpt_only' ) ) AND has_excerpt() ) {
	$the_content = do_shortcode( apply_filters( 'the_excerpt', get_the_excerpt() ) );

	// Limit the amount of words for the Excerpt
	if ( intval( $excerpt_length ) > 0 ) {
		$the_content = wp_trim_words( $the_content, intval( $excerpt_length ) );
	}

	// Either the excerpt is empty and we show the content instead or we show the content only
} elseif ( in_array( $type, array( 'excerpt_content', 'part_content', 'full_content' ) ) ) {
	global $us_is_search_page_block;

	if ( get_post_type() == 'attachment' ) {
		$the_content = get_the_content();
	} else {

		// Get WooCommerce Shop Page content
		if ( function_exists( 'is_shop' ) AND is_shop() AND $us_elm_context == 'shortcode' ) {

			if ( ! is_search() AND $shop_page = get_post( wc_get_page_id( 'shop' ) ) ) {
				$the_content = $shop_page->post_content;
			}

		} elseif ( ! empty( $us_is_search_page_block ) AND $us_elm_context == 'shortcode' AND $search_page = get_post( us_get_option( 'search_page' ) ) ) {
			if ( has_filter( 'us_tr_object_id' ) ) {
				$search_page = get_post( apply_filters( 'us_tr_object_id', $search_page->ID, 'page', TRUE ) );
			}

			// Replacing last post ID at page blocks stack with actual search page template ID
			us_remove_from_page_block_ids();
			us_add_to_page_block_ids( $search_page->ID );

			$the_content = $search_page->post_content;
			$us_is_search_page_block = FALSE;

		} elseif ( is_404() AND $us_elm_context == 'shortcode' AND $page_404 = get_post( us_get_option( 'page_404' ) ) ) {
			if ( has_filter( 'us_tr_object_id' ) ) {
				$page_404 = get_post( apply_filters( 'us_tr_object_id', $page_404->ID, 'page', TRUE ) );
			}
			$the_content = $page_404->post_content;

		} else {
			$the_content = get_the_content();
		}

		// Remove [vc_row] and [vc_column] if set
		if ( $remove_rows == 1 ) {
			$the_content = str_replace( array( '[vc_row]', '[/vc_row]', '[vc_column]', '[/vc_column]' ), '', $the_content );
			$the_content = preg_replace( '~\[vc_row (.+?)]~', '', $the_content );
			$the_content = preg_replace( '~\[vc_column (.+?)]~', '', $the_content );

		// Force fullwidth for all [vc_row] if set
		} elseif ( $force_fullwidth_rows ) {
			$the_content = str_replace( '[vc_row]', '[vc_row width="full"]', $the_content );
			$the_content = str_replace( '[vc_row ', '[vc_row width="full" ', $the_content );
		}

		// Check enabled option show image title and description
		if ( ! $strip_from_the_content AND preg_match( '/\[us_image_slider.+meta="1[^\]]\]/', $the_content ) ) {
			$strip_from_the_content = TRUE;
		}

		// Remove video, audio, slider, gallery from the content for relevant post formats
		us_get_post_preview( $the_content, $strip_from_the_content );

		$the_content = apply_filters( 'the_content', $the_content );

		// Limit the amount of words for the Content
		if ( in_array( $type, array( 'excerpt_content', 'part_content' ) ) AND intval( $length ) > 0 ) {
			$the_content = wp_trim_words( $the_content, intval( $length ) );
		}
	}
}

// Add pagination for Full Content only
if ( $type == 'full_content' ) {
	$the_content .= us_wp_link_pages();
}

// In case of excluding parent Row and Columns, output the content itself
if ( $type == 'full_content' AND $remove_rows === 'parent_row' ) {
	$output = $the_content;

} else {

	$_atts['class'] = 'w-post-elm post_content';
	$_atts['class'] .= isset( $classes ) ? $classes : '';

	if ( ! empty( $el_class ) ) {
		$_atts['class'] .= ' ' . $el_class;
	}
	if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
		$_atts['id'] = $el_id;
	}

	// Schema.org markup
	if ( us_get_option( 'schema_markup' ) AND $us_elm_context == 'shortcode' ) {
		$_atts['itemprop'] = 'text';
	}

	// Add specific class, when "Show More" is enabled
	if ( $show_more_toggle ) {
		$_atts['class'] .= ' with_show_more_toggle';
		$_atts['data-toggle-height'] = $show_more_toggle_height;
	}

	// Output the element
	$output = '<div ' . us_implode_atts( $_atts ) . '>';

	// Additional <div>, when "Show More" is enabled
	if ( $show_more_toggle AND ! us_amp() ) {
		$output .= '<div>';
	}

	$output .= $the_content;

	if ( $show_more_toggle  AND ! us_amp() ) {
		$output .= '</div>';
		$output .= '<div class="toggle-links align_' . $show_more_toggle_alignment . '">';
		$output .= '<a href="javascript:void(0)" class="toggle-show-more">' . strip_tags( $show_more_toggle_text_more ) . '</a>';
		$output .= '<a href="javascript:void(0)" class="toggle-show-less">' . strip_tags( $show_more_toggle_text_less ) . '</a>';
		$output .= '</div>';
	}
	$output .= '</div>';
}

if ( $type == 'full_content' ) {
	$us_full_content_stack --;
}
us_remove_from_page_block_ids();

// Output nothing when no content
if ( $the_content == '' ) {
	return;
} else {
	echo $output;
}
