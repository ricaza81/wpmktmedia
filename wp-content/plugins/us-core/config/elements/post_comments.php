<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );
$hover_options = us_config( 'elements_hover_options' );

return array(
	'title' => __( 'Post Comments', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-comments',
	'params' => array_merge( array(

		'layout' => array(
			'title' => us_translate( 'Show' ),
			'type' => 'select',
			'options' => array(
				'comments_template' => __( 'List of comments with response form', 'us' ),
				'amount' => __( 'Comments amount', 'us' ),
			),
			'std' => 'comments_template',
			'admin_label' => TRUE,
			'context' => array( 'shortcode' ),
		),
		'hide_zero' => array(
			'type' => 'switch',
			'switch_text' => __( 'Hide this element if no comments', 'us' ),
			'std' => FALSE,
			'show_if' => array( 'layout', '=', 'amount' ),
		),
		'number' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show only number', 'us' ),
			'std' => FALSE,
			'show_if' => array( 'layout', '=', 'amount' ),
		),
		'link' => array(
			'title' => us_translate( 'Link' ),
			'type' => 'radio',
			'options' => array(
				'post' => __( 'To a Post comments', 'us' ),
				'custom' => __( 'Custom', 'us' ),
				'none' => us_translate( 'None' ),
			),
			'std' => 'post',
			'show_if' => array( 'layout', '=', 'amount' ),
		),
		'custom_link' => array(
			'placeholder' => us_translate( 'Enter the URL' ),
			'description' => $misc['desc_grid_custom_link'],
			'type' => 'link',
			'std' => array(),
			'shortcode_std' => '',
			'grid_classes' => 'desc_3',
			'show_if' => array( 'link', '=', 'custom' ),
		),
		'color_link' => array(
			'title' => __( 'Link Color', 'us' ),
			'type' => 'switch',
			'switch_text' => __( 'Inherit from text color', 'us' ),
			'std' => TRUE,
			'show_if' => array( 'link', '!=', 'none' ),
		),
		'icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => '',
			'show_if' => array( 'layout', '=', 'amount' ),
		),

	), $design_options, $hover_options ),
);
