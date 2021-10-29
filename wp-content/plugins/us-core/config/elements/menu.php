<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => us_translate( 'Menu' ),
	'icon' => 'fas fa-bars',
	'params' => array_merge( array(
		// General
		'source' => array(
			'title' => us_translate( 'Menu' ),
			'description' => $misc['desc_menu_select'],
			'type' => 'select',
			'options' => us_get_nav_menus(),
			'std' => 'header-menu',
		),
		'indents' => array(
			'title' => __( 'Distance Between Main Items', 'us' ),
			'type' => 'slider',
			'std' => '20px',
			'options' => array(
				'px' => array(
					'min' => 0,
					'max' => 50,
				),
				'rem' => array(
					'min' => 0.0,
					'max' => 3.0,
					'step' => 0.1,
				),
				'em' => array(
					'min' => 0.0,
					'max' => 3.0,
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
		'spread' => array(
			'type' => 'switch',
			'switch_text' => __( 'Spread menu items evenly over the available width', 'us' ),
			'std' => FALSE,
			'classes' => 'for_above',
		),
		'vstretch' => array(
			'title' => __( 'Main Items Height', 'us' ),
			'type' => 'switch',
			'switch_text' => __( 'Stretch to the full available height', 'us' ),
			'std' => TRUE,
		),
		'hover_effect' => array(
			'title' => __( 'Main Items Hover Effect', 'us' ),
			'type' => 'select',
			'options' => array(
				'simple' => __( 'Simple', 'us' ),
				'underline' => us_translate( 'Underline' ),
			),
			'std' => 'simple',
		),

		// Dropdowns
		'dropdown_arrow' => array(
			'title' => __( 'Dropdown Indication', 'us' ),
			'type' => 'switch',
			'switch_text' => __( 'Show arrows for main items with dropdown', 'us' ),
			'std' => FALSE,
			'group' => __( 'Dropdowns', 'us' ),
		),
		'dropdown_effect' => array(
			'title' => __( 'Dropdown Effect', 'us' ),
			'type' => 'select',
			'options' => $misc['dropdown_effect_values'],
			'std' => 'height',
			'group' => __( 'Dropdowns', 'us' ),
		),
		'dropdown_font_size' => array(
			'title' => __( 'Dropdown Font Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '1rem',
			'group' => __( 'Dropdowns', 'us' ),
		),
		'dropdown_width' => array(
			'title' => __( 'Dropdown Width', 'us' ),
			'type' => 'switch',
			'switch_text' => __( 'Limit full-width dropdowns by a menu width', 'us' ),
			'std' => FALSE,
			'group' => __( 'Dropdowns', 'us' ),
		),

		// Menu colors
		'color_active_bg' => array(
			'type' => 'color',
			'text' => __( 'Active Menu Item Background', 'us' ),
			'std' => 'transparent',
			'group' => us_translate( 'Colors' ),
		),
		'color_active_text' => array(
			'type' => 'color',
			'with_gradient' => FALSE,
			'text' => __( 'Active Menu Item Text', 'us' ),
			'std' => '_header_middle_text_hover',
			'group' => us_translate( 'Colors' ),
		),
		'color_transparent_active_bg' => array(
			'type' => 'color',
			'text' => __( 'Transparent Header', 'us' ) . ': ' . __( 'Active Menu Item Background', 'us' ),
			'std' => 'transparent',
			'group' => us_translate( 'Colors' ),
		),
		'color_transparent_active_text' => array(
			'type' => 'color',
			'with_gradient' => FALSE,
			'text' => __( 'Transparent Header', 'us' ) . ': ' . __( 'Active Menu Item Text', 'us' ),
			'std' => '_header_transparent_text_hover',
			'group' => us_translate( 'Colors' ),
		),
		'color_hover_bg' => array(
			'type' => 'color',
			'text' => __( 'Menu Item Background on hover', 'us' ),
			'std' => 'transparent',
			'group' => us_translate( 'Colors' ),
		),
		'color_hover_text' => array(
			'type' => 'color',
			'with_gradient' => FALSE,
			'text' => __( 'Menu Item Text on hover', 'us' ),
			'std' => '_header_middle_text_hover',
			'group' => us_translate( 'Colors' ),
		),

		'color_drop_bg' => array(
			'type' => 'color',
			'text' => __( 'Dropdown Background', 'us' ),
			'std' => '_header_middle_bg',
			'group' => us_translate( 'Colors' ),
		),
		'color_drop_text' => array(
			'type' => 'color',
			'with_gradient' => FALSE,
			'text' => __( 'Dropdown Text', 'us' ),
			'std' => '_header_middle_text',
			'group' => us_translate( 'Colors' ),
		),
		'color_drop_hover_bg' => array(
			'type' => 'color',
			'text' => __( 'Dropdown Item Background on hover', 'us' ),
			'std' => 'transparent',
			'group' => us_translate( 'Colors' ),
		),
		'color_drop_hover_text' => array(
			'type' => 'color',
			'with_gradient' => FALSE,
			'text' => __( 'Dropdown Item Text on hover', 'us' ),
			'std' => '_header_middle_text_hover',
			'group' => us_translate( 'Colors' ),
		),
		'color_drop_active_bg' => array(
			'type' => 'color',
			'text' => __( 'Dropdown Active Item Background', 'us' ),
			'std' => 'transparent',
			'group' => us_translate( 'Colors' ),
		),
		'color_drop_active_text' => array(
			'type' => 'color',
			'with_gradient' => FALSE,
			'text' => __( 'Dropdown Active Item Text', 'us' ),
			'std' => '_header_middle_text_hover',
			'group' => us_translate( 'Colors' ),
		),

		// Mobile menu
		'mobile_width' => array(
			'title' => __( 'Show mobile menu when screen width is less than', 'us' ),
			'type' => 'slider',
			'std' => '900px',
			'options' => array(
				'px' => array(
					'min' => 300,
					'max' => 2000,
					'step' => 10,
				),
			),
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_layout' => array(
			'title' => __( 'Mobile Menu Layout', 'us' ),
			'type' => 'radio',
			'options' => array(
				'dropdown' => __( 'Dropdown', 'us' ),
				'panel' => __( 'Vertical Panel', 'us' ),
				'fullscreen' => __( 'Full Screen', 'us' ),
			),
			'std' => 'dropdown',
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_effect_p' => array(
			'type' => 'radio',
			'options' => array(
				'afl' => __( 'Appear From Left', 'us' ),
				'afr' => __( 'Appear From Right', 'us' ),
			),
			'std' => 'afl',
			'show_if' => array( 'mobile_layout', '=', 'panel' ),
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_effect_f' => array(
			'title' => __( 'Animation', 'us' ),
			'type' => 'select',
			'options' => array(
				'fade' => __( 'Fade', 'us' ),
				'aft' => __( 'Appear From Top', 'us' ),
				'afc' => __( 'Appear From Center', 'us' ),
				'afb' => __( 'Appear From Bottom', 'us' ),
			),
			'std' => 'aft',
			'show_if' => array( 'mobile_layout', '=', 'fullscreen' ),
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_font_size' => array(
			'title' => __( 'Main Items Font Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '1.1rem',
			'cols' => 2,
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_dropdown_font_size' => array(
			'title' => __( 'Dropdown Font Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '0.9rem',
			'cols' => 2,
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_align' => array(
			'title' => __( 'Menu Items Alignment', 'us' ),
			'type' => 'radio',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'center' => us_translate( 'Center' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'left',
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_behavior' => array(
			'title' => __( 'Show dropdown by click on', 'us' ),
			'description' => sprintf( __( 'You can change this behavior separately for every menu item on the %s page', 'us' ), '<a href="' . admin_url( 'nav-menus.php' ) . '" target="_blank" rel="noopener">' . us_translate( 'Menus' ) . '</a>' ),
			'type' => 'radio',
			'options' => array(
				'0' => __( 'Arrow', 'us' ),
				'1' => __( 'Label and Arrow', 'us' ),
			),
			'std' => '1',
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_icon_size' => array(
			'title' => __( 'Icon Size', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '24px',
			'cols' => 3,
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_icon_size_tablets' => array(
			'title' => __( 'Icon Size on Tablets', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '24px',
			'cols' => 3,
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_icon_size_mobiles' => array(
			'title' => __( 'Icon Size on Mobiles', 'us' ),
			'description' => $misc['desc_font_size'],
			'type' => 'text',
			'std' => '24px',
			'cols' => 3,
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_icon_thickness' => array(
			'title' => __( 'Icon Thickness', 'us' ),
			'type' => 'slider',
			'std' => '3px',
			'options' => array(
				'px' => array(
					'min' => 1.0,
					'max' => 5.0,
					'step' => 0.5,
				),
			),
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_icon_text' => array(
			'title' => __( 'Text near Icon', 'us' ),
			'type' => 'radio',
			'options' => array(
				'none' => us_translate( 'None' ),
				'left' => us_translate( 'Left' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'none',
			'group' => __( 'Mobile Menu', 'us' ),
		),
		'mobile_icon_text_label' => array(
			'type' => 'text',
			'std' => us_translate( 'Menu' ),
			'classes' => 'for_above',
			'show_if' => array( 'mobile_icon_text', '!=', 'none' ),
			'group' => __( 'Mobile Menu', 'us' ),
		),

	), $design_options ),
);
