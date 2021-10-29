<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );
$link_custom_values = us_get_elm_link_options();

return array(
	'title' => us_translate( 'Text' ),
	'description' => __( 'Custom text with link and icon', 'us' ),
	'icon' => 'fas fa-text',
	'params' => array_merge( array(

		'text' => array(
			'title' => us_translate( 'Text' ),
			'type' => 'text',
			'std' => 'Some text',
			'holder' => 'div',
		),
		'wrap' => array(
			'type' => 'switch',
			'switch_text' => __( 'Allow move content to the next line', 'us' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'context' => array( 'header' ),
		),
		'link_type' => array(
			'title' => us_translate( 'Link' ),
			'type' => 'select',
			'options' => array_merge(
				array(
					'none' => us_translate( 'None' ),
					'elm_value' => __( 'Use the element value as link', 'us' ),
					'onclick' => __( 'Onclick JavaScript action', 'us' ),
				),
				$link_custom_values,
				array( 'custom' => __( 'Custom', 'us' ) )
			),
			'std' => 'none',
		),
		'link_new_tab' => array(
			'type' => 'switch',
			'switch_text' => us_translate( 'Open link in a new tab' ),
			'std' => FALSE,
			'classes' => 'for_above',
			'show_if' => array( 'link_type', '!=', array( 'none', 'custom' ) ),
		),
		'link' => array(
			'placeholder' => us_translate( 'Enter the URL' ),
			'description' => $misc['desc_grid_custom_link'],
			'type' => 'link',
			'std' => array(),
			'shortcode_std' => '',
			'classes' => 'for_above desc_3',
			'show_if' => array( 'link_type', '=', 'custom' ),
		),
		'onclick_code' => array(
			'type' => 'text',
			'std' => 'return false',
			'classes' => 'for_above',
			'show_if' => array( 'link_type', '=', 'onclick' ),
		),
		'tag' => array(
			'title' => __( 'HTML tag', 'us' ),
			'type' => 'radio',
			'options' => $misc['html_tag_values'],
			'std' => 'div',
			'admin_label' => TRUE,
		),
		'icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => '',
		),
	), $design_options ),
	'deprecated_params' => array(
		'align',
	),
);
