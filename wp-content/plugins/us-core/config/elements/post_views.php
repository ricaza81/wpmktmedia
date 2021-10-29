<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The main config form for the Post Views
 */

$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Post Views', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-eye',
	'params' => us_set_params_weight(

		// General section
		array(
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'usb_preview' => TRUE,
			),
			'text_before' => array(
				'title' => __( 'Text before value', 'us' ),
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
			'text_after' => array(
				'title' => __( 'Text after value', 'us' ),
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
			'result_thousand_short' => array(
				'type' => 'switch',
				'switch_text' => __( 'Use "K" shorthand for thousands', 'us' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'result_thousand_separator' => array(
				'title' => __( 'Thousand separator', 'us' ),
				'type' => 'text',
				'std' => ',',
				'usb_preview' => TRUE,
			),
		),

		$design_options_params,
		$hover_options_params
	)
);
