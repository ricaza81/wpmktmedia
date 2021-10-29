<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme White Label
 *
 * @filter us_config_white-label
 */

return array(

	'white_label' => array(
		'title' => '',
		'fields' => array(

			// White Label
			'white_label' => array(
				'switch_text' => __( 'Activate White Label', 'us' ),
				'type' => 'switch',
				'std' => 0,
			),
			'white_label_theme_name' => array(
				'title' => __( 'Theme Name', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'Will be shown on all admin pages, except the current one. The theme directory won\'t be renamed due to server security reasons.', 'us' ),
				'type' => 'text',
				'placeholder' => US_THEMENAME,
				'show_if' => array( 'white_label', '=', 1 ),
			),
			'white_label_theme_screenshot' => array(
				'title' => __( 'Theme Image', 'us' ),
				'title_pos' => 'side',
				'description' => sprintf( __( 'Will be shown on the "%s" page.', 'us' ), us_translate( 'Themes' ) ) . ' ' . __( 'Use the 4:3 aspect ratio to display correctly.', 'us' ),
				'type' => 'upload',
				'show_if' => array( 'white_label', '=', 1 ),
			),
			'white_label_theme_icon' => array(
				'title' => __( 'Theme Menu Icon', 'us' ),
				'title_pos' => 'side',
				'description' => __( 'Will be shown in the admin menu.', 'us' ),
				'type' => 'upload',
				'show_if' => array( 'white_label', '=', 1 ),
			),
			'white_label_login_icon' => array(
				'title' => __( 'Login Page Icon', 'us' ),
				'title_pos' => 'side',
				'description' => sprintf( __( 'Will be shown on the "%s" page.', 'us' ), __( 'Login', 'us' ) ),
				'type' => 'upload',
				'show_if' => array( 'white_label', '=', 1 ),
			),

		),
	),

);
