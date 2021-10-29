<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: image
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );
$link_custom_values = us_get_elm_link_options();

/**
 * @return array
 */
return array(
	'title' => us_translate( 'Image' ),
	'category' => __( 'Basic', 'us' ),
	'icon' => 'fas fa-image',
	'params' => us_set_params_weight(

		// General section
		array(
			'img' => array(
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'context' => array( 'header', 'grid' ),
				'usb_preview' => TRUE,
			),
			'image' => array(
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'shortcode_cols' => 2,
				'context' => array( 'shortcode' ),
				'usb_preview' => TRUE,
			),
			'size' => array(
				'title' => __( 'Image Size', 'us' ),
				'description' => $misc['desc_img_sizes'],
				'type' => 'select',
				'options' => us_get_image_sizes_list(),
				'std' => 'large',
				'cols' => 2,
				'admin_label' => TRUE,
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
				'std' => 'none',
				'cols' => 2,
				'context' => array( 'shortcode' ),
				'usb_preview' => array(
					'mod' => 'align',
				),
			),
			'style' => array(
				'title' => __( 'Image Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => us_translate( 'None' ),
					'circle' => __( 'Circle', 'us' ),
					'outlined' => __( 'Outlined', 'us' ),
					'shadow-1' => __( 'Simple Shadow', 'us' ),
					'shadow-2' => __( 'Colored Shadow', 'us' ),
					'phone12' => __( 'Phone 12 Flat', 'us' ),
					'phone6-1' => __( 'Phone 6 Black Realistic', 'us' ),
					'phone6-2' => __( 'Phone 6 White Realistic', 'us' ),
					'phone6-3' => __( 'Phone 6 Black Flat', 'us' ),
					'phone6-4' => __( 'Phone 6 White Flat', 'us' ),
				),
				'std' => '',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'has_ratio' => array(
				'switch_text' => __( 'Set Aspect Ratio', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'show_if' => array( 'style', '=', array( '', 'circle', 'outlined', 'shadow-1', 'shadow-2' ) ),
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
				'show_if' => array( 'has_ratio', '!=', FALSE ),
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
			'meta' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show image title and description', 'us' ),
				'std' => FALSE,
				'context' => array( 'shortcode' ),
				'usb_preview' => TRUE,
			),
			'meta_style' => array(
				'title' => __( 'Title and Description Style', 'us' ),
				'type' => 'radio',
				'options' => array(
					'simple' => __( 'Below the image', 'us' ),
					'modern' => __( 'Over the image', 'us' ),
				),
				'std' => 'simple',
				'show_if' => array( 'meta', '!=', FALSE ),
				'context' => array( 'shortcode' ),
				'usb_preview' => array(
					'mod' => 'meta',
				),
			),
			'onclick' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'select',
				'options' => array_merge(
					array(
						'none' => us_translate( 'None' ),
						'lightbox' => __( 'Open original image in a popup', 'us' ),
						'onclick' => __( 'Onclick JavaScript action', 'us' ),
					),
					$link_custom_values,
					array( 'custom_link' => __( 'Custom', 'us' ) )
				),
				'std' => 'none',
				'usb_preview' => TRUE,
			),
			'link' => array(
				'placeholder' => us_translate( 'Enter the URL' ),
				'description' => $misc['desc_grid_custom_link'],
				'type' => 'link',
				'std' => array(),
				'shortcode_std' => '',
				'classes' => 'for_above desc_3',
				'show_if' => array( 'onclick', '=', 'custom_link' ),
				'usb_preview' => TRUE,
			),
			'link_new_tab' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Open link in a new tab' ),
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'onclick', '=', array_keys( $link_custom_values ) ),
				'usb_preview' => array(
					'toggle_atts' => array(
						'target' => '_blank',
						'rel' => 'noopener nofollow',
					),
					'elm' => 'a.w-image-h',
				),
			),
			'onclick_code' => array(
				'type' => 'text',
				'std' => 'return false',
				'classes' => 'for_above',
				'show_if' => array( 'onclick', '=', 'onclick' ),
				'usb_preview' => array(
					'attr' => 'onclick',
					'elm' => 'a.w-image-h',
				),
			),
			'img_transparent' => array(
				'title' => __( 'Different Image for Transparent Header', 'us' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'context' => array( 'header' ),
				'usb_preview' => TRUE,
			),
		),

		// Height in Header only section
		array(
			'heading_1' => array(
				'title' => us_translate( 'Default' ),
				'type' => 'heading',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
			'height_default' => array(
				'title' => __( 'Desktops', 'us' ),
				'type' => 'text',
				'std' => '35px',
				'cols' => 4,
				'classes' => 'for_above',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
			'height_laptops' => array(
				'title' => __( 'Laptops', 'us' ),
				'type' => 'text',
				'std' => '30px',
				'cols' => 4,
				'classes' => 'for_above',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
			'height_tablets' => array(
				'title' => __( 'Tablets', 'us' ),
				'type' => 'text',
				'std' => '25px',
				'cols' => 4,
				'classes' => 'for_above',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
			'height_mobiles' => array(
				'title' => __( 'Mobiles', 'us' ),
				'type' => 'text',
				'std' => '20px',
				'cols' => 4,
				'classes' => 'for_above',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
			'heading_2' => array(
				'title' => __( 'Sticky Header', 'us' ),
				'type' => 'heading',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
			'height_sticky' => array(
				'title' => __( 'Desktops', 'us' ),
				'type' => 'text',
				'std' => '35px',
				'cols' => 4,
				'classes' => 'for_above',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
			'height_sticky_laptops' => array(
				'title' => __( 'Laptops', 'us' ),
				'type' => 'text',
				'std' => '30px',
				'cols' => 4,
				'classes' => 'for_above',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
			'height_sticky_tablets' => array(
				'title' => __( 'Tablets', 'us' ),
				'type' => 'text',
				'std' => '25px',
				'cols' => 4,
				'classes' => 'for_above',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
			'height_sticky_mobiles' => array(
				'title' => __( 'Mobiles', 'us' ),
				'type' => 'text',
				'std' => '20px',
				'cols' => 4,
				'classes' => 'for_above',
				'group' => us_translate( 'Height' ),
				'context' => array( 'header' ),
			),
		),

		$design_options_params,
		$hover_options_params
	),

	// Not used params, required for correct fallback
	'fallback_params' => array(
		'animate',
		'animate_delay',
	),

	'usb_init_js' => 'jQuery( $elm ).wPopupLink()',
);
