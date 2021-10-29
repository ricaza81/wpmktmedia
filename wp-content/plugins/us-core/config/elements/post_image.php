<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );
$hover_options = us_config( 'elements_hover_options' );
$link_custom_values = us_get_elm_link_options();

return array(
	'title' => __( 'Post Image', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-image',
	'params' => array_merge( array(

		'link' => array(
			'title' => us_translate( 'Link' ),
			'type' => 'select',
			'options' => array_merge(
				array(
					'none' => us_translate( 'None' ),
					'post' => __( 'To a Post', 'us' ),
					'popup_post_image' => __( 'Open original image in a popup', 'us' ),
				),
				$link_custom_values,
				array( 'custom' => __( 'Custom', 'us' ) )
			),
			'std' => 'post',
			'shortcode_std' => 'none',
		),
		'link_new_tab' => array(
			'type' => 'switch',
			'switch_text' => us_translate( 'Open link in a new tab' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'show_if' => array( 'link', '!=', array( 'none', 'popup_post_image', 'custom' ) ),
		),
		'custom_link' => array(
			'placeholder' => us_translate( 'Enter the URL' ),
			'description' => $misc['desc_grid_custom_link'],
			'type' => 'link',
			'std' => array(),
			'shortcode_std' => '',
			'classes' => 'for_above desc_3',
			'show_if' => array( 'link', '=', 'custom' ),
		),
		'placeholder' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show placeholder when post image is absent', 'us' ),
			'std' => FALSE,
		),
		'media_preview' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show media preview for posts with video, audio and gallery format', 'us' ),
			'std' => FALSE,
		),
		'circle' => array(
			'type' => 'switch',
			'switch_text' => __( 'Enable rounded image', 'us' ),
			'std' => FALSE,
		),
		'has_ratio' => array(
			'switch_text' => __( 'Set Aspect Ratio', 'us' ),
			'type' => 'switch',
			'std' => FALSE,
		),
		'ratio' => array(
			'type' => 'select',
			'options' => array(
				'1x1' => '1x1 ' . __( 'square', 'us' ),
				'4x3' => '4x3 ' . __( 'landscape', 'us' ),
				'3x2' => '3x2 ' . __( 'landscape', 'us' ),
				'16x9' => '16:9 ' . __( 'landscape', 'us' ),
				'2x3' => '2x3 ' . __( 'portrait', 'us' ),
				'3x4' => '3x4 ' . __( 'portrait', 'us' ),
				'custom' => __( 'Custom', 'us' ),
			),
			'std' => '1x1',
			'classes' => 'for_above',
			'show_if' => array( 'has_ratio', '!=', FALSE ),
		),
		'ratio_width' => array(
			'placeholder' => us_translate( 'Width' ),
			'type' => 'text',
			'std' => '21',
			'cols' => 2,
			'classes' => 'for_above',
			'show_if' => array( 'ratio', '=', 'custom' ),
		),
		'ratio_height' => array(
			'placeholder' => us_translate( 'Height' ),
			'type' => 'text',
			'std' => '9',
			'cols' => 2,
			'classes' => 'for_above',
			'show_if' => array( 'ratio', '=', 'custom' ),
		),
		'stretch' => array(
			'type' => 'switch',
			'switch_text' => __( 'Stretch the image to the container width', 'us' ),
			'std' => TRUE,
			'show_if' => array( 'has_ratio', '=', FALSE ),
		),
		'thumbnail_size' => array(
			'title' => __( 'Image Size', 'us' ),
			'description' => $misc['desc_img_sizes'],
			'type' => 'select',
			'options' => us_get_image_sizes_list(),
			'std' => 'large',
			'admin_label' => TRUE,
		),

	), $design_options, $hover_options ),
);
