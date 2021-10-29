<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Meta Boxes
 *
 * @filter us_config_meta-boxes
 */


$posts_titles = (array) us_get_all_posts_titles_for( array( 'us_page_block', 'us_content_template', 'us_header' ) );

// Get Page Blocks
$us_page_blocks_list = us_arr_path( $posts_titles, 'us_page_block', array() );

// Get Content templates
$us_content_templates_list = us_arr_path( $posts_titles, 'us_content_template', array() );

// Use Page Blocks as Sidebars, if set in Theme Options
if ( us_get_option( 'enable_page_blocks_for_sidebars', 0 ) ) {
	$sidebars_list = $us_page_blocks_list;
	$sidebar_hints_for = 'us_page_block';

	// else use regular sidebars
} else {
	$sidebars_list = us_get_sidebars();
	$sidebar_hints_for = NULL;
}

$metabox_config = array();

// Get responsive states
$responsive_states = array();
foreach ( us_get_responsive_states() as $state => $data ) {
	$responsive_states[ $state ] = $data['title'];
}

// Page Layout
$metabox_config[] = array(
	'id' => 'us_page_settings',
	'title' => __( 'Page Layout', 'us' ),
	'post_types' => array_keys( us_get_public_post_types() ),
	'context' => 'side',
	'usb_context' => TRUE,
	'priority' => 'low',
	'fields' => array(

		// Header
		'us_header_id' => array(
			'title' => _x( 'Header', 'site top area', 'us' ),
			'type' => 'select',
			'hints_for' => 'us_header',
			'options' => us_array_merge(
				array(
					'__defaults__' => '&ndash; ' . __( 'As in Theme Options', 'us' ) . ' &ndash;',
					'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
				), us_arr_path( $posts_titles, 'us_header', array() )
			),
			'std' => '__defaults__',
			'usb_preview' => TRUE,
		),
		'us_header_sticky_override' => array(
			'title' => __( 'Sticky Header', 'us' ),
			'type' => 'switch',
			'switch_text' => __( 'Override this setting', 'us' ),
			'std' => 0,
			'classes' => 'for_above',
			'show_if' => array( 'us_header_id', '!=', array( '__defaults__', '' ) ),
			'usb_preview' => TRUE
		),
		'us_header_sticky' => array(
			'type' => 'checkboxes',
			'options' => $responsive_states,
			'std' => '',
			'classes' => 'for_above vertical',
			'show_if' => array(
				array( 'us_header_id', '!=', array( '__defaults__', '' ) ),
				'and',
				array( 'us_header_sticky_override', '=', '1' ),
			),
			'usb_preview' => TRUE
		),
		'us_header_transparent_override' => array(
			'title' => __( 'Transparent Header', 'us' ),
			'type' => 'switch',
			'switch_text' => __( 'Override this setting', 'us' ),
			'std' => 0,
			'classes' => 'for_above',
			'show_if' => array( 'us_header_id', '!=', array( '__defaults__', '' ) ),
			'usb_preview' => TRUE
		),
		'us_header_transparent' => array(
			'type' => 'checkboxes',
			'options' => $responsive_states,
			'std' => '',
			'classes' => 'for_above vertical',
			'show_if' => array(
				array( 'us_header_id', '!=', array( '__defaults__', '' ) ),
				'and',
				array( 'us_header_transparent_override', '=', '1' ),
			),
			'usb_preview' => TRUE
		),
		'us_header_shadow' => array(
			'title' => __( 'Header Shadow', 'us' ),
			'type' => 'switch',
			'switch_text' => __( 'Remove header shadow', 'us' ),
			'std' => 0,
			'classes' => 'for_above',
			'show_if' => array( 'us_header_id', '!=', array( '__defaults__', '' ) ),
			'usb_preview' => TRUE
		),
		'us_header_sticky_pos' => array(
			'title' => __( 'Sticky Header Initial Position', 'us' ),
			'type' => 'select',
			'options' => array(
				'' => __( 'At the Top of this page', 'us' ),
				'bottom' => __( 'At the Bottom of the first content row', 'us' ),
				'above' => __( 'Above the first content row', 'us' ),
				'below' => __( 'Below the first content row', 'us' ),
			),
			'std' => '',
			'classes' => 'for_above',
			'show_if' => array( 'us_header_id', '!=', array( '__defaults__', '' ) ),
			'usb_preview' => TRUE
		),

		// Titlebar
		'us_titlebar_id' => array(
			'title' => __( 'Titlebar', 'us' ),
			'type' => 'select',
			'hints_for' => 'us_page_block',
			'options' => us_array_merge(
				array(
					'__defaults__' => '&ndash; ' . __( 'As in Theme Options', 'us' ) . ' &ndash;',
					'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
				), $us_page_blocks_list
			),
			'std' => '__defaults__',
			'place_if' => us_get_option( 'enable_sidebar_titlebar', 0 ),
			'usb_preview' => TRUE,
		),

		// Content template
		'us_content_id' => array(
			'title' => __( 'Content template', 'us' ),
			'type' => 'select',
			'hints_for' => 'us_content_template',
			'options' => us_array_merge(
				array(
					'__defaults__' => '&ndash; ' . __( 'As in Theme Options', 'us' ) . ' &ndash;',
					'' => '&ndash; ' . __( 'Show content as is', 'us' ) . ' &ndash;',
				), $us_content_templates_list
			),
			'std' => '__defaults__',
			'usb_preview' => TRUE,
		),

		// Sidebar
		'us_sidebar_id' => array(
			'title' => __( 'Sidebar', 'us' ),
			'type' => 'select',
			'options' => us_array_merge(
				array(
					'__defaults__' => '&ndash; ' . __( 'As in Theme Options', 'us' ) . ' &ndash;',
					'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
				), $sidebars_list
			),
			'hints_for' => $sidebar_hints_for,
			'std' => '__defaults__',
			'place_if' => us_get_option( 'enable_sidebar_titlebar', 0 ),
			'usb_preview' => TRUE,
		),

		// Sidebar Position
		'us_sidebar_pos' => array(
			'type' => 'radio',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'right',
			'classes' => 'for_above',
			'show_if' => array( 'us_sidebar_id', '!=', array( '', '__defaults__' ) ),
			'place_if' => us_get_option( 'enable_sidebar_titlebar', 0 ),
			'usb_preview' => TRUE
		),
		// Footer
		'us_footer_id' => array(
			'title' => __( 'Footer', 'us' ),
			'type' => 'select',
			'hints_for' => 'us_page_block',
			'options' => us_array_merge(
				array(
					'__defaults__' => '&ndash; ' . __( 'As in Theme Options', 'us' ) . ' &ndash;',
					'' => '&ndash; ' . __( 'Do not display', 'us' ) . ' &ndash;',
				), $us_page_blocks_list
			),
			'std' => '__defaults__',
			'usb_preview' => TRUE,
		),
	),
);

