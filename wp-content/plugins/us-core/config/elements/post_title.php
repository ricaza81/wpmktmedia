<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: post_title
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );
$link_custom_values = us_get_elm_link_options();

/**
 * @return array
 */
return array(
	'title' => __( 'Post Title', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-font',
	'params' => us_set_params_weight(

		// General section
		array(
			'link' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'select',
				'options' => array_merge(
					array(
						'none' => us_translate( 'None' ),
						'post' => __( 'To a Post', 'us' ),
					),
					$link_custom_values,
					array( 'custom' => __( 'Custom', 'us' ) )
				),
				'std' => 'post',
				'shortcode_std' => 'none',
				'usb_preview' => TRUE,
			),
			'link_new_tab' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Open link in a new tab' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'link', '!=', array( 'none', 'custom' ) ),
				'usb_preview' => TRUE,
			),
			'custom_link' => array(
				'placeholder' => us_translate( 'Enter the URL' ),
				'description' => $misc['desc_grid_custom_link'],
				'type' => 'link',
				'std' => array(),
				'shortcode_std' => '',
				'classes' => 'for_above desc_3',
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
			'tag' => array(
				'title' => __( 'HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'h2',
				'admin_label' => TRUE,
				'usb_preview' => array(
					'attr' => 'tag',
				),
			),
		),

		$design_options_params,
		$hover_options_params
	),
);
