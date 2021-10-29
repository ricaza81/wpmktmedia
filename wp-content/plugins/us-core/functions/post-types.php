<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// Disable Gutenberg (Block Editor) when editing any posts in admin
if ( ! us_get_option( 'block_editor' ) ) {
	remove_theme_support( 'core-block-patterns' );
	add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );
	remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
}

// Should be inited before the WPBakery Page Builder (that is 9)
global $portfolio_slug;
$portfolio_slug = us_get_option( 'portfolio_slug', 'portfolio' );

// Create theme related post types
add_action( 'init', 'us_create_post_types', 8 );
function us_create_post_types() {

	if ( us_get_option( 'enable_portfolio', 1 ) ) {
		global $portfolio_slug;
		if ( $portfolio_slug == '' ) {
			$portfolio_rewrite = array( 'slug' => FALSE, 'with_front' => FALSE );
		} else {
			$portfolio_rewrite = array( 'slug' => untrailingslashit( $portfolio_slug ) );
		}

		// Portfolio Categories
		register_taxonomy(
			'us_portfolio_category', array( 'us_portfolio' ), array(
				'labels' => array(
					'name' => apply_filters( 'us_portfolio_category_label', __( 'Portfolio Categories', 'us' ) ),
					'menu_name' => us_translate( 'Categories' ),
				),
				'show_admin_column' => TRUE,
				'hierarchical' => TRUE,
				'rewrite' => array( 'slug' => us_get_option( 'portfolio_category_slug', 'portfolio_category' ) ),
			)
		);

		// Portfolio Tags
		register_taxonomy(
			'us_portfolio_tag', array( 'us_portfolio' ), array(
				'labels' => array(
					'name' => apply_filters( 'us_portfolio_tags_label', __( 'Portfolio Tags', 'us' ) ),
					'menu_name' => us_translate( 'Tags' ),
				),
				'show_admin_column' => TRUE,
				'rewrite' => array( 'slug' => us_get_option( 'portfolio_tag_slug' ) ),
			)
		);

		// Portfolio Page post type
		register_post_type(
			'us_portfolio', array(
				'labels' => apply_filters(
					'us_portfolio_labels', array(
						'name' => __( 'Portfolio', 'us' ),
						'singular_name' => __( 'Portfolio Page', 'us' ),
						'add_new' => __( 'Add Portfolio Page', 'us' ),
						'add_new_item' => __( 'Add Portfolio Page', 'us' ),
						'edit_item' => __( 'Edit Portfolio Page', 'us' ),
						'featured_image' => us_translate_x( 'Featured Image', 'page' ),
						'view_item' => us_translate( 'View Page' ),
						'not_found' => us_translate( 'No pages found.' ),
						'not_found_in_trash' => us_translate( 'No pages found in Trash.' ),
					)
				),
				'public' => TRUE,
				'rewrite' => $portfolio_rewrite,
				'supports' => array(
					'title',
					'editor',
					'excerpt',
					'thumbnail',
					'custom-fields',
					'revisions',
					'comments',
					'author',
				),
				'menu_icon' => 'dashicons-images-alt',
			)
		);

		// Add "Preview" column for Portfolio Pages
		add_filter( 'manage_us_portfolio_posts_columns', 'us_add_preview_column' );
		add_action( 'manage_us_portfolio_posts_custom_column', 'us_add_preview_column_value', 10, 2 );
		function us_add_preview_column( $columns ) {
			$num = 1; // after which column paste new column
			$new_column = array( 'us_preview' => '&nbsp;' );

			return array_slice( $columns, 0, $num ) + $new_column + array_slice( $columns, $num );
		}

		function us_add_preview_column_value( $column_name, $post_ID ) {
			if ( $column_name == 'us_preview' AND $thumbnail_id = get_post_meta( $post_ID, '_thumbnail_id', TRUE ) ) {
				echo wp_get_attachment_image( $thumbnail_id, 'thumbnail', TRUE );
			}
		}

		// Portfolio slug may have changed, so we need to keep WP's rewrite rules fresh
		$alloptions = wp_load_alloptions();
		if (
			us_get_option( 'enable_portfolio', FALSE )
			AND isset( $alloptions['us_flush_rules'] )
		) {
			flush_rewrite_rules();
			delete_option( 'us_flush_rules' );
		}
	}

	if ( us_get_option( 'enable_testimonials', 1 ) ) {

		// Testimonial Categories
		register_taxonomy(
			'us_testimonial_category', array( 'us_testimonial' ), array(
				'labels' => array(
					'name' => __( 'Testimonial Categories', 'us' ),
					'menu_name' => us_translate( 'Categories' ),
				),
				'public' => TRUE,
				'show_admin_column' => TRUE,
				'publicly_queryable' => FALSE,
				'show_in_nav_menus' => FALSE,
				'show_in_rest' => FALSE,
				'show_tagcloud' => FALSE,
				'hierarchical' => TRUE,
			)
		);

		// Testimonial post type
		register_post_type(
			'us_testimonial', array(
				'labels' => array(
					'name' => __( 'Testimonials', 'us' ),
					'singular_name' => __( 'Testimonial', 'us' ),
					'add_new' => __( 'Add Testimonial', 'us' ),
					'add_new_item' => __( 'Add Testimonial', 'us' ),
					'edit_item' => __( 'Edit Testimonial', 'us' ),
					'featured_image' => __( 'Author Photo', 'us' ),
				),
				'public' => TRUE,
				'publicly_queryable' => FALSE,
				'show_in_nav_menus' => FALSE,
				'supports' => array(
					'title',
					'editor',
					'thumbnail',
				),
				'menu_icon' => 'dashicons-testimonial',
				'rewrite' => FALSE,
				'query_var' => FALSE,
			)
		);
	}

	// Media Categories
	if ( us_get_option( 'media_category', 0 ) ) {
		register_taxonomy(
			'us_media_category', array( 'attachment' ), array(
				'labels' => array(
					'name' => __( 'Media Categories', 'us' ),
					'menu_name' => us_translate( 'Categories' ),
				),
				'public' => TRUE,
				'show_admin_column' => TRUE,
				'publicly_queryable' => FALSE,
				'show_in_nav_menus' => FALSE,
				'show_in_rest' => FALSE,
				'show_tagcloud' => FALSE,
				'hierarchical' => TRUE,
				'update_count_callback' => 'us_media_category_update_count_callback',
			)
		);
	}

	// Grid Layouts
	register_post_type(
		'us_grid_layout', array(
			'labels' => array(
				'name' => __( 'Grid Layouts', 'us' ),
				'singular_name' => __( 'Grid Layout', 'us' ),
				'add_new' => __( 'Add Grid Layout', 'us' ),
				'add_new_item' => __( 'Add Grid Layout', 'us' ),
				'edit_item' => __( 'Edit Grid Layout', 'us' ),
			),
			'public' => TRUE,
			'show_in_menu' => 'us-theme-options',
			'exclude_from_search' => TRUE,
			'show_in_admin_bar' => FALSE,
			'publicly_queryable' => FALSE,
			'show_in_nav_menus' => FALSE,
			'capability_type' => array( 'us_page_block', 'us_page_blocks' ),
			'map_meta_cap' => TRUE,
			'supports' => FALSE,
			'rewrite' => FALSE,
			'query_var' => FALSE,
			'register_meta_box_cb' => 'us_duplicate_post',
		)
	);

	// Headers
	register_post_type(
		'us_header', array(
			'labels' => array(
				'name' => _x( 'Headers', 'site top area', 'us' ),
				'singular_name' => _x( 'Header', 'site top area', 'us' ),
				'add_new' => _x( 'Add Header', 'site top area', 'us' ),
				'add_new_item' => _x( 'Add Header', 'site top area', 'us' ),
				'edit_item' => _x( 'Edit Header', 'site top area', 'us' ),
			),
			'public' => TRUE,
			'show_in_menu' => 'us-theme-options',
			'exclude_from_search' => TRUE,
			'show_in_admin_bar' => FALSE,
			'publicly_queryable' => FALSE,
			'show_in_nav_menus' => FALSE,
			'capability_type' => array( 'us_page_block', 'us_page_blocks' ),
			'map_meta_cap' => TRUE,
			'supports' => FALSE,
			'rewrite' => FALSE,
			'query_var' => FALSE,
			'register_meta_box_cb' => 'us_duplicate_post',
		)
	);

	/*
	 * Since post types should be inited before live builder,
	 * detecting if we are on builder preview page directly by checking builder's nonce
	 *
	 * Dev note: $nonce name should be the same as USBuilder::BUILDER_SLUG constant
	 */
	$is_builder_preview = FALSE;
	if ( $nonce = us_arr_path( $_REQUEST, 'us-builder' ) AND wp_verify_nonce( $nonce, 'us-builder' ) ) {
		$is_builder_preview = TRUE;
	}

	// Content templates
	register_post_type(
		'us_content_template', array(
			'labels' => array(
				'name' => __( 'Content templates', 'us' ),
				'singular_name' => __( 'Content template', 'us' ),
				'add_new' => __( 'Add Content template', 'us' ),
				'add_new_item' => __( 'Add Content template', 'us' ),
				'edit_item' => __( 'Edit Content template', 'us' ),
			),
			'public' => TRUE,
			'show_in_menu' => 'us-theme-options',
			'exclude_from_search' => TRUE,
			'show_in_admin_bar' => FALSE,
			'publicly_queryable' => $is_builder_preview,
			'show_in_nav_menus' => FALSE,
			'capability_type' => array( 'us_page_block', 'us_page_blocks' ),
			'map_meta_cap' => TRUE,
			'rewrite' => FALSE,
			'query_var' => FALSE,
			'register_meta_box_cb' => 'us_duplicate_post',
		)
	);

	// Page Blocks
	register_post_type(
		'us_page_block', array(
			'labels' => array(
				'name' => __( 'Page Blocks', 'us' ),
				'singular_name' => __( 'Page Block', 'us' ),
				'add_new' => __( 'Add Page Block', 'us' ),
				'add_new_item' => __( 'Add Page Block', 'us' ),
				'edit_item' => __( 'Edit Page Block', 'us' ),
			),
			'public' => TRUE,
			'show_in_menu' => 'us-theme-options',
			'exclude_from_search' => TRUE,
			'show_in_admin_bar' => FALSE,
			'publicly_queryable' => $is_builder_preview,
			'show_in_nav_menus' => TRUE,
			'capability_type' => array( 'us_page_block', 'us_page_blocks' ),
			'map_meta_cap' => TRUE,
			'rewrite' => FALSE,
			'query_var' => FALSE,
			'register_meta_box_cb' => 'us_duplicate_post',
		)
	);

	/*
	* Creates duplication of the post in admin list, called via "Duplicate" link from 'us_post_row_actions_duplicate'
	* also creates post instantly instead of WP auto-draft status
	* also creates additional conditions for "us_header" post types
	*/
	if ( ! function_exists( 'us_duplicate_post' ) ) {
		function us_duplicate_post( $post ) {
			if ( $post->post_status === 'auto-draft' ) {
				// Do not process posts that being created as translations for existing posts
				if ( isset( $_GET['from_post'] ) AND isset( $_GET['new_lang'] ) ) {
					return FALSE;
				}

				// Page for creating new header: creating it instantly and proceeding to editing
				$post_data = array( 'ID' => $post->ID );

				// Retrieve occupied names to generate new post title properly
				$existing_posts = us_get_posts_titles_for( $post->post_type );

				// Handle post duplication
				if ( isset( $_GET['duplicate_from'] ) AND $original_post = get_post( (int) $_GET['duplicate_from'] ) ) {
					$post_data['post_content'] = $original_post->post_content;

					// Add slashes for headers content
					if ( $post->post_type == 'us_header' ) {
						$post_data['post_content'] = wp_slash( $post_data['post_content'] );
					}
					$title_pattern = $original_post->post_title . ' (%d)';
					$cur_index = 2;

					// Handle creation from scratch
				} else {
					$post_obj = get_post_type_object( $post->post_type );
					$title_pattern = $post_obj->labels->singular_name . ' %d';
					$cur_index = count( $existing_posts ) + 1;
				}

				// Generate new post title
				while ( in_array( $post_data['post_title'] = sprintf( $title_pattern, $cur_index ), $existing_posts ) ) {
					$cur_index ++;
				}
				wp_update_post( $post_data );
				wp_publish_post( $post->ID );

				// Redirect
				if ( isset( $_GET['duplicate_from'] ) ) {

					// When duplicating post, showing posts list next
					wp_redirect( admin_url( 'edit.php?post_type=' . $post->post_type ) );
				} else {

					$extra_get = '';
					if ( ! empty( $_GET['from_post'] ) AND ! empty( $_GET['new_lang'] ) ) {
						$extra_get = "&from_post={$_GET['from_post']}&new_lang={$_GET['new_lang']}";
					}

					// When creating from scratch proceeding to post editing next
					wp_redirect( admin_url( 'post.php?post=' . $post->ID . '&action=edit' . $extra_get ) );
				}

				// Add Header Builder actions for headers
			} elseif ( $post->post_type == 'us_header' ) {
				add_filter( 'admin_body_class', 'us_builder_admin_body_class' );
				add_action( 'admin_enqueue_scripts', 'us_hb_enqueue_scripts' );
				add_action( 'edit_form_top', 'us_hb_edit_form_top' );

				// Add Grid Builder actions for grid layouts
			} elseif ( $post->post_type == 'us_grid_layout' ) {
				add_filter( 'admin_body_class', 'us_builder_admin_body_class' );
				add_action( 'admin_enqueue_scripts', 'usgb_enqueue_scripts' );
				add_action( 'edit_form_top', 'usgb_edit_form_top' );
			}
		}
	}

	// Add links to duplicate posts in admin list
	if ( ! function_exists( 'us_post_row_actions_duplicate' ) ) {
		add_filter( 'post_row_actions', 'us_post_row_actions_duplicate', 11, 2 );

		function us_post_row_actions_duplicate( $actions, $post ) {
			$duplicated_post_types = array(
				// 'us_portfolio',
				// 'us_testimonial',
				'us_header',
				'us_grid_layout',
				'us_content_template',
				'us_page_block',
			);
			if ( in_array( $post->post_type, $duplicated_post_types ) ) {

				// Removing duplicate post plugin affection
				unset( $actions['duplicate'], $actions['edit_as_new_draft'] );

				if ( empty( $actions ) ) {
					$actions = array();
				}

				$actions = us_array_merge_insert(
					$actions, array(
					'duplicate' => '<a href="' . admin_url( 'post-new.php?post_type=' . $post->post_type . '&duplicate_from=' . $post->ID ) . '" aria-label="' . esc_attr__( 'Duplicate', 'us' ) . '">' . esc_html__( 'Duplicate', 'us' ) . '</a>',
				), 'before', isset( $actions['trash'] ) ? 'trash' : 'untrash'
				);
			}

			return $actions;
		}
	}

	// Add "Used in" column into Headers admin page
	add_filter( 'manage_us_grid_layout_posts_columns', 'us_post_admin_columns_head' );
	add_action( 'manage_us_grid_layout_posts_custom_column', 'us_post_admin_columns_content', 10, 2 );
	add_filter( 'manage_us_header_posts_columns', 'us_post_admin_columns_head' );
	add_action( 'manage_us_header_posts_custom_column', 'us_post_admin_columns_content', 10, 2 );
	add_filter( 'manage_us_content_template_posts_columns', 'us_post_admin_columns_head' );
	add_action( 'manage_us_content_template_posts_custom_column', 'us_post_admin_columns_content', 10, 2 );
	add_filter( 'manage_us_page_block_posts_columns', 'us_post_admin_columns_head' );
	add_action( 'manage_us_page_block_posts_custom_column', 'us_post_admin_columns_content', 10, 2 );
	if ( ! function_exists( 'us_post_admin_columns_head' ) ) {
		function us_post_admin_columns_head( $defaults ) {
			$result = array();
			foreach ( $defaults as $key => $title ) {
				if ( $key == 'date' ) {
					$result['used_in'] = __( 'Used in', 'us' );
				}
				$result[ $key ] = $title;
			}

			return $result;
		}
	}
	if ( ! function_exists( 'us_post_admin_columns_content' ) ) {
		function us_post_admin_columns_content( $column_name, $post_ID ) {
			if ( $column_name == 'used_in' ) {
				global $wp_query;
				if ( count( $wp_query->posts ) ) {
					// The function itself is able to cache data, it does not need to be taken care of after the call
					$used_in_locations = (array) us_get_all_used_in_locations( wp_list_pluck( $wp_query->posts, 'ID' ) );
					if ( ! empty( $used_in_locations[ $post_ID ] ) ) {
						echo $used_in_locations[ $post_ID ];
					}
				}
			}
		}
	}

	// Remove new lines on post insert - fix for headers import for PHP 7.3
	add_filter( 'wp_insert_post_data', 'us_header_wp_insert_post_data', 11, 2 );
	function us_header_wp_insert_post_data( $data, $postarr ) {
		if ( $data['post_type'] == 'us_header' ) {
			$data['post_content'] = str_replace( array( "\n", "\r" ), '', $data['post_content'] );
		}

		return $data;
	}

	// Add iframe param for posts and pages opened in grid lightbox
	global $us_iframe;
	$us_iframe = ( isset( $_GET['us_iframe'] ) AND $_GET['us_iframe'] == 1 ) ? TRUE : FALSE;
	if ( $us_iframe ) {
		add_filter( 'show_admin_bar', '__return_false' );
		remove_action( 'wp_head', '_admin_bar_bump_cb' );
	}

}

