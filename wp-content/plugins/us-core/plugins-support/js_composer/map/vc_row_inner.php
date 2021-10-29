<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Extending shortcode: vc_row_inner
 */

vc_remove_param( 'vc_row_inner', 'equal_height' );
vc_remove_param( 'vc_row_inner', 'content_placement' );
vc_remove_param( 'vc_row_inner', 'rtl_reverse' );

vc_update_shortcode_param(
	'vc_row_inner', array(
		'heading' => __( 'Additional gap', 'us' ),
		'param_name' => 'gap',
		'type' => 'textfield',
		'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">2px</span>, <span class="usof-example">1.5rem</span>, <span class="usof-example">1vw</span>',
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 9,
		'group' => us_translate( 'Columns' ),
	)
);
vc_update_shortcode_param(
	'vc_row_inner', array(
		'param_name' => 'disable_element',
		'heading' => '',
		'value' => array( us_translate( 'Disable row', 'js_composer' ) => 'yes' ),
		'weight' => 1,
		'group' => us_translate( 'Columns' ),
	)
);
vc_update_shortcode_param(
	'vc_row_inner', array(
		'param_name' => 'el_class',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'weight' => 1,
		'group' => __( 'Design', 'us' ),
	)
);
vc_update_shortcode_param(
	'vc_row_inner', array(
		'param_name' => 'el_id',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'group' => __( 'Design', 'us' ),
	)
);

vc_update_shortcode_param(
	'vc_row_inner', array(
		'param_name' => 'css',
		'type' => 'us_design_options',
		'heading' => '',
		'params' => us_config( 'elements_design_options.css.params', array() ),
		'group' => __( 'Design', 'us' ),
	)
);

vc_add_params(
	'vc_row_inner', array(
		array(
			'param_name' => 'content_placement',
			'heading' => __( 'Columns Content Position', 'us' ),
			'type' => 'dropdown',
			'value' => array(
				us_translate( 'Top' ) => 'top',
				us_translate( 'Middle' ) => 'middle',
				us_translate( 'Bottom' ) => 'bottom',
			),
			'std' => $config['atts']['content_placement'],
			'edit_field_class' => 'vc_col-sm-6',
			'weight' => 10,
			'group' => us_translate( 'Columns' ),
		),
		array(
			'param_name' => 'columns_type',
			'description' => __( 'Improves appearance of columns with background', 'us' ),
			'type' => 'checkbox',
			'value' => array( __( 'Add extra padding around columns content', 'us' ) => TRUE ),
			( ( $config['atts']['columns_type'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['columns_type'],
			'weight' => 8,
			'group' => us_translate( 'Columns' ),
		),
		array(
			'param_name' => 'columns_reverse',
			'description' => __( 'The last column will be shown on the top.', 'us' ) . ' ' . sprintf( __( 'Applied when the screen width is less than %s', 'us' ), '<a target="_blank" rel="noopener" href="' . admin_url( 'admin.php?page=us-theme-options' ) . '#layout" title="' . __( 'edit in Theme Options', 'us' ) . '">' . us_get_option( 'columns_stacking_width' ) . '</a>' ),
			'type' => 'checkbox',
			'value' => array( __( 'Reverse order for columns stacking', 'us' ) => TRUE ),
			( ( $config['atts']['columns_reverse'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['columns_reverse'],
			'weight' => 7,
			'group' => us_translate( 'Columns' ),
		),
	)
);
