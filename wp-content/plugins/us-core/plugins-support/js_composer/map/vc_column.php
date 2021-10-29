<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Extending shortcode: vc_column
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
vc_remove_param( 'vc_column', 'css_animation' );
vc_remove_param( 'vc_column', 'video_bg' );
vc_remove_param( 'vc_column', 'video_bg_url' );
vc_remove_param( 'vc_column', 'video_bg_parallax' );
vc_remove_param( 'vc_column', 'parallax' );
vc_remove_param( 'vc_column', 'parallax_image' );
vc_remove_param( 'vc_column', 'parallax_speed_video' );
vc_remove_param( 'vc_column', 'parallax_speed_bg' );

vc_update_shortcode_param(
	'vc_column', array(
		'param_name' => 'width',
		'description' => '',
		'value' => array(
			'1/12 — 8.33%' => '1/12',
			'1/6 — 16.66%'=> '1/6',
			'1/5 — 20%'=> '1/5',
			'1/4 — 25%'=> '1/4',
			'1/3 — 33.33%'=> '1/3',
			'2/5 — 40%'=> '2/5',
			'5/12 — 41.66%'=> '5/12',
			'1/2 — 50%'=> '1/2',
			'7/12 — 58.33%'=> '7/12',
			'3/5 — 60%'=> '3/5',
			'2/3 — 66.66%'=> '2/3',
			'3/4 — 75%'=> '3/4',
			'4/5 — 80%'=> '4/5',
			'5/6 — 83.33%'=> '5/6',
			'11/12 — 91.66%'=> '11/12',
			'1/1 — 100%'=> '1/1',
		),
	)
);
vc_update_shortcode_param(
	'vc_column', array(
		'param_name' => 'offset',
		'heading' => '',
	)
);
vc_update_shortcode_param(
	'vc_column', array(
		'param_name' => 'el_class',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 20,
		'group' => __( 'Design', 'us' ),
	)
);
vc_update_shortcode_param(
	'vc_column', array(
		'param_name' => 'el_id',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'group' => __( 'Design', 'us' ),
	)
);

vc_update_shortcode_param(
	'vc_column', array(
		'param_name' => 'css',
		'type' => 'us_design_options',
		'heading' => '',
		'params' => us_config( 'elements_design_options.css.params', array() ),
		'group' => __( 'Design', 'us' ),
	)
);

vc_add_params( 'vc_column', array(
	array(
		'param_name' => 'link',
		'heading' => us_translate( 'Link' ),
		'type' => 'vc_link',
		'std' => $config['atts']['link'],
		'weight' => 50,
	),
	array(
		'param_name' => 'animate',
		'heading' => __( 'Animation', 'us' ),
		'description' => __( 'Selected animation will be applied to this element, when it enters into the browsers viewport.', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			us_translate( 'None' ) => '',
			__( 'Fade', 'us' ) => 'fade',
			__( 'Appear From Center', 'us' ) => 'afc',
			__( 'Appear From Left', 'us' ) => 'afl',
			__( 'Appear From Right', 'us' ) => 'afr',
			__( 'Appear From Bottom', 'us' ) => 'afb',
			__( 'Appear From Top', 'us' ) => 'aft',
			__( 'Height Stretch', 'us' ) => 'hfc',
			__( 'Width Stretch', 'us' ) => 'wfc',
		),
		'std' => $config['atts']['animate'],
		'admin_label' => TRUE,
		'weight' => 30,
	),
	array(
		'param_name' => 'animate_delay',
		'heading' => __( 'Animation Delay (in seconds)', 'us' ),
		'type' => 'textfield',
		'std' => $config['atts']['animate_delay'],
		'dependency' => array( 'element' => 'animate', 'not_empty' => TRUE ),
		'admin_label' => TRUE,
		'weight' => 20,
	),
	array(
		'param_name' => 'sticky',
		'heading' => __( 'Sticky Column', 'us' ),
		'type' => 'checkbox',
		'value' => array( __( 'Fix this column at the top of a page during scroll', 'us' ) => TRUE ),
		( ( $config['atts']['sticky'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['sticky'],
		'weight' => 10,
	),
	array(
		'param_name' => 'sticky_pos_top',
		'heading' => __( 'Sticky Column Top Position', 'us' ),
		'description' => __( 'Set the distance from the top of a page where the column will stick.', 'us' ) . ' ' . __( 'Leave blank to use the default.', 'us' ) . ' ' . __( 'Examples:', 'us' ) . ' <span class="usof-example">0</span>, <span class="usof-example">80px</span>, <span class="usof-example">6rem</span>',
		'type' => 'textfield',
		'value' => $config['atts']['sticky_pos_top'],
		'dependency' => array( 'element' => 'sticky', 'not_empty' => TRUE ),
		'weight' => 9,
	),
	array(
		'param_name' => 'stretch',
		'heading' => __( 'Stretched Column', 'us' ),
		'type' => 'checkbox',
		'value' => array( __( 'Stretch to the screen edge', 'us' ) => TRUE ),
		( ( $config['atts']['stretch'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['stretch'],
		'weight' => 8,
	),
	array(
		'param_name' => 'us_bg_overlay_color',
		'heading' => __( 'Background Overlay', 'us' ),
		'type' => 'us_color',
		'clear_pos' => 'right',
		'std' => us_arr_path( $config, 'atts.us_bg_overlay_color', '' ),
		'weight' => 7,
	),
)
);
