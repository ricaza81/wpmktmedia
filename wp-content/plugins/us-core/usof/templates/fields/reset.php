<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Transfer
 *
 * Transfer theme options data
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 *
 * @var   $value string Current value
 */

$output = '<div class="usof-reset">';
$output .= '<div class="usof-button type_reset"><span>' . __( 'Reset Options', 'us' ) . '</span>';
$output .= '<span class="usof-preloader"></span></div>';
$output .= '</div>';

$i18n = array(
	'reset_confirm' => __( 'Are you sure want to reset all the options to default values?', 'us' ),
	'reset_complete' => __( 'Options were reset', 'us' ),
);
$output .= '<div class="usof-form-row-control-i18n"' . us_pass_data_to_js( $i18n ) . '></div>';

echo $output;
