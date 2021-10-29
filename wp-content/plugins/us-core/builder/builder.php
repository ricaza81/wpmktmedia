<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The entry point for US Builder
 */

// Full link to the builder dir
if ( ! defined( 'US_BUILDER_DIR' ) ) {
	define( 'US_BUILDER_DIR', US_CORE_DIR . 'builder' );
}

// Link to builder directory
if ( ! defined( 'US_BUILDER_URL' ) ) {
	define( 'US_BUILDER_URL', US_CORE_URI . '/builder' );
}

// TODO: define exact list of classes that are allowed to load
// Autoload for the USBuilder* classes
if ( ! function_exists( 'usbuilder_autoload' ) ) {
	function usbuilder_autoload( $class_name ) {
		if ( strpos( $class_name, 'USBuilder' ) !== 0 ) {
			return;
		}
		$file_path = US_BUILDER_DIR . '/classes/' . str_replace( '_', '/', $class_name ) . '.php';
		if ( file_exists( $file_path ) ) {
			require $file_path;
		}
	}
}
spl_autoload_register( 'usbuilder_autoload' );

require_once US_BUILDER_DIR . '/classes/USBuilder.php';

// Initializing the US Builder
if ( ! function_exists( 'us_init_builder' ) ) {
	add_action( 'init', 'us_init_builder' );
	function us_init_builder() {
		new USBuilder;
	}
}
