<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Link
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['text'] string Field additional text
 *
 * @var   $value string Current value
 * @var   $context string Context param states which builder is it
 *
 * @param $value ['url'] string Link URL
 * @param $value ['target'] string Link Target
 */

// Get current context
$context = ! empty( $context )
	? $context
	: '';

// Data format by contexts
$data_format_by_contexts = array(
	'json' => array( 'header'/* builder */, 'grid'/* builder */ ), // JSON object
	'jsons' => array( 'metabox', 'usb_metabox' ), // JSON string
	'serialized' => array( 'shortcode' ), // Serialized `serialize(...)`
);

// Get current data format
$data_format = '';
if ( ! empty( $context ) ) {
	foreach ( $data_format_by_contexts as $format => $contexts ) {
		if ( in_array( $context, $contexts ) ) {
			$data_format = $format;
			break;
		}
	}
}

// If the data format is not set, we will set the default JSON string
if ( empty( $data_format ) ) {
	$data_format = 'jsons';
}

// All attributes for input field
$input_atts = array(
	'data-format' => (string) $data_format,
	'name' => $name,
	'type' => 'hidden',
);

// Utilize WPB format for links
if ( $context === 'shortcode' AND is_string( $value ) ) {
	$input_atts['value'] = $value;
	$input_atts['data-format'] = 'serialized';

	if (
		strpos( $value, 'url:' ) === 0
		OR strpos( $value, '|' ) !== FALSE
	) {
		$params_pairs = explode( '|', $value );
		$value = array();
		if ( ! empty( $params_pairs ) ) {
			foreach ( $params_pairs as $pair ) {
				$param = explode( ':', $pair, 2 );
				if ( ! empty( $param[0] ) AND isset( $param[1] ) ) {
					$value[ $param[0] ] = rawurldecode( $param[1] );
				}
			}
		}
	}
}

// Fallback for cases when URL was set in WPBakery editor
if ( ! is_array( $value ) ) {
	$value = array(
		'url' => is_string( $value ) ? $value : '',
	);

}
if ( ! isset( $value['url'] ) ) {
	$value['url'] = '';
}
if ( ! isset( $value['target'] ) ) {
	$value['target'] = '';
}

if ( $context !== 'shortcode' ) {
	$input_atts['value'] = json_encode( $value );
}

// Output the html
$output = '<input' . us_implode_atts( $input_atts ) . '>';
$output .= '<input type="text" value="' . esc_attr( rawurldecode( $value['url'] ) ) . '"';
if ( ! empty( $field['placeholder'] ) ) {
	$output .= ' placeholder="' . esc_attr( $field['placeholder'] ) . '"';
}
$output .= '>';

// "Open in the new tab" checkbox
$output .= '<div class="usof-checkbox">';
$output .= '<label>';
$output .= '<input type="checkbox"' . checked( $value['target'], '_blank', FALSE ) . '>';
$output .= '<span class="usof-checkbox-text">' . strip_tags( us_translate( 'Open link in a new tab' ) ) . '</span>';
$output .= '</label>';
$output .= '</div>';

echo $output;
