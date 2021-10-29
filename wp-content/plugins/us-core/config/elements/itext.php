<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Interactive Text', 'us' ),
	'description' => __( 'Text with dynamically changing part', 'us' ),
	'icon' => 'fas fa-italic',
	'params' => array_merge( array(

		// General
		'texts' => array(
			'title' => __( 'Text States', 'us' ),
			'description' => __( 'Each value on a new line', 'us' ),
			'type' => 'textarea',
			'std' => 'We create great design' . "\n" . 'We create great websites' . "\n" . 'We create great code',
			'holder' => 'div',
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
			'cols' => 2,
		),
		'tag' => array(
			'title' => __( 'HTML tag', 'us' ),
			'type' => 'select',
			'options' => $misc['html_tag_values'],
			'std' => 'h2',
			'cols' => 2,
		),

		// More Options
		'disable_part_animation' => array(
			'type' => 'switch',
			'switch_text' => __( 'Disable Part Animation', 'us' ),
			'description' => __( 'When enabled, lines of text will be animated without using the dynamic part.', 'us' ),
			'std' => FALSE,
			'group' => us_translate( 'Appearance' ),
		),
		'html_spaces' => array(
			'type' => 'switch',
			'switch_text' => __( 'Use non-breaking spaces', 'us' ),
			'std' => '1',
			'show_if' => array( 'disable_part_animation', '=', FALSE ),
			'group' => us_translate( 'Appearance' ),
		),
		'dynamic_bold' => array(
			'type' => 'switch',
			'switch_text' => __( 'Make the dynamic part bold', 'us' ),
			'std' => FALSE,
			'show_if' => array( 'disable_part_animation', '=', FALSE ),
			'group' => us_translate( 'Appearance' ),
		),
		'dynamic_color' => array(
			'title' => __( 'Dynamic Part Color', 'us' ),
			'type' => 'color',
			'clear_pos' => 'left',
			'with_gradient' => FALSE,
			'std' => '',
			'show_if' => array( 'disable_part_animation', '=', FALSE ),
			'group' => us_translate( 'Appearance' ),
		),
		'animation_type' => array(
			'title' => __( 'Animation', 'us' ),
			'type' => 'select',
			'options' => array(
				'fadeIn' => __( 'Fade in the whole part', 'us' ),
				'zoomIn' => __( 'Zoom in the whole part', 'us' ),
				'zoomInChars' => __( 'Zoom in character by character', 'us' ),
				'typingChars' => __( 'Typing', 'us' ),
			),
			'std' => 'fadeIn',
			'show_if' => array( 'disable_part_animation', '=', FALSE ),
			'group' => us_translate( 'Appearance' ),
		),
		'duration' => array(
			'title' => __( 'Animation Duration (in seconds)', 'us' ),
			'type' => 'text',
			'std' => '0.3',
			'cols' => 2,
			'group' => us_translate( 'Appearance' ),
		),
		'delay' => array(
			'title' => __( 'Animation Delay (in seconds)', 'us' ),
			'type' => 'text',
			'std' => '5',
			'cols' => 2,
			'group' => us_translate( 'Appearance' ),
		),

	), $design_options ),
);
