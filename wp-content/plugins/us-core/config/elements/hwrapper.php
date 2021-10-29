<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$design_options = us_config( 'elements_design_options' );
$hover_options = us_config( 'elements_hover_options' );

return array(
	'title' => __( 'Horizontal Wrapper', 'us' ),
	'icon' => 'fas fa-ellipsis-h',
	'as_parent' => array(
		'except' => 'vc_row,vc_column,vc_tta_tabs,vc_tta_tour,vc_tta_accordion,vc_tta_section,us_hwrapper',
	),
	'show_settings_on_create' => FALSE,
	'js_view' => 'VcColumnView',
	'params' => array_merge(
		array(
			'alignment' => array(
				'title' => __( 'Content Horizontal Alignment', 'us' ),
				'type' => 'select',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
					'justify' => us_translate( 'Justify' ),
				),
				'std' => 'left',
				'cols' => 2,
			),
			'valign' => array(
				'title' => __( 'Content Vertical Alignment', 'us' ),
				'type' => 'select',
				'options' => array(
					'top' => us_translate( 'Top' ),
					'middle' => us_translate( 'Middle' ),
					'bottom' => us_translate( 'Bottom' ),
					'baseline' => __( 'With baseline', 'us' ),
				),
				'std' => 'top',
				'cols' => 2,
			),
			'inner_items_gap' => array(
				'title' => __( 'Gap between Items', 'us' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">0</span>, <span class="usof-example">1.5rem</span>, <span class="usof-example">10px</span>',
				'type' => 'text',
				'std' => '1.2rem',
				'placeholder' => '1.2rem',
			),
			'wrap' => array(
				'switch_text' => __( 'Allow move content to the next line', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
			),
		), $design_options, $hover_options
	),
);
