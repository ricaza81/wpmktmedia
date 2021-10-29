<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Header Options used by Header Builder plugin.
 * Options and elements' fields are described in USOF-style format.
 */

$elements = array(
	'text',
	'image',
	'menu',
	'additional_menu',
	'search',
	'dropdown',
	'socials',
	'btn',
	'html',
	'popup',
	'hwrapper',
	'vwrapper',
);
if ( class_exists( 'woocommerce' ) ) {
	$elements[] = 'cart';
}

// Generate informative description to improve UX
$breakpoints_desc = __( 'By default, header states are changed via the global breakpoints:', 'us' );
$breakpoints_desc .= '<ul>';
$breakpoints_desc .= '<li><strong>' . us_get_option( 'laptops_breakpoint' ) . '</strong> - ';
$breakpoints_desc .= us_config( 'theme-options.layout.fields.laptops_breakpoint.title' ) . '</li>';
$breakpoints_desc .= '<li><strong>' . us_get_option( 'tablets_breakpoint' ) . '</strong> - ';
$breakpoints_desc .= us_config( 'theme-options.layout.fields.tablets_breakpoint.title' ) . '</li>';
$breakpoints_desc .= '<li><strong>' . us_get_option( 'mobiles_breakpoint' ) . '</strong> - ';
$breakpoints_desc .= us_config( 'theme-options.layout.fields.mobiles_breakpoint.title' ) . '</li>';
$breakpoints_desc .= '</ul>';
$breakpoints_desc .= '<a target="_blank" rel="noopener" href="' . admin_url( 'admin.php?page=us-theme-options#layout' ) . '">' . __( 'Change them in Theme Options', 'us' ) . '</a>';

