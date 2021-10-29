<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );
$hover_options = us_config( 'elements_hover_options' );
$link_custom_values = us_get_elm_link_options();

return array(
	'title' => __( 'Post Title', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-font',
	'params' => array_merge( array(

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
		),
		'link_new_tab' => array(
			'type' => 'switch',
			'switch_text' => us_translate( 'Open link in a new tab' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'show_if' => array( 'link', '!=', array( 'none', 'custom' ) ),
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
		'color_link' => array(
			'title' => __( 'Link Color', 'us' ),
			'type' => 'switch',
			'switch_text' => __( 'Inherit from text color', 'us' ),
			'std' => TRUE,
			'show_if' => array( 'link', '!=', 'none' ),
		),
		'align' => array(
			'title' => us_translate( 'Alignment' ),
			'type' => 'radio',
			'options' => array(
				'none' => us_translate( 'Default' ),
				'left' => us_translate( 'Left' ),
				'center' => us_translate( 'Center' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'none',
			'admin_label' => TRUE,
		),
		'tag' => array(
			'title' => __( 'HTML tag', 'us' ),
			'type' => 'radio',
			'options' => $misc['html_tag_values'],
			'std' => 'h2',
			'admin_label' => TRUE,
		),

	), $design_options, $hover_options ),
);
