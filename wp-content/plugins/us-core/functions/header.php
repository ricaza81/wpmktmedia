<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'us_get_header_option' ) ) {
	/**
	 * Get header option for the specified state
	 *
	 * @param string $name Option name
	 * @param string $state Header state: default|laptops|tablets|mobiles
	 * @param string $default
	 *
	 * @return string
	 */
	function us_get_header_option( $name, $state = 'default', $default = NULL ) {
		global $us_header_settings;
		us_load_header_settings_once();

		// These options are available in Default state only
		$shared_options = array(
			'top_fullwidth',
			'top_bg_color',
			'top_text_color',
			'top_text_hover_color',
			'top_transparent_bg_color',
			'top_transparent_text_color',
			'top_transparent_text_hover_color',
			'middle_fullwidth',
			'middle_bg_color',
			'middle_text_color',
			'middle_text_hover_color',
			'middle_transparent_bg_color',
			'middle_transparent_text_color',
			'middle_transparent_text_hover_color',
			'bottom_fullwidth',
			'bottom_bg_color',
			'bottom_text_color',
			'bottom_text_hover_color',
			'bottom_transparent_bg_color',
			'bottom_transparent_text_color',
			'bottom_transparent_text_hover_color',
		);

		if (
			$state != 'default'
			AND ( ! isset( $us_header_settings[ $state ]['options'][ $name ] )
			OR in_array( $name, $shared_options ) )
		) {
			$state = 'default';
		}

		if ( ! empty( $us_header_settings[ $state ]['options'][ $name ] ) ) {
			return $us_header_settings[ $state ]['options'][ $name ];
		}

		/*
		 * Default settings from the config
		 * @var array
		 */
		static $default_header_settings = array();
		if ( is_null( $default ) AND empty( $default_header_settings ) ) {
			foreach ( us_config( 'header-settings.options', array() ) as $group ) {
				if ( ! is_array( $group ) ) {
					continue;
				}
				foreach ( $group as $param_name => $options ) {
					if ( us_arr_path( $options, 'type' ) == 'color' AND ! empty( $options['std'] ) ) {
						$default_header_settings[ $param_name ] = $options['std'];
					}
				}
			}
		}

		if ( is_null( $default ) AND ! empty( $default_header_settings[ $name ] ) ) {
			return $default_header_settings[ $name ];
		}

		return $default;
	}
}


/**
 * Get header layout for the specified state
 *
 * @param $state
 *
 * @return array
 */
function us_get_header_layout( $state = 'default' ) {
	global $us_header_settings;
	us_load_header_settings_once();
	$layout = array(
		'top_left' => array(),
		'top_center' => array(),
		'top_right' => array(),
		'middle_left' => array(),
		'middle_center' => array(),
		'middle_right' => array(),
		'bottom_left' => array(),
		'bottom_center' => array(),
		'bottom_right' => array(),
		'hidden' => array(),
	);
	if ( $state != 'default' AND isset( $us_header_settings['default']['layout'] ) AND is_array( $us_header_settings['default']['layout'] ) ) {
		$layout = array_merge( $layout, $us_header_settings['default']['layout'] );
	}
	if ( isset( $us_header_settings[ $state ]['layout'] ) AND is_array( $us_header_settings[ $state ]['layout'] ) ) {
		$layout = array_merge( $layout, $us_header_settings[ $state ]['layout'] );
	}

	return $layout;
}

/**
 * Load the current header settings for all possible responsive states
 */
function us_load_header_settings_once() {
	global $us_header_settings;

	// Note: At this point, a check for the presence of options is necessary because
	// the current function can be called from the helpers.php file that is connected
	// before `after_setup_theme`
	if ( ! empty( $us_header_settings['default']['options'] ) ) {
		return;
	}
	// Basic structure
	$us_header_settings = array_fill_keys(
		us_get_responsive_states( /* only keys */TRUE ),
		array( 'options' => array(), 'layout' => array() )
	);
	// Add space for data
	$us_header_settings['data'] = array();
	$us_header_settings = apply_filters( 'us_load_header_settings', $us_header_settings );
}

/**
 * Recursively output elements of a certain state / place
 *
 * @param array $settings Current layout
 * @param string $state Current state
 * @param string $place Outputted place
 * @param string $context 'header' / 'grid'
 * @param string $grid_object_type 'post' / 'term'
 */
