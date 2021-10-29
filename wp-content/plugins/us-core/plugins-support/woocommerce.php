<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WooCommerce Theme Support
 *
 * @link http://www.woothemes.com/woocommerce/
 */

if ( ! class_exists( 'woocommerce' ) ) {
	return FALSE;
}

add_action( 'after_setup_theme', 'us_woocommerce_support' );
function us_woocommerce_support() {
	add_theme_support(
		'woocommerce', array(
			'gallery_thumbnail_image_width' => 150, // changed gallery thumbnail size to default WP 'thumbnail'
		)
	);
	if ( in_array( 'zoom', us_get_option( 'product_gallery_options', array() ) ) ) {
		add_theme_support( 'wc-product-gallery-zoom' );
	}
	if ( in_array( 'lightbox', us_get_option( 'product_gallery_options', array() ) ) ) {
		add_theme_support( 'wc-product-gallery-lightbox' );
	}
	if ( us_get_option( 'product_gallery' ) == 'slider' ) {
		add_theme_support( 'wc-product-gallery-slider' );
	}
}

// Change size of "Product gallery" thumbnails, when "Slider" is OFF, for showing like 1 column gallery
if ( us_get_option( 'product_gallery' ) !== 'slider' ) {
	add_filter( 'woocommerce_gallery_thumbnail_size', 'us_woocommerce_gallery_thumbnail_size' );
	function us_woocommerce_gallery_thumbnail_size() {
		return 'woocommerce_single';
	}
}

// Change columns count for Product gallery thumbs
if ( ! function_exists( 'us_woocommerce_product_thumbnails_columns' ) ) {
	add_filter( 'woocommerce_product_thumbnails_columns', 'us_woocommerce_product_thumbnails_columns', 100, 1 );
	function us_woocommerce_product_thumbnails_columns( $cols ) {
		return (int) us_get_option( 'product_gallery_thumbs_cols', 4 );
	}
}

// Disable WooCommerce front CSS
add_filter( 'woocommerce_enqueue_styles', '__return_false' );

// Disable select2 CSS on Checkout page
add_action( 'wp_enqueue_scripts', 'us_woocomerce_dequeue_checkout_styles', 100 );
function us_woocomerce_dequeue_checkout_styles() {
	wp_dequeue_style( 'select2' );
	wp_deregister_style( 'select2' );
}

// Enqueue theme CSS
if ( defined( 'US_DEV' ) OR ! us_get_option( 'optimize_assets', 0 ) ) {
	add_action( 'wp_enqueue_scripts', 'us_woocommerce_enqueue_styles', 14 );
}
function us_woocommerce_enqueue_styles( $styles ) {
	global $us_template_directory_uri;
	$min_ext = defined( 'US_DEV' ) ? '' : '.min';
	wp_enqueue_style( 'us-woocommerce', $us_template_directory_uri . '/common/css/plugins/woocommerce' . $min_ext . '.css', array(), US_THEMEVERSION, 'all' );
}

// Add classes to <body> of WooCommerce pages
add_action( 'body_class', 'us_wc_body_class' );
function us_wc_body_class( $classes ) {
	$classes[] = 'us-woo-cart_' . us_get_option( 'shop_cart', 'standard' );
	if ( us_get_option( 'shop_catalog', 0 ) == 1 ) {
		$classes[] = 'us-woo-catalog';
	}

	return $classes;
}

