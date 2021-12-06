<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Select
 *
 * Drop-down selector field.
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array List of value => title pairs
 * @param $field ['is_visual_composer'] boolean
 *
 * @var $name  string Field name
 * @var $id    string Field ID
 * @var $field array Field options
 * @var $classes string
 *
 * @var   $value string Current value
 */

if ( empty( $field['options'] ) OR ! is_array( $field['options'] ) ) {
	return;
}

$value = isset( $value )
	? (string) $value
	: '';

$_atts = array(
	'class' => 'usof-select',
);

if ( ! empty( $classes ) ) {
	$_atts['class'] .= ' ' . $classes;
}

// The custom data
if ( ! empty( $field['data'] ) AND is_array( $field['data'] ) ) {
	foreach ( $field['data'] as $attr_name => $attr_value ) {
		$_atts[ 'data-' . $attr_name ] = (string) $attr_value;
	}
}

$_select_atts = array(
	'name' => $name,
	'class' => 'wpb_vc_param_value', // TODO: remove this
);

/**
 * Generating the option tag
 *
 * @param string $key The key
 * @param string $name The name
 * @return string
 */
$func_gen_option = function ( $key, $title ) use ( $value, $field, $name ) {
	$option_data = '';
	/*
	 * If this is a select for Page Layout settings,
	 * checking if default templates for page areas were set in Theme Options
	 * and adding data for links to those templates edit pages
	 */
	if (
		usb_is_builder_page()
		AND isset( $field['hints_for'] )
		AND in_array(
			$name, array(
			'us_header_id',
			'us_titlebar_id',
			'us_content_id',
			'us_sidebar_id',
			'us_footer_id',
		)
		)
		AND $key == '__defaults__'
	) {
		$post_type = get_post_type( (int) USBuilder::get_post_id() );
		$area = str_replace( array( 'us_', '_id' ), '', $name );
		$default_id = us_get_page_area_id( $area, array( 'page_type' => 'post', 'post_type' => $post_type ) );
		if ( get_post_status( $default_id ) != FALSE ) {
			$option_data .= ' data-id="' . $default_id . '"';
			if ( $default_title = get_the_title( $default_id ) ) {
				$option_data .= ' data-title="' . get_the_title( $default_id ) . '"';
			}
		}

	}

	return '<option value="' . esc_attr( $key ) . '"' . selected( $value, $key, FALSE ) . $option_data . '>' . $title . '</option>';
};

$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= '<select' . us_implode_atts( $_select_atts ) . '>';

foreach ( $field['options'] as $option_key => $option_value ) {
	if ( is_string( $option_value ) ) {
		$output .= $func_gen_option( $option_key, $option_value );

	} else if ( ! empty( $option_value ) AND is_array( $option_value ) ) {
		$output .= '<optgroup label="' . esc_attr( $option_key ) . '">';
		foreach ( $option_value as $item_key => $item_value ) {
			if ( empty( $item_value ) ) {
				continue;
			}
			$output .= $func_gen_option( $item_key, $item_value );
		}
		$output .= '</optgroup>';
	}
}

$output .= '</select>';
$output .= '</div>';
echo $output;
