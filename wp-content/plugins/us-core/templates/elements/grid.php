<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_grid
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the element config.
 *
 */
if ( apply_filters( 'us_stop_grid_execution', FALSE ) ) {
	return;
}

global $us_grid_loop_running, $us_grid_no_items_message, $us_grid_no_items_action, $us_grid_no_items_page_block;

// If we are running US Grid loop already, return nothing
if ( ! empty( $us_grid_loop_running ) ) {
	return;
}
// DEV NOTE: always change $us_grid_loop_running to FALSE if you interrupt this file execution via return
$us_grid_loop_running = TRUE;

// Set it outside the condition to take a corresponding message
$us_grid_no_items_message = $no_items_message;

$us_grid_no_items_action = $no_items_action;

$us_grid_no_items_page_block = $no_items_page_block;

$classes = isset( $classes ) ? $classes : '';

global $us_context_layout, $us_grid_applied_params;
if ( ! $us_grid_applied_params ) {
	$us_grid_applied_params = array();
}

// Grid indexes for CSS, start from 1
global $us_grid_index;
$us_grid_index = isset( $us_grid_index ) ? ( $us_grid_index + 1 ) : 1;

// Get the page we are on for AJAX calls
global $us_page_block_ids;
if ( ! empty( $us_page_block_ids ) ) {
	$post_id = $us_page_block_ids[0];
} else {
	$post_id = get_the_ID();
}
if ( ! is_archive() ) {
	$current_post_id = get_the_ID();
} else {
	$current_post_id = $post_id;
}

global $us_is_menu_page_block;
$is_menu = ( isset( $us_is_menu_page_block ) AND $us_is_menu_page_block ) ? TRUE : FALSE;

// Grid indexes for ajax, start from 1
if ( $shortcode_base != 'us_carousel' AND ! $is_menu ) {
	global $us_grid_ajax_indexes;
	$us_grid_ajax_indexes[ $post_id ] = isset( $us_grid_ajax_indexes[ $post_id ] ) ? ( $us_grid_ajax_indexes[ $post_id ] + 1 ) : 1;
} else {
	$us_grid_ajax_indexes = NULL;
}

// Preparing the query
$query_args = $filter_taxonomies = array();
$filter_taxonomy_name = $filter_default_taxonomies = '';
$terms = FALSE; // init this as array in terms case

// Items per page
if ( $items_quantity < 1 ) {
	$items_quantity = 999;
}

// Force single item in Carousel for AMP version
if ( us_amp() AND $shortcode_base == 'us_carousel' ) {
	$items_quantity = 1;
}

/*
 * THINGS TO OUTPUT
 */

// Substituting specific post types instead of query depended for US Builder preview of content templates
if (
	apply_filters( 'usb_is_preview_page_for_template', NULL )
	AND in_array(
		$post_type,
		array(
			'related',
			'current_query',
			'current_child_pages',
			'current_child_terms',
			'product_upsells',
			'product_crosssell',
		)
	)
) {
	// First check if there are products present, since they have most of custom fields
	if (
		class_exists( 'woocommerce' )
		AND $count_posts = wp_count_posts( 'product' )
		AND $count_posts->publish > 1
	) {
		$post_type = 'product';

		// then check if there are products present
	} elseif (
		$count_posts = wp_count_posts( 'post' )
		AND $count_posts->publish > 1
	) {
		$post_type = 'post';

		// otherwise using pages
	} else {
		$post_type = 'page';
	}
}

