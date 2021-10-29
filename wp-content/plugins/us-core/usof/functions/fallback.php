<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'us_assets_option_value_fallback' ) ) {
	/*
	 * Starting from version 7.5 'assets' option will have a new format.
	 * In this function we will provide a fallback for it.
	 */
	function us_assets_option_value_fallback( $value ) {

		// If value is set for the option and we detect it is in old format, we should transform it.
		if ( is_array( $value ) AND ( reset( $value ) !== 0 ) AND ( reset( $value ) !== 1 ) ) {
			$assets_config = us_config( 'assets', array() );
			$new_value = array();

			// First check / uncheck assets from older versions
			foreach ( $assets_config as $component => $component_atts ) {
				if ( isset( $component_atts['hidden'] ) AND $component_atts['hidden'] ) {
					continue;
				}
				$new_value[ $component ] = in_array( $component, $value ) ? 1 : 0;
			}

			// Then force check assets added since 7.5
			$new_assets = array(
				'grid_filter',
				'grid_templates',
				'grid_pagination',
				'grid_popup',
				'hor_parallax',
				'hwrapper',
				'image_gallery',
				'image_slider',
				'magnific_popup',
				'post_elements',
				'post_navigation',
				'simple_menu',
				'text',
				'ver_parallax',
				'vwrapper',
				'wp_widgets',
			);
			foreach ( $new_assets as $component ) {
				$new_value[ $component ] = 1;
			}

			return $new_value;
		}

		// If value is empty or format is OK return it as is
		return $value;
	}
}

if ( ! function_exists( 'usof_load_options_once_fallback' ) ) {
	function usof_load_options_once_fallback( $usof_options ) {
		if ( isset( $usof_options['assets'] ) ) {
			$usof_options['assets'] = us_assets_option_value_fallback( $usof_options['assets'] );
		}
		return $usof_options;
	}
	add_filter( 'usof_load_options_once', 'usof_load_options_once_fallback' );
}