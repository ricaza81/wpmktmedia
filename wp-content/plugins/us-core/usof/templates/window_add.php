<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output elements list to choose from
 * @var array $elements Set of elements to display
 */

$elements = isset($elements) ? $elements : us_config( 'header-settings.elements', array() );

$output = '<div class="us-bld-window for_adding"><div class="us-bld-window-h"><div class="us-bld-window-header">';
$output .= '<div class="us-bld-window-title">' . __( 'Add element', 'us' ) . '</div>';
$output .= '<div class="us-bld-window-closer" title="' . us_translate( 'Close' ) . '"></div></div>';
$output .= '<div class="us-bld-window-body"><ul class="us-bld-window-list">';
foreach ( $elements as $name ) {
	$elm = us_config( 'elements/' . $name );
	if ( isset( $elm['place_if'] ) AND $elm['place_if'] === FALSE ) {
		continue;
	}
	$output .= '<li class="us-bld-window-item type_' . $name . '" data-name="' . $name . '"><div class="us-bld-window-item-h">';
	$output .= '<div class="us-bld-window-item-icon';
	if ( ! empty( $elm['icon'] ) ) {
		$output .= ' ' . $elm['icon'];
	}
	$output .= '"></div>';
	$output .= '<div class="us-bld-window-item-title">' . ( isset( $elm['title'] ) ? $elm['title'] : $name ) . '</div>';
	$output .= '</div></li>';
}
$output .= '</ul></div></div></div>';

echo $output;
