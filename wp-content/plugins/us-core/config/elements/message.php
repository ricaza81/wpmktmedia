<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Message Box', 'us' ),
	'description' => __( 'Colored notification box', 'us' ),
	'icon' => 'icon-wpb-information-white',
	'js_view' => 'VcMessageView',
	'params' => array_merge( array(

		'color' => array(
			'title' => us_translate( 'Color' ),
			'type' => 'select',
			'options' => array(
				'blue' => __( 'Blue', 'us' ),
				'yellow' => __( 'Yellow', 'us' ),
				'green' => __( 'Green', 'us' ),
				'red' => __( 'Red', 'us' ),
			),
			'std' => 'blue',
		),
		'content' => array(
			'title' => __( 'Message Text', 'us' ),
			'type' => 'textarea',
			'holder' => 'div',
			'std' => 'I am message box. Click edit button to change this text.',
		),
		'icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => '',
		),
		'closing' => array(
			'type' => 'switch',
			'switch_text' => __( 'Enable closing', 'us' ),
			'std' => FALSE,
		),

	), $design_options ),
);
