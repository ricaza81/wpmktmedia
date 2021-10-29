<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output html element
 *
 * @var $content        string
 * @var $design_options array
 * @var $classes        string
 * @var $id             string
 */

$_atts['class'] = 'w-html';
$_atts['class'] .= isset( $classes ) ? $classes : '';

echo '<div' . us_implode_atts( $_atts ) . '>';
echo do_shortcode( rawurldecode( base64_decode( $content ) ) );
echo '</div>';