// Portfolio labels
if ( ! function_exists( 'us_portfolio_labels' ) ) {
	add_filter( 'us_portfolio_labels', 'us_portfolio_labels' );

	function us_portfolio_labels( $labels ) {
		if ( us_get_option( 'portfolio_rename', 0 ) ) {
			$portofolio_keys = array( 'name', 'singular_name', 'add_new', 'edit_item' );
			foreach ( $portofolio_keys as $key ) {
				if ( us_get_option( 'portfolio_label_' . $key, '' ) != '' ) {
					$labels[ $key ] = wp_strip_all_tags( us_get_option( 'portfolio_label_' . $key ), TRUE );
					if ( $key == 'add_new' ) {
						$labels['add_new_item'] = $labels['add_new'];
					}
				}
			}
		}

		return $labels;
	}
}

// Portfolio Label Category
if ( ! function_exists( 'us_portfolio_category_label' ) ) {
	add_filter( 'us_portfolio_category_label', 'us_portfolio_category_label' );
	function us_portfolio_category_label( $label ) {
		if ( us_get_option( 'portfolio_rename', 0 ) AND us_get_option( 'portfolio_label_category', '' ) != '' ) {
			$label = wp_strip_all_tags( us_get_option( 'portfolio_label_category' ), TRUE );
		}

		return $label;
	}
}

