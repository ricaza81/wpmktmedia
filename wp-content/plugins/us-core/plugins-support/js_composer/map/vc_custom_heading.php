<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_custom_heading
 *
 * @var $shortcode string Current shortcode name
 * @var $config    array Shortcode's config
 */

vc_update_shortcode_param(
	'vc_custom_heading', array(
		'param_name' => 'el_id',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'group' => __( 'Design', 'us' ),
	)
);
vc_update_shortcode_param(
	'vc_custom_heading', array(
		'param_name' => 'el_class',
		'description' => '',
		'edit_field_class' => 'vc_col-sm-6',
		'group' => __( 'Design', 'us' ),
	)
);

// Update 'font_container' param to remove duplicated settings, located in new Design options
vc_update_shortcode_param(
	'vc_custom_heading', array(
		'type' => 'font_container',
		'param_name' => 'font_container',
		'value' => 'tag:h2|text_align:left',
		'settings' => array(
			'fields' => array( 'tag', 'text_align' )
		),
	)
);

// We are removing css_animation param for custom heading, but we are not redefying it's template, so we need to set a default value for css_animation param for front end editor compatibility
vc_remove_param( 'vc_custom_heading', 'css_animation' );
add_filter( 'vc_map_get_attributes', 'us_css_animation_default_value_for_vc_custom_heading', 100, 2 );
function us_css_animation_default_value_for_vc_custom_heading( $atts, $tag ) {
	if ( $tag == 'vc_custom_heading' ) {
		$atts['css_animation'] = '';
	}

	return $atts;
}
