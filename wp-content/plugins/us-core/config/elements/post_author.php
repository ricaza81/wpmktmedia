<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: post_author
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Post Author', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-user',
	'params' => us_set_params_weight(

		// General section
		array(
			'link' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'select',
				'options' => array(
					'author_page' => __( 'To the page with the Author\'s posts', 'us' ),
					'author_website' => __( 'To the Author\'s website (if specified on his profile)', 'us' ),
					'post' => __( 'To a Post', 'us' ),
					'custom' => __( 'Custom', 'us' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'author_page',
				'usb_preview' => TRUE,
			),
			'custom_link' => array(
				'placeholder' => us_translate( 'Enter the URL' ),
				'description' => $misc['desc_grid_custom_link'],
				'type' => 'link',
				'std' => array(),
				'shortcode_std' => '',
				'grid_classes' => 'desc_3',
				'show_if' => array( 'link', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'color_link' => array(
				'title' => __( 'Link Color', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Inherit from text color', 'us' ),
				'std' => TRUE,
				'show_if' => array( 'link', '!=', 'none' ),
				'usb_preview' => array(
					'toggle_class' => 'color_link_inherit',
				),
			),
			'avatar' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Profile Picture' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'avatar_width' => array(
				'title' => __( 'Picture Width', 'us' ),
				'description' => $misc['desc_pixels'],
				'type' => 'text',
				'std' => '96px',
				'cols' => 2,
				'show_if' => array( 'avatar', '!=', FALSE ),
				'usb_preview' => TRUE,
			),
			'avatar_pos' => array(
				'title' => __( 'Picture Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'top' => us_translate( 'Top' ),
					'left' => us_translate( 'Left' ),
				),
				'std' => 'top',
				'cols' => 2,
				'show_if' => array( 'avatar', '!=', FALSE ),
				'usb_preview' => array(
					'mod' => 'avapos',
				),
			),
			'posts_count' => array(
				'type' => 'switch',
				'switch_text' => __( 'Posts count', 'us' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'website' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Website' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'info' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Biographical Info' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'usb_preview' => TRUE,
			),
		),

		$design_options_params,
		$hover_options_params
	),
);
