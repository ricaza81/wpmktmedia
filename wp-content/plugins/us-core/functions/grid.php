<?php
/**
 * All methods that apply to Grid, Grid Filter, Grid Order
 *
 */

if ( ! function_exists( 'us_get_grid_url_prefix' ) ) {
	/**
	 * Get URL param for grid_* shortcodes
	 *
	 * @param string $param_name
	 * @return string - If there is no value then the `param_name` will be returned as the default value
	 */
	function us_get_grid_url_prefix( $param_name ) {
		$value = (string) us_get_option( "grid_{$param_name}_url_prefix", $param_name );
		// The checking the parameter and leave only valid characters for the URL
		$value = preg_replace( '/[^a-z\d\_\-]+/', '', us_strtolower( $value ) );

		return ! empty( $value )
			? $value
			: $param_name;
	}
}

if ( ! function_exists( 'us_post_type_is_available' ) ) {
	/**
	 * Check if post type is available for usage in Grid
	 *
	 * @param string|array $post_types
	 * @param array $available_post_types
	 * @return bool
	 */
	function us_post_type_is_available( $post_types, $available_post_types = array() ) {
		if ( empty( $post_types ) OR empty( $available_post_types ) ) {
			return FALSE;
		}

		if ( is_string( $post_types ) ) {
			$post_types = array( $post_types );
		}

		foreach ( $post_types as $post_type ) {
			if ( in_array( $post_type, $available_post_types ) ) {
				return TRUE;
			}
		}

		return FALSE;
	}
}

if ( ! function_exists( 'us_grid_query_offset' ) ) {
	/**
	 * Grid function
	 */
	function us_grid_query_offset( &$query ) {
		if ( ! isset( $query->query['_id'] ) OR $query->query['_id'] !== 'us_grid' ) {
			return;
		}

		global $us_grid_items_offset;

		$posts_per_page = ( ! empty( $query->query['posts_per_page'] ) )
			? $query->query['posts_per_page']
			: get_option( 'posts_per_page' );

		if ( $query->is_paged ) {
			$page_offset = $us_grid_items_offset + ( ( $query->query_vars['paged'] - 1 ) * $posts_per_page );

			// Apply adjust page offset
			$query->set( 'offset', $page_offset );

		} else {
			// This is the first page. Just use the offset...
			$query->set( 'offset', $us_grid_items_offset );

		}

		remove_action( 'pre_get_posts', 'us_grid_query_offset' );
	}
}

if ( ! function_exists( 'us_grid_adjust_offset_pagination' ) ) {
	/**
	 * Grid function
	 */
	function us_grid_adjust_offset_pagination( $found_posts, $query ) {
		if ( ! isset( $query->query['_id'] ) OR $query->query['_id'] !== 'us_grid' ) {
			return $found_posts;
		}

		global $us_grid_items_offset;
		remove_filter( 'found_posts', 'us_grid_adjust_offset_pagination' );

		// Reduce WordPress's found_posts count by the offset...
		return $found_posts - $us_grid_items_offset;
	}
}

if ( ! function_exists( 'us_get_available_post_statuses' ) ) {
	/**
	 * Get a list of available statuses to display
	 * @return array
	 */
	function us_get_available_post_statuses() {
		$post_statuses = get_post_stati( array( 'publicly_queryable' => TRUE ), /* output */ 'names' );
		$post_statuses = array_keys( $post_statuses );

		// If private posts are available, also add to the list
		if ( is_user_logged_in() AND current_user_can( 'read_private_posts' ) ) {
			$post_statuses[] = 'private';
		}

		// List of additional statuses
		$post_statuses = array_merge(
			$post_statuses, array(
				'inherit',
			)
		);

		return array_unique( $post_statuses );
	}
}

if ( ! function_exists( 'us_fix_grid_settings' ) ) {
	/**
	 * Make the provided grid settings value consistent and proper
	 *
	 * @param $value array
	 *
	 * @return array
	 */
	function us_fix_grid_settings( $value ) {
		if ( empty( $value ) OR ! is_array( $value ) ) {
			$value = array();
		}
		if ( ! isset( $value['data'] ) OR ! is_array( $value['data'] ) ) {
			$value['data'] = array();
		}

		$options_defaults = array();
		$elements_defaults = array();
		if ( function_exists( 'usof_get_default' ) ) {
			foreach ( us_config( 'grid-settings.options', array() ) as $option_name => $option_group ) {
				foreach ( $option_group as $option_name => $option_field ) {
					$options_defaults[ $option_name ] = usof_get_default( $option_field );
				}
			}

			foreach ( us_config( 'grid-settings.elements', array() ) as $element_name ) {
				$element_settings = us_config( 'elements/' . $element_name );
				$elements_defaults[ $element_name ] = array();
				foreach ( $element_settings['params'] as $param_name => $param_field ) {
					$elements_defaults[ $element_name ][ $param_name ] = usof_get_default( $param_field );
				}
			}
		}

		foreach ( $options_defaults as $option_name => $option_default ) {
			if ( ! isset( $value['default']['options'][ $option_name ] ) ) {
				$value['default']['options'][ $option_name ] = $option_default;
			}
		}
		foreach ( $value['data'] as $element_name => $element_values ) {
			$element_type = strtok( $element_name, ':' );
			if ( ! isset( $elements_defaults[ $element_type ] ) ) {
				continue;
			}
			foreach ( $elements_defaults[ $element_type ] as $param_name => $param_default ) {
				if ( ! isset( $value['data'][ $element_name ][ $param_name ] ) ) {
					$value['data'][ $element_name ][ $param_name ] = $param_default;
				}
			}
		}

		foreach ( array( 'default' ) as $state ) {
			if ( ! isset( $value[ $state ] ) OR ! is_array( $value[ $state ] ) ) {
				$value[ $state ] = array();
			}
			if ( ! isset( $value[ $state ]['layout'] ) OR ! is_array( $value[ $state ]['layout'] ) ) {
				if ( $state != 'default' AND isset( $value['default']['layout'] ) ) {
					$value[ $state ]['layout'] = $value['default']['layout'];
				} else {
					$value[ $state ]['layout'] = array();
				}
			}
			$state_elms = array();
			foreach ( $value[ $state ]['layout'] as $place => $elms ) {
				if ( ! is_array( $elms ) ) {
					$elms = array();
				}
				foreach ( $elms as $index => $elm_id ) {
					if ( ! is_string( $elm_id ) OR strpos( $elm_id, ':' ) == -1 ) {
						unset( $elms[ $index ] );
					} else {
						$state_elms[] = $elm_id;
						if ( ! isset( $value['data'][ $elm_id ] ) ) {
							$value['data'][ $elm_id ] = array();
						}
					}
				}
				$value[ $state ]['layout'][ $place ] = array_values( $elms );
			}
			if ( ! isset( $value[ $state ]['layout']['hidden'] ) OR ! is_array( $value[ $state ]['layout']['hidden'] ) ) {
				$value[ $state ]['layout']['hidden'] = array();
			}
			$value[ $state ]['layout']['hidden'] = array_merge( $value[ $state ]['layout']['hidden'], array_diff( array_keys( $value['data'] ), $state_elms ) );
			// Fixing options
			if ( ! isset( $value[ $state ]['options'] ) OR ! is_array( $value[ $state ]['options'] ) ) {
				$value[ $state ]['options'] = array();
			}
			$value[ $state ]['options'] = array_merge( $options_defaults, ( $state != 'default' ) ? $value['default']['options'] : array(), $value[ $state ]['options'] );
		}

		return $value;
	}
}

