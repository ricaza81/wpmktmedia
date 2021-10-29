<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: vwrapper
 */

$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Vertical Wrapper', 'us' ),
	'category' => __( 'Containers', 'us' ),
	'icon' => 'fas fa-ellipsis-v',
	'is_container' => TRUE,
	'show_settings_on_create' => FALSE,
	'as_parent' => array(
		'except' => 'vc_row,vc_row_inner,vc_column,vc_tta_tabs,vc_tta_tour,vc_tta_accordion,vc_tta_section,us_vwrapper',
	),
	'js_view' => 'VcColumnView',
	'params' => us_set_params_weight(

		// General section
		array(
			'alignment' => array(
				'title' => __( 'Items Horizontal Alignment', 'us' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-align-*',
				'options' => array(
					'none' => us_translate( 'Default' ),
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'none',
				'cols' => 2,
				'usb_preview' => array(
					'mod' => 'align',
				),
			),
			'valign' => array(
				'title' => __( 'Items Vertical Alignment', 'us' ),
				'type' => 'radio',
				'options' => array(
					'top' => us_translate( 'Top' ),
					'middle' => us_translate( 'Middle' ),
					'bottom' => us_translate( 'Bottom' ),
				),
				'std' => 'top',
				'cols' => 2,
				'usb_preview' => array(
					'mod' => 'valign',
				),
			),
			'inner_items_gap' => array(
				'title' => __( 'Gap between Items', 'us' ),
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'std' => '0.7rem',
				'usb_preview' => array(
					'css' => '--vwrapper-gap',
				),
			),
		),

		$design_options_params,
		$hover_options_params
	),
);
