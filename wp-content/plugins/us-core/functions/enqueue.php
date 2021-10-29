<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Embed Google Fonts
 */
if ( ! us_get_option( 'optimize_assets', 0 ) ) {
	add_action( 'wp_enqueue_scripts', 'us_enqueue_fonts' );
} elseif ( ! us_get_option( 'include_gfonts_css', 0 ) ) {
	add_action( 'wp_enqueue_scripts', 'us_enqueue_fonts' );
}

/**
 * Embed CSS files
 */
add_action( 'wp_enqueue_scripts', 'us_styles', 12 );
function us_styles() {
	global $us_template_directory_uri;
	if ( empty( $us_template_directory_uri ) ) {
		return;
	}

	$assets_config = us_config( 'assets', array() );

	// Embed all CSS components, when DEV mode is enabled
	if ( defined( 'US_DEV' ) ) {
		foreach ( $assets_config as $component => $component_atts ) {
			if ( ! empty( $component_atts['css'] ) ) {
				wp_enqueue_style( 'us-' . $component, $us_template_directory_uri . $component_atts['css'], array(), US_THEMEVERSION, 'all' );
			}
		}

		// Generate and embed single CSS file
	} elseif ( us_get_option( 'optimize_assets', 0 ) ) {

		// Locate asset file
		$css_file = us_get_asset_file( 'css' );

		// If the file doesn't exist
		if ( ! file_exists( $css_file ) ) {

			// try to create the styles file
			us_generate_asset_file( 'css' );

			// if create attempt failed
			if ( ! file_exists( $css_file ) ) {

				// switch the Optimize option off
				global $usof_options;
				usof_load_options_once();
				$updated_options = $usof_options;
				$updated_options['optimize_assets'] = 0;
				usof_save_options( $updated_options );

				// and load all styles to make sure site looks as it should
				foreach ( $assets_config as $component => $component_atts ) {
					if ( ! empty( $component_atts['css'] ) ) {
						wp_enqueue_style( 'us-' . $component, $us_template_directory_uri . $component_atts['css'], array(), US_THEMEVERSION, 'all' );
					}
				}
			}
		}

		// Embed generated file
		if ( file_exists( $css_file ) ) {
			$css_file_version = hash_file( 'crc32b', $css_file );
			$css_file_url = us_get_asset_file( 'css', TRUE );
			wp_enqueue_style( 'us-theme', $css_file_url, array(), $css_file_version, 'all' );
		}

	} else {
		// Common CSS file in other cases
		wp_enqueue_style( 'us-style', $us_template_directory_uri . '/css/style.min.css', array(), US_THEMEVERSION, 'all' );
	}

	// Styles only for AMP version of pages
	if ( us_amp() ) {
		wp_enqueue_style( 'us-amp', $us_template_directory_uri . '/common/css/plugins/amp.css', array(), US_THEMEVERSION, 'all' );
	}

	// Ripple effect CSS file if enabled
	if ( ! us_amp() AND us_get_option( 'ripple_effect', 0 ) AND ! us_get_option( 'optimize_assets', 0 ) ) {
		wp_enqueue_style( 'us-ripple', $us_template_directory_uri . '/common/css/base/ripple.css', array(), US_THEMEVERSION, 'all' );
	}

	// Remove WP Block Editor styles if set
	if ( ! us_get_option( 'block_editor' ) ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wc-block-style' );
	}
}

// RTL CSS file needed enqueued separately with higher priority
add_action( 'wp_enqueue_scripts', 'us_rtl_styles', 15 );
function us_rtl_styles() {
	global $us_template_directory_uri;
	if ( empty( $us_template_directory_uri ) ) {
		return;
	}
	$min_ext = defined( 'US_DEV' ) ? '' : '.min';

	if ( is_rtl() ) {
		wp_enqueue_style( 'us-rtl', $us_template_directory_uri . '/common/css/rtl' . $min_ext . '.css', array(), US_THEMEVERSION, 'all' );
	}
}

// Child theme styles
add_action( 'wp_enqueue_scripts', 'us_custom_styles', 18 );
function us_custom_styles() {
	if ( is_child_theme() ) {
		global $us_stylesheet_directory_uri;
		wp_enqueue_style( 'theme-style', $us_stylesheet_directory_uri . '/style.css', array(), US_THEMEVERSION, 'all' );
	}
}

// Disable jQuery migrate script
if ( us_get_option( 'disable_jquery_migrate', 1 ) ) {
	add_action( 'wp_default_scripts', 'us_dequeue_jquery_migrate' );
}
function us_dequeue_jquery_migrate( &$wp_scripts ) {
	if ( is_admin() ) {
		return;
	}
	$jquery_core_obj = $wp_scripts->registered['jquery-core'];
	$wp_scripts->remove( 'jquery' );
	$wp_scripts->add( 'jquery', FALSE, array( 'jquery-core' ), $jquery_core_obj->ver );
}

// Move jQuery scripts to the footer
if ( us_get_option( 'jquery_footer', 1 ) ) {
	add_action( 'wp_default_scripts', 'us_move_jquery_to_footer' );
}
function us_move_jquery_to_footer( $wp_scripts ) {
	if ( is_admin() ) {
		return;
	}
	$wp_scripts->add_data( 'jquery', 'group', 1 );
	$wp_scripts->add_data( 'jquery-core', 'group', 1 );
	$wp_scripts->add_data( 'jquery-migrate', 'group', 1 );
}

/**
 * Embed JS files
 */
