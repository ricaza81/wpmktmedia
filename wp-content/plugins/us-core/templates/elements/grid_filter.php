<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_grid_filter
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the element config.
 */

// Don't output the Grid Filter on AMP pages
if ( us_amp() ) {
	return;
}

// Don't output the Grid Filter if there are no items for it
if ( empty( $filter_items ) AND ! usb_is_preview_page() ) {
	return;
}

$filter_items = json_decode( urldecode( $filter_items ), TRUE );

$form_atts['class'] = 'w-filter state_desktop';
$form_atts['class'] .= isset( $classes ) ? $classes : '';

$form_atts['class'] .= ' layout_' . $layout;
$form_atts['class'] .= ' items_' . count( $filter_items );

if ( $layout == 'hor' ) {
	$form_atts['class'] .= ' style_' . $style;
	$form_atts['class'] .= ' align_' . $align;
	$form_atts['class'] .= ' show_on_' . $values_drop;
	if ( empty( $show_item_title ) ) {
		$form_atts['class'] .= ' hide_item_title';
	}
}

if ( $hide_disabled_values ) {
	$form_atts['class'] .= ' hide_disabled_values';
}
if ( ! empty( $el_id ) ) {
	$form_atts['id'] = $el_id;
}
$form_atts['action'] = '';
$form_atts['onsubmit'] = 'return false;';

$filter_url_prefix = us_get_grid_url_prefix( 'filter' );

// Get filter taxonomies
$filter_taxonomies = us_get_filter_taxonomies( $filter_url_prefix );

// Get portfolio taxonomies slugs map
$portfolio_slugs = us_get_portfolio_slugs_map();

// Export settings to grid-filter.js
$json_data = array(
	'assignedGrid' => (string) isset( $assigned_grid ) ? $assigned_grid : '',
	'filterPrefix' => (string) $filter_url_prefix,
	'hideDisabledValues' => (bool) $hide_disabled_values,
	'layout' => (string) $layout,
	'mobileWidth' => (int) $mobile_width,
);

// Message when Grid is not found
$json_data['gridNotFoundMessage'] = 'Nothing to filter. Add a suitable Grid to this page.';

$output = '<form' . us_implode_atts( $form_atts ) . us_pass_data_to_js( $json_data ) . '>';
$output .= '<div class="w-filter-list">';

if ( ! empty( $mobile_width ) ) {
	$output .= '<div class="w-filter-list-title">' . strip_tags( $mobile_button_label ) . '</div>';
	$output .= '<a class="w-filter-list-closer" href="javascript:void(0);" title="' . esc_attr( us_translate( 'Close' ) ) . '"></a>';
}

/**
 * Sorts the order of terms
 *
 * @param array $terms
 * @param int $parent
 * @return array
 */
$func_sort_terms = function ( &$terms, $parent = 0 ) use ( &$func_sort_terms ) {
	$result = array();
	foreach ( $terms as $i => $term ) {
		if ( $term->parent == $parent ) {
			$result[] = $term;
			unset( $terms[ $i ] );
			foreach ( $terms as $item ) {
				if ( $item->parent AND $item->parent === $term->term_id ) {
					$result = array_merge( $result, $func_sort_terms( $terms, $term->term_id ) );
				}
			}
		}
	}

	return $result;
};

/**
 * Get depth
 *
 * @param int $parent
 * @param array $terms_parent The terms parent
 * @return int
 */
$func_get_depth = function ( $parent, $terms_parent ) {
	$depth = 1;
	while ( $parent > 0 ) {
		if ( $depth > 5 ) { // limit hierarchy by 5 levels
			break;
		}
		if ( isset( $terms_parent[ $parent ] ) ) {
			$parent = $terms_parent[ $parent ];
			$depth ++;
		} else {
			$parent = 0;
		}
	}

	return $depth;
};

/**
 * @var array
 */
$query_args = array(
	'fields' => 'ids',
	'nopaging' => TRUE,
	'post_status' => (array) us_get_available_post_statuses(),
	'post_type' => array_keys( (array) us_grid_available_taxonomies() ),
	// TODO: 'posts_per_page' seems to have no effect when 'nopaging' is TRUE - investigate and fix or remove
	'posts_per_page' => 1, // We get only 1 record, we do not need data, we need the total in found_posts
	'suppress_filters' => TRUE,
);

/*
 * If the filter is being output not on an post archive page (but on a regular page)
 * then try to find the selected parameters
 */