// Singulars
if ( in_array( $post_type, array_keys( us_grid_available_post_types( TRUE ) ) ) ) {
	$query_args['post_type'] = explode( ',', $post_type );

	$atts = ! empty( $atts ) ? $atts : array();
	if ( empty( $atts['post_type'] ) ) {
		$atts['post_type'] = $post_type;
	}

	// Get selected taxonomies for $query_args
	$selected_taxonomies = us_grid_get_selected_taxonomies( $atts );
	if ( is_array( $selected_taxonomies ) AND ! empty( $selected_taxonomies ) ) {
		$query_args = array_merge( $query_args, $selected_taxonomies );
	}

	// Media attachments should have some differ arguments
	if ( $post_type == 'attachment' ) {
		if ( ! empty( $images ) ) {
			$ids = explode( ',', $images );
			$query_args['post__in'] = $ids;
		} else {
			$attached_images = get_attached_media( 'image', $current_post_id );
			if ( ! empty( $attached_images ) ) {
				foreach ( $attached_images as $attached_image ) {
					$query_args['post__in'][] = $attached_image->ID;
				}
			}
		}
		$query_args['post_status'] = 'inherit';
		$query_args['post_mime_type'] = 'image';

	} else {

		// Proper post statuses
		$query_args['post_status'] = array( 'publish' => 'publish' );
		$query_args['post_status'] += (array) get_post_stati( array( 'public' => TRUE ) );

		// Add private states if user is capable to view them
		if ( is_user_logged_in() AND current_user_can( 'read_private_posts' ) ) {
			$query_args['post_status'] += (array) get_post_stati( array( 'private' => TRUE ) );
		}
		$query_args['post_status'] = array_values( $query_args['post_status'] );
	}

	// Data for filter
	if ( ! empty( ${'filter_' . $post_type} ) ) {
		$filter_taxonomy_name = ${'filter_' . $post_type};
		$terms_args = array(
			'hierarchical' => FALSE,
			'taxonomy' => $filter_taxonomy_name,
			'number' => 100,
		);

		// When choosing taxonomies in the settings, we display only the selected
		if ( ! empty( $atts[ 'taxonomy_' . $filter_taxonomy_name ] ) ) {
			$terms_args['slug'] = explode( ',', $atts[ 'taxonomy_' . $filter_taxonomy_name ] );

			// For logged in users, need to show private posts
			if ( is_user_logged_in() ) {
				$terms_args['hide_empty'] = FALSE;
			}
			$filter_default_taxonomies = $atts[ 'taxonomy_' . $filter_taxonomy_name ];
		}

		$filter_taxonomies = get_terms( $terms_args );
		if ( is_user_logged_in() ) {

			// Show private posts, but exclude empty posts
			foreach ( $filter_taxonomies as $key => $filter_term ) {
				if ( is_object( $filter_term ) AND $filter_term->count == 0 ) {
					$the_query = new WP_Query(
						array(
							'tax_query' => array(
								array(
									'taxonomy' => $filter_term->taxonomy,
									'field' => 'slug',
									'terms' => $filter_term->slug,
								),
							),
						)
					);

					// Unset empty terms
					if ( ! ( $the_query->have_posts() ) ) {
						unset ( $filter_taxonomies[ $key ] );
					}
				}
			}
		}
		if (
			isset( $filter_show_all )
			AND ! $filter_show_all
			AND ! empty( $filter_taxonomies[0] )
			AND $filter_taxonomies[0] instanceof WP_Term
		) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => $filter_taxonomy_name,
					'field' => 'slug',
					'terms' => $filter_taxonomies[0]->slug,
				),
			);
		}
	}

	// Specific items by IDs
} elseif ( $post_type == 'ids' ) {
	if ( empty( $ids ) ) {
		us_grid_stop_loop();

		return;
	}

	$ids = explode( ',', $ids );
	$query_args['ignore_sticky_posts'] = 1;
	$query_args['post_type'] = 'any';
	$query_args['post__in'] = array_map( 'trim', $ids );

	// Items with the same taxonomy of current post
} elseif ( $post_type == 'related' ) {
	if ( ! is_singular() OR empty( $related_taxonomy ) ) {
		us_grid_stop_loop( FALSE );

		return;
	}

	$query_args['ignore_sticky_posts'] = 1;
	$query_args['post_type'] = 'any';
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => $related_taxonomy,
			'terms' => wp_get_object_terms( $current_post_id, $related_taxonomy, array( 'fields' => 'ids' ) ),
		),
	);

	// Product upsells (WooCommerce only)
} elseif ( $post_type == 'product_upsells' ) {
	if ( ! is_singular( 'product' ) ) {
		us_grid_stop_loop( FALSE );

		return;
	}

	$upsell_ids = get_post_meta( $current_post_id, '_upsell_ids', TRUE );

	// Pass a negative number to reject random goods
	if ( empty( $upsell_ids ) ) {
		$upsell_ids = array( - 1 );
	}
	$query_args['post_type'] = array( 'product', 'product_variation' );
	$query_args['post__in'] = (array) $upsell_ids;

	// Product cross-sells (WooCommerce only)
} elseif ( $post_type == 'product_crosssell' ) {
	if ( ! is_singular( 'product' ) ) {
		us_grid_stop_loop( FALSE );

		return;
	}

	$crosssell_ids = get_post_meta( $current_post_id, '_crosssell_ids', TRUE );

	// Pass a negative number to reject random goods
	if ( empty( $crosssell_ids ) ) {
		$crosssell_ids = array( - 1 );
	}
	$query_args['post_type'] = array( 'product', 'product_variation' );
	$query_args['post__in'] = (array) $crosssell_ids;

	// Child posts of current
} elseif ( $post_type == 'current_child_pages' ) {
	$query_args['post_parent'] = $current_post_id;
	$query_args['post_type'] = 'any';
	$query_args['ignore_sticky_posts'] = 1;

	// Terms of selected (or current) taxonomy
} elseif ( in_array( $post_type, array( 'taxonomy_terms', 'current_child_terms', 'ids_terms' ) ) ) {
	$current_term_id = $parent = 0;
	$hide_empty = TRUE;
	if ( strpos( $terms_include, 'children' ) !== FALSE ) {
		$parent = '';
	}
	if ( strpos( $terms_include, 'empty' ) !== FALSE ) {
		$hide_empty = FALSE;
	}

	// If the current page is taxonomy page, we will output its children terms only
	if ( $post_type == 'current_child_terms' ) {
		if ( ! is_tag() AND ! is_category() AND ! is_tax() ) {
			us_grid_stop_loop( FALSE );

			return;
		}
		$current_term = get_queried_object();
		$related_taxonomy = $current_term->taxonomy;
		if ( strpos( $terms_include, 'children' ) !== FALSE ) {
			$current_term_id = $current_term->term_id;
		} else {
			$parent = $current_term->term_id;
		}
	}

	if ( $terms_orderby != 'rand' ) {
		$terms_args_query = array(
			'taxonomy' => $related_taxonomy,
			'orderby' => $terms_orderby,
			'order' => ( $terms_orderby == 'count' ) ? 'DESC' : 'ASC',
			'number' => $items_quantity,
			'hide_empty' => $hide_empty,
			'child_of' => $current_term_id,
			'parent' => $parent,
		);

		// Manually selected terms
		if ( $post_type == 'ids_terms' ) {
			if ( empty( $ids_terms ) ) {
				us_grid_stop_loop();

				return;
			} else {
				if ( $terms_orderby == 'menu_order' ) {
					$terms_orderby = 'include';
				}
				$terms_args_query = array(
					'orderby' => $terms_orderby,
					'order' => ( $terms_orderby == 'count' ) ? 'DESC' : 'ASC',
					'number' => $items_quantity,
					'include' => array_map( 'trim', explode( ',', $ids_terms ) ),
				);
			}
		}
		$terms_raw = get_terms( $terms_args_query );
	} else {
		global $wpdb;
		$terms_query_where = '';
		if ( $post_type == 'ids_terms' ) {
			if ( empty( $ids_terms ) ) {
				us_grid_stop_loop();

				return;
			} else {
				$ids_terms = array_map( 'intval', explode( ',', $ids_terms ) );
				$terms_query_where .= ' AND t.term_id IN(' . implode( ',', $ids_terms ) . ')';
			}
		}
		if ( $hide_empty ) {
			$terms_query_where .= ' AND tt.count > 0';
		}
		if ( $parent !== '' ) {
			$terms_query_where .= ' AND tt.parent = ' . (int) $parent;
		}
		$terms_query = "
			SELECT
				t.*, tt.*
			FROM {$wpdb->terms} AS t
			INNER JOIN {$wpdb->term_taxonomy} AS tt
				ON t.term_id = tt.term_id
			WHERE
				tt.taxonomy = %s
				 $terms_query_where
			ORDER BY RAND()
			LIMIT %d
		";
		$terms_query = $wpdb->prepare( $terms_query, $related_taxonomy, $items_quantity );
		$terms_raw = $wpdb->get_results( $terms_query );
	}

	$terms = array();

	// When taxonomy doesn't exist, it returns WP_Error object, so we need to use empty array for further work
	if ( ! is_wp_error( $terms_raw ) ) {

		$ids_terms_map = ( $post_type == 'ids_terms' AND ! empty( $ids_terms ) )
			? array_flip( array_map( 'trim', explode( ',', $ids_terms ) ) )
			: array();

		$available_taxonomy = us_get_taxonomies( TRUE, FALSE );
		foreach ( $terms_raw as $key => $term_item ) {
			// if taxonomy of this term is not available, remove it
			if ( is_object( $term_item ) ) {
				if ( in_array( $term_item->taxonomy, array_keys( $available_taxonomy ) ) ) {
					if ( isset( $ids_terms_map[ $term_item->term_id ] ) ) {
						$terms[ $ids_terms_map[ $term_item->term_id ] ] = $term_item;
					} else {
						$terms[] = $term_item;
					}
				}
			}
		}

		// Apply sorting if it is not by title (name)
		if ( $terms_orderby !== 'name' ) {
			ksort( $terms );
		}
	}

	// Generate query for "Gallery" and "Post Object" types from ACF PRO plugin
} elseif ( strpos( $post_type, 'acf_' ) === 0 ) {
	if ( ! is_singular() ) {
		$current_post_id = get_queried_object_id();
	}

	// ACF Galleries
	if ( strpos( $post_type, 'acf_gallery_' ) === 0 ) {
		$key = str_replace( 'acf_gallery_', '', $post_type );

		$query_args['post_type'] = 'attachment';
		$query_args['post_status'] = 'inherit';

		if ( is_singular() ) {
			$query_args['post__in'] = get_post_meta( $current_post_id, $key, TRUE );
		} else {
			$query_args['post__in'] = get_term_meta( $current_post_id, $key, TRUE );
		}

		// Don't show the Grid, if ACF Gallery has no images
		if ( empty( $query_args['post__in'] ) ) {
			us_grid_stop_loop();

			return;
		}
	}

	// ACF Post objects
	if ( strpos( $post_type, 'acf_posts_' ) === 0 ) {
		if ( ! is_singular() ) {
			us_grid_stop_loop( FALSE );

			return;
		}

		$key = str_replace( 'acf_posts_', '', $post_type );
		$ids = get_post_meta( $current_post_id, $key, TRUE );

		$query_args['post_type'] = 'any';
		$query_args['ignore_sticky_posts'] = 1;
		$query_args['post__in'] = is_array( $ids ) ? $ids : array( $ids );
	}

	// Values from predefined custom fields
} elseif ( strpos( $post_type, 'cf|' ) === 0 ) {
	$key = str_replace( 'cf|', '', $post_type );

	// Get images from metabox "Custom appearance in Grid"
	if ( $key === 'us_tile_additional_image' ) {

		// Include Featured image
		if ( $include_post_thumbnail AND $post_thumbnail_id = get_post_thumbnail_id() ) {
			$ids = array( $post_thumbnail_id );
		} else {
			$ids = array();
		}

		if ( $custom_images = get_post_meta( $current_post_id, $key, TRUE ) ) {
			$ids = array_merge( $ids, explode( ',', $custom_images ) );
		}

		if ( $ids ) {
			$query_args['post__in'] = $ids;
			$query_args['post_status'] = 'inherit';
			$query_args['post_mime_type'] = 'image';
			$query_args['post_type'] = 'attachment';
		} else {
			us_grid_stop_loop();

			return;
		}
	}

	// Product gallery images
} elseif ( $post_type == 'product_gallery' ) {
	if ( ! is_singular( 'product' ) ) {
		us_grid_stop_loop( FALSE );

		return;
	}

	// Include Featured image
	if ( $include_post_thumbnail AND $post_thumbnail_id = get_post_thumbnail_id() ) {
		$ids = array( $post_thumbnail_id );
	} else {
		$ids = array();
	}

	if ( $product_images = get_post_meta( $current_post_id, '_product_image_gallery', TRUE ) ) {
		$ids = array_merge( $ids, explode( ',', $product_images ) );
	}

	// Remove empty ids to avoid duplications in output
	$ids = array_diff( $ids, array( '' ) );

	if ( $ids ) {
		$query_args['post__in'] = $ids;
		$query_args['post_status'] = 'inherit';
		$query_args['post_mime_type'] = 'image';
		$query_args['post_type'] = 'attachment';
	} else {
		us_grid_stop_loop();

		return;
	}
}

