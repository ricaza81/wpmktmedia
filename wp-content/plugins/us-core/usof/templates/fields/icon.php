<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Icon
 *
 * Icon field with preview
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
 */

$icon_sets = us_get_available_icon_sets();
reset( $icon_sets );

$value = trim( $value );

// Reset the value to default, when icon sets are available and value doesn't fit to icon sets
if ( ! empty( $icon_sets ) AND ! preg_match( '/(fas|far|fal|fad|fab|material)\|[a-z0-9-]/i', $value ) ) {
	$value = us_arr_path( $field, 'std', '' );
}

$input_atts = array(
	'class' => 'us-icon-value',
	'name' => $name,
	'value' => $value,
	'type' => empty( $icon_sets ) ? 'text' : 'hidden',
);

// Append class, if set (used for correct work in WPBakery Backend Editor)
if ( ! empty( $input_class ) ) {
	$input_atts['class'] .= ' ' . $input_class;
}

// Output the HTML
$output = '<div class="us-icon">';
$output .= '<input' . us_implode_atts( $input_atts ) . '>';

// Output icon sets selection and icon preview, when at least one icon set is available
if ( ! empty( $icon_sets ) ) {

	$select_value = $input_value = '';
	$value_arr = explode( '|', $value );
	if ( count( $value_arr ) == 2 ) {
		$select_value = $value_arr[0];
		$input_value = $value_arr[1];
	}
	if ( empty( $select_value ) ) {
		$select_value = key( $icon_sets );
	}

	$output .= '<div class="usof-select">';
	$output .= '<select name="icon_set" class="us-icon-select">';
	foreach ( $icon_sets as $icon_set_slug => $icon_set ) {
		$option_atts = array(
			'value' => $icon_set_slug,
			'data-info-url' => $icon_set['set_url'],
		);
		if ( $select_value == $icon_set_slug ) {
			$option_atts['selected'] = 'selected';
		}
		$output .= '<option' . us_implode_atts( $option_atts ) . '>' . $icon_set['set_name'] . '</option>';
	}
	$output .= '</select>';
	$output .= '</div>';

	$output .= '<div class="us-icon-preview">';
	if ( $icon_preview_html = us_prepare_icon_tag( $value ) ) {
		$output .= $icon_preview_html;
	} else {
		$output .= '<i class="material-icons"></i>';
	}
	$output .= '</div>';

	$output .= '<div class="us-icon-input">';
	$output .= '<input name="icon_name" class="us-icon-text" type="text" value="' .  $input_value . '">';
	$output .= '</div>';

	$output .= '</div>';

	$output .= '<div class="us-icon-desc">';
	if ( ! empty( $icon_sets[ $select_value ]['set_url'] ) ) {
		$output .= '<a class="us-icon-set-link" href="' . $icon_sets[ $select_value ]['set_url'] . '" target="_blank" rel="noopener">';
		$output .= __( 'Enter icon name from the list', 'us' );
		$output .= '</a>. ';
	}
	$output .= __( 'Examples:', 'us' ) . ' <span class="usof-example">star</span>, <span class="usof-example">edit</span>, <span class="usof-example">code</span>';
}

$output .= '</div>';

echo $output;
