<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Plugin Name: UpSolution Core
 * Plugin URI: https://help.us-themes.com/impreza/us-core/
 * Description: Adds plenty of features for Impreza and Zephyr themes.
 * Author: UpSolution
 * Author URI: https://us-themes.com/
 * Version: 7.13
 **/

// Global variables for plugin usage
$uscore_dir = plugin_dir_path( __FILE__ );
$uscore_uri = plugins_url( '', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

if ( ! defined( 'US_CORE_DIR' ) ) {
	define( 'US_CORE_DIR', $uscore_dir );
}
if ( ! defined( 'US_CORE_URI' ) ) {
	define( 'US_CORE_URI', $uscore_uri );
}

$uscore_data = get_file_data( __FILE__, array( 'Version' => 'Version' ), FALSE );
$uscore_version = $uscore_data['Version'] ? $uscore_data['Version'] : FALSE;
if ( $uscore_version AND ! defined( 'US_CORE_VERSION' ) ) {
	define( 'US_CORE_VERSION', $uscore_version );
}

// Reinit files location
global $us_files_search_paths, $us_file_paths;
unset( $us_files_search_paths );
unset( $us_file_paths );

require_once US_CORE_DIR . 'functions/init.php';
