<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

require US_CORE_DIR . 'usof/functions/fallback.php';

if ( is_admin() ) {
	if ( ! defined( 'DOING_AJAX' ) OR ! DOING_AJAX ) {
		// Front-end interface
		require US_CORE_DIR . 'usof/functions/interface.php';
		require US_CORE_DIR . 'usof/functions/meta-box.php';
		require US_CORE_DIR . 'usof/functions/menu-dropdown.php';
	} elseif ( ( isset( $_POST['action'] ) AND substr( $_POST['action'], 0, 5 ) == 'usof_' ) OR ( isset( $_GET['action'] ) AND substr( $_GET['action'], 0, 5 ) == 'usof_' ) ) {
		// Ajax methods
		require US_CORE_DIR . 'usof/functions/ajax.php';
		require US_CORE_DIR . 'usof/functions/ajax-menu-dropdown.php';
	}
}

/**
 * Get theme option or return default value
 *
 * @param string $name
 * @param mixed $default_value
 *
 * @return mixed
 */
function usof_get_option( $name, $default_value = NULL ) {
	global $usof_options;
	usof_load_options_once();

	if ( $default_value === NULL ) {
		$default_value = usof_defaults( $name );
	}

	$value = isset( $usof_options[ $name ] ) ? $usof_options[ $name ] : $default_value;

	return apply_filters( 'usof_get_option_' . $name, $value );
}

/**
 * Get default value for a certain USOF field
 * @param array $field
 * @return string
 */
function usof_get_default( &$field ) {

	$no_values_types = array(
		'backup',
		'heading',
		'message',
		'transfer',
		'wrapper_start',
		'wrapper_end',
	);

	$selectable_types = array(
		'imgradio',
		'radio',
		'select',
		'style_scheme',
	);

	if ( ! isset( $field['type'] ) OR in_array( $field['type'], $no_values_types ) ) {
		return '';
	}

	// Using first value as standard for selectable types
	if ( ! isset( $field['std'] ) AND in_array( $field['type'], $selectable_types ) ) {
		if ( ! empty( $field['options'] ) AND is_array( $field['options'] ) ) {
			$field['std'] = key( $field['options'] );
			reset( $field['options'] );
		}
	}

	return isset( $field['std'] ) ? $field['std'] : '';
}

/**
 * Get default values
 *
 * @param string $key If set, retreive only one default value
 *
 * @return mixed Array of values or a single value if the $key is specified
 */
function usof_defaults( $key = NULL ) {
	$config = us_config( 'theme-options' );

	$values = array();
	foreach ( $config as &$section ) {
		if ( ! isset( $section['fields'] ) ) {
			continue;
		}
		foreach ( $section['fields'] as $field_id => &$field ) {
			if ( $key !== NULL AND $field_id != $key ) {
				continue;
			}
			if ( isset( $values[ $field_id ] ) ) {
				continue;
			}

			if ( isset( $field['type'] ) AND $field['type'] == 'style_scheme' ) {
				$options = array_keys( us_config( 'color-schemes' ) );
				if ( empty( $options ) ) {
					continue;
				}
				$field['std'] = isset( $field['std'] ) ? $field['std'] : $options[0];

				// If theme has default style scheme, it's values will be used as standard as well
				$values = array_merge( $values, us_config( 'color-schemes.' . $field['std'] . '.values' ) );
			}

			$default_value = usof_get_default( $field );
			if ( $default_value !== NULL ) {
				$values[ $field_id ] = $default_value;
			}
		}
	}

	if ( $key !== NULL ) {
		return isset( $values[ $key ] ) ? $values[ $key ] : '';
	}

	return $values;
}

/**
 * If the options were not loaded, load them
 */
function usof_load_options_once( $force_reload = FALSE ) {
	global $usof_options;
	if ( isset( $usof_options ) AND ! $force_reload ) {
		return;
	}
	if ( ! defined( 'US_THEMENAME' ) ) {
		return;
	}
	$usof_options = get_option( 'usof_options_' . US_THEMENAME );
	if ( $usof_options === FALSE ) {
		// Trying to fetch the old good SMOF options
		$usof_options = get_option( US_THEMENAME . '_options' );
		if ( $usof_options !== FALSE ) {
			// Disabling the old options autoload
			update_option( US_THEMENAME . '_options', $usof_options, FALSE );
		} else {
			// Not defined yet, using default values
			$usof_options = usof_defaults();
		}
		update_option( 'usof_options_' . US_THEMENAME, $usof_options, TRUE );
	}

	$usof_options = apply_filters( 'usof_load_options_once', $usof_options );
}

