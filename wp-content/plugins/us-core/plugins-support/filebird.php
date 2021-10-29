<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * FileBird Support.
 *
 * @link https://wordpress.org/plugins/filebird/
 */

if ( ! class_exists( '\FileBird\Classes\PageBuilders' ) ) {
	return;
}

if ( ! function_exists( 'usb_filebird_enqueue_scripts' ) ) {
	/**
	 * Add FileBird assets to the USBuilder page.
	 */
	function usb_filebird_enqueue_scripts() {
		if ( class_exists( '\FileBird\Classes\PageBuilders' ) ) {
			\FileBird\Classes\PageBuilders::getInstance()->enqueueScripts();
		}
	}
	add_action( 'usb_enqueue_assets_for_builder', 'usb_filebird_enqueue_scripts', 1 );
}
