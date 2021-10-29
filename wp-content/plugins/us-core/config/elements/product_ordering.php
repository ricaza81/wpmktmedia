<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: product_ordering
 */

$design_options_params = us_config( 'elements_design_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Product ordering', 'us' ),
	'icon' => 'fas fa-sort-amount-down',
	'place_if' => class_exists( 'woocommerce' ),
	'category' => __( 'Post Elements', 'us' ),
	'params' => us_set_params_weight( $design_options_params ),
	'show_settings_on_create' => FALSE, // used in WPBakery editor
);
