<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'rocket_init' ) ) {
	return FALSE;
}

if ( ! function_exists( 'us_exclude_delayed_assets' ) ) {
	/**
	 * Exclude theme assets from "Delay JavaScript execution"
	 */
	add_filter( 'rocket_delay_js_exclusions', 'us_exclude_delayed_assets' );
	function us_exclude_delayed_assets( $excluded ) {
		$exclude = array(
			'/jquery-?[0-9.](.*)(.min|.slim|.slim.min)?.js',
			'maps.googleapis.com',
		);

		if ( us_get_option( 'optimize_assets', 0 ) ) {
			$exclude[] = us_get_asset_file( 'js', TRUE );
		} else {
			$exclude[] = 'us.core.min.js';
		}

		return array_merge( $excluded, $exclude );
	}
}
