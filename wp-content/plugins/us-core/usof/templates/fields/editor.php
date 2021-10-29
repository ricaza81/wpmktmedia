<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: WordPress Editor
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['placeholder'] string Field placeholder
 *
 * @var   $value string Current value
 *
 */

/**
 * Default editor settings
 * @docs: https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/
 */
$default_editor_settings = array(
	'textarea_name' => esc_attr( $name ), // should not be changed
	'editor_height' => 300, // in pixels, takes precedence and has no default value
	'default_editor' => 'html', // preventing unwanted init
);

$editor_settings = us_arr_path( $field, 'editor_settings', array() );
$editor_settings = array_merge( $default_editor_settings, $editor_settings );

echo '<div class="usof-editor">';

echo '<script type="template/html" class="usof-editor-template">';
wp_editor( '', $id, $editor_settings );
echo '</script>';

// Get mceInit for init the wp_editor via load AJAX
if ( $context === 'header' AND function_exists( 'usof_extract_tinymce_options' ) ) {
	$_mceInit = (string) usof_extract_tinymce_options( $id, $editor_settings );
	echo "<script>window.tinyMCEPreInit.mceInit['{$id}'] = {$_mceInit};</script>";
}

// Height should be set through textarea's style
echo '<textarea name="' . $name . '" data-editor-id="' . $id . '" style="height: ' . $editor_settings[ 'editor_height' ] . 'px;">' . esc_textarea( $value ) . '</textarea>';
echo '<div class="usof-editor-settings hidden"' . us_pass_data_to_js( $editor_settings ) . '></div>';

echo '</div>';
