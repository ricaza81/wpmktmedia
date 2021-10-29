<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: socials
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Social Links', 'us' ),
	'icon' => 'fab fa-facebook',
	'params' => us_set_params_weight(

		// General
		array(
			'items' => array(
				'type' => 'group',
				'show_controls' => TRUE,
				'is_sortable' => TRUE,
				'is_accordion' => FALSE,
				'params' => array(
					'type' => array(
						'shortcode_title' => us_translate( 'Type' ),
						'type' => 'select',
						'options' => array_merge(
							us_config( 'social_links' ),
							array( 'custom' => __( 'Custom Icon', 'us' ) )
						),
						'std' => 's500px',
						'cols' => 2,
						'admin_label' => TRUE,
						'classes' => 'for_socials',
					),
					'url' => array(
						'shortcode_title' => us_translate( 'Enter the URL' ),
						'placeholder' => us_translate( 'Enter the URL' ),
						'type' => 'text',
						'std' => '',
						'cols' => 2,
					),
					'custom_start' => array(
						'type' => 'wrapper_start',
						'show_if' => array( 'type', '=', 'custom' ),
					),
					'icon' => array(
						'type' => 'icon',
						'std' => 'fab|apple',
						'show_if' => array( 'type', '=', 'custom' ),
					),
					'title' => array(
						'shortcode_title' => us_translate( 'Title' ),
						'placeholder' => us_translate( 'Title' ),
						'type' => 'text',
						'std' => '',
						'cols' => 2,
						'show_if' => array( 'type', '=', 'custom' ),
					),
					'color' => array(
						'shortcode_title' => us_translate( 'Color' ),
						'type' => 'color',
						'clear_pos' => 'right',
						'std' => '_content_faded',
						'cols' => 2,
						'show_if' => array( 'type', '=', 'custom' ),
					),
					'custom_end' => array(
						'type' => 'wrapper_end',
					),
				),
				'std' => array(
					array(
						'type' => 'facebook',
						'url' => '#',
					),
					array(
						'type' => 'twitter',
						'url' => '#',
					),
				),
				'usb_preview' => TRUE,
			),
		),

		// Appearance
		array(
			'shape' => array(
				'title' => __( 'Icons Shape', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'square' => __( 'Square', 'us' ),
					'rounded' => __( 'Rounded Square', 'us' ),
					'circle' => __( 'Circle', 'us' ),
				),
				'std' => 'square',
				'admin_label' => TRUE,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'shape',
				),
			),
			'style' => array(
				'title' => __( 'Icons Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Simple', 'us' ),
					'colored' => __( 'Solid', 'us' ),
					'outlined' => __( 'Outlined', 'us' ),
					'solid' => __( 'With alternate background', 'us' ),
				),
				'std' => 'default',
				'cols' => 2,
				'show_if' => array( 'shape', '!=', 'none' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'style',
				),
			),
			'icons_color' => array(
				'title' => __( 'Icons Color', 'us' ),
				'type' => 'select',
				'options' => array(
					'brand' => __( 'Default brands colors', 'us' ),
					'text' => __( 'Text (theme color)', 'us' ),
					'link' => __( 'Link (theme color)', 'us' ),
				),
				'std' => 'brand',
				'admin_label' => TRUE,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'color',
				),
			),
			'hover' => array(
				'title' => __( 'Hover Style', 'us' ),
				'type' => 'radio',
				'options' => array(
					'fade' => __( 'Fade', 'us' ),
					'slide' => __( 'Slide', 'us' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'fade',
				'cols' => 2,
				'show_if' => array( 'shape', '!=', 'none' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'hover',
				),
			),
			'gap' => array(
				'title' => __( 'Gap between Icons', 'us' ),
				'type' => 'slider',
				'std' => '0em',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
					'em' => array(
						'min' => 0.0,
						'max' => 1.0,
						'step' => 0.1,
					),
				),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'css' => '--gap',
				),
			),
			'hide_tooltip' => array(
				'type' => 'switch',
				'switch_text' => __( 'Hide tooltip on hover', 'us' ),
				'std' => FALSE,
				'group' => us_translate( 'Appearance' ),
				'context' => array( 'shortcode' ),
				'usb_preview' => TRUE,
			),
			'nofollow' => array(
				'type' => 'switch',
				'switch_text' => sprintf( __( 'Add "%s" attribute', 'us' ), 'nofollow' ),
				'std' => TRUE,
				'group' => us_translate( 'Appearance' ),
			),
		),

		$design_options_params
	),
	'fallback_params' => array(
		'color',
		'align',
	),
);
