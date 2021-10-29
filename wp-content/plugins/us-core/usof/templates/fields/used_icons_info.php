<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Used icons info
 *
 * @var $field['button_text'] string The button text
 * @var $field['description'] string Description near the button
 */

$_atts = array(
	'class' => 'usof-icons-info',
	'data-nonce' => wp_create_nonce( 'usof_ajax_used_icons_info' ),
);

$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= '<div class="usof-button type_show_used_icons">';
$output .= '<span class="usof-button-text">' . strip_tags( $field['button_text'] ) . '</span>';
$output .= '<span class="usof-preloader"></span>';
$output .= '</div>';

if ( ! empty( $field['$description'] ) ) {
	$output .= '<div class="usof-form-row-desc">';
	$output .= '<div class="usof-form-row-desc-icon"></div>';
	$output .= '<div class="usof-form-row-desc-text">' . $field['$description'] . '</div>';
	$output .= '</div>';
}

$output .= '<div class="usof-form-wrapper for_used_icons hidden"></div>';
$output .= '</div>';

echo $output;