function us_output_builder_elms( &$settings, $state, $place, $context = 'header', $grid_object_type = 'post' ) {

	$layout = &$settings[ $state ]['layout'];
	$data = &$settings['data'];
	if ( ! isset( $layout[ $place ] ) OR ! is_array( $layout[ $place ] ) ) {
		return;
	}

	// Set 3 states for header and 1 for other contexts, like Grid Layouts
	$_states = ( $context === 'header' )
		? (array) us_get_responsive_states( /* Only keys */TRUE )
		: array( 'default' );

	$visible_elms = array();
	foreach ( $_states as $_state ) {
		$visible_elms[ $_state ] = us_get_builder_shown_elements_list( us_arr_path( $settings, $_state . '.layout', array() ) );
	}

	foreach ( $layout[ $place ] as $elm ) {
		$classes = '';
		if ( $context === 'header' ) {
			if ( isset( $data[ $elm ] ) ) {
				if ( us_arr_path( $data[ $elm ], 'hide_for_sticky', FALSE ) ) {
					$classes .= ' hide-for-sticky';
				}
				if ( us_arr_path( $data[ $elm ], 'hide_for_not_sticky', FALSE ) ) {
					$classes .= ' hide-for-not-sticky';
				}
			}
		}
		foreach ( $_states as $_state ) {
			if ( ! in_array( $elm, $visible_elms[ $_state ] ) ) {
				$classes .= ' hidden_for_' . $_state;
			}
		}
		if ( $context === 'header' ) {
			$classes .= ' ush_' . str_replace( ':', '_', $elm );
		} elseif ( $context === 'grid' ) {
			$classes .= ' usg_' . str_replace( ':', '_', $elm );
		}

		// Add custom class name, if set
		if ( ! empty( $data[ $elm ]['el_class'] ) ) {
			$classes .= ' ' . $data[ $elm ]['el_class'];
		}

		// Add animation class if set in Design options
		if (
			! us_amp()
			AND ! empty( $data[ $elm ][ 'css' ] )
			AND us_design_options_has_property( $data[ $elm ][ 'css' ], 'animation-name' )
		) {
			if ( ! isset( $data[ $elm ]['el_class'] ) ) {
				$classes = ' us_animate_this';
			} else {
				$classes .= ' us_animate_this';
			}
		}

		// Wrapper
		if ( substr( $elm, 1, 7 ) == 'wrapper' ) {
			$wrapper_atts = array(
				'class' => 'w-' . strtok( $elm, ':' ) . $classes,
			);
			if ( isset( $data[ $elm ] ) ) {
				if ( isset( $data[ $elm ]['alignment'] ) ) {
					$wrapper_atts['class'] .= ' align_' . $data[ $elm ]['alignment'];
				}
				if ( isset( $data[ $elm ]['valign'] ) ) {
					$wrapper_atts['class'] .= ' valign_' . $data[ $elm ]['valign'];
				}
				if ( ! empty( $data[ $elm ]['wrap'] ) ) {
					$wrapper_atts['class'] .= ' wrap';
				}
				if ( isset( $data[ $elm ]['inner_items_gap'] ) ) {
					$inner_items_gap = trim( $data[ $elm ]['inner_items_gap'] );
					if ( strpos( $elm, 'hwrapper' ) !== FALSE ) {

						// Set CSS var for Horizontal wrapper, if the value is not default
						if ( $inner_items_gap != '1.2rem' ) {
							$wrapper_atts['style'] = '--hwrapper-gap:' . $inner_items_gap;
						}

						// Set CSS var for Vertical wrapper, if the value is not default
					} elseif ( $inner_items_gap != '0.7rem' ) {
						$wrapper_atts['style'] = '--vwrapper-gap:' . $inner_items_gap;
					}
				}
			}
			echo '<div' . us_implode_atts( $wrapper_atts ) . '>';
			us_output_builder_elms( $settings, $state, $elm, $context );
			echo '</div>';

			// Element
		} else {
			$type = strtok( $elm, ':' );
			$defaults = us_get_elm_defaults( $type, $context );
			if ( ! isset( $data[ $elm ] ) ) {
				$data[ $elm ] = us_get_elm_defaults( $type, $context );
			}
			$values = array_merge( $defaults, array_intersect_key( $data[ $elm ], $defaults ) );
			$values['id'] = $elm;
			$values['classes'] = ( isset( $values['classes'] ) ? $values['classes'] : '' ) . $classes;
			$values['us_elm_context'] = $context;
			$values['us_grid_object_type'] = $grid_object_type;

			// Adding special classes
			us_load_template( 'templates/elements/' . $type, $values );
		}
	}
}

