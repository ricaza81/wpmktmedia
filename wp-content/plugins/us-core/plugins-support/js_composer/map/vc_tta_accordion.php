<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_tta_accordion
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
	vc_remove_param( 'vc_tta_accordion', 'title' );
	vc_remove_param( 'vc_tta_accordion', 'style' );
	vc_remove_param( 'vc_tta_accordion', 'shape' );
	vc_remove_param( 'vc_tta_accordion', 'color' );
	vc_remove_param( 'vc_tta_accordion', 'no_fill' );
	vc_remove_param( 'vc_tta_accordion', 'spacing' );
	vc_remove_param( 'vc_tta_accordion', 'gap' );
	vc_remove_param( 'vc_tta_accordion', 'autoplay' );
	vc_remove_param( 'vc_tta_accordion', 'collapsible_all' );
	vc_remove_param( 'vc_tta_accordion', 'active_section' );
	vc_remove_param( 'vc_tta_accordion', 'c_align' );
	vc_remove_param( 'vc_tta_accordion', 'c_icon' );
	vc_remove_param( 'vc_tta_accordion', 'c_position' );
	vc_remove_param( 'vc_tta_accordion', 'css_animation' );

	vc_update_shortcode_param(
		'vc_tta_accordion', array(
			'param_name' => 'el_class',
			'description' => '',
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 10,
			'group' => __( 'Design', 'us' ),
		)
	);
	vc_update_shortcode_param(
		'vc_tta_accordion', array(
			'param_name' => 'el_id',
			'description' => '',
			'edit_field_class' => 'vc_col-sm-6',
			'group' => __( 'Design', 'us' ),
		)
	);

	vc_update_shortcode_param(
		'vc_tta_accordion', array(
			'param_name' => 'css',
			'type' => 'us_design_options',
			'heading' => '',
			'params' => us_config( 'elements_design_options.css.params', array() ),
			'group' => __( 'Design', 'us' ),
		)
	);

	vc_add_params(
		'vc_tta_accordion', array(
		array(
			'param_name' => 'toggle',
			'heading' => '',
			'type' => 'checkbox',
			'value' => array( __( 'Allow several sections to be opened at the same time', 'us' ) => TRUE ),
			( ( $config['atts']['toggle'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['toggle'],
			'weight' => 60,
		),
		array(
			'param_name' => 'scrolling',
			'heading' => '',
			'type' => 'checkbox',
			'value' => array( __( 'Scroll to the beginning of the section when opening', 'us' ) => TRUE ),
			( ( $config['atts']['scrolling'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['scrolling'],
			'dependency' => array( 'element' => 'toggle', 'is_empty' => TRUE ),
			'weight' => 55,
		),
		array(
			'param_name' => 'remove_indents',
			'heading' => '',
			'type' => 'checkbox',
			'value' => array( __( 'Remove left and right indents', 'us' ) => TRUE ),
			( ( $config['atts']['remove_indents'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['remove_indents'],
			'weight' => 52,
		),
		array(
			'param_name' => 'faq_markup',
			'heading' => '',
			'type' => 'checkbox',
			'value' => array( __( 'Add FAQ structured data markup', 'us' ) => TRUE ),
			( ( $config['atts']['faq_markup'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['faq_markup'],
			'weight' => 51,
		),
		array(
			'param_name' => 'c_align',
			'heading' => __( 'Title Alignment', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				us_translate( 'Left' ) => 'left',
				us_translate( 'Right' ) => 'right',
				us_translate( 'Center' ) => 'center',
			),
			'std' => $config['atts']['c_align'],
			'weight' => 50,
		),
		array(
			'param_name' => 'title_tag',
			'heading' => __( 'Title HTML tag', 'us' ),
			'type' => 'dropdown',
			'value' => $misc['html_tag_values'],
			'std' => $config['atts']['title_tag'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 30,
		),
		array(
			'param_name' => 'title_size',
			'heading' => __( 'Title Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'textfield',
			'std' => $config['atts']['title_size'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 40,
		),
		array(
			'param_name' => 'c_icon',
			'heading' => __( 'Icon', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				us_translate( 'None' ) => '',
				__( 'Chevron', 'us' ) => 'chevron',
				__( 'Plus', 'us' ) => 'plus',
				__( 'Triangle', 'us' ) => 'triangle',
			),
			'std' => $config['atts']['c_icon'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 20,
		),
		array(
			'param_name' => 'c_position',
			'heading' => __( 'Icon Position', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				us_translate( 'Left' ) => 'left',
				us_translate( 'Right' ) => 'right',
			),
			'std' => $config['atts']['c_position'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 10,
		),
	)
	);
}

// Setting proper shortcode order in VC shortcodes listing
vc_map_update( 'vc_tta_accordion', array( 'weight' => 310 ) );
