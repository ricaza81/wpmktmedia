<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Counter', 'us' ),
	'description' => __( 'Animated number with text', 'us' ),
	'params' => array_merge( array(

		// General
		'initial' => array(
			'title' => __( 'Initial counting value', 'us' ),
			'description' => __( 'With all the prefixes, suffixes and decimal marks if needed.', 'us' ) . ' ' . __( 'Examples:', 'us' ) . ' 0, $0, 1%, 0.001, 1kg',
			'type' => 'text',
			'std' => '1',
		),
		'final' => array(
			'title' => __( 'Final counting value', 'us' ),
			'description' => __( 'The way it should look like, when the animation ends.', 'us' ) . ' ' . __( 'Examples:', 'us' ) . ' 100, $70, 98%, 0.374, 35kg',
			'type' => 'text',
			'std' => '99',
			'holder' => 'div',
		),
		'color' => array(
			'title' => us_translate( 'Color' ),
			'type' => 'select',
			'options' => array(
				'primary' => __( 'Primary (theme color)', 'us' ),
				'secondary' => __( 'Secondary (theme color)', 'us' ),
				'heading' => __( 'Heading (theme color)', 'us' ),
				'text' => __( 'Text (theme color)', 'us' ),
				'custom' => __( 'Custom', 'us' ),
			),
			'std' => 'primary',
		),
		'custom_color' => array(
			'type' => 'color',
			'clear_pos' => 'left',
			'with_gradient' => FALSE,
			'std' => '',
			'classes' => 'for_above',
			'show_if' => array( 'color', '=', 'custom' ),
		),

		// More Options
		'title' => array(
			'title' => us_translate( 'Title' ),
			'type' => 'text',
			'std' => __( 'Projects completed', 'us' ),
			'holder' => 'div',
			'group' => __( 'More Options', 'us' ),
		),
		'title_size' => array(
			'title' => __( 'Title Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'title_tag' => array(
			'title' => __( 'Title HTML tag', 'us' ),
			'type' => 'select',
			'options' => $misc['html_tag_values'],
			'std' => 'h6',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
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
			'group' => __( 'More Options', 'us' ),
		),
		'duration' => array(
			'title' => __( 'Animation Duration (in seconds)', 'us' ),
			'type' => 'text',
			'std' => '2',
			'group' => __( 'More Options', 'us' ),
		),

	), $design_options ),
);
