<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'us_locate_file' ) ) {
	/**
	 * Search for some file in child theme, in parent theme and in common folder
	 *
	 * @param string $filename Relative path to filename with extension
	 * @param bool $all List an array of found files
	 *
	 * @return mixed Single mode: full path to file or FALSE if no file was found
	 * @return array All mode: array or all the found files
	 */
	function us_locate_file( $filename, $all = FALSE ) {
		global $us_template_directory, $us_stylesheet_directory, $us_files_search_paths, $us_file_paths;
		if ( ! isset( $us_files_search_paths ) ) {
			$us_files_search_paths = array();
			if ( defined( 'US_THEMENAME' ) ) {
				if ( is_child_theme() ) {
					// Searching in child theme first
					$us_files_search_paths[] = trailingslashit( $us_stylesheet_directory );
				}
				// Parent theme
				$us_files_search_paths[] = trailingslashit( $us_template_directory );
				// The common folder with files common for all themes
				$us_files_search_paths[] = $us_template_directory . '/common/';
			}

			if ( defined( 'US_CORE_DIR' ) ) {
				$us_files_search_paths[] = US_CORE_DIR;
			}
			// Can be overloaded if you decide to overload something from certain plugin
			$us_files_search_paths = apply_filters( 'us_files_search_paths', $us_files_search_paths );
		}
		if ( ! $all ) {
			if ( ! isset( $us_file_paths ) ) {
				$us_file_paths = apply_filters( 'us_file_paths', array() );
			}
			$filename = untrailingslashit( $filename );
			if ( ! isset( $us_file_paths[ $filename ] ) ) {
				$us_file_paths[ $filename ] = FALSE;
				foreach ( $us_files_search_paths as $search_path ) {
					if ( file_exists( $search_path . $filename ) ) {
						$us_file_paths[ $filename ] = $search_path . $filename;
						break;
					}
				}
			}

			return $us_file_paths[ $filename ];
		} else {
			$found = array();

			foreach ( $us_files_search_paths as $search_path ) {
				if ( file_exists( $search_path . $filename ) ) {
					$found[] = $search_path . $filename;
				}
			}

			return $found;
		}
	}
}

if ( ! function_exists( 'us_get_option' ) ) {
	/**
	 * Get theme option or return default value
	 *
	 * @param string $name
	 * @param mixed $default_value
	 *
	 * @return mixed
	 */
	function us_get_option( $name, $default_value = NULL ) {
		if ( function_exists( 'usof_get_option' ) ) {
			return usof_get_option( $name, $default_value );
		} else {
			return $default_value;
		}

	}
}

if ( ! function_exists( 'us_config' ) ) {
	/**
	 * Load and return some specific config or it's part
	 *
	 * @param string $path <config_name>[.<key1>[.<key2>[...]]]
	 * @param mixed $default Value to return if no data is found
	 * @return mixed
	 */
	function us_config( $path, $default = NULL, $reload = FALSE ) {
		global $us_template_directory;
		// Caching configuration values in a inner static value within the same request
		static $configs = array();
		// Defined paths to configuration files
		$config_name = strtok( $path, '.' );
		if ( ! isset( $configs[ $config_name ] ) OR $reload ) {
			$config_paths = array_reverse( us_locate_file( 'config/' . $config_name . '.php', TRUE ) );
			if ( empty( $config_paths ) ) {
				if ( WP_DEBUG ) {
					// TODO rework this check for correct plugin activation
					//wp_die( 'Config not found: ' . $config_name );
				}
				$configs[ $config_name ] = array();
			} else {
				us_maybe_load_theme_textdomain();
				// Parent $config data may be used from a config file
				$config = array();
				foreach ( $config_paths as $config_path ) {
					$config = require $config_path;
					// Config may be forced not to be overloaded from a config file
					if ( isset( $final_config ) AND $final_config ) {
						break;
					}
				}
				$configs[ $config_name ] = apply_filters( 'us_config_' . $config_name, $config );
			}
		}

		$path = substr( $path, strlen( $config_name ) + 1 );
		if ( $path == '' ) {
			return $configs[ $config_name ];
		}

		return us_arr_path( $configs[ $config_name ], $path, $default );
	}
}

if ( ! function_exists( 'us_maybe_load_theme_textdomain' ) ) {
	/**
	 * Load theme's textdomain
	 *
	 * @param string $domain
	 * @param string $path Relative path to seek in child theme and theme
	 *
	 * @return bool
	 */
	function us_maybe_load_theme_textdomain( $domain = 'us', $path = '/languages' ) {
		if ( is_textdomain_loaded( $domain ) ) {
			return TRUE;
		}
		$locale = apply_filters( 'theme_locale', determine_locale(), $domain );
		$filepath = us_locate_file( trailingslashit( $path ) . $locale . '.mo' );
		if ( $filepath === FALSE ) {
			return FALSE;
		}

		return load_textdomain( $domain, $filepath );
	}
}

if ( ! function_exists( 'us_translate' ) ) {
	/**
	 * Call language function with string existing in WordPress or supported plugins and prevent those strings from going into theme .po/.mo files
	 *
	 * @return string Translated text.
	 */
	function us_translate( $text, $domain = NULL ) {
		if ( $domain == NULL ) {
			return __( $text );
		} else {
			return __( $text, $domain );
		}
	}
}

if ( ! function_exists( 'us_translate_x' ) ) {
	function us_translate_x( $text, $context, $domain = NULL ) {
		if ( $domain == NULL ) {
			return _x( $text, $context );
		} else {
			return _x( $text, $context, $domain );
		}
	}
}

if ( ! function_exists( 'us_translate_n' ) ) {
	function us_translate_n( $single, $plural, $number, $domain = NULL ) {
		if ( $domain == NULL ) {
			return _n( $single, $plural, $number );
		} else {
			return _n( $single, $plural, $number, $domain );
		}
	}
}


if ( ! function_exists( 'us_api_remote_request' ) ) {
	/**
	 * Perform request to US Portal API
	 *
	 * @param $url
	 *
	 * @return array|bool|mixed|object
	 */
	function us_api_remote_request( $url ) {

		if ( empty( $url ) ) {
			return FALSE;
		}

		$args = array(
			'headers' => array( 'Accept-Encoding' => '' ),
			'timeout' => 300,
			'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36',
		);
		$request = wp_remote_request( $url, $args );

		if ( is_wp_error( $request ) ) {
			return FALSE;
		}

		$data = json_decode( $request['body'] );

		return $data;
	}
}

if ( ! function_exists( 'us_wp_link_pages' ) ) {
	/**
	 * Custom Post Pagination
	 * @param bool $echo
	 * @return string Returns or Echoes Pagination
	 */
	function us_wp_link_pages( $echo = FALSE ) {
		$links = wp_link_pages(
			array(
				'before' => '<nav class="post-pagination"><span class="title">' . us_translate( 'Pages:' ) . '</span>',
				'after' => '</nav>',
				'link_before' => '<span>',
				'link_after' => '</span>',
				'echo' => $echo,
			)
		);

		if ( ! $echo ) {
			return $links;
		}
	}
}