/*
*************** Adjust HTML markup for all WooCommerce pages ***************
*/
add_action( 'template_redirect', 'us_maybe_change_woocommerce_template_path' );
function us_maybe_change_woocommerce_template_path() {
	$custom_content = FALSE;

	// Get WooCommerce taxonomies only
	$woo_taxonomies = array_keys( us_get_taxonomies( TRUE, FALSE, 'woocommerce_only' ) );

	// Get taxonomies linked to Products (created via CPT UI)
	$product_taxonomies = get_object_taxonomies( 'product' );

	// Check if the current page is Shop and it has custom Content template
	if ( is_shop() AND us_get_option( 'content_shop_id', '' ) != '' ) {
		$custom_content = TRUE;

		// Check if the current page is WooCommerce taxonomy
	} elseif ( is_tax( $woo_taxonomies ) ) {
		$current_tax = get_query_var( 'taxonomy' );

		// Check if the current taxonomy has custom Content template
		if ( us_get_option( 'content_tax_' . $current_tax . '_id', '__defaults__' ) != '__defaults__' ) {
			$custom_content = TRUE;

		} elseif ( us_get_option( 'content_shop_id', '' ) != '' ) {
			$custom_content = TRUE;

			// Check if the current term has custom Content template for its Archive
		} elseif ( is_numeric( get_term_meta( get_queried_object_id(), 'archive_content_id', TRUE ) ) ) {
			$custom_content = TRUE;
		}

		// Check if the current page is Product custom taxonomy
	} elseif ( is_tax( $product_taxonomies ) ) {
		$current_tax = get_query_var( 'taxonomy' );

		// Check if the current taxonomy has custom Content template
		if ( us_get_option( 'content_tax_' . $current_tax . '_id', '__defaults__' ) != '__defaults__' ) {
			$custom_content = TRUE;
		} elseif ( us_get_option( 'content_archive_id', '' ) != '' ) {
			$custom_content = TRUE;
		}
	}

	// Change path to templates, if custom content is set
	if ( $custom_content ) {
		add_filter( 'woocommerce_template_path', 'us_woocommerce_template_path' );
		function us_woocommerce_template_path() {
			return 'wc-templates/';
		}
	}
}

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
if ( ! function_exists( 'us_woocommerce_before_main_content' ) ) {
	function us_woocommerce_before_main_content() {
		$show_shop_section = TRUE;

		if ( function_exists( 'us_register_context_layout' ) ) {
			us_register_context_layout( 'main' );
		}

		if ( is_single() ) {
			$content_area_id = us_get_page_area_id( 'content' );
			if ( $content_area_id != '' AND get_post_status( $content_area_id ) != FALSE ) {
				$show_shop_section = FALSE;
				add_filter( 'wc_get_template_part', 'us_wc_get_template_part_content_single_product', 10, 3 );
			}
		}

		echo '<main id="page-content" class="l-main">';

		if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) {

			// Titlebar, if it is enabled in Theme Options
			us_load_template( 'templates/titlebar' );

			// START wrapper for Sidebar
			us_load_template( 'templates/sidebar', array( 'place' => 'before' ) );
		}

		// Output content of Shop page in a first separate section
		if (
			is_post_type_archive( 'product' )
			AND ! is_search()
			AND absint( get_query_var( 'paged' ) ) === 0
			AND $shop_page = get_post( wc_get_page_id( 'shop' ) )
			AND $shop_page_content = apply_filters( 'the_content', $shop_page->post_content )
			) {
			if ( strpos( $shop_page_content, ' class="l-section' ) === FALSE ) {
				$shop_page_content = '<section class="l-section for_shop_description"><div class="l-section-h i-cf">' . $shop_page_content . '</div></section>';
			}
			echo $shop_page_content;
		}

		if ( $show_shop_section ) {
			echo '<section id="shop" class="l-section height_' . us_get_option( 'row_height', 'medium' ) . ' for_shop">';
			echo '<div class="l-section-h i-cf">';
		}
	}
	add_action( 'woocommerce_before_main_content', 'us_woocommerce_before_main_content', 10 );
}

function us_wc_get_template_part_content_single_product( $template, $slug, $name = '' ) {
	if ( $slug == 'content' AND $name == 'single-product' ) {

		// Output form only, if single Product is password protected
		if ( post_password_required() ) {
			echo '<section class="l-section height_' . us_get_option( 'row_height', 'medium' ) . '"><div class="l-section-h">' . get_the_password_form() . '</div></section>';
			return;
		} else {
			return us_locate_file( 'templates/content.php' );
		}

	} else {
		return $template;
	}
}

remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
if ( ! function_exists( 'us_woocommerce_after_main_content' ) ) {
	add_action( 'woocommerce_after_main_content', 'us_woocommerce_after_main_content', 20 );
	function us_woocommerce_after_main_content() {
		$show_shop_section = TRUE;

		if ( is_single() ) {
			$content_area_id = us_get_page_area_id( 'content' );
			if ( $content_area_id != '' AND get_post_status( $content_area_id ) != FALSE ) {
				$show_shop_section = FALSE;
			}
		}

		if ( $show_shop_section ) {
			echo '</div></section>';
		}

		if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) {
			// AFTER wrapper for Sidebar
			us_load_template( 'templates/sidebar', array( 'place' => 'after' ) );
		}
		echo '</main>';
	}
}

// Change columns number on Shop page (from Theme Options > Shop)
add_filter( 'loop_shop_columns', 'loop_columns' );
if ( ! function_exists( 'loop_columns' ) ) {
	function loop_columns() {
		return us_get_option( 'shop_columns', 4 );
	}
}

// Change items number on Shop page (from Theme Options > Shop)
add_filter( 'loop_shop_per_page', 'us_loop_shop_per_page' );
if ( ! function_exists( 'us_loop_shop_per_page' ) ) {
	function us_loop_shop_per_page() {
		return get_option( 'posts_per_page' );
	}
}

// Change Related Products quantity (from Theme Options > Shop)
add_filter( 'woocommerce_output_related_products_args', 'us_related_products_args' );
function us_related_products_args( $args ) {
	$args['posts_per_page'] = us_get_option( 'product_related_qty', 4 );
	$args['columns'] = us_get_option( 'product_related_qty', 4 );

	return $args;
}

// Change Cross-sells quantity (from Theme Options > Shop)
add_filter( 'woocommerce_cross_sells_total', 'us_woocommerce_cross_sells_total' );
add_filter( 'woocommerce_cross_sells_columns', 'us_woocommerce_cross_sells_total' );
function us_woocommerce_cross_sells_total( $count ) {
	return us_get_option( 'product_related_qty', 4 );
}

// Remove default woocommerce sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Move cross sells bellow the shipping
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 10 );

// Move breadcrumbs before product title on Products default template
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 3 );

// Alter Cart - add total number
add_filter( 'woocommerce_add_to_cart_fragments', 'us_add_to_cart_fragments' );
function us_add_to_cart_fragments( $fragments ) {
	global $woocommerce;

	$fragments['a.cart-contents'] = '<a class="cart-contents" href="' . esc_url( wc_get_cart_url() ) . '">' . $woocommerce->cart->get_cart_total() . '</a>';

	return $fragments;
}

// Correct pagination
if ( ! function_exists( 'woocommerce_pagination' ) ) {
	function woocommerce_pagination() {
		global $us_woo_disable_pagination;
		if ( isset( $us_woo_disable_pagination ) AND $us_woo_disable_pagination ) {
			return;
		}

		global $wp_query;
		if ( $wp_query->max_num_pages <= 1 ) {
			return;
		}
		the_posts_pagination(
			array(
				'mid_size' => 3,
				'before_page_number' => '<span>',
				'after_page_number' => '</span>',
			)
		);
	}
}

// Remove focus state on Checkout page
add_filter( 'woocommerce_checkout_fields', 'us_woocommerce_disable_autofocus_billing_firstname' );
function us_woocommerce_disable_autofocus_billing_firstname( $fields ) {
	$fields['shipping']['shipping_first_name']['autofocus'] = FALSE;

	return $fields;
}

// Wrap attributes <select> for better styling
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'us_woocommerce_dropdown_variation_attribute_options_html' );
function us_woocommerce_dropdown_variation_attribute_options_html( $html ) {
	$html = '<div class="woocommerce-select">' . $html . '</div>';

	return $html;
}

// Add amount of products in cart to show in Header element
add_action( 'woocommerce_after_mini_cart', 'us_woocommerce_after_mini_cart' );
function us_woocommerce_after_mini_cart() {
	global $woocommerce;

	echo '<span class="us_mini_cart_amount" style="display: none;">' . $woocommerce->cart->cart_contents_count . '</span>';
}

