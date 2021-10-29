<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Images
 *
 * Radiobutton-like toggler of images
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array Associative array of value => image
 *
 * @var   $value string Current value
 */

$output = '<ul class="usof-imgradio">';
foreach ( $field['options'] as $key => $img_url ) {
	$output .= '<li class="usof-imgradio-item">';
	$output .= '<input type="radio" id="' . $id . '_' . $key . '" name="' . $name . '"' . checked( $value, $key, FALSE ) . ' value="' . esc_attr( $key ) . '">';
	$output .= '<label for="' . $id . '_' . $key . '"><img src="' . $img_url . '.png" srcset="' . $img_url . '-2x.png 2x" alt="' . $key . '"></label>';
	$output .= '</li>';
}
$output .= '</ul>';

echo $output;
