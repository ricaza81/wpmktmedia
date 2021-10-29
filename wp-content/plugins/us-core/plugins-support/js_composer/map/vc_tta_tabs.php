<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_tta_tabs
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */

$misc = us_config( 'elements_misc' );

if ( version_compare( WPB_VC_VERSION, '4.6', '<' ) ) {
	// Oops: the modified shorcode doesn't exist in current VC version. Doing nothing.
	return;
}

if ( ! vc_is_page_editable() ) {
	vc_remove_param( 'vc_tta_tabs', 'title' );
	vc_remove_param( 'vc_tta_tabs', 'style' );
	vc_remove_param( 'vc_tta_tabs', 'shape' );
	vc_remove_param( 'vc_tta_tabs', 'color' );
	vc_remove_param( 'vc_tta_tabs', 'no_fill_content_area' );
	vc_remove_param( 'vc_tta_tabs', 'spacing' );
	vc_remove_param( 'vc_tta_tabs', 'gap' );
	vc_remove_param( 'vc_tta_tabs', 'tab_position' );
	vc_remove_param( 'vc_tta_tabs', 'alignment' );
	vc_remove_param( 'vc_tta_tabs', 'autoplay' );
	vc_remove_param( 'vc_tta_tabs', 'active_section' );
	vc_remove_param( 'vc_tta_tabs', 'pagination_style' );
	vc_remove_param( 'vc_tta_tabs', 'pagination_color' );
	vc_remove_param( 'vc_tta_tabs', 'css_animation' );

	vc_update_shortcode_param(
		'vc_tta_tabs', array(
			'param_name' => 'el_class',
			'description' => '',
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 10,
			'group' => __( 'Design', 'us' ),
		)
	);
	vc_update_shortcode_param(
		'vc_tta_tabs', array(
			'param_name' => 'el_id',
			'description' => '',
			'edit_field_class' => 'vc_col-sm-6',
			'group' => __( 'Design', 'us' ),
		)
	);

	vc_update_shortcode_param(
		'vc_tta_tabs', array(
			'param_name' => 'css',
			'type' => 'us_design_options',
			'heading' => '',
			'params' => us_config( 'elements_design_options.css.params', array() ),
			'group' => __( 'Design', 'us' ),
		)
	);

	vc_add_params(
		'vc_tta_tabs', array(
			array(
				'param_name' => 'switch_sections',
				'heading' => __( 'Switch sections', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'On click', 'us' ) => 'click',
					__( 'On hover', 'us' ) => 'hover',
				),
				'std' => $config['atts']['switch_sections'],
				'weight' => 202,
			),
			array(
				'param_name' => 'title_tag',
				'heading' => __( 'Sections Title HTML tag', 'us' ),
				'type' => 'dropdown',
				'value' => $misc['html_tag_values'],
				'std' => $config['atts']['title_tag'],
				'weight' => 200,
			),
			array(
				'param_name' => 'layout',
				'heading' => us_translate( 'Style' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Simple', 'us' ) => 'default',
					__( 'Modern', 'us' ) => 'modern',
					__( 'Trendy', 'us' ) => 'trendy',
					__( 'Timeline', 'us' ) => 'timeline',
					__( 'Timeline', 'us' ) . ' 2' => 'timeline2',
				),
				'std' => $config['atts']['layout'],
				'group' => us_translate( 'Tabs', 'js_composer' ),
				'weight' => 180,
			),
			array(
				'param_name' => 'stretch',
				'type' => 'checkbox',
				'value' => array( __( 'Stretch tabs to the full available width', 'us' ) => TRUE ),
				( ( $config['atts']['stretch'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['stretch'],
				'group' => us_translate( 'Tabs', 'js_composer' ),
				'weight' => 170,
			),
			array(
				'param_name' => 'title_font',
				'heading' => __( 'Font', 'us' ),
				'type' => 'dropdown',
				'value' => array_flip( us_get_fonts( 'without_groups' ) ),
				'std' => $config['atts']['title_font'],
				'group' => us_translate( 'Tabs', 'js_composer' ),
				'weight' => 160,
			),
			array(
				'param_name' => 'title_weight',
				'heading' => __( 'Font Weight', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Default' ) => '',
					'100 ' . __( 'thin', 'us' ) => '100',
					'200 ' . __( 'extra-light', 'us' ) => '200',
					'300 ' . __( 'light', 'us' ) => '300',
					'400 ' . __( 'normal', 'us' ) => '400',
					'500 ' . __( 'medium', 'us' ) => '500',
					'600 ' . __( 'semi-bold', 'us' ) => '600',
					'700 ' . __( 'bold', 'us' ) => '700',
					'800 ' . __( 'extra-bold', 'us' ) => '800',
					'900 ' . __( 'ultra-bold', 'us' ) => '900',
				),
				'std' => $config['atts']['title_weight'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => us_translate( 'Tabs', 'js_composer' ),
				'weight' => 142,
			),
			array(
				'param_name' => 'title_transform',
				'heading' => __( 'Text Transform', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Default' ) => '',
					us_translate( 'None' ) => 'none',
					'UPPERCASE' => 'uppercase',
					'lowercase' => 'lowercase',
					'Capitalize' => 'capitalize',
				),
				'std' => $config['atts']['title_transform'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => us_translate( 'Tabs', 'js_composer' ),
				'weight' => 142,
			),
			array(
				'param_name' => 'title_size',
				'heading' => __( 'Font Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'textfield',
				'std' => $config['atts']['title_size'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => us_translate( 'Tabs', 'js_composer' ),
				'weight' => 140,
			),
			array(
				'param_name' => 'title_lineheight',
				'heading' => __( 'Line height', 'us' ),
				'description' => $misc['desc_line_height'],
				'type' => 'textfield',
				'std' => $config['atts']['title_lineheight'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => us_translate( 'Tabs', 'js_composer' ),
				'weight' => 130,
			),
		)
	);
}

// Setting proper shortcode order in VC shortcodes listing
vc_map_update( 'vc_tta_tabs', array( 'weight' => 320 ) );
