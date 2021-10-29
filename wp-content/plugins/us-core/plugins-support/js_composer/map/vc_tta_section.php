<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_tta_section
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
if ( version_compare( WPB_VC_VERSION, '4.6', '<' ) ) {
	// Oops: the modified shorcode doesn't exist in current VC version. Doing nothing.
	return;
}

if ( ! vc_is_page_editable() ) {
	vc_remove_param( 'vc_tta_section', 'add_icon' );
	vc_remove_param( 'vc_tta_section', 'i_type' );
	vc_remove_param( 'vc_tta_section', 'i_icon_fontawesome' );
	vc_remove_param( 'vc_tta_section', 'i_icon_openiconic' );
	vc_remove_param( 'vc_tta_section', 'i_icon_typicons' );
	vc_remove_param( 'vc_tta_section', 'i_icon_entypo' );
	vc_remove_param( 'vc_tta_section', 'i_icon_linecons' );
	vc_remove_param( 'vc_tta_section', 'i_icon_monosocial' );
	vc_remove_param( 'vc_tta_section', 'i_icon_material' );
	vc_remove_param( 'vc_tta_section', 'i_position' );
	vc_update_shortcode_param(
		'vc_tta_section', array(
			'param_name' => 'title',
			'description' => '',
			'weight' => 90,
		)
	);
	vc_update_shortcode_param(
		'vc_tta_section', array(
			'param_name' => 'tab_id',
			'std' => $config['atts']['tab_id'],
			'weight' => 10,
		)
	);
	vc_add_params(
		'vc_tta_section', array(
			array(
				'param_name' => 'tab_link',
				'heading' => us_translate( 'Link' ),
				'type' => 'vc_link',
				'std' => $config['atts']['tab_link'],
				'weight' => 80,
			),
			array(
				'param_name' => 'active',
				'type' => 'checkbox',
				'value' => array( __( 'Show this section open', 'us' ) => TRUE ),
				( ( $config['atts']['active'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['active'],
				'weight' => 70,
			),
			array(
				'param_name' => 'indents',
				'type' => 'checkbox',
				'value' => array( __( 'Stretch this section content to the full available area', 'us' ) => 'none' ),
				( ( $config['atts']['indents'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['indents'],
				'weight' => 60,
			),
			array(
				'param_name' => 'icon',
				'heading' => __( 'Icon', 'us' ),
				'type' => 'us_icon',
				'std' => $config['atts']['icon'],
				'weight' => 50,
			),
			array(
				'param_name' => 'i_position',
				'heading' => __( 'Icon Position', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Before title', 'us' ) => 'left',
					__( 'After title', 'us' ) => 'right',
				),
				'std' => $config['atts']['i_position'],
				'weight' => 40,
			),
			array(
				'param_name' => 'bg_color',
				'heading' => __( 'Background Color', 'us' ),
				'type' => 'us_color',
				'clear_pos' => 'right',
				'value' => '',
				'std' => $config['atts']['bg_color'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 30,
			),
			array(
				'param_name' => 'text_color',
				'heading' => __( 'Text Color', 'us' ),
				'type' => 'us_color',
				'with_gradient' => FALSE,
				'clear_pos' => 'right',
				'value' => '',
				'std' => $config['atts']['text_color'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 20,
			),
		)
	);
	vc_update_shortcode_param(
		'vc_tta_section', array(
			'param_name' => 'css',
			'type' => 'us_design_options',
			'heading' => '',
			'params' => us_config( 'elements_design_options.css.params', array() ),
			'group' => __( 'Design', 'us' ),
		)
	);
}
