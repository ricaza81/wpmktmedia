<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs Sidebar HTML
 *
 * @filter Template variables: 'us_template_vars:templates/content'
 */

$sidebar_id = us_get_page_area_id( 'sidebar' );

// If set page block load it design css
if ( is_numeric( $sidebar_id ) ) {
	$sidebar_post = get_post( $sidebar_id );
	if ( is_object( $sidebar_post ) ) {
		us_output_design_css( [ $sidebar_post ] );
	}
}

if ( $is_page_blocks_enabled = us_get_option( 'enable_page_blocks_for_sidebars', 0 ) ) {
	$page_block_id = us_get_page_area_id( 'sidebar' );
	$page_block = get_post( $page_block_id );
	us_open_wp_query_context();
	if ( $page_block ) {
		$translated_page_block_id = apply_filters( 'us_tr_object_id', $page_block->ID, 'us_page_block', TRUE );
		if ( $translated_page_block_id != $page_block->ID ) {
			$page_block = get_post( $translated_page_block_id );
		}

		us_add_to_page_block_ids( $translated_page_block_id );
		us_add_page_shortcodes_custom_css( $translated_page_block_id );

		$page_block_content = $page_block->post_content;
		$page_block_content = str_replace(
			array(
				'[vc_row]',
				'[/vc_row]',
				'[vc_column]',
				'[/vc_column]',
			), '', $page_block_content
		);
		$page_block_content = preg_replace( '~\[vc_row (.+?)]~', '', $page_block_content );
		$page_block_content = preg_replace( '~\[vc_column (.+?)]~', '', $page_block_content );
		us_remove_from_page_block_ids();
	}
	us_close_wp_query_context();

	$is_display_sidebar = ! empty( $page_block_content );
} else {
	$is_page_block = FALSE;
	if ( is_numeric( $sidebar_id ) ) {
		$post = get_post( $sidebar_id );
		if ( ! empty( $post ) AND property_exists( $post, 'post_type' ) ) {
			if ( $post->post_type == 'us_page_block' ) {
				$is_page_block = TRUE;
			}
		}
	}
	$is_display_sidebar = ! empty( $sidebar_id ) && ! $is_page_block;
}

if ( ! isset( $place ) OR $sidebar_id == '' ) {
	return;
}

