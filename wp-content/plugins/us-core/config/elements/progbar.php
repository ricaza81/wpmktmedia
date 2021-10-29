<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: progbar
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Progress Bar', 'us' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-tasks',
	'params' => us_set_params_weight(
		array(
			'count' => array(
				'title' => __( 'Progress Value', 'us' ),
				'type' => 'text',
				'std' => '50%',
				'holder' => 'span',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'final_value' => array(
				'title' => __( 'Final Value', 'us' ),
				'type' => 'text',
				'std' => '100%',
				'holder' => 'span',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'hide_count' => array(
				'type' => 'switch',
				'switch_text' => __( 'Hide progress value counter', 'us' ),
				'std' => FALSE,
				'cols' => 2,
				'classes' => 'for_above',
				'usb_preview' => TRUE,
			),
			'hide_final_value' => array(
				'type' => 'switch',
				'switch_text' => __( 'Hide final value', 'us' ),
				'std' => '1',
				'classes' => 'for_above',
				'cols' => 2,
				'show_if' => array( 'hide_count', '=', FALSE ),
				'usb_preview' => TRUE,
			),
			'title' => array(
				'title' => us_translate( 'Title' ),
				'type' => 'text',
				'std' => 'This is Progress Bar',
				'holder' => 'div',
				'usb_preview' => array(
					'attr' => 'text',
					'elm' => '.w-progbar-title-text',
				),
			),
			'title_size' => array(
				'title' => __( 'Title Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '1rem',
				'cols' => 2,
				'show_if' => array( 'title', '!=', '' ),
				'usb_preview' => array(
					'css' => 'font-size',
					'elm' => '.w-progbar-title',
				),
			),
			'title_tag' => array(
				'title' => __( 'Title HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'h6',
				'cols' => 2,
				'show_if' => array( 'title', '!=', '' ),
				'usb_preview' => array(
					'attr' => 'tag',
					'elm' => '.w-progbar-title',
				),
			),

			'style' => array(
				'title' => us_translate( 'Style' ),
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'std' => '1',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'style',
				),
			),
			'color' => array(
				'title' => __( 'Progress Bar Color', 'us' ),
				'type' => 'select',
				'options' => array(
					'primary' => __( 'Primary (theme color)', 'us' ),
					'secondary' => __( 'Secondary (theme color)', 'us' ),
					'heading' => __( 'Heading (theme color)', 'us' ),
					'text' => __( 'Text (theme color)', 'us' ),
					'custom' => us_translate( 'Custom color' ),
				),
				'std' => 'primary',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'color',
				),
			),
			'bar_color' => array(
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'color', '=', 'custom' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'css' => 'background',
					'elm' => '.w-progbar-bar-h',
				),
			),
			'size' => array(
				'title' => __( 'Progress Bar Height', 'us' ),
				'type' => 'slider',
				'std' => '10px',
				'options' => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
					'rem' => array(
						'min' => 0.1,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => 0.1,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'css' => 'height',
					'elm' => '.w-progbar-bar-h',
				),
			),
		),

		$design_options_params
	),
	'usb_init_js' => '$elm.wProgbar(); $us.$window.trigger( \'scroll.waypoints\' )',
);