/**
 * Get default value for an element
 *
 * @param string $type
 * @param string $context 'header' or 'grid'
 *
 * @return mixed
 */
function us_get_elm_defaults( $type, $context = 'header' ) {
	global $us_elm_defaults, $usof_options;
	if ( ! isset( $us_elm_defaults ) ) {
		$us_elm_defaults = array();
	}
	if ( ! isset( $us_elm_defaults[ $context ] ) ) {
		$us_elm_defaults[ $context ] = array();
	}
	if ( ! isset( $us_elm_defaults[ $context ][ $type ] ) ) {
		$us_elm_defaults[ $context ][ $type ] = array();
		$elm_config = us_config( 'elements/' . $type, array() );
		foreach ( us_arr_path( $elm_config, 'params', array() ) as $field_name => $field ) {
			$value = isset( $field['std'] ) ? $field['std'] : '';
			// Check if context specific standard value is set
			$value = isset( $field[ $context . '_std' ] ) ? $field[ $context . '_std' ] : $value;
			if ( $context === 'header' ) {
				// Some default header values may be based on main theme options' values
				if ( function_exists( 'usof_load_options_once' ) ) {
					usof_load_options_once();
				}
				if ( is_string( $value ) AND substr( $value, 0, 1 ) == '=' AND isset( $usof_options[ substr( $value, 1 ) ] ) ) {
					$value = $usof_options[ substr( $value, 1 ) ];
				}
			}
			$us_elm_defaults[ $context ][ $type ][ $field_name ] = $value;
		}
		if ( ! empty( $elm_config['fallback_params'] ) ) {
			foreach ( $elm_config['fallback_params'] as $field_name ) {
				$us_elm_defaults[ $context ][ $type ][ $field_name ] = '';
			}
		}
	}

	return us_arr_path( $us_elm_defaults, array( $context, $type ), array() );
}

// Backward compability with older HB versions
if ( ! function_exists( 'us_get_header_elm_defaults' ) ) {
	function us_get_header_elm_defaults( $type ) {
		return us_get_elm_defaults( $type, 'header' );
	}
}

/**
 * Get elements
 *
 * @param string $type
 * @param bool $key_as_class Should the keys of the resulting array be css classes instead of elms ids?
 *
 * @return array
 */
function us_get_header_elms_of_a_type( $type, $key_as_class = TRUE ) {
	global $us_header_settings;
	us_load_header_settings_once();
	$defaults = us_get_elm_defaults( $type, 'header' );
	$result = array();
	if ( ! is_array( $us_header_settings['data'] ) ) {
		return $result;
	}
	foreach ( $us_header_settings['data'] as $elm_id => $elm ) {
		if ( strtok( $elm_id, ':' ) != $type ) {
			continue;
		}
		$key = $key_as_class ? ( 'ush_' . str_replace( ':', '_', $elm_id ) ) : $elm_id;
		$result[ $key ] = array_merge( $defaults, array_intersect_key( $elm, $defaults ) );
	}

	return $result;
}

/**
 * Make the provided header settings value consistent and proper
 *
 * @param $value array
 *
 * @return array
 */
