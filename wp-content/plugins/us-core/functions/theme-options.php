<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options: USOF + UpSolution extendings
 *
 * Should be included in global context.
 */

add_action( 'usof_after_save', 'us_generate_asset_files' );
add_action( 'usof_ajax_mega_menu_save_settings', 'us_generate_asset_files' );
add_action( 'update_option_siteurl', 'us_generate_asset_files' );
add_action( 'update_option_home', 'us_generate_asset_files' );

function us_generate_asset_files() {
	$subdomains = array();
	// Empty array without code for default language
	$language_domains[] = '';

	// Is WPML installed and activated
	if ( class_exists( 'SitePress' ) AND has_filter( 'us_tr_setting' ) ) {
		$default_lang = apply_filters( 'us_tr_default_language', NULL );
		// get negotiation type, '2' is different domain per language
		$language_negotiation_type = apply_filters( 'us_tr_setting', 'language_negotiation_type', FALSE );

		// Create extra styles and js only if selected different domain per language negotiation type
		if ( ! empty( $default_lang ) AND $language_negotiation_type == '2' ) {
			$language_domains = array_merge( $language_domains, apply_filters( 'us_tr_setting', 'language_domains', array() ) );
		}
	}

	// Support for subdomain asset files for Polylang
	if ( function_exists( 'pll_languages_list' ) ) {
		$default_lang = apply_filters( 'us_tr_default_language', NULL );
		$pll_domains = apply_filters( 'us_tr_setting', 'domains', array() );

		// get negotiation type, 3 is different domain per language
		$language_negotiation_type = apply_filters( 'us_tr_setting', 'force_lang', 0 );

		if ( $language_negotiation_type == 3 ) {
			foreach ( $pll_domains as $pll_key => $pll_domain ) {
				// Remove protocols for consistency
				$pll_domains[ $pll_key ] = preg_replace( '#^https?://#', '', $pll_domain );

				if ( $pll_key == $default_lang ) {
					// Remove default domain for consistency
					$pll_domains[ $pll_key ] = '';
				}
			}

			$language_domains = $pll_domains;
		}
	}

	foreach ( $language_domains as $custom_language_domain ) {

		// Temporarily replacing template directory URI for CSS file for custom language domain
		if ( $custom_language_domain != '' ) {
			global $us_template_directory_uri;

			// Generating URL of custom language domain
			$custom_language_domain_url = '';
			if ( us_get_option( 'keep_url_protocol', 1 ) ) {
				$custom_language_domain_url .= is_ssl() ? 'https:' : 'http:';
			}
			$custom_language_domain_url .= '//' . $custom_language_domain;

			// Getting URL for main language domain
			$main_language_domain_url = get_site_url();

			// Storing template directory URI in temporary variable
			$default_us_template_directory_uri = $us_template_directory_uri;

			// Replacing main language domain with custom one in template directory URI
			$us_template_directory_uri = str_replace( $main_language_domain_url, $custom_language_domain_url, $us_template_directory_uri );
		}

		us_generate_asset_file( 'css', $custom_language_domain );
		us_generate_asset_file( 'js', $custom_language_domain );

		// Restoring original template directory URI
		if ( $custom_language_domain != '' ) {
			$us_template_directory_uri = $default_us_template_directory_uri;
		}
	}

	us_generate_editor_styles();
}

/**
 * Update assets files on "US CORE" plugin update
 */
add_action( 'upgrader_process_complete', 'us_core_updade_action', 10, 2 );
function us_core_updade_action( $upgrader_object, $options ) {
	$current_plugin_name = plugin_basename( __FILE__ );

	if ( $options['action'] == 'update' && $options['type'] == 'plugin' AND ! empty( $options['plugins'] ) AND is_array( $options['plugins'] ) ) {
		foreach ( $options['plugins'] as $plugin_name ) {
			if ( $plugin_name == $current_plugin_name ) {
				us_generate_asset_files();
			}
		}
	}
}

