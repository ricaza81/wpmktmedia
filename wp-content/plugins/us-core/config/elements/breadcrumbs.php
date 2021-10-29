<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Breadcrumbs', 'us' ),
	'description' => __( 'Shows current page location', 'us' ),
	'icon' => 'fas fa-angle-double-right',
	'params' => array_merge( array(

		'home' => array(
			'title' => __( 'Homepage Label', 'us' ),
			'description' => __( 'Leave blank to hide the homepage link', 'us' ),
			'type' => 'text',
			'std' => us_translate( 'Home' ),
		),
		'show_current' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show current page', 'us' ),
			'std' => FALSE,
		),
		'align' => array(
			'title' => us_translate( 'Alignment' ),
			'type' => 'select',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'center' => us_translate( 'Center' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
			'admin_label' => TRUE,
		),
		'separator_type' => array(
			'title' => __( 'Separator between items', 'us' ),
			'type' => 'select',
			'options' => array(
				'icon' => __( 'Icon', 'us' ),
				'custom' => __( 'Custom symbol', 'us' ),
			),
			'std' => 'icon',
		),
		'separator_icon' => array(
			'type' => 'icon',
			'std' => 'far|angle-right',
			'classes' => 'for_above',
			'show_if' => array( 'separator_type', '=', 'icon' ),
		),
		'separator_symbol' => array(
			'type' => 'text',
			'std' => '/',
			'classes' => 'for_above',
			'show_if' => array( 'separator_type', '=', 'custom' ),
		),

	), $design_options ),
);
