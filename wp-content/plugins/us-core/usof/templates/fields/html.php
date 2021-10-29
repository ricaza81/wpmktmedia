<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Html
 *
 * Simple textarea field.
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['encoded'] bool Value is encoded
 *
 * @var   $value string Current value
 */

$params = array(
	'editor' => FALSE,
);
if ( isset( $field['encoded'] ) AND $field['encoded'] ) {
	$params['encoded'] = 1;
}

if ( function_exists( 'wp_enqueue_code_editor' ) ) {
	$params['editor'] = wp_enqueue_code_editor( array(
		'type' => 'text/html',
		// https://codemirror.net/doc/manual.html#config
		'codemirror' => array(
			'viewportMargin' => 100,
			'lineWrapping' => TRUE
		)
	) );
}

$output = '<div class="usof-form-row-control-params"' . us_pass_data_to_js( $params ) . '></div>';
$output .= '<textarea name="' . $name . '">' . esc_textarea( $value ) . '</textarea>';

echo $output;