// Wrap "Add To Cart" button's text with placehoders.
add_action( 'woocommerce_before_template_part', 'us_woocommerce_before_loop_add_to_cart_template_part', 10, 4 );
function us_woocommerce_before_loop_add_to_cart_template_part( $template_name, $template_path, $located, $args ) {
	if ( $template_name == 'loop/add-to-cart.php' ) {
		add_filter( 'woocommerce_product_add_to_cart_text', 'us_add_to_cart_text', 99, 2 );
		add_filter( 'woocommerce_loop_add_to_cart_link', 'us_add_to_cart_text_replace', 99, 3 );
	}
}

add_action( 'woocommerce_after_template_part', 'us_woocommerce_after_loop_add_to_cart_template_part', 10, 4 );
function us_woocommerce_after_loop_add_to_cart_template_part( $template_name, $template_path, $located, $args ) {
	if ( $template_name == 'loop/add-to-cart.php' ) {
		remove_filter( 'woocommerce_product_add_to_cart_text', 'us_add_to_cart_text', 99 );
		remove_filter( 'woocommerce_loop_add_to_cart_link', 'us_add_to_cart_text_replace', 99 );
	}
}

// Use placeholders instead of actual HTML semantics, because after this filter the esc_html() function is applied
function us_add_to_cart_text( $text, $product ) {
	$text = '{{us_add_to_cart_start}}' . $text . '{{us_add_to_cart_end}}';

	return $text;
}

// Replace placeholders with actual HTML wrapper for "Add To Cart" buttons
function us_add_to_cart_text_replace( $html, $product, $args ) {
	$html = str_replace( '{{us_add_to_cart_start}}', '<i class="g-preloader type_1"></i><span class="w-btn-label">', $html );
	$html = str_replace( '{{us_add_to_cart_end}}', '</span>', $html );

	return $html;
}

// Remove metaboxes from Shop page
add_filter( 'us_config_meta-boxes', 'us_remove_meta_for_shop_page' );
function us_remove_meta_for_shop_page( $config ) {
	$post_id = isset( $_GET['post'] ) ? intval( $_GET['post'] ) : NULL;
	if ( $post_id !== NULL AND $post_id == get_option( 'woocommerce_shop_page_id' ) ) {
		foreach ( $config as $metabox_key => $metabox ) {
			if ( $metabox['id'] == 'us_portfolio_settings' ) {
				unset( $config[ $metabox_key ] );
			}
			if ( $metabox['id'] == 'us_page_settings' ) {
				$keys = array(
					'us_header_id', 'us_header_sticky_pos', 'us_titlebar_id',
					'us_sidebar_id', 'us_sidebar_pos', 'us_content_id', 'us_footer_id'
				);
				foreach ( $keys as $key ) {
					unset( $config[ $metabox_key ]['fields'][ $key ] );
				}
			}
		}
	}

	return $config;
}

add_filter( 'us_stop_grid_execution', 'us_stop_grid_execution_wc_product_summary' );
function us_stop_grid_execution_wc_product_summary() {
	return doing_action( 'woocommerce_single_product_summary' );
}

if ( ! function_exists( 'us_wc_add_to_cart_message_html' ) ) {
	add_action( 'wc_add_to_cart_message_html', 'us_wc_add_to_cart_message_html', 10, 1 );
	/**
	 * Customizing add-to-cart messages for woocommerce notice
	 *
	 * @param string $message The HTML message
	 * @return string
	 */
	function us_wc_add_to_cart_message_html( $message ) {
		return preg_replace_callback( '/(<\s*a[^>]*>.*<\s*\/\s*a>)(.*)/', function( $matches ) {
			return $matches[2] . ' ' . preg_replace( '/="button\s/' , '="', $matches[1] );
		}, $message );
	}
}

