<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'us_strtolower' ) ) {
	/**
	 * Make a string lowercase.
	 * Try to use mb_strtolower() when available.
	 *
	 * @param string $string String to format.
	 * @return string
	 */
	function us_strtolower( $string ) {
		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
	}
}

if ( ! function_exists( 'us_prepare_icon_tag' ) ) {
	/**
	 * Prepare a proper icon tag from user's custom input
	 *
	 * @param {String} $icon
	 *
	 * @return mixed|string
	 */

	function us_prepare_icon_tag( $icon, $inline_css = '' ) {
		$icon = apply_filters( 'us_icon_class', $icon );
		$icon_arr = explode( '|', $icon );
		if ( count( $icon_arr ) != 2 ) {
			return '';
		}

		$icon_arr[1] = strtolower( sanitize_text_field( $icon_arr[1] ) );
		if ( $icon_arr[0] == 'material' ) {
			$icon_tag = '<i class="material-icons"' . $inline_css . '>' . str_replace(
					array(
						' ',
						'-',
					), '_', $icon_arr[1]
				) . '</i>';
		} else {
			if ( substr( $icon_arr[1], 0, 3 ) == 'fa-' ) {
				$icon_tag = '<i class="' . $icon_arr[0] . ' ' . $icon_arr[1] . '"' . $inline_css . '></i>';
			} else {
				$icon_tag = '<i class="' . $icon_arr[0] . ' fa-' . $icon_arr[1] . '"' . $inline_css . '></i>';
			}
		}

		return apply_filters( 'us_icon_tag', $icon_tag );
	}
}

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

			$result = $us_file_paths[ $filename ];
		} else {
			$found = array();

			foreach ( $us_files_search_paths as $search_path ) {
				if ( file_exists( $search_path . $filename ) ) {
					$found[] = $search_path . $filename;
				}
			}

			$result = $found;
		}

		return apply_filters( 'us_locate_file', $result, $filename, $all );
	}
}

if ( ! function_exists( 'us_load_template' ) ) {
	/**
	 * Load some specified template and pass variables to it's scope.
	 *
	 * (!) If you create a template that is loaded via this method, please describe the variables that it should receive.
	 *
	 * @param string $template_name Template name to include (ex: 'templates/form/form')
	 * @param array $vars Array of variables to pass to a included templated
	 */
	function us_load_template( $template_name, $vars = NULL ) {

		// Searching for the needed file in a child theme, in the parent theme and, finally, in the common folder
		$file_path = us_locate_file( $template_name . '.php' );

		// Template not found
		if ( $file_path === FALSE ) {
			do_action( 'us_template_not_found:' . $template_name, $vars );

			return;
		}

		$vars = apply_filters( 'us_template_vars:' . $template_name, (array) $vars );
		if ( is_array( $vars ) AND count( $vars ) > 0 ) {
			extract( $vars, EXTR_SKIP );
		}

		do_action( 'us_before_template:' . $template_name, $vars );

		include $file_path;

		do_action( 'us_after_template:' . $template_name, $vars );
	}
}

