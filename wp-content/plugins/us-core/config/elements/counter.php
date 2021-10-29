<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: counter
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Counter', 'us' ),
	'description' => __( 'Animated number with text', 'us' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-stopwatch-20',
	'params' => us_set_params_weight(
		array(
			'initial' => array(
				'title' => __( 'Initial counting value', 'us' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">0</span>, <span class="usof-example">$0</span>, <span class="usof-example">1%</span>, <span class="usof-example">0.001</span>, <span class="usof-example">1kg</span>',
				'type' => 'text',
				'std' => '1',
				'usb_preview' => TRUE,
			),
			'final' => array(
				'title' => __( 'Final counting value', 'us' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">100</span>, <span class="usof-example">$70</span>, <span class="usof-example">98%</span>, <span class="usof-example">0.374</span>, <span class="usof-example">35kg</span>',
				'type' => 'text',
				'std' => '99',
				'holder' => 'div',
				'usb_preview' => TRUE,
			),
			'title' => array(
				'title' => us_translate( 'Title' ),
				'type' => 'text',
				'std' => __( 'Projects completed', 'us' ),
				'holder' => 'div',
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-counter-title',
				),
			),
			'title_size' => array(
				'title' => __( 'Title Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'usb_preview' => array(
					'css' => 'font-size',
					'elm' => '.w-counter-title',
				),
			),
			'title_tag' => array(
				'title' => __( 'Title HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'h6',
				'cols' => 2,
				'usb_preview' => array(
					'attr' => 'tag',
					'elm' => '.w-counter-title',
				),
			),

			// Appearance
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
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'color'
				),
			),
			'custom_color' => array(
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'color', '=', 'custom' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'css' => 'color',
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
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'align',
				),
			),
			'duration' => array(
				'title' => __( 'Animation Duration', 'us' ),
				'type' => 'slider',
				'std' => '2.0s',
				'options' => array(
					's' => array(
						'min' => 1.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),
	'usb_init_js' => '$elm.wCounter(); $us.$window.trigger( \'scroll.waypoints\' )',
);