// Portfolio Label Tags
if ( ! function_exists( 'us_portfolio_tags_label' ) ) {
	add_filter( 'us_portfolio_tags_label', 'us_portfolio_tags_label' );
	function us_portfolio_tags_label( $label ) {
		if ( us_get_option( 'portfolio_rename', 0 ) AND us_get_option( 'portfolio_label_tag', '' ) != '' ) {
			$label = wp_strip_all_tags( us_get_option( 'portfolio_label_tag' ), TRUE );
		}

		return $label;
	}
}

// Set Portfolio Pages slug
if ( us_get_option( 'enable_portfolio', 1 ) ) {
	if ( strpos( $portfolio_slug, '%us_portfolio_category%' ) !== FALSE ) {
		function us_portfolio_link( $post_link, $id = 0 ) {
			$post = get_post( $id );
			if ( is_object( $post ) ) {
				$terms = wp_get_object_terms( $post->ID, 'us_portfolio_category' );
				if ( $terms ) {
					return str_replace( '%us_portfolio_category%', $terms[0]->slug, $post_link );
				} else {
					// If no terms are assigned to this post, use a string instead (can't leave the placeholder there)
					return str_replace( '%us_portfolio_category%', 'uncategorized', $post_link );
				}
			}

			return $post_link;
		}

		add_filter( 'post_type_link', 'us_portfolio_link', 1, 3 );
	} elseif ( $portfolio_slug == '' ) {
		function us_portfolio_remove_slug( $post_link, $post, $leavename ) {
			if ( 'us_portfolio' != $post->post_type OR 'publish' != $post->post_status ) {
				return $post_link;
			}
			$post_link = str_replace( '/' . trailingslashit( $post->post_type ), '/', $post_link );

			return $post_link;
		}

		add_filter( 'post_type_link', 'us_portfolio_remove_slug', 10, 3 );

		function us_portfolio_parse_request( $query ) {
			if ( ! $query->is_main_query() OR 2 != count( $query->query ) OR ! isset( $query->query['page'] ) ) {
				return;
			}
			if ( ! empty( $query->query['name'] ) ) {
				$query->set( 'post_type', array( 'post', 'us_portfolio', 'page' ) );
			}
		}

		add_action( 'pre_get_posts', 'us_portfolio_parse_request' );
	}
}