// Custom appearance in Grid
$metabox_config[] = array(
	'id' => 'us_portfolio_settings',
	'title' => __( 'Custom appearance in Grid', 'us' ),
	'post_types' => array_keys( us_get_public_post_types() ),
	'context' => 'normal',
	'usb_context' => TRUE,
	'priority' => 'default',
	'fields' => array(
		'us_tile_additional_image' => array(
			'title' => us_translate( 'Images' ),
			'title_pos' => 'side',
			'type' => 'upload',
			'is_multiple' => TRUE,
			'extension' => 'png,jpg,jpeg,gif,svg',
			'usb_preview' => TRUE,
		),
		'us_tile_link' => array(
			'title' => __( 'Custom Link', 'us' ),
			'title_pos' => 'side',
			'type' => 'link',
			'placeholder' => us_translate( 'Enter the URL' ),
			'std' => '',
		),
		'us_tile_icon' => array(
			'title' => __( 'Icon', 'us' ),
			'title_pos' => 'side',
			'type' => 'icon',
			'std' => '',
		),
		'us_tile_size' => array(
			'title' => __( 'Custom Size', 'us' ),
			'title_pos' => 'side',
			'type' => 'radio',
			'options' => array(
				'1x1' => us_translate( 'None' ),
				'2x1' => '2x1',
				'1x2' => '1x2',
				'2x2' => '2x2',
			),
			'std' => '1x1',
		),
		'us_tile_bg_color' => array(
			'title' => __( 'Background Color', 'us' ),
			'title_pos' => 'side',
			'type' => 'color',
			'clear_pos' => 'right',
			'std' => '',
		),
		'us_tile_text_color' => array(
			'title' => __( 'Text Color', 'us' ),
			'title_pos' => 'side',
			'type' => 'color',
			'clear_pos' => 'right',
			'with_gradient' => FALSE,
			'std' => '',
		),
	)
);

// Testimonials settings
$metabox_config[] = array(
	'id' => 'us_testimonials_settings',
	'title' => __( 'More Options', 'us' ),
	'post_types' => array( 'us_testimonial' ),
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
		'us_testimonial_author' => array(
			'title' => __( 'Author Name', 'us' ),
			'title_pos' => 'side',
			'type' => 'text',
			'std' => 'John Doe',
		),
		'us_testimonial_role' => array(
			'title' => __( 'Author Role', 'us' ),
			'title_pos' => 'side',
			'type' => 'text',
			'std' => '',
		),
		'us_testimonial_company' => array(
			'title' => __( 'Author Company', 'us' ),
			'title_pos' => 'side',
			'type' => 'text',
			'std' => '',
		),
		'us_testimonial_link' => array(
			'title' => __( 'Author Link', 'us' ),
			'title_pos' => 'side',
			'type' => 'link',
			'placeholder' => us_translate( 'Enter the URL' ),
			'std' => '',
		),
		'us_testimonial_rating' => array(
			'title' => __( 'Rating', 'us' ),
			'title_pos' => 'side',
			'type' => 'radio',
			'options' => array(
				'none' => us_translate( 'None' ),
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
			),
			'std' => 'none',
		),
	)
);

// Page Block and Content template info
$metabox_config[] = array(
	'id' => 'us_post_info',
	'title' => __( 'Used in', 'us' ),
	'post_types' => array( 'us_page_block', 'us_content_template' ),
	'context' => 'side',
	'priority' => 'default',
	'fields' => array(
		'used_in_locations' => array(
			'description' => '',
			'type' => 'message',
		),
	)
);

// SEO meta tags
if ( us_get_option( 'og_enabled', 1 ) ) {
	$metabox_config[] = array(
		'id' => 'us_seo_settings',
		'title' => __( 'SEO meta tags', 'us' ),
		'post_types' => array_keys( us_get_public_post_types() ),
		'context' => 'normal',
		'usb_context' => TRUE,
		'priority' => 'default',
		'fields' => us_config( 'seo-meta-fields', array() ),
	);
}

return $metabox_config;