function us_fix_header_settings( $value ) {
	if ( empty( $value ) OR ! is_array( $value ) ) {
		$value = array();
	}
	if ( ! isset( $value['data'] ) OR ! is_array( $value['data'] ) ) {
		$value['data'] = array();
	}
	$options_defaults = array();
	foreach ( us_config( 'header-settings.options', array() ) as $group => $opts ) {
		foreach ( $opts as $opt_name => $opt ) {
			$options_defaults[ $opt_name ] = isset( $opt['std'] ) ? $opt['std'] : '';
		}
	}
	foreach ( (array) us_get_responsive_states( /* Only keys */TRUE ) as $state ) {
		if ( ! isset( $value[ $state ] ) OR ! is_array( $value[ $state ] ) ) {
			$value[ $state ] = array();
		}
		if ( ! isset( $value[ $state ]['layout'] ) OR ! is_array( $value[ $state ]['layout'] ) ) {
			if ( $state != 'default' AND isset( $value['default']['layout'] ) ) {
				$value[ $state ]['layout'] = $value['default']['layout'];
			} else {
				$value[ $state ]['layout'] = array();
			}
		}
		$state_elms = array();
		foreach ( $value[ $state ]['layout'] as $place => $elms ) {
			if ( ! is_array( $elms ) ) {
				$elms = array();
			}
			foreach ( $elms as $index => $elm_id ) {
				if ( ! is_string( $elm_id ) OR strpos( $elm_id, ':' ) == - 1 ) {
					unset( $elms[ $index ] );
				} else {
					$state_elms[] = $elm_id;
					if ( ! isset( $value['data'][ $elm_id ] ) ) {
						$value['data'][ $elm_id ] = array();
					}
				}
			}
			$value[ $state ]['layout'][ $place ] = array_values( $elms );
		}
		if ( ! isset( $value[ $state ]['layout']['hidden'] ) OR ! is_array( $value[ $state ]['layout']['hidden'] ) ) {
			$value[ $state ]['layout']['hidden'] = array();
		}
		$value[ $state ]['layout']['hidden'] = array_merge( $value[ $state ]['layout']['hidden'], array_diff( array_keys( $value['data'] ), $state_elms ) );
		// Fixing options
		if ( ! isset( $value[ $state ]['options'] ) OR ! is_array( $value[ $state ]['options'] ) ) {
			$value[ $state ]['options'] = array();
		}
		$value[ $state ]['options'] = array_merge( $options_defaults, ( $state != 'default' ) ? $value['default']['options'] : array(), $value[ $state ]['options'] );
	}

	foreach ( $value['data'] as $elm_id => $values ) {
		$type = strtok( $elm_id, ':' );
		$defaults = us_get_elm_defaults( $type, 'header' );
		$value['data'][ $elm_id ] = array_merge( $defaults, array_intersect_key( $value['data'][ $elm_id ], $defaults ) );
	}

	return $value;
}

function us_fix_header_template_settings( $value ) {

	if ( isset( $value['title'] ) ) {
		// Don't need this in data processing
		unset( $value['title'] );
	}

	// Basic structure
	$template_structure = array_fill_keys(
		us_get_responsive_states( /* only keys */TRUE ),
		array( 'options' => array(), 'layout' => array() )
	);
	// Add space for data
	$template_structure['data'] = array();

	$value = us_array_merge( $template_structure, $value );
	$layout_structure = array(
		'top_left' => array(),
		'top_center' => array(),
		'top_right' => array(),
		'middle_left' => array(),
		'middle_center' => array(),
		'middle_right' => array(),
		'bottom_left' => array(),
		'bottom_center' => array(),
		'bottom_right' => array(),
		'hidden' => array(),
	);
	foreach ( (array) us_get_responsive_states( /* Only keys */TRUE ) as $state ) {

		// Options
		$value[ $state ]['options'] = array_merge( ( $state == 'default' ) ? array() : $value['default']['options'], $value[ $state ]['options'] );

		// Layout
		$value[ $state ]['layout'] = array_merge( $layout_structure, ( $state == 'default' ) ? array() : $value['default']['layout'], $value[ $state ]['layout'] );
	}
	$value = us_fix_header_settings( $value );

	return $value;
}

/**
 * Get list of user registered nav menus with theirs proper names, in a format sutable for usof select field
 *
 * @return array
 */
function us_get_nav_menus() {
	$menus = array();
	foreach ( get_terms( 'nav_menu', array( 'hide_empty' => TRUE ) ) as $menu ) {
		$menus[ $menu->slug ] = $menu->name;
	}

	// Adding us_main_menu location if it is filled with mene
	$theme_locations = get_nav_menu_locations();
	if ( isset( $theme_locations['us_main_menu'] ) ) {
		$menu_obj = get_term( $theme_locations['us_main_menu'], 'nav_menu' );
		if ( $menu_obj AND is_object( $menu_obj ) AND isset ( $menu_obj->name ) ) {
			$menus['location:us_main_menu'] = $menu_obj->name . ' (' . __( 'Custom Menu', 'us' ) . ')';
		}
	}

	return $menus;
}

/**
 * Get the list of header elements that are shown in the certain layout listing
 *
 * @param array $list Euther layout or separate list
 *
 * @return array
 */
function us_get_builder_shown_elements_list( $list ) {
	$shown = array();
	foreach ( $list as $key => $sublist ) {
		if ( $key != 'hidden' ) {
			$shown = array_merge( $shown, $sublist );
		}
	}

	return $shown;
}

