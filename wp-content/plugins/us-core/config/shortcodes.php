<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcodes
 *
 * @filter us_config_shortcodes
 */

return array(

	// Main theme elements. The order affects on position in the "Add Element" list in USBuilder
	'theme_elements' => array(

		// Containers
		'vc_row',
		'vc_row_inner',
		'vc_column',
		'vc_column_inner',
		'hwrapper',
		'vwrapper',
		'vc_tta_accordion',
		'vc_tta_tabs',
		'vc_tta_tour',
		'vc_tta_section',

		// Basic
		'vc_column_text',
		'text',
		'btn',
		'iconbox',
		'image',
		'separator',

		// Grid
		'grid',
		'grid_filter',
		'grid_order',
		'carousel',

		// Interactive
		'counter',
		'flipbox',
		'image_slider',
		'ibanner',
		'itext',
		'message',
		'popup',
		'progbar',
		'scroller',

		// Other
		'page_block',
		'cform',
		'contacts',
		'cta',
		'dropdown',
		'gmaps',
		'login',
		'person',
		'pricing',
		'additional_menu',
		'search',
		'sharing',
		'socials',
		'vc_video',
		'html',

		// Post Elements
		'post_content',
		'post_image',
		'post_title',
		'post_custom_field',
		'post_date',
		'post_taxonomy',
		'post_author',
		'post_comments',
		'post_navigation',
		'post_views',
		'breadcrumbs',
		'add_to_cart',
		'product_field',
		'product_gallery',
		'product_ordering',
	),

	// Shortcodes, that use template file of other shortcodes
	'alias' => array(
		'vc_column_inner' => 'vc_column', // for example, vc_column_inner uses vc_column template file
		'vc_tta_accordion' => 'vc_tta_tabs',
		'vc_tta_tour' => 'vc_tta_tabs',
		'us_carousel' => 'us_grid',
	),

	// VC shortcodes, which are disabled by default
	'disabled' => array(
		'vc_btn',
		'vc_cta',
		'vc_gallery',
		'vc_single_image',
		'vc_message',
		'vc_gmaps',
		'vc_icon',
		'vc_facebook',
		'vc_tweetmeme',
		'vc_googleplus',
		'vc_pinterest',
		'vc_flickr',
		'vc_tta_pageable',
		'vc_toggle',
		'vc_tour',
		'vc_posts_slider',
		'vc_progress_bar',
		'vc_pie',
		'vc_basic_grid',
		'vc_media_grid',
		'vc_images_carousel',
		'vc_masonry_grid',
		'vc_masonry_media_grid',
		'vc_section',
		'vc_button2',
		'vc_separator',
		'vc_empty_space',
		'vc_text_separator',
		'vc_zigzag',
		'vc_hoverbox',
		'vc_tabs',
		'vc_accordion',
		'vc_tab',
		'vc_accordion_tab',
		'vc_gutenberg',
		'vc_acf',

		// WooCommerce
		'product',
		'products',
		'product_category',
		'product_categories',
		'top_rated_products',
		'best_selling_products',
		'recent_products',
		'featured_products',
		'sale_products',
	),

	// WordPress gallery shortcode, which is modified via theme custom template
	'modified' => array(
		'gallery' => array(
			'atts' => array(
				'ids' => '',
				'columns' => 3,
				'orderby' => FALSE,
				'indents' => FALSE,
				'meta' => FALSE,
				'link' => FALSE,
				'masonry' => FALSE,
				'size' => 'thumbnail',
			),
		),
	),

		// VC shortcodes, which don't have theme configs, but needed theme Design options
	'added_design_options' => array(
		'vc_custom_heading',
		'vc_line_chart',
		'vc_raw_html',
		'vc_round_chart',
	),
);