return array(

	// Supported elements
	'elements' => $elements,

	// Side options
	'options' => array(

		// General Header Settings
		'global' => array(
			'custom_breakpoint' => array(
				'switch_text' => __( 'Custom Breakpoint', 'us' ),
				'type' => 'switch',
				'description' => $breakpoints_desc,
				'std' => FALSE,
				'classes' => 'desc_2',
				'show_if' => array(
					// Placing stub condition that will always be true but will force to check this show_if rule
					array( 'orientation', '=', array( 'hor', 'ver' ) ),
					'and',
					array( 'state', '!=', 'default' ),
				),
			),
			'breakpoint' => array(
				'type' => 'slider',
				'std' => '900px',
				'options' => array(
					'px' => array(
						'min' => 300,
						'max' => 1400,
					),
				),
				'classes' => 'for_above',
				'show_if' => array( 'custom_breakpoint', '=', TRUE ),
			),
			'orientation' => array(
				'title' => __( 'Orientation', 'us' ),
				'type' => 'radio',
				'options' => array(
					'hor' => __( 'Horizontal', 'us' ),
					'ver' => __( 'Vertical', 'us' ),
				),
				'std' => 'hor',
			),
			'sticky' => array(
				'switch_text' => __( 'Sticky Header', 'us' ),
				'type' => 'switch',
				'description' => __( 'Fix the header at the top of a page during scroll', 'us' ),
				'std' => TRUE,
				'classes' => 'desc_2',
				'show_if' => array( 'orientation', '=', 'hor' ),
			),
			'sticky_auto_hide' => array(
				'switch_text' => __( 'Auto-hide on scroll down', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'classes' => 'desc_2 for_above',
				'show_if' => array(
					array( 'orientation', '=', 'hor' ),
					'and',
					array( 'sticky', '=', TRUE ),
				),
			),
			'scroll_breakpoint' => array(
				'title' => __( 'Header Scroll Breakpoint', 'us' ),
				'description' => __( 'This option sets scroll distance from the top of a page after which the sticky header will be shrunk', 'us' ),
				'type' => 'slider',
				'std' => '1px',
				'options' => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'classes' => 'desc_2 desc_fix',
				'show_if' => array(
					array( 'orientation', '=', 'hor' ),
					'and',
					array( 'sticky', '=', TRUE ),
				),
			),
			'transparent' => array(
				'switch_text' => __( 'Transparent Header', 'us' ),
				'type' => 'switch',
				'description' => __( 'Make the header transparent at its initial position', 'us' ),
				'std' => FALSE,
				'classes' => 'desc_2',
				'show_if' => array( 'orientation', '=', 'hor' ),
			),
			'width' => array(
				'title' => __( 'Header Width', 'us' ),
				'type' => 'slider',
				'std' => '300px',
				'options' => array(
					'px' => array(
						'min' => 200,
						'max' => 400,
					),
					'rem' => array(
						'min' => 10.0,
						'max' => 30.0,
						'step' => 0.1,
					),
				),
				'show_if' => array( 'orientation', '=', 'ver' ),
			),
			'elm_align' => array(
				'title' => __( 'Elements Alignment', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'center',
				'show_if' => array( 'orientation', '=', 'ver' ),
			),
			'shadow' => array(
				'title' => __( 'Header Shadow', 'us' ),
				'type' => 'radio',
				'options' => array(
					'none' => us_translate( 'None' ),
					'thin' => __( 'Thin', 'us' ),
					'wide' => __( 'Wide', 'us' ),
				),
				'std' => 'thin',
			),
		),

		// Top Area
		'top' => array(
			'top_show' => array(
				'switch_text' => __( 'Show Area', 'us' ),
				'type' => 'switch',
				'std' => TRUE,
			),
			'top_height' => array(
				'title' => __( 'Area Height', 'us' ),
				'type' => 'slider',
				'std' => '40px',
				'options' => array(
					// Adding new units needs TONS of changes in "templates/css-header.php", so leave "px" only now
					'px' => array(
						'min' => 40,
						'max' => 300,
					),
				),
				'show_if' => array(
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'orientation', '=', 'hor' ),
				),
			),
			'top_sticky_height' => array(
				'title' => __( 'Sticky Area Height', 'us' ),
				'type' => 'slider',
				'std' => '40px',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'show_if' => array(
					array( 'sticky', '=', TRUE ),
					'and',
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'orientation', '=', 'hor' ),
				),
			),
			'top_fullwidth' => array(
				'switch_text' => __( 'Full Width Content', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'show_if' => array(
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'orientation', '=', 'hor' ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'top_centering' => array(
				'switch_text' => __( 'Center the middle cell', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array(
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'orientation', '=', 'hor' ),
				),
			),
			'top_bg_color' => array(
				'type' => 'color',
				'title' => us_translate( 'Background' ),
				'std' => '_header_top_bg',
				'show_if' => array(
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'top_text_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'std' => '_header_top_text',
				'show_if' => array(
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'top_text_hover_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => __( 'Link on hover', 'us' ),
				'std' => '_header_top_text_hover',
				'show_if' => array(
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'top_transparent_bg_color' => array(
				'type' => 'color',
				'title' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Background' ),
				'std' => '_header_top_transparent_bg',
				'show_if' => array(
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'top_transparent_text_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'std' => '_header_top_transparent_text',
				'show_if' => array(
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'top_transparent_text_hover_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => __( 'Transparent Header', 'us' ) . ': ' . __( 'Link on hover', 'us' ),
				'std' => '_header_top_transparent_text_hover',
				'show_if' => array(
					array( 'top_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
		),

		// Main Area
		'middle' => array(
			'middle_height' => array(
				'title' => __( 'Area Height', 'us' ),
				'type' => 'slider',
				'std' => '80px',
				'options' => array(
					// Adding new units needs TONS of changes in "templates/css-header.php", so leave "px" only now
					'px' => array(
						'min' => 40,
						'max' => 300,
					),
				),
				'show_if' => array( 'orientation', '=', 'hor' ),
			),
			'middle_sticky_height' => array(
				'title' => __( 'Sticky Area Height', 'us' ),
				'type' => 'slider',
				'std' => '60px',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'show_if' => array(
					array( 'sticky', '=', TRUE ),
					'and',
					array( 'orientation', '=', 'hor' ),
				),
			),
			'middle_fullwidth' => array(
				'switch_text' => __( 'Full Width Content', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'show_if' => array(
					array( 'orientation', '=', 'hor' ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'middle_centering' => array(
				'switch_text' => __( 'Center the middle cell', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array( 'orientation', '=', 'hor' ),
			),
			'elm_valign' => array(
				'title' => __( 'Elements Vertical Alignment', 'us' ),
				'type' => 'radio',
				'options' => array(
					'top' => us_translate( 'Top' ),
					'middle' => us_translate( 'Middle' ),
					'bottom' => us_translate( 'Bottom' ),
				),
				'std' => 'top',
				'show_if' => array(
					array( 'orientation', '=', 'ver' ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'bg_img' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'std' => '',
			),
			'bg_img_wrapper_start' => array(
				'type' => 'wrapper_start',
				'show_if' => array( 'bg_img', '!=', '' ),
			),
			'bg_img_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'select',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
			),
			'bg_img_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'select',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
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
				'std' => 'top left',
				'classes' => 'bgpos',
			),
			'bg_img_attachment' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Scroll with Page' ),
				'std' => TRUE,
			),
			'bg_img_wrapper_end' => array(
				'type' => 'wrapper_end',
			),
			'middle_bg_color' => array(
				'type' => 'color',
				'title' => us_translate( 'Background' ),
				'std' => '_header_middle_bg',
				'show_if' => array(
					// Placing stub condition that will always be true but will force to check this show_if rule
					array( 'orientation', '=', array( 'hor', 'ver' ) ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'middle_text_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'std' => '_header_middle_text',
				'show_if' => array(
					// Placing stub condition that will always be true but will force to check this show_if rule
					array( 'orientation', '=', array( 'hor', 'ver' ) ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'middle_text_hover_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => __( 'Link on hover', 'us' ),
				'std' => '_header_middle_text_hover',
				'show_if' => array(
					// Placing stub condition that will always be true but will force to check this show_if rule
					array( 'orientation', '=', array( 'hor', 'ver' ) ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'middle_transparent_bg_color' => array(
				'type' => 'color',
				'title' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Background' ),
				'std' => '_header_transparent_bg',
				'show_if' => array(
					// Placing stub condition that will always be true but will force to check this show_if rule
					array( 'orientation', '=', array( 'hor', 'ver' ) ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'middle_transparent_text_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'std' => '_header_transparent_text',
				'show_if' => array(
					// Placing stub condition that will always be true but will force to check this show_if rule
					array( 'orientation', '=', array( 'hor', 'ver' ) ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'middle_transparent_text_hover_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => __( 'Transparent Header', 'us' ) . ': ' . __( 'Link on hover', 'us' ),
				'std' => '_header_transparent_text_hover',
				'show_if' => array(
					// Placing stub condition that will always be true but will force to check this show_if rule
					array( 'orientation', '=', array( 'hor', 'ver' ) ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
		),

		// Bottom Area
		'bottom' => array(
			'bottom_show' => array(
				'switch_text' => __( 'Show Area', 'us' ),
				'type' => 'switch',
				'std' => TRUE,
			),
			'bottom_height' => array(
				'title' => __( 'Area Height', 'us' ),
				'type' => 'slider',
				'std' => '50px',
				'options' => array(
					'px' => array(
						'min' => 40,
						'max' => 300,
					),
				),
				'show_if' => array(
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'orientation', '=', 'hor' ),
				),
			),
			'bottom_sticky_height' => array(
				'title' => __( 'Sticky Area Height', 'us' ),
				'type' => 'slider',
				'std' => '50px',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'show_if' => array(
					array( 'sticky', '=', TRUE ),
					'and',
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'orientation', '=', 'hor' ),
				),
			),
			'bottom_fullwidth' => array(
				'switch_text' => __( 'Full Width Content', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'show_if' => array(
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'orientation', '=', 'hor' ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'bottom_centering' => array(
				'switch_text' => __( 'Center the middle cell', 'us' ),
				'type' => 'switch',
				'std' => FALSE,
				'classes' => 'for_above',
				'show_if' => array(
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'orientation', '=', 'hor' ),
				),
			),
			'bottom_bg_color' => array(
				'type' => 'color',
				'title' => us_translate( 'Background' ),
				'std' => '_header_middle_bg',
				'show_if' => array(
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'bottom_text_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'std' => '_header_middle_text',
				'show_if' => array(
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'bottom_text_hover_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => __( 'Link on hover', 'us' ),
				'std' => '_header_middle_text_hover',
				'show_if' => array(
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'bottom_transparent_bg_color' => array(
				'type' => 'color',
				'title' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Background' ),
				'std' => '_header_transparent_bg',
				'show_if' => array(
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'bottom_transparent_text_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => __( 'Transparent Header', 'us' ) . ': ' . us_translate( 'Text' ) . ' / ' . us_translate( 'Link' ),
				'std' => '_header_transparent_text',
				'show_if' => array(
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
			'bottom_transparent_text_hover_color' => array(
				'type' => 'color',
				'with_gradient' => FALSE,
				'title' => __( 'Transparent Header', 'us' ) . ': ' . __( 'Link on hover', 'us' ),
				'std' => '_header_transparent_text_hover',
				'show_if' => array(
					array( 'bottom_show', '=', TRUE ),
					'and',
					array( 'state', '=', 'default' ),
				),
			),
		),
	),

);