/**
 * Adding header settings JSON value to footer
 */
if ( ! function_exists( 'us_pass_header_settings_to_js' ) ) {
	// Changing ordering to avoid JavaScript errors with NextGEN Gallery plugin
	add_action( 'wp_footer', 'us_pass_header_settings_to_js', -2 );
	function us_pass_header_settings_to_js() {
		// Do not proceed with this function if the current page is an AMP page
		if ( us_amp() ) {
			return false;
		}
		global $us_header_settings;
		us_load_header_settings_once();
		$header_settings = $us_header_settings;
		if ( isset( $header_settings['data'] ) ) {
			unset( $header_settings['data'] );
		}

		$output =  '<script>';
		$output .= 'if ( window.$us === undefined ) window.$us = {};';
		$output .= '$us.headerSettings = ' . json_encode( $header_settings ) . ';';
		$output .= '</script>';

		/**
		 * Header Settings output filter
		 */
		echo apply_filters( 'us_pass_header_settings_to_js', $output );
	}
}

/**
 * Get the header design options css for all the fields
 *
 * @return string
 */
function us_get_header_design_options_css() {
	global $us_header_settings;
	us_load_header_settings_once();

	// Get header states
	$header_states = array();
	foreach ( (array) us_get_responsive_states( /* Only keys */TRUE ) as $state ) {
		if ( ! empty( $us_header_settings[ $state ]['options']['custom_breakpoint'] ) ) {
			$header_states[ $state ]['breakpoint'] = (int) $us_header_settings[ $state ]['options']['breakpoint'];
		} else {
			$header_states[ $state ]['breakpoint'] = (int) us_get_option( $state . '_breakpoint' );
		}
	}

	// Get breakpoints from jsoncss parameters
	$breakpoints = (array) us_get_jsoncss_options( /* breakpoints */array(), $header_states )['breakpoints'];

	$jsoncss_collection = array();
	foreach ( $us_header_settings['data'] as $elm_id => $elm ) {
		if ( ! isset( $elm['css'] ) OR empty( $elm['css'] ) OR ! is_array( $elm['css'] ) ) {
			continue;
		}
		foreach ( array_keys( $breakpoints ) as $state ) {
			if ( $css_options = us_arr_path( $elm, 'css.' . $state, FALSE ) ) {
				$class_name = 'ush_' . str_replace( ':', '_', $elm_id );
				$css_options = apply_filters( 'us_output_design_css_options', $css_options, $state );
				$jsoncss_collection[ $state ][ $class_name ] = $css_options;
			}
		}
	}

	return us_jsoncss_compile( $jsoncss_collection, $breakpoints );
}

/**
 * Add link to Theme Options to Admin Bar
 *
 * @param $wp_admin_bar
 */
function us_admin_bar_theme_options_link( $wp_admin_bar ) {
	$wp_admin_bar->add_node(
		array(
			'id' => 'us_theme_otions',
			'title' => __( 'Theme Options', 'us' ),
			'href' => admin_url( 'admin.php?page=us-theme-options' ),
			'parent' => 'site-name',
		)
	);
}

add_action( 'init', 'us_admin_bar_theme_options_link_init' );
function us_admin_bar_theme_options_link_init() {
	if ( ! is_admin() AND function_exists( 'current_user_can' ) AND function_exists( 'wp_get_current_user' ) AND current_user_can( 'administrator' ) ) {
		add_action( 'admin_bar_menu', 'us_admin_bar_theme_options_link', 99 );
	}
}

/**
 * Preload Uploaded Fonts, priority should be higher the main CSS file
 */
if ( ! function_exists( 'us_preload_uploaded_fonts' ) ) {
	add_action( 'wp_head', 'us_preload_uploaded_fonts', 6 );
	function us_preload_uploaded_fonts() {
		if ( $uploaded_fonts = us_get_option( 'uploaded_fonts', array() ) ) {
			foreach ( $uploaded_fonts as $uploaded_font ) {
				$files = explode( ',', $uploaded_font['files'] );
				foreach ( $files as $file ) {
					if ( $url = wp_get_attachment_url( $file ) ) {
						echo '<link rel="preload" href="' . esc_url( $url ) . '" as="font" type="font/' . pathinfo( $url, PATHINFO_EXTENSION ) . '" crossorigin>';
					}
				}
			}
		}
	}
}