// Always exclude the current post from the query
if ( is_singular() ) {
	$query_args['post__not_in'] = array( $current_post_id );
}

// Exclude sticky posts
if ( ! empty( $ignore_sticky ) ) {
	$query_args['ignore_sticky_posts'] = 1;
}

// Fallback (after version 7.11)
if ( $orderby == 'alpha' ) {
	$orderby = 'title';
}

// Begin set orderby params to $query_args
$orderby_params = array(
	'custom_field' => $orderby_custom_field,
	'custom_field_numeric' => $orderby_custom_type,
	'invert' => $order_invert,
	'orderby' => $orderby,
	'post_type' => $post_type,
);

// Apply Grid OrderBy params
global $us_get_orderby;
$get_orderby = us_arr_path( $_GET, us_get_grid_url_prefix( 'order' ), $us_get_orderby );
if (
	! empty( $get_orderby )
	AND $us_context_layout === 'main'
	AND $shortcode_base != 'us_carousel'
	AND empty( $filter_post )
	AND empty( $us_grid_applied_params['grid_order'] )
	AND ! us_post_type_is_available(
		$post_type, array(
			'ids',
			'ids_terms',
			'taxonomy_terms',
			'current_child_terms',
		)
	)
) {
	$us_grid_applied_params['grid_order'] = TRUE;
	$orderby_params = array_merge(
		$orderby_params,
		(array) us_grid_orderby_str_to_params( $get_orderby )
	);
}
unset( $get_orderby );