if ( ! function_exists( 'us_search_query_adjustment' ) ) {
	/**
	 * Search query adjustment
	 *
	 * @param WP_Query $query The query
	 */
	function us_search_query_adjustment( $query ) {
		if ( ! $query->is_search OR is_admin() ) {
			return;
		}
		global $wp_post_types;

		// Always exclude Testimonials, they are public, but don't have the own frontend template
		if ( us_get_option( 'enable_testimonials', 1 ) == 1 AND post_type_exists( 'us_testimonial' ) ) {
			$wp_post_types['us_testimonial']->exclude_from_search = TRUE;
		}

		// Excluded post types, specified by user in theme options
		$exclude_post_types = us_get_option( 'exclude_post_types_in_search' );

		// If no post types were set to be excluded, abort following execution
		if ( empty( $exclude_post_types ) ) {
			return;
		}

		// If some post type is set explicitly via URL params, abort following execution
		if ( ! empty( $_GET['post_type'] ) ) {
			return;
		}

		// If post_type is already set in WP Query, abort following execution
		if ( ! empty( $query->query_vars['post_type'] ) ) {
			return;
		}

		// Getting list of all public post types
		$post_types = function_exists( 'us_get_public_post_types' )
			? array_keys( us_get_public_post_types() )
			: array();

		// Failsafe - if somehow post types array is empty, abort following execution
		if ( empty( $post_types ) ) {
			return;
		}

		// Fallback for var type
		if ( is_array( $exclude_post_types ) ) {
			$exclude_post_types = implode( ',', $exclude_post_types );
		}

		foreach ( $post_types as $key => $item ) {
			if ( strpos( $exclude_post_types, $item ) !== FALSE ) {
				unset( $post_types[ $key ] );
			}
		}

		$query->query_vars['post_type'] = array_unique( $post_types );

		// If all types were excluded, then add a nonexistent one and a message will be displayed
		if ( empty( $query->query_vars['post_type'] ) ) {
			$query->query_vars['post_type'] = '_not_selected_post_types_';
		}
	}

	add_action( 'pre_get_posts', 'us_search_query_adjustment' );
}