if ( ! function_exists( 'us_posts_clauses' ) ) {
	/**
	 * @param array $args The parameters for query request
	 * @return array
	 */
	function us_posts_clauses( $args, $wp_query ) {
		global $wpdb;
		$query_vars = $wp_query->query_vars;
		if (
			$query_vars[ 'post_type' ] === 'product'
			AND ! empty( $query_vars[ 'orderby' ] )
			AND in_array( $query_vars[ 'orderby' ], array( 'price', 'popularity', 'rating' ) )
		) {
			// Additional sorting for records that do not contain data in the adjacent table will allow you to organize the output.
			$args['orderby'] = rtrim( $args['orderby'] ) . ', ' . $wpdb->posts . '.ID ' . ( strrpos( $args['orderby'] , 'ASC' ) !== FALSE
				? 'ASC'
				: 'DESC'
			);
		}

		return $args;
	}
	add_action( 'posts_clauses', 'us_posts_clauses', 100, 2 );
}

if ( ! function_exists( 'us_woocommerce_enable_setup_wizard' ) ) {
	/**
	 * Disable redirects wc-setup for developers after resetting the database
	 *
	 * @param bool $true
	 * @return bool
	 */
	function us_woocommerce_enable_setup_wizard( $true ) {
		return defined( 'US_DEV' ) ? FALSE : $true;
	}
	add_filter( 'woocommerce_enable_setup_wizard', 'us_woocommerce_enable_setup_wizard', 10, 1 );
}

if ( ! function_exists( 'us_wc_pre_get_posts' ) ) {
	/**
	 * Disable the output of products that are out of stock
	 *
	 * @param WP_Query $query
	 * @return void
	 */
	function us_wc_pre_get_posts( $query ) {
		if (
			is_admin()
			OR ! class_exists( 'woocommerce' )
			OR get_option( 'woocommerce_hide_out_of_stock_items', 'no' ) !== 'yes'
			// If the search page is not for products then exit
			OR (
				is_search()
				AND $query->get( 'post_type' ) !== 'product'
			)
		) {
			return;
		}

		$query_vars = &$query->query_vars;
		$has_product_post_type = FALSE;

		// Check if the query has post type(s) set
		// then check if it matches post types that support out of stock taxonomy
		if ( ! empty( $query_vars['post_type'] ) ) {
			$product_post_types = apply_filters( 'woocommerce_taxonomy_objects_product_visibility', array( 'product', 'product_variation' ) );
			foreach ( $product_post_types as $product_post_type ) {
				if ( $query_vars['post_type'] === $product_post_type
					OR (
						is_array( $query_vars['post_type'] )
						AND in_array( $product_post_type, $query_vars['post_type'] )
					)
				) {
					$has_product_post_type = TRUE;
					break;
				}
			}
			// If the query post type(s) do not match those supporting out of stock, abort following execution
			if ( ! $has_product_post_type ) {
				return;
			}
		}

		$include_outofstock_meta = FALSE;

		// We will add meta query with hide out of stock condition in following cases:
		if (
			// Product Archive Pages ...
			( isset( $query_vars['wc_query'] ) AND $query_vars['wc_query'] === 'product_query' )
			// OR query for products but not a single product page ...
			OR ( ! isset( $query_vars['product'] ) AND $has_product_post_type )
		) {
			$include_outofstock_meta = TRUE;

			// OR query has product categories
		} else if ( ! empty( $query_vars['tax_query'] ) ) {
			foreach ( $query_vars['tax_query'] as $tax ) {
				if (
					! empty( $tax['taxonomy'] )
					AND (
						$tax['taxonomy'] === 'product_cat'
						OR taxonomy_is_product_attribute( $tax['taxonomy'] )
					)
				) {
					$include_outofstock_meta = TRUE;
					break;
				}
			}
		}

		// Add meta_query for outofstock
		if ( $include_outofstock_meta ) {
			$query_vars['meta_query'][] = array(
				'key' => '_stock_status',
				'value' => 'outofstock',
				'compare' => '!=',
			);
		}
	}
	add_action( 'pre_get_posts', 'us_wc_pre_get_posts', 10, 1 );
}