$orderby_query_args = array();
us_grid_set_orderby_to_query_args( $orderby_query_args, $orderby_params );
unset( $orderby_params );
// End set orderby params to $query_args

// Force "Numbered" pagination for AMP version to avoid AMP ajax developing
if ( us_amp() AND $pagination != 'none' ) {
	$pagination = 'regular';
}

// Pagination
if ( $pagination == 'regular' ) {
	// Fix for get_query_var() that is empty on AMP frontpage
	$request_paged = ( is_front_page() AND ! us_amp() ) ? 'page' : 'paged';

	if ( get_query_var( $request_paged ) ) {
		$query_args['paged'] = get_query_var( $request_paged );
	}
}

// Extra arguments for WooCommerce products
if (
	class_exists( 'woocommerce' )
	AND (
		us_is_grid_products_defined_by_query_args( $query_args )
		OR us_post_type_is_available(
			$post_type, array(
				'product',
				'product_upsells',
				'product_crosssell',
			)
		)
	)
) {

	$query_args['meta_query'] = array();

	// Exclude out of stock products
	if (
		$exclude_items == 'out_of_stock'
		OR get_option( 'woocommerce_hide_out_of_stock_items', 'none' ) === 'yes'
	) {
		$query_args['meta_query'][] = array(
			'key' => '_stock_status',
			'value' => 'outofstock',
			'compare' => '!=',
		);
	}

	// Show Sale products
	if ( strpos( $products_include, 'sale' ) !== FALSE ) {
		if ( function_exists( 'wc_get_product_ids_on_sale' ) AND ! empty( wc_get_product_ids_on_sale() ) ) {
			$query_args['post__in'] = wc_get_product_ids_on_sale();
		} else {
			us_grid_stop_loop();

			return;
		}

	}

	// Show Featured products
	if ( strpos( $products_include, 'featured' ) !== FALSE ) {
		$query_args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field' => 'name',
			'terms' => 'featured',
			'operator' => 'IN',
		);
	}
}

