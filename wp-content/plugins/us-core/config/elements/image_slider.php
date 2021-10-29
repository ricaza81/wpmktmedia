<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: image_slider
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Image Slider', 'us' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-images',
	'params' => us_set_params_weight(

		// General section
		array(
			'ids' => array(
				'title' => us_translate( 'Images' ),
				'type' => 'upload',
				'is_multiple' => TRUE,
				'extension' => 'png,jpg,jpeg,gif,svg', // sets available file types
				'usb_preview' => TRUE,
			),
			'arrows' => array(
				'title' => __( 'Prev/Next arrows', 'us' ),
				'type' => 'select',
				'options' => array(
					'always' => __( 'Show always', 'us' ),
					'hover' => __( 'Show on hover', 'us' ),
					'hide' => us_translate( 'Hide' ),
				),
				'std' => 'always',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'nav' => array(
				'title' => __( 'Additional Navigation', 'us' ),
				'type' => 'radio',
				'options' => array(
					'none' => us_translate( 'None' ),
					'dots' => __( 'Dots', 'us' ),
					'thumbs' => __( 'Thumbnails', 'us' ),
				),
				'std' => 'none',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'transition' => array(
				'title' => __( 'Transition Effect', 'us' ),
				'type' => 'radio',
				'options' => array(
					'slide' => __( 'Slide', 'us' ),
					'crossfade' => __( 'Fade', 'us' ),
				),
				'std' => 'slide',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'transition_speed' => array(
				'title' => __( 'Transition Duration', 'us' ),
				'type' => 'slider',
				'std' => '250ms',
				'options' => array(
					'ms' => array(
						'min' => 0,
						'max' => 2000,
						'step' => 50,
					),
				),
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'meta' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show image title and description', 'us' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'orderby' => array(
				'type' => 'switch',
				'switch_text' => __( 'Display items in random order', 'us' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'fullscreen' => array(
				'type' => 'switch',
				'switch_text' => __( 'Allow Full Screen view', 'us' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'autoplay' => array(
				'type' => 'switch',
				'switch_text' => __( 'Auto Rotation', 'us' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'pause_on_hover' => array(
				'type' => 'switch',
				'switch_text' => __( 'Pause on hover', 'us' ),
				'std' => TRUE,
				'show_if' => array( 'autoplay', '!=', FALSE ),
				'usb_preview' => TRUE,
			),
			'autoplay_period' => array(
				'title' => __( 'Auto Rotation Interval', 'us' ),
				'type' => 'slider',
				'std' => '3s',
				'options' => array(
					's' => array(
						'min' => 1.0,
						'max' => 9.0,
						'step' => 0.5,
					),
				),
				'show_if' => array( 'autoplay', '!=', FALSE ),
				'usb_preview' => TRUE,
			),
			'img_size' => array(
				'title' => __( 'Images Size', 'us' ),
				'description' => $misc['desc_img_sizes'],
				'type' => 'select',
				'options' => us_get_image_sizes_list(),
				'std' => 'large',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'img_fit' => array(
				'title' => __( 'Images Fit', 'us' ),
				'type' => 'select',
				'options' => array(
					'scaledown' => __( 'Initial', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'cover' => __( 'Fill Area', 'us' ),
				),
				'std' => 'scaledown',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'style' => array(
				'title' => __( 'Images Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'phone6-1' => __( 'Phone 6 Black Realistic', 'us' ),
					'phone6-2' => __( 'Phone 6 White Realistic', 'us' ),
					'phone6-3' => __( 'Phone 6 Black Flat', 'us' ),
					'phone6-4' => __( 'Phone 6 White Flat', 'us' ),
				),
				'std' => 'none',
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),
	'usb_init_js' => '$elm.wSlider()',
);