/* Get asset file path */
function us_get_asset_file( $ext, $url = FALSE, $custom_language_domain = '' ) {
	if ( empty( $ext ) ) {
		return FALSE;
	}

	// Set specific file name for editor styles
	if ( $ext == 'tinymce' ) {
		$file_name = 'tinymce-editor-style';
		$ext = 'css';

	} elseif ( $ext == 'gutenberg' ) {
		$file_name = 'gutenberg-editor-style';
		$ext = 'css';

		// Set file name based on site name
	} else {
		$site_url_parts = parse_url( site_url() );
		$file_name = ( ! empty( $site_url_parts['host'] ) ) ? $site_url_parts['host'] : '';
		$file_name .= ( ! empty( $site_url_parts['path'] ) ) ? str_replace( '/', '_', $site_url_parts['path'] ) : '';
	}

	$file = '';
	$upload_dir = wp_get_upload_dir();

	if ( $url ) {
		$file = $upload_dir['baseurl'] . '/us-assets/' . $file_name . '.' . $ext;
		// remove protocols for better compatibility with caching plugins and services
		if ( ! us_get_option( 'keep_url_protocol', 0 ) ) {
			$file = str_replace( array( 'http:', 'https:' ), '', $file );
		}
	} else {
		// Create file directory
		$file_dir = wp_normalize_path( $upload_dir['basedir'] . '/us-assets' );
		if ( ! is_dir( $file_dir ) ) {
			wp_mkdir_p( trailingslashit( $file_dir ) );
		}
		//check is this file for custom domain
		$file_name = ! empty( $custom_language_domain ) ? $custom_language_domain : $file_name;
		$file = trailingslashit( $file_dir ) . $file_name . '.' . $ext;
	}

	return $file;
}

/**
 * Generate main FRONTEND assets (JS or CSS)
 *
 * @param string $ext file type (JS/CSS)
 * @param string $custom_language_domain
 * @return bool whether file was successfully created
 */
function us_generate_asset_file( $ext, $custom_language_domain = '' ) {
	if ( empty( $ext ) ) {
		return FALSE;
	}

	global $usof_options, $us_template_directory;
	usof_load_options_once();

	if ( us_arr_path( $usof_options, 'optimize_assets', FALSE ) ) {
		$content = $first_content = '';

		// Add assets specified in Theme Options
		$assets_config = us_config( 'assets', array() );
		$icon_css_files = array();
		foreach ( $assets_config as $component => $component_atts ) {

			// Skip assets that do not meet requiremets specified in their config
			if ( isset( $component_atts['apply_if'] ) AND ! $component_atts['apply_if'] ) {
				continue;
			}

			// Skip assets that have no files for the current file extension
			if ( empty( $component_atts[ $ext ] ) ) {
				continue;
			}

			// Include asset's files if it is included by default or checked by user in Theme Options
			if (
				! empty( $component_atts['hidden'] )
				OR ! isset( $usof_options['assets'] )
				OR ( ! isset( $usof_options['assets'][ $component ] ) OR $usof_options['assets'][ $component ] == 1 )
			) {
				$asset_filename = $us_template_directory . $component_atts[ $ext ];
				if ( $ext == 'js' ) {
					$asset_filename = str_replace( '.js', '.min.js', $asset_filename );
				}
				// Move assets with "order" to the top of generated file
				if ( isset( $component_atts['order'] ) AND $component_atts['order'] == 'top' ) {
					$first_content .= file_get_contents( $asset_filename );
					$first_content .= ( $ext == 'js' ) ? ';' : '';
				} else {
					$content .= file_get_contents( $asset_filename );
					$content .= ( $ext == 'js' ) ? ';' : '';
				}
			}

			// Get icon CSS files which used for generating icons styles AND disabled in Theme Options
			if ( ! empty( $component_atts['search_icons'] ) AND empty( $usof_options['assets'][ $component ] ) ) {
				$icon_css_files[ $component ] = us_arr_path( $component_atts, 'css' );
			}
		}

		// Combine first content with other
		$content = $first_content . $content;

		// Add "Ripple Effect" assets separately, if option is enabled
		if ( $usof_options['ripple_effect'] ) {
			$min = ( $ext == 'js' ) ? 'min.' : '';
			$content .= file_get_contents( $us_template_directory . '/common/' . $ext . '/base/ripple.' . $min . $ext );
		}

		// For CSS
		if ( $ext == 'css' ) {

			// Prepend Google Fonts styles
			if ( $usof_options['include_gfonts_css'] ) {
				$options = array(
					'http' => array(
						'method' => "GET",
						'header' => "Accept-language: en\r\n" .
							"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36\r\n",
					),
				);
				$context = stream_context_create( $options );

				if ( $google_fonts_content = file_get_contents( us_enqueue_fonts( TRUE ), FALSE, $context ) ) {
					$content = $google_fonts_content . $content;
				}
			}

			// add theme-options styles
			delete_option( 'us_theme_options_css' );
			$content .= us_get_template( 'templates/css-theme-options' );

			// add user custom styles
			if ( ( $us_custom_css = us_get_option( 'custom_css', '' ) ) != '' ) {
				$content .= $us_custom_css;
			}

			// Add styles of only USED ICONS from Font Awesome CSS files
			if (
				! empty( $icon_css_files )
				AND $used_icons = US_Get_Used_Icons::get_data( 'icons' )
			) {
				$icons_css = array();
				foreach ( $icon_css_files as $fonticon_name => $filepath ) {

					// Pattern for extracting all icons from a CSS file
					$_pattern = '/((\.fad)?\.fa-([\w\-\_]+):([before|after]+){([^\}]+)})/';
					if (
						$file_content = file_get_contents( $us_template_directory . $filepath )
						AND preg_match_all( $_pattern, $file_content, $matches )
					) {
						$keys = array_flip( $matches[3] );
						$icons_styles = $matches[0];

						foreach ( $used_icons as $icon ) {
							if ( strpos( $icon, '|' ) === FALSE ) {
								continue;
							}

							// Get the icon_set and icon_name of the icon
							$_icon = explode( '|', $icon, 2 );
							array_map( 'trim', $_icon );
							list( $icon_set, $icon_name ) = $_icon;

							// Skip "material" icons, because they don't need css styles
							if ( $icon_set === 'material' ) {
								continue;
							}

							// Skip fallback icons for Solid, Regular, Light icon sets
							if ( in_array(
									$icon_set, array(
										'fas',
										'far',
										'fal',
									)
								) AND us_is_fallback_icon( $icon_name ) ) {
								continue;
							}

							// Skip "font-awesome-duotone.css" file, if the icon is not "Duotone"
							if ( $icon_set !== 'fad' AND $fonticon_name == 'font-awesome-duotone' ) {
								continue;
							}

							// Skip "Duotone" icons styles, if "Font Awesome Duotone" set is disabled
							if ( $icon_set === 'fad' AND $usof_options['icons_fad'] == 'none' ) {
								continue;
							}

							// Add css code for every used icon, which exists in CSS file
							if ( isset( $keys[ $icon_name ] ) ) {
								$icon_index = $keys[ $icon_name ];
								$icons_css[] = $icons_styles[ $icon_index ];
							}
						}

						unset( $keys, $icons_styles );
					}
				}

				// Add used icons styles to main file
				if ( $icons_css ) {
					$icons_css = array_unique( $icons_css );
					$content .= implode( '', $icons_css );
				}
			}

			// minify
			$content = us_minify_css( $content );
		}

		// Break if content is empty
		if ( empty( $content ) ) {
			return FALSE;
		}

		// Locate asset file
		$file = us_get_asset_file( $ext, FALSE, $custom_language_domain );

		// Generate file in directory
		$handle = @fopen( $file, 'w' );
		if ( $handle ) {
			if ( ! fwrite( $handle, $content ) ) {
				return FALSE;
			}
			fclose( $handle );

			return TRUE;
		}

		return FALSE;

	} elseif ( $ext == 'css' ) {
		update_option( 'us_theme_options_css', us_minify_css( us_get_template( 'templates/css-theme-options' ) ), TRUE );
	}

	return FALSE;
}

