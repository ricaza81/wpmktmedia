<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Image Slider', 'us' ),
	'icon' => 'icon-wpb-images-carousel',
	'params' => array_merge( array(

		'ids' => array(
			'title' => us_translate( 'Images' ),
			'type' => 'upload',
			'is_multiple' => TRUE,
			'extension' => 'png,jpg,jpeg,gif,svg', // sets available file types
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
		),
		'nav' => array(
			'title' => __( 'Additional Navigation', 'us' ),
			'type' => 'select',
			'options' => array(
				'none' => us_translate( 'None' ),
				'dots' => __( 'Dots', 'us' ),
				'thumbs' => __( 'Thumbnails', 'us' ),
			),
			'std' => 'none',
			'cols' => 2,
		),
		'transition' => array(
			'title' => __( 'Transition Effect', 'us' ),
			'type' => 'select',
			'options' => array(
				'slide' => __( 'Slide', 'us' ),
				'crossfade' => __( 'Fade', 'us' ),
			),
			'std' => 'slide',
			'cols' => 2,
		),
		'transition_speed' => array(
			'title' => __( 'Transition Duration', 'us' ),
			'description' => $misc['desc_milliseconds'],
			'type' => 'text',
			'std' => '300',
			'cols' => 2,
		),
		'meta' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show image title and description', 'us' ),
			'std' => FALSE,
		),
		'orderby' => array(
			'type' => 'switch',
			'switch_text' => __( 'Display items in random order', 'us' ),
			'std' => FALSE,
		),
		'fullscreen' => array(
			'type' => 'switch',
			'switch_text' => __( 'Allow Full Screen view', 'us' ),
			'std' => FALSE,
		),
		'autoplay' => array(
			'type' => 'switch',
			'switch_text' => __( 'Auto Rotation', 'us' ),
			'std' => FALSE,
		),
		'pause_on_hover' => array(
			'type' => 'switch',
			'switch_text' => __( 'Pause on hover', 'us' ),
			'std' => TRUE,
			'show_if' => array( 'autoplay', '!=', FALSE ),
		),
		'autoplay_period' => array(
			'title' => __( 'Auto Rotation Interval', 'us' ),
			'description' => $misc['desc_seconds'],
			'type' => 'text',
			'std' => '3',
			'show_if' => array( 'autoplay', '!=', FALSE ),
		),
		'img_size' => array(
			'title' => __( 'Images Size', 'us' ),
			'description' => $misc['desc_img_sizes'],
			'type' => 'select',
			'options' => us_get_image_sizes_list(),
			'std' => 'large',
			'cols' => 2,
			'admin_label' => TRUE,
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
			'admin_label' => TRUE,
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
		),

	), $design_options ),
);
