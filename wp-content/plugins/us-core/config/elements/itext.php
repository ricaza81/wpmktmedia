<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: itext
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Interactive Text', 'us' ),
	'description' => __( 'Text with dynamically changing part', 'us' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-italic',
	'params' => us_set_params_weight(

		// General section
		array(
			'texts' => array(
				'title' => __( 'Text States', 'us' ),
				'description' => __( 'Each value on a new line', 'us' ),
				'type' => 'textarea',
				'std' => 'We create great design' . "\n" . 'We create great websites' . "\n" . 'We create great code',
				'holder' => 'div',
				'usb_preview' => TRUE,
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
				'cols' => 2,
				'usb_preview' => array(
					'mod' => 'align',
				),
			),
			'tag' => array(
				'title' => __( 'HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'h2',
				'cols' => 2,
				// Animation strongly attached to the markup,
				// so we will re-render the tag change
				'usb_preview' => TRUE,
			),
		),

		// Appearance
		array(
			'disable_part_animation' => array(
				'type' => 'switch',
				'switch_text' => __( 'Disable Part Animation', 'us' ),
				'description' => __( 'When enabled, lines of text will be animated without using the dynamic part.', 'us' ),
				'std' => FALSE,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'html_spaces' => array(
				'type' => 'switch',
				'switch_text' => __( 'Use non-breaking spaces', 'us' ),
				'std' => '1',
				'show_if' => array( 'disable_part_animation', '=', FALSE ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'dynamic_bold' => array(
				'type' => 'switch',
				'switch_text' => __( 'Make the dynamic part bold', 'us' ),
				'std' => FALSE,
				'show_if' => array( 'disable_part_animation', '=', FALSE ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'toggle_class' => 'dynamic_bold',
				),
			),
			'dynamic_color' => array(
				'title' => __( 'Dynamic Part Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'show_if' => array( 'disable_part_animation', '=', FALSE ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
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
				'usb_preview' => TRUE,
			),
			'duration' => array(
				'title' => __( 'Animation Duration', 'us' ),
				'type' => 'slider',
				'std' => '0.3s',
				'options' => array(
					's' => array(
						'min' => 0.05,
						'max' => 1.00,
						'step' => 0.05,
					),
				),
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'delay' => array(
				'title' => __( 'Animation Delay', 'us' ),
				'type' => 'slider',
				'std' => '5s',
				'options' => array(
					's' => array(
						'min' => 1.0,
						'max' => 9.0,
						'step' => 0.5,
					),
				),
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),

	'usb_init_js' => '$elm.wItext()',
);