/**
 * Add "no-touch" class to <html> on desktops
 */
if ( ! function_exists( 'us_output_no_touch_script' ) ) {
	add_action( 'wp_head', 'us_output_no_touch_script' );
	function us_output_no_touch_script() {
		// Do not proceed with this function if the current page is an AMP page
		if ( us_amp() ) {
			return false;
		}
		?>
		<script>
			if ( ! /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) ) {
				var root = document.getElementsByTagName( 'html' )[ 0 ]
				root.className += " no-touch";
			}
		</script>
		<?php
	}
}

if ( ! function_exists( 'us_admin_bar_menu' ) ) {
	add_action( 'admin_bar_menu', 'us_admin_bar_menu', 500 );
	/**
	 * Add link to Admin bar to edit the current header, content template and page blocks
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The admin bar
	 */
	function us_admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
		global $pagenow;
		if ( current_user_can( 'administrator' ) AND $pagenow === 'index.php' ) {
			$area_ids = array();
			$received_posts = array();
			$edit_menu = array();

			$post_args = array(
				'post_type' => array( 'us_header', 'us_content_template', 'us_page_block' ),
				'post__in' => array(),
			);
			$area_names = array(
				'header' => _x( 'Header', 'site top area', 'us' ),
				'content' => __( 'Content template', 'us' ),
				'page_blocks' => __( 'Page Blocks', 'us' ),
				'footer' => __( 'Footer', 'us' ),
			);

			/**
			 * Add the page blocks in a collection $area_ids and $received_posts
			 * @param WP_Post $post
			 */
			$func_acc_page_blocks = function ( $post ) use ( &$area_ids, &$received_posts ) {
				if ( $post instanceof WP_Post AND ! empty( $post->post_content ) ) {
					$received_posts[ $post->ID ] = $post;
					$area_ids['page_blocks'][] = $post->ID;
				}
			};

			// Search for page block in the current page object
			$current_page = get_queried_object();
			if (
				$current_page instanceof WP_Post AND strpos( $current_page->post_content, '[us_page_block' ) !== FALSE ) {
				// Recursive get of all page blocks
				us_get_recursive_parse_page_block( $current_page, $func_acc_page_blocks );
			}
			unset( $current_page );

			// Get all ids
			foreach ( array_keys( $area_names ) as $area ) {
				if ( $area == 'page_blocks' AND $page_block_ids = (array) us_get_current_page_block_ids() ) {
					$query_args = array(
						'nopaging' => TRUE,
						'post__in' => $page_block_ids,
						'post_type' => array( 'us_page_block', 'us_content_template' ),
						'suppress_filters' => TRUE,
					);
					foreach ( get_posts( $query_args ) as $post ) {
						// Recursive get of all page blocks
						us_get_recursive_parse_page_block( $post, $func_acc_page_blocks );
					}
					if ( ! empty( $area_ids[ $area ] ) AND is_array( $post_args['post__in'] ) ) {
						$post_args['post__in'] = array_merge( $post_args['post__in'], $area_ids[ $area ] );
					}
				} elseif ( $area_id = us_get_page_area_id( $area ) ) {
					$post_args['post__in'][] = $area_ids[ $area ] = $area_id;

					if (
						has_filter( 'us_tr_object_id' )
						AND $translated_id = apply_filters( 'us_tr_object_id', $area_id, 'post', TRUE )
					) {
						$post_args['post__in'][] = $area_ids[ $area ] = $translated_id;
					}
				}
				$edit_menu[ $area ] = array(
					'id' => sprintf( 'us-%s', $area ),
					'parent' => 'edit',
					'title' => us_arr_path( $area_names, $area ),
					'meta' => array(
						'class' => 'us-admin-bar',
						'onclick' => 'return false',
						'html' => '',
					),
				);
			}
			// Delete already received posts from the request
			if ( ! empty( $area_ids['page_blocks'] ) ) {
				foreach ( $area_ids['page_blocks'] as $id ) {
					if ( isset( $received_posts[ $id ] ) ) {
						$key = array_search( $id, $post_args['post__in'], TRUE );
						if ( $key !== FALSE ) {
							unset( $post_args['post__in'][ $key ] );
						}
					}
				}
			}
			// Get all posts
			if ( ! empty( $post_args['post__in'] ) AND $posts = get_posts( $post_args ) ) {
				if ( ! empty( $received_posts ) ) {
					$posts = array_merge( $posts, $received_posts );
				}
				foreach ( $posts as $post ) {
					if (
						$post->post_type === 'us_page_block'
						AND ! empty( $area_ids['page_blocks'] )
						AND in_array( $post->ID, $area_ids['page_blocks'] )
					) {
						$key = 'page_blocks';
					} else {
						$keys = $area_ids;
						unset( $keys['page_blocks'] );
						$keys = array_flip( $keys );
						if ( ! empty( $keys[ $post->ID ] ) ) {
							$key = $keys[ $post->ID ];
						}

						unset( $keys );
					}
					if ( isset( $key ) AND isset( $edit_menu[ $key ]['meta']['html'] ) ) {
						$edit_menu[ $key ]['meta']['html'] .= sprintf(
							'<a href="%s">%s</a>',
							admin_url( 'post.php?post=' . $post->ID . '&action=edit' ),
							strip_tags( $post->post_title )
						);
					}
				}
			}

			if ( ! empty( $edit_menu ) ) {

				// US Admin bar styles
				$style = us_minify_css(
					'
					.us-admin-bar {
						margin-bottom: 6px !important;
						white-space: nowrap;
						max-width: 300px;
						overflow: hidden;
					}
					.us-admin-bar > .ab-item {
						font-weight: 600 !important;
						color: #fff !important;
					}
					.us-admin-bar > * {
						line-height: 24px !important;
						height: 24px !important;
					}
				'
				);
				echo '<style id="us-admin-bar-style">' . $style . '</style>';

				// Moved the edit link to the drop-down list, this is only necessary for the mobile devices
				if ( wp_is_mobile() AND $admin_bar_nodes = $wp_admin_bar->get_nodes() AND ! empty( $admin_bar_nodes['edit'] ) ) {
					$edit_node = (array) $admin_bar_nodes['edit'];
					$edit_node = array_merge( $edit_node, array(
						'id' => 'mobile_edit',
						'parent' => 'edit',
					) );
					$wp_admin_bar->add_menu( $edit_node );
					unset( $admin_bar_nodes );

					// US Admin bar javascript
					echo '<script id="us-admin-bar-javascript">
						;(function( $ ){
							$( "#wp-admin-bar-edit > a:first").on( "click", function( e ) {
								e.preventDefault();
								e.stopPropagation();
							} );
						})( jQuery );
					</script>';
				}

				foreach ( $edit_menu as $area => $values ) {
					if ( ! empty( $values['meta']['html'] ) ) {
						$wp_admin_bar->add_menu( $values );
					}
				}
			}
			unset( $area_ids, $area_names, $post_args, $received_posts, $edit_menu );
		}
	}
}

