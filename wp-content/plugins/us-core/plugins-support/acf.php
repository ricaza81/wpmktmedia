<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Advanced Custom Fields
 *
 * @link https://www.advancedcustomfields.com/
 */

if ( ! class_exists( 'ACF' ) ) {
	return;
}

// Register Google Maps API key
// https://www.advancedcustomfields.com/resources/google-map/
function us_acf_google_map_api( $api ) {

	$api['key'] = trim( esc_attr( us_get_option( 'gmaps_api_key', '' ) ) );

	return $api;
}

add_filter( 'acf/fields/google_map/api', 'us_acf_google_map_api' );