// Exclude "Hidden" products
if (
	class_exists( 'woocommerce' )
	AND us_post_type_is_available(
		$post_type, array(
			'ids',
			'related',
			'product',
			'product_upsells',
			'product_crosssell',
		)
	)
) {
	$query_args['tax_query'][] = array(
		'taxonomy' => 'product_visibility',
		'field' => 'slug',
		'terms' => array( 'exclude-from-catalog' ),
		'operator' => 'NOT IN',
	);
}

// Exclude posts of previous grids on the same page
if ( $exclude_items == 'prev' ) {
	global $us_grid_skip_ids;
	if ( ! empty( $us_grid_skip_ids ) AND is_array( $us_grid_skip_ids ) ) {
		if ( empty( $query_args['post__not_in'] ) OR ! is_array( $query_args['post__not_in'] ) ) {
			$query_args['post__not_in'] = array();
		}
		$query_args['post__not_in'] = array_merge( $query_args['post__not_in'], $us_grid_skip_ids );
	}
}

$query_args['posts_per_page'] = $items_quantity;

// Reset query for using on archives
if ( us_post_type_is_available( $post_type, array( 'current_query' ) ) ) {
	if ( is_tax( 'tribe_events_cat' ) OR is_post_type_archive( 'tribe_events' ) ) {
		$the_content = apply_filters( 'the_content', get_the_content() );

		// The page may be paginated itself via <!--nextpage--> tags
		$the_pagination = us_wp_link_pages();

		echo $the_content . $the_pagination;
		us_grid_stop_loop( FALSE );

		return;
	} elseif ( is_archive() OR is_search() OR is_home() ) {
		$query_args = NULL;
	} else {
		us_grid_stop_loop( FALSE );

		return;
	}
}

