<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Post Views Counter Support
 *
 * https://wordpress.org/plugins/post-views-counter/
 */

if ( ! function_exists( 'us_pvc_enqueue_styles' ) ) {
	add_filter( 'pvc_enqueue_styles', 'us_pvc_enqueue_styles', 100 );
	/**
	 * Removing styles from the Post Views counter plugin
	 *
	 * @return bool
	 */
	function us_pvc_enqueue_styles() {
		if ( us_get_option( 'optimize_assets', 0 ) AND is_plugin_active( 'post-views-counter/post-views-counter.php' ) ) {
			return FALSE;
		}
		return TRUE;
	}
}
