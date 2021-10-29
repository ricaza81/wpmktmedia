<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Upload
 *
 * Upload some file with the specified settings.
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['preview_type'] string 'image' / 'text'
 * @param $field ['is_multiple'] bool
 *
 * @var   $field array Field options
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $value mixed Either full path to the file, or ID from WordPress media uploads
 */
$field['preview_type'] = isset( $field['preview_type'] ) ? $field['preview_type'] : 'image';
$field['is_multiple'] = isset( $field['is_multiple'] ) ? $field['is_multiple'] : FALSE;

$files = array();

// Helper variable to differ a default value and the one set by user
$has_value = FALSE;

// Transform the value to files array
if ( ! empty( $value ) ) {
	$img_size = $field['is_multiple'] ? 'thumbnail' : 'medium';
	$files_ids = explode( ',', $value );

	foreach ( $files_ids as $file_id ) {

		// For image preview type get url to the thumbnail of an image
		if ( $field['preview_type'] == 'image' ) {
			if ( $url = wp_get_attachment_image_url( $file_id, $img_size ) ) {
				$files[] = array( 'id' => $file_id, 'url' => $url );
			} elseif ( count( $files ) == 1 ) {
				// Fallback for value as single image URL
				$files[] = array( 'id' => -1, 'url' => $file_id );
			}

			// For other cases get url to the file itself
		} elseif ( $url = wp_get_attachment_url( $file_id ) ) {
			$files[] = array( 'id' => $file_id, 'url' => $url );
		}
	}

	if ( count( $files ) > 0 ) {
		$has_value = TRUE;
	}
}

// Output the field
$output = '<div class="usof-upload preview_' . $field['preview_type'];
if ( $field['is_multiple'] ) {
	$output .= ' is_multiple';
}
$output .= '">';
$output .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';

// Keep default image in a hidden field to show it after clearing value via JS
if ( $field['preview_type'] == 'image' AND ! empty( $field['std'] ) ) {
	$output .= '<input type="hidden" name="placeholder" value="' . $field['std'] . '">';
}

$output .= '<div class="usof-upload-preview' . ( ( $has_value OR ! empty( $field['std'] ) ) ? '' : ' hidden' ) . '">';

// Output files html
if ( $has_value ) {
	foreach ( $files as $file ) {
		$output .= '<div class="usof-upload-preview-file" data-id="' . esc_attr( $file['id'] ) . '">';

		// Output img tag
		if ( $field['preview_type'] == 'image' ) {
			$output .= '<img src="' . esc_attr( $file['url'] ) . '" alt="" loading="lazy">';

			// Output file name
		} elseif ( $field['preview_type'] == 'text' ) {
			$output .= '<span>' . basename( $file['url'] ) . '</span>';
		}
		$output .= '<div class="ui-icon_delete" title="' . us_translate( 'Delete' ) . '"></div>';
		$output .= '</div>';
	}

	// If there's no files, check if image placeholder is present and output it
} elseif ( ! empty( $field['std'] ) AND $field['preview_type'] == 'image' ) {
	$output .= '<div class="usof-upload-preview-file">';
	$output .= '<img src="' . esc_attr( $field['std'] ) . '" alt="" loading="lazy">';
	$output .= '</div>';
}

$output .= '</div>';

// Output "Add" button
$output .= '<div class="ui-icon_add"></div>';

// Internationalization
$i18n = array(
	'delete' => us_translate( 'Delete' ),
);
$output .= '<div class="usof-upload-i18n hidden"' . us_pass_data_to_js( $i18n ) . '></div>';
$output .= '</div>';

echo $output;

unset( $has_value );