add_action( 'wp_enqueue_scripts', 'us_jscripts' );
function us_jscripts() {
	global $us_template_directory_uri;
	if ( empty( $us_template_directory_uri ) OR us_amp() ) {
		return;
	}

	// Link Google Maps API key
	if ( $gmaps_api_key = trim( us_get_option( 'gmaps_api_key', '' ) ) ) {
		wp_register_script( 'us-google-maps', '//maps.googleapis.com/maps/api/js?key=' . esc_attr( $gmaps_api_key ), array(), NULL, FALSE );
	} else {
		wp_register_script( 'us-google-maps', '//maps.googleapis.com/maps/api/js', array(), NULL, FALSE );
	}

	// Embed vendor JS components
	if ( ! us_get_option( 'ajax_load_js', 0 ) ) {

		// Enqueued in Grid
		wp_register_script( 'us-objectfit', $us_template_directory_uri . '/common/js/vendor/objectFitPolyfill.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
		wp_register_script( 'us-isotope', $us_template_directory_uri . '/common/js/vendor/isotope.js', array( 'jquery' ), US_THEMEVERSION, TRUE );

		// Enqueued in Grid & Image Slider
		wp_register_script( 'us-royalslider', $us_template_directory_uri . '/common/js/vendor/royalslider.js', array( 'jquery' ), US_THEMEVERSION, TRUE );

		// Enqueued in Carousel
		wp_register_script( 'us-owl', $us_template_directory_uri . '/common/js/vendor/owl.carousel.js', array( 'jquery' ), US_THEMEVERSION, TRUE );

		// Enqueued in Map
		wp_register_script( 'us-gmap', $us_template_directory_uri . '/common/js/vendor/gmaps.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
		wp_register_script( 'us-lmap', $us_template_directory_uri . '/common/js/vendor/leaflet.js', array( 'jquery' ), US_THEMEVERSION, TRUE );

		// Enqueued here (for all pages)
		wp_enqueue_script( 'us-magnific-popup', $us_template_directory_uri . '/common/js/vendor/magnific-popup.js', array( 'jquery' ), US_THEMEVERSION, TRUE );

		// Enqueued datepicker for Contact Form
		wp_enqueue_script( 'jquery-ui-datepicker' );
	}

	// Embed all JS components, when DEV mode is enabled
	if ( defined( 'US_DEV' ) ) {
		$assets_config = us_config( 'assets', array() );
		foreach ( $assets_config as $component => $component_atts ) {
			if ( isset( $component_atts['js'] ) AND isset( $component_atts['order'] ) AND $component_atts['order'] == 'top' ) {
				wp_enqueue_script( 'us-' . $component, $us_template_directory_uri . $component_atts['js'], array( 'jquery' ), US_THEMEVERSION, TRUE );
			}
		}
		foreach ( $assets_config as $component => $component_atts ) {
			if ( isset( $component_atts['js'] ) AND ! isset( $component_atts['order'] ) ) {
				wp_enqueue_script( 'us-' . $component, $us_template_directory_uri . $component_atts['js'], array( 'jquery' ), US_THEMEVERSION, TRUE );
			}
		}

		// Generate and embed single JS file
	} elseif ( us_get_option( 'optimize_assets', 0 ) ) {

		// Locate asset file
		$js_file = us_get_asset_file( 'js' );

		// If the file doesn't exist
		if ( ! file_exists( $js_file ) ) {

			// try to create the styles file
			us_generate_asset_file( 'js' );

			// if create attempt failed
			if ( ! file_exists( $js_file ) ) {

				// switch the Optimize option off
				global $usof_options;
				usof_load_options_once();
				$updated_options = $usof_options;
				$updated_options['optimize_assets'] = 0;
				usof_save_options( $updated_options );

				// and load default core file to make sure site works
				wp_enqueue_script( 'us-core', $us_template_directory_uri . '/js/us.core.min.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
			}
		}

		// Embed generated file
		if ( file_exists( $js_file ) ) {
			$js_file_version = hash_file( 'crc32b', $js_file );
			$js_file_url = us_get_asset_file( 'js', TRUE );

			wp_register_script( 'us-core', $js_file_url, array( 'jquery' ), $js_file_version, TRUE );
		} else {
			wp_register_script( 'us-core', $us_template_directory_uri . '/js/us.core.min.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
		}
		wp_enqueue_script( 'us-core' );

	} else { // Embed default core file in other cases
		wp_enqueue_script( 'us-core', $us_template_directory_uri . '/js/us.core.min.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
	}

	// Ripple effect CSS file if enabled
	if ( us_get_option( 'ripple_effect', 0 ) AND ! us_get_option( 'optimize_assets', 0 ) ) {
		$min_ext = defined( 'US_DEV' ) ? '' : '.min';
		wp_enqueue_script( 'us-ripple', $us_template_directory_uri . '/common/js/base/ripple' . $min_ext . '.js', array(), US_THEMEVERSION, TRUE );
	}
}

// Output Custom HTML before </body>
add_action( 'wp_footer', 'us_custom_html_output', 99 );
function us_custom_html_output() {
	echo us_get_option( 'custom_html', '' );
}

/**
 * Generate and cache theme options css data
 *
 * @return string
 */
function us_get_theme_options_css() {
	if ( ( $styles_css = get_option( 'us_theme_options_css' ) ) === FALSE OR $styles_css == '' OR defined( 'US_DEV' ) ) {
		$styles_css = us_minify_css( us_get_template( 'templates/css-theme-options' ) );
		if ( ! defined( 'US_DEV' ) ) {
			update_option( 'us_theme_options_css', $styles_css, TRUE );
		}
	}

	return $styles_css;
}