if ( ! function_exists( 'us_grid_available_post_types' ) ) {
	/**
	 * Get post types for selection in Grid element
	 *
	 * @param bool $reload used when list of available post types should be reloaded
	 * because data that affects it was changed
	 *
	 * @return array
	 */
	function us_grid_available_post_types( $reload = FALSE ) {
		static $available_posts_types = array();

		if ( empty( $available_posts_types ) OR $reload ) {
			$posts_types_params = array(
				'show_in_menu' => TRUE,
			);
			$skip_post_types = array(
				'us_header',
				'us_page_block',
				'us_content_template',
				'us_grid_layout',
				'shop_order',
				'shop_coupon',
			);
			foreach ( get_post_types( $posts_types_params, 'objects' ) as $post_type_name => $post_type ) {
				if ( in_array( $post_type_name, $skip_post_types ) ) {
					continue;
				}
				$available_posts_types[ $post_type_name ] = $post_type->labels->name . ' (' . $post_type_name . ')';
			}
		}

		return apply_filters( 'us_grid_available_post_types', $available_posts_types );
	}
}

if ( ! function_exists( 'us_grid_available_post_types_for_import' ) ) {
	/**
	 * Get post types for selection in Grid element for import
	 * NOTE: Used when filtering imported shortcodes.
	 *
	 * @return array
	 */
	function us_grid_available_post_types_for_import() {
		// These types shoudn't be replaced to posts
		$grid_available_post_types = array(
			'attachment',
			'related',
			'current_query',
			'taxonomy_terms',
			'current_child_terms',
			'product_upsells',
			'product_crosssell',
		);
		// Get post types for selection in Grid element
		foreach ( array_keys( us_grid_available_post_types() ) as $post_type ) {
			if ( wp_count_posts( $post_type )->publish ) {
				$grid_available_post_types[] = $post_type;
			}
		}

		return $grid_available_post_types;
	}
}

if ( ! function_exists( 'us_grid_available_taxonomies' ) ) {
	/**
	 * Get post taxonomies for selection in Grid element
	 *
	 * @return array
	 */
	function us_grid_available_taxonomies() {
		$available_taxonomies = array();
		$available_posts_types = us_grid_available_post_types();

		foreach ( $available_posts_types as $post_type => $name ) {
			$post_taxonomies = array();
			$object_taxonomies = get_object_taxonomies( $post_type, 'objects' );
			foreach ( $object_taxonomies as $tax_object ) {
				if ( ( $tax_object->public ) AND ( $tax_object->show_ui ) ) {
					$post_taxonomies[] = $tax_object->name;
				}
			}
			if ( is_array( $post_taxonomies ) AND count( $post_taxonomies ) > 0 ) {
				$available_taxonomies[ $post_type ] = array();
				foreach ( $post_taxonomies as $post_taxonomy ) {
					$available_taxonomies[ $post_type ][] = $post_taxonomy;
				}
			}
		}

		return $available_taxonomies;
	}
}