if ( ! function_exists( 'us_get_template' ) ) {
	/**
	 * Get some specified template output with variables passed to it's scope.
	 *
	 * (!) If you create a template that is loaded via this method, please describe the variables that it should receive.
	 *
	 * @param string $template_name Template name to include (ex: 'templates/form/form')
	 * @param array $vars Array of variables to pass to a included templated
	 *
	 * @return string
	 */
	function us_get_template( $template_name, $vars = NULL ) {
		ob_start();
		us_load_template( $template_name, $vars );

		return ob_get_clean();
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

if ( ! function_exists( 'us_update_option' ) ) {
	/**
	 * Theme Settings Updates
	 *
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return boll
	 */
	function us_update_option( $name, $value ) {
		if ( function_exists( 'usof_save_options' ) ) {
			global $usof_options;
			usof_load_options_once();

			if ( isset( $usof_options[ $name ] ) ) {
				$usof_options[ $name ] = $value;
				usof_save_options( $usof_options );

				return TRUE;
			}
		}

		return FALSE;
	}
}

/**
 * @var $us_query array Allows to use different global $wp_query in different context safely
 */
$us_wp_queries = array();

if ( ! function_exists( 'us_open_wp_query_context' ) ) {
	/**
	 * Opens a new context to use a new custom global $wp_query
	 *
	 * (!) Don't forget to close it!
	 */
	function us_open_wp_query_context() {
		if ( is_array( $GLOBALS ) AND isset( $GLOBALS['wp_query'] ) ) {
			array_unshift( $GLOBALS['us_wp_queries'], $GLOBALS['wp_query'] );
		}
	}
}

if ( ! function_exists( 'us_close_wp_query_context' ) ) {
	/**
	 * Closes last context with a custom
	 */
	function us_close_wp_query_context() {
		if ( isset( $GLOBALS['us_wp_queries'] ) AND count( $GLOBALS['us_wp_queries'] ) > 0 ) {
			$GLOBALS['wp_query'] = array_shift( $GLOBALS['us_wp_queries'] );
			wp_reset_postdata();
		} else {
			// In case someone forgot to open the context
			wp_reset_query();
		}
	}
}

if ( ! function_exists( 'us_add_to_page_block_ids' ) ) {
	/**
	 * Opens a new page block context
	 *
	 */
	function us_add_to_page_block_ids( $page_block_id = NULL ) {

		global $us_page_block_ids;
		if ( empty( $us_page_block_ids ) ) {
			$us_page_block_ids = array();
		}
		if ( $page_block_id != NULL ) {
			array_unshift( $us_page_block_ids, $page_block_id );
		}

	}
}

if ( ! function_exists( 'us_remove_from_page_block_ids' ) ) {
	/**
	 * Closes last page_block context
	 */
	function us_remove_from_page_block_ids() {

		global $us_page_block_ids;

		return array_shift( $us_page_block_ids );
	}
}

if ( ! function_exists( 'us_arr_path' ) ) {
	/**
	 * Get a value from multidimensional array by path
	 *
	 * @param array $arr
	 * @param string|array $path <key1>[.<key2>[...]]
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	function us_arr_path( &$arr, $path, $default = NULL ) {
		$path = is_string( $path ) ? explode( '.', $path ) : $path;
		foreach ( $path as $key ) {
			if ( ! is_array( $arr ) OR ! isset( $arr[ $key ] ) ) {
				return $default;
			}
			$arr = &$arr[ $key ];
		}

		return $arr;
	}
}

if ( ! function_exists( 'us_implode_atts' ) ) {
	/**
	 * Converts an array to a attributes string
	 *
	 * @param array $params Parameter Array
	 * @param string $separator Separator between parameters
	 * @return string
	 */
	function us_implode_atts( $params = array(), $separator = ' ' ) {
		$output = array();
		foreach ( $params as $key => $value ) {
			if ( $value == '' ) {
				$output[] = esc_attr( $key );
			} else {
				$output[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
			}
		}

		return implode( $separator, $output );
	}
}

if ( ! function_exists( 'us_config' ) ) {
	/**
	 * Load and return some specific config or it's part
	 *
	 * @param string $path <config_name>[.<key1>[.<key2>[...]]]
	 *
	 * @oaram mixed $default Value to return if no data is found
	 *
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

if ( ! function_exists( 'us_get_image_size_params' ) ) {
	/**
	 * Get image size information as an array
	 *
	 * @param string $size_name
	 *
	 * @return array
	 */
	function us_get_image_size_params( $size_name ) {
		$img_sizes = wp_get_additional_image_sizes();

		// Getting custom image size
		if ( isset( $img_sizes[ $size_name ] ) ) {
			return $img_sizes[ $size_name ];

			// Get standard image size
		} else {
			return array(
				'width' => get_option( "{$size_name}_size_w" ),
				'height' => get_option( "{$size_name}_size_h" ),
				'crop' => get_option( "{$size_name}_crop", '0' ),
			);
		}
	}
}

if ( ! function_exists( 'us_pass_data_to_js' ) ) {
	/**
	 * Transform some variable to elm's onclick attribute, so it could be obtained from JavaScript as:
	 * var data = elm.onclick()
	 *
	 * @param mixed $data Data to pass
	 *
	 * @return string Element attribute ' onclick="..."'
	 */
	function us_pass_data_to_js( $data ) {
		return ' onclick=\'return ' . htmlspecialchars( json_encode( $data ), ENT_QUOTES, 'UTF-8' ) . '\'';
	}
}

if ( ! function_exists( 'us_maybe_get_post_json' ) ) {
	/**
	 * Try to get variable from JSON-encoded post variable
	 *
	 * Note: we pass some params via json-encoded variables, as via pure post some data (ex empty array) will be absent
	 *
	 * @param string $name $_POST's variable name
	 *
	 * @return array
	 */
	function us_maybe_get_post_json( $name = 'template_vars' ) {
		if ( isset( $_POST[ $name ] ) AND is_string( $_POST[ $name ] ) ) {
			$result = json_decode( stripslashes( $_POST[ $name ] ), TRUE );
			if ( ! is_array( $result ) ) {
				$result = array();
			}

			return $result;
		} else {
			return array();
		}
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
		$locale = apply_filters( 'theme_locale', is_admin() ? get_user_locale() : get_locale(), $domain );
		$filepath = us_locate_file( trailingslashit( $path ) . $locale . '.mo' );
		if ( $filepath === FALSE ) {
			return FALSE;
		}

		return load_textdomain( $domain, $filepath );
	}
}

if ( ! function_exists( 'us_array_merge_insert' ) ) {
	/**
	 * Merge arrays, inserting $arr2 into $arr1 before/after certain key
	 *
	 * @param array $arr Modifyed array
	 * @param array $inserted Inserted array
	 * @param string $position 'before' / 'after' / 'top' / 'bottom'
	 * @param string $key Associative key of $arr1 for before/after insertion
	 *
	 * @return array
	 */
	function us_array_merge_insert( array $arr, array $inserted, $position = 'bottom', $key = NULL ) {
		if ( $position == 'top' ) {
			return array_merge( $inserted, $arr );
		}
		$key_position = ( $key === NULL ) ? FALSE : array_search( $key, array_keys( $arr ) );
		if ( $key_position === FALSE OR ( $position != 'before' AND $position != 'after' ) ) {
			return array_merge( $arr, $inserted );
		}
		if ( $position == 'after' ) {
			$key_position ++;
		}

		return array_merge( array_slice( $arr, 0, $key_position, TRUE ), $inserted, array_slice( $arr, $key_position, NULL, TRUE ) );
	}
}

if ( ! function_exists( 'us_array_merge' ) ) {
	/**
	 * Recursively merge two or more arrays in a proper way
	 *
	 * @param array $array1
	 * @param array $array2
	 * @param array ...
	 *
	 * @return array
	 */
	function us_array_merge( $array1, $array2 ) {
		$keys = array_keys( $array2 );
		// Is associative array?
		if ( array_keys( $keys ) !== $keys ) {
			foreach ( $array2 as $key => $value ) {
				if ( is_array( $value ) AND isset( $array1[ $key ] ) AND is_array( $array1[ $key ] ) ) {
					$array1[ $key ] = us_array_merge( $array1[ $key ], $value );
				} else {
					$array1[ $key ] = $value;
				}
			}
		} else {
			foreach ( $array2 as $value ) {
				if ( ! in_array( $value, $array1, TRUE ) ) {
					$array1[] = $value;
				}
			}
		}

		if ( func_num_args() > 2 ) {
			foreach ( array_slice( func_get_args(), 2 ) as $array2 ) {
				$array1 = us_array_merge( $array1, $array2 );
			}
		}

		return $array1;
	}
}

if ( ! function_exists( 'us_shortcode_atts' ) ) {
	/**
	 * Combine user attributes with known attributes and fill in defaults from config when needed.
	 *
	 * @param array $atts Passed attributes
	 * @param string $shortcode Shortcode name
	 * @param string $param_name Shortcode's config param to take pairs from
	 *
	 * @return array
	 */
	function us_shortcode_atts( $atts, $shortcode ) {
		if ( substr( $shortcode, 0, 3 ) == 'us_' ) {
			$element = substr( $shortcode, 3 );
			$pairs = array();
			if ( in_array( $element, us_config( 'shortcodes.theme_elements', array() ) ) ) {
				$element_config = us_config( 'elements/' . $element, array() );
				if ( ! empty( $element_config['params'] ) ) {
					foreach ( $element_config['params'] as $param_name => $param_config ) {
						if ( isset( $param_config['shortcode_std'] ) ) {
							$param_config['std'] = $param_config['shortcode_std'];
						}
						if ( $param_config['type'] == 'checkboxes' AND isset( $param_config['std'] ) AND is_array( $param_config['std'] ) ) {
							$param_config['std'] = implode( ',', $param_config['std'] );
						}
						$pairs[ $param_name ] = ( isset( $param_config['std'] ) ) ? $param_config['std'] : NULL;
					}
				}
				if ( ! empty( $element_config['deprecated_params'] ) ) {
					foreach ( $element_config['deprecated_params'] as $param_name ) {
						$pairs[ $param_name ] = '';
					}
				}
			}
		} else {
			$pairs = us_config( 'shortcodes.modified.' . $shortcode . '.' . 'atts', array() );
		}

		$atts = shortcode_atts( $pairs, $atts, $shortcode );

		return apply_filters( 'us_shortcode_atts', $atts, $shortcode );
	}
}

if ( ! function_exists( 'us_get_sharing_counts' ) ) {
	/**
	 * Get number of shares of the provided URL.
	 *
	 * @param string $url The url to count shares
	 * @param array $providers Possible array values: 'facebook', 'pinterest', 'vk'
	 *
	 * Dev note: keep in mind that list of providers may differ for the same URL in different function calls.
	 *
	 * @return array Associative array of providers => share counts
	 */
	function us_get_sharing_counts( $url, $providers ) {
		$transient = 'us_sharing_count_' . md5( $url );
		// Will be used for array keys operations
		$flipped = array_flip( $providers );
		$cached_counts = get_transient( $transient );
		if ( is_array( $cached_counts ) ) {
			$counts = array_intersect_key( $cached_counts, $flipped );
			if ( count( $counts ) == count( $providers ) ) {
				// The data exists and is complete
				return $counts;
			}
		} else {
			$counts = array();
		}

		// Facebook share count
		if ( in_array( 'facebook', $providers ) AND ! isset( $counts['facebook'] ) ) {
			$remote_get_url = 'https://graph.facebook.com/?ids=' . $url . '&fields=og_object{engagement}';
			$result = wp_remote_get( $remote_get_url, array( 'timeout' => 3 ) );
			if ( is_array( $result ) ) {
				$data = json_decode( $result['body'], TRUE );
			} else {
				$data = NULL;
			}
			if (
				is_array( $data )
				AND isset( $data[ $url ]['og_object']['engagement']['count'] ) ) {
				$counts['facebook'] = use_letters_for_numbers( $data[ $url ]['og_object']['engagement']['count'] );
			} else {
				$counts['facebook'] = '0';
			}
		}
		// Pinterest share count
		if ( in_array( 'pinterest', $providers ) AND ! isset( $counts['pinterest'] ) ) {
			$result = wp_remote_get( 'https://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=' . $url, array( 'timeout' => 3 ) );
			if ( is_array( $result ) ) {
				$data = json_decode( rtrim( str_replace( 'receiveCount(', '', $result['body'] ), ')' ), TRUE );
			} else {
				$data = NULL;
			}
			$counts['pinterest'] = isset( $data['count'] ) ? use_letters_for_numbers( $data['count'] ) : '0';
		}

		// VK share count
		if ( in_array( 'vk', $providers ) AND ! isset( $counts['vk'] ) ) {
			$result = wp_remote_get( 'http://vkontakte.ru/share.php?act=count&index=1&url=' . $url, array( 'timeout' => 3 ) );
			if ( is_array( $result ) ) {
				$data = intval( trim( str_replace( ');', '', str_replace( 'VK.Share.count(1, ', '', $result['body'] ) ) ) );
			} else {
				$data = NULL;
			}
			$counts['vk'] = ( ! empty( $data ) ) ? use_letters_for_numbers( $data ) : '0';
		}

		// Caching the result for the next 2 hours
		set_transient( $transient, $counts, 2 * HOUR_IN_SECONDS );

		return $counts;
	}
}

if ( ! function_exists( 'use_letters_for_numbers' ) ) {

	/**
	 * Replace millions and thousands for "M" and "K" in numbers
	 */
	function use_letters_for_numbers( $value ) {

		if ( (int) $value > 1000000 ) {
			$value = number_format( $value / 1000000, 1 ) . 'M';
		} elseif ( (int) $value > 1000 ) {
			$value = number_format( $value / 1000, 1 ) . 'К';
		}

		return $value;
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

if ( ! function_exists( 'us_prepare_inline_css' ) ) {
	/**
	 * Prepare a proper inline-css string from given css property
	 *
	 * @param array $props
	 * @param bool $style_attr
	 * @param string $tag
	 *
	 * @return string
	 */
	function us_prepare_inline_css( $props, $style_attr = TRUE ) {
		$result = '';

		foreach ( $props as $prop => $value ) {
			$value = is_string( $value ) ? trim( $value ) : $value;

			// Do not apply if a value is empty string or begins double minus --
			if ( $value == '' OR ( is_string( $value ) AND strpos( $value, '--' ) === 0 ) ) {
				continue;
			}

			switch ( $prop ) {

				// Font-family exceptions
				case 'font-family':
					$result .= us_get_font_css( $value );
					break;

				// Properties with image values
				case 'background-image':
					if ( is_numeric( $value ) ) {
						if ( $image = wp_get_attachment_image_url( $value, 'full' ) ) {
							$result .= $prop . ':url(' . $image . ');';
						}
					} else {
						$result .= $prop . ':url(' . $value . ');';
					}
					break;

				// All other properties
				default:
					$result .= $prop . ':' . $value . ';';
					break;
			}
		}
		if ( $style_attr AND ! empty( $result ) ) {
			$result = ' style="' . esc_attr( $result ) . '"';
		}

		return $result;
	}
}

if ( ! function_exists( 'us_minify_css' ) ) {
	/**
	 * Prepares a minified version of CSS file
	 *
	 * @link http://manas.tungare.name/software/css-compression-in-php/
	 * @param string $css
	 *
	 * @return string
	 */
	function us_minify_css( $css ) {

		// Remove unwanted symbols
		$css = wp_strip_all_tags( $css, TRUE );

		// Remove comments
		$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

		// Remove spaces
		$css = str_replace( array( ' {', '{ ' ), '{', $css );
		$css = str_replace( ': ', ':', $css );
		$css = str_replace( ' > ', '>', $css );
		$css = str_replace( ' ~ ', '~', $css );
		$css = str_replace( '; ', ';', $css );
		$css = str_replace( ' !', '!', $css );
		$css = str_replace( ', ', ',', $css );

		// Remove doubled spaces
		$css = str_replace( array( '  ', '    ', '    ' ), '', $css );

		// Remove semicolon before closing bracket
		$css = str_replace( array( ';}', '; }', ' }', '} ' ), '}', $css );

		return $css;
	}
}

if ( ! function_exists( 'us_api_remote_request' ) ) {
	// TODO maybe move to admin area functions
	/**
	 * Perform request to US Portal API
	 *
	 * @param $url
	 * @param $as_array decode JSON as array, default FALSE
	 *
	 * @return array|bool|mixed|object
	 */
	function us_api_remote_request( $url, $as_array = FALSE ) {

		if ( empty( $url ) ) {
			return FALSE;
		}

		$args = array(
			'headers' => array( 'Accept-Encoding' => '' ),
			//		'sslverify' => FALSE,
			'timeout' => 300,
			'user-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36',
		);
		$request = wp_remote_request( $url, $args );

		if ( is_wp_error( $request ) ) {
			//		echo $request->get_error_message();
			return FALSE;
		}

		$data = json_decode( $request['body'], $as_array );

		return $data;
	}
}

if ( ! function_exists( 'usof_meta' ) ) {
	/**
	 * Get metabox option value
	 *
	 * @return string|array
	 */
	function usof_meta( $key, $post_id = NULL ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$value = '';
		if ( ! empty( $key ) ) {
			$value = get_post_meta( $post_id, $key, TRUE );
		}

		return $value;
	}
}

if ( ! function_exists( 'us_get_preloader_numeric_types' ) ) {
	/**
	 * Get preloader numbers
	 *
	 * @return array
	 */
	function us_get_preloader_numeric_types() {
		$config = us_config( 'theme-options' );
		$result = array();

		if ( isset( $config['general']['fields']['preloader']['options'] ) ) {
			$options = $config['general']['fields']['preloader']['options'];
		} else {
			return array();
		}

		if ( is_array( $options ) ) {
			foreach ( $options as $option => $title ) {
				if ( intval( $option ) != 0 ) {
					$result[] = $option;
				}
			}

			return $result;
		} else {
			return array();
		}
	}
}

if ( ! function_exists( 'us_shade_color' ) ) {
	/**
	 * Shade color https://stackoverflow.com/a/13542669
	 *
	 * @return string
	 */
	function us_shade_color( $color, $percent = '0.2' ) {
		$default = '';

		if ( empty( $color ) ) {
			return $default;
		}
		// TODO: make RGBA values appliable
		$color = str_replace( '#', '', $color );

		if ( strlen( $color ) == 6 ) {
			$RGB = str_split( $color, 2 );
			$R = hexdec( $RGB[0] );
			$G = hexdec( $RGB[1] );
			$B = hexdec( $RGB[2] );
		} elseif ( strlen( $color ) == 3 ) {
			$RGB = str_split( $color, 1 );
			$R = hexdec( $RGB[0] );
			$G = hexdec( $RGB[1] );
			$B = hexdec( $RGB[2] );
		} else {
			return $default;
		}

		// Determine color lightness (from 0 to 255)
		$lightness = $R * 0.213 + $G * 0.715 + $B * 0.072;

		// Make result lighter, when initial color lightness is low
		$t = $lightness < 60 ? 255 : 0;

		// Correct shade percent regarding color lightness
		$percent = $percent * ( 1.3 - $lightness / 255 );

		$output = 'rgb(';
		$output .= round( ( $t - $R ) * $percent ) + $R . ',';
		$output .= round( ( $t - $G ) * $percent ) + $G . ',';
		$output .= round( ( $t - $B ) * $percent ) + $B . ')';

		$output = us_rgba2hex( $output );

		// Return HEX color
		return $output;
	}
}

if ( ! function_exists( 'us_hex2rgba' ) ) {
	/**
	 * Convert HEX to RGBA
	 *
	 * @return string
	 */
	function us_hex2rgba( $color, $opacity = FALSE ) {
		$default = 'rgb(0,0,0)';

		// Return default if no color provided
		if ( empty( $color ) ) {
			return $default;
		}

		// Sanitize $color if "#" is provided
		if ( $color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		// Check if color has 6 or 3 characters and get values
		if ( strlen( $color ) == 6 ) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}

		// Convert hexadec to rgb
		$rgb = array_map( 'hexdec', $hex );

		// Check if opacity is set(rgba or rgb)
		if ( $opacity ) {
			if ( abs( $opacity ) > 1 ) {
				$opacity = 1.0;
			}
			$output = 'rgba(' . implode( ",", $rgb ) . ',' . $opacity . ')';
		} else {
			$output = 'rgb(' . implode( ",", $rgb ) . ')';
		}

		// Return rgb(a) color string
		return $output;
	}
}

if ( ! function_exists( 'us_gradient2hex' ) ) {
	/**
	 * Extract first value from linear-gradient
	 *
	 * @param $color String linear-gradient value
	 * @return String hex value
	 */
	function us_gradient2hex( $color = '' ) {
		if ( preg_match( '~linear-gradient\(([^,]+),([^,]+),([^)]+)\)~', $color, $matches ) ) {
			$color = (string) $matches[2];

			if ( ( strpos( $color, 'rgb' ) !== FALSE ) AND preg_match( '~rgba?\([^)]+\)~', $matches[0], $rgba ) ) {
				$color = (string) $rgba[0];
				$color = us_rgba2hex( $color );
			}
		}

		return $color;
	}
}

if ( ! function_exists( 'us_rgba2hex' ) ) {
	/**
	 * Convert RGBA to HEX
	 *
	 * @return string
	 */
	function us_rgba2hex( $color ) {
		// Returns HEX in case of RGB is provided, otherwise returns as is
		$default = "#000000";

		if ( empty( $color ) ) {
			return $default;
		}

		$rgb = array();
		$regex = '#\((([^()]+|(?R))*)\)#';

		if ( preg_match_all( $regex, $color, $matches ) ) {
			$rgba = explode( ',', implode( ' ', $matches[1] ) );
			// Cuts first 3 values for RGB
			$rgb = array_slice( $rgba, 0, 3 );
		} else {
			return (string) $color;
		}

		$output = "#";

		foreach ( $rgb as $color ) {
			$hex_val = dechex( intval( $color ) );
			if ( strlen( $hex_val ) === 1 ) {
				$output .= '0' . $hex_val;
			} else {
				$output .= $hex_val;
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'us_get_color' ) ) {
	/**
	 * Return filtered color value
	 *
	 * @param string $value
	 * @param bool $allow_gradient
	 *
	 * @return String
	 */
	function us_get_color( $value, $allow_gradient = FALSE, $_iterations = 0 ) {
		if ( empty( $value ) ) {
			return '';
		}

		// If the value begins "_", get the color from Theme Options > Colors
		if ( strpos( $value, '_' ) === 0 ) {
			$color = us_get_option( 'color' . $value, '' );

			// If the value contain "color", get the color from the option with that name
		} elseif ( strpos( $value, 'color' ) !== FALSE ) {
			$color = us_get_option( $value, '' );

			// in other cases use value as color
		} else {
			$color = $value;
		}

		// Check for recursion, values may have variables
		if ( strpos( $color, '_' ) === 0 AND $_iterations <= /* Max count iterations */ 3 ) {
			$color = us_get_color( 'color' . $color, $allow_gradient, ++ $_iterations );
		}

		return ( $allow_gradient ) ? $color : us_gradient2hex( $color );
	}
}

if ( ! function_exists( 'us_get_taxonomies' ) ) {
	/**
	 * Get taxonomies for selection
	 *
	 * @param $public_only bool
	 * @param $show_slug bool
	 * @param $output string 'woocommerce_exclude' / 'woocommerce_only'
	 * @param $key_prefix string 'tax|'
	 *
	 * @return array: slug => title (plural label)
	 */
	function us_get_taxonomies( $public_only = FALSE, $show_slug = TRUE, $output = '', $key_prefix = '' ) {
		$result = array();

		// Check if 'woocommerce_only' is requested and WooCommerce is not active
		if ( $output == 'woocommerce_only' AND ! class_exists( 'woocommerce' ) ) {
			// Return an empty result in this case
			return $result;
		}
		/*
		 * Getting list of taxonomies. Some public taxonomies may have no regular UI, so we combine two conditions.
		 * Public taxonomies may have no regular admin UI.
		 * And rest of taxonomies should have admin UI to get into our taxonomies list.
		 */
		$not_public_args = array( 'show_ui' => TRUE );
		$public_args = array( 'public' => TRUE, 'publicly_queryable' => TRUE );
		$taxonomies = array();
		if ( ! $public_only ) {
			$taxonomies = get_taxonomies( $not_public_args, 'object' );
		}

		$taxonomies = array_merge( $taxonomies, get_taxonomies( $public_args, 'object' ) );
		foreach ( $taxonomies as $taxonomy ) {

			// Exclude taxonomy which is not linked to any post type
			if ( empty( $taxonomy->object_type ) OR empty( $taxonomy->object_type[0] ) ) {
				continue;
			}

			// Skipping already added taxonomies
			if ( isset( $result[ $key_prefix . $taxonomy->name ] ) ) {
				continue;
			}

			// Check if the taxonomy is related to WooCommerce
			if ( class_exists( 'woocommerce' ) ) {
				$is_woo_tax = FALSE;
				if (
					$taxonomy->name == 'product_cat'
					OR $taxonomy->name == 'product_tag'
					OR (
						strpos( $taxonomy->name, 'pa_' ) === 0
						AND is_object_in_taxonomy( 'product', $taxonomy->name )
					)
				) {
					$is_woo_tax = TRUE;
				}

				// Exclude WooCommerce taxonomies
				if ( $output == 'woocommerce_exclude' ) {
					if ( $is_woo_tax ) {
						continue;
					}

					// Exclude all except WooCommerce taxonomies
				} elseif ( $output == 'woocommerce_only' ) {
					if ( ! $is_woo_tax ) {
						continue;
					}
				}
			}

			$taxonomy_title = $taxonomy->labels->name;

			// Show slug if set
			if ( $show_slug ) {
				$taxonomy_title .= ' (' . $taxonomy->name . ')';
			}

			$result[ $key_prefix . $taxonomy->name ] = $taxonomy_title;
		}

		return $result;
	}
}

if ( ! function_exists( 'us_enqueue_fonts' ) ) {
	/**
	 * Enqueue Google Fonts CSS file, used in frontend and admin pages
	 */
	function us_enqueue_fonts( $url = FALSE ) {
		$prefixes = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body' );
		$font_options = $fonts = array();

		$uploaded_fonts = us_get_option( 'uploaded_fonts', array() );
		$uploaded_font_names = array( 'get_h1' );
		if ( is_array( $uploaded_fonts ) AND count( $uploaded_fonts ) > 0 ) {
			foreach ( $uploaded_fonts as $uploaded_font ) {
				$uploaded_font_names[] = esc_attr( strip_tags( $uploaded_font['name'] ) );
			}
		}

		foreach ( $prefixes as $prefix ) {
			$font_option = explode( '|', us_get_option( $prefix . '_font_family', 'none' ), 2 );
			if ( in_array( $font_option[0], $uploaded_font_names ) ) {
				continue;
			}
			$font_options[] = $font_option;
		}

		$custom_fonts = us_get_option( 'custom_font', array() );
		if ( is_array( $custom_fonts ) AND count( $custom_fonts ) > 0 ) {
			foreach ( $custom_fonts as $custom_font ) {
				$font_options[] = explode( '|', $custom_font['font_family'], 2 );
			}
		}

		foreach ( $font_options as $font ) {
			if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
				$font[1] = '400,700'; // fault tolerance for missing font-variants
			}
			$selected_font_variants = explode( ',', $font[1] );

			// Empty font or web safe combination selected
			if ( $font[0] == 'none' OR strpos( $font[0], ',' ) !== FALSE ) {
				continue;
			}

			$font[0] = str_replace( ' ', '+', $font[0] );
			if ( ! isset( $fonts[ $font[0] ] ) ) {
				$fonts[ $font[0] ] = array();
			}

			foreach ( $selected_font_variants as $font_variant ) {
				$fonts[ $font[0] ][] = $font_variant;
			}
		}

		$font_display = '&display=' . us_get_option( 'font_display', 'swap' );
		$font_family = '';

		foreach ( $fonts as $font_name => $font_variants ) {
			if ( count( $font_variants ) == 0 ) {
				continue;
			}
			$font_variants = array_unique( $font_variants );
			if ( $font_family != '' ) {
				$font_family .= urlencode( '|' );
			}
			$font_family .= $font_name . ':' . implode( '%2C', $font_variants );
		}

		if ( $font_family != '' ) {
			$font_url = 'https://fonts.googleapis.com/css?family=' . $font_family . $font_display;

			if ( $url ) {
				return $font_url;
			} else {
				wp_enqueue_style( 'us-fonts', $font_url );
			}
		}
	}
}

if ( ! function_exists( 'us_get_fonts' ) ) {
	/**
	 * Get fonts for selection
	 *
	 * @return array
	 */
	function us_get_fonts( $without_groups = FALSE ) {

		// Default empty value
		$options = array( '' => us_translate( 'Default' ) );

		// Body & Headings fonts
		if ( ! $without_groups ) {
			$options[] = array(
				'optgroup' => TRUE,
				'title' => __( 'Preset Fonts', 'us' ),
			);
		}
		$body_font = explode( '|', us_get_option( 'body_font_family', 'none' ), 2 );
		if ( $body_font[0] != 'none' ) {
			$options['body'] = $body_font[0] . ' (' . __( 'used as default font', 'us' ) . ')';
		}

		// Headings
		for ( $i = 1; $i <= 6; $i ++ ) {
			$heading_font = explode( '|', us_get_option( 'h' . $i . '_font_family', 'none' ), 2 );
			if ( ! in_array( $heading_font[0], array( 'none', 'get_h1' ) ) ) {
				$options[ 'h' . $i ] = $heading_font[0] . ' (' . sprintf( __( 'used in Heading %s', 'us' ), $i ) . ')';
			}
		}

		// Additional Google Fonts
		$custom_fonts = us_get_option( 'custom_font', array() );
		if ( is_array( $custom_fonts ) AND count( $custom_fonts ) > 0 ) {
			if ( ! $without_groups ) {
				$options[] = array(
					'optgroup' => TRUE,
					'title' => __( 'Additional Google Fonts', 'us' ),
				);
			}
			foreach ( $custom_fonts as $custom_font ) {
				$font_options = explode( '|', $custom_font['font_family'], 2 );
				$options[ $font_options[0] ] = $font_options[0];
			}
		}

		// Uploaded Fonts
		$uploaded_fonts = us_get_option( 'uploaded_fonts', array() );
		if ( is_array( $uploaded_fonts ) AND count( $uploaded_fonts ) > 0 ) {
			if ( ! $without_groups ) {
				$options[] = array(
					'optgroup' => TRUE,
					'title' => __( 'Uploaded Fonts', 'us' ),
				);
			}
			$uploaded_font_families = array();
			foreach ( $uploaded_fonts as $uploaded_font ) {
				$uploaded_font_name = strip_tags( $uploaded_font['name'] );
				if ( $uploaded_font_name == '' OR in_array( $uploaded_font_name, $uploaded_font_families ) OR empty( $uploaded_font['files'] ) ) {
					continue;
				}
				$uploaded_font_families[] = $uploaded_font_name;
				$options[ $uploaded_font_name ] = $uploaded_font_name;
			}
		}

		// Web Safe Fonts
		if ( ! $without_groups ) {
			$options[] = array(
				'optgroup' => TRUE,
				'title' => __( 'Web safe font combinations (do not need to be loaded)', 'us' ),
			);
		}
		$web_safe_fonts = us_config( 'web-safe-fonts' );
		foreach ( $web_safe_fonts as $web_safe_font ) {
			$options[ $web_safe_font ] = $web_safe_font;
		}

		return $options;
	}
}

if ( ! function_exists( 'us_get_all_fonts' ) ) {
	function us_get_all_fonts( $only_google = FALSE, $get_h1 = FALSE ) {
		$fonts_arr = array();

		// Uploaded Fonts
		$uploaded_font_families = array();
		$uploaded_font_list = array();
		$uploaded_fonts = us_get_option( 'uploaded_fonts', array() );
		if ( is_array( $uploaded_fonts ) AND count( $uploaded_fonts ) > 0 ) {
			foreach ( $uploaded_fonts as $uploaded_font ) {
				$uploaded_font_name = strip_tags( $uploaded_font['name'] );
				if ( $uploaded_font_name == '' OR in_array( $uploaded_font_name, $uploaded_font_families ) OR empty( $uploaded_font['files'] ) ) {
					continue;
				}
				$uploaded_font_families[] = $uploaded_font_name;
				$group_name = __( 'Uploaded Fonts', 'us' );
				$uploaded_font_list[ $group_name ][ esc_attr( $uploaded_font_name ) ] = $uploaded_font_name;
			}
		}

		// Output if 'only_google' param is not set
		if ( $only_google ) {
			$fonts_arr['none'] = __( 'No font specified', 'us' );

			if ( $get_h1 ) {
				$fonts_arr['get_h1'] = __( 'As in Heading 1', 'us' );
			}

			// Uploaded Fonts
			if ( ! empty( $uploaded_font_list ) AND is_array( $uploaded_font_list ) ) {
				$fonts_arr = $fonts_arr + $uploaded_font_list;
			}

			if ( ! isset( $web_safe_fonts ) ) {
				$web_safe_fonts = us_config( 'web-safe-fonts' );
			}

			if ( ! empty( $web_safe_fonts ) AND is_array( $web_safe_fonts ) ) {
				foreach ( $web_safe_fonts as $font_name ) {
					if ( $font_name == '' OR in_array( $font_name, $uploaded_font_families ) ) {
						continue;
					}
					$group_name = __( 'Web safe font combinations (do not need to be loaded)', 'us' );
					$fonts_arr[ $group_name ][ esc_attr( $font_name ) ] = $font_name;
				}
			}
		}

		// Google Fonts
		if ( $google_fonts = us_config( 'google-fonts', array() ) AND is_array( $google_fonts ) ) {
			foreach ( $google_fonts as $font_name => &$tmp_font_value ) {
				if ( $font_name == '' OR in_array( $font_name, $uploaded_font_families ) ) {
					continue;
				}
				$group_name = __( 'Google Fonts (loaded from Google servers)', 'us' );
				$fonts_arr[ $group_name ][ esc_attr( $font_name ) ] = $font_name;
			}
		}

		return $fonts_arr;
	}
}

if ( ! function_exists( 'us_get_font_css' ) ) {
	/**
	 * Generate CSS font-family & font-weight of selected font
	 *
	 * @param string $font_name
	 * @param bool $with_weight
	 *
	 * @return string
	 */
	function us_get_font_css( $font_name, $value_only = FALSE, $with_weight = FALSE ) {
		if ( empty( $font_name ) ) {
			return '';
		}

		static $font_css;
		if ( empty( $font_css ) ) {
			$font_options = $font_css = array();

			// Add Regular Text font
			$font_options['body'] = explode( '|', us_get_option( 'body_font_family', 'none' ), 2 );

			// Add Headings fonts
			for ( $i = 1; $i <= 6; $i ++ ) {
				if ( us_get_option( 'h' . $i . '_font_family', 'none' ) == 'get_h1|' ) {
					$font_options[ 'h' . $i ] = explode( '|', us_get_option( 'h1_font_family', 'none' ), 2 );
				} else {
					$font_options[ 'h' . $i ] = explode( '|', us_get_option( 'h' . $i . '_font_family', 'none' ), 2 );
				}
			}

			// Add Additional Google fonts
			$custom_fonts = us_get_option( 'custom_font', array() );
			if ( is_array( $custom_fonts ) AND count( $custom_fonts ) > 0 ) {
				foreach ( $custom_fonts as $custom_font ) {
					$font_option = explode( '|', $custom_font['font_family'], 2 );
					$font_options[ $font_option[0] ] = $font_option;
				}
			}

			// Add Uploaded fonts
			$uploaded_fonts = us_get_option( 'uploaded_fonts', array() );
			if ( is_array( $uploaded_fonts ) AND count( $uploaded_fonts ) > 0 ) {
				foreach ( $uploaded_fonts as $uploaded_font ) {
					$font_options[ $uploaded_font['name'] ] = array(
						0 => strip_tags( $uploaded_font['name'] ),
						1 => $uploaded_font['weight'],
					);
				}
			}

			// Add Websafe fonts
			$web_safe_fonts = us_config( 'web-safe-fonts' );
			foreach ( $web_safe_fonts as $web_safe_font ) {
				$font_options[ $web_safe_font ] = array( $web_safe_font );
			}

			foreach ( $font_options as $prefix => $font ) {
				if ( $font[0] == 'none' ) {
					$font_css[ $prefix ][0] = '';
				} elseif ( strpos( $font[0], ',' ) === FALSE ) {
					$fallback_font_family = us_config( 'google-fonts.' . $font[0] . '.fallback', 'sans-serif' );
					$font_css[ $prefix ][0] = 'font-family:\'' . $font[0] . '\', ' . $fallback_font_family . ';';
					// Fault tolerance for missing font-variants
					if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
						$font[1] = '400,700';
					}
					// The first active font-weight will be used for "normal" weight
					$font_css[ $prefix ][1] = intval( $font[1] );
				} else {
					// Web-safe font combination
					$font_css[ $prefix ][0] = 'font-family:' . $font[0] . ';';
					$font_css[ $prefix ][1] = '400';
				}
			}
		}

		if ( isset( $font_css[ $font_name ] ) AND ! empty( $font_css[ $font_name ][0] ) ) {
			$result = $font_css[ $font_name ][0];

			if ( ! $value_only AND $with_weight AND ! empty( $font_css[ $font_name ][1] ) ) {
				$result .= 'font-weight: ' . $font_css[ $font_name ][1] . ';';
			}

			return ( $value_only ) ? str_replace( array( 'font-family:', ';' ), '', $result ) : $result;

		} else {
			return '';
		}
	}
}

if ( ! function_exists( 'us_get_ip' ) ) {
	// TODO maybe move to admin area functions
	/**
	 * Get the remote IP address
	 *
	 * @return string
	 */
	function us_get_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return apply_filters( 'us_get_ip', $ip );
	}
}

if ( ! function_exists( 'us_get_sidebars' ) ) {
	/**
	 * Get Sidebars for selection
	 *
	 * @return array
	 */
	function us_get_sidebars() {
		$sidebars = array();
		global $wp_registered_sidebars;

		if ( is_array( $wp_registered_sidebars ) AND ! empty( $wp_registered_sidebars ) ) {
			foreach ( $wp_registered_sidebars as $sidebar ) {
				if ( $sidebar['id'] == 'default_sidebar' ) {
					// Add Default Sidebar to the beginning
					$sidebars = array_merge( array( $sidebar['id'] => $sidebar['name'] ), $sidebars );
				} else {
					$sidebars[ $sidebar['id'] ] = $sidebar['name'];
				}
			}
		}

		return $sidebars;
	}
}

if ( ! function_exists( 'us_get_public_post_types' ) ) {
	/**
	 * Get post types, which have frontend single template, taking into account theme options
	 *
	 * @param array $exclude post types to exlude from result
	 *
	 * @return array: name => title (plural label)
	 */
	function us_get_public_post_types( $exclude = array() ) {

		// Default result includes built-in pages and posts
		$result = array(
			'page' => us_translate_x( 'Pages', 'post type general name' ),
			'post' => us_translate_x( 'Posts', 'post type general name' ),
		);

		// Append custom post types with specified arguments
		$custom_post_types = get_post_types(
			array(
				'public' => TRUE,
				'publicly_queryable' => TRUE,
				'_builtin' => FALSE,
			),
			'objects'
		);
		foreach ( $custom_post_types as $post_type_name => $post_type_obj ) {
			$result[ $post_type_name ] = $post_type_obj->labels->name;
		}

		// Exclude predefined post types, which can't have single frontend template
		$exclude_post_types = array_merge(
			array(
				'reply', // bbPress
				'us_testimonial',
			),
			$exclude
		);
		foreach ( $exclude_post_types as $type ) {
			unset( $result[ $type ] );
		}

		return $result;
	}
}

if ( ! function_exists( 'us_get_page_area_id' ) ) {
	/**
	 * Get value of specified area ID for current page
	 *
	 * @param string $area : header / titlebar / content template / sidebar / footer
	 *
	 * @return string
	 */
	function us_get_page_area_id( $area ) {
		if ( empty( $area ) ) {
			return FALSE;
		}

		// Get public post types except Pages and Products
		$public_post_types = array_keys( us_get_public_post_types( array( 'page', 'product' ) ) );

		// Get public taxonomies EXCEPT Products
		$public_taxonomies = array_keys( us_get_taxonomies( TRUE, FALSE, 'woocommerce_exclude' ) );

		// Get Products taxonomies ONLY
		$product_taxonomies = array_keys( us_get_taxonomies( TRUE, FALSE, 'woocommerce_only' ) );

		// Default from Theme Options
		$area_id = us_get_option( $area . '_id', '' );

		// WooCommerce Products
		if ( function_exists( 'is_product' ) AND is_product() ) {
			$area_id = us_get_option( $area . '_product_id' );

			// WooCommerce Shop Page
		} elseif ( function_exists( 'is_shop' ) AND is_shop() ) {
			$area_id = us_get_option( $area . '_shop_id' );

			// WooCommerce Products Search
		} elseif ( class_exists( 'woocommerce' ) AND is_post_type_archive( 'product' ) AND is_search() ) {
			$area_id = us_get_option( $area . '_shop_id' );

			// Author Pages
		} elseif ( is_author() ) {
			$area_id = us_get_option( $area . '_author_id', '__defaults__' );

			if ( $area_id == '__defaults__' ) {
				$area_id = us_get_option( $area . '_archive_id', '' );
			}

			// Archives
		} elseif ( is_archive() OR is_tax( $public_taxonomies ) OR ( ! empty( $product_taxonomies ) AND is_tax( $product_taxonomies ) ) ) {

			// For product taxonomies use "Shop Page" by default
			if ( ! empty( $product_taxonomies ) AND is_tax( $product_taxonomies ) ) {
				$area_id = us_get_option( $area . '_shop_id' );

				// For others use "Archives" by default
			} else {
				$area_id = us_get_option( $area . '_archive_id' );
			}

			if ( is_category() ) {
				$current_tax = 'category';
			} elseif ( is_tag() ) {
				$current_tax = 'post_tag';
				/*
				 * Checking WooCommerce taxonomies,
				 * same as is_category / is_tag they require separate check
				 */
			} elseif (
				function_exists( 'is_product_category' )
				AND is_product_category()
			) {
				$current_tax = 'product_cat';
			} elseif (
				function_exists( 'is_product_tag' )
				AND is_product_tag()
			) {
				$current_tax = 'product_tag';
			} elseif ( is_tax() ) {
				$current_tax = get_query_var( 'taxonomy' );
			}

			// Archive Content template, specified in terms "Edit" admin screen
			if ( $area === 'content' AND $archive_content_id = get_term_meta( get_queried_object_id(), 'archive_content_id', TRUE ) ) {
				if ( is_numeric( $archive_content_id ) ) {
					$area_id = $archive_content_id;
					$current_tax = NULL;
				}
			}

			if (
				! empty( $current_tax )
				AND ( $_area_id = us_get_option( $area . '_tax_' . $current_tax . '_id' ) ) !== NULL
				AND $_area_id !== '__defaults__'
			) {
				$area_id = $_area_id;
			}

			// Other Post Types
		} elseif ( ! empty( $public_post_types ) AND is_singular( $public_post_types ) ) {

			if ( is_attachment() ) {
				$post_type = 'post'; // force "post" suffix for attachments
			} elseif ( is_singular( 'us_portfolio' ) ) {
				$post_type = 'portfolio'; // force "portfolio" suffix to avoid migration from old theme options
			} elseif ( is_singular( 'tribe_events' ) ) {
				$post_type = 'tribe_events'; // force "tribe_*" suffix cause The Events Calendar always returns "page" type
			} elseif ( is_singular( 'tribe_venue' ) ) {
				$post_type = 'tribe_venue';
			} elseif ( is_singular( 'tribe_organizer' ) ) {
				$post_type = 'tribe_organizer';
			} else {
				$post_type = get_post_type();
			}

			$area_id = us_get_option( $area . '_' . $post_type . '_id', '__defaults__' );
		}

		// Forums archive page
		if ( is_post_type_archive( 'forum' ) OR ( function_exists( 'bbp_is_search' ) AND bbp_is_search() ) OR ( function_exists( 'bbp_is_search_results' ) AND bbp_is_search_results() ) ) {
			$area_id = us_get_option( $area . '_forum_id' );
		}

		// Events calendar archive page
		if ( is_post_type_archive( 'tribe_events' ) ) {
			$area_id = us_get_option( $area . '_tax_tribe_events_cat_id', '__defaults__' );

			if ( $area_id == '__defaults__' ) {
				$area_id = us_get_option( $area . '_archive_id', '' );
			}
		}

		// Search Results page
		if (
			is_search()
			AND ! is_post_type_archive( 'product' )
			AND $postID = us_get_option( 'search_page', 'default' )
			AND is_numeric( $postID )
		) {
			$area_id = usof_meta( 'us_' . $area . '_id', $postID );
		}

		// Posts page
		if (
			is_home()
			AND $postID = us_get_option( 'posts_page', 'default' )
			AND $postID != 'default'
		) {
			$area_id = usof_meta( 'us_' . $area . '_id', $postID );
		}

		// 404 page
		if (
			is_404()
			AND $postID = us_get_option( 'page_404', 'default' )
			AND $postID != 'default'
		) {
			$area_id = usof_meta( 'us_' . $area . '_id', $postID );
		}

		// Specific page
		if ( is_singular() ) {
			$postID = get_queried_object_id();

			// Check all terms of the post and get "Pages Content template" term custom field (any first numeric value it's enough)
			if ( $area === 'content' AND ! empty( get_post_taxonomies( $postID ) ) ) {
				foreach ( get_post_taxonomies( $postID ) as $taxonomy_slug ) {

					$terms = get_the_terms( $postID, $taxonomy_slug );

					if ( ! empty( $terms ) AND is_array( $terms ) ) {
						foreach ( $terms as $term ) {
							if ( is_numeric( $pages_content_id = get_term_meta( $term->term_id, 'pages_content_id', TRUE ) ) ) {
								$area_id = $pages_content_id;

								break 2;
							}
						}
					}

				}
			}

			// Check the existance of post custom field and get its value
			if ( $postID AND metadata_exists( 'post', $postID, 'us_' . $area . '_id' ) ) {

				$singular_area_id = usof_meta( 'us_' . $area . '_id', $postID );

				// then check if the value has ID of non-existing Page Block (if it was deleted)
				if ( $singular_area_id == '' OR is_registered_sidebar( $singular_area_id ) OR get_post_status( $singular_area_id ) != FALSE ) {
					$area_id = $singular_area_id;
				}
			}
		}

		// Reset Pages defaults
		if ( $area_id == '__defaults__' ) {
			$area_id = us_get_option( $area . '_id', '' );
		}

		// If you have WPML or Polylang plugins then check the translations
		if ( has_filter( 'us_tr_object_id' ) AND is_numeric( $area_id ) ) {
			$area_id = (int) apply_filters( 'us_tr_object_id', $area_id );
		}

		return apply_filters( 'us_get_page_area_id', $area_id );
	}
}

if ( ! function_exists( 'us_get_current_page_block_ids' ) ) {
	/**
	 * Get Page Blocks ids of the current page
	 *
	 * @return array
	 */
	function us_get_current_page_block_ids() {
		$ids = array();
		foreach ( array( 'footer', 'content', 'titlebar' ) as $name ) {
			if ( $area_id = us_get_page_area_id( $name ) AND is_numeric( $area_id ) ) {
				if ( has_filter( 'us_tr_object_id' ) ) {
					$translated_id = apply_filters( 'us_tr_object_id', $area_id, 'us_page_block', TRUE );
					if ( $translated_id != $area_id ) {
						$area_id = $translated_id;
					}
				}
				$ids[] = $area_id;
			}
		}

		return array_unique( $ids );
	}
}

if ( ! function_exists( 'us_get_current_page_block_content' ) ) {
	/**
	 * Get Page Blocks content of the current page
	 *
	 * @return string
	 */
	function us_get_current_page_block_content() {
		$output = '';
		if ( $page_block_ids = (array) us_get_current_page_block_ids() ) {
			$query_args = array(
				'nopaging' => TRUE,
				'post__in' => $page_block_ids,
				'post_type' => array( 'us_page_block', 'us_content_template' ),
				'suppress_filters' => TRUE,
			);
			foreach ( get_posts( $query_args ) as $post ) {
				if ( ! empty( $post->post_content ) ) {
					$output .= $post->post_content;
				}
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'us_get_btn_styles' ) ) {
	/**
	 * Get Button Styles created on Theme Options > Button Styles
	 *
	 * @return array: id => name
	 */
	function us_get_btn_styles() {

		$btn_styles_list = array();
		$btn_styles = us_get_option( 'buttons', array() );

		if ( is_array( $btn_styles ) ) {
			foreach ( $btn_styles as $btn_style ) {
				$btn_name = trim( $btn_style['name'] );
				if ( $btn_name == '' ) {
					$btn_name = us_translate( 'Style' ) . ' ' . $btn_style['id'];
				}

				$btn_styles_list[ $btn_style['id'] ] = esc_html( $btn_name );
			}
		}

		return $btn_styles_list;
	}
}

if ( ! function_exists( 'us_get_image_sizes_list' ) ) {
	/**
	 * Get image size values for selection
	 *
	 * @param array [$size_names] List of size names
	 *
	 * @return array
	 */
	function us_get_image_sizes_list( $include_full = TRUE ) {
		if ( ! is_admin() ) {
			return array();
		}

		if ( $include_full ) {
			$image_sizes = array( 'full' => us_translate( 'Full Size' ) );
		} else {
			$image_sizes = array();
		}

		// Exclude doubled WooCommerce size names
		$exclude_sizes = array(
			'woocommerce_thumbnail',
			'woocommerce_single',
			'woocommerce_gallery_thumbnail',
		);

		foreach ( get_intermediate_image_sizes() as $size_name ) {
			if ( in_array( $size_name, $exclude_sizes ) ) {
				continue;
			}

			// Get size params
			$size = us_get_image_size_params( $size_name );

			// Do not include sizes with both zero values
			if ( (int) $size['width'] == 0 AND (int) $size['height'] == 0 ) {
				continue;
			}

			$size_title = ( ( (int) $size['width'] == 0 ) ? __( 'any', 'us' ) : $size['width'] );
			$size_title .= '×';
			$size_title .= ( (int) $size['height'] == 0 ) ? __( 'any', 'us' ) : $size['height'];
			if ( $size['crop'] ) {
				$size_title .= ' ' . __( 'cropped', 'us' );
			}

			if ( ! in_array( $size_title, $image_sizes ) ) {
				$image_sizes[ $size_name ] = $size_title;
			}
		}

		return apply_filters( 'us_image_sizes_select_values', $image_sizes );
	}
}

if ( ! function_exists( 'us_generate_link_atts' ) ) {
	/**
	 * Generate attributes for link tag based on elements options
	 *
	 * @param string|array $link
	 *
	 * @return array
	 */
	function us_generate_link_atts( $link = '' ) {
		if ( empty( $link ) ) {
			return array();
		}

		$link_array = array();

		// Check the type of provided value
		if ( is_array( $link ) ) {
			$link_array = $link;

			// If it is string and begins with "url", use WPBakery way to create array
		} elseif ( strpos( $link, 'url:' ) === 0 OR strpos( $link, '|' ) !== FALSE ) {
			$params_pairs = explode( '|', $link );
			if ( ! empty( $params_pairs ) ) {
				foreach ( $params_pairs as $pair ) {
					$param = explode( ':', $pair, 2 );
					if ( ! empty( $param[0] ) AND isset( $param[1] ) ) {
						$link_array[ $param[0] ] = rawurldecode( $param[1] );
					}
				}
			}
		} else {
			$link_array['url'] = $link;
		}

		// Check for custom fields values like '{{field_name}}'
		if ( isset( $link_array['url'] ) AND preg_match( "#{{([^}]+)}}#", trim( $link_array['url'] ), $matches ) ) {
			global $us_grid_term;
			$postID = get_the_ID();

			// Definition of an identifier for terms
			$term = ( $us_grid_term !== NULL )
				? $us_grid_term
				: get_queried_object();
			$term_id = $term instanceof WP_Term
				? $term->term_id
				: NULL;

			if ( $meta_value = get_post_meta( $postID, $matches[1], TRUE ) ) {

				// If the value is array, return itself
				if ( is_array( $meta_value ) ) {
					$link_array = $meta_value;

					// If the value is serialized array (used in USOF metabox options)
				} elseif ( strpos( $meta_value, '{' ) === 0 ) {
					try {
						$meta_value_array = json_decode( $meta_value, TRUE );
						if ( is_array( $meta_value_array ) ) {
							$link_array['url'] = $meta_value_array['url'];

							// Override "target" only if it was empty
							if ( empty( $link_array['target'] ) AND ! empty( $meta_value_array['target'] ) ) {
								$link_array['target'] = $meta_value_array['target'];
							}

							// Force "nofollow" for metabox URLs
							$link_array['rel'] = 'nofollow';
						}
					}
					catch ( Exception $e ) {
					}

					// If the value is string with digits, use it as post or attachment ID
				} elseif ( is_numeric( $meta_value ) AND $post_type = get_post_type( (int) $meta_value ) ) {
					$link_array['url'] = ( $post_type == 'attachment' )
						? wp_get_attachment_url( $meta_value )
						: get_permalink( $meta_value );

					// In other cases return the value as 'url'
				} else {
					$link_array['url'] = $meta_value;
				}

				// If the value in terms
			} elseif ( $term_id AND $term_metadata = get_metadata( 'term', $term_id, $matches[1], TRUE ) ) {
				$link_array['url'] = ! empty( $term_metadata['url'] )
					? $term_metadata['url']
					: '';

				// If the value is empty, return empty 'url'
			} else {
				$link_array['url'] = '';
			}
		}

		// Parse "url" attribute
		if ( ! empty( $link_array['url'] ) ) {

			// Replace [lang] shortcode with current language code
			if ( strpos( $link_array['url'], '[lang]' ) !== FALSE ) {
				$current_lang = apply_filters( 'us_tr_current_language', NULL );

				$link_array['url'] = str_replace( '[lang]', $current_lang, $link_array['url'] );
			}

			// If the URL is email, add "mailto:"
			if ( is_email( $link_array['url'] ) ) {
				$link_array['href'] = 'mailto:' . $link_array['url'];

				// Remove "target" attribute, because it doesn't work with mailto:
				if ( isset( $link_array['target'] ) ) {
					unset( $link_array['target'] );
				}

			} else {
				$link_array['href'] = $link_array['url'];
			}

			// Force rel="noopener" for target="_blank"
			if ( ! empty( $link_array['target'] ) ) {
				if ( ! empty( $link_array['rel'] ) ) {
					$link_array['rel'] .= ' noopener';
				} else {
					$link_array['rel'] = 'noopener';
				}

				// Remove empty "target" attribute
			} elseif ( isset( $link_array['target'] ) ) {
				unset( $link_array['target'] );
			}

			// Remove extra "url" attribute
			unset( $link_array['url'] );

		} else {
			return array();
		}

		// Trim all attributes
		$link_array = array_map( 'trim', $link_array );

		return apply_filters( 'us_generate_link_atts_link_array', $link_array );
	}
}

if ( ! function_exists( 'us_get_elm_link_options' ) ) {
	/**
	 * Generate array for "Link" option, used in theme elements
	 *
	 * @return array
	 */
	function us_get_elm_link_options() {

		// Predefined options
		$link_options = array(
			'us_tile_link' => __( 'Custom appearance in Grid', 'us' ) . ': ' . __( 'Custom Link', 'us' ),
		);

		// Add Testimonial author link, if Testimonials are enabled
		if ( us_get_option( 'enable_testimonials', 1 ) ) {
			$link_options['us_testimonial_link'] = __( 'Testimonial', 'us' ) . ': ' . __( 'Author Link', 'us' );
		}

		// Add field types from "Advanced Custom Fields" plugin
		if ( function_exists( 'acf_get_field_groups' ) AND $acf_groups = acf_get_field_groups() ) {
			foreach ( $acf_groups as $group ) {
				$fields = acf_get_fields( $group['ID'] );
				foreach ( $fields as $field ) {

					// Add specific ACF types as link options
					if ( in_array( $field['type'], array( 'url', 'link', 'page_link', 'file', 'email' ) ) ) {
						$link_options[ $field['name'] ] = $group['title'] . ': ' . $field['label'];
					}
				}
			}
		}

		return $link_options;
	}
}

if ( ! function_exists( 'us_get_smart_date' ) ) {
	/**
	 * Return date and time in Human readable format
	 *
	 * @param int $from Unix timestamp from which the difference begins.
	 * @param int $to Optional. Unix timestamp to end the time difference. Default becomes current_time() if not set.
	 *
	 * @return string Human readable date and time.
	 */
	function us_get_smart_date( $from, $to = '' ) {
		if ( empty( $to ) ) {
			$to = current_time( 'U' );
		}

		$diff = (int) abs( $to - $from );

		// Get time format from site general settings
		$site_time_format = get_option( 'time_format', 'g:i a' );

		$time_string = date( $site_time_format, $from );
		$day = (int) date( 'jmY', $from );
		$current_day = (int) date( 'jmY', $to );
		$yesterday = (int) date( 'jmY', strtotime( 'yesterday', $to ) );
		$year = (int) date( 'Y', $from );
		$current_year = (int) date( 'Y', $to );

		if ( $diff < HOUR_IN_SECONDS ) {
			$mins = round( $diff / MINUTE_IN_SECONDS );
			if ( $mins <= 1 ) {
				$mins = 1;
			}

			// 1-59 minutes ago
			$mins_string = sprintf( us_translate_n( '%s min', '%s mins', $mins ), $mins );
			$result = sprintf( us_translate( '%s ago' ), $mins_string );
		} elseif ( $diff <= ( HOUR_IN_SECONDS * 4 ) ) {
			$hours = round( $diff / HOUR_IN_SECONDS );
			if ( $hours <= 1 ) {
				$hours = 1;
			}

			// 1-4 hours ago
			$hours_string = sprintf( us_translate_n( '%s hour', '%s hours', $hours ), $hours );
			$result = sprintf( us_translate( '%s ago' ), $hours_string );
		} elseif ( $current_day == $day ) {

			// Today at 9:30
			$result = sprintf( us_translate( '%1$s at %2$s' ), us_translate( 'Today' ), $time_string );
		} elseif ( $yesterday == $day ) {

			// Yesterday at 9:30
			$result = sprintf( us_translate( '%1$s at %2$s' ), __( 'Yesterday', 'us' ), $time_string );
		} elseif ( $current_year == $year ) {

			// 23 Jan at 12:30
			$result = sprintf( us_translate( '%1$s at %2$s' ), date_i18n( 'j M', $from ), $time_string );
		} else {

			// 18 Dec 2018
			$result = date_i18n( 'j M Y', $from );
		}

		return $result;
	}
}

if ( ! function_exists( 'us_get_posts_titles_for' ) ) {
	/**
	 * Get list of posts titles by a certain post types
	 * @param array $post_types Post types to get
	 * @param bool $force_no_cache Allow using cache (use FALSE to force not-cached version)
	 * @return array
	 */
	function us_get_all_posts_titles_for( $post_types, $orderby = 'title', $force_no_cache = TRUE ) {
		if ( empty( $post_types ) OR ! is_array( $post_types ) ) {
			return array();
		}

		static $results = array();
		$post_types = array_map( 'trim', $post_types );

		$is_empty_result = FALSE;
		foreach ( $post_types as $post_type ) {
			if ( ! isset( $results[ $post_type ] ) ) {
				$results[ $post_type ] = array();
				$is_empty_result = TRUE;
			}
		}

		if ( $is_empty_result ) {
			global $wpdb;
			$query = "
				SELECT
					ID, post_title, post_status, post_type
				FROM {$wpdb->posts}
				WHERE
					post_type IN('" . implode( "','", $post_types ) . "')
					AND post_status != 'trash'
			";
			if ( ! empty( $orderby ) AND $orderby == 'title' ) {
				$query .= " ORDER BY post_title ASC";
			}
			$posts = array();
			foreach ( $wpdb->get_results( $query ) as $post ) {
				$posts[ $post->ID ] = $post;
			}
			// Filtering by language
			if ( apply_filters( 'us_tr_selected_lang_page', /* Default value */ FALSE ) ) {
				$posts = apply_filters( 'us_filter_posts_by_language', $posts );
			}
			foreach ( $posts as $post ) {
				$results[ $post->post_type ][ $post->ID ] = ( $post->post_title )
					? $post->post_title
					: us_translate( '(no title)' );
			}
		}

		return $results;
	}

	/**
	 * Get list of posts titles by a certain post type
	 * @param string $post_type Post type to get
	 * @param bool $force_no_cache Allow using cache (use FALSE to force not-cached version)
	 * @return array
	 */
	function us_get_posts_titles_for( $post_type, $orderby = 'title', $force_no_cache = TRUE ) {
		$results = (array) us_get_all_posts_titles_for( array( $post_type ), $orderby, $force_no_cache );

		return us_arr_path( $results, $post_type );
	}
}

if ( ! class_exists( 'Us_Vc_Base' ) ) {
	// some functions from Vc_Base, without extending from Vc_Base
	class Us_Vc_Base {

		public function init() {
			add_action( 'wp_head', array( $this, 'addFrontCss' ), 1000 );
		}

		public function is_vc_active() {
			if ( class_exists( 'Vc_Manager' ) ) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		/**
		 * Add css styles for current page and elements design options added w\ editor.
		 */
		public function addFrontCss() {
			$this->addPageCustomCss();
			$this->addShortcodesCustomCss();
		}

		/**
		 * Add custom styles to the page
		 *
		 * @param mixed $id Unique post id
		 * @return void
		 */
		public function addPageCustomCss( $id = NULL ) {
			$ids = array();
			if ( is_front_page() OR is_home() ) {
				$ids[] = get_queried_object_id();
			} elseif ( is_singular() ) {
				$ids[] = ! is_null( $id )
					? $id
					: get_the_ID();
			}

			global $us_page_block_ids;
			if ( ! empty( $us_page_block_ids ) ) {
				$ids = array_merge( $ids, $us_page_block_ids );
			}

			// Get custom styles by available identifiers
			foreach ( array_unique( $ids ) as $id ) {
				if ( $this->is_vc_active() AND 'true' === vc_get_param( 'preview' ) ) {
					$latest_revision = wp_get_post_revisions( $id );
					if ( ! empty( $latest_revision ) ) {
						$array_values = array_values( $latest_revision );
						$id = $array_values[0]->ID;
					}
				}
				if ( $post_custom_css = get_metadata( 'post', $id, '_wpb_post_custom_css', TRUE ) ) {
					echo sprintf( '<style data-type="us_custom-css">%s</style>', $post_custom_css );
				}
			}
		}

		public function addShortcodesCustomCss( $id = NULL ) {
			if ( ! is_singular() AND ! $id ) {
				return;
			}
			if ( ! $id ) {
				$id = get_the_ID();
			}

			if ( $id ) {
				if ( $this->is_vc_active() AND 'true' === vc_get_param( 'preview' ) ) {
					$latest_revision = wp_get_post_revisions( $id );
					if ( ! empty( $latest_revision ) ) {
						$array_values = array_values( $latest_revision );
						$id = $array_values[0]->ID;
					}
				}
				$shortcodes_custom_css = get_metadata( 'post', $id, '_wpb_shortcodes_custom_css', TRUE );
				if ( ! empty( $shortcodes_custom_css ) ) {
					$shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
					echo '<style data-type="vc_shortcodes-custom-css">';
					echo $shortcodes_custom_css;
					echo '</style>';
				}
			}
		}
	}
}

if ( ! function_exists( 'us_get_img_placeholder' ) ) {
	/**
	 * Returns image placeholder
	 * @param string $size The image size
	 * @param string $src_only if TRUE returns file URL, if FALSE returns string with <img>
	 * @return string
	 */
	function us_get_img_placeholder( $size = 'full', $src_only = FALSE ) {

		// Default placeholder
		$size_array = us_get_image_size_params( $size );
		$img_src = US_CORE_URI . '/assets/images/placeholder.svg';
		$img_full = '<img class="g-placeholder"';
		$img_full .= ' src="' . US_CORE_URI . '/assets/images/placeholder.svg"';
		$img_full .= ' width="' . $size_array['width'] . '"';
		$img_full .= ' height="' . $size_array['height'] . '"';
		$img_full .= ' alt="">';

		// If Images Placeholder is set, use its attachment ID
		if ( preg_match( '~^(\d+)(\|(.+))?$~', us_get_option( 'img_placeholder', '' ), $matches ) ) {
			$img_src = wp_get_attachment_image_url( $matches[1], $size );
			$img_full = wp_get_attachment_image( $matches[1], $size, TRUE, array( 'class' => 'g-placeholder' ) );
		}

		if ( $src_only ) {
			return $img_src;
		} else {
			return $img_full;
		}
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
				'echo' => 0,
			)
		);

		if ( $echo ) {
			echo $links;
		} else {
			return $links;
		}
	}
}

if ( ! function_exists( 'us_get_demo_import_config' ) ) {
	/**
	 * Get the config for Demo Import feature from support portal
	 *
	 * @return array|mixed Demos Config
	 */
	function us_get_demo_import_config() {
		global $help_portal_url;
		$transient = 'us_demo_import_config_data_' . US_THEMENAME;

		if ( ! defined( 'US_DEV' ) AND ( FALSE !== $results = get_transient( $transient ) ) ) {
			return $results;
		}

		$help_portal_config_url = $help_portal_url . '/us.api/demos_config/';
		$help_portal_config_url .= ( defined( 'US_ACTIVATION_THEMENAME' ) ) ? strtolower( US_ACTIVATION_THEMENAME ) : strtolower( US_THEMENAME );
		$help_portal_config_url .= defined( 'US_DEV' ) ? '?hidden=1' : '';
		$config_result = us_api_remote_request( $help_portal_config_url, TRUE );

		if ( ! empty( $config_result ) AND ! empty( $config_result['data'] ) ) {
			$config = $config_result['data']; // TODO validation
		} else {
			$config = array();
		}
		set_transient( $transient, $config, HOUR_IN_SECONDS );

		return $config;
	}
}

if ( ! function_exists( 'us_output_design_css' ) ) {
	/**
	 * Prepares all custom styles for page output.
	 * @return string
	 */
	function us_output_design_css( $custom_posts = [] ) {
		global $wp_query;

		// Load css for specific page
		$posts = is_404() ? array() : $wp_query->posts;
		if ( ! empty( $custom_posts ) AND is_array( $custom_posts ) ) {
			$posts = array_merge( $posts, $custom_posts );
		}

		$query_posts_id = array();
		foreach ( $posts as $post ) {
			$query_posts_id[] = $post->ID;
		}

		// 404 Page Not Found
		if ( is_404() AND $page_404_id = us_get_option( 'page_404' ) ) {
			$page_404_id = has_filter( 'us_tr_object_id' )
				? (int) apply_filters( 'us_tr_object_id', $page_404_id, 'page', TRUE )
				: $page_404_id;
			if ( $page_404_id AND $page_404 = get_post( $page_404_id ) ) {
				$posts[] = $page_404;
			}
		}

		// Maintenance Page
		if ( us_get_option( 'maintenance_mode' ) AND $maintenance_page_id = us_get_option( 'maintenance_page' ) ) {
			$maintenance_page_id = has_filter( 'us_tr_object_id' )
				? (int) apply_filters( 'us_tr_object_id', $maintenance_page_id, 'page', TRUE )
				: $maintenance_page_id;
			if ( $maintenance_page = get_post( $maintenance_page_id ) ) {
				$posts[] = $maintenance_page;
			}
		}

		// List of post IDs
		$include_ids = array();

		foreach ( array( 'header', 'titlebar', 'sidebar', 'content', 'footer' ) as $area ) {
			if ( $area_id = us_get_page_area_id( $area ) AND $post = get_post( (int) $area_id ) ) {

				// Specific manipulations with Headers
				if ( $area === 'header' ) {
					$header_options = json_decode( $post->post_content, TRUE );
					$data = us_arr_path( $header_options, 'data', array() );
					foreach ( $data as $key => $item ) {

						// Check Menu element, if it uses Page Block as menu item
						if ( strpos( $key, 'menu' ) === 0 ) {
							$menu = wp_get_nav_menu_object( $item['source'] );
							if ( $menu === FALSE ) {
								continue;
							}
							$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => FALSE ) );
							foreach ( $menu_items as $menu_item ) {
								if ( $menu_item->object === 'us_page_block' ) {
									$posts[] = get_post( (int) $menu_item->object_id );
								}
							}
							unset( $menu, $menu_items );

							// Get Page Block IDs from Popup element
						} else if ( strpos( $key, 'popup' ) === 0 AND ! empty( $item['use_page_block'] ) ) {
							$include_ids[] = (int) $item['use_page_block'];
						}
					}
				} else {
					$posts[] = $post;
				}
			}
		}

		// The Event Calendar plugin uses a non-standard way of receiving data, so we get the id from the request object
		if (
			is_singular( array( 'tribe_events', 'tribe_venue', 'tribe_organizer' ) )
			AND get_queried_object() instanceof WP_Post
		) {
			$include_ids[] = get_queried_object_id();
		}

		// If we are on the search page, we will add the template page from the settings
		if ( $wp_query->is_search AND $search_page = us_get_option( 'search_page' ) ) {
			$include_ids[] = (int) $search_page;
		}

		// Get a custom page to display posts
		if ( get_option( 'show_on_front' ) === 'page' AND $posts_page = us_get_option( 'posts_page' ) ) {
			$include_ids[] = (int) $posts_page;
		}

		// Get page block IDs from popups
		foreach ( $posts as $post ) {
			if (
				strpos( $post->post_content, 'use_page_block="' )
				AND preg_match_all( '/\suse_page_block="(\d+)"/', $post->post_content, $matches )
			) {
				$include_ids = array_merge( $include_ids, $matches[1] );
			}
		}

		$include_ids = array_unique( $include_ids );

		// The include posts to $posts
		if ( ! empty( $include_ids ) ) {
			$include_posts = get_posts(
				array(
					'include' => array_map( 'intval', $include_ids ),
					'post_type' => array_keys( get_post_types() ),
				)
			);
			$posts = array_merge( $include_posts, $posts );
		}

		// Get Templatera IDs and add templates to $posts
		if ( class_exists( 'VcTemplateManager' ) ) {
			$templatera_ids = array();
			foreach ( $posts as $post ) {
				if (
					! empty( $post->post_content )
					AND preg_match_all( '/\[templatera([^\]]+)\]/', $post->post_content, $matches )
				) {
					foreach ( us_arr_path( $matches, '1', array() ) as $atts ) {
						if ( empty( $atts ) ) {
							continue;
						}
						$atts = shortcode_parse_atts( $atts );
						if ( $id = us_arr_path( $atts, 'id' ) ) {
							$templatera_ids[] = $id;
						}
					}
				}
			}
			if ( ! empty( $templatera_ids ) ) {
				$include_posts = get_posts(
					array(
						'include' => array_map( 'intval', $templatera_ids ),
						'post_type' => 'templatera',
						'posts_per_page' => - 1,
					)
				);
				$posts = array_merge( $include_posts, $posts );
			}
		}

		/**
		 * Collect all Page Blocks into one variable
		 * @param WP_Post $post
		 * @return void
		 */
		$func_acc_posts = function ( $post ) use ( &$posts ) {
			if ( $post instanceof WP_Post ) {
				$posts[ $post->ID ] = $post;
			}
		};

		foreach ( $posts as $post ) {
			if ( $post instanceof WP_Post AND strpos( $post->post_content, 'us_page_block' ) !== FALSE ) {
				us_get_recursive_parse_page_block( $post, $func_acc_posts );
			}
		}

		// Gets custom CSS from shortcodes
		$jsoncss_collection = array();
		foreach ( $posts as $post ) {

			// Do not display internal styles for archives page
			if ( in_array( $post->ID, $query_posts_id ) AND count( $query_posts_id ) > 1 ) {
				continue;
			}

			$jsoncss_data = get_post_meta( $post->ID, '_us_jsoncss_data', TRUE );
			if ( $jsoncss_data === '' AND function_exists( 'us_update_postmeta_for_custom_css' ) ) {
				$jsoncss_data = us_update_postmeta_for_custom_css( $post );
			}
			if ( ! empty( $jsoncss_data ) AND is_array( $jsoncss_data ) ) {
				foreach ( $jsoncss_data as $jsoncss ) {
					if ( ! empty( $jsoncss ) AND is_string( $jsoncss ) ) {
						$class_name = us_get_design_css_class( $jsoncss );
						$jsoncss = rawurldecode( $jsoncss );
						if ( $jsoncss AND $jsoncss = json_decode( $jsoncss, TRUE ) ) {
							foreach ( array( 'default', 'tablets', 'mobiles' ) as $device_type ) {
								if ( $css_options = us_arr_path( $jsoncss, $device_type, FALSE ) ) {
									if (
										! empty( $jsoncss_collection[ $device_type ] )
										AND in_array( $class_name, $jsoncss_collection[ $device_type ] )
									) {
										continue;
									}
									$css_options = apply_filters( 'us_output_design_css_options', $css_options, $device_type );
									$jsoncss_collection[ $device_type ][ $class_name ] = $css_options;
								}
							}
						};
					}
				}
			}
		}

		// Apply filters
		$jsoncss_collection = apply_filters( 'us_output_design_css', $jsoncss_collection, $posts );

		// Generate CSS code and output data
		if ( $custom_css = us_jsoncss_compile( $jsoncss_collection ) ) {
			echo sprintf( '<style id="us-design-options-css">%s</style>', $custom_css );
		}
	}

	add_action( 'us_before_closing_head_tag', 'us_output_design_css', 10 );
}

if ( ! function_exists( 'us_filter_design_css_colors' ) ) {
	/**
	 * Replace variable colors with values
	 *
	 * @param array $css_options
	 * @return array
	 */
	function us_filter_design_css_colors( $css_options ) {
		// key => with_gradient
		$keys = array(
			'color' => FALSE,
			'background' => TRUE,
			'background-color' => TRUE,
			'border-color' => FALSE,
			'box-shadow-color' => FALSE,
		);
		foreach ( $keys as $key => $with_gradient ) {
			if ( ! empty( $css_options[ $key ] ) ) {
				$css_options[ $key ] = us_get_color( $css_options[ $key ], $with_gradient );
			}
		}

		return $css_options;
	}

	add_filter( 'us_output_design_css_options', 'us_filter_design_css_colors', 1, 1 );
}

if ( ! function_exists( 'us_get_recursive_parse_page_block' ) ) {
	/**
	 * Recursive parse page_block
	 *
	 * @param WP_Post $post The post
	 * @param function $callback The callback `function( $post, $atts ){}`
	 * @param integer $max_level The max level
	 * @param integer $current_level The current level
	 *
	 * @return array page block ids
	 */
	function us_get_recursive_parse_page_block( $post, $callback = NULL, $max_level = 15, $current_level = 1 ) {
		$output = array();
		if ( $current_level > $max_level ) {
			return $output;
		}
		global $us_recursive_parse_page_blocks;
		if ( ! is_array( $us_recursive_parse_page_blocks ) ) {
			$us_recursive_parse_page_blocks = array();
		}
		if ( $post instanceof WP_Post AND ! empty( $post->post_content ) ) {
			$page_block_pattern = '/' . get_shortcode_regex( array( 'us_page_block' ) ) . '/';
			if ( preg_match_all( $page_block_pattern, $post->post_content, $matches ) ) {
				foreach ( us_arr_path( $matches, '3', array() ) as $atts ) {
					$atts = shortcode_parse_atts( $atts );
					$output[] = $id = us_arr_path( $atts, 'id' );
					if ( ! in_array( $id, array_keys( $us_recursive_parse_page_blocks ) ) ) {
						$us_recursive_parse_page_blocks[ $id ] = get_post( $id );
					}
					$next_post = $us_recursive_parse_page_blocks[ $id ];
					if ( is_callable( $callback ) ) {
						call_user_func( $callback, $next_post, $atts );
					}
					if ( $next_post instanceof WP_Post AND strrpos( $next_post->post_content, 'us_page_block' ) !== FALSE ) {
						$output = array_merge( $output, us_get_recursive_parse_page_block( $next_post, $callback, $max_level, ++ $current_level ) );
					}
				}
			}
		}

		return (array) $output;
	}
}

if ( ! function_exists( 'us_find_element_in_post_page_blocks' ) ) {
	/**
	 * Check for shortcode in all nested page blocks
	 *
	 * @param inteer $post_id The post identifier
	 * @param string $find_value The find value
	 *
	 * @return boolean
	 */
	function us_find_element_in_post_page_blocks( $post_id, $find_value = '' ) {
		$result = FALSE;
		if (
			! empty( $find_value )
			AND ! empty( $post_id )
			AND $post = get_post( $post_id )
			AND function_exists( 'us_get_recursive_parse_page_block' )
		) {
			us_get_recursive_parse_page_block(
				$post, function ( $post ) use ( &$result, $find_value ) {
				if ( $result ) {
					return;
				}
				if ( $post instanceof WP_Post ) {
					$result = stripos( $post->post_content, $find_value ) !== FALSE;
				}
			}
			);
		}

		return $result;
	}
}

if ( ! function_exists( 'us_get_design_css_class' ) ) {
	/**
	 * Get a unique class for custom CSS styles
	 *
	 * @param string $str The css
	 * @param string $class_name The prefix for css class name
	 * @return string
	 */
	function us_get_design_css_class( $str, $class_name = 'us_custom' ) {
		if ( ! empty( $str ) AND ! empty( $class_name ) ) {
			return $class_name . '_' . hash( 'crc32b', $str );
		}

		return '';
	}
}

if ( ! function_exists( 'us_jsoncss_compile' ) ) {
	/**
	 * Compilation of jsoncss styles
	 * @param array $jsoncss_collection
	 * @param array $device_breakpoints
	 * @return string
	 */
	function us_jsoncss_compile( $jsoncss_collection, $device_breakpoints = array() ) {

		$tablets_breakpoint = intval( us_get_option( 'tablets_breakpoint', '1024px' ) );
		$mobiles_breakpoint = intval( us_get_option( 'mobiles_breakpoint', '600px' ) );

		$device_breakpoints = array_merge(
			array(
				'default' => '',
				'tablets' => '(min-width:' . $mobiles_breakpoint . 'px) and (max-width:' . ( $tablets_breakpoint - 1 ) . 'px)',
				'mobiles' => '(max-width:' . ( $mobiles_breakpoint - 1 ) . 'px)',
			), $device_breakpoints
		);

		$css_mask = array(
			'background' => 'color image repeat attachment position size',
			'padding' => 'top right bottom left',
			'margin' => 'top right bottom left',
			'border-style' => 'top right bottom left',
			'border-width' => 'top right bottom left',
			'border' => 'width style color',
			'box-shadow' => 'h-offset v-offset blur spread color',
			'font' => 'style weight size height family',
		);
		foreach ( $css_mask as &$mask_keys ) {
			$mask_keys = explode( ' ', $mask_keys );
		}
		unset( $mask_keys );

		$default_options = us_config( 'elements_design_options.css.params', array() );
		$default_option_types = wp_list_pluck( $default_options, 'type' );

		/**
		 * Optimization of the CSS options
		 * @param array $css_options
		 * @return array
		 */
		$css_optimize = function ( $css_options ) use ( $css_mask, $default_option_types ) {
			// Normalization of css parameters
			foreach ( $css_options as $prop_name => $prop_value ) {
				if ( isset( $default_option_types[ $prop_name ] ) AND $default_option_types[ $prop_name ] === 'upload' ) {
					// If the field is an upload, then we will receive and establish a link to the image
					if (
						strpos( $prop_value, '|' ) !== FALSE
						AND $image_url = call_user_func_array( 'wp_get_attachment_image_url', explode( '|', $prop_value ) )
					) {
						$prop_value = sprintf( 'url(%s)', $image_url );
					}
				}

				// Generate correct font-family value
				if ( $prop_name == 'font-family' ) {
					$prop_value = us_get_font_css( $prop_value, TRUE );
				}

				$css_options[ $prop_name ] = trim( $prop_value );

				// border-style to border-{position}-style provided that there is a width of this border
				if ( $prop_name === 'border-style' AND isset( $css_mask['border-width'] ) ) {
					foreach ( $css_mask['border-width'] as $position ) {
						$_prop = sprintf( 'border-%s-width', $position );
						if ( isset( $css_options[ $_prop ] ) AND $css_options[ $_prop ] != '' ) {
							$css_options[ sprintf( 'border-%s-style', $position ) ] = $css_options[ $prop_name ];
						}
					}
					unset( $css_options[ $prop_name ] );
				}
			}

			// Preparing styles for $css_mask
			$map_values = array();
			foreach ( $css_mask as $mask_name => $map_keys ) {
				// Grouping parameters by $css_mask
				foreach ( $map_keys as $mask_value ) {

					switch ( $mask_name ) {
						case 'border-width':
							$prop_name = sprintf( 'border-%s-width', $mask_value );
							break;
						case 'border-style':
							$prop_name = sprintf( 'border-%s-style', $mask_value );
							break;
						default:
							$prop_name = $mask_name . '-' . $mask_value;
							break;
					}

					if ( $prop_name == 'font-height' ) {
						$prop_name = 'line-height';
					}

					if ( isset( $css_options[ $prop_name ] ) AND trim( $css_options[ $prop_name ] ) != '' ) {
						$map_values[ $mask_name ][ $mask_value ] = $css_options[ $prop_name ];
					} // If there is at least one parameter for box-shadow, then fill in the missing ones with default
					elseif (
						$mask_value === 'position'
						AND empty( $map_values[ $mask_name ][ $mask_value ] )
						AND ! empty( $css_options['background-size'] )
					) {
						// Set default value for background-position
						$map_values[ $mask_name ][ $mask_value ] = 'left top';
					} elseif (
						strpos( implode( ' ', array_keys( $css_options ) ), 'box-shadow-' ) !== FALSE
						AND strpos( $prop_name, 'box-shadow-' ) === 0
					) {
						$map_values[ $mask_name ][ $mask_value ] = ( $mask_value == 'color' )
							? 'transparent' // Default color
							: '0';
					}

					// Combine the same options for padding, margin and border-width
					if (
						in_array( $mask_name, array( 'padding', 'margin', 'border-width', 'border-style' ) )
						AND isset( $map_values[ $mask_name ] )
						AND count( $map_values[ $mask_name ] ) === count( $map_keys )
						AND $unique_map_values = array_unique( $map_values[ $mask_name ] )
						AND count( $unique_map_values ) === 1
					) {
						$css_options[ $mask_name ] = array_shift( $unique_map_values );
					}
				}
			}

			// Checking css masks and adjusting parameters
			foreach ( $map_values as $mask_name => &$mask_props ) {
				if ( count( $mask_props ) === count( $css_mask[ $mask_name ] ) OR $mask_name == 'background' ) {

					// Clear unwanted params
					foreach ( array_keys( $mask_props ) as $mask_prop ) {

						// Creating a prop name
						$mask_prop = ( $mask_name === 'border-width' )
							? sprintf( 'border-%s-width', $mask_prop )
							: $mask_name . '-' . $mask_prop;
						unset( $css_options[ $mask_prop ] );
					}

					// Adjust background options before merging
					if ( $mask_name == 'background' ) {

						// If there is a gradinet, then add it to the end of the parameters
						if ( ! empty( $mask_props['color'] ) AND strpos( $mask_props['color'], 'gradient' ) !== FALSE ) {
							if ( ! empty( $mask_props['image'] ) ) {
								$_gradient = ', ' . $mask_props['color'];
								unset( $mask_props['color'] );

								end( $mask_props );
								$mask_props[ key( $mask_props ) ] .= $_gradient;
							} else {
								$mask_props = array_slice( $mask_props, 0, 1, TRUE );
							}
						}
						if ( ! empty( $mask_props['size'] ) ) {
							$mask_props['size'] = '/ ' . $mask_props['size'];
						}
					}

					// Correction for the font parameter
					if ( $mask_name === 'font' AND isset( $mask_props['height'] ) ) {
						$mask_props['height'] = '/ ' . $mask_props['height'];
						unset( $css_options['line-height'] );
					}

					// Remove border-{position}-style properties
					if ( $mask_name === 'border-style' ) {
						foreach ( array_keys( $mask_props ) as $position ) {
							unset( $css_options[ sprintf( 'border-%s-style', $position ) ] );
						}
					}
					// Combine parameters in one line
					if ( ! isset( $css_options[ $mask_name ] ) OR $css_options[ $mask_name ] == '' ) {
						$css_options[ $mask_name ] = implode( ' ', $map_values[ $mask_name ] );
					}
				} else {
					unset( $map_values[ $mask_name ] );
				}
			}
			unset( $mask_props, $map_values );

			return $css_options;
		};

		$output_css = '';

		if ( ! empty( $jsoncss_collection ) ) {

			// Optimization and the formation of CSS
			foreach ( array_keys( $device_breakpoints ) as $device_type ) {
				if ( ! empty( $jsoncss_collection[ $device_type ] ) ) {
					foreach ( $jsoncss_collection[ $device_type ] as $class_name => &$css_options ) {
						$css_options = $css_optimize( $css_options );
					}
					unset( $css_options );
				}
			}

			// Convert options to css styles
			foreach ( $device_breakpoints as $device_type => $media ) {
				if ( ! empty( $jsoncss_collection[ $device_type ] ) ) {
					$media_css = '';
					foreach ( $jsoncss_collection[ $device_type ] as $class_name => $css_options ) {
						$styles = '';

						// Remove duplicate styles
						if ( 'default' !== $device_type AND ! empty( $jsoncss_collection['default'][ $class_name ] ) ) {
							$default_css_options = $jsoncss_collection['default'][ $class_name ];
							foreach ( $css_options as $prop_name => $prop_value ) {
								if ( isset( $default_css_options[ $prop_name ] ) AND $default_css_options[ $prop_name ] === $prop_value ) {
									unset( $css_options[ $prop_name ] );
								}
							}
						}
						foreach ( $css_options as $prop_name => $prop_value ) {
							if ( trim( $prop_value ) == '' ) {
								continue;
							}
							$styles .= sprintf( '%s:%s!important;', $prop_name, $prop_value );
						}
						if ( ! empty( $styles ) ) {
							$media_css .= sprintf( '.%s{%s}', $class_name, $styles );
						}
					}
					if ( empty( $media_css ) ) {
						continue;
					}
					$output_css .= ! empty( $media )
						? sprintf( '@media %s {%s}', $media, $media_css )
						: $media_css;
				}
			}
		}

		return us_minify_css( $output_css );
	}
}

if ( ! function_exists( 'us_remove_url_protocol' ) ) {
	/**
	 * Removing a protocol from a link
	 *
	 * @param string $url
	 * @return string
	 */
	function us_remove_url_protocol( $url ) {
		return str_replace( array( 'http:', 'https:' ), '', $url );
	}
}

if ( ! function_exists( 'us_get_terms_by_slug' ) ) {

	/**
	 * Get all terms by taxonomy slug
	 *
	 * @param array $slug
	 * @param integer $offset
	 * @param integer $number_per_page
	 * @param string $name
	 * @return array
	 */
	function us_get_all_terms_by_slug( $taxonomies_slug, $offset = 0, $number_per_page = 50, $name = '' ) {
		if ( empty( $taxonomies_slug ) OR ! is_array( $taxonomies_slug ) ) {
			return array();
		}

		$result = array();
		static $results = array();

		$func_args = func_get_args();
		$taxonomies_unique_keys = array();
		// Making keys for cache, combining all args (taxonomy slug, offset and number per page) in one key
		foreach ( array_unique( array_map( 'trim', $taxonomies_slug ) ) as $taxonomy ) {
			$func_args[ /* $taxonomies_slug */
			0] = $taxonomy;
			$taxonomy_key = md5( json_encode( $func_args ) );
			if ( ! empty( $results[ $taxonomy ][ $taxonomy_key ] ) ) {
				$result[ $taxonomy ] = $results[ $taxonomy ][ $taxonomy_key ];
			} else {
				$taxonomies_unique_keys[ $taxonomy ] = $taxonomy_key;
			}
		}
		unset( $func_args );

		// If the list of keys is empty then return the result
		if ( empty( $taxonomies_unique_keys ) ) {
			return $result;
		}

		$query_args = array(
			//'fields' => 'id=>name',
			'hide_empty' => TRUE,
			'number' => (int) $number_per_page,
			'offset' => (int) $offset,
			'suppress_filter' => FALSE,
			'taxonomy' => array_keys( $taxonomies_unique_keys ),
		);

		if ( ! empty( $name ) AND count( $taxonomies_slug ) === 1 ) {
			if ( substr( $name, 0, strlen( 'params:' ) ) === 'params:' ) {
				$slug = explode( ',', substr( $name, strlen( 'params:' ) ) );
				$query_args['slug'] = array_unique( $slug );
			} else {
				$query_args['name__like'] = trim( $name );
			}
		}

		foreach ( get_terms( $query_args ) as $term ) {
			if ( $taxonomy_key = us_arr_path( $taxonomies_unique_keys, $term->taxonomy ) ) {
				// Data to be saved to cache
				$results[ $term->taxonomy ][ $taxonomy_key ][ $term->slug ] = $term->name;
				// Data to return as result
				$result[ $term->taxonomy ][ $term->slug ] = $term->name;
			}
		}

		return (array) $result;
	}

	/**
	 * Get terms by taxonomy slug
	 *
	 * @param string $slug
	 * @param integer $offset
	 * @param integer $number_per_page
	 * @param string $name
	 * @return array
	 */
	function us_get_terms_by_slug( $taxonomy_slug, $offset = 0, $number_per_page = 50, $name = '' ) {
		$results = (array) us_get_all_terms_by_slug( array( $taxonomy_slug ), $offset, $number_per_page, $name );

		return ! empty( $results[ $taxonomy_slug ] )
			? $results[ $taxonomy_slug ]
			: array();
	}
}

if ( ! function_exists( 'us_get_aspect_ratio_values' ) ) {
	/**
	 * Calculate Aspect Ratio width and height, used in Grids
	 *
	 * @param string $_ratio
	 * @param string $_width
	 * @param string $_height
	 * @return array
	 */
	function us_get_aspect_ratio_values( $_ratio = '1x1', $_width = '1', $_height = '1' ) {
		if ( $_ratio == '4x3' ) {
			$_width = 4;
			$_height = 3;
		} elseif ( $_ratio == '3x2' ) {
			$_width = 3;
			$_height = 2;
		} elseif ( $_ratio == '2x3' ) {
			$_width = 2;
			$_height = 3;
		} elseif ( $_ratio == '3x4' ) {
			$_width = 3;
			$_height = 4;
		} elseif ( $_ratio == '16x9' ) {
			$_width = 16;
			$_height = 9;
		} elseif ( $_ratio == 'custom' ) {
			$_width = floatval( str_replace( ',', '.', preg_replace( '/^[^\d.,]+$/', '', $_width ) ) );
			if ( $_width <= 0 ) {
				$_width = 1;
			}
			$_height = floatval( str_replace( ',', '.', preg_replace( '/^[^\d.,]+$/', '', $_height ) ) );
			if ( $_height <= 0 ) {
				$_height = 1;
			}
		} else {
			$_width = $_height = 1;
		}

		return array( $_width, $_height );
	}
}

if ( ! function_exists( 'us_filter_posts_by_language' ) ) {
	/**
	 * Filters posts and remove unnecessary translations from the list
	 *
	 * @param $array $posts
	 * @return array
	 */
	function us_filter_posts_by_language( $posts ) {
		if (
			has_filter( 'us_tr_current_language' )
			AND ! empty( $posts )
			AND is_array( $posts )
		) {
			$current_lang = apply_filters( 'us_tr_current_language', NULL );
			if ( ! is_null( $current_lang ) ) {
				foreach ( $posts as $post_id => $post ) {

					// Exclude Grid Layouts
					if ( get_post_type( $post_id ) === 'us_grid_layout' ) {
						continue;
					}

					$post_lang_code = apply_filters( 'us_tr_get_post_language_code', (int) $post_id );
					if ( ! is_null( $post_lang_code ) AND $current_lang !== $post_lang_code ) {
						unset( $posts[ $post_id ] );
					}
				}
			}
		}

		return $posts;
	}

	add_filter( 'us_filter_posts_by_language', 'us_filter_posts_by_language', 10, 1 );
}

if ( ! function_exists( 'us_set_time_limit' ) ) {
	function us_set_time_limit( $limit = 0 ) {
		$limit = intval( $limit );
		if (
			function_exists( 'set_time_limit' )
			&& FALSE === strpos( ini_get( 'disable_functions' ), 'set_time_limit' )
			&& ! ini_get( 'safe_mode' )
		) {
			set_time_limit( $limit );
		} elseif ( function_exists( 'ini_set' ) ) {
			ini_set( 'max_execution_time', $limit );
		}
	}
}

if ( ! function_exists( 'us_replace_dynamic_value' ) ) {
	/**
	 * Filters the string via replacing {{}} with custom field value or some other data
	 *
	 * @param string $string
	 * @param string $elm_context : shortcode / grid / header
	 * @param string $grid_object_type 'post' / 'term' - used for grid context only
	 * @return string
	 */
	function us_replace_dynamic_value( $string, $elm_context, $grid_object_type = 'post' ) {
		if ( ! is_string( $string ) ) {
			return '';
		}

		$pattern = '/{{([^}]+)}}/';

		if ( ! empty( $string ) AND preg_match( $pattern, $string ) ) {
			/**
			 * Filter the string, only if it contains the {{}} value
			 *
			 * @param array $matches 0 - variable code, 1 - variable key
			 * @return string
			 */
			$func_replace_callback = function ( $matches ) use ( $elm_context, $grid_object_type ) {

				// Check the current object type and ID
				// case: the grid item showing single term
				if ( $elm_context == 'grid' AND $grid_object_type == 'term' ) {
					global $us_grid_term;
					$object_id = $us_grid_term->term_id;
					$object_type = 'term';

					// case: the current single term
				} elseif (
					$elm_context != 'grid'
					AND (
						is_tax()
						OR is_tag()
						OR is_category()
					)
					AND $term = get_queried_object()
				) {
					$object_id = $term->term_id;
					$object_type = 'term';

					// case: the current single post or grid item showing single post.
				} else {
					$object_id = get_the_ID();
					$object_type = 'post';
				}

				$result = '';

				// Predefined behavior: change '{{comment_count}}' to comments amount of the current post
				if ( $matches[0] === '{{comment_count}}' AND $object_type === 'post' ) {
					$comments_amount = get_comment_count( $object_id );
					$result = $comments_amount['approved'];

					// Check the metadata existance and replace by its value
				} elseif ( $meta_value = get_metadata( $object_type, $object_id, $matches[1], TRUE ) ) {
					$result = is_string( $meta_value ) ? $meta_value : '';
				}

				return make_clickable( $result );
			};

			// Apply search and processing of variables
			return (string) preg_replace_callback( $pattern, $func_replace_callback, $string );
		}

		return $string;
	}
}

if ( ! function_exists( 'us_register_context_layout' ) ) {
	/**
	 * Register context layout
	 *
	 * @param string $layout
	 * @return void
	 */
	function us_register_context_layout( $layout ) {
		global $us_context_layout;
		$us_context_layout = (string) strtolower( $layout );
	}
}

if ( ! function_exists( 'us_is_faqs_page' ) ) {
	/**
	 * The current page is FAQs
	 * @return bool
	 */
	function us_is_faqs_page() {
		return (
			is_singular( 'page' )
			AND us_get_option( 'schema_markup', FALSE )
			AND us_get_option( 'schema_faqs_page', NULL ) == get_the_ID()
		);
	}
}

if ( ! function_exists( 'us_get_available_icon_sets' ) ) {
	/**
	 * Get available icon sets
	 * @return array
	 */
	function us_get_available_icon_sets() {
		$icon_sets = us_config( 'icon-sets', array() );
		foreach ( $icon_sets as $icon_slug => $icon_set ) {
			if ( us_get_option( 'icons_' . $icon_slug ) === 'none' ) {
				unset( $icon_sets[ $icon_slug ] );
			}
		}

		return $icon_sets;
	}
}

if ( ! function_exists( 'us_amp' ) ) {
	/**
	 * The current page is AMP page
	 * @return bool
	 */
	function us_amp() {
		if ( function_exists( 'amp_is_request' ) ) {
			return amp_is_request();
		}

		return FALSE;
	}
}

if ( ! function_exists( 'us_map_get_bbox' ) ) {
	/**
	 * Get bounding box from coordinates for OpenStreetMap, used for AMP only
	 * https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames
	 *
	 * @param float $latitude
	 * @param float $longitude
	 * @param int $zoom
	 * @return string
	 */
	function us_map_get_bbox( $latitude, $longitude, $zoom ) {
		$width = 1000;
		$height = 600;
		$tile_size = 256;

		$xtile = floor( ( ( $longitude + 180 ) / 360 ) * pow( 2, $zoom ) );
		$ytile = floor( ( 1 - log( tan( deg2rad( $latitude ) ) + 1 / cos( deg2rad( $latitude ) ) ) / pi() ) / 2 * pow( 2, $zoom ) );

		$xtile_s = ( $xtile * $tile_size - $width / 2 ) / $tile_size;
		$ytile_s = ( $ytile * $tile_size - $height / 2 ) / $tile_size;
		$xtile_e = ( $xtile * $tile_size + $width / 2 ) / $tile_size;
		$ytile_e = ( $ytile * $tile_size + $height / 2 ) / $tile_size;

		$south = us_map_lon_lat( $xtile_s, $ytile_s, $zoom );
		$east = us_map_lon_lat( $xtile_e, $ytile_e, $zoom );

		return ( implode( ',', $south ) . ',' . implode( ',', $east ) );
	}
}

if ( ! function_exists( 'us_map_lon_lat' ) ) {
	/**
	 * Get Longitude and Latitude based on tile size and zoom for OpenStreetMap, used for AMP only
	 *
	 * @param $xtile float from us_map_get_bbox()
	 * @param $ytile float from us_map_get_bbox()
	 * @param $zoom int zoom from us_map_get_bbox()
	 * @return array
	 */
	function us_map_lon_lat( $xtile, $ytile, $zoom ) {
		$n = pow( 2, $zoom );
		$lon_deg = $xtile / $n * 360.0 - 180.0;
		$lat_deg = rad2deg( atan( sinh( pi() * ( 1 - 2 * $ytile / $n ) ) ) );

		return array( $lon_deg, $lat_deg );
	}
}

if ( ! function_exists( 'us_user_profile_html' ) ) {
	/**
	 * Get profile info for Login element/widget
	 * @param $logout_redirect
	 * @param bool $hidden
	 * @return string
	 */
	function us_user_profile_html( $logout_redirect, $hidden = FALSE ) {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			return '';
		}

		// Redirect to the current page, if other is not set
		if ( empty( $logout_redirect ) ) {
			$logout_redirect = site_url( $_SERVER['REQUEST_URI'] );
		}

		$output = '<div class="w-profile' . ( $hidden ? ' hidden' : '' ) . '">';
		$output .= '<a class="w-profile-link for_user" href="' . esc_url( admin_url( 'profile.php' ) ) . '">';
		$output .= '<span class="w-profile-avatar">' . get_avatar( get_current_user_id(), '64' ) . '</span>';
		$output .= '<span class="w-profile-name">' . wp_get_current_user()->display_name . '</span>';
		$output .= '</a>';
		$output .= '<a class="w-profile-link for_logout" href="' . esc_url( wp_logout_url( $logout_redirect ) ) . '">' . us_translate( 'Log Out' ) . '</a>';
		$output .= '</div>';

		return $output;
	}
}