/**
 * Save current usof options values from global $usof_options variable to database
 *
 * @param array $updated_options Array of the new options values
 */
function usof_save_options( $updated_options ) {

	if ( ! is_array( $updated_options ) OR empty( $updated_options ) ) {
		return;
	}

	global $usof_options;
	usof_load_options_once();

	do_action( 'usof_before_save', $updated_options );

	$usof_options = has_filter( 'usof_updated_options' )
		? apply_filters( 'usof_updated_options', $updated_options )
		: $updated_options;

	update_option( 'usof_options_' . US_THEMENAME, $usof_options, TRUE );

	do_action( 'usof_after_save', $updated_options );
}

/**
 * Save a backup with current usof options values
 */
if ( ! function_exists( 'usof_backup' ) ) {
	function usof_backup() {
		global $usof_options;
		usof_load_options_once();

		$backup = array(
			'time' => current_time( 'mysql', TRUE ),
			'usof_options' => $usof_options,
		);

		update_option( 'usof_backup_' . US_THEMENAME, $backup, FALSE );

	}
}

/**
 * Checks if the showing condition is true
 *
 * Note: at any possible syntax error we choose to show the field so it will be functional anyway.
 *
 * @param array $condition Showing condition
 * @param array $values Current values
 *
 * @return bool
 */