if ( ! function_exists( 'us_get_filter_taxonomies' ) ) {
	/**
	 * Get grid filter params
	 * @param string|array $prefixes
	 * @param string|array $params (Example: {prefix}_{param}={values}&...)
	 *
	 * @return array
	 */
	function us_get_filter_taxonomies( $prefixes = array(), $params = '' ) {
		// Parameters to check
		$prefixes = is_array( $prefixes )
			? $prefixes
			: array( $prefixes );

		// The resulting parameters as a string or array
		if ( ! empty( $params ) AND is_string( $params ) ) {
			parse_str( $params, $params );
		} else {
			// Get default params
			$params = $_REQUEST;
		}

		// Get all taxonomies
		$available_taxonomy = array();
		foreach ( array_keys( us_get_taxonomies( FALSE, TRUE, '' ) ) as $tax_name ) {
			$available_taxonomy[ $tax_name ] = 'tax';
		}

		// Add WooCommerce related fields
		$available_taxonomy['_price'] = 'cf';

		// Add fields from "Advanced Custom Fields" plugin
		if ( function_exists( 'acf_get_fields' ) ) {
			foreach ( $params as $param_name => $param_value ) {
				if ( ! preg_match( '/(\w+)_(\d+)$/', $param_name, $matches ) ) {
					continue;
				}
				if ( $acf_field = acf_get_field( $matches[ /* ACF Field ID */ 2] ) ) {
					$available_taxonomy[ sprintf( '%s_%s', $acf_field['name'], $acf_field['ID'] ) ] = 'cf';
				}
			}
		}

		$result = array();
		static $_terms = array();

		// Get slugs from portfolio settings
		$portfolio_slugs = array();
		foreach ( us_get_portfolio_slugs_map() as $default_slug => $option_name ) {
			if (
				$slug = us_get_option( $option_name, $default_slug )
				AND $default_slug !== "us_" . $slug // Checking a `$slug` from a prefix
				AND $default_slug !== $slug
			) {
				$portfolio_slugs[ $slug ] = $default_slug;
			}
		}

		foreach ( $prefixes as $prefix ) {
			foreach ( $params as $param => $param_values ) {
				$param = strtolower( $param );
				if ( strpos( $param, $prefix ) !== 0 ) {
					continue;
				}

				// Remove prefix and get parameter name
				$param_name = substr( $param, strlen( $prefix . /* Separator */ '_' ) );

				// Check the paran_name in the portfolio slugs
				if (
					isset( $portfolio_slugs[ $param_name ] )
					AND $new_param_name = us_arr_path( $portfolio_slugs, $param_name, $param_name )
				) {
					$param_name = $new_param_name;
				}

				if ( ! empty( $available_taxonomy[ $param_name ] ) ) {
					$source_prefix = $available_taxonomy[ $param_name ];
				} else {
					continue;
				}

				// The taxonomy validation
				if ( $source_prefix === 'tax' ) {
					if ( ! isset( $_terms[ $param_name ] ) ) {
						$terms_query = array(
							'taxonomy' => $param_name,
							'hide_empty' => TRUE,
						);
						foreach ( get_terms( $terms_query ) as $term ) {
							$_terms[ $param_name ][ $term->term_id ] = $term->slug;
						}
					}
					if ( empty( $_terms[ $param_name ] ) OR ! is_string( $param_values ) ) {
						continue;
					}
				}

				// Formation of an array of parameters
				$param_values = explode( ',', $param_values );
				array_map( 'strtolower', $param_values );
				array_map( 'trim', $param_values );
				foreach ( $param_values as $item_value ) {
					if (
						(
							! empty( $_terms[ $param_name ] )
							AND in_array( $item_value, $_terms[ $param_name ] )
						)
						OR ! empty( $item_value )
					) {
						$result[ $source_prefix . '|' . $param_name ][] = ( string ) urldecode( $item_value );
					}
				}
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'us_grid_filter_parse_param' ) ) {
	/**
	 * Parse param for grid filter
	 *
	 * @param string $param_name
	 * @return array
	 */
	function us_grid_filter_parse_param( $param_name ) {
		$result = array();
		if ( strpos( $param_name, '|' ) !== FALSE ) {
			list( $source, $param_name ) = explode( '|', $param_name, 2 );
			$result['source'] = strtolower( $source );
			// The for Advanced Custom Fields
			if (
				$result['source'] === 'cf'
				AND $param_name !== '_price'
				AND preg_match( '/([\w+\-?]+)_(\d+)$/', $param_name, $matches )
			) {
				$result['param_name'] = $matches[1];
				$result['acf_field_id'] = (int) $matches[2];
			} else {
				$result['param_name'] = $param_name;
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'us_apply_grid_filters' ) ) {
	/**
	 * Apply grid filters to query_args
	 *
	 * @param ineger $post_id
	 * @param array $query_args
	 * @param string $grid_filter_params
	 */
	function us_apply_grid_filters( $post_id, &$query_args, $grid_filter_params = NULL ) {
		/**
		 * @var array
		 */
		$post_grid_filter_atts = array();

		/**
		 * Get grid filter and load attributes
		 * @param WP_Post $post
		 */
		$func_grid_filter_atts = function ( $post ) use ( &$post_grid_filter_atts ) {
			if (
				empty( $post_grid_filter_atts )
				AND $post instanceof WP_Post
				AND strpos( $post->post_content, '[us_grid_filter' ) !== FALSE
				AND ! $post_grid_filter_atts = get_post_meta( $post->ID, '_us_grid_filter_atts', TRUE )
			) {
				// Try to Save Grid Filter shortcode attributes if they weren't saved yet
				$post_grid_filter_atts = us_save_post_grid_filter_atts( $post->ID );
			}
		};

		// Recursively search for a grid filter on a page or in templates / page blocks
		$post = get_post( ( int ) $post_id );

		// The search on current page
		if ( is_callable( $func_grid_filter_atts ) ) {
			call_user_func( $func_grid_filter_atts, $post );
		}
		// The search on Page Blocks if they are on the page
		if ( ! empty( $func_grid_filter_atts ) ) {
			us_get_recursive_parse_page_block( $post, $func_grid_filter_atts );
		}
		// The search on templates
		if ( ! empty( $func_grid_filter_atts ) ) {
			foreach ( array( 'header', 'titlebar', 'sidebar', 'content', 'footer' ) as $area ) {
				if ( $area_id = get_post_meta( $post_id, sprintf( 'us_%s_id', $area ), TRUE ) ) {
					if ( $area_id === '__defaults__' ) {
						$area_id = us_get_option( sprintf( '%s_id', $area ) );
					}
					if ( is_numeric( $area_id ) ) {
						us_get_recursive_parse_page_block( get_post( (int) $area_id ), $func_grid_filter_atts );
					}
				}
			}
		}

		// If no filter params found, no grid filter is present at the page
		if ( $post_id AND empty( $post_grid_filter_atts ) ) {
			return;
		}

		$allowed_taxonomies = array();
		foreach ( $post_grid_filter_atts as $filter_atts ) {
			if ( ! empty( $filter_atts['source'] ) AND strpos( $filter_atts['source'], '|' ) !== FALSE ) {
				$filter_atts_source = explode( '|', $filter_atts['source'] );
				$allowed_taxonomies[] = us_arr_path( $filter_atts_source, '1', NULL );
			}
		}

		// Get grid filter params
		$filter_ranges = array();
		$filter_items = us_get_filter_taxonomies( us_get_grid_url_prefix( 'filter' ), $grid_filter_params );

		foreach ( $filter_items as $item_name => $item_values ) {
			if ( is_string( $item_values ) ) {
				$filter_items[ $item_name ] = array( $item_values );
			}

			// Skip numeric WooCommerce attributes
			if ( count( $item_values ) === 1 AND ( strpos( $item_name, 'tax|pa_' ) !== FALSE ) ) {
				continue;
			}

			// The for range values
			if ( count( $item_values ) === 1 AND preg_match( '/^(\d+)-(\d+)$/', $item_values[0], $matches ) ) {
				$filter_ranges[ $item_name ] = array( /* start value */
					$matches[1], /* end value */
					$matches[2],
				);
				unset( $filter_items[ $item_name ] );
			}
		}

		// Delete the filter by category for the store, this filter is in the tax_query
		if ( ! empty( $query_args['product_cat'] ) ) {
			unset( $query_args['product_cat'] );
		}

		$current_tax_queries = $current_acf_filters = $ranges = array();

		// Adding parameters from the filter to the query request
		if ( ! empty( $filter_items ) ) {
			foreach ( $filter_items as $item_name => $item_values ) {

				// Get param_name
				$param = us_grid_filter_parse_param( $item_name );
				$item_source = us_arr_path( $param, 'source' );
				$item_name = us_arr_path( $param, 'param_name', $item_name );

				if (
					in_array( '*', $item_values )
					OR (
						! empty( $post_id )
						AND ! in_array( $item_name, $allowed_taxonomies )
					)
				) {
					continue;
				}

				// The for taxonomies
				if ( $item_source === 'tax' ) {
					if ( ! isset( $current_tax_queries[ $item_name ] ) ) {
						$current_tax_queries[ $item_name ] = array();
					}
					$item_values = array_unique( array_merge( $current_tax_queries[ $item_name ], $item_values ) );
					$current_tax_queries[ $item_name ] = $item_values;


					// The for Advanced Custom Fields
				} elseif ( $item_source === 'cf' AND $item_name !== '_price' ) {
					$current_acf_filters[ $item_name ] = array(
						'field_id' => us_arr_path( $param, 'acf_field_id', NULL ),
						'values' => array_unique( $item_values ),
					);
				}
			}
		}

		// Creating conditions for taxonomies
		if ( empty( $query_args['tax_query'] ) AND ! empty( $current_tax_queries ) ) {
			$query_args['tax_query'] = array(
				'relation' => 'AND',
			);
		}
		foreach ( $current_tax_queries as $item_name => $item_values ) {
			$tax_query = array(
				'taxonomy' => $item_name,
				'field' => 'slug',
				'terms' => $item_values,
				'operator' => 'IN',
			);
			// At this stage, it is important to separate the is_int from is_number
			// The number in the string entry is the parameters from the filter
			if ( is_int( $item_values ) OR ( isset( $item_values[0] ) AND is_int( $item_values[0] ) ) ) {
				unset( $tax_query['field'] );
			}
			$query_args['tax_query'][] = $tax_query;
		}

		// If a category filter is installed on the category page, then delete `category_name`
		if ( ! empty( $current_tax_queries['category'] ) AND isset( $query_args['category_name'] ) ) {
			unset( $query_args['category_name'] );
		}

		if ( empty( $query_args['meta_query'] ) ) {
			$query_args['meta_query'] = array(
				'relation' => 'AND',
			);
		}

		// Creating conditions for ranges
		foreach ( $filter_ranges as $item_name => $item_values ) {
			$param = us_grid_filter_parse_param( $item_name );
			if ( us_arr_path( $param, 'source' ) !== 'cf' ) {
				continue;
			}

			$param_name = us_arr_path( $param, 'param_name', $item_name );
			if ( $param_name === '_price' ) {
				// Private param
				$query_args['_us_product_meta_lookup_prices'] = array(
					'min_price' => us_arr_path( $item_values, '0' ),
					'max_price' => us_arr_path( $item_values, '1' ),
				);
			} else {
				$meta_query = array(
					'key' => $param_name,
					'type' => 'NUMERIC',
				);

				if ( /* min */ $item_values[0] === 0 ) {
					$meta_query = array_merge(
						array(
							'value' => $item_values[1],
							'compare' => '<=',
						), $meta_query
					);
				} elseif ( /* max */ $item_values[1] == 0 ) {
					$meta_query = array_merge(
						array(
							'value' => $item_values[0],
							'compare' => '>=',
						), $meta_query
					);
				} else {
					$meta_query = array_merge(
						array(
							'value' => $item_values,
							'compare' => 'BETWEEN',
						), $meta_query
					);
				}
				$query_args['meta_query'][] = $meta_query;
			}
		}

		// Creating conditions for Advanced Custom Fields ( select, radio and checkboxes )
		foreach ( $current_acf_filters as $acf_field_name => $acf_item ) {
			if ( function_exists( 'acf_get_field' ) ) {
				$acf_values = array();
				$acf_field = acf_get_field( $acf_item['field_id'] );

				foreach ( array_keys( us_arr_path( $acf_field, 'choices', array() ) ) as $item ) {
					$item_key = preg_replace( '/\s/', '_', us_strtolower( $item ) );

					// Check the record type `value : label`
					if ( preg_match( '/(.*)\s:\s(.*)/', $item_key, $matches ) ) {
						$item_key = $matches[1];
					}

					if ( $item_key AND in_array( $item_key, us_arr_path( $acf_item, 'values', array() ) ) ) {
						$acf_values[] = $item;
					}
				}

				$acf_values = array_map( 'trim', $acf_values );
				$acf_values = array_unique( $acf_values );

				$meta_query = array( 'relation' => 'OR' );
				foreach ( $acf_values as $acf_value ) {
					$meta_query[] = array(
						'relation' => 'OR',
						array(
							'key' => $acf_field_name,
							'value' => '"' . $acf_value . '"',
							'compare' => 'LIKE',
							'type' => 'CHAR',
						),
						array(
							'key' => $acf_field_name,
							'value' => $acf_value,
							'compare' => '=',
							'type' => 'CHAR',
						),
					);
				}

				$query_args['meta_query'][] = $meta_query;
			}
		}
	}
}

if ( class_exists( 'woocommerce' ) AND ! function_exists( 'us_product_meta_lookup_prices' ) ) {
	/**
	 * Custom query used to filter products by price.
	 *
	 * @param array $args Query args.
	 * @param WC_Query $wp_query WC_Query object.
	 * @return array
	 */
	function us_product_meta_lookup_prices( $args, $wp_query ) {
		if ( empty( $wp_query->query_vars['_us_product_meta_lookup_prices'] ) ) {
			return $args;
		}

		$prices = $wp_query->query_vars['_us_product_meta_lookup_prices'];
		unset( $wp_query->query_vars['_us_product_meta_lookup_prices'] );

		$current_min_price = isset( $prices['min_price'] )
			? (float) wp_unslash( $prices['min_price'] )
			: 0; // WPCS: input var ok, CSRF ok.
		$current_max_price = isset( $prices['max_price'] )
			? (float) wp_unslash( $prices['max_price'] )
			: PHP_INT_MAX; // WPCS: input var ok, CSRF ok.

		/**
		 * Adjust if the store taxes are not displayed how they are stored.
		 * Kicks in when prices excluding tax are displayed including tax.
		 */
		if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
			$tax_rates = WC_Tax::get_rates( $tax_class );
			if ( $tax_rates ) {
				$current_min_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_min_price, $tax_rates ) );
				$current_max_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( $current_max_price, $tax_rates ) );
			}
		}

		global $wpdb;
		if ( ! strstr( $args['join'], 'wc_product_meta_lookup' ) ) {
			$args['join'] .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
		}

		$args['where'] .= $wpdb->prepare(
			' AND NOT ( %f > wc_product_meta_lookup.max_price OR %f < wc_product_meta_lookup.min_price ) ',
			$current_min_price,
			$current_max_price
		);


		return $args;
	}

	add_filter( 'posts_clauses', 'us_product_meta_lookup_prices', 100, 2 );
}

