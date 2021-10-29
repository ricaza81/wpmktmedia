<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_column_text
 *
 * @var $shortcode string Current shortcode name
 * @var $config    array Shortcode's config
 */

vc_remove_param( 'vc_column_text', 'css_animation' );

vc_update_shortcode_param(
	'vc_column_text', array(
		'param_name' => 'content',
		'heading' => '',
		'weight' => 20,
	)
);
vc_update_shortcode_param(
	'vc_column_text', array(
		'param_name' => 'el_class',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 10,
		'group' => __( 'Design', 'us' ),
	)
);
vc_update_shortcode_param(
	'vc_column_text', array(
		'param_name' => 'el_id',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'group' => __( 'Design', 'us' ),
	)
);

// Add "More toggle" settings
vc_add_params(
	'vc_column_text', array(
		array(
			'param_name' => 'show_more_toggle',
			'type' => 'checkbox',
			'value' => array( __( 'Hide part of a content with the "Show More" link', 'us' ) => TRUE ),
			( ( $config['atts']['show_more_toggle'] !== FALSE ) ?
				'std' : '_std' ) => $config['atts']['show_more_toggle'],
			'weight' => 15,
			'group' => __( 'More Options', 'us' ),
		),
		array(
			'param_name' => 'show_more_toggle_height',
			'heading' => __( 'Height of visible content', 'us' ),
			'description' => __( 'In pixels:', 'us' ) . ' <span class="usof-example">100px</span>, <span class="usof-example">150px</span>, <span class="usof-example">200px</span>',
			'type' => 'textfield',
			'std' => $config['atts']['show_more_toggle_height'],
			'dependency' => array( 'element' => 'show_more_toggle', 'value' => '1' ),
			'group' => __( 'More Options', 'us' ),
		),
		array(
			'param_name' => 'show_more_toggle_text_more',
			'heading' => __( 'Text when content is hidden', 'us' ),
			'type' => 'textfield',
			'std' => $config['atts']['show_more_toggle_text_more'],
			'dependency' => array( 'element' => 'show_more_toggle', 'value' => '1' ),
			'group' => __( 'More Options', 'us' ),
		),
		array(
			'param_name' => 'show_more_toggle_text_less',
			'heading' => __( 'Text when content is shown', 'us' ),
			'description' => __( 'Leave blank to prevent content from being hidden again.', 'us' ),
			'type' => 'textfield',
			'std' => $config['atts']['show_more_toggle_text_less'],
			'dependency' => array( 'element' => 'show_more_toggle', 'value' => '1' ),
			'group' => __( 'More Options', 'us' ),
		),
		array(
			'param_name' => 'show_more_toggle_alignment',
			'heading' => us_translate( 'Alignment' ),
			'type' => 'dropdown',
			'value' => array(
				us_translate( 'Default' ) => 'default',
				us_translate( 'Left' ) => 'left',
				us_translate( 'Center' ) => 'center',
				us_translate( 'Right' ) => 'right',
			),
			'std' => $config['atts']['show_more_toggle_alignment'],
			'dependency' => array( 'element' => 'show_more_toggle', 'value' => '1' ),
			'group' => __( 'More Options', 'us' ),
		),
	)
);

vc_update_shortcode_param(
	'vc_column_text', array(
		'param_name' => 'css',
		'type' => 'us_design_options',
		'heading' => '',
		'params' => us_config( 'elements_design_options.css.params', array() ),
		'group' => __( 'Design', 'us' ),
	)
);

// Setting proper shortcode order in VC shortcodes listing
vc_map_update( 'vc_column_text', array( 'weight' => 380 ) );
