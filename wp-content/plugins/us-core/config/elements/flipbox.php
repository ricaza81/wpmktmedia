<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: flipbox
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'FlipBox', 'us' ),
	'description' => __( 'Two-sided content element, flipping on hover', 'us' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-cube',
	'params' => us_set_params_weight(

		// Front Side
		array(
			'front_title' => array(
				'title' => us_translate( 'Title' ),
				'type' => 'text',
				'std' => __( 'Front Side', 'us' ),
				'holder' => 'div',
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-flipbox-front-title',
				),
			),
			'front_title_size' => array(
				'title' => __( 'Title Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'front_title', '!=', '' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => array(
					'css' => 'font-size',
					'elm' => '.w-flipbox-front-title',
				),
			),
			'front_title_tag' => array(
				'title' => __( 'Title HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'h4',
				'cols' => 2,
				'show_if' => array( 'front_title', '!=', '' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => array(
					'attr' => 'tag',
					'elm' => '.w-flipbox-front-title'
				),
			),
			'front_desc' => array(
				'title' => us_translate( 'Description' ),
				'type' => 'textarea',
				'std' => '',
				'holder' => 'div',
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'front_bgcolor' => array(
				'title' => __( 'Background Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => array(
					'css' => 'background',
					'elm' => '.w-flipbox-front',
				),
			),
			'front_textcolor' => array(
				'title' => __( 'Text Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'cols' => 2,
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => array(
					'css' => 'color',
					'elm' => '.w-flipbox-front',
				),
			),
			'front_bgimage' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'cols' => 2,
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'front_bgimage_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'description' => $misc['desc_img_sizes'],
				'type' => 'select',
				'options' => us_get_image_sizes_list(),
				'std' => 'large',
				'cols' => 2,
				'show_if' => array( 'front_bgimage', '!=', '' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'front_icon_type' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'radio',
				'options' => array(
					'none' => us_translate( 'None' ),
					'font' => __( 'Icon', 'us' ),
					'image' => __( 'Custom', 'us' ),
				),
				'std' => 'none',
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'front_icon_name' => array(
				'type' => 'icon',
				'show_if' => array( 'front_icon_type', '=', 'font' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'front_icon_size' => array(
				'title' => __( 'Icon Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '2rem',
				'cols' => 2,
				'show_if' => array( 'front_icon_type', '=', 'font' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-flipbox-front-icon',
					'css' => 'font-size',
				),
			),
			'front_icon_style' => array(
				'title' => __( 'Icon Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Simple', 'us' ),
					'circle' => __( 'Inside the Solid circle', 'us' ),
				),
				'std' => 'default',
				'cols' => 2,
				'show_if' => array( 'front_icon_type', '=', 'font' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-flipbox-front-icon',
					'mod' => 'style'
				),
			),
			'front_icon_color' => array(
				'title' => __( 'Icon Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'front_icon_type', '=', 'font' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-flipbox-front-icon',
					'css' => 'color',
				),
			),
			'front_icon_bgcolor' => array(
				'title' => __( 'Icon Circle Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'front_icon_type', '=', 'font' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-flipbox-front-icon',
					'css' => 'background',
				),
			),
			'front_icon_image' => array(
				'title' => us_translate( 'Image' ),
				'type' => 'upload',
				'cols' => 2,
				'show_if' => array( 'front_icon_type', '=', 'image' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'front_icon_image_width' => array(
				'title' => us_translate( 'Width' ),
				'description' => $misc['desc_width'],
				'type' => 'text',
				'std' => '4rem',
				'cols' => 2,
				'show_if' => array( 'front_icon_type', '=', 'image' ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'front_icon_pos' => array(
				'title' => __( 'Icon Position', 'us' ),
				'type' => 'select',
				'options' => array(
					'above_title' => __( 'Above Title', 'us' ),
					'below_title' => __( 'Below Title', 'us' ),
					'below_desc' => __( 'Below Description', 'us' ),
				),
				'std' => 'above_title',
				'show_if' => array( 'front_icon_type', '=', array( 'font', 'image' ) ),
				'group' => __( 'Front Side', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		// Back Side
		array(
			'back_title' => array(
				'title' => us_translate( 'Title' ),
				'type' => 'text',
				'std' => __( 'Back Side', 'us' ),
				'holder' => 'div',
				'group' => __( 'Back Side', 'us' ),
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-flipbox-back-title',
				),
			),
			'back_title_size' => array(
				'title' => __( 'Title Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'back_title', '!=', '' ),
				'group' => __( 'Back Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'back_title_tag' => array(
				'title' => __( 'Title HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'h4',
				'cols' => 2,
				'show_if' => array( 'back_title', '!=', '' ),
				'group' => __( 'Back Side', 'us' ),
				'usb_preview' => array(
					'attr' => 'tag',
					'elm' => '.w-flipbox-back-title',
				),
			),
			'back_desc' => array(
				'title' => us_translate( 'Description' ),
				'type' => 'textarea',
				'std' => '',
				'group' => __( 'Back Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'back_bgcolor' => array(
				'title' => __( 'Background Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Back Side', 'us' ),
				'usb_preview' => array(
					'css' => 'background',
					'elm' => '.w-flipbox-back',
				),
			),
			'back_textcolor' => array(
				'title' => __( 'Text Color', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'cols' => 2,
				'group' => __( 'Back Side', 'us' ),
				'usb_preview' => array(
					'css' => 'color',
					'elm' => '.w-flipbox-back',
				),
			),
			'back_bgimage' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'cols' => 2,
				'group' => __( 'Back Side', 'us' ),
				'usb_preview' => TRUE,
			),
			'back_bgimage_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'description' => $misc['desc_img_sizes'],
				'type' => 'select',
				'options' => us_get_image_sizes_list(),
				'std' => 'large',
				'cols' => 2,
				'show_if' => array( 'back_bgimage', '!=', '' ),
				'group' => __( 'Back Side', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		// Link
		array(
			'link_type' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'container' => __( 'The whole element', 'us' ),
					'btn' => __( 'Button on the Back Side', 'us' ),
				),
				'std' => 'none',
				'group' => us_translate( 'Link' ),
				'usb_preview' => TRUE,
			),
			'link' => array(
				'type' => 'link',
				'std' => '',
				'show_if' => array( 'link_type', '=', array( 'container', 'btn' ) ),
				'group' => us_translate( 'Link' ),
				'usb_preview' => TRUE,
			),
			'btn_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => __( 'Click Me', 'us' ),
				'cols' => 2,
				'show_if' => array( 'link_type', '=', 'btn' ),
				'group' => us_translate( 'Link' ),
				'usb_preview' => TRUE,
			),
			'btn_size' => array(
				'title' => __( 'Button Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'show_if' => array( 'link_type', '=', 'btn' ),
				'group' => us_translate( 'Link' ),
				'usb_preview' => TRUE,
			),
			'btn_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => us_get_btn_styles(),
				'std' => '1',
				'show_if' => array( 'link_type', '=', 'btn' ),
				'group' => us_translate( 'Link' ),
				'usb_preview' => TRUE,
			),
		),

		// Appearance
		array(
			'animation' => array(
				'title' => __( 'Animation Type', 'us' ),
				'type' => 'select',
				'options' => array(
					'cardflip' => __( 'Card Flip', 'us' ),
					'cubetilt' => __( 'Cube Tilt', 'us' ),
					'cubeflip' => __( 'Cube Flip', 'us' ),
					'coveropen' => __( 'Cover Open', 'us' ),
				),
				'std' => 'cardflip',
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'direction' => array(
				'title' => __( 'Animation Direction', 'us' ),
				'type' => 'select',
				'options' => array(
					'n' => us_translate( 'Top' ),
					'e' => us_translate( 'Right' ),
					's' => us_translate( 'Bottom' ),
					'w' => us_translate( 'Left' ),
					'ne' => us_translate( 'Top Right' ),
					'se' => us_translate( 'Bottom Right' ),
					'sw' => us_translate( 'Bottom Left' ),
					'nw' => us_translate( 'Top Left' ),
				),
				'std' => 'w',
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'duration' => array(
				'title' => __( 'Animation Duration', 'us' ),
				'type' => 'slider',
				'std' => '0.5s',
				'options' => array(
					's' => array(
						'min' => 0.0,
						'max' => 2.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-flipbox-h',
					'css' => 'transition-duration',
				),
			),
			'easing' => array(
				'title' => __( 'Animation Easing', 'us' ),
				'type' => 'select',
				'options' => array(
					'ease' => 'ease',
					'easeInOutExpo' => 'easeInOutExpo',
					'easeInOutCirc' => 'easeInOutCirc',
				),
				'std' => 'ease',
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-flipbox-h',
					'mod' => 'easing',
				),
			),
		),

		$design_options_params
	),

	'usb_init_js' => '$elm.wFlipBox()',
);
