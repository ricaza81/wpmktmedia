<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$design_options = us_config( 'elements_design_options' );
$hover_options = us_config( 'elements_hover_options' );

return array(
	'title' => __( 'Vertical Wrapper', 'us' ),
	'icon' => 'fas fa-ellipsis-v',
	'show_settings_on_create' => FALSE,
	'as_parent' => array(
		'except' => 'vc_row,vc_column,vc_tta_tabs,vc_tta_tour,vc_tta_accordion,vc_tta_section,us_vwrapper',
	),
	'js_view' => 'VcColumnView',
	'params' => array_merge( array(
		'alignment' => array(
			'title' => __( 'Content Horizontal Alignment', 'us' ),
			'type' => 'radio',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'center' => us_translate( 'Center' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
			'cols' => 2,
		),
		'valign' => array(
			'title' => __( 'Content Vertical Alignment', 'us' ),
			'type' => 'radio',
			'options' => array(
				'top' => us_translate( 'Top' ),
				'middle' => us_translate( 'Middle' ),
				'bottom' => us_translate( 'Bottom' ),
			),
			'std' => 'top',
			'cols' => 2,
		),
		'inner_items_gap' => array(
			'title' => __( 'Gap between Items', 'us' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">0</span>, <span class="usof-example">0.7rem</span>, <span class="usof-example">10px</span>',
			'type' => 'text',
			'std' => '0.7rem',
			'placeholder' => '0.7rem',
		),

	), $design_options, $hover_options ),
);