$selected_taxonomies = array();
if ( ! is_archive() ) {

	// Get current post ID
	if ( is_search() ) {
		$post_id = (int) us_get_option( 'search_page', /* Default */get_the_ID() );
	} else {
		$post_id = get_the_ID();
	}

	$meta_key = '_us_first_grid_selected_taxonomies';
	// If there are no parameters in the current object, then we will get all the indicators to check
	if ( ! $selected_taxonomies = get_post_meta( $post_id, $meta_key, TRUE ) ) {
		$page_block_ids = array();
		us_get_recursive_parse_page_block( get_post( $post_id ), function ( $page_block ) use ( &$page_block_ids ) {
			$page_block_ids[] = $page_block->ID;
		} );
	}
	if ( ! empty( $page_block_ids ) ) {
		foreach ( $page_block_ids as $page_block_id ) {
			if ( $selected_taxonomies = get_post_meta( $page_block_id, $meta_key, TRUE ) ) {
				break;
			}
		}
	}

	// Get the post types of the first grid
	if ( ! is_search() AND $post_types = (array) get_post_meta( $post_id, '_us_first_grid_post_type', TRUE ) ) {
		$query_args['post_type'] = (string) us_arr_path( $post_types, '0', /* Default */'post' );
	}
	// Get products include of the first grid
	if ( $products_include = (string) get_post_meta( $post_id, '_us_first_grid_products_include', TRUE ) ) {
		// Show Sale products
		if (
			strpos( $products_include, 'sale' ) !== FALSE
			AND function_exists( 'wc_get_product_ids_on_sale' )
			AND ! empty( wc_get_product_ids_on_sale() )
		) {
			$query_args['post__in'] = wc_get_product_ids_on_sale();
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
	unset( $post_id, $page_block_ids, $meta_key, $products_include );
}

if ( is_archive() ) {
	global $wp_query;
	// Gets post type of the archive page from the first queried post
	if ( isset( $wp_query->posts[0]->post_type ) AND in_array( $wp_query->posts[0]->post_type, $query_args['post_type'] ) ) {
		$query_args['post_type'] = array( $wp_query->posts[0]->post_type );
	}
}

/**
 * @var array
 */
$data_query_args = $filters_args = array();

// If we are on a post archive page, we will add it's conditions to the current request
$queried_object = get_queried_object();
if ( $queried_object instanceof WP_Term ) {
	$query_args['tax_query'] = array(
		array(
			'field' => 'slug',
			'taxonomy' => $queried_object->taxonomy,
			'terms' => $queried_object->slug,
		),
	);
	// For author pages, add author id to request
} elseif ( $queried_object instanceof WP_User ) {
	$query_args['author'] = get_queried_object_id();
}

/**
 * Adds search params to query_args
 *
 * @param array $query_args The query arguments
 */
$func_add_search_params_to_query_args = function ( &$query_args ) {
	if ( $search_query = get_search_query() ) {
		$query_args['s'] = trim( $search_query );
	}
};

$output_items = '';

foreach ( $filter_items as $filter_item ) {
	if ( empty( $filter_item['source'] ) ) {
		continue;
	}

	$source = $filter_item['source'];

	extract(
		array_combine(
			array( 'item_type', 'item_name' ),
			explode( '|', $source, 2 )
		)
	);

	$ui_type = $filter_item['ui_type'];
	$item_values = $terms_parent = array();
	$taxonomy_obj = NULL;

	$item_title = ( ! empty( $filter_item['label'] ) ) ? $filter_item['label'] : '';

	// Total number of related posts for the current item
	$item_posts_count = 0;

	// The total number of values for the current item (like terms of taxonomy)
	$item_values_count = 0;

	// Processing Taxonomies filters
	if ( $item_type === 'tax' ) {
		$taxonomy_obj = get_taxonomy( $item_name );

		// Check if the item is a WooCommerce Product attribute and get its title in this case
		if (
			empty( $item_title )
			AND strpos( us_strtolower( $item_name ), 'pa_' ) === 0
			AND function_exists( 'wc_attribute_label' )
		) {
			$item_title = (string) wc_attribute_label( $item_name );
		}

		// If item title is empty, use singular name of taxonomy for it
		if ( empty( $item_title ) AND $taxonomy_obj instanceof WP_Taxonomy ) {
			$item_title = $taxonomy_obj->labels->singular_name;
		}

		$terms_query_args = array(
			'hide_empty' => FALSE,
			'hierarchical' => TRUE,
			'taxonomy' => $item_name,
		);

		// Exclude current taxonomies from output if filter is set on a taxonomy page
		if ( $queried_object instanceof WP_Term AND $queried_object->taxonomy === $item_name ) {
			$terms_query_args['child_of'] = $queried_object->term_id;
		}

		// Populate values with terms of taxonomy
		$item_values = get_terms( $terms_query_args );


		// get_terms() might return an error or might be empty so skip further execution if it's the case
		if ( ! is_array( $item_values ) OR empty( $item_values ) ) {
			continue;
		}


		// Set 'inherit' post_type for attachments
		if ( is_string( $taxonomy_obj->object_type ) ) {
			$taxonomy_obj->object_type = array( $taxonomy_obj->object_type );
		};
		if ( in_array( 'attachment', $taxonomy_obj->object_type ) ) {
			$query_args['post_status'] = 'inherit';
		}

		// Define parent terms to display terms hierarchy
		foreach ( $item_values as $index => $term ) {

			// Get the number of entries for a taxonomy
			$item_query_args = $query_args;

			if (
				! empty( $item_query_args['tax_query'] )
				AND is_array( $item_query_args['tax_query'] )
			) {
				foreach ( $item_query_args as &$tax_query ) {
					if (
						us_arr_path( $tax_query, 'taxonomy' ) === $term->taxonomy
						AND us_arr_path( $tax_query, 'field' ) == 'slug'
					) {
						$terms = &$tax_query['terms'];
						if ( ! is_array( $terms ) ) {
							$terms = array( $terms );
						}
						$terms = array_merge( $terms, array( $term->slug ) );
						$terms = array_unique( $terms );
					}
				}

			}

			if ( empty( $terms ) ) {
				// Overriding indexes for archived taxonomies as further duplicates will be removed
				$item_index = isset( $item_query_args['tax_query'] )
					? count( $item_query_args['tax_query'] )
					: 0;
				if ( $queried_object instanceof WP_Term AND $queried_object->taxonomy == $term->taxonomy ) {
					$item_index = 0;
				}
				$item_query_args['tax_query'][ $item_index ] = array(
					'taxonomy' => $term->taxonomy,
					'field' => 'slug',
					'terms' => $term->slug,
				);
			}
			unset( $tax_query, $terms );

			// Add search params to a condition
			$func_add_search_params_to_query_args( $item_query_args );

			// Get the count of items for a term
			$item_values[ $index ]->count = us_grid_filter_get_count_items( $item_query_args, $selected_taxonomies );
			$item_posts_count += $item_values[ $index ]->count;

			// Saving data to send to the JS component
			$filters_args['taxonomies_query_args'][ $source ][ urlencode( $term->slug ) ] = $item_query_args;

			if ( $term instanceof WP_Term ) {
				$terms_parent[ $term->term_id ] = $term->parent;
			}
		}


		// Sort the terms with parents regarding hierarchy
		$start_parent = ! empty( $terms_query_args['child_of'] )
			? $terms_query_args['child_of']
			: 0;
		$item_values = $func_sort_terms( $item_values, (int) $start_parent );

		// Processing Custom Fields filters
	} elseif ( $item_type === 'cf' ) {

		if ( $item_name === '_price' AND ! class_exists( 'woocommerce' ) ) {
			continue;
		}

		// ACF
		if ( function_exists( 'acf_get_field' ) AND $acf_field = acf_get_field( $item_name ) ) {

			// Add a unique ID to the item name and source
			$filter_item['source'] = strtolower( $filter_item['source'] . '_' . $acf_field['ID'] );
			$item_name .= '_' . $acf_field['ID'];
			$source .= '_' . $acf_field['ID'];

			// Define Title from ACF field
			if ( empty( $item_title ) ) {
				$item_title = $acf_field['label'];
			}

			// Populate values with relevant ACF fields values
			if ( in_array( $acf_field['type'], array( 'select', 'checkbox', 'radio' ) ) ) {
				foreach ( us_arr_path( $acf_field, 'choices', array() ) as $option_key => $option_name ) {

					$acf_slug = preg_replace( '/\s/', '_', us_strtolower( $option_key ) );

					// Get the number of entries for a ACF
					$item_query_args = $query_args;

					// Name to lowercase
					if ( $acf_field_name = us_arr_path( $acf_field, 'name', NULL ) ) {
						$acf_field_name = strtolower( (string) $acf_field_name );
					}

					$item_query_args['meta_query'][] = array(
						'relation' => 'OR',
						array(
							'key' => $acf_field_name,
							'value' => '"' . $option_key . '"',
							'compare' => 'LIKE',
							'type' => 'CHAR',
						),
						array(
							'key' => $acf_field_name,
							'value' => '"' . $option_name . '"',
							'compare' => 'LIKE',
							'type' => 'CHAR',
						),
						array(
							'key' => $acf_field_name,
							'value' => array( $option_key, $option_name ),
							'compare' => 'IN',
							'type' => 'CHAR',
						),
					);

					// Add search params to a condition
					$func_add_search_params_to_query_args( $item_query_args );

					// Saving data to send to the JS component
					$filters_args['taxonomies_query_args'][ $source ][ urlencode( $acf_slug ) ] = $item_query_args;

					// Get the count of items for a ACF Field
					$count_items = (int) us_grid_filter_get_count_items( $item_query_args );

					$item_values[] = ( object ) array(
						'name' => $option_name,
						'slug' => $acf_slug,
						'parent' => 0,
						'count' => $count_items,
					);

					$item_posts_count += $count_items;
				}
			}
		}

		// Add a title if it is not in the settings
		if ( empty( $item_title ) AND $item_name === '_price' ) {
			$item_title = us_translate( 'Price', 'woocommerce' );
		}

	} else {
		continue;
	}

	// If there are no records, skip the item, unless it is a range filter
	if ( ! $item_posts_count AND $ui_type !== 'range' ) {
		continue;
	}

	// Checking portfolio slugs and replacing default
	if (
		$option_key = us_arr_path( $portfolio_slugs, $item_name )
		AND $new_item_name = us_get_option( $option_key, $item_name )
		AND $item_name !== "us_" . $new_item_name // Checking a `$new_item_name` from a prefix
	) {
		$item_name = $new_item_name;
	}

	$item_atts = array(
		'class' => 'w-filter-item',
		'data-source' => "$item_type|$item_name",
		'data-ui_type' => $ui_type,
	);

	$input_name = sprintf( '%s_%s', $filter_url_prefix, $item_name );

	// Separate variable for the item's HTML
	$output_item_content = '';


	// Checkboxes and Radio Buttons semantics
	if ( in_array( $ui_type, array( 'checkbox', 'radio' ) ) AND ! empty( $item_values ) ) {

		// Add "All" radio button
		if ( $ui_type == 'radio' AND ! empty( $filter_item['show_all_value'] ) ) {
			$selected_all_value = '';

			if (
				empty( $filter_taxonomies[ $filter_item['source'] ] )
				OR (
					! empty( $filter_taxonomies[ $filter_item['source'] ] )
					AND in_array( '*' /* All */, $filter_taxonomies[ $filter_item['source'] ] )
				)
			) {
				$selected_all_value = ' selected';
			}

			$all_value_atts = array(
				'class' => 'screen-reader-text',
				'type' => 'radio',
				'value' => '*',
				'name' => $input_name,
			);

			$output_item_content .= '<a class="w-filter-item-value' . $selected_all_value . '" href="javascript:void(0);">';
			$output_item_content .= '<label>';
			$output_item_content .= '<input' . us_implode_atts( $all_value_atts ) . checked( $selected_all_value, ' selected', FALSE ) . '>';
			$output_item_content .= '<span class="w-form-radio"></span>';
			$output_item_content .= '<span class="w-filter-item-value-label">' . __( 'All', 'us' ) . '</span>';
			$output_item_content .= '</label>';
			$output_item_content .= '</a>';
		}

		$item_values_counter = 0;

		foreach ( $item_values as $item_value ) {

			// Skip taxonomies that do not have entries
			if ( empty( $item_value->count ) ) {
				continue;
			}

			$item_value_slug = urlencode( $item_value->slug );

			// Mark selected item values
			$selected_value = '';
			if (
				! empty ( $filter_taxonomies[ $filter_item['source'] ] )
				AND (
					// For checkboxes
					(
						is_array( $filter_taxonomies[ $filter_item['source'] ] )
						AND in_array( $item_value->slug, $filter_taxonomies[ $filter_item['source'] ] )
					)
					OR
					// For radio buttons
					(
						is_string( $filter_taxonomies[ $filter_item['source'] ] )
						AND $item_value->slug == $filter_taxonomies[ $filter_item['source'] ]
					)
				)
			) {
				$selected_value = ' selected';
				$item_values_counter ++;
			}

			if ( $ui_type == 'radio' and $item_values_counter > 1 ) {
				$selected_value = '';
			}

			// Determine which ones to hide based on filters
			$disabled = FALSE;
			if ( ! empty( $filters_args['taxonomies_query_args'][ $source ][ $item_value_slug ] ) ) {
				$item_query_args = $filters_args['taxonomies_query_args'][ $source ][ $item_value_slug ];
				us_apply_grid_filters( NULL, $item_query_args );

				$item_value->count = us_grid_filter_get_count_items( $item_query_args );
				$disabled = ! $item_value->count;
			}

			$item_value_atts = array(
				'class' => 'w-filter-item-value' . $selected_value,
				'data-item-amount' => (int) $item_value->count,
				'href' => 'javascript:void(0);',
				'tabindex' => '-1',
			);

			// Define hierarchy depth of every term
			if ( ! empty( $terms_parent ) AND $parent = $item_value->parent ) {
				$item_value_atts['class'] .= ' depth_' . $func_get_depth( $parent, $terms_parent );
			}

			if ( $disabled ) {
				$item_value_atts['class'] .= ' disabled';
				// If the parameter is disabled, then it cannot be selected we remove the choice.
				$selected_value = '';
			}

			// Output filter item values
			$item_value_html = '<a' . us_implode_atts( $item_value_atts ) . '>';
			$item_value_html .= '<label>';
			$input_atts = array(
				'class' => 'screen-reader-text',
				'aria-hidden' => 'true',
				'type' => $ui_type,
				'value' => $item_value_slug,
				'name' => $input_name,
			);

			if ( $disabled ) {
				$input_atts['disabled'] = 'disabled';
			} else {
				$item_values_count ++;
			}

			$item_value_html .= '<input' . us_implode_atts( $input_atts ) . checked( $selected_value, ' selected', FALSE ) . '>';
			$item_value_html .= '<span class="w-form-' . $ui_type . '"></span>';
			$item_value_html .= '<span class="w-filter-item-value-label">' . strip_tags( $item_value->name ) . '</span>';

			// Show amount of relevant posts
			if ( ! empty( $filter_item['show_amount'] ) ) {
				$item_value_html .= '<span class="w-filter-item-value-amount">' . $item_value->count . '</span>';
			}
			$item_value_html .= '</label>';
			$item_value_html .= '</a>';

			/**
			 * Allows to adjust filter items values output
			 *
			 * @param string $item_value_html Original HTML semantics for Filter item value
			 * @param object $item_value Object with item value's params
			 */
			$output_item_content .= apply_filters( 'us_grid_filter_item_value_html', $item_value_html, $item_value );
		}

		// Number Range semantics
	} elseif ( $ui_type === 'range' ) {

		$input_min_atts = array(
			'class' => 'w-filter-item-value-input type_min',
			'aria-label' => __( 'Min', 'us' ),
			'placeholder' => __( 'Min', 'us' ),
			'type' => 'text',
		);
		$input_max_atts = array(
			'class' => 'w-filter-item-value-input type_max',
			'aria-label' => __( 'Max', 'us' ),
			'placeholder' => __( 'Max', 'us' ),
			'type' => 'text',
		);
		$input_hidden_atts = array(
			'type' => 'hidden',
			'name' => $input_name,
			'value' => '',
		);

		// Get and set value
		if (
			! empty( $filter_taxonomies[ $filter_item['source'] ] )
			AND $value = us_arr_path( $filter_taxonomies, $filter_item['source'] . '.0', '' )
		) {
			$input_hidden_atts['value'] = $value;
			if ( preg_match( '/(\d+)-(\d+)/', $value, $matches ) ) {
				$input_min_atts['value'] = $matches[1];
				$input_max_atts['value'] = $matches[2];
			}
		}

		// Get MIN and MAX values to show in placeholders
		if ( $item_type === 'cf' ) {
			$range_placeholders = array();

			// Check ACF fields for predefined Min, Max parameters
			if ( ! empty( $acf_field ) ) {
				if ( $min = us_arr_path( $acf_field, 'min', FALSE ) ) {
					$range_placeholders['min'] = $min;
				}
				if ( $max = us_arr_path( $acf_field, 'max', FALSE ) ) {
					$range_placeholders['max'] = $max;
				}
			}
			// Get values from the database
			if ( empty( $range_placeholders ) OR count( $range_placeholders ) !== 2 ) {

				// Get real item name without ID for ACF
				$param = us_grid_filter_parse_param( $filter_item['source'] );
				$real_item_name = us_arr_path( $param, 'param_name', $item_name );

				// Get min max prices of products, taking into account tax etc.
				if ( $real_item_name === '_price' ) {
					$min_max_price_query_vars = array(
						'tax_query' => us_arr_path( $query_args, 'tax_query', array() ),
						'meta_query' => us_arr_path( $query_args, 'meta_query', array() ),
					);
					us_apply_grid_filters( NULL, $min_max_price_query_vars );
					$range_placeholders = ( array ) us_wc_get_min_max_price( $min_max_price_query_vars );

					// Enable get min and max prices when changing filters
					$filters_args['wc_min_max_price'] = TRUE;

					// Get other ranges
				} else {
					global $wpdb;

					$range_placeholders = (array) $wpdb->get_row(
						"
						SELECT
							MIN( cast( meta_value as UNSIGNED ) ) AS min,
							MAX( cast( meta_value as UNSIGNED ) ) AS max
						FROM {$wpdb->postmeta}
						WHERE
							meta_key = " . $wpdb->prepare( '%s', $real_item_name ) . "
							AND meta_value > 0
						LIMIT 1;
					"
					);
				}
			}
			foreach ( $range_placeholders as $key => $value ) {
				if ( ! in_array( $key, array( 'min', 'max' ) ) OR empty( $value ) ) {
					continue;
				}
				$variable_atts = 'input_' . $key . '_atts';
				$$variable_atts['placeholder'] = $value;
			}
		}

		$output_item_content .= '<input' . us_implode_atts( $input_min_atts ) . '>';
		$output_item_content .= '<input' . us_implode_atts( $input_max_atts ) . '>';
		$output_item_content .= '<input' . us_implode_atts( $input_hidden_atts ) . '>';

		$item_values_count ++;

		// Dropdown list
	} elseif ( $ui_type === 'dropdown' ) {

		$select_atts = array(
			'class' => 'w-filter-item-value-select',
			'name' => $input_name,
		);
		$select_options = '<option value="">' . __( 'All', 'us' ) . '</option>';

		foreach ( $item_values as $item_value ) {

			// Skip taxonomies that do not have entries
			if ( empty( $item_value->count ) ) {
				continue;
			}

			$item_value_slug = urlencode( $item_value->slug );

			$option_atts = array(
				'value' => $item_value_slug,
				'class' => '',
			);

			// Define hierarchy depth of every term
			if (
				! empty( $terms_parent )
				AND $parent = $item_value->parent
				AND $option_depth = ( $func_get_depth( $parent, $terms_parent ) - 1 )
			) {
				// Prepend non-breaking spaces for visual hierarchy
				$option_depth = implode( '', array_fill( 0, $option_depth, html_entity_decode( '&nbsp;&nbsp;&nbsp;' ) ) );
				$item_value->name = $option_depth . $item_value->name;
			}

			// Mark selected item values
			if (
				! empty ( $filter_taxonomies[ $filter_item['source'] ] )
				AND (
					// For checkboxes
					(
						is_array( $filter_taxonomies[ $filter_item['source'] ] )
						AND in_array( $item_value->slug, $filter_taxonomies[ $filter_item['source'] ] )
					)
					OR
					// For radio buttons
					(
						is_string( $filter_taxonomies[ $filter_item['source'] ] )
						AND $item_value->slug == $filter_taxonomies[ $filter_item['source'] ]
					)
				)
			) {
				$option_atts['selected'] = 'selected';
			}

			// Determine which ones to hide based on filters
			if ( ! empty( $filters_args['taxonomies_query_args'][ $source ][ $item_value_slug ] ) ) {
				$item_query_args = $filters_args['taxonomies_query_args'][ $source ][ $item_value_slug ];
				us_apply_grid_filters( NULL, $item_query_args );

				$item_value->count = us_grid_filter_get_count_items( $item_query_args );
				if ( ! $item_value->count ) {
					$option_atts['disabled'] = 'disabled';
					$option_atts['class'] .= ' disabled';
				} else {
					$item_values_count ++;
				}
			}

			// Show amount of relevant posts
			if ( ! empty( $filter_item['show_amount'] ) ) {
				$option_atts['data-template'] = $item_value->name . ' %s';
				if ( $item_value->count ) {
					$item_value->name .= ' ' . $item_value->count;
				}
			}

			$select_options .= '<option' . us_implode_atts( $option_atts ) . '>' . strip_tags( $item_value->name ) . '</option>';
		}

		$output_item_content .= '<select' . us_implode_atts( $select_atts ) . '>' . $select_options . '</select>';
	}

	// If set "Hide unavailable values" and there are no values, disable the filter item
	if ( $hide_disabled_values AND ! $item_values_count ) {
		$item_atts['class'] .= ' disabled';
	}

	// Output filter item
	$output_items .= '<div' . us_implode_atts( $item_atts ) . '>';
	$output_items .= '<a class="w-filter-item-title" href="javascript:void(0);">';
	$output_items .= strip_tags( $item_title );
	$output_items .= '<span></span></a>';

	// Output "Reset" filter item link
	$output_items .= '<a class="w-filter-item-reset" href="javascript:void(0);" title="' . esc_attr( __( 'Reset', 'us' ) ) . '">';
	$output_items .= '<span>' . strip_tags( __( 'Reset', 'us' ) ) . '</span>';
	$output_items .= '</a>';

	// Output filter item values
	$item_values_atts = array(
		'class' => 'w-filter-item-values',
		'data-maxheight' => $values_max_height,
	);
	if ( ! empty( $values_max_height ) ) {
		$item_values_atts['style'] = 'max-height:' . $values_max_height;
	}
	$output_items .= '<div' . us_implode_atts( $item_values_atts ) . '>';
	$output_items .= $output_item_content;
	$output_items .= '</div>';
	$output_items .= '</div>';
}

$output .= $output_items;
$output .= '</div>'; // w-filter-list

// Add Mobiles related button and styles
if ( ! empty( $mobile_width ) AND $output_items !== '' ) {
	$output .= '<div class="w-filter-list-panel">';
	$output .= '<a class="w-btn us-btn-style_1" href="javascript:void(0);">';
	$output .= '<span class="w-btn-label">' . strip_tags( us_translate( 'Apply' ) ) . '</span>';
	$output .= '</a>';
	$output .= '</div>';

	$mobile_button_atts = array(
		'class' => 'w-filter-opener',
		'href' => 'javascript:void(0);',
	);

	// Make link as Button if set
	if ( ! empty( $mobile_button_style ) ) {
		$mobile_button_atts['class'] .= ' w-btn us-btn-style_' . $mobile_button_style;
	}

	// Icon
	$mobile_button_icon_html = '';
	if ( ! empty( $mobile_button_icon ) ) {
		$mobile_button_icon_html = us_prepare_icon_tag( $mobile_button_icon );
		$mobile_button_atts['class'] .= ' icon_at' . $mobile_button_iconpos;

		// Swap icon position for RTL
		if ( is_rtl() ) {
			$mobile_button_iconpos = ( $mobile_button_iconpos == 'left' ) ? 'right' : 'left';
		}
	}

	// Add aria-label when label is empty
	if ( empty( $mobile_button_label ) ) {
		$mobile_button_atts['class'] .= ' text_none';
		$mobile_button_atts['aria-label'] = __( 'Filters', 'us' );
	}

	$style = '@media( max-width:' . (int) $mobile_width . 'px ) {';
	$style .= '.w-filter.state_desktop .w-filter-list,';
	$style .= '.w-filter-item-title > span { display: none; }';
	$style .= '.w-filter-opener { display: inline-block; }';
	$style .= '}';

	$output .= '<style>' . us_minify_css( $style ) . '</style>';
	$output .= '<a' . us_implode_atts( $mobile_button_atts ) . '>';
	if ( $mobile_button_iconpos == 'left' ) {
		$output .= $mobile_button_icon_html;
	}
	$output .= '<span>' . strip_tags( $mobile_button_label ) . '</span>';
	if ( $mobile_button_iconpos == 'right' ) {
		$output .= $mobile_button_icon_html;
	}
	$output .= '</a>';
}

if ( ! empty( $filters_args ) ) {
	$output .= '<div class="w-filter-json-filters-args hidden"' . us_pass_data_to_js( $filters_args ) . '></div>';
}

$output .= '</form>';

echo $output;
