<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: text
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );
$link_custom_values = us_get_elm_link_options();

/**
 * @return array
 */
return array(
	'title' => us_translate( 'Text' ),
	'description' => __( 'Custom text with link and icon', 'us' ),
	'category' => __( 'Basic', 'us' ),
	'icon' => 'fas fa-font',
	'params' => us_set_params_weight(

		// General section
		array(
			'text' => array(
				'title' => us_translate( 'Text' ),
				'type' => 'text',
				'std' => 'Some text',
				'holder' => 'div',
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-text-value',
				),
			),
			'wrap' => array(
				'type' => 'switch',
				'switch_text' => __( 'Allow move content to the next line', 'us' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'context' => array( 'header' ),
				'usb_preview' => array(
					'toggle_class' => 'wrap',
				),
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
				'usb_preview' => TRUE,
			),
			'link_new_tab' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Open link in a new tab' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'link_type', '!=', array( 'none', 'custom' ) ),
				'usb_preview' => TRUE,
			),
			'link' => array(
				'placeholder' => us_translate( 'Enter the URL' ),
				'description' => $misc['desc_grid_custom_link'],
				'type' => 'link',
				'std' => array(),
				'shortcode_std' => '',
				'classes' => 'for_above desc_3',
				'show_if' => array( 'link_type', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'onclick_code' => array(
				'type' => 'text',
				'std' => 'return false',
				'classes' => 'for_above',
				'show_if' => array( 'link_type', '=', 'onclick' ),
				'usb_preview' => TRUE,
			),
			'tag' => array(
				'title' => __( 'HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'div',
				'admin_label' => TRUE,
				'usb_preview' => array(
					'attr' => 'tag',
				),
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
	'fallback_params' => array(
		'align',
	),
);
