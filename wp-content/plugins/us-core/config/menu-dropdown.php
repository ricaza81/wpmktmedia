<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Menu Dropdown settings
 *
 * @filter us_config_menu-dropdown
 */

return array(
	'width' => array(
		'title' => us_translate( 'Width' ),
		'type' => 'radio',
		'options' => array(
			'auto' => us_translate( 'Auto' ),
			'full' => __( 'Full Width', 'us' ),
			'custom' => __( 'Custom', 'us' ),
		),
		'std' => 'auto',
	),
	'custom_width' => array(
		'type' => 'slider',
		'std' => '600px',
		'options' => array(
			'px' => array(
				'min' => 200,
				'max' => 1000,
			),
			'rem' => array(
				'min' => 10,
				'max' => 60,
			),
			'em' => array(
				'min' => 10,
				'max' => 60,
			),
			'vw' => array(
				'min' => 20,
				'max' => 100,
			),
		),
		'classes' => 'for_above',
		'show_if' => array( 'width', '=', 'custom' ),
	),
	'stretch' => array(
		'type' => 'switch',
		'switch_text' => __( 'Stretch background to the screen edges', 'us' ),
		'std' => FALSE,
		'classes' => 'for_above',
		'show_if' => array( 'width', '=', 'full' ),
	),
	'drop_from' => array(
		'title' => __( 'Drop from', 'us' ),
		'type' => 'select',
		'options' => array(
			'menu_item' => __( 'Menu item', 'us' ),
			'header' => _x( 'Header', 'site top area', 'us' ),
		),
		'std' => 'menu_item',
		'cols' => 2,
		'show_if' => array( 'width', '!=', 'full' ),
	),
	'drop_to' => array(
		'title' => __( 'Drop to', 'us' ),
		'type' => 'select',
		'options' => array(
			'left' => us_translate( 'Left' ),
			'center' => us_translate( 'Center' ),
			'right' => us_translate( 'Right' ),
		),
		'std' => 'right',
		'cols' => 2,
		'show_if' => array( 'width', '!=', 'full' ),
	),
	'columns' => array(
		'title' => __( 'Columns for sub-items', 'us' ),
		'type' => 'radio',
		'options' => array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
		),
		'std' => '1',
	),
	'padding' => array(
		'title' => __( 'Inner indents', 'us' ) . ' (padding)',
		'type' => 'slider',
		'std' => '0px',
		'options' => array(
			'px' => array(
				'min' => 0,
				'max' => 50,
			),
			'rem' => array(
				'min' => 0.0,
				'max' => 6.0,
				'step' => 0.1,
			),
			'em' => array(
				'min' => 0.0,
				'max' => 6.0,
				'step' => 0.1,
			),
			'vw' => array(
				'min' => 0,
				'max' => 10,
			),
			'vh' => array(
				'min' => 0,
				'max' => 10,
			),
		),
	),
	'color_bg' => array(
		'title' => __( 'Background Color', 'us' ),
		'type' => 'color',
		'clear_pos' => 'right',
		'std' => '',
		'cols' => 2,
	),
	'color_text' => array(
		'title' => __( 'Text Color', 'us' ),
		'type' => 'color',
		'clear_pos' => 'right',
		'with_gradient' => FALSE,
		'std' => '',
		'cols' => 2,
	),
	'bg_image' => array(
		'title' => __( 'Background Image', 'us' ),
		'type' => 'upload',
	),
	'bg_image_size' => array(
		'title' => __( 'Background Image Size', 'us' ),
		'type' => 'radio',
		'options' => array(
			'cover' => __( 'Fill Area', 'us' ),
			'contain' => __( 'Fit to Area', 'us' ),
			'initial' => __( 'Initial', 'us' ),
		),
		'std' => 'cover',
		'show_if' => array( 'bg_image', '!=', '' ),
	),
	'bg_image_repeat' => array(
		'title' => __( 'Background Image Repeat', 'us' ),
		'type' => 'radio',
		'options' => array(
			'repeat' => __( 'Repeat', 'us' ),
			'repeat-x' => __( 'Horizontally', 'us' ),
			'repeat-y' => __( 'Vertically', 'us' ),
			'no-repeat' => us_translate( 'None' ),
		),
		'std' => 'repeat',
		'show_if' => array( 'bg_image', '!=', '' ),
	),
	'bg_image_position' => array(
		'title' => __( 'Background Image Position', 'us' ),
		'type' => 'radio',
		'labels_as_icons' => 'fas fa-arrow-up',
		'options' => array(
			'top left' => us_translate( 'Top Left' ),
			'top center' => us_translate( 'Top' ),
			'top right' => us_translate( 'Top Right' ),
			'center left' => us_translate( 'Left' ),
			'center center' => us_translate( 'Center' ),
			'center right' => us_translate( 'Right' ),
			'bottom left' => us_translate( 'Bottom Left' ),
			'bottom center' => us_translate( 'Bottom' ),
			'bottom right' => us_translate( 'Bottom Right' ),
		),
		'std' => 'top left',
		'classes' => 'bgpos',
		'show_if' => array( 'bg_image', '!=', '' ),
	),
	'override_settings' => array(
		'title' => __( 'Mobile Menu', 'us' ),
		'type' => 'switch',
		'switch_text' => __( 'Override settings for this menu item', 'us' ),
		'std' => FALSE,
	),
	'mobile_behavior' => array(
		'title' => __( 'Show dropdown by click on', 'us' ),
		'type' => 'radio',
		'options' => array(
			'arrow' => __( 'Arrow', 'us' ),
			'label' => __( 'Label and Arrow', 'us' ),
		),
		'std' => 'arrow',
		'classes' => 'for_above',
		'show_if' => array( 'override_settings', '=', TRUE ),
	),
);