function usof_execute_show_if( $condition, &$values = NULL ) {
	if ( ! is_array( $condition ) OR count( $condition ) < 3 ) {
		// Wrong condition
		$result = TRUE;
	} elseif ( in_array( strtolower( $condition[1] ), array( 'and', 'or' ) ) ) {
		// Complex or / and statement
		$result = usof_execute_show_if( $condition[0], $values );
		$index = 2;
		while ( isset( $condition[ $index ] ) ) {
			$condition[ $index - 1 ] = strtolower( $condition[ $index - 1 ] );
			if ( $condition[ $index - 1 ] == 'and' ) {
				$result = ( $result AND usof_execute_show_if( $condition[ $index ], $values ) );
			} elseif ( $condition[ $index - 1 ] == 'or' ) {
				$result = ( $result OR usof_execute_show_if( $condition[ $index ], $values ) );
			}
			$index = $index + 2;
		}
	} else {
		if ( ! isset( $values[ $condition[0] ] ) ) {
			if ( $condition[1] == '=' AND ( ! in_array( $condition[2], array( 0, '', FALSE, NULL ) ) ) ) {
				return FALSE;
			} elseif ( $condition[1] == '!=' AND in_array( $condition[2], array( 0, '', FALSE, NULL ) ) ) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
		$value = $values[ $condition[0] ];
		if ( $condition[1] == '=' ) {
			if ( is_array( $condition[2] ) ) {
				$result = ( in_array( $value, $condition[2] ) );
			} else {
				$result = ( $value == $condition[2] );
			}
		} elseif ( $condition[1] == '!=' ) {
			if ( is_array( $condition[2] ) ) {
				$result = ( ! in_array( $value, $condition[2] ) );
			} else {
				$result = ( $value != $condition[2] );
			}
		} elseif ( $condition[1] == 'has' ) {
			$result = ( ! is_array( $value ) OR in_array( $condition[2], $value ) );
		} elseif ( $condition[1] == '<=' ) {
			$result = ( $value <= $condition[2] );
		} elseif ( $condition[1] == '<' ) {
			$result = ( $value < $condition[2] );
		} elseif ( $condition[1] == '>' ) {
			$result = ( $value > $condition[2] );
		} elseif ( $condition[1] == '>=' ) {
			$result = ( $value >= $condition[2] );
		} else {
			$result = TRUE;
		}
	}

	return $result;
}

/**
 * Output preview for color scheme used by ajax and style_scheme
 *
 * @param array $scheme
 *
 * @return string
 */
function usof_color_scheme_preview( $scheme ) {
	if ( empty( $scheme ) ) {
		return '';
	}

	$values = us_arr_path( $scheme, 'values', array() );

	$preview = '<div class="usof-scheme-preview">';
	// Header
	$preview .= '<div class="preview_header" style="background:' . us_get_color( $values['color_header_middle_bg'], /* Gradient */ TRUE ) . ';"></div>';
	// Content
	$preview .= '<div class="preview_content" style="background:' . us_get_color( $values['color_content_bg'], /* Gradient */ TRUE ) . ';">';
	// Heading
	$preview .= '<div class="preview_heading" style="color:' . us_get_color( $values['color_content_heading'] ) . ';">' . trim( esc_html( $scheme['title'] ) ) . '</div>';
	// Text
	$preview .= '<div class="preview_text" style="color:' . us_get_color( $values['color_content_text'] ) . ';">';
	$preview .= 'Lorem ipsum dolor sit amet, <span style="color:' . us_get_color( $values['color_content_link'] ) . ';">consectetur</span> adipiscing elit. Maecenas arcu lectus, sollicitudin dictum dapibus sit amet.';
	$preview .= '</div>';
	// Primary
	$preview .= '<div class="preview_primary" style="background:' . us_get_color( $values['color_content_primary'], /* Gradient */ TRUE ) . ';"></div>';
	// Secondary
	$preview .= '<div class="preview_secondary" style="background:' . us_get_color( $values['color_content_secondary'], /* Gradient */ TRUE ) . ';"></div>';
	$preview .= '</div>';
	// Footer
	$preview .= '<div class="preview_footer" style="background:' . us_get_color( $values['color_footer_bg'], /* Gradient */ TRUE ) . ';"></div>';
	$preview .= '</div>';

	return $preview;
}

/**
 * Color picker with palette
 */
if ( ! function_exists( 'usof_color_picker' ) ) {
	add_action( 'admin_footer', 'usof_color_picker' );
	add_action( 'usb_admin_footer_scripts', 'usof_color_picker', 101 );
	function usof_color_picker() {

		$palette = defined( 'US_THEMENAME' ) ? get_option( 'usof_color_palette_' . US_THEMENAME ) : array();
		if ( ! is_array( $palette ) ) {
			$palette = array();
		}

		$output = '<div class="usof-colpick type_solid usof-colpick-template">';

		// Palette colors
		$output .= '<div class="usof-colpick-palette">';

		// Fill empty palette with predefined colors
		if ( empty( $palette ) ) {
			global $usof_options;

			$predefined_colors = array(
				$usof_options['color_content_primary'],
				$usof_options['color_content_secondary'],
				$usof_options['color_content_heading'],
				$usof_options['color_content_text'],
				$usof_options['color_content_faded'],
				$usof_options['color_content_border'],
				$usof_options['color_content_bg_alt'],
				$usof_options['color_content_bg'],
			);
			if ( defined( 'US_THEMENAME' ) ) {
				update_option( 'usof_color_palette_' . US_THEMENAME, $predefined_colors );
			}
		}

		foreach ( $palette as $color ) {
			$output .= '<div class="usof-colpick-palette-value">';
			$output .= '<span style="background:' . $color . '" title="' . esc_attr( $color ) . '"></span>';
			$output .= '<div class="usof-colpick-palette-delete" title="' . us_translate( 'Delete' ) . '"></div>';
			$output .= '</div>';
		}
		$output .= '<div class="usof-colpick-palette-add" title="' . __( 'Add the current color to the palette', 'us' ) . '"></div>';
		$output .= '</div>';

		// Radio buttons: Solid / Gradient
		$output .= '<div class="usof-radio">';
		$output .= '<label>';
		$output .= '<input name="usof-colpick-type" type="radio" value="solid">';
		$output .= '<span class="usof-radio-value">' . _x( 'Solid', 'color type', 'us' ) . '</span>';
		$output .= '</label>';
		$output .= '<label>';
		$output .= '<input name="usof-colpick-type" type="radio" value="gradient">';
		$output .= '<span class="usof-radio-value">' . _x( 'Gradient', 'color type', 'us' ) . '</span>';
		$output .= '</label>';
		$output .= '</div>';

		// Angle slider
		$output .= '<div class="usof-colpick-angle">';
		$output .= __( 'Angle', 'us' );
		$output .= '<div class="usof-colpick-angle-selector"></div>';
		$output .= '</div>';

		// First color picker
		$output .= '<div class="usof-colpick-wrap first">';
		$output .= '<div class="usof-colpick-color"><div class="usof-colpick-color-selector"></div></div>';
		$output .= '<div class="usof-colpick-hue"><div class="usof-colpick-hue-selector"></div></div>';
		$output .= '<div class="usof-colpick-alpha"><div class="usof-colpick-alpha-selector"></div></div>';
		$output .= '</div>';

		// Second color picker
		$output .= '<div class="usof-colpick-wrap second">';
		$output .= '<div class="usof-colpick-color"><div class="usof-colpick-color-selector"></div></div>';
		$output .= '<div class="usof-colpick-hue"><div class="usof-colpick-hue-selector"></div></div>';
		$output .= '<div class="usof-colpick-alpha"><div class="usof-colpick-alpha-selector"></div></div>';
		$output .= '</div>';

		$output .= '</div>';

		echo $output;
	}
}

if ( ! function_exists( 'usof_get_dynamic_colors' ) ) {
	/**
	 * Get a list of dynamic colors for $usof.field['color']
	 *
	 * @return array
	 */
	function usof_get_dynamic_colors() {
		$result = array();
		$group_name = NULL;

		foreach ( us_config( 'theme-options.colors.fields', array() ) as $field_name => $field ) {
			// Group Search
			if (
				isset( $field['type'] )
				AND $field['type'] === 'heading'
				AND ! empty( $field['title'] )
			) {
				$group_name = $field['title'];
			}

			// Skip all types except color
			if ( isset( $field['type'] ) AND $field['type'] !== 'color' ) {
				continue;
			}

			// Remove "color" prefix for better UI
			if ( strpos( $field_name, 'color' ) === 0 ) {
				$field_name = substr( $field_name, strlen( 'color' ) );
			}

			// Color option
			$item = array(
				'name' => $field_name,
				'title' => us_arr_path( $field, 'text', '' ),
				'value' => us_get_color( $field_name, /* Gradient */ TRUE, /* CSS var */ FALSE ),
			);

			if ( ! is_null( $group_name ) ) {
				$result[ $group_name ][] = $item;
			} else {
				$result[] = $item;
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'usof_extract_tinymce_options' ) ) {
	/**
	 * Extracting mceInit settings for editor by ID
	 * Note: The current method is called for the editor field in the context of the header.
	 *
	 * @param string $id The editor ID
	 * @param array $set The settings
	 * @return string $mceInit
	 */
	function usof_extract_tinymce_options( $id, $set ) {
		if ( ! is_array( $set ) ) {
			$set = array();
		}
		$mceInit = array();
		/**
		 * Filter function to extract data
		 *
		 * @param array $mceInit The mce init settings
		 * @param string $editor_id The editor ID
		 * @return array
		 */
		$func_tiny_mce_before_init = function ( $_mceInit, $editor_id ) use( $id, &$mceInit ) {
			if ( $id === $editor_id ) {
				$mceInit = (array) $_mceInit;
			}
			return $mceInit;
		};

		// Add a filter to extract `$mceInit`
		add_filter( 'tiny_mce_before_init', $func_tiny_mce_before_init, 1, 2 );

		// Init of editor settings to form all options
		if ( ! class_exists( '_WP_Editors', false ) ) {
			require ABSPATH . WPINC . '/class-wp-editor.php';
		}
		$set = \_WP_Editors::parse_settings( $id, $set );
		\_WP_Editors::editor_settings( $id, $set );

		// Remove the filter after extracting the `$mceInit`
		remove_filter( 'tiny_mce_before_init', $func_tiny_mce_before_init );

		// Parsing received options
		$options = '';
		foreach ( $mceInit as $key => $value ) {
			if ( is_bool( $value ) ) {
				$val = $value ? 'true' : 'false';
				$options .= $key . ':' . $val . ',';
				continue;
			} elseif (
				! empty( $value )
				&& is_string( $value )
				&& (
					( '{' === $value[0] && '}' === $value[ strlen( $value ) - 1 ] )
					|| ( '[' === $value[0] && ']' === $value[ strlen( $value ) - 1 ] )
					|| preg_match( '/^\(?function ?\(/', $value )
				)
			) {

				$options .= $key . ':' . $value . ',';
				continue;
			}
			$options .= $key . ':"' . $value . '",';
		}

		return '{' . trim( $options, ' ,' ) . '}';
	}
}
