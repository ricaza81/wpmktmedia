<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Backup
 *
 * Store / restore options backup
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

$backup = defined( 'US_THEMENAME' ) ? get_option( 'usof_backup_' . US_THEMENAME ) : FALSE;

$output = '<div class="usof-backup">';
$output .= '<div class="usof-backup-status">';
if ( $backup AND is_array( $backup ) AND isset( $backup['time'] ) ) {
	$backup_time = strtotime( $backup['time'] ) + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
	$output .= __( 'Last Backup', 'us' ) . ': <span>' . date_i18n( 'F j, Y - G:i T', $backup_time ) . '</span>';
} else {
	$output .= __( 'No backups yet', 'us' );
}
$output .= '</div>';
$output .= '<div class="usof-button type_backup"><span>' . __( 'Backup Options', 'us' ) . '</span>';
$output .= '<span class="usof-preloader"></span>';
$output .= '</div>';
$output .= '<div class="usof-button type_restore"';
if ( ! $backup OR ! is_array( $backup ) OR ! isset( $backup['usof_options'] ) ) {
	$output .= ' style="display: none"';
}
$output .= '><span class="usof-button-label">' . __( 'Restore Options', 'us' ) . '</span>';
$output .= '<span class="usof-preloader"></span>';
$output .= '</div>';
$i18n = array(
	'restore_confirm' => __( 'Are you sure want to restore options from the backup?', 'us' ),
);
$output .= '<div class="usof-backup-i18n"' . us_pass_data_to_js( $i18n ) . '></div>';
$output .= '</div>';

echo $output;
