<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Common Design options
 */

$misc = us_config( 'elements_misc' );

// Generate options for "Hide on states"
$hide_on_states_options = array();
foreach ( us_get_responsive_states() as $state => $data ) {
	$hide_on_states_options[ $state ] = $data['title'];
}

return array(

	// Design settings based on CSS properties
	'css' => array(
		'type' => 'design_options',
		'group' => __( 'Design', 'us' ),

		// DEV: property keys for css MUST be written with a hyphen. Example: font-size and not font_size
		'params' => array(

			// Text
			'color' => array(
				'title' => us_translate( 'Color' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'cols' => 2,
				'group' => us_translate( 'Text' ),
			),
			// Note: When using responsive design, the default value will be `inherit`
			// for the possibility of canceling other values.
			'text-align' => array(
				'title' => us_translate( 'Alignment' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-align-*',
				'options' => array(
					'inherit' => us_translate( 'Default' ),
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
					'justify' => us_translate( 'Justify' ),
				),
				'std' => 'inherit',
				'cols' => 2,
				'group' => us_translate( 'Text' ),
			),
			'font-family' => array(
				'title' => __( 'Font', 'us' ),
				'type' => 'select',
				'options' => us_get_fonts(),
				'std' => '',
				'group' => us_translate( 'Text' )
			),
			'font-weight' => array(
				'title' => __( 'Font Weight', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => us_translate( 'Default' ),
					'100' => '100 ' . __( 'thin', 'us' ),
					'200' => '200 ' . __( 'extra-light', 'us' ),
					'300' => '300 ' . __( 'light', 'us' ),
					'400' => '400 ' . __( 'normal', 'us' ),
					'500' => '500 ' . __( 'medium', 'us' ),
					'600' => '600 ' . __( 'semi-bold', 'us' ),
					'700' => '700 ' . __( 'bold', 'us' ),
					'800' => '800 ' . __( 'extra-bold', 'us' ),
					'900' => '900 ' . __( 'ultra-bold', 'us' ),
				),
				'std' => '',
				'cols' => 3,
				'group' => us_translate( 'Text' ),
			),
			'text-transform' => array(
				'title' => __( 'Text Transform', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => us_translate( 'Default' ),
					'none' => us_translate( 'None' ),
					'uppercase' => 'UPPERCASE',
					'lowercase' => 'lowercase',
					'capitalize' => 'Capitalize',
				),
				'std' => '',
				'cols' => 3,
				'group' => us_translate( 'Text' ),
			),
			'font-style' => array(
				'title' => __( 'Font Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => us_translate( 'Default' ),
					'normal' => __( 'normal', 'us' ),
					'italic' => __( 'italic', 'us' ),
				),
				'std' => '',
				'cols' => 3,
				'group' => us_translate( 'Text' ),
			),
			'font-size' => array(
				'title' => __( 'Font Size', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 3,
				'group' => us_translate( 'Text' ),
			),
			'line-height' => array(
				'title' => __( 'Line height', 'us' ),
				'description' => $misc['desc_line_height'],
				'type' => 'text',
				'std' => '',
				'cols' => 3,
				'group' => us_translate( 'Text' ),
			),
			'letter-spacing' => array(
				'title' => __( 'Letter Spacing', 'us' ),
				'description' => $misc['desc_letter_spacing'],
				'type' => 'text',
				'std' => '',
				'cols' => 3,
				'group' => us_translate( 'Text' ),
			),

			// Background
			'background-color' => array(
				'title' => __( 'Background Сolor', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'std' => '',
				'group' => __( 'Background', 'us' ),
			),
			'background-image' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'std' => '',
				'group' => __( 'Background', 'us' ),
			),
			'background-position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'description' => $misc['desc_bg_pos'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Background', 'us' ),
				'show_if' => array( 'background-image', '!=', '' ),
			),
			'background-size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'text',
				'description' => $misc['desc_bg_size'],
				'std' => 'auto',
				'cols' => 2,
				'group' => __( 'Background', 'us' ),
				'show_if' => array( 'background-image', '!=', '' ),
			),
			'background-repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'select',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
				'cols' => 2,
				'group' => __( 'Background', 'us' ),
				'show_if' => array( 'background-image', '!=', '' ),
			),
			'background-attachment' => array(
				'title' => __( 'Background Image Attachment', 'us' ),
				'type' => 'radio',
				'options' => array(
					'scroll' => 'scroll',
					'fixed' => 'fixed',
				),
				'std' => 'scroll',
				'cols' => 2,
				'group' => __( 'Background', 'us' ),
				'show_if' => array( 'background-image', '!=', '' ),
			),

			// Sizes
			'width' => array(
				'title' => us_translate( 'Width' ),
				'description' => $misc['desc_width'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Sizes', 'us' ),
			),
			'height' => array(
				'title' => us_translate( 'Height' ),
				'description' => $misc['desc_height'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Sizes', 'us' ),
			),
			'max-width' => array(
				'title' => us_translate( 'Max Width' ),
				'description' => $misc['desc_width'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Sizes', 'us' ),
			),
			'max-height' => array(
				'title' => us_translate( 'Max Height' ),
				'description' => $misc['desc_height'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Sizes', 'us' ),
			),
			'min-width' => array(
				'title' => __( 'Min Width', 'us' ),
				'description' => $misc['desc_width'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Sizes', 'us' ),
			),
			'min-height' => array(
				'title' => __( 'Min Height', 'us' ),
				'description' => $misc['desc_height'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Sizes', 'us' ),
			),

			// Spacing
			'margin-left' => array(
				'title' => 'Margin',
				'description' => us_translate( 'Left' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Spacing', 'us' ),
				'html-data' => array( 'relations' => array( 'margin-top', 'margin-right', 'margin-bottom' ) ),
			),
			'margin-top' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Top' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Spacing', 'us' ),
			),
			'margin-bottom' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Bottom' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Spacing', 'us' ),
			),
			'margin-right' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Right' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Spacing', 'us' ),
			),
			'padding-left' => array(
				'title' => 'Padding',
				'description' => us_translate( 'Left' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Spacing', 'us' ),
				'html-data' => array( 'relations' => array( 'padding-top', 'padding-right', 'padding-bottom' ) ),
			),
			'padding-top' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Top' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Spacing', 'us' ),
			),
			'padding-bottom' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Bottom' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Spacing', 'us' ),
			),
			'padding-right' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Right' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Spacing', 'us' ),
			),

			// Border
			'border-style' => array(
				'title' => __( 'Border Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'solid' => __( 'Solid', 'us' ),
					'dashed' => __( 'Dashed', 'us' ),
					'dotted' => __( 'Dotted', 'us' ),
					'double' => __( 'Double', 'us' ),
				),
				'std' => 'none',
				'cols' => 2,
				'group' => __( 'Border', 'us' ),
			),
			'border-radius' => array(
				'title' => __( 'Border Radius', 'us' ),
				'description' => $misc['desc_border_radius'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Border', 'us' ),
			),
			'border-left-width' => array(
				'title' => __( 'Border Width', 'us' ),
				'description' => us_translate( 'Left' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Border', 'us' ),
				'html-data' => array(
					'relations' => array(
						'border-top-width',
						'border-right-width',
						'border-bottom-width',
					),
				),
				'show_if' => array( 'border-style', '!=', 'none' ),
			),
			'border-top-width' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Top' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Border', 'us' ),
				'show_if' => array( 'border-style', '!=', 'none' ),
			),
			'border-bottom-width' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Bottom' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Border', 'us' ),
				'show_if' => array( 'border-style', '!=', 'none' ),
			),
			'border-right-width' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Right' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Border', 'us' ),
				'show_if' => array( 'border-style', '!=', 'none' ),
			),
			'border-color' => array(
				'title' => __( 'Border Сolor', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'group' => __( 'Border', 'us' ),
				'show_if' => array( 'border-style', '!=', 'none' ),
			),

			// Position
			'position' => array(
				'type' => 'select',
				'options' => array(
					'static' => 'Static',
					'relative' => 'Relative',
					'absolute' => 'Absolute',
					'fixed' => 'Fixed',
					'sticky' => 'Sticky',
				),
				'std' => 'static',
				'group' => __( 'Position', 'us' ),
			),
			'left' => array(
				'title' => __( 'Position', 'us' ),
				'description' => us_translate( 'Left' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Position', 'us' ),
				'html-data' => array( 'relations' => array( 'top', 'right', 'bottom' ) ),
				'show_if' => array( 'position', '!=', 'static' ),
			),
			'top' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Top' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Position', 'us' ),
				'show_if' => array( 'position', '!=', 'static' ),
			),
			'bottom' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Bottom' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Position', 'us' ),
				'show_if' => array( 'position', '!=', 'static' ),
			),
			'right' => array(
				'title' => '&nbsp;',
				'description' => us_translate( 'Right' ),
				'type' => 'text',
				'std' => '',
				'cols' => 4,
				'group' => __( 'Position', 'us' ),
				'show_if' => array( 'position', '!=', 'static' ),
			),
			'z-index' => array(
				'title' => 'z-index',
				'description' => $misc['desc_integers'],
				'type' => 'text',
				'std' => '',
				'group' => __( 'Position', 'us' ),
				'show_if' => array( 'position', '!=', 'static' ),
			),

			// Box Shadow
			'box-shadow-h-offset' => array(
				'title' => __( 'Horizontal Offset', 'us' ),
				'description' => $misc['desc_shadow'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Box Shadow', 'us' ),
			),
			'box-shadow-v-offset' => array(
				'title' => __( 'Vertical Offset', 'us' ),
				'description' => $misc['desc_shadow'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Box Shadow', 'us' ),
			),
			'box-shadow-blur' => array(
				'title' => __( 'Blur', 'us' ),
				'description' => $misc['desc_shadow'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Box Shadow', 'us' ),
			),
			'box-shadow-spread' => array(
				'title' => __( 'Spread', 'us' ),
				'description' => $misc['desc_shadow'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Box Shadow', 'us' ),
			),
			'box-shadow-color' => array(
				'title' => us_translate( 'Color' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '',
				'group' => __( 'Box Shadow', 'us' ),
			),

			// Animation
			'animation-name' => array(
				'description' => __( 'Will be applied to this element, when it enters into the browsers viewport.', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'fade' => __( 'Fade', 'us' ),
					'afc' => __( 'Appear From Center', 'us' ),
					'afl' => __( 'Appear From Left', 'us' ),
					'afr' => __( 'Appear From Right', 'us' ),
					'afb' => __( 'Appear From Bottom', 'us' ),
					'aft' => __( 'Appear From Top', 'us' ),
					'hfc' => __( 'Height Stretch', 'us' ),
					'wfc' => __( 'Width Stretch', 'us' ),
					'bounce' => __( 'Bounce', 'us' ),
				),
				'std' => 'none',
				'group' => __( 'Animation', 'us' ),
			),
			'animation-delay' => array(
				'title' => __( 'Animation Delay', 'us' ),
				'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">250ms</span>, <span class="usof-example">0.5s</span>, <span class="usof-example">1s</span>, <span class="usof-example">1.5s</span>',
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'animation-name', '!=', '' ),
				'group' => __( 'Animation', 'us' ),
			),
		),

		// The value will be compiled into css and added to the style tag
		'usb_preview' => array(
			'design_options' => array(
				// List of specific classes that will be added if there is a value by key name
				'color' => 'has_text_color',
				'font-size' => 'has_font_size',
				'background-color' => 'has_bg_color',
				'width' => 'has_width',
				'height' => 'has_height',
				'border-radius' => 'has_border_radius',
			),
			// ...
		),
	),

	// Extra CSS class
	'el_class' => array(
		'title' => __( 'Extra class', 'us' ),
		'type' => 'text',
		'std' => '',
		'shortcode_cols' => 2,
		'header_cols' => 2,
		'group' => __( 'Design', 'us' ),
		'usb_preview' => array(
			'attr' => 'class',
		),
	),

	// Element ID
	'el_id' => array(
		'title' => __( 'Element ID', 'us' ),
		'type' => 'text',
		'std' => '',
		'cols' => 2,
		'group' => __( 'Design', 'us' ),
		'context' => array( 'shortcode', 'header' ), // can't be added to Grid Layout
		'usb_preview' => array(
			'attr' => 'id',
		),
	),

	// Hide element on responsive states
	'hide_on_states' => array(
		'title' => __( 'Hide on', 'us' ),
		'type' => 'checkboxes',
		'options' => $hide_on_states_options,
		'std' => '',
		'classes' => 'vertical',
		'cols' => 1, // for correct UI in WPBakery
		'group' => __( 'Design', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => array(
			'mod' => 'hide_on',
		),
	),

	// Additional options for Header elements
	'hide_for_sticky' => array(
		'type' => 'switch',
		'switch_text' => __( 'Hide this element when the header is sticky', 'us' ),
		'std' => FALSE,
		'group' => __( 'Design', 'us' ),
		'context' => array( 'header' ),
	),
	'hide_for_not_sticky' => array(
		'type' => 'switch',
		'switch_text' => __( 'Hide this element when the header is NOT sticky', 'us' ),
		'std' => FALSE,
		'group' => __( 'Design', 'us' ),
		'context' => array( 'header' ),
	),

	// Additional options for Grid Layout elements
	'hide_below' => array(
		'title' => __( 'Hide on screens LESS than', 'us' ),
		'type' => 'slider',
		'std' => '0px',
		'options' => array(
			'px' => array(
				'min' => 0,
				'max' => 2000,
				'step' => 10,
			),
		),
		'cols' => 2,
		'group' => __( 'Design', 'us' ),
		'context' => array( 'grid' ),
	),
	'hide_above' => array(
		'title' => __( 'Hide on screens MORE than', 'us' ),
		'type' => 'slider',
		'std' => '0px',
		'options' => array(
			'px' => array(
				'min' => 0,
				'max' => 2000,
				'step' => 10,
			),
		),
		'cols' => 2,
		'group' => __( 'Design', 'us' ),
		'context' => array( 'grid' ),
	),

);
