<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: dropdown
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

$source_values = array(
	'own' => us_translate( 'Custom Links' ),
	'sidebar' => __( 'Sidebar with Widgets', 'us' ),
);
if ( class_exists( 'SitePress' ) ) {
	$source_values['wpml'] = us_translate( 'Language Switcher', 'sitepress' );
}
if ( class_exists( 'Polylang' ) ) {
	$source_values['polylang'] = us_translate( 'Language switcher', 'polylang' );
}

/**
 * @return array
 */
return array(
	'title' => __( 'Dropdown', 'us' ),
	'icon' => 'fas fa-caret-square-down',
	'params' => us_set_params_weight(

		// General section
		array(
			'source' => array(
				'title' => us_translate( 'Show' ),
				'type' => 'select',
				'options' => $source_values,
				'std' => 'own',
				'usb_preview' => TRUE,
			),
			'links' => array(
				'type' => 'group',
				'show_controls' => TRUE,
				'is_sortable' => TRUE,
				'is_accordion' => TRUE,
				'accordion_title' => 'label',
				'params' => array(
					'label' => array(
						'title' => us_translate( 'Title' ),
						'type' => 'text',
						'std' => us_translate( 'Custom Link' ),
						'admin_label' => TRUE,
					),
					'url' => array(
						'title' => us_translate( 'Link' ),
						'placeholder' => us_translate( 'Enter the URL' ),
						'type' => 'link',
						'std' => array(),
						'shortcode_std' => '',
					),
					'icon' => array(
						'title' => __( 'Icon', 'us' ),
						'type' => 'icon',
						'std' => '',
					)
				),
				'std' => array(),
				'show_if' => array( 'source', '=', 'own' ),
				'usb_preview' => TRUE,
			),
			'sidebar_id' => array(
				'description' => sprintf( __( 'Add or edit a Sidebar on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank" rel="noopener">' . us_translate( 'Widgets' ) . '</a>' ),
				'type' => 'select',
				'options' => us_get_sidebars(),
				'std' => 'default_sidebar',
				'classes' => 'for_above',
				'show_if' => array( 'source', '=', 'sidebar' ),
				'usb_preview' => TRUE,
			),
			'wpml_switcher' => array(
				'type' => 'checkboxes',
				'options' => array(
					'flag' => us_translate( 'Flag', 'sitepress' ),
					'native_lang' => us_translate( 'Native language name', 'sitepress' ),
					'display_lang' => us_translate( 'Language name in current language', 'sitepress' ),
				),
				'std' => 'native_lang,display_lang',
				'show_if' => array( 'source', '=', 'wpml' ),
				'place_if' => class_exists( 'SitePress' ),
				'usb_preview' => TRUE,
			),
			'polylang_switcher' => array(
				'type' => 'checkboxes',
				'options' => array(
					'flag' => us_translate( 'Flag', 'polylang' ),
					'full_name' => us_translate( 'Full name', 'polylang' ),
				),
				'std' => 'flag,full_name',
				'show_if' => array( 'source', '=', 'polylang' ),
				'place_if' => class_exists( 'Polylang' ),
				'usb_preview' => TRUE,
			),
			'link_title' => array(
				'title' => __( 'Dropdown Title', 'us' ),
				'type' => 'text',
				'std' => __( 'Click Me', 'us' ),
				'show_if' => array( 'source', '=', array( 'own', 'sidebar' ) ),
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-dropdown-current .w-dropdown-item-title',
				),
			),
			'link_icon' => array(
				'title' => __( 'Dropdown Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'show_if' => array( 'source', '=', array( 'own', 'sidebar' ) ),
				'usb_preview' => TRUE,
			),
			'dropdown_open' => array(
				'title' => __( 'Open Dropdown', 'us' ),
				'type' => 'radio',
				'options' => array(
					'click' => __( 'On click', 'us' ),
					'hover' => __( 'On hover', 'us' ),
				),
				'std' => 'click',
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => TRUE,
			),
			'dropdown_dir' => array(
				'title' => __( 'Dropdown Direction', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'right',
				'cols' => 2,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'drop_to',
				),
			),
			'dropdown_effect' => array(
				'title' => __( 'Dropdown Effect', 'us' ),
				'type' => 'select',
				'options' => $misc['dropdown_effect_values'],
				'std' => 'height',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'mod' => 'dropdown',
				),
			),
		),

		$design_options_params
	),

	'usb_init_js' => '$elm.wDropdown()',
);