// Add admin capabilities to Portfolio, Testimonials, Page Blocks, Content Template
add_action( 'admin_init', 'us_add_theme_caps' );
function us_add_theme_caps() {
	global $wp_post_types;
	$role = get_role( 'administrator' );
	if ( empty( $role ) ) {
		return;
	}
	$force_refresh = FALSE;
	$custom_post_types = array( 'us_portfolio', 'us_testimonial', 'us_page_block', 'us_content_template' );
	foreach ( $custom_post_types as $post_type ) {
		if ( ! post_type_exists( $post_type ) ) {
			continue;
		}
		foreach ( $wp_post_types[ $post_type ]->cap as $cap ) {
			if ( ! $role->has_cap( $cap ) ) {
				$role->add_cap( $cap );
				$force_refresh = TRUE;
			}
		}
	}
	if ( $force_refresh AND current_user_can( 'manage_options' ) AND ! isset( $_COOKIE['us_cap_page_refreshed'] ) ) {
		// To prevent infinite refreshes when the DB is not writable
		setcookie( 'us_cap_page_refreshed' );
		header( 'Refresh: 0' );
	}
}

// Add role capabilities to Portfolio & Testimonials
add_action( 'admin_init', 'us_theme_activation_add_caps' );
function us_theme_activation_add_caps() {
	global $pagenow;
	if ( is_admin() AND $pagenow == 'themes.php' AND isset( $_GET['activated'] ) ) {
		if ( ! defined( 'US_THEMENAME' ) ) {
			return;
		}
		if ( get_option( US_THEMENAME . '_editor_caps_set' ) == 1 ) {
			return;
		}
		update_option( US_THEMENAME . '_editor_caps_set', 1 );
		global $wp_post_types;
		$role = get_role( 'editor' );
		if ( empty( $role ) ) {
			return;
		}
		$custom_post_types = array( 'us_portfolio', 'us_testimonial' );
		foreach ( $custom_post_types as $post_type ) {
			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}
			foreach ( $wp_post_types[ $post_type ]->cap as $cap ) {
				if ( ! $role->has_cap( $cap ) ) {
					$role->add_cap( $cap );
				}
			}
		}
	}
}

// Remove not public post types from insert/edit link dialog
add_filter( 'wp_link_query_args', 'us_link_query_filter' );
function us_link_query_filter( $query ) {

	$not_public_post_types = get_post_types(
		array(
			'publicly_queryable' => FALSE,
			'_builtin' => FALSE,
		)
	);

	foreach ( $query['post_type'] as $key => $value ) {
		if ( in_array( $value, $not_public_post_types ) ) {
			unset( $query['post_type'][ $key ] );
		}
	}

	return $query;
}

// Add needed filters to Page Block and Content Template content
foreach ( array( 'page_block', 'content_template' ) as $page_type_name ) {
	add_filter( 'us_' . $page_type_name . '_the_content', 'wptexturize' );
	add_filter( 'us_' . $page_type_name . '_the_content', 'wpautop' );
	add_filter( 'us_' . $page_type_name . '_the_content', 'shortcode_unautop' );
	if ( ! function_exists( 'wp_filter_content_tags' ) ) {
		// Deprecated since WP 5.5
		add_filter( 'us_' . $page_type_name . '_the_content', 'wp_make_content_images_responsive' );
	} else {
		add_filter( 'us_' . $page_type_name . '_the_content', 'wp_filter_content_tags' );
	}
	add_filter( 'us_' . $page_type_name . '_the_content', 'do_shortcode', 12 );
	add_filter( 'us_' . $page_type_name . '_the_content', 'convert_smilies', 20 );
}

// Remember extra IDs when save post. For "Used in" UI
add_action( 'save_post', 'us_save_post_add_in_content_ids' );
function us_save_post_add_in_content_ids( $post_id ) {
	$ids = array();
	$post = get_post( $post_id );
	$the_content = $post->post_content;

	// Add Grid Layouts IDs
	if ( preg_match_all( '/\[us_grid[^\]]+items_layout="([0-9]+)"/i', $the_content, $matches ) ) {
		$ids = array_merge( $ids, $matches[1] );
	}
	if ( preg_match_all( '/\[us_carousel[^\]]+items_layout="([0-9]+)"/i', $the_content, $matches ) ) {
		$ids = array_merge( $ids, $matches[1] );
	}

	// Add Page Blocks IDs
	if ( preg_match_all( '/\[us_page_block[^\]]+id="([0-9]+)"/i', $the_content, $matches ) ) {
		$ids = array_merge( $ids, $matches[1] );
	}

	// Add Page Blocks used in Popups
	if (
		preg_match_all( '/\[us_popup[^\]]+use_page_block="([0-9]+)"/i', $the_content, $matches )
		OR (
			$post->post_type === 'us_header'
			AND preg_match_all( '/\"use_page_block\":\"(\d+)\"/i' , $the_content, $matches )
		)
	) {
		$ids = array_merge( $ids, $matches[1] );
	}

	if ( count( $ids ) > 0 ) {
		$ids = implode( ',', $ids );
	} else {
		$ids = '';
	}

	update_post_meta( $post_id, '_us_in_content_ids', $ids );
}

// Should the post be visible for the current language?
function us_is_post_visible_for_curr_lang( $post, $page_block_ID = NULL ) {
	$is_post_visible_for_curr_lang = TRUE;
	if ( has_filter( 'us_tr_get_post_language_code' ) ) {
		$post_language_code = apply_filters( 'us_tr_get_post_language_code', $post->ID );
		$page_block_language_code = apply_filters( 'us_tr_get_post_language_code', $page_block_ID );
		if ( $page_block_language_code != $post_language_code ) {
			$is_post_visible_for_curr_lang = FALSE;
		}
	}

	return $is_post_visible_for_curr_lang;
}

