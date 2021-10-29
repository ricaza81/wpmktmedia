<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: breadcrumbs
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Breadcrumbs', 'us' ),
	'icon' => 'fas fa-angle-double-right',
	'category' => __( 'Post Elements', 'us' ),
	'params' => us_set_params_weight(

		// General section
		array(
			'home' => array(
				'title' => __( 'Homepage Label', 'us' ),
				'description' => __( 'Leave blank to hide the homepage link', 'us' ),
				'type' => 'text',
				'std' => us_translate( 'Home' ),
				'usb_preview' => array(
					'attr' => 'text',
					'elm' => '.g-breadcrumbs-item:first span',
				),
			),
			'show_current' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show current page', 'us' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'align' => array(
				'title' => us_translate( 'Alignment' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-align-*',
				'options' => array(
					'none' => us_translate( 'Default' ),
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'none',
				'admin_label' => TRUE,
				'usb_preview' => array(
					'mod' => 'align',
				),
			),
			'separator_type' => array(
				'title' => __( 'Separator between items', 'us' ),
				'type' => 'radio',
				'options' => array(
					'icon' => __( 'Icon', 'us' ),
					'custom' => __( 'Custom', 'us' ),
				),
				'std' => 'icon',
				'usb_preview' => TRUE,
			),
			'separator_icon' => array(
				'type' => 'icon',
				'std' => 'far|angle-right',
				'classes' => 'for_above',
				'show_if' => array( 'separator_type', '=', 'icon' ),
				'usb_preview' => TRUE,
			),
			'separator_symbol' => array(
				'type' => 'text',
				'std' => '/',
				'classes' => 'for_above',
				'show_if' => array( 'separator_type', '=', 'custom' ),
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.g-breadcrumbs-separator',
				),
			),
		),

		$design_options_params
	),
);