if ( class_exists( 'woocommerce' ) AND ! function_exists( 'us_wc_add_lookup_prices_to_query' ) ) {
	/**
	 * Add the settings of the price range from the filter to the query, if set.
	 * Note: `_us_product_meta_lookup_prices` - processing in the `posts_clauses` filter.
	 *
	 * @param WP_Query $wp_query The WP query
	 */
	function us_wc_add_lookup_prices_to_query( $wp_query ) {
		$post_type = us_arr_path( $wp_query->query_vars, 'post_type' );
		if ( wp_doing_ajax() OR ! us_post_type_is_available( $post_type, array( 'product' ) ) ) {
			return;
		}
		if (
			empty( $wp_query->query['_us_product_meta_lookup_prices'] )
			AND isset( $_GET['min_price'], $_GET['max_price'] )
		) {
			$wp_query->query['_us_product_meta_lookup_prices'] = array(
				'min_price' => (int) us_arr_path( $_GET, 'min_price' ),
				'max_price' => (int) us_arr_path( $_GET, 'max_price' ),
			);
		}
	}

	add_action( 'pre_get_posts', 'us_wc_add_lookup_prices_to_query', 2, 1 );
}

if ( ! function_exists( 'us_search_grid_filter_the_post' ) ) {
	/**
	 * Check the presence of a grid filter in the post
	 *
	 * @param intval $post_id
	 * @return null|int
	 */
	function us_search_grid_filter_the_post( $post_id ) {
		$result = NULL;

		// If template for content area is found ...
		if ( $post = get_post( (int) $post_id ) ) {
			$substring = '[us_grid_filter';

			// ... first, check if content area has grid filter ...
			if ( strpos( $post->post_content, $substring ) !== FALSE ) {
				return $post->ID;

				// ... otherwise search grid filter in Page Blocks.
			} else {
				us_get_recursive_parse_page_block(
					$post, function ( $post ) use ( &$result, $substring ) {
					if (
						is_null( $result )
						AND $post instanceof WP_Post
						AND strpos( $post->post_content, $substring ) !== FALSE
					) {
						$result = $post->ID;
					}
				}
				);
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'us_define_content_and_apply_grid_filters' ) ) {
	/**
	 * Define content and apply grid filters to query_args
	 *
	 * @param array $query_vars
	 * @param WP_Tax_Query $tax_query
	 * @return boolean
	 */
	function us_define_content_and_apply_grid_filters( &$query_vars, $tax_query = NULL ) {
		global $_us_content_id;

		if ( ! $_us_content_id ) {
			$_us_content_id = us_get_page_area_id( 'content' );
			$_us_content_id = us_search_grid_filter_the_post( $_us_content_id );

			// If we couldn't find a filter in the content, then check the sidebar
			if ( ! $_us_content_id AND us_get_option( 'enable_page_blocks_for_sidebars', FALSE ) ) {
				$_us_content_id = us_get_page_area_id( 'sidebar' );
				$_us_content_id = us_search_grid_filter_the_post( $_us_content_id );
			}

			// Update tax_query
			if ( $_us_content_id AND $tax_query instanceof WP_Tax_Query ) {
				$query_vars['tax_query'] = $tax_query->queries;
			}
		}
		if ( $_us_content_id ) {
			us_apply_grid_filters( $_us_content_id, $query_vars );
		}

		return (bool) $_us_content_id;
	}
}

if ( ! function_exists( 'us_grid_filter_get_count_items' ) ) {
	/**
	 * Get the number of records for a filter element
	 *
	 * @param array $query_args
	 * @return int
	 */
	function us_grid_filter_get_count_items( $query_args, $selected_taxonomies = array() ) {
		if ( empty( $query_args ) OR ! is_array( $query_args ) ) {
			return 0;
		}
		if (
			! empty( $query_args['post_type'] )
			AND (
				(
					is_array( $query_args['post_type'] )
					AND in_array( 'product', $query_args['post_type'] )
				)
				OR $query_args['post_type'] == 'product'
			)
			AND class_exists( 'woocommerce' )
			AND is_object( wc() )
		) {
			if ( ! isset( $query_args['tax_query'] ) ) {
				$query_args['tax_query'] = array();
			}
			$query_args['tax_query'] = wc()->query->get_tax_query( $query_args['tax_query'] );
		}

		// Remove duplicate fields in tax_query
		$tax_maps = array();
		foreach ( us_arr_path( $query_args, 'tax_query', array() ) as $index => $tax ) {
			$field = us_arr_path( $tax, 'taxonomy' );
			$taxonomy = us_arr_path( $tax, 'taxonomy' );
			if ( is_null( $taxonomy ) ) {
				continue;
			} elseif ( ! isset( $tax_maps[ $taxonomy ][ $field ] ) ) {
				$tax_maps[ $taxonomy ][ $field ] = $index;
				continue;
			} else {
				unset( $query_args['tax_query'][ $index ] );
			}
		}

		// Remove duplicate fields in meta_query
		$meta_maps = array();
		foreach ( us_arr_path( $query_args, 'meta_query', array() ) as $index => $meta ) {
			if ( $index === 'relation' ) {
				continue;
			}

			if ( $key = us_arr_path( $meta, 'key' ) ) {
				if ( ! isset( $meta_maps[ $key ] ) ) {
					$meta_maps[ $key ] = $index;
				} else {
					unset( $query_args['meta_query'][ $index ] );
				}
			} elseif ( isset( $meta[0] ) ) {
				$keys = array();
				array_walk_recursive(
					$meta, function ( $value, $key ) use ( &$keys ) {
					if ( $key === 'key' ) {
						$keys[] = $value;
					}
				}
				);
				$keys = array_unique( $keys );
				foreach ( $keys as $key ) {
					if ( isset( $meta_maps[ $key ] ) ) {
						unset( $query_args['meta_query'][ $index ] );
					} else {
						$meta_maps[ $key ] = $index;
					}
				}
			}
		}
		unset( $tax_maps, $meta_maps );

		foreach ( array( 'tax_query', 'meta_query' ) as $key ) {
			if ( ! empty( $selected_taxonomies[ $key ] ) AND is_array( $selected_taxonomies[ $key ] ) ) {
				$query_args[ $key ] = array_merge( $query_args[ $key ], $selected_taxonomies[ $key ] );
			}
		}

		if ( $query_args['post_type'] == 'current_child_pages' ) {
			$query_args['post_type'] = 'any';
		}

		return ( new WP_Query( $query_args ) )->post_count;
	}
}

if ( ! function_exists( 'us_grid_pre_get_posts' ) ) {
	/**
	 * Modifying the query to get records for the grid
	 *
	 * @param WP_Query $query
	 */

	function us_grid_pre_get_posts( $query ) {
		global $us_context_layout, $us_get_orderby, $pagenow, $us_grid_pre_get_posts_running;

		/**
		 * Removing `orderby` and `order` from "GET" query for grid_order/grid_filter
		 */
		if ( $us_order_key = us_get_grid_url_prefix( 'order' )
			AND is_search()
			AND isset( $_GET[ $us_order_key ] ) ) {

			$us_get_orderby = $_GET[ $us_order_key ];
			unset( $_GET[ $us_order_key ] );
		}

		// Prevent nesting calls of this function, which happens in rare cases
		if ( empty( $us_grid_pre_get_posts_running ) ) {
			$us_grid_pre_get_posts_running = TRUE;
		} else {
			return;
		}
		// Skip executing on post list pages in backend to avoid conflict with WooCommerce add-ons
		if ( is_admin() AND $pagenow == 'edit.php' ) {
			return;
		}

		// Apply filters to archive page
		if (
			is_null( $us_context_layout )
			AND (
				$query->is_tax OR $query->is_tag OR $query->is_archive
			)
		) {
			$grid_filter_found = us_define_content_and_apply_grid_filters( $query->query_vars, $query->tax_query );
			if ( $grid_filter_found AND class_exists( 'woocommerce' ) AND is_object( wc() ) ) {
				$current_tax_query = us_arr_path( $query->query_vars, 'tax_query', array() );
				$query->set( 'tax_query', wc()->query->get_tax_query( $current_tax_query ) );
			}
		}

		// Apply sorting params to archive or search page
		if (
			$query->is_main_query()
			AND (
				is_archive()
				OR is_search()
			)
			AND $get_orderby = us_arr_path( $_GET, us_get_grid_url_prefix( 'orderby' ), $us_get_orderby )
			AND $orderby_params = us_grid_orderby_str_to_params( $get_orderby )
		) {
			us_grid_set_orderby_to_query_args( $query->query_vars, $orderby_params );
		}
		$us_grid_pre_get_posts_running = FALSE;
	}

	add_action( 'pre_get_posts', 'us_grid_pre_get_posts', 10, 1 );
}

if ( ! function_exists( 'us_save_post_grid_filter_atts' ) ) {
	/**
	 * Save Grid Filter Attributes as post meta data
	 *
	 * @param integer $post_id The post identifier
	 * @return array
	 */
	function us_save_post_grid_filter_atts( $post_id ) {
		$filter_atts = '';
		if (
			$post = get_post( (int) $post_id )
			AND preg_match_all( '/\[us_grid_filter.+?filter_items="([^\"]+)"[^]]*]/i', $post->post_content, $matches )
		) {
			$filter_atts = array();
			foreach ( us_arr_path( $matches, '1', array() ) as $match ) {
				if ( $atts = json_decode( urldecode( $match ), TRUE ) ) {
					if ( ! is_array( $atts ) ) {
						continue;
					}
					$filter_atts = array_merge( $filter_atts, array_values( $atts ) );
				}
			}
		}
		update_post_meta( $post_id, '_us_grid_filter_atts', $filter_atts );

		return $filter_atts;
	}

	add_action( 'save_post', 'us_save_post_grid_filter_atts', 100, 1 );
}

if ( ! function_exists( 'us_grid_get_selected_taxonomies' ) ) {
	/**
	 * Get selected taxonomies for $query_args
	 *
	 * @param array $atts
	 * @return array
	 */
	function us_grid_get_selected_taxonomies( $atts ) {
		if ( empty( $atts ) OR ! is_array( $atts ) ) {
			return;
		}
		$query_args = array();
		extract( $atts );

		if ( empty( $atts['post_type'] ) ) {
			$post_type = 'post';
		}

		// Posts from selected taxonomies
		$known_post_type_taxonomies = us_grid_available_taxonomies();
		if ( ! empty( $post_type ) AND ! empty( $known_post_type_taxonomies[ $post_type ] ) ) {
			foreach ( $known_post_type_taxonomies[ $post_type ] as $taxonomy ) {
				$_taxonomy = str_replace( '-', '_', $taxonomy );
				if ( ! empty( ${'taxonomy_' . $_taxonomy} ) ) {
					if ( ! isset( $query_args['tax_query'] ) ) {
						$query_args['tax_query'] = array();
					}
					$terms = explode( ',', ${'taxonomy_' . $_taxonomy} );
					// Validating values to support identifiers
					foreach ( $terms as &$item ) {
						if ( is_numeric( $item ) AND $term = get_term( $item, $taxonomy ) ) {
							$item = $term->slug;
						}
					}
					unset( $item );
					$query_args['tax_query'][] = array(
						'taxonomy' => $taxonomy,
						'field' => 'slug',
						'terms' => $terms,
					);
				}
			}
		}

		return $query_args;
	}
}

if ( ! function_exists( 'us_save_post_first_grid_data' ) ) {
	/**
	 * Get data from the first found grid f
	 *
	 * @param int $post_id
	 */
	function us_save_post_first_grid_data( $post_id ) {
		/**
		 * The search first grid
		 * @param WP_Post $post
		 * @return string
		 */
		$func_search_first_grid = function ( $post ) {
			if (
				$post instanceof WP_Post
				AND preg_match( '/\[us_grid((?!\_)([^\]]+)?)/i', $post->post_content, $matches )
			) {

				// Parse attributes
				$atts = us_arr_path( $matches, '1', '' );
				$atts = shortcode_parse_atts( $atts );
				if ( ! is_array( $atts ) ) {
					$atts = array();
				}

				// Saving the types of posts selected for the first grid,
				// this is necessary for displaying different shortcodes,
				// for example us_grid_filter, us_grid_order etc.
				$post_types = array( 'post' );
				if ( ! empty( $atts['post_type'] ) ) {
					$post_types = is_array( $atts['post_type'] )
						? array_unique( $atts['post_type'] )
						: array( (string) $atts['post_type'] );
				}
				update_post_meta( $post->ID, '_us_first_grid_post_type', $post_types );

				if ( empty( $atts ) ) {
					return;
				}

				$atts['post_type'] = us_arr_path( $post_types, '0', 'post' );
				if ( us_post_type_is_available( $post_types, array_keys( us_grid_available_taxonomies() ) ) ) {
					update_post_meta( $post->ID, '_us_first_grid_selected_taxonomies', us_grid_get_selected_taxonomies( $atts ) );
					update_post_meta( $post->ID, '_us_first_grid_products_include', us_arr_path( $atts, 'products_include', '' ) );
				}
			}
		};

		if (
			$post = get_post( (int) $post_id )
			AND ! $func_search_first_grid( $post )
		) {
			us_get_recursive_parse_page_block( $post, $func_search_first_grid );
		}
	}

	add_action( 'save_post', 'us_save_post_first_grid_data', 100, 1 );
}

if ( ! function_exists( 'us_check_grid_filter_url_prefix' ) ) {
	/**
	 * Remove illegal URL characters from the prefix name
	 *
	 * @param array $updated_options
	 * @return array $updated_options
	 */
	function us_check_grid_filter_url_prefix( $updated_options ) {
		if ( ! empty( $updated_options['grid_filter_url_prefix'] ) ) {
			$grid_filter_url_prefix = (string) $updated_options['grid_filter_url_prefix'];
			$updated_options['grid_filter_url_prefix'] = preg_replace( '/[^\dA-z\-]+/', '', $grid_filter_url_prefix );
		}

		return $updated_options;
	}

	add_filter( 'usof_updated_options', 'us_check_grid_filter_url_prefix', 100, 1 );
}

if ( ! function_exists( 'us_is_grid_products_defined_by_query_args' ) ) {
	/**
	 * The is a grid of products defined by query_args, useful when post type any or related
	 *
	 * @param array $query_args
	 * @return bool
	 */
	function us_is_grid_products_defined_by_query_args( $query_args ) {
		$result = FALSE;
		if ( ! empty( $query_args['tax_query'] ) ) {
			array_walk_recursive(
				$query_args['tax_query'], function ( $value, $key ) use ( &$result ) {
				if ( $result OR ! in_array( $key, array( 'taxonomy', 'terms' ) ) ) {
					return;
				}
				// Checking taxonomies
				if ( $key === 'taxonomy' AND strpos( $value, 'product_' ) === 0 ) {
					return $result = TRUE;
				}
				// Checking terms
				if ( $key == 'terms' AND function_exists( 'is_product_category' ) ) {
					foreach ( explode( ',', $value ) as $term ) {
						if ( is_product_category( $term ) ) {
							return $result = TRUE;
						}
					}
				}

			}
			);
		}

		return $result;
	}
}

if ( ! function_exists( 'us_get_portfolio_slugs_map' ) ) {
	/**
	 * Get portfolio taxonomies slugs map
	 *
	 * @return array
	 */
	function us_get_portfolio_slugs_map() {
		return array(
			// default_slug => option_key
			'us_portfolio_category' => 'portfolio_category_slug',
			'us_portfolio_tag' => 'portfolio_tag_slug',
		);
	}
}

if ( ! function_exists( 'us_grid_orderby_str_to_params' ) ) {
	/**
	 * Convert a orderby string to params
	 *
	 * @param string $string The string
	 * @return array
	 */
	function us_grid_orderby_str_to_params( $string ) {
		$result = array();
		if (
			! $string = trim( $string )
			OR ! $params = explode( ',', $string )
		) {
			return $result;
		}

		// Remove extra spaces just in case
		array_map( 'trim', $params );

		// Get sorting key or custom field name
		$orderby = $result['orderby'] = us_arr_path( $params, '0', '' );

		// Check if the field is custom or not
		$options = (array) us_config( 'elements/grid_order.params.orderby_items.params.value.options' );
		if ( ! in_array( $orderby, array_keys( $options ) ) ) {
			$result['orderby'] = 'custom';
			$result['custom_field'] = $orderby;
		}

		// Check for additional parameters
		$result['invert'] = in_array( 'asc', $params );
		$result['custom_field_numeric'] = in_array( 'numeric', $params );

		return $result;
	}
}

if ( ! function_exists( 'us_grid_set_orderby_to_query_args' ) ) {
	/**
	 * Set orderby params to $query_args
	 *
	 * @param array $query_args
	 * @param array $params
	 */
	function us_grid_set_orderby_to_query_args( &$query_args, $params = array() ) {
		if ( empty( $params ) OR ! is_array( $params ) ) {
			return;
		}

		$params = array_merge(
			array(
				'orderby' => '',
				'invert' => FALSE,
				'custom_field' => NULL,
				'custom_field_numeric' => FALSE,
				'post_type' => array(),
			), $params
		);

		if ( ! is_array( $params['post_type'] ) ) {
			$params['post_type'] = array( $params['post_type'] );
		}

		$order = $params['invert']
			? 'ASC'
			: 'DESC';
		$order_reverse = $params['invert']
			? 'DESC'
			: 'ASC';

		// Add Orderby and Order arguments to query_args
		switch ( $params['orderby'] ) {
			case 'date':
				$query_args['orderby'] = array( 'date' => $order );
				break;
			case 'modified':
				// When sorting by modified date adding creation date in case of bulk post updating
				// First item in orderby array is main param to order by
				$query_args['orderby'] = array( 'modified' => $order, 'date' => $order );
				break;
			case 'title':
				$query_args['orderby'] = array( 'title' => $order_reverse );
				$query_args['order'] = $order_reverse;
				break;
			case 'post__in':
				$query_args['orderby'] = array( 'post__in' => $order_reverse );
				$query_args['order'] = $order_reverse;
				break;
			case 'menu_order':
				// Sort posts order for ids
				if ( in_array( 'ids', $params['post_type'] ) AND ! empty( $query_args['post__in'] ) ) {
					$query_args['orderby'] = 'post__in';
				} else {
					$query_args['orderby'] = array( 'menu_order' => $order_reverse );
					$query_args['order'] = $order_reverse;
				}
				break;
			case 'rand':
				$query_args['orderby'] = 'RAND(' . mt_rand() . ')';
				break;
			case 'custom':
				// The `_orderby_custom_` value does not affect anything,
				// it only allows you to preserve uniqueness
				$query_args['meta_query']['_orderby_custom_'] = array(
					'relation' => 'OR',
					array(
						'compare' => 'EXISTS',
						'key' => $params['custom_field'],
						'type' => empty( $params['custom_field_numeric'] ) ? 'CHAR' : 'SIGNED',
					),
					array(
						'compare' => 'NOT EXISTS',
						'key' => $params['custom_field'],
						'type' => empty( $params['custom_field_numeric'] ) ? 'CHAR' : 'SIGNED',
					),
				);
				$query_args['orderby'] = $params['custom_field'];
				$query_args['order'] = $order;
				break;
			case 'price':
				$query_args['orderby'] = 'meta_value_num';
				$query_args['meta_key'] = '_price';
				$query_args['order'] = $order;
				break;
			case 'popularity':
				// When sorting by meta_value_num adding title in case of same values for meta_value_num
				// First item in orderby array is main param to order by
				$query_args['orderby'] = array( 'meta_value_num' => $order, 'title' => $order_reverse );
				$query_args['meta_key'] = 'total_sales';
				$query_args['order'] = $order;
				break;
			case 'rating':
				$query_args['orderby'] = 'meta_value_num';
				$query_args['meta_key'] = '_wc_average_rating';
				$query_args['order'] = $order;
				break;
			case 'post_views_counter':
			case 'post_views_counter_day':
			case 'post_views_counter_week':
			case 'post_views_counter_month':
				if ( class_exists( 'Post_Views_Counter' ) ) {
					$query_args = array_merge(
						$query_args, array(
							// required by PVC
							'suppress_filters' => FALSE,
							'orderby' => 'post_views',
							'fields' => '',
							'views_query' => array(
								'hide_empty' => FALSE,
							),
						)
					);
				} else {
					$query_args['orderby'] = array( $params['orderby'] => $order );
				}
				break;
			default:
				$query_args['orderby'] = array( $params['orderby'] => $order );
		}

		// Order by views per month, week, day
		if (
			class_exists( 'Post_Views_Counter' )
			AND in_array(
				$params['orderby'], array(
					'post_views_counter_day',
					'post_views_counter_week',
					'post_views_counter_month',
				)
			)
		) {
			$views_query = array(
				'year' => date( 'Y' ),
				'month' => date( 'm' ),
				'week' => date( 'W' ),
				'day' => date( 'd' ),
			);
			switch ( $params['orderby'] ) {
				// Views for last day
				case 'post_views_counter_day':
					unset( $views_query['week'] );
					break;
				// Views for last week
				case 'post_views_counter_week':
					unset( $views_query['day'] );
					break;
				// Views for last month
				case 'post_views_counter_month':
					unset( $views_query['day'], $views_query['week'] );
					break;
			}
			$query_args['views_query'] = array_merge( $query_args['views_query'], $views_query );
			unset( $views_query );
		}
		unset( $params, $order, $order_reverse );
	}
}

if ( ! function_exists( 'us_filter_sorting_priorities_for_grid_orderby' ) ) {
	/**
	 * Adjusting the orderby to sort correctly when the field is empty or missing
	 *
	 * @param array $clauses
	 * @param WP_Query $wp_query
	 * @return array $clauses
	 */
	function us_filter_sorting_priorities_for_grid_orderby( $clauses, $wp_query ) {
		if (
			! empty( $meta_query['_orderby_custom_'] )
			AND preg_match_all( '/wp_posts\.ID\s=\s([\w\d\_]+)\./', $clauses['join'], $matches )
		) {
			$tables = $matches[1];
			$order = $wp_query->get( 'order' );
			// Depending on the order, we drop empty and non-existent values to the end
			$if_order = us_strtolower( $order ) === 'desc' ? '0,1' : '1,0';
			// This condition will first check for nonexistent and empty values then only set $clauses['orderby']
			$clauses['orderby'] = "IF(${tables[1]}.post_id IS NULL OR ${tables[0]}.meta_value = '',${if_order}) ${order},${clauses['orderby']}";
		}

		return $clauses;
	}

	add_filter( 'posts_clauses', 'us_filter_sorting_priorities_for_grid_orderby', 100, 2 );
}

if ( ! function_exists( 'us_grid_get_orderby_options' ) ) {
	/**
	 * Get sorting options for grid config
	 *
	 * @return array
	 */
	function us_grid_get_orderby_options() {
		$options = array(
			'date' => __( 'Date of creation', 'us' ),
			'modified' => __( 'Date of update', 'us' ),
			'title' => us_translate( 'Title' ),
			'rand' => us_translate( 'Random' ),
			'comment_count' => us_translate( 'Comments' ),
			'menu_order' => sprintf( __( '"%s" value from "%s" box', 'us' ), us_translate( 'Order' ), us_translate( 'Page Attributes' ) ),
			'post__in' => __( 'Manually for selected images and items', 'us' ),
		);

		// Additional values for WooCommerce products
		if ( class_exists( 'woocommerce' ) ) {
			$options = array_merge(
				$options, array(
					'popularity' => us_translate( 'Sales', 'woocommerce' ),
					'price' => us_translate( 'Price', 'woocommerce' ),
					'rating' => us_translate( 'Rating', 'woocommerce' ),
				)
			);
		}

		// Orders for Post Views Counter
		if ( class_exists( 'Post_Views_Counter' ) ) {
			$options = array_merge(
				$options, array(
					'post_views_counter' => __( 'Total views', 'us' ),
					'post_views_counter_month' => __( 'Views for last month', 'us' ),
					'post_views_counter_week' => __( 'Views for last week', 'us' ),
					'post_views_counter_day' => __( 'Views for last day', 'us' ),
				)
			);
		}

		// Add an option for custom settings
		$options = array_merge(
			$options, array(
				'custom' => __( 'Custom Field', 'us' ),
			)
		);

		return $options;
	}
}

if ( ! function_exists( 'us_grid_stop_loop' ) ) {
	/**
	 * Stop grid loop execution
	 *
	 * @param bool $show_message whether to show "No items" message / page block set by user
	 * @param bool $interrupt whether to interrupt the loop
	 */
	function us_grid_stop_loop( $show_message = TRUE, $interrupt = TRUE ) {
		global $us_grid_loop_running, $us_grid_no_items_message, $us_grid_no_items_action, $us_grid_no_items_page_block;

		if ( $interrupt ) {
			$us_grid_loop_running = FALSE;
		}

		if ( $show_message AND $us_grid_no_items_action !== 'hide_grid' ) {
			
			// Output specified Page Block
			if ( $us_grid_no_items_action === 'page_block' AND is_numeric( $us_grid_no_items_page_block ) ) {
				if ( has_filter( 'us_tr_object_id' ) ) {
					$us_grid_no_items_page_block = apply_filters( 'us_tr_object_id', $us_grid_no_items_page_block, 'us_page_block', TRUE );
				}

				$page_block = get_post( $us_grid_no_items_page_block );

				if ( $page_block instanceof WP_Post AND $page_block->post_type == 'us_page_block' ) {
					us_add_to_page_block_ids( $page_block->ID );

					$page_block_content = $page_block->post_content;
					us_open_wp_query_context();
					us_add_page_shortcodes_custom_css( $us_grid_no_items_page_block );
					us_close_wp_query_context();

					// Remove [vc_row] and [vc_column]
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

					// Apply filters to Page Block content and echoing it out of us_open_wp_query_context
					echo '<div class="w-grid-none">' . apply_filters( 'us_page_block_the_content', $page_block_content ) . '</div>';

					us_remove_from_page_block_ids();
				}

				// Output specified non-empty message
			} elseif ( $us_grid_no_items_action === 'message' AND ! empty( $us_grid_no_items_message ) ) {
				echo '<h4 class="w-grid-none">' . strip_tags( $us_grid_no_items_message, '<br><strong>' ) . '</h4>';
			}
		} elseif ( apply_filters( 'usb_is_preview_page', NULL ) ) {
			echo '<div class="w-grid-none"></div>';
		}
	}
}

if ( ! function_exists( 'us_get_post_ids_for_autocomplete' ) ) {
	/**
	 * Get a list of records for an us_autocomplete WPB
	 *
	 * @param integer $limit The limit
	 * @return array
	 */
	function us_get_post_ids_for_autocomplete( $limit = 50 ) {
		if ( ! is_admin() ) {
			return array();
		} elseif ( ! check_ajax_referer( 'us_ajax_get_post_ids_for_autocomplete', '_nonce', FALSE ) ) {
			return array();
		}

		// US Autocomplete options
		$search = isset( $_GET['search'] ) ? $_GET['search'] : '';
		$offset = isset( $_GET['offset'] ) ? (int) $_GET['offset'] : 0;

		// Remove media from post_type
		$post_type = array_keys( us_grid_available_post_types() );
		if ( ( $index = array_search( 'attachment', $post_type ) ) !== FALSE ) {
			unset( $post_type[ $index ] );
		}

		$query_args = array(
			'post_type' => $post_type,
			'posts_per_page' => $limit,
			'post_status' => 'any',
			'suppress_filters' => 0,
			'offset' => $offset,
		);

		// Get selected params
		if ( strpos( $search, 'params:' ) === 0 ) {
			$params = explode( ',', substr( $search, strlen( 'params:' ) ) );
			$query_args['post__in'] = array_map( 'intval', $params );
			$search = '';
		}

		if ( ! empty( $search ) ) {
			$query_args['s'] = $search;
		}

		$results = array();
		foreach ( get_posts( $query_args ) as $post ) {
			$results[ $post->ID ] = strlen( $post->post_title ) > 0
				? esc_attr( $post->post_title )
				: us_translate( '(no title)' );

			if ( $post_type = get_post_type_object( $post->post_type ) ) {
				$results[ $post->ID ] .= sprintf( ' <i>%s</i>', $post_type->labels->singular_name );
			}
		}

		return $results;
	}

	/**
	 * AJAX Request Handler
	 */
	function us_ajax_get_post_ids_for_autocomplete() {
		if ( ! check_ajax_referer( 'us_ajax_get_post_ids_for_autocomplete', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
			wp_die();
		}
		wp_send_json_success( array( 'items' => us_get_post_ids_for_autocomplete() ) );
		wp_die();
	}

	add_action( 'wp_ajax_us_get_post_ids_for_autocomplete', 'us_ajax_get_post_ids_for_autocomplete', 1 );
}

if ( ! function_exists( 'us_get_term_ids_for_autocomplete' ) ) {
	/**
	 * Get a list of records for an a us_autocomplete WPB
	 *
	 * @param integer $limit The limit
	 * @return array
	 */
	function us_get_term_ids_for_autocomplete( $limit = 50 ) {
		if ( ! is_admin() ) {
			return array();
		} elseif ( ! check_ajax_referer( 'us_ajax_get_term_ids_for_autocomplete', '_nonce', FALSE ) ) {
			return array();
		}

		// US Autocomplete options
		$search = isset( $_GET['search'] ) ? $_GET['search'] : '';
		$offset = isset( $_GET['offset'] ) ? (int) $_GET['offset'] : 0;

		$taxonomies = us_get_taxonomies( TRUE, FALSE );

		$query_args = array(
			'taxonomy' => array_keys( $taxonomies ),
			'hide_empty' => FALSE,
			'number' => $limit,
			'offset' => $offset,
		);

		// Get selected params
		if ( strpos( $search, 'params:' ) === 0 ) {
			$params = explode( ',', substr( $search, strlen( 'params:' ) ) );
			$query_args['include'] = array_map( 'intval', $params );
			$search = '';
		}

		if ( ! empty( $search ) ) {
			$query_args['name__like'] = $search;
		}

		$results = array();
		foreach ( get_terms( $query_args ) as $term ) {
			$results[ $term->term_id ] = strlen( $term->name ) > 0
				? esc_attr( $term->name )
				: us_translate( '(no title)' );

			if ( ! empty( $taxonomies [ $term->taxonomy ] ) ) {
				$results[ $term->term_id ] .= sprintf( ' <i>%s</i>', $taxonomies [ $term->taxonomy ] );
			}
		}

		return $results;
	}

	/**
	 * AJAX Request Handler
	 */
	function us_ajax_get_term_ids_for_autocomplete() {
		if ( ! check_ajax_referer( 'us_ajax_get_term_ids_for_autocomplete', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
			wp_die();
		}
		wp_send_json_success( array( 'items' => us_get_term_ids_for_autocomplete() ) );
		wp_die();
	}

	add_action( 'wp_ajax_us_get_term_ids_for_autocomplete', 'us_ajax_get_term_ids_for_autocomplete', 1 );
}

if ( wp_doing_ajax() AND ! function_exists( 'us_get_taxonomies_autocomplete' ) ) {
	add_action( 'wp_ajax_us_get_taxonomies_autocomplete', 'us_get_taxonomies_autocomplete', 1 );
	/**
	 * Request AJAX handler for us_get_taxonomies_autocomplete
	 * @return string
	 */
	function us_get_taxonomies_autocomplete() {
		if ( ! check_ajax_referer( 'us_ajax_get_taxonomies_autocomplete', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
			wp_die();
		}

		// Query params
		if ( ! $slug = trim( $_GET['slug'] ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'Taxonomy cannot be empty' ),
				)
			);
			wp_die();
		}
		$offset = (int) $_GET['offset'];
		$search_text = trim( $_GET['search'] );

		$response = array(
			'items' => array(),
		);

		// The method for obtaining data should be able to receive data in batches,
		// search the search field and load the list on the search field if it contains a separator `params:name,name2,name3`
		$response['items'] = us_get_terms_by_slug( $slug, $offset, 15, $search_text );

		wp_send_json_success( $response );
	}
}

if ( ! function_exists( 'us_import_grid_layout' ) ) {
	/**
	 * This is a method to add a layout based on the passed data
	 *
	 * @param string $data The data
	 * @param string $post_type The post type
	 * @return int|string
	 */
	function us_import_grid_layout( $data, $post_type = 'us_grid_layout' ) {
		$result = 'blog_1'; // The default layout
		$data = explode( '|', $data );
		if ( count( $data ) != 2 ) {
			return $result;
		}
		$post_content = base64_decode( $data[1] );
		if ( json_decode( $post_content ) === NULL ) {
			$post_content = NULL;
		}
		if ( ! $post_content OR ! isset( $data[0] ) ) {
			return $result;
		}

		global $wpdb;

		// Preparing a query to find a duplicate us_grid_layout
		$sql = $wpdb->prepare(
			"SELECT id FROM $wpdb->posts WHERE post_type = %s AND TRIM(`post_content`) = %s LIMIT 1",
			$post_type,
			$post_content
		);
		if ( $post_id = $wpdb->get_var( $sql ) ) {
			// If the record exists, we get the identifier
			$result = $post_id;
		} else {
			$post_id = wp_insert_post(
				array(
					'post_type' => $post_type,
					'post_content' => $post_content,
					'post_author' => get_current_user_id(),
					'post_title' => trim( $data[0] ),
					'post_status' => 'publish',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
				)
			);
			if ( $post_id > 0 ) {
				$result = $post_id;
			}
		}

		return $result;
	}
}