if ( ! function_exists( 'us_get_used_in_locations' ) ) {
	/**
	 * Generate all locations names where used specific element
	 *
	 * @param array $post_ID
	 * @param bool $show_no_results
	 * @return array
	 */
	function us_get_all_used_in_locations( $post_IDs, $show_no_results = FALSE ) {
		if ( empty( $post_IDs ) OR ! is_array( $post_IDs ) ) {
			return array();
		}

		$ids = array_unique( array_map( 'intval', $post_IDs ) );
		static $results = array();

		$is_empty_result = FALSE;
		foreach ( $ids as $id ) {
			if ( ! isset( $results[ $id ] ) ) {
				$is_empty_result = TRUE;
				break;
			}
		}

		if ( $is_empty_result ) {
			global $usof_options, $wpdb;
			usof_load_options_once();

			$used_in = $posts_types = array();
			$areas = array(
				'header' => '',
				'titlebar' => ' > ' . __( 'Titlebar', 'us' ),
				'sidebar' => ' > ' . __( 'Sidebar', 'us' ),
				'content' => '',
				'footer' => ' > ' . __( 'Footer', 'us' ),
			);

			foreach ( $ids as $id ) {
				$used_in[ $id ] = array(
					'theme_options' => array(),
					'singulars_meta' => array(),
					'singulars_content' => array(),
					'nav_menu_item' => array(),
				);
				$results[ $id ] = '';
				$posts_types[ $id ] = get_post_type( $id );
			}

			// Theme Options > Pages Layout
			foreach ( us_get_public_post_types( array( 'product' ) ) as $type => $title ) {
				// Fix suffixes regarding historical theme options names
				switch ( $type ) {
					case 'page':
						$type = '';
						break;
					case 'us_portfolio':
						$type = '_portfolio';
						break;
					default:
						$type = '_' . $type;
						break;
				}

				$edit_link = ' (<a href="' . admin_url() . 'admin.php?page=us-theme-options#pages_layout" target="_blank" rel="noopener">' . __( 'edit in Theme Options', 'us' ) . '</a>)</div>';

				foreach ( $ids as $id ) {
					foreach ( $areas as $area => $area_name ) {
						if ( isset( $usof_options[ $area . $type . '_id' ] ) AND $usof_options[ $area . $type . '_id' ] == $id ) {
							$used_in[ $id ]['theme_options'][] = '<div><strong>' . $title . $area_name . '</strong>' . $edit_link;
						}
					}
				}
			}

			// Theme Options > Archives Layout
			$archives_layout_types = array_merge(
				array(
					'archive' => us_translate( 'Archives' ),
					'author' => __( 'Authors', 'us' ),
				), us_get_taxonomies( TRUE, FALSE, 'woocommerce_exclude' )
			);

			foreach ( $archives_layout_types as $type => $title ) {
				if ( ! in_array( $type, array( 'archive', 'author' ) ) ) {
					$type = 'tax_' . $type;
				}
				$edit_link = ' (<a href="' . admin_url() . 'admin.php?page=us-theme-options#archives_layout" target="_blank" rel="noopener">' . __( 'edit in Theme Options', 'us' ) . '</a>)</div>';
				foreach ( $ids as $id ) {
					foreach ( $areas as $area => $area_name ) {
						if ( isset( $usof_options[ $area . '_' . $type . '_id' ] ) AND $usof_options[ $area . '_' . $type . '_id' ] == $id ) {
							$used_in[ $id ]['theme_options'][] = '<div><strong>' . $title . $area_name . '</strong>' . $edit_link;
						}
					}
				}
			}

			// Theme Options > Shop
			if ( class_exists( 'woocommerce' ) ) {
				$woocommerce_types = array_merge(
					array(
						'product' => us_translate( 'Products', 'woocommerce' ),
						'shop' => us_translate( 'Shop Page', 'woocommerce' ),
					), us_get_taxonomies( TRUE, FALSE, 'woocommerce_only' )
				);

				foreach ( $woocommerce_types as $type => $title ) {
					if ( ! in_array( $type, array( 'product', 'shop' ) ) ) {
						$type = 'tax_' . $type;
					}

					$edit_link = ' (<a href="' . admin_url() . 'admin.php?page=us-theme-options#woocommerce" target="_blank" rel="noopener">' . __( 'edit in Theme Options', 'us' ) . '</a>)</div>';

					foreach ( $ids as $id ) {
						foreach ( $areas as $area => $area_name ) {
							if ( isset( $usof_options[ $area . '_' . $type . '_id' ] ) AND $usof_options[ $area . '_' . $type . '_id' ] == $id ) {
								$used_in[ $id ]['theme_options'][] = '<div><strong>' . $title . $area_name . '</strong>' . $edit_link;
							}
						}
					}
				}
			}

			// Append locations to result string
			foreach ( $ids as $id ) {
				$results[ $id ] .= implode( $used_in[ $id ]['theme_options'] );
			}

			// Singulars (metabox)
			if ( ! empty( $areas ) ) {
				$usage_meta_keys = array_map(
					function ( $area ) {
						return sprintf( 'us_%s_id', $area );
					}, array_keys( $areas )
				);

				$usage_query = "
					SELECT
						post_id, meta_key, meta_value
					FROM {$wpdb->postmeta}
					WHERE
						meta_key IN( '" . implode( "','", $usage_meta_keys ) . "' )
						AND meta_value IN( '" . implode( "','", $ids ) . "' )
					LIMIT 0, 100";

				foreach ( $wpdb->get_results( $usage_query ) as $usage_result ) {
					if ( $post = get_post( $usage_result->post_id ) ) {
						foreach ( $ids as $id ) {
							if (
								in_array( $id, explode( ',', $usage_result->meta_value ) )
								AND us_is_post_visible_for_curr_lang( $post, $id )
							) {
								if ( empty( $post_title = get_the_title( $post->ID ) ) ) {
									$post_title = us_translate( '(no title)' );
								}

								$used_in[ $id ]['singulars_meta'][] = '<div><a href="' . get_permalink( $post->ID ) . '" target="_blank" rel="noopener" title="' . us_translate( 'View Page' ) . '">' . $post_title . '</a>' . us_arr_path( $areas, $usage_result->meta_key, '' ) . '</div>';
							}
						}
					}
				}
			}

			// Append locations to result string
			foreach ( $ids as $id ) {
				$results[ $id ] .= implode( $used_in[ $id ]['singulars_meta'] );
			}

			// Singulars (content)
			$meta_value_like = '';
			foreach ( $ids as $id ) {
				if ( ! empty( $meta_value_like ) ) {
					$meta_value_like .= ' OR';
				}
				$meta_value_like .= " meta_value LIKE '%" . $id . "%'";
			}

			$usage_query = "
				SELECT
					post_id, meta_value
				FROM {$wpdb->postmeta}
				WHERE
					meta_key = '_us_in_content_ids'
					AND ( {$meta_value_like} )
				LIMIT 0, 100";

			foreach ( $wpdb->get_results( $usage_query ) as $usage_result ) {
				if ( $post = get_post( $usage_result->post_id ) ) {
					foreach ( $ids as $id ) {
						if (
							in_array( $id, explode( ',', $usage_result->meta_value ) )
							AND us_is_post_visible_for_curr_lang( $post, $id )
						) {
							$used_in[ $id ]['singulars_content'][ $post->ID ] = array(
								'url' => get_permalink( $post->ID ),
								'edit_url' => get_edit_post_link( $post->ID ),
								'title' => ( get_the_title( $post->ID ) != '' )
									? get_the_title( $post->ID )
									: us_translate( '(no title)' ),
								'post_type' => ! empty( $posts_types[ $id ] )
									? $posts_types[ $id ]
									: NULL,
							);
						}
					}
				}
			}

			// Append locations to result string
			foreach ( $ids as $id ) {
				if ( ! empty( $used_in[ $id ] ) AND ! empty( $used_in[ $id ]['singulars_content'] ) ) {
					foreach ( $used_in[ $id ]['singulars_content'] as $location ) {
						switch ( $location['post_type'] ) {
							case 'us_page_block':
								$url = $location['edit_url'];
								$title = __( 'Edit Page Block', 'us' );
								break;
							case 'us_content_template':
								$url = $location['edit_url'];
								$title = __( 'Edit Content template', 'us' );
								break;
							default:
								$url = $location['url'];
								$title = us_translate( 'View Page' );
								break;
						}

						$results[ $id ] .= '<div><a href="' . $url . '" target="_blank" rel="noopener" title="' . $title . '">' . $location['title'] . '</a></div>';
					}
				}
			}

			// Widgets (for Grid Layouts only)
			$regexp_layouts = array();
			foreach ( $ids as $id ) {
				$regexp_layouts[] = strlen( $id ) . ':"' . $id;
			}
			$regexp_layouts = implode( '|', $regexp_layouts );

			$usage_query = "
				SELECT
					`option_name`, `option_value`
				FROM {$wpdb->options}
				WHERE
					option_name LIKE 'widget%'
					AND option_value REGEXP '\"layout\";s:({$regexp_layouts})\"'
				LIMIT 0, 100";

			if ( $widget_options = $wpdb->get_results( $usage_query ) ) {
				global $wp_registered_sidebars, $wp_registered_widgets;

				$_widget_titles = $_sidebars_widgets = array();

				// Get widget_id => Sidebar name
				foreach ( wp_get_sidebars_widgets() as $sidebar_id => $widget_ids ) {
					if ( $sidebar_id === 'wp_inactive_widgets' OR ! isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
						continue;
					}

					$_sidebars_widgets = array_merge(
						$_sidebars_widgets,
						array_fill_keys( array_values( $widget_ids ), $wp_registered_sidebars[ $sidebar_id ]['name'] )
					);
				}

				// Get widget name
				foreach ( $wp_registered_widgets as $base_id => $widget ) {
					foreach ( $widget['callback'] as $callback ) {
						if ( isset( $callback->option_name, $_sidebars_widgets[ $base_id ] ) ) {
							$number = substr( $base_id, mb_strlen( $callback->id_base . '-' ) );
							$_widget_titles[ $callback->option_name ][ $number ] = [
								'sidebar_name' => $_sidebars_widgets[ $base_id ],
								'name' => $callback->name,
							];
						}
					}
				}
				unset( $_sidebars_widgets );

				// Creating links for widgets
				foreach ( $widget_options as $usage_result ) {
					foreach ( $ids as $id ) {
						foreach ( unserialize( $usage_result->option_value ) as $number => $value ) {
							if ( ! is_array( $value ) OR ! isset( $value['layout'] ) OR $value['layout'] != $id ) {
								continue;
							}

							$_widget = isset( $_widget_titles[ $usage_result->option_name ][ $number ] )
								? $_widget_titles[ $usage_result->option_name ][ $number ]
								: [];

							$name = isset( $_widget['name'] )
								? $_widget['name']
								: '';

							if ( ! empty( $value['title'] ) ) {
								$name .= ': ' . $value['title'];
							}

							$sidebar_name = isset( $_widget['sidebar_name'] )
								? $_widget['sidebar_name'] . ' > '
								: '';

							// NOTE: The widget is in the config because it is not deleted, you can find it on
							// the widgets page in the "Inactive Sidebar (not used)" action, but we do not display this.
							if ( empty( $sidebar_name ) ) {
								continue;
							}

							$result .= '<div>' . esc_html( $sidebar_name ) . '<a href="' . admin_url() . 'widgets.php">' . esc_html( $name ) . '</a></div>';
							unset( $_widget, $name, $sidebar_name );
						}

						foreach ( unserialize( $usage_result->option_value ) as $number => $value ) {
							if ( ! is_array( $value ) OR ! isset( $value['layout'] ) OR $value['layout'] != $post_ID ) {
								continue;
							}

							$_widget = isset( $_widget_titles[ $usage_result->option_name ][ $number ] )
								? $_widget_titles[ $usage_result->option_name ][ $number ]
								: [];

							$name = isset( $_widget['name'] )
								? $_widget['name']
								: '';

							if ( ! empty( $value['title'] ) ) {
								$name .= ': ' . $value['title'];
							}

							$sidebar_name = isset( $_widget['sidebar_name'] )
								? $_widget['sidebar_name'] . ' > '
								: '';

							// NOTE: The widget is in the config because it is not deleted, you can find it on
							// the widgets page in the "Inactive Sidebar (not used)" action, but we do not display this.
							if ( empty( $sidebar_name ) ) {
								continue;
							}

							$results[ $id ] .= '<div>' . esc_html( $sidebar_name ) . '<a href="' . admin_url() . 'widgets.php">' . esc_html( $name ) . '</a></div>';
							unset( $_widget, $name, $sidebar_name );
						}
					}
				}
			}

			$group_posts_types = array();
			foreach ( $posts_types as $id => $post_type ) {
				$group_posts_types[ $post_type ][] = $id;
			}

			// Content template
			if ( ! empty( $group_posts_types['us_content_template'] ) ) {
				$usage_meta_keys = array(
					'archive_content_id' => 'Archives',
					'pages_content_id' => 'Pages',
				);

				$usage_query = "
					SELECT
						`tm`.`term_id`, `t`.`name`, `tt`.`taxonomy`, `tm`.`meta_key`, `tm`.`meta_value`
					FROM {$wpdb->termmeta} AS `tm`
					LEFT JOIN {$wpdb->terms} AS `t`
						ON `tm`.`term_id` = `t`.`term_id`
					LEFT JOIN {$wpdb->term_taxonomy} AS `tt`
						ON `tm`.`term_id` = `tt`.`term_id`
					WHERE
						`tm`.`meta_key` IN( '" . implode( "','", array_keys( $usage_meta_keys ) ) . "' )
						AND `tm`.`meta_value` IN( '" . implode( "','", $group_posts_types['us_content_template'] ) . "' )
					LIMIT 0, 100;
				";

				foreach ( $wpdb->get_results( $usage_query ) as $usage_result ) {
					foreach ( $group_posts_types['us_content_template'] as $id ) {
						if (
							in_array( $id, explode( ',', $usage_result->meta_value ) )
							AND $tax = get_taxonomy( $usage_result->taxonomy )
						) {
							$result = '<div><strong>' . $tax->label . ' > ';
							$result .= $usage_result->name . ' > ';
							$result .= us_translate( $usage_meta_keys[ $usage_result->meta_key ] ) . '</strong>';
							$result .= ' (<a href="term.php?taxonomy=' . esc_attr( $usage_result->taxonomy );
							$result .= '&tag_ID=' . (int) $usage_result->term_id;
							$result .= '&post_type=' . esc_attr( $tax->object_type[0] );
							$result .= '" target="_blank" rel="noopener">' . us_translate( 'Edit' ) . '</a>)</div>';
							$results[ $id ] .= $result;
						}
					}
				}
			}

			// Menus (nav_menu_item) for Page Blocks only
			if ( ! empty( $group_posts_types['us_page_block'] ) ) {
				$meta_value_like = '';
				foreach ( $group_posts_types['us_page_block'] as $id ) {
					if ( ! empty( $meta_value_like ) ) {
						$meta_value_like .= ' OR';
					}
					$meta_value_like .= " meta1.meta_value LIKE '%" . $id . "%'";
				}

				$usage_query = "
					SELECT
						meta1.post_id, meta1.meta_value
					FROM {$wpdb->postmeta} meta1
					LEFT JOIN {$wpdb->postmeta} meta2
						ON (
							meta1.post_id = meta2.post_id
							AND meta2.meta_key = '_menu_item_object'
							AND meta2.meta_value = 'us_page_block'
						)
					WHERE
						meta1.meta_key = '_menu_item_object_id'
						AND ( {$meta_value_like} )
					LIMIT 0, 100
				";

				foreach ( $wpdb->get_results( $usage_query ) as $usage_result ) {
					foreach ( $ids as $id ) {
						if (
							in_array( $id, explode( ',', $usage_result->meta_value ) )
							AND $post = get_post( $usage_result->post_id )
						) {
							$used_in[ $id ]['nav_menu_item'][ $post->ID ] = wp_get_post_terms( $post->ID, 'nav_menu', array( 'fields' => 'all' ) );
						}
					}
				}
			}

			// Append locations to result string
			foreach ( $ids as $id ) {
				if ( ! empty( $used_in[ $id ] ) AND ! empty( $used_in[ $id ]['nav_menu_item'] ) ) {
					foreach ( $used_in[ $id ]['nav_menu_item'] as $location ) {
						if ( ! empty( $location ) ) {
							$results[ $id ] .= '<div><strong>' . us_translate( 'Menus' ) . '</strong> > <a href="nav-menus.php?action=edit&menu=' . $location[0]->term_id . '" target="_blank" rel="noopener" title="' . us_translate( 'Edit Menu' ) . '">' . $location[0]->name . '</a></div>';
						}
					}
				}
			}

			// Return "No results" message if set
			foreach ( $results as &$result ) {
				if ( empty( $result ) AND $show_no_results ) {
					$result = us_translate( 'No results found.' );
				}
			}
		}

		return $results;
	}

	/**
	 * Generate locations names where used specific element
	 *
	 * @param int $post_ID
	 * @param bool $show_no_results
	 * @return string
	 */
	function us_get_used_in_locations( $post_ID, $show_no_results = FALSE ) {
		$results = (array) us_get_all_used_in_locations( array( $post_ID ), $show_no_results );

		return ! empty( $results[ $post_ID ] )
			? $results[ $post_ID ]
			: '';
	}
}
