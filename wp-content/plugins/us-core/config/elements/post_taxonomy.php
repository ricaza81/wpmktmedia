<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: post_taxonomy
 */

$taxonomies_options = us_get_taxonomies();

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Post Taxonomy', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-tags',
	'params' => us_set_params_weight(

		// General section
		array(
			'taxonomy_name' => array(
				'title' => us_translate( 'Show' ),
				'type' => 'select',
				'options' => $taxonomies_options,
				'std' => key( $taxonomies_options ),
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
			'style' => array(
				'title' => __( 'Display as', 'us' ),
				'type' => 'radio',
				'options' => array(
					'simple' => us_translate( 'Text' ),
					'badge' => __( 'Button', 'us' ),
				),
				'std' => 'simple',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'btn_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => us_array_merge(
					array( 'badge' => '– ' . __( 'Badge by default', 'us' ) . ' –' ),
					us_get_btn_styles()
				),
				'std' => 'badge',
				'cols' => 2,
				'show_if' => array( 'style', '=', 'badge' ),
				'usb_preview' => array(
					'elm' => '.w-btn',
					'mod' => 'us-btn-style',
				),
			),
			'separator' => array(
				'title' => __( 'Separator between items', 'us' ),
				'type' => 'text',
				'std' => ', ',
				'cols' => 2,
				'show_if' => array( 'style', '=', 'simple' ),
				'usb_preview' => TRUE,
			),
			'link' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'select',
				'options' => array(
					'post' => __( 'To a Post', 'us' ),
					'archive' => __( 'To a Post Archive', 'us' ),
					'custom' => __( 'Custom', 'us' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'archive',
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
				'usb_preview' => TRUE,
			),
		),

		$design_options_params,
		$hover_options_params
	),
);
