<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: separator
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Separator', 'us' ),
	'description' => __( 'Gap between elements', 'us' ),
	'category' => __( 'Basic', 'us' ),
	'icon' => 'fas fa-grip-lines',
	'params' => us_set_params_weight(

		// General section
		array(
			'size' => array(
				'title' => us_translate( 'Height' ),
				'type' => 'radio',
				'options' => array(
					'small' => 'S',
					'medium' => 'M',
					'large' => 'L',
					'huge' => 'XL',
					'custom' => __( 'Custom', 'us' ),
				),
				'std' => 'medium',
				'admin_label' => TRUE,
				'usb_preview' => array(
					'mod' => 'size',
				),
			),
			'height' => array(
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">30px</span>, <span class="usof-example">2rem</span>, <span class="usof-example">5vh</span>',
				'type' => 'text',
				'std' => '',
				'holder' => 'div',
				'classes' => 'for_above',
				'show_if' => array( 'size', '=', 'custom' ),
				'usb_preview' => array(
					'css' => 'height',
				),
			),
			'show_line' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show horizontal line in the middle', 'us' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'line_width' => array(
				'title' => __( 'Line Width', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => '100%',
					'30' => '30%',
					'50' => '50%',
					'screen' => __( 'Full Width', 'us' ),
				),
				'std' => 'default',
				'cols' => 2,
				'show_if' => array( 'show_line', '!=', FALSE ),
				'usb_preview' => TRUE,
			),
			'thick' => array(
				'title' => __( 'Line Thickness', 'us' ),
				'type' => 'radio',
				'options' => array(
					'1' => '1px',
					'2' => '2px',
					'3' => '3px',
					'4' => '4px',
					'5' => '5px',
				),
				'std' => '1',
				'cols' => 2,
				'show_if' => array( 'show_line', '!=', FALSE ),
				'usb_preview' => array(
					'mod' => 'thick',
				),
			),
			'color' => array(
				'title' => __( 'Line Color', 'us' ),
				'type' => 'select',
				'options' => array(
					'border' => __( 'Border (theme color)', 'us' ),
					'text' => __( 'Text (theme color)', 'us' ),
					'primary' => __( 'Primary (theme color)', 'us' ),
					'secondary' => __( 'Secondary (theme color)', 'us' ),
				),
				'std' => 'border',
				'cols' => 2,
				'show_if' => array( 'show_line', '!=', FALSE ),
				'usb_preview' => array(
					'mod' => 'color',
				),
			),
			'style' => array(
				'title' => __( 'Line Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'solid' => __( 'Solid', 'us' ),
					'dashed' => __( 'Dashed', 'us' ),
					'dotted' => __( 'Dotted', 'us' ),
					'double' => __( 'Double', 'us' ),
				),
				'std' => 'solid',
				'cols' => 2,
				'show_if' => array( 'show_line', '!=', FALSE ),
				'usb_preview' => array(
					'mod' => 'style',
				),
			),
		),

		// Icon and Title
		array(
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'show_if' => array( 'show_line', '!=', FALSE ),
				'group' => __( 'Icon and Title', 'us' ),
				'usb_preview' => TRUE,
			),
			'text' => array(
				'title' => us_translate( 'Title' ),
				'type' => 'text',
				'std' => '',
				'holder' => 'div',
				'show_if' => array( 'show_line', '!=', FALSE ),
				'group' => __( 'Icon and Title', 'us' ),
				'usb_preview' => TRUE,
			),
			'link' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'link',
				'std' => '',
				'show_if' => array( 'text', '!=', '' ),
				'group' => __( 'Icon and Title', 'us' ),
				'usb_preview' => TRUE,
			),
			'title_tag' => array(
				'title' => __( 'Title HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'h6',
				'show_if' => array( 'text', '!=', '' ),
				'group' => __( 'Icon and Title', 'us' ),
				'usb_preview' => array(
					'attr' => 'tag',
					'elm' => '.w-separator-text',
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
				'std' => 'center',
				'show_if' => array( 'show_line', '!=', FALSE ),
				'group' => __( 'Icon and Title', 'us' ),
				'usb_preview' => array(
					'mod' => 'align',
				),
			),
		),

		// Responsive Options section
		array(
			'breakpoint_1_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 600,
						'max' => 1200,
					),
				),
				'std' => '1025px',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_1_height' => array(
				'title' => us_translate( 'Height' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">10px</span>, <span class="usof-example">1rem</span>, <span class="usof-example">3vh</span>',
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_2_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 300,
						'max' => 900,
					),
				),
				'std' => '601px',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
			'breakpoint_2_height' => array(
				'title' => us_translate( 'Height' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">10px</span>, <span class="usof-example">1rem</span>, <span class="usof-example">3vh</span>',
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),
);