// Default query_args created from grid settings
$_default_query_args = array();
if ( ! empty( $query_args ) ) {
	foreach ( array( 'tax_query', 'meta_query' ) as $key ) {
		if ( ! empty( $query_args[ $key ] ) ) {
			$_default_query_args[ $key ] = $query_args[ $key ];
		}
	}
}

// Load Grid Listing template with given params
$template_vars = array(
	'_default_query_args' => $_default_query_args,
	'_us_grid_post_type' => $post_type,
	'classes' => $classes,
	'filter_default_taxonomies' => $filter_default_taxonomies,
	'filter_taxonomies' => $filter_taxonomies,
	'filter_taxonomy_name' => $filter_taxonomy_name,
	'orderby_query_args' => $orderby_query_args,
	'post_id' => $post_id,
	'terms' => $terms,
	'us_grid_ajax_indexes' => $us_grid_ajax_indexes,
	'us_grid_index' => $us_grid_index,
);

// Apply Grid Filter params
if (
	! is_archive() // For archives, the us_inject_grid_filters_into_archive_pages() function will be used
	AND $post_type != 'current_query'
	AND $us_context_layout === 'main'
	AND $shortcode_base != 'us_carousel'
	AND empty( $filter_post )
	AND empty( $us_grid_applied_params['grid_filters'] )
) {
	// Use for all but archive pages
	$us_grid_applied_params['grid_filters'] = TRUE;
	us_apply_grid_filters( $post_id, $query_args );
}

// Apply Grid Filter params to Search page
if ( is_search() ) {
	$search_query_args = $query_args;
	us_apply_grid_filters( $post_id, $search_query_args );

	// Check for Grid Filter attributes
	if ( ! empty( $search_query_args['tax_query'] ) ) {
		$us_grid_applied_params['grid_filters'] = TRUE;
		$query_args['tax_query'] = $search_query_args['tax_query'];
		$query_args['s'] = get_search_query();
		$query_args['paged'] = get_query_var('paged');
	}
}

$template_vars['query_args'] = $query_args;

// Add default values for unset variables from Grid config
$default_grid_params = us_shortcode_atts( array(), 'us_grid' );
foreach ( $default_grid_params as $param => $value ) {
	$template_vars[ $param ] = isset( $$param ) ? $$param : $value;
}

// Add default values for unset variables from Carousel config
if ( $shortcode_base == 'us_carousel' ) {
	$default_carousel_params = us_shortcode_atts( array(), 'us_carousel' );
	foreach ( $default_carousel_params as $param => $value ) {
		$template_vars[ $param ] = isset( $$param ) ? $$param : $value;
	}
	$template_vars['type'] = 'carousel'; // force 'carousel' type for us_carousel shortcode
}

us_load_template( 'templates/us_grid/listing', $template_vars );

$us_grid_loop_running = FALSE;