/* Generate 2 CSS files, for Gutenberg and for TinyMCE */
function us_generate_editor_styles() {
	foreach ( array( 'tinymce', 'gutenberg' ) as $editor ) {

		// Get styles from the template
		$content = us_get_template( 'templates/css-editor-style', array( 'editor' => $editor ) );

		// Minify styles
		$content = us_minify_css( $content );

		// Break if content is empty
		if ( empty( $content ) ) {
			return FALSE;
		}

		// Locate asset file
		$file = us_get_asset_file( $editor );

		// Generate file in directory
		$handle = @fopen( $file, 'w' );
		if ( $handle ) {
			if ( ! fwrite( $handle, $content ) ) {
				return FALSE;
			}
			fclose( $handle );
		} else {
			return FALSE;
		}
	}

	return TRUE;
}

// Flushing WP rewrite rules on portfolio slug changes
add_action( 'usof_before_save', 'us_maybe_flush_rewrite_rules' );
add_action( 'usof_after_save', 'us_maybe_flush_rewrite_rules' );
function us_maybe_flush_rewrite_rules( $updated_options ) {
	// The function is called twice: before and after options change
	static $old_portfolio_slug = NULL;
	static $old_portfolio_category_slug = NULL;
	$flush_rules = FALSE;
	if ( ! isset( $updated_options['portfolio_slug'] ) ) {
		$updated_options['portfolio_slug'] = NULL;
	}
	if ( ! isset( $updated_options['portfolio_category_slug'] ) ) {
		$updated_options['portfolio_category_slug'] = NULL;
	}
	if ( $old_portfolio_slug === NULL ) {
		// At first call we're storing the previous portfolio slug
		$old_portfolio_slug = us_get_option( 'portfolio_slug', 'portfolio' );
	} elseif ( $old_portfolio_slug != $updated_options['portfolio_slug'] ) {
		// At second call we're triggering flush rewrite rules at the next app execution
		// We're using transients to reduce the number of excess auto-loaded options
		$flush_rules = TRUE;
	}
	if ( $old_portfolio_category_slug === NULL ) {
		// At first call we're storing the previous portfolio slug
		$old_portfolio_category_slug = us_get_option( 'portfolio_category_slug', 'portfolio_category' );
	} elseif ( $old_portfolio_slug != $updated_options['portfolio_category_slug'] ) {
		// At second call we're triggering flush rewrite rules at the next app execution
		// We're using transients to reduce the number of excess auto-loaded options
		$flush_rules = TRUE;
	}

	if ( $flush_rules ) {
		update_option( 'us_flush_rules', TRUE, /* autoload */ 'yes' );
	}
}

