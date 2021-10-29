<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Separator', 'us' ),
	'description' => __( 'Gap between elements', 'us' ),
	'icon' => 'icon-wpb-ui-separator',
	'params' => array_merge( array(

		// General
		'size' => array(
			'title' => us_translate( 'Height' ),
			'type' => 'select',
			'options' => array(
				'small' => __( 'Small', 'us' ),
				'medium' => __( 'Medium', 'us' ),
				'large' => __( 'Large', 'us' ),
				'huge' => __( 'Huge', 'us' ),
				'custom' => __( 'Custom', 'us' ),
			),
			'std' => 'medium',
			'admin_label' => TRUE,
		),
		'height' => array(
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">30px</span>, <span class="usof-example">2rem</span>, <span class="usof-example">5vh</span>',
			'type' => 'text',
			'std' => '',
			'holder' => 'div',
			'classes' => 'for_above',
			'show_if' => array( 'size', '=', 'custom' ),
		),
		'show_line' => array(
			'type' => 'switch',
			'switch_text' => __( 'Show horizontal line in the middle', 'us' ),
			'std' => FALSE,
		),
		'line_width' => array(
			'title' => __( 'Line Width', 'us' ),
			'type' => 'select',
			'options' => array(
				'default' => __( 'Container width', 'us' ),
				'screen' => __( 'Screen width', 'us' ),
				'30' => sprintf( __( '%s of container width', 'us' ), '30%' ),
				'50' => sprintf( __( '%s of container width', 'us' ), '50%' ),
			),
			'std' => 'default',
			'cols' => 2,
			'show_if' => array( 'show_line', '!=', FALSE ),
		),
		'thick' => array(
			'title' => __( 'Line Thickness', 'us' ),
			'type' => 'select',
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
		),

		// Icon and Title
		'icon' => array(
			'title' => __( 'Icon', 'us' ),
			'type' => 'icon',
			'std' => '',
			'show_if' => array( 'show_line', '!=', FALSE ),
			'group' => __( 'Icon and Title', 'us' ),
		),
		'text' => array(
			'title' => us_translate( 'Title' ),
			'type' => 'text',
			'std' => '',
			'holder' => 'div',
			'show_if' => array( 'show_line', '!=', FALSE ),
			'group' => __( 'Icon and Title', 'us' ),
		),
		'link' => array(
			'title' => us_translate( 'Link' ),
			'type' => 'link',
			'std' => '',
			'show_if' => array( 'text', '!=', '' ),
			'group' => __( 'Icon and Title', 'us' ),
		),
		'title_tag' => array(
			'title' => __( 'Title HTML tag', 'us' ),
			'type' => 'select',
			'options' => $misc['html_tag_values'],
			'std' => 'h6',
			'show_if' => array( 'text', '!=', '' ),
			'group' => __( 'Icon and Title', 'us' ),
		),
		'align' => array(
			'title' => us_translate( 'Alignment' ),
			'type' => 'select',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'center' => us_translate( 'Center' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'center',
			'show_if' => array( 'show_line', '!=', FALSE ),
			'group' => __( 'Icon and Title', 'us' ),
		),

		// Responsive Options
		'breakpoint_1_width' => array(
			'title' => __( 'Below screen width', 'us' ),
			'type' => 'text',
			'std' => '1025px',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_1_height' => array(
			'title' => us_translate( 'Height' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">10px</span>, <span class="usof-example">1rem</span>, <span class="usof-example">3vh</span>',
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_2_width' => array(
			'title' => __( 'Below screen width', 'us' ),
			'type' => 'text',
			'std' => '601px',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),
		'breakpoint_2_height' => array(
			'title' => us_translate( 'Height' ),
			'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">10px</span>, <span class="usof-example">1rem</span>, <span class="usof-example">3vh</span>',
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => us_translate( 'Responsive Options', 'js_composer' ),
		),

	), $design_options ),
);
