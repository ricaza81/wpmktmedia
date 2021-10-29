<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$misc = us_config( 'elements_misc' );
$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Person', 'us' ),
	'description' => __( 'Photo, name, description and social links', 'us' ),
	'icon' => 'fas fa-user',
	'params' => array_merge( array(

		// General
		'image' => array(
			'title' => __( 'Photo', 'us' ),
			'type' => 'upload',
			'is_multiple' => FALSE,
			'extension' => 'png,jpg,jpeg,gif,svg',
			'cols' => 2,
		),
		'image_hover' => array(
			'title' => __( 'Photo on hover (optional)', 'us' ),
			'type' => 'upload',
			'is_multiple' => FALSE,
			'extension' => 'png,jpg,jpeg,gif,svg',
			'cols' => 2,
		),
		'name' => array(
			'title' => us_translate( 'Name' ),
			'type' => 'text',
			'std' => 'John Doe',
			'holder' => 'div',
			'cols' => 2,
		),
		'role' => array(
			'title' => __( 'Role', 'us' ),
			'type' => 'text',
			'std' => 'UpSolution Team',
			'holder' => 'div',
			'cols' => 2,
		),
		'content' => array(
			'title' => us_translate( 'Description' ),
			'type' => 'textarea',
			'std' => '',
			'holder' => 'div',
		),
		'link' => array(
			'title' => us_translate( 'Link' ),
			'description' => __( 'Applies to the Name and to the Photo', 'us' ),
			'type' => 'link',
			'std' => '',
		),

		// More Options
		'layout' => array(
			'title' => __( 'Layout', 'us' ),
			'type' => 'select',
			'options' => array(
				'simple' => __( 'Simple', 'us' ),
				'simple_circle' => __( 'Simple (rounded photo)', 'us' ),
				'square' => __( 'Compact', 'us' ),
				'circle' => __( 'Compact (rounded photo)', 'us' ),
				'modern' => __( 'Modern', 'us' ),
				'trendy' => __( 'Trendy', 'us' ),
				'cards' => __( 'Cards', 'us' ),
			),
			'std' => 'circle',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'effect' => array(
			'title' => __( 'Photo Effect', 'us' ),
			'type' => 'select',
			'options' => array(
				'none' => us_translate( 'None' ),
				'sepia' => __( 'Sepia', 'us' ),
				'bw' => __( 'Black & White', 'us' ),
				'faded' => __( 'Faded', 'us' ),
				'colored' => __( 'Colored', 'us' ),
			),
			'std' => 'none',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'img_size' => array(
			'title' => __( 'Image Size', 'us' ),
			'description' => $misc['desc_img_sizes'],
			'type' => 'select',
			'options' => us_get_image_sizes_list(),
			'std' => 'us_350_350_crop',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'email' => array(
			'title' => us_translate( 'Email' ),
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'facebook' => array(
			'title' => 'Facebook',
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'twitter' => array(
			'title' => 'Twitter',
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'google_plus' => array(
			'title' => 'Google',
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'linkedin' => array(
			'title' => 'LinkedIn',
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'skype' => array(
			'title' => 'Skype',
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'custom_link' => array(
			'title' => __( 'Custom Link', 'us' ),
			'type' => 'text',
			'std' => '',
			'cols' => 2,
			'group' => __( 'More Options', 'us' ),
		),
		'custom_icon' => array(
			'title' => __( 'Custom Link Icon', 'us' ),
			'type' => 'icon',
			'std' => 'fas|star',
			'show_if' => array( 'custom_link', '!=', '' ),
			'group' => __( 'More Options', 'us' ),
		),

	), $design_options ),
);
