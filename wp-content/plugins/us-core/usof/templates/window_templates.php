<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output elements list to choose from
 *
 * @var $body string Optional predefined body
 */

$templates = us_config( 'grid-templates', array() );

if ( ! isset( $body ) ) {
	$body = '<ul class="us-bld-window-list">';
	foreach ( $templates as $name => $template ) {
		$template_title = isset( $template['title'] ) ? $template['title'] : ucfirst( $name );
		$template = us_fix_grid_settings( $template );

		if ( isset( $template['group'] ) ) {
			$body .= '</ul>';
			$body .= '<div class="us-bld-window-list-title">' . $template['group'] . '</div>';
			$body .= '<ul class="us-bld-window-list">';
		}
		$body .= '<li data-name="' . esc_attr( $name ) . '" class="us-bld-window-item ' . $name . '">';
		$body .= '<div class="us-bld-window-item-h">';
		if ( isset( $template['hover_effect'] ) ) {
			$body .= '<img class="hover_state" src="' . US_CORE_URI . '/admin/img/grid-templates/' . $name . '_hover.jpg" alt="">';
		}
		$body .= '<img class="default_state" src="' . US_CORE_URI . '/admin/img/grid-templates/' . $name . '.jpg" alt="' . $name . '">';
		$body .= '<div class="us-bld-window-item-data hidden"' . us_pass_data_to_js( $template ) . '></div>';
		$body .= '</div>';
		$body .= '<div class="us-bld-window-item-popup">' . $template_title . '</div>';
		$body .= '</li>';
	}
	$body .= '</ul>';
}

$output = '<div class="us-bld-window for_templates type_gtemplate"><div class="us-bld-window-h">';
$output .= '<div class="us-bld-window-header"><div class="us-bld-window-title">' . __( 'Grid Layout Templates', 'us' ) . '</div><div class="us-bld-window-closer" title="' . us_translate( 'Close' ) . '"></div></div>';
$output .= '<div class="us-bld-window-body">';
$output .= $body;
$output .= '<span class="usof-preloader"></span>';
$output .= '</div>';
$output .= '</div></div>';

echo $output;
