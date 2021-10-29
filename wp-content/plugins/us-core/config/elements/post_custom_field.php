<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: post_custom_field
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );
$link_custom_values = us_get_elm_link_options();

// Predefined Custom Fields, used in the theme built-in elements
global $us_predefined_post_custom_fields;
$us_predefined_post_custom_fields = array(
	'us_tile_additional_image' => __( 'Custom appearance in Grid', 'us' ) . ': ' . us_translate( 'Images' ),
	'us_tile_icon' => __( 'Custom appearance in Grid', 'us' ) . ': ' . __( 'Icon', 'us' ),
);
if ( us_get_option( 'enable_testimonials', 1 ) ) {
	$us_predefined_post_custom_fields = array_merge(
		$us_predefined_post_custom_fields,
		array(
			'us_testimonial_author' => __( 'Testimonial', 'us' ) . ': ' . __( 'Author Name', 'us' ),
			'us_testimonial_role' => __( 'Testimonial', 'us' ) . ': ' . __( 'Author Role', 'us' ),
			'us_testimonial_company' => __( 'Testimonial', 'us' ) . ': ' . __( 'Author Company', 'us' ),
			'us_testimonial_rating' => __( 'Testimonial', 'us' ) . ': ' . __( 'Rating', 'us' ),
		)
	);
}

// Defined image types for show_if conditions
$image_fields = array( 'us_tile_additional_image' );

// Get options from "Advanced Custom Fields" plugin
$acf_custom_fields = array();
if ( function_exists( 'acf_get_field_groups' ) AND $acf_groups = acf_get_field_groups() ) {
	foreach ( $acf_groups as $group ) {
		$fields = acf_get_fields( $group['ID'] );
		foreach ( $fields as $field ) {

			// Exclude specific ACF types, cause they can be used in Grid only
			if ( ! in_array( $field['type'], array( 'gallery', 'post_object', 'repeater', 'flexible_content' ) ) ) {
				$acf_custom_fields[ $field['name'] ] = $group['title'] . ': ' . $field['label'];
			}

			// Add image types for show_if conditions
			if ( $field['type'] == 'image' ) {
				$image_fields[] = $field['name'];
			}
		}
	}
}

/**
 * @return array
 */
return array(
	'title' => __( 'Post Custom Field', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'icon' => 'fas fa-cog',
	'params' => us_set_params_weight(

	// General section
		array(
			'key' => array(
				'title' => us_translate( 'Show' ),
				'type' => 'select',
				'options' => array_merge(
					$us_predefined_post_custom_fields,
					$acf_custom_fields,
					array( 'custom' => __( 'Custom Field', 'us' ) )
				),
				'std' => 'us_tile_additional_image',
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
			'custom_key' => array(
				'description' => __( 'Enter custom field name to retrieve its value.', 'us' ),
				'type' => 'text',
				'std' => '',
				'admin_label' => TRUE,
				'classes' => 'for_above',
				'show_if' => array( 'key', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'thumbnail_size' => array(
				'title' => __( 'Image Size', 'us' ),
				'description' => $misc['desc_img_sizes'],
				'type' => 'select',
				'options' => us_get_image_sizes_list(),
				'std' => 'large',
				'show_if' => array( 'key', '=', $image_fields ),
				'usb_preview' => TRUE,
			),
			'has_ratio' => array(
				'switch_text' => __( 'Set Aspect Ratio', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'show_if' => array( 'key', '=', $image_fields ),
				'usb_preview' => TRUE,
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
				'show_if' => array( 'has_ratio', '!=', FALSE ), // TODO: make to work multiple conditions
				'usb_preview' => TRUE,
			),
			'ratio_width' => array(
				'placeholder' => us_translate( 'Width' ),
				'type' => 'text',
				'std' => '21',
				'cols' => 2,
				'classes' => 'for_above',
				'show_if' => array( 'ratio', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'ratio_height' => array(
				'placeholder' => us_translate( 'Height' ),
				'type' => 'text',
				'std' => '9',
				'cols' => 2,
				'classes' => 'for_above',
				'show_if' => array( 'ratio', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'stretch' => array(
				'type' => 'switch',
				'switch_text' => __( 'Stretch the image to the container width', 'us' ),
				'std' => FALSE,
				'show_if' => array( 'has_ratio', '=', FALSE ),
				'usb_preview' => array(
					'toggle_class' => 'stretched',
				),
			),
			'hide_empty' => array(
				'type' => 'switch',
				'switch_text' => __( 'Hide this element if its value is empty', 'us' ),
				'std' => FALSE,
				'show_if' => array( 'key', '!=', 'us_testimonial_rating' ),
			),
			'link' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'select',
				'options' => array_merge(
					array(
						'none' => us_translate( 'None' ),
						'post' => __( 'To a Post', 'us' ),
						'popup_post_image' => __( 'Open original image in a popup', 'us' ),
						'elm_value' => __( 'Use the element value as link', 'us' ),
						'onclick' => __( 'Onclick JavaScript action', 'us' ),
					),
					$link_custom_values,
					array( 'custom' => __( 'Custom', 'us' ) )
				),
				'std' => 'none',
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
			'link_new_tab' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Open link in a new tab' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'link', '!=', array( 'none', 'popup_post_image', 'onclick', 'custom' ) ),
				'usb_preview' => TRUE,
			),
			'onclick_code' => array(
				'type' => 'text',
				'std' => 'return false',
				'classes' => 'for_above',
				'show_if' => array( 'link', '=', 'onclick' ),
				'usb_preview' => TRUE,
			),
			'custom_link' => array(
				'placeholder' => us_translate( 'Enter the URL' ),
				'description' => $misc['desc_grid_custom_link'],
				'type' => 'link',
				'std' => array(),
				'shortcode_std' => '',
				'classes' => 'for_above desc_3',
				'show_if' => array( 'link', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'color_link' => array(
				'title' => __( 'Link Color', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Inherit from text color', 'us' ),
				'std' => TRUE,
				'show_if' => array( 'link', '!=', array( 'none', 'popup_post_image' ) ),
				'usb_preview' => array(
					'toggle_class' => 'color_link_inherit',
				),
			),
			'tag' => array(
				'title' => __( 'HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'div',
				'show_if' => array( 'key', '!=', $image_fields ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'attr' => 'tag',
				),
			),
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'show_if' => array( 'key', '!=', array( 'us_testimonial_rating', 'us_tile_icon' ) ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'text_before' => array(
				'title' => __( 'Text before value', 'us' ),
				'type' => 'text',
				'std' => '',
				'admin_label' => TRUE,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'text_before_tag' => array(
				'description' => __( 'HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'span',
				'classes' => 'for_above',
				'show_if' => array( 'text_before', '!=', '' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-post-elm-before',
					'attr' => 'tag',
				),
			),
			'text_after' => array(
				'title' => __( 'Text after value', 'us' ),
				'type' => 'text',
				'std' => '',
				'admin_label' => TRUE,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'text_after_tag' => array(
				'description' => __( 'HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'span',
				'classes' => 'for_above',
				'show_if' => array( 'text_after', '!=', '' ),
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-post-elm-after',
					'attr' => 'tag',
				),
			),
		),

		$design_options_params,
		$hover_options_params
	),
	'usb_preview_dummy_data' => array(
		'us_tile_icon' => 'fas|star',
		'us_testimonial_author' => 'John Doe',
		'us_testimonial_role' => 'CEO',
		'us_testimonial_company' => 'UpSolution',
		'us_testimonial_rating' => '4',
	),
);
