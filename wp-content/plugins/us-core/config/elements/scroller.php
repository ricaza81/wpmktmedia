<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: scroller
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Page Scroller', 'us' ),
	'description' => __( 'Accurate scroll to page sections', 'us' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-mouse',
	'params' => us_set_params_weight(

		// General section
		array(
			'disable_width' => array(
				'title' => __( 'Disable scrolling at width', 'us' ),
				'description' => __( 'When screen width is less than this value, scrolling by rows will be disabled.', 'us' ),
				'type' => 'text',
				'std' => '768px',
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
			'speed' => array(
				'title' => __( 'Scroll Speed', 'us' ),
				'type' => 'slider',
				'std' => '1000ms',
				'options' => array(
					'ms' => array(
						'min' => 0,
						'max' => 2000,
						'step' => 100,
					),
				),
				'usb_preview' => TRUE,
			),
			'dots' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show Navigation Dots', 'us' ),
				'std' => FALSE,
				'usb_preview' => TRUE,
			),
			'dots_style' => array(
				'title' => __( 'Dots Style', 'us' ),
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'std' => '1',
				'cols' => 2,
				'show_if' => array( 'dots', '!=', FALSE ),
				'usb_preview' => array(
					'mod' => 'style',
				),
			),
			'dots_pos' => array(
				'title' => __( 'Dots Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'right',
				'cols' => 2,
				'show_if' => array( 'dots', '!=', FALSE ),
				'usb_preview' => array(
					'mod' => 'pos',
				),
			),
			'dots_size' => array(
				'title' => __( 'Dots Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'slider',
				'std' => '10px',
				'options' => array(
					'px' => array(
						'min' => 5,
						'max' => 30,
					),
					'rem' => array(
						'min' => 0.5,
						'max' => 2.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'show_if' => array( 'dots', '!=', FALSE ),
				'usb_preview' => array(
					'elm' => '.w-scroller-dots',
					'css' => 'font-size',
				),
			),
			'dots_color' => array(
				'title' => __( 'Dots Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'dots', '!=', FALSE ),
				'usb_preview' => array(
					'elm' => '.w-scroller-dots',
					'css' => 'color',
				),
			),
			'include_footer' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show dots for Footer', 'us' ),
				'std' => FALSE,
				'show_if' => array( 'dots', '!=', FALSE ),
				'usb_preview' => TRUE,
			),
		),

		$design_options_params
	),
	'usb_init_js' => 'jQuery( $elm ).usPageScroller()',
);
