<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Grid Layout and Elements Options.
 * Options and elements' fields are described in USOF-style format.
 */

$elements = array(
	'post_image',
	'post_title',
	'post_date',
	'post_taxonomy',
	'post_author',
	'post_comments',
	'post_content',
	'post_custom_field',
	'btn',
	'html',
	'hwrapper',
	'vwrapper',
	'text',
	'image',
);
if ( class_exists( 'Post_Views_Counter' ) ) {
	$elements[] = 'post_views';
}
if ( class_exists( 'woocommerce' ) ) {
	$elements[] = 'product_field';
	$elements[] = 'add_to_cart';
}

// Set image sources for selection
$bg_img_sources = array(
	'none' => us_translate( 'None' ),
	'media' => __( 'Custom', 'us' ),
	'featured' => us_translate( 'Featured Image' ),
	'us_tile_additional_image' => __( 'Custom appearance in Grid', 'us' ) . ': ' . us_translate( 'Images' ),
);

// Add image types from ACF
if ( function_exists( 'acf_get_field_groups' ) AND $acf_groups = acf_get_field_groups() ) {
	foreach ( $acf_groups as $group ) {
		$fields = acf_get_fields( $group['ID'] );
		foreach ( $fields as $field ) {
			if ( $field['type'] == 'image' ) {
				$bg_img_sources[ $field['name'] ] = $group['title'] . ': ' . $field['label'];
			}
		}
	}
}

return array(

	// Supported elements
	'elements' => $elements,

	// General Options
	'options' => array(
		'global' => array(
			'fixed' => array(
				'switch_text' => __( 'Set Aspect Ratio', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
			),
			'ratio' => array(
				'type' => 'select',
				'options' => array(
					'1x1' => '1x1 ' . __( 'square', 'us' ),
					'4x3' => '4x3 ' . __( 'landscape', 'us' ),
					'3x2' => '3x2 ' . __( 'landscape', 'us' ),
					'16x9' => '16:9 ' . __( 'landscape', 'us' ),
					'2x3' => '2x3 ' . __( 'portrait', 'us' ),
					'3x4' => '3x4 ' . __( 'portrait', 'us' ),
					'custom' => __( 'Custom', 'us' ),
				),
				'std' => '1x1',
				'classes' => 'for_above',
				'show_if' => array( 'fixed', '=', TRUE ),
			),
			'ratio_width' => array(
				'placeholder' => us_translate( 'Width' ),
				'type' => 'text',
				'std' => '21',
				'show_if' => array(
					array( 'fixed', '=', TRUE ),
					'and',
					array( 'ratio', '=', 'custom' ),
				),
			),
			'ratio_height' => array(
				'placeholder' => us_translate( 'Height' ),
				'type' => 'text',
				'std' => '9',
				'show_if' => array(
					array( 'fixed', '=', TRUE ),
					'and',
					array( 'ratio', '=', 'custom' ),
				),
			),
			'overflow' => array(
				'switch_text' => __( 'Hide Overflowing Content', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'show_if' => array( 'fixed', '=', FALSE ),
			),
			'color_bg' => array(
				'title' => __( 'Background Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'left',
				'std' => '',
				'classes' => 'clear_right',
			),
			'color_text' => array(
				'title' => __( 'Text Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'left',
				'with_gradient' => FALSE,
				'std' => '',
				'classes' => 'clear_right',
			),

			// Background
			'bg_img_source' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'select',
				'options' => $bg_img_sources,
				'std' => 'none',
			),
			'bg_img' => array(
				'type' => 'upload',
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'bg_img_source', '=', 'media' ),
			),
			'bg_img_wrapper_start' => array(
				'type' => 'wrapper_start',
				'show_if' => array( 'bg_img_source', '!=', 'none' ),
			),
			'bg_img_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
				),
				'std' => 'cover',
			),
			'bg_img_position' => array(
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
				'std' => 'center',
				'classes' => 'bgpos',
			),
			'bg_img_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'select',
				'options' => array(
					'no-repeat' => us_translate( 'None' ),
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
				),
				'std' => 'no-repeat',
			),
			'bg_img_wrapper_end' => array(
				'type' => 'wrapper_end',
			),

			'border_radius' => array(
				'title' => __( 'Border Radius', 'us' ),
				'type' => 'slider',
				'std' => '0',
				'options' => array(
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
			),
			'box_shadow' => array(
				'title' => __( 'Shadow', 'us' ),
				'type' => 'slider',
				'std' => '0',
				'options' => array(
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
			),
			'box_shadow_hover' => array(
				'title' => __( 'Shadow on hover', 'us' ),
				'type' => 'slider',
				'std' => '0',
				'options' => array(
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
			),
		),
	),

);