// Get Sidebar position for the current page (based on "us_get_page_area_id" function)
if ( $is_display_sidebar ) {
	$public_post_types = array_keys( us_get_public_post_types( array( 'page', 'product' ) ) ); // public post types except Pages and Products
	$public_taxonomies = array_keys( us_get_taxonomies( TRUE, FALSE, 'woocommerce_exclude' ) ); // public taxonomies EXCEPT Products
	$product_taxonomies = array_keys( us_get_taxonomies( TRUE, FALSE, 'woocommerce_only' ) ); // Products taxonomies ONLY

	// Default from Theme Options
	$position = $pages_position = us_get_option( 'sidebar_pos', 'right' );

	// WooCommerce Products
	if ( function_exists( 'is_product' ) AND is_product() AND us_get_option( 'sidebar_product_id', '__defaults__' ) !== '__defaults__' ) {
		$position = us_get_option( 'sidebar_product_pos', $pages_position );

		// WooCommerce Shop Page
	} elseif ( function_exists( 'is_shop' ) AND is_shop() AND us_get_option( 'sidebar_shop_id', '__defaults__' ) !== '__defaults__' ) {
		$position = us_get_option( 'sidebar_shop_pos', $pages_position );

		// WooCommerce Products Search
	} elseif ( class_exists( 'woocommerce' ) AND is_post_type_archive( 'product' ) AND is_search() AND us_get_option( 'sidebar_shop_id', '__defaults__' ) !== '__defaults__' ) {
		$position = us_get_option( 'sidebar_shop_pos', $pages_position );

		// WooCommerce Products Taxonomies
	} elseif ( class_exists( 'woocommerce' ) AND is_tax( $product_taxonomies ) ) {
		if ( us_get_option( 'sidebar_shop_id', '__defaults__' ) !== '__defaults__' ) {
			$position = us_get_option( 'sidebar_shop_pos', $pages_position );
		}

		$current_tax = get_query_var( 'taxonomy' );

		if ( us_get_option( 'sidebar_tax_' . $current_tax . '_id', '__defaults__' ) !== '__defaults__' ) {
			$position = us_get_option( 'sidebar_tax_' . $current_tax . '_pos', $pages_position );
		}

		// Custom Post Types
	} elseif ( ! empty( $public_post_types ) AND is_singular( $public_post_types ) ) {

		if ( is_attachment() ) {
			$post_type = 'post'; // force "post" suffix for attachments
		} elseif ( is_singular( 'us_portfolio' ) ) {
			$post_type = 'portfolio'; // force "portfolio" suffix to avoid migration from old theme options
		} elseif ( is_singular( 'tribe_events' ) ) {
			$post_type = 'tribe_events'; // force "tribe_*" suffix cause The Events Calendar always returns "page" type
		} elseif ( is_singular( 'tribe_venue' ) ) {
			$post_type = 'tribe_venue';
		} elseif ( is_singular( 'tribe_organizer' ) ) {
			$post_type = 'tribe_organizer';
		} else {
			$post_type = get_post_type();
		}

		if ( us_get_option( 'sidebar_' . $post_type . '_id', '__defaults__' ) !== '__defaults__' ) {
			$position = us_get_option( 'sidebar_' . $post_type . '_pos', $pages_position );
		}

		// Archives
	} elseif ( is_archive() OR is_search() OR is_tax( $public_taxonomies ) ) {
		$position = $archives_position = us_get_option( 'sidebar_archive_pos', $pages_position );

		if ( is_category() ) {
			$current_tax = 'category';
		} elseif ( is_tag() ) {
			$current_tax = 'post_tag';
		} elseif ( is_tax() ) {
			$current_tax = get_query_var( 'taxonomy' );
		}

		if ( ! empty( $current_tax ) AND us_get_option( 'sidebar_tax_' . $current_tax . '_id', '__defaults__' ) !== '__defaults__' ) {
			$position = us_get_option( 'sidebar_tax_' . $current_tax . '_pos', $archives_position );
		}

		// Author Pages
	} elseif ( is_author() AND us_get_option( 'sidebar_author_id', '__defaults__' ) !== '__defaults__' ) {
		$position = us_get_option( 'sidebar_author_pos', $archives_position );
	}

	// Forums archive page
	if ( ( is_post_type_archive( 'forum' ) OR ( function_exists( 'bbp_is_search' ) AND bbp_is_search() ) OR ( function_exists( 'bbp_is_search_results' ) AND bbp_is_search_results() ) ) AND us_get_option( 'sidebar_forum_pos', '__defaults__' ) !== '__defaults__' ) {
		$position = us_get_option( 'sidebar_forum_pos', $archives_position );
	}

	// Events calendar archive page
	if ( is_post_type_archive( 'tribe_events' ) AND us_get_option( 'sidebar_tax_tribe_events_cat_id', '__defaults__' ) !== '__defaults__' ) {
		$position = us_get_option( 'sidebar_tax_tribe_events_cat_pos', $archives_position );
	}

	// Search Results page
	if ( is_search() AND ! is_post_type_archive( 'product' ) AND $postID = us_get_option( 'search_page', 'default' ) AND $postID !== 'default' ) {
		$position = usof_meta( 'us_sidebar_pos', $postID );
	}

	// Posts page
	if ( is_home() AND $postID = us_get_option( 'posts_page', 'default' ) AND $postID !== 'default' ) {
		$position = usof_meta( 'us_sidebar_pos', $postID );
	}

	// 404 page
	if ( is_404() AND $postID = us_get_option( 'page_404', 'default' ) AND $postID !== 'default' ) {
		$position = usof_meta( 'us_sidebar_pos', $postID );
	}

	// Specific page
	if ( is_singular() ) {
		$postID = get_queried_object_id();

		if ( $postID AND metadata_exists( 'post', $postID, 'us_sidebar_pos' ) AND usof_meta( 'us_sidebar_id', $postID ) !== '__defaults__' ) {
			$position = usof_meta( 'us_sidebar_pos', $postID );
		}
	}

	// Outputs HTML regarding place value
	if ( $place == 'before' ) {
		echo '<section class="l-section height_auto for_sidebar at_' . $position . '">';
		echo '<div class="l-section-h">';

		// Content column
		echo '<div class="l-content">';
		echo '<div class="vc_column-inner">';

	} elseif ( $place == 'after' ) {

		echo '</div></div>';

		// Sidebar column
		echo '<div class="l-sidebar">';
		echo '<div class="vc_column-inner">';

		if ( $is_page_blocks_enabled AND $page_block ) {
			us_add_to_page_block_ids( $translated_page_block_id );
			echo apply_filters( 'us_page_block_the_content', $page_block_content );
			us_remove_from_page_block_ids();
		} else {
			dynamic_sidebar( $sidebar_id );
		}

		echo '</div></div>';

		echo '</div>';
		echo '</section>';
	}
}
