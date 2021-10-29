<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * HOVER EFFECT settings for Grid Layout elements
 */

return array(

	'hover' => array(
		'switch_text' => __( 'Enable hover effect', 'us' ),
		'description' => __( 'Change appearance of this element when hover on the whole Grid Layout', 'us' ),
		'type' => 'switch',
		'std' => FALSE,
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'opacity' => array(
		'title' => __( 'Opacity', 'us' ),
		'type' => 'slider',
		'std' => '1',
		'options' => array(
			'' => array(
				'min' => 0.00,
				'max' => 1.00,
				'step' => 0.05,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'opacity_hover' => array(
		'title' => __( 'Opacity on Hover', 'us' ),
		'type' => 'slider',
		'std' => '1',
		'options' => array(
			'' => array(
				'min' => 0.00,
				'max' => 1.00,
				'step' => 0.05,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'scale' => array(
		'title' => __( 'Scale', 'us' ),
		'type' => 'slider',
		'std' => '1',
		'options' => array(
			'' => array(
				'min' => 0.00,
				'max' => 2.00,
				'step' => 0.05,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'scale_hover' => array(
		'title' => __( 'Scale on Hover', 'us' ),
		'type' => 'slider',
		'std' => '1',
		'options' => array(
			'' => array(
				'min' => 0.00,
				'max' => 2.00,
				'step' => 0.05,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'translateX' => array(
		'title' => __( 'Horizontal Shift', 'us' ),
		'type' => 'slider',
		'std' => '0',
		'options' => array(
			'%' => array(
				'min' => - 100,
				'max' => 100,
			),
			'px' => array(
				'min' => - 100,
				'max' => 100,
			),
			'rem' => array(
				'min' => - 6.0,
				'max' => 6.0,
				'step' => 0.1,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'translateX_hover' => array(
		'title' => __( 'Horizontal Shift on Hover', 'us' ),
		'type' => 'slider',
		'std' => '0',
		'options' => array(
			'%' => array(
				'min' => - 100,
				'max' => 100,
			),
			'px' => array(
				'min' => - 100,
				'max' => 100,
			),
			'rem' => array(
				'min' => - 6.0,
				'max' => 6.0,
				'step' => 0.1,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'translateY' => array(
		'title' => __( 'Vertical Shift', 'us' ),
		'type' => 'slider',
		'std' => '0',
		'options' => array(
			'%' => array(
				'min' => - 100,
				'max' => 100,
			),
			'px' => array(
				'min' => - 100,
				'max' => 100,
			),
			'rem' => array(
				'min' => - 6.0,
				'max' => 6.0,
				'step' => 0.1,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'translateY_hover' => array(
		'title' => __( 'Vertical Shift on Hover', 'us' ),
		'type' => 'slider',
		'std' => '0',
		'options' => array(
			'%' => array(
				'min' => - 100,
				'max' => 100,
			),
			'px' => array(
				'min' => - 100,
				'max' => 100,
			),
			'rem' => array(
				'min' => - 6.0,
				'max' => 6.0,
				'step' => 0.1,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'color_bg_hover' => array(
		'title' => __( 'Background Сolor on Hover', 'us' ),
		'type' => 'color',
		'clear_pos' => 'right',
		'std' => '',
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'color_border_hover' => array(
		'title' => __( 'Border Сolor on Hover', 'us' ),
		'type' => 'color',
		'clear_pos' => 'right',
		'with_gradient' => FALSE,
		'std' => '',
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'color_text_hover' => array(
		'title' => __( 'Text Сolor on Hover', 'us' ),
		'type' => 'color',
		'clear_pos' => 'right',
		'with_gradient' => FALSE,
		'std' => '',
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'transition_duration' => array(
		'title' => __( 'Hover Transition Duration', 'us' ),
		'type' => 'slider',
		'std' => '0.3s',
		'options' => array(
			's' => array(
				'min' => 0.00,
				'max' => 2.00,
				'step' => 0.05,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'transform_origin_X' => array(
		'title' => 'Transform origin X',
		'type' => 'slider',
		'std' => '50%',
		'options' => array(
			'%' => array(
				'min' => 0,
				'max' => 100,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),
	'transform_origin_Y' => array(
		'title' => 'Transform origin Y',
		'type' => 'slider',
		'std' => '50%',
		'options' => array(
			'%' => array(
				'min' => 0,
				'max' => 100,
			),
		),
		'cols' => 2,
		'show_if' => array( 'hover', '=', TRUE ),
		'group' => __( 'Hover Effect', 'us' ),
		'context' => array( 'grid' ),
	),

);