if ( ! function_exists( 'us_wc_get_min_max_price' ) ) {
	/**
	 * Get min max prices of products, taking into account tax etc.
	 *
	 * @param array $query_vars
	 * @return array
	 */
	function us_wc_get_min_max_price( $query_vars = array() ) {
		if ( ! wp_doing_ajax() AND defined( 'WP_ADMIN' ) ) {
			return array();
		}

		global $wpdb;

		$tax_query = us_arr_path( $query_vars, 'tax_query', array() );
		$meta_query = us_arr_path( $query_vars, 'meta_query', array() );

		$meta_query = new WP_Meta_Query( $meta_query );
		$tax_query = new WP_Tax_Query( $tax_query );

		$meta_query_sql = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
		$tax_query_sql = $tax_query->get_sql( $wpdb->posts, 'ID' );
		// TODO: Add search criteria to $search_query_sql
		$search_query_sql = '';

		// Get post_types
		$post_types = array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) );

		// Preparing a SQL query to get the min and max price
		$query_sql = "
			SELECT
				MIN( min_price ) AS min_price, MAX( max_price ) AS max_price
			FROM {$wpdb->wc_product_meta_lookup}
			WHERE product_id IN (
				SELECT
					ID
				FROM {$wpdb->posts} " . $tax_query_sql['join'] . $meta_query_sql['join'] . "
				WHERE
					{$wpdb->posts}.post_type IN ('" . implode( "','", $post_types ) . "')
					AND {$wpdb->posts}.post_status = 'publish'
					" . $tax_query_sql['where'] . $meta_query_sql['where'] . $search_query_sql . '
			)';
		$query_sql = apply_filters( 'woocommerce_price_filter_sql', $query_sql, $meta_query_sql, $tax_query_sql );

		// Get the min and max price
		$prices = $wpdb->get_row( $query_sql );
		$min_price = floor( $prices->min_price );
		$max_price = ceil( $prices->max_price );

		// Check to see if we should add taxes to the prices if store are excl tax but display incl.
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		if ( wc_tax_enabled() && ! wc_prices_include_tax() && 'incl' === $tax_display_mode ) {
			$tax_rates = WC_Tax::get_rates( apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ) );
			if ( $tax_rates ) {
				$min_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $min_price, $tax_rates ) );
				$max_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max_price, $tax_rates ) );
			}
		}

		// Round values to nearest 10 by default.
		$step = max( apply_filters( 'woocommerce_price_filter_widget_step', 10 ), 1 );
		$min_price = apply_filters( 'woocommerce_price_filter_widget_min_amount', floor( $min_price / $step ) * $step );
		$max_price = apply_filters( 'woocommerce_price_filter_widget_max_amount', ceil( $max_price / $step ) * $step );

		return array(
			'min' => $min_price,
			'max' => $max_price,
		);
	}
}

if ( ! function_exists( 'us_filter_woocommerce_get_catalog_ordering_args' ) ) {
	/**
	 * Injection of sorting parameters by custom grid_order shortcode settings
	 *
	 * @param array $args The arguments
	 * @return array
	 */
	function us_filter_woocommerce_get_catalog_ordering_args( $args ) {
		if ( wp_doing_ajax() ) {
			$template_vars = us_maybe_get_post_json( 'template_vars' );
			// The exception for search page
			if ( isset( $template_vars['query_args']['s'] ) ) {
				return $args;
			}
			$us_orderby = us_arr_path( $template_vars, 'grid_orderby' );
		} else {
			global $us_get_orderby;
			$us_orderby = us_arr_path( $_GET, us_get_grid_url_prefix( 'order' ), $us_get_orderby );
		}
		if ( $us_orderby ) {
			$params = (array) us_grid_orderby_str_to_params( $us_orderby );
			$params['post_type'] = 'product';
			us_grid_set_orderby_to_query_args( $args, $params );
		}

		return $args;
	}
	add_filter( 'woocommerce_get_catalog_ordering_args', 'us_filter_woocommerce_get_catalog_ordering_args', 1, 1 );
}
