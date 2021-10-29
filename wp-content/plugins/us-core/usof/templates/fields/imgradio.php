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
 * @param $field ['group'] string Group name
 * @param $field ['options'] array The options list
 * @param $field ['preview_path'] string Patch to preview file
 *
 * @var   $value string Current value
 */

if ( $group = us_arr_path( $field, 'group', '' ) ) {
	$group = preg_replace( '/\s+/u', '-', strtolower( $group ) );
}

$classes = isset( $classes ) ? $classes : '';
$value = isset( $value ) ? $value : '';

$output = '<div class="usof-imgradio">';
foreach ( us_arr_path( $field, 'options' ) as $filename => $filename_title ) {
	$preview = '';

	if ( ! empty( $filename ) AND $path = us_arr_path( $field, 'preview_path', FALSE ) ) {
		$path = sprintf( $path, $filename );
		$fullpath = realpath( US_CORE_DIR . $path );
		if ( 'svg' == pathinfo( $fullpath, PATHINFO_EXTENSION ) AND file_exists( $fullpath ) ) {
			ob_start();
			require( $fullpath );
			$image = ob_get_clean();
		} else {
			$img_path = US_CORE_URI . '/' . ltrim( $path, '/' );
			$image = '<img src="' . esc_url( $img_path ) . '" alt="' . esc_attr( $filename_title ) . '">';
		}
		if ( $image ) {
			$preview = '<span class="usof-imgradio-image">' . $image . '</span>';
		}
	}

	unset( $path, $fullpath, $image, $img_path );

	// Input atts
	$input_atts = array(
		'type' => 'radio',
		'name' => '_' . $name . '_',
		'value' => $filename,
	);

	// Output html code
	$output .= '<label title="' . esc_attr( $filename_title ) . '">';
	$output .= '<input' . us_implode_atts( $input_atts ) . checked( $value, $filename, FALSE ) . '>';
	$output .= $preview;
	if ( $filename_title !== '' ) {
		$output .= '<span class="usof-imgradio-label">' . strip_tags( $filename_title ) . '</span>';
	}
	$output .= '</label>';
}

// The hidden field is required for correct work in WPBakery and is also
// used as the value for the USOF field
$hidden = array(
	'type' => 'hidden',
	'name' => $name,
	'class' => $classes,
	'value' => $value,
);
$output .= '<input' . us_implode_atts( $hidden ) . '>';
$output .= '</div>';

echo $output;