// Allow to change Site Icon via Theme Options page
add_action( 'usof_after_save', 'us_update_site_icon_from_options' );
function us_update_site_icon_from_options( $updated_options ) {
	$options_site_icon = $updated_options['site_icon'];
	$wp_site_icon = get_option( 'site_icon' );

	if ( $options_site_icon != $wp_site_icon ) {
		update_option( 'site_icon', $options_site_icon );
	}
}

// Get Site Icon to display on Theme Options page
add_filter( 'usof_load_options_once', 'us_get_site_icon_for_options' );
function us_get_site_icon_for_options( $usof_options ) {
	$wp_site_icon = get_option( 'site_icon' );

	$usof_options['site_icon'] = $wp_site_icon;

	return $usof_options;
}

// Replace WP logo with Site Icon on wp-login screen
add_action( 'login_head', 'us_replace_login_logo' );
function us_replace_login_logo() {
	$site_icon_url = get_site_icon_url( 150 ); // returns thumbnail size

	// Replace icon specified in White Label settings
	if ( us_get_option( 'white_label', 0 ) AND $white_label_icon = us_get_option( 'white_label_login_icon', '' ) ) {
		$site_icon_url = wp_get_attachment_image_url( $white_label_icon, 'medium' );
	}

	if ( empty( $site_icon_url ) ) {
		return;
	}

	echo '<style>
	.login h1 a {
		background-image: url(' . $site_icon_url . ') !important;
		background-size: contain;
		width: auto;
		}
	</style>';
}

// Allow upload woff, woff2 files on Theme Options page
add_filter( 'upload_mimes', 'us_mime_types' );
function us_mime_types( $mimes ) {
	$mimes['woff2'] = 'font/woff';
	$mimes['woff2'] = 'font/woff2';

	return $mimes;
}

// Using USOF for theme options
$usof_directory = US_CORE_DIR . 'usof';
require $usof_directory . '/usof.php';

// Exclude pages that are set as Search results / Posts page / 404 not found
add_action( 'pre_get_posts', 'us_exclude_special_pages_from_search' );
function us_exclude_special_pages_from_search( $query ) {
	if ( $query->is_search && $query->is_main_query() ) {
		$special_pages = array();
		$special_pages_names = array( 'search_page', 'posts_page', 'page_404' );
		foreach ( $special_pages_names as $special_page_name ) {
			$special_page_id = us_get_option( $special_page_name, 'default' );
			if ( $special_page_id != 'default' AND intval( $special_page_id ) > 0 ) {
				$special_pages[] = intval( $special_page_id );
			}
		}
		if ( count( $special_pages ) > 0 ) {
			$post__not_in = $query->get( 'post__not_in' );
			if ( ! is_array( $post__not_in ) ) {
				$post__not_in = array();
			}
			$post__not_in = array_merge( $post__not_in, $special_pages );
			$query->set( 'post__not_in', $post__not_in );
		}
	}
}

// If the development version is activated, then maintenance mode is enabled
if ( get_option( 'us_license_dev_activated', 0 ) ) {
	us_update_option( 'maintenance_mode', 1 );
}

if (
	is_admin()
	AND has_filter( 'us_tr_default_language' )
) {
	/**
	 * Add redirect for Theme Options page, so it is always displayed in default language
	 * @return void
	 */
	global $pagenow;
	$current_language = apply_filters( 'us_tr_current_language', NULL );
	$default_language = apply_filters( 'us_tr_default_language', NULL );
	if (
		$current_language != $default_language
		AND $pagenow == 'admin.php'
		AND us_arr_path( $_GET, 'page' ) == 'us-theme-options'
	) {
		wp_redirect( admin_url() . 'admin.php?page=us-theme-options&lang=' . $default_language );
		exit;
	}
}
