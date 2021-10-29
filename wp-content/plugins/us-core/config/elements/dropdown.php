<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

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

return array(
	'title' => __( 'Dropdown', 'us' ),
	'icon' => 'fas fa-caret-square-down',
	'params' => array_merge( array(

		'source' => array(
			'title' => us_translate( 'Show' ),
			'type' => 'select',
			'options' => $source_values,
			'std' => 'own',
		),
		'link_title' => array(
			'title' => __( 'Dropdown Title', 'us' ),
			'type' => 'text',
			'std' => __( 'Click Me', 'us' ),
			'show_if' => array( 'source', '=', array( 'own', 'sidebar' ) ),
		),
		'link_icon' => array(
			'title' => __( 'Dropdown Icon', 'us' ),
			'type' => 'icon',
			'std' => '',
			'show_if' => array( 'source', '=', array( 'own', 'sidebar' ) ),
		),
		'h_links' => array(
			'title' => __( 'Dropdown Links', 'us' ),
			'type' => 'heading',
			'show_if' => array( 'source', '=', 'own' ),
			'classes' => 'as_field_title',
		),
		'links' => array(
			'title' => '{{label}}',
			'type' => 'group',
			'show_controls' => TRUE,
			'is_sortable' => TRUE,
			'is_accordion' => TRUE,
			'show_if' => array( 'source', '=', 'own' ),
			'std' => array(),
			'params' => array(
				'label' => array(
					'title' => us_translate( 'Title' ),
					'type' => 'text',
					'std' => us_translate( 'Custom Link' ),
				),
				'url' => array(
					'title' => us_translate( 'Link' ),
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'link',
					'std' => array(),
				),
				'icon' => array(
					'title' => __( 'Icon', 'us' ),
					'type' => 'icon',
					'std' => '',
				),
			),
		),
		'sidebar_id' => array(
			'title' => __( 'Sidebar', 'us' ),
			'description' => sprintf( __( 'Add or edit Sidebar on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank" rel="noopener">' . us_translate( 'Widgets' ) . '</a>' ),
			'type' => 'select',
			'options' => us_get_sidebars(),
			'std' => 'default_sidebar',
			'show_if' => array( 'source', '=', 'sidebar' ),
		),
		'wpml_switcher' => array(
			'type' => 'checkboxes',
			'options' => array(
				'flag' => us_translate( 'Flag', 'sitepress' ),
				'native_lang' => us_translate( 'Native language name', 'sitepress' ),
				'display_lang' => us_translate( 'Language name in current language', 'sitepress' ),
			),
			'std' => array( 'native_lang', 'display_lang' ),
			'show_if' => array( 'source', '=', 'wpml' ),
			'place_if' => class_exists( 'SitePress' ),
		),
		'polylang_switcher' => array(
			'type' => 'checkboxes',
			'options' => array(
				'flag' => us_translate( 'Flag', 'polylang' ),
				'full_name' => us_translate( 'Full name', 'polylang' ),
			),
			'std' => array( 'flag', 'full_name' ),
			'show_if' => array( 'source', '=', 'polylang' ),
			'place_if' => class_exists( 'Polylang' ),
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
		),
		'dropdown_effect' => array(
			'title' => __( 'Dropdown Effect', 'us' ),
			'type' => 'select',
			'options' => $misc['dropdown_effect_values'],
			'std' => 'height',
			'group' => us_translate( 'Appearance' ),
		),

	), $design_options ),
);