if ( ! function_exists( 'us_hb_settings_fallback' ) ) {
	/**
	 * Apply fallback changes for old header settings on update
	 *
	 * @param $header_settings
	 * @return array
	 */
	function us_hb_settings_fallback( $header_settings ) {
		global $usof_options;

		// Check if the settings are empty and abort following execution in this case
		if ( ! is_array( $header_settings ) OR empty( $header_settings['default'] ) ) {
			return $header_settings;
		}

		// Populate Laptops settings with values from the Default.
		// Also set a custom breakpoint equals the Tablets breakpoint, needed to avoid fallback in metabox (Sticky and Transparent settings)
		if ( ! isset( $header_settings['laptops'] ) ) {
			$header_settings['laptops'] = $header_settings['default'];
			$header_settings['laptops']['options']['custom_breakpoint'] = 1;
			$header_settings['laptops']['options']['breakpoint'] = $header_settings['tablets']['options']['breakpoint'];
		}

		// Fallback for colors
		if ( ! isset( $header_settings['default']['options']['top_transparent_text_hover_color'] ) ) {
			$header_settings['default']['options']['top_transparent_text_hover_color'] =
				isset( $usof_options['color_header_bottom_text_hover'] ) ? '_header_transparent_text_hover' : '_header_top_transparent_text_hover';
		}
		if ( ! isset( $header_settings['default']['options']['bottom_bg_color'] ) ) {
			$header_settings['default']['options']['bottom_bg_color'] =
				us_arr_path( $usof_options, 'color_header_bottom_bg', '_header_middle_bg' );
		}
		if ( ! isset( $header_settings['default']['options']['bottom_text_hover_color'] ) ) {
			$header_settings['default']['options']['bottom_text_hover_color'] =
				us_arr_path( $usof_options, 'color_header_bottom_text_hover', '_header_middle_text_hover' );
		}
		if ( ! isset( $header_settings['default']['options']['bottom_text_color'] ) ) {
			$header_settings['default']['options']['bottom_text_color'] =
				us_arr_path( $usof_options, 'color_header_bottom_text', '_header_middle_text' );
		}

		// Turn on the custom breakpoint option for Tablets and Mobiles, if it wasn't exist
		if ( ! isset( $header_settings['tablets']['options']['custom_breakpoint'] ) ) {
			$header_settings['tablets']['options']['custom_breakpoint'] = 1;
		}
		if ( ! isset( $header_settings['mobiles']['options']['custom_breakpoint'] ) ) {
			$header_settings['mobiles']['options']['custom_breakpoint'] = 1;
		}

		// Fallback for elements
		foreach ( $header_settings['data'] as $elm_id => $elm_data ) {

			// Menu
			if ( strpos( $elm_id, 'menu' ) === 0 ) {
				if ( ! isset( $elm_data['color_active_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_active_bg'] =
						us_arr_path( $usof_options, 'color_menu_active_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_active_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_active_text'] =
						us_arr_path( $usof_options, 'color_menu_active_text', '_header_middle_text_hover' );
				}
				if ( ! isset( $elm_data['color_transparent_active_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_transparent_active_bg'] =
						us_arr_path( $usof_options, 'color_menu_transparent_active_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_transparent_active_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_transparent_active_text'] =
						us_arr_path( $usof_options, 'color_menu_transparent_active_text', '_header_transparent_text_hover' );
				}
				if ( ! isset( $elm_data['color_hover_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_hover_bg'] =
						us_arr_path( $usof_options, 'color_menu_hover_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_hover_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_hover_text'] =
						us_arr_path( $usof_options, 'color_menu_hover_text', '_header_middle_text_hover' );
				}
				if ( ! isset( $elm_data['color_drop_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_bg'] =
						us_arr_path( $usof_options, 'color_drop_bg', '_header_middle_bg' );
				}
				if ( ! isset( $elm_data['color_drop_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_text'] =
						us_arr_path( $usof_options, 'color_drop_text', '_header_middle_text' );
				}
				if ( ! isset( $elm_data['color_drop_hover_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_hover_bg'] =
						us_arr_path( $usof_options, 'color_drop_hover_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_drop_hover_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_hover_text'] =
						us_arr_path( $usof_options, 'color_drop_hover_text', '_header_middle_text_hover' );
				}
				if ( ! isset( $elm_data['color_drop_active_bg'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_active_bg'] =
						us_arr_path( $usof_options, 'color_drop_active_bg', 'transparent' );
				}
				if ( ! isset( $elm_data['color_drop_active_text'] ) ) {
					$header_settings['data'][ $elm_id ]['color_drop_active_text'] =
						us_arr_path( $usof_options, 'color_drop_active_text', '_header_middle_text_hover' );
				}
			}

			// Search
			if ( strpos( $elm_id, 'search' ) === 0 ) {
				if ( ! isset( $elm_data['field_bg_color'] ) ) {
					$header_settings['data'][ $elm_id ]['field_bg_color'] =
						us_arr_path( $usof_options, 'color_header_search_bg', '' );
				}
				if ( ! isset( $elm_data['field_text_color'] ) ) {
					$header_settings['data'][ $elm_id ]['field_text_color'] =
						us_arr_path( $usof_options, 'color_header_search_text', '' );
				}

				// Search Shop Products only
				if ( ! empty( $elm_data['product_search'] ) ) {
					$header_settings['data'][ $elm_id ]['search_post_type'] = 'product';
					unset( $header_settings['data'][ $elm_id ]['product_search'] );
				}
			}

		}

		return $header_settings;
	}
}

if ( ! function_exists( 'us_hb_post_edit_form_tag' ) ) {
	/**
	 * Block the submission of a standard form in the header builder.
	 *
	 * @param WP_Post $post The post
	 */
	function us_hb_post_edit_form_tag( WP_Post $post ) {
		if ( $post->post_type === 'us_header' ) {
			echo ' onsubmit="return false"';
		}
	}
	add_action( 'post_edit_form_tag', 'us_hb_post_edit_form_tag', 1, 1 );
}
