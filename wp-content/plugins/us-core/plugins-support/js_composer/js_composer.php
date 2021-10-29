<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WPBakery Page Builder support
 *
 * @link http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431?ref=UpSolution
 */

/**
 * Link "fallback" file for correct work of deprecated shortcodes attributes.
 * This allows to avoid content migration after updates.
 */
require US_CORE_DIR . 'plugins-support/js_composer/fallback.php';

/**
 * IF WPBakery is inactive - add functions that we need ONLY in case it is inactive and abort following file execution
 */
if ( ! class_exists( 'Vc_Manager' ) ) {

	/**
	 * @param $width
	 *
	 * @return bool|string
	 * @since 4.2
	 */
	function us_wpb_translateColumnWidthToSpan( $width ) {
		preg_match( '/(\d+)\/(\d+)/', $width, $matches );
		if ( ! empty( $matches ) ) {
			$part_x = (int) $matches[1];
			$part_y = (int) $matches[2];
			if ( $part_x > 0 AND $part_y > 0 ) {
				$value = ceil( $part_x / $part_y * 12 );
				if ( $value > 0 AND $value <= 12 ) {
					$width = 'vc_col-sm-' . $value;
				}
			}
		}
		if ( preg_match( '/\d+\/5$/', $width ) ) {
			$width = 'vc_col-sm-' . $width;
		}

		return $width;
	}

	/**
	 * @param $column_offset
	 * @param $width
	 *
	 * @return mixed|string
	 */
	function us_vc_column_offset_class_merge( $column_offset, $width ) {
		if ( preg_match( '/vc_col\-sm\-\d+/', $column_offset ) ) {
			return $column_offset;
		}

		return $width . ( empty( $column_offset ) ? '' : ' ' . $column_offset );
	}

	return;
}

/**
 * Code from this line and to the end of the file should be executed ONLY with WPBakery active
 */
add_action( 'vc_before_init', 'us_vc_set_as_theme' );
function us_vc_set_as_theme() {
	vc_set_as_theme();
}

// Disable WPBakery own updating hooks
add_action( 'vc_after_init', 'us_vc_after_init' );
function us_vc_after_init() {
	$updater = vc_manager()->updater();
	$updateManager = $updater->updateManager();

	remove_filter( 'upgrader_pre_download', array( $updater, 'preUpgradeFilter' ) );
	remove_filter( 'pre_set_site_transient_update_plugins', array( $updateManager, 'check_update' ) );
	remove_filter( 'plugins_api', array( $updateManager, 'check_info' ) );
	remove_action( 'in_plugin_update_message-' . vc_plugin_name(), array( $updateManager, 'addUpgradeMessageLink' ) );
}

add_action( 'vc_after_set_mode', 'us_vc_after_set_mode' );
function us_vc_after_set_mode() {

	do_action( 'us_before_js_composer_mappings' );

	// Remove VC Font Awesome style in admin pages
	add_action( 'admin_head', 'us_wpb_remove_admin_assets', 1 );
	function us_wpb_remove_admin_assets() {
		foreach ( array( 'ui-custom-theme', 'vc_font_awesome_5_shims', 'vc_font_awesome_5' ) as $handle ) {
			if ( wp_style_is( $handle, 'registered' ) ) {
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
			}
		}
		if ( us_get_option( 'disable_extra_vc', 1 ) AND wp_style_is( 'vc_animate-css', 'registered' ) ) {
			wp_dequeue_style( 'vc_animate-css' );
			wp_deregister_style( 'vc_animate-css' );
		}
	}

	// Remove original VC styles and scripts
	if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {

		// Remove some of the shortcodes handlers to use native VC shortcodes instead for front-end compatibility
		US_Shortcodes::instance()->vc_front_end_compatibility();

		// Add theme CSS for frontend editor
		add_action( 'wp_enqueue_scripts', 'us_process_css_for_frontend_js_composer', 15 );
		function us_process_css_for_frontend_js_composer() {
			wp_enqueue_style( 'us_js_composer_front', US_CORE_URI . '/plugins-support/js_composer/css/us_frontend_editor.css' );
		}

	} else {

		// Remove original VC styles and scripts
		add_action( 'wp_enqueue_scripts', 'us_vc_remove_base_css_js', 15 );
		function us_vc_remove_base_css_js() {
			if ( wp_style_is( 'vc_font_awesome_5', 'registered' ) ) {
				wp_dequeue_style( 'vc_font_awesome_5' );
				wp_deregister_style( 'vc_font_awesome_5' );
			}
			if ( us_get_option( 'disable_extra_vc', 1 ) ) {
				if ( wp_style_is( 'js_composer_front', 'registered' ) ) {
					wp_dequeue_style( 'js_composer_front' );
					wp_deregister_style( 'js_composer_front' );
				}
				if ( wp_script_is( 'wpb_composer_front_js', 'registered' ) ) {
					wp_deregister_script( 'wpb_composer_front_js' );
				}
				// Starting from version 6.1, id was removed from inline styles
				if ( defined( 'WPB_VC_VERSION' ) AND version_compare( WPB_VC_VERSION, '6.0.3', '<=' ) ) {
					// Add custom css
					( new Us_Vc_Base )->init();
				}
			}
		}
	}

	// Remove "Grid" admin menu item
	if ( is_admin() AND us_get_option( 'disable_extra_vc', 1 ) ) {

		add_action( 'admin_menu', 'us_vc_remove_grid_elements_submenu' );
		function us_vc_remove_grid_elements_submenu() {
			remove_submenu_page( VC_PAGE_MAIN_SLUG, 'edit.php?post_type=vc_grid_item' );
		}
	}

	// Disable Icon Picker assets
	if ( us_get_option( 'disable_extra_vc', 1 ) ) {
		remove_action( 'vc_backend_editor_enqueue_js_css', 'vc_iconpicker_editor_jscss' );
		remove_action( 'vc_frontend_editor_enqueue_js_css', 'vc_iconpicker_editor_jscss' );
	}

	do_action( 'us_after_js_composer_mappings' );
}

if ( ! function_exists( 'us_vc_init_shortcodes' ) ) {
	add_action( 'wp_loaded', 'us_vc_init_shortcodes', 11 );
	function us_vc_init_shortcodes() {
		if (
			! function_exists( 'vc_mode' )
			OR ! function_exists( 'vc_map' )
			OR ! function_exists( 'vc_remove_element' )
		) {
			return;
		}

		// Gets configurations for shortcodes
		$shortcodes_config = us_config( 'shortcodes', array(), TRUE );

		if ( us_get_option( 'disable_extra_vc', 1 ) ) {
			// Removing the elements that are not supported at the moment by the theme
			if (
				is_admin()
				AND ! empty( $shortcodes_config['disabled'] )
				AND is_array( $shortcodes_config['disabled'] )
			) {
				foreach ( $shortcodes_config['disabled'] as $shortcode ) {
					vc_remove_element( $shortcode );
				}
			} else {
				add_action( 'template_redirect', 'us_vc_disable_extra_sc', 100 );
			}
		}

		if ( vc_mode() === 'page' ) {
			return;
		}

		// Mapping WPBakery Page Builder backend behaviour for used shortcodes
		global $pagenow;

		/**
		 * If the page for editing roles then the result will be TRUE
		 * @var bool
		 */
		$is_edit_vc_roles = (
			$pagenow === 'admin.php'
			AND us_arr_path( $_GET, 'page' ) === 'vc-roles'
		);

		// Receive data only on the edit page or create a record
		if (
			wp_doing_ajax()
			OR in_array( $pagenow, array( 'post.php', 'post-new.php' ) )
			OR $is_edit_vc_roles
			OR ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() )
		) {
			foreach ( $shortcodes_config['theme_elements'] as $elm_name ) {
				$is_vc_elm = strpos( $elm_name, 'vc_' ) === 0;

				// Add prefix "us_" for non "vc_" shortcodes
				$shortcode = us_get_shortcode_full_name( $elm_name );
				$elm = us_config( "elements/{$elm_name}", array() );

				$vc_elm = array(
					'name' => isset( $elm['title'] ) ? $elm['title'] : $shortcode,
					'description' => isset( $elm['description'] ) ? $elm['description'] : '',
					'base' => $shortcode,
					'icon' => isset( $elm['icon'] ) ? $elm['icon'] : '',
					'category' => isset( $elm['category'] ) ? $elm['category'] : us_translate( 'Content' ),
					'weight' => isset( $elm['weight'] ) ? $elm['weight'] : 380, // elements go after "Text Block", which has the "390" weight
					'admin_enqueue_js' => isset( $elm['admin_enqueue_js'] ) ? $elm['admin_enqueue_js'] : NULL,
					'is_container' => isset( $elm['is_container'] ) ? $elm['is_container'] : NULL,
					'as_parent' => isset( $elm['as_parent'] ) ? $elm['as_parent'] : NULL,
					'as_child' => isset( $elm['as_child'] ) ? $elm['as_child'] : NULL,
					'js_view' => isset( $elm['js_view'] ) ? $elm['js_view'] : NULL,
					'params' => array(),
				);

				// Global updates for the correct work of the shortcode in all editors
				if ( isset( $elm['allowed_container_element'] ) ) {
					vc_map_update( 'vc_column_inner', array( 'allowed_container_element' => $elm['allowed_container_element'] ) );
				}

				$vc_elm_params_names = array();
				if ( isset( $elm['params'] ) AND is_array( $elm['params'] ) ) {
					foreach ( $elm['params'] as $param_name => &$param ) {
						if (
							isset( $param['context'] )
							AND is_array( $param['context'] )
							AND ! in_array( 'shortcode', $param['context'] )
							OR (
								isset( $param['place_if'] )
								AND $param['place_if'] === FALSE
							)
						) {
							continue;
						}
						$vc_param = _us_vc_param( $param_name, $param );
						if ( $vc_param != NULL ) {
							$vc_elm['params'][] = $vc_param;
							$vc_elm_params_names[] = $param_name;
						}
					}
					unset( $param );
				}

				// Add specified params as hidden fields, so js_composer processes them during fallback
				if ( ! empty( $elm['fallback_params'] ) ) {
					foreach ( $elm['fallback_params'] as $param_name ) {
						$vc_elm['params'][] = array(
							'type' => 'textfield',
							'param_name' => $param_name,
							'std' => '',
							'edit_field_class' => 'hidden',
						);
						$vc_elm_params_names[] = $param_name;
					}
				}

				// Adds US shortcode
				if ( ! $is_vc_elm ) {
					vc_map( $vc_elm );

					// Adds Visual Composer shortcode
				} else {

					// Get VC element default param names
					$original_params = vc_map_get_defaults( $shortcode );
					$original_params_names = ( ! empty( $original_params ) ) ? array_keys( $original_params ) : array();

					// Get params to remove, which set in config
					$params_to_remove = ( ! empty( $elm['vc_remove_params'] ) ) ? $elm['vc_remove_params'] : array();
					$params_to_remove = array_merge( $params_to_remove, array_keys( $elm['params'] ) );

					// Remove params with the same name as original
					foreach ( $params_to_remove as $param_name ) {
						if ( in_array( $param_name, $original_params_names ) ) {
							vc_remove_param( $shortcode, $param_name );
						}
					}

					// Add params as new
					foreach( $vc_elm['params'] as $vc_param ) {
						vc_add_param( $shortcode, $vc_param );
					}

					// Update category for VC element
					// Dev note: vc_map_update should go after vc_update_shortcode_param / vc_add_param here (otherwise WPBakery may glitch)
					if ( ! empty( $elm[ 'category' ] ) ) {
						vc_map_update( $shortcode, array( 'category' => $elm[ 'category' ] ) );
					}
					if ( ! empty( $elm[ 'weight' ] ) ) {
						vc_map_update( $shortcode, array( 'weight' => $elm[ 'weight' ] ) );
					}
				}

				// This is required for the access edit page on the vc-roles page
				if ( $is_edit_vc_roles AND ! $is_vc_elm ) {
					vc_lean_map( $shortcode, function() use( $vc_elm ) {
						return $vc_elm;
					} );
				}
			}
		}

		// Apply new design styles to VC shortcodes for which there is no map
		$shortcodes_with_design_options = $shortcodes_config['added_design_options'];
		foreach ( $shortcodes_with_design_options as $vc_shortcode_name ) {
			vc_update_shortcode_param(
				$vc_shortcode_name, array(
					'param_name' => 'css',
					'type' => 'us_design_options',
					'heading' => '',
					'params' => us_config( 'elements_design_options.css.params', array() ),
					'group' => __( 'Design', 'us' ),
				)
			);
		}
	}

	/**
	 * Formats US parameter to VC format
	 *
	 * @param string $param_name The param name
	 * @param array $param The params
	 * @return array
	 */
	function _us_vc_param( $param_name, $param ) {
		// Translation from our builder param types to WPBakery param types
		$related_types = array(
			'checkboxes' => 'checkbox',
			'color' => 'us_color',
			'css_editor' => 'css_editor',
			'design_options' => 'us_design_options',
			'editor' => 'textarea_html',
			'group' => 'param_group',
			'heading' => 'param_to_delete',
			'html' => 'textarea_raw_html',
			'icon' => 'us_icon',
			'imgradio' => 'us_imgradio',
			'link' => 'vc_link',
			'radio' => 'dropdown',
			'select' => 'us_select',
			'slider' => 'textfield',
			'switch' => 'checkbox',
			'text' => 'textfield',
			'textarea' => 'textarea',
			'upload' => 'attach_image',
			'wrapper_end' => 'param_to_delete',
			'wrapper_start' => 'param_to_delete',
			'us_autocomplete' => 'us_autocomplete',
		);

		$param = is_array( $param ) ? $param : array();
		$param['type'] = isset( $param['type'] ) ? $param['type'] : 'text';

		$type = ( isset( $param['type'] ) AND isset( $related_types[ $param['type'] ] ) )
			? $related_types[ $param['type'] ]
			: 'textfield';

		// Check if param is not wanted in WPBakery builder, and if so, return nothing for it
		if ( $type == 'param_to_delete' ) {
			return NULL;
		}

		/**
		 * Some attributes of params may be set for shortcodes exclusively,
		 * which is indicated by shortcode_ prefix in their names,
		 * checking if such attributes are present and adding them to the result array without prefix
		 */
		$attributes_with_prefixes = array(
			'title',
			'description',
			'options',
			'classes',
			'cols',
			'std',
			'show_if',
		);
		foreach ( $attributes_with_prefixes as $attribute ) {
			if ( isset( $param[ 'shortcode_' . $attribute ] ) ) {
				$param[ $attribute ] = $param[ 'shortcode_' . $attribute ];
			}
		}

		// Base structure of a param
		$vc_param = array(
			'admin_label' => isset( $param['admin_label'] ) ? $param['admin_label'] : FALSE,
			'description' => isset( $param['description'] ) ? $param['description'] : '',
			'edit_field_class' => ! empty( $param['classes'] ) ? $param['classes'] : NULL,
			'heading' => isset( $param['title'] ) ? $param['title'] : '',
			'holder' => isset( $param['holder'] ) ? $param['holder'] : NULL,
			// Important! This attribute must be non-empty
			'param_name' => $param_name,
			'params' => ( isset( $param['params'] ) AND $param['type'] === 'design_options' ) ? $param['params'] : NULL,
			'settings' => isset( $param['settings'] ) ? $param['settings'] : NULL,
			'std' => isset( $param['std'] ) ? $param['std'] : '',
			'type' => $type,
			'weight' => isset( $param['weight'] ) ? $param['weight'] : NULL,
		);

		// Add option CSS classes based on "cols" param
		if ( isset( $param['cols'] ) ) {
			$_cols_k = 12 / (int) $param['cols'];
			if ( empty( $vc_param['edit_field_class'] ) ) {
				$vc_param['edit_field_class'] = 'vc_col-sm-' . $_cols_k;
			} else {
				$vc_param['edit_field_class'] .= ' vc_col-sm-' . $_cols_k;
			}
		}

		// Setting group tab for a param
		if ( ! empty( $param['group'] ) ) {
			$vc_param['group'] = $param['group'];
		}

		// Changing type for attach_image with is_multiple setting to attach_images
		if ( $vc_param['type'] == 'attach_image' AND isset( $param['is_multiple'] ) AND $param['is_multiple'] ) {
			$vc_param['type'] = 'attach_images';
		}

		// Adding is_multiple / is_sortable args for us_autocomplete param if set in our config
		if ( $vc_param['type'] == 'us_autocomplete' ) {
			foreach ( array( 'is_multiple', 'is_sortable' ) as $_param_arg ) {
				if ( isset( $param[ $_param_arg ] ) ) {
					$vc_param[ $_param_arg ] = $param[ $_param_arg ];
				}
			}
		}


		// Translating value options for respective params to WPBakery format
		$param_types_with_options = array( 'dropdown', 'us_autocomplete', 'us_imgradio', );
		if (
			in_array( $vc_param['type'], $param_types_with_options )
			AND isset( $param['options'] )
		) {
			$vc_param['value'] = array();
			foreach ( $param['options'] as $option_val => $option_name ) {
				if ( is_string( $option_name ) ) {
					$vc_param['value'][ $option_name . ' ' ] = $option_val . '';
				}
			}
		}

		// VC Checkboxes
		if ( $vc_param['type'] == 'checkbox' ) {
			// For USBuilder and Visual Composer compatibility
			if ( strpos( $param_name, 'taxonomy_' ) === 0 AND is_array( $param['options'] ) ) {
				$param['options'] = array_flip( $param['options'] );
			}

			if ( isset( $param['options'] ) AND ! empty( $param['options_prepared_for_wpb'] ) ) {
				$vc_param['value'] = array();
				foreach ( $param['options'] as $option_val => $option_name ) {
					$vc_param['value'][ $option_val . '' ] = $option_name . '';
				}
			} elseif ( isset( $param['options'] ) ) {
				$vc_param['value'] = array();
				foreach ( $param['options'] as $option_val => $option_name ) {
					$vc_param['value'][ $option_name . ' ' ] = $option_val . '';
				}
			} elseif ( isset( $param['switch_text'] ) ) {
				$vc_param['value'] = array( $param['switch_text'] => TRUE );
			}
			if ( is_array( $vc_param['std'] ) ) {
				$vc_param['std'] = implode( ',', $vc_param['std'] );
			} elseif ( $vc_param['std'] === TRUE ) {
				$vc_param['std'] = '1';
			} elseif ( $vc_param['std'] === FALSE ) {
				$vc_param['std'] = '';
			}
		}

		// Proper dependency rules
		if ( isset( $param['show_if'] ) AND count( $param['show_if'] ) == 3 ) {
			$vc_param['dependency'] = array(
				'element' => $param['show_if'][0],
			);
			if ( $param['show_if'][1] == '=' AND $param['show_if'][2] == '' ) {
				$vc_param['dependency']['is_empty'] = TRUE;
			} elseif ( $param['show_if'][1] == '!=' AND $param['show_if'][2] == '' ) {
				$vc_param['dependency']['not_empty'] = TRUE;
			} elseif ( $param['show_if'][1] == '!=' AND ! empty( $param['show_if'][2] ) ) {
				$vc_param['dependency']['value_not_equal_to'] = $param['show_if'][2];
			} else {
				$vc_param['dependency']['value'] = $param['show_if'][2];
			}
		}

		// Proper group rules
		if ( $vc_param['type'] == 'param_group' ) {
			if ( isset( $param['params'] ) AND is_array( $param['params'] ) ) {
				$group_params = $param['params'];
				$param['params'] = array();
				foreach ( $group_params as $group_param_name => $group_param ) {
					$group_vc_param = _us_vc_param( $group_param_name, $group_param );
					if ( $group_vc_param != NULL ) {
						$vc_param['params'][] = $group_vc_param;
					}
				}
			}

			// Transform the array value to a string
			if ( isset( $vc_param['std'] ) AND is_array( $vc_param['std'] ) ) {
				$vc_param['std'] = rawurlencode( json_encode( $vc_param['std'] ) );
			}
		}

		// US Color additional params
		if ( isset( $param['type'] ) and $param['type'] == 'color' ) {
			if ( isset( $param['clear_pos'] ) ) {
				$vc_param['clear_pos'] = $param['clear_pos'];
			}
			if ( isset( $param['with_gradient'] ) ) {
				$vc_param['with_gradient'] = FALSE;
			}
			if ( ! empty( $param['disable_dynamic_vars'] ) ) {
				$vc_param['disable_dynamic_vars'] = TRUE;
			}
		}

		// US ImgRadio
		if ( $vc_param['type'] === 'us_imgradio' AND $preview_path = us_arr_path( $param, 'preview_path' ) ) {
			$vc_param['preview_path'] = $preview_path;
		}

		// US Select
		if ( $vc_param['type'] === 'us_select' ) {
			$vc_param[ 'settings' ] = $param['options'];

			// Add data to support the display if there is `admin_label`
			if ( TRUE === us_arr_path( $vc_param, 'admin_label', FALSE ) ) {
				$vc_value = (array) $vc_param[ 'settings' ];
				// Note: Visual Composer does not support multidimensional arrays,
				// so if the array is multidimensional, turn it into one-dimensional.
				if ( count( $vc_value, COUNT_RECURSIVE ) - count( $vc_value ) ) {
					$_vc_value = array();
					array_walk_recursive( $vc_value, function ( $value, $key ) use ( &$_vc_value ) {
						$_vc_value[ $key ] = $value;
					} );
					$vc_value = $_vc_value;
					unset( $_vc_value );
				}
				// Visual Composer supports `value => key`.
				$vc_param[ 'value' ] = array_flip( $vc_value );
			}

			if ( ! empty( $param['data'] ) ) {
				$vc_param['data'] = (array) $param['data'];
			}
		}

		return $vc_param;
	}
}

if ( ! function_exists( 'us_vc_shortcodes_custom_css_class' ) ) {
	add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'us_vc_shortcodes_custom_css_class', 10, 3 );
	/**
	 * Adding a unique class from Design settings for VC shortcodes, which don't have theme templates
	 *
	 * @param string $class
	 * @param string $shortcode_base
	 * @param array $atts
	 *
	 * @return string
	 */
	function us_vc_shortcodes_custom_css_class( $class, $shortcode_base, $atts = array() ) {
		$shortcodes_with_design_options = us_config( 'shortcodes.added_design_options', array(), TRUE );
		if (
			in_array( $shortcode_base, $shortcodes_with_design_options )
			AND function_exists( 'us_get_design_css_class' )
			AND ( ! empty( $atts['css'] ) )
		) {
			$class .= ' ' . us_get_design_css_class( $atts['css'] );
		}
		if ( ! empty( $atts['css'] ) AND us_design_options_has_property( $atts['css'], 'border-radius' ) ) {
			$class .= ' has_border_radius';
		}

		return $class;
	}
}

add_action( 'current_screen', 'us_wpb_disable_post_type_specific_elements' );
function us_wpb_disable_post_type_specific_elements() {
	if ( function_exists( 'get_current_screen' ) ) {
		global $pagenow;
		// Receive data only on the edit page or create a record
		if ( wp_doing_ajax() OR ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}

		$screen = get_current_screen();
		$shortcodes_config = us_config( 'shortcodes', array(), TRUE );

		foreach ( $shortcodes_config['theme_elements'] as $elm_name ) {
			// Add prefix "us_" for non "vc_" shortcodes
			$shortcode = us_get_shortcode_full_name( $elm_name );
			$elm = us_config( "elements/{$elm_name}", array() );

			if ( isset( $elm['shortcode_post_type'] ) ) {
				if ( ! empty( $screen->post_type ) AND ! in_array( $screen->post_type, $elm['shortcode_post_type'] ) ) {
					vc_remove_element( $shortcode );
				}
			}
		}
	}
}

/**
 * Disable WPBakery Frontend editor, when Live Editor is enabled
 */
if ( function_exists( 'vc_disable_frontend' ) AND us_get_option( 'live_builder' ) ) {
	vc_disable_frontend();
}

/**
 * Remove disabled WPB shortcodes
 */
if ( ! function_exists( 'us_vc_disable_extra_sc' ) ) {
	function us_vc_disable_extra_sc() {
		$disabled_shortcodes = us_config( 'shortcodes.disabled', array() );

		foreach ( $disabled_shortcodes as $shortcode ) {
			remove_shortcode( $shortcode );
		}
	}
}

// Disable redirect to VC welcome page
remove_action( 'init', 'vc_page_welcome_redirect' );

add_action( 'after_setup_theme', 'us_vc_init_vendor_woocommerce', 99 );
function us_vc_init_vendor_woocommerce() {
	remove_action( 'wp_enqueue_scripts', 'vc_woocommerce_add_to_cart_script' );
}

if ( ! function_exists( 'us_VC_fixPContent' ) ) {
	add_filter( 'us_page_block_the_content', 'us_VC_fixPContent', 11 );
	add_filter( 'us_content_template_the_content', 'us_VC_fixPContent', 11 );
	/**
	 * @param string|NULL $content The content
	 * @return mixed
	 */
	function us_VC_fixPContent( $content = NULL ) {
		if ( $content ) {
			$patterns = array(
				'/' . preg_quote( '</div>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<div ', '/' ) . '/i',
				'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<section ', '/' ) . '/i',
				'/' . preg_quote( '</section>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
			);
			$replacements = array(
				'</div>',
				'<div ',
				'<section ',
				'</section>',
			);
			$content = preg_replace( $patterns, $replacements, $content );

			return $content;
		}

		return NULL;
	}
}

// Hide activation notice
add_action( 'admin_notices', 'us_wpb_hide_activation_notice', 100 );
function us_wpb_hide_activation_notice() {
	?>
	<script>
		( function( $ ) {
			var setCookie = function( c_name, value, exdays ) {
				var exdate = new Date();
				exdate.setDate( exdate.getDate() + exdays );
				var c_value = encodeURIComponent( value ) + ( ( null === exdays ) ? "" : "; expires=" + exdate.toUTCString() );
				document.cookie = c_name + "=" + c_value;
			};
			setCookie( 'vchideactivationmsg_vc11', '100', 30 );
			$( '#vc_license-activation-notice' ).remove();
		} )( window.jQuery );
	</script>
	<?php
}

// Set Backend Editor as default for post types
if ( function_exists( 'vc_set_default_editor_post_types' ) ) {
	$post_types_list = array(
		'page',
		'us_portfolio',
		'us_page_block',
		'us_content_template',
	);
	vc_set_default_editor_post_types( $post_types_list );
}

// Remove Backend Editor for Headers & Grid Layouts
add_filter( 'vc_settings_exclude_post_type', 'us_vc_settings_exclude_post_type' );
function us_vc_settings_exclude_post_type( $types ) {
	$types = array(
		'us_header',
		'us_grid_layout',
	);

	return $types;
}

add_filter( 'vc_is_valid_post_type_be', 'us_vc_is_valid_post_type_be', 10, 2 );
function us_vc_is_valid_post_type_be( $result, $type ) {
	if ( in_array( $type, array( 'us_header', 'us_grid_layout' ) ) ) {
		$result = FALSE;
	}

	return $result;
}

// For a text field of `us_text` will replace all hyphenation with tags for correct display in the edit field
if ( ! function_exists( 'us_vc_form_fields_render_field_us_text_text_param_value' ) ) {
	add_filter( 'vc_form_fields_render_field_us_text_text_param_value', 'us_vc_form_fields_render_field_us_text_text_param_value', 10, 1 );
	function us_vc_form_fields_render_field_us_text_text_param_value( $value ) {
		return nl2br( $value );
	}
}

add_action( 'current_screen', 'us_vc_header_check_post_type_validation_fix' );
function us_vc_header_check_post_type_validation_fix( $current_screen ) {
	global $pagenow;
	if ( $pagenow == 'post.php' AND $current_screen->post_type == 'us_header' ) {
		add_filter( 'vc_check_post_type_validation', '__return_false', 12 );
	}
}

// New design option
if ( ! function_exists( 'us_vc_field_design_options' ) ) {
	vc_add_shortcode_param( 'us_design_options', 'us_vc_field_design_options', US_CORE_URI . '/plugins-support/js_composer/js/us_design_options.js' );
	/**
	 * The group of parameters that will be converted to inline css
	 * Inline css supports both grouping and linear parameters
	 *
	 * @param array $settings The field settings
	 * @param string $value The field value
	 * @return string
	 */
	function us_vc_field_design_options( $settings, $value ) {
		$design_options = us_get_template(
			'usof/templates/fields/design_options', array(
				'params' => $settings['params'],
				'name' => $settings['param_name'],
				'value' => $value,
				'classes' => 'wpb_vc_param_value',
			)
		);

		return '<div class="type_design_options" data-name="' . esc_attr( $settings['param_name'] ) . '">' . $design_options . '</div>';

	}
}

if ( ! function_exists( 'us_vc_field_autocomplete' ) ) {
	vc_add_shortcode_param( 'us_autocomplete', 'us_vc_field_autocomplete', US_CORE_URI . '/plugins-support/js_composer/js/us_autocomplete.js' );
	/**
	 * @param array $settings The settings
	 * @param mixed $value The value
	 * @return string
	 */
	function us_vc_field_autocomplete( $settings, $values ) {
		$output = us_get_template( 'usof/templates/fields/autocomplete', array(
			'name' => esc_attr( $settings['param_name'] ),
			'value' => $values,
			'field' => array(
				'classes' => 'wpb_vc_param_value',
				'is_multiple' => (bool) us_arr_path( $settings, 'is_multiple', FALSE ),
				'is_sortable' => (bool) us_arr_path( $settings, 'is_sortable', FALSE ),
				'settings' => us_arr_path( $settings, 'settings', array() ),
				'options' => array_map( 'trim', array_flip( us_arr_path( $settings, 'value', array() ) ) ),
			),


		) );

		return '<div class="type_autocomplete" data-name="'. esc_attr( $settings['param_name'] ) .'">'. $output .'</div>';
	}
}

// Add parameter for icon selection
if ( ! function_exists( 'us_vc_field_icon' ) ) {
	vc_add_shortcode_param( 'us_icon', 'us_vc_field_icon', US_CORE_URI . '/plugins-support/js_composer/js/us_icon.js' );

	function us_vc_field_icon( $settings, $value ) {

		// Get "Icon" usof template with changed class for input
		return us_get_template(
			'usof/templates/fields/icon', array(
				'name' => $settings['param_name'],
				'value' => $value,
				'input_class' => 'wpb_vc_param_value wpb-textinput ' . $settings['param_name'] . ' ' . $settings['type'] . '_field',
			)
		);
	}
}

// Add parameter for colorpicker
if ( ! function_exists( 'us_vc_field_color' ) ) {
	vc_add_shortcode_param( 'us_color', 'us_vc_field_color', US_CORE_URI . '/plugins-support/js_composer/js/us_color.js' );

	function us_vc_field_color( $settings, $value ) {
		$value = trim( $value );
		ob_start();
		?>
		<div class="us_color">
			<input name="<?php echo esc_attr( $settings['param_name'] ); ?>" class="wpb_vc_param_value wpb-textinput <?php echo esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field'; ?>" type="hidden" value="<?php echo esc_attr( $value ); ?>">
			<div class="type_color" data-name="<?php echo $settings['param_name']; ?>" data-id="<?php echo $settings['param_name']; ?>">
				<?php
				us_load_template(
					'usof/templates/fields/color', array(
						'name' => $settings['param_name'],
						'value' => $value,
						'field' => array(
							'std' => $settings['std'],
							'clear_pos' => isset( $settings['clear_pos'] ) ? $settings['clear_pos'] : NULL,
							'with_gradient' => isset( $settings['with_gradient'] ) ? FALSE : NULL,
						),
					)
				);
				?>
			</div>
		</div>

		<?php
		$result = ob_get_clean();

		return $result;
	}
}

// Add parameter for images radio selection
if ( ! function_exists( 'us_vc_field_imgradio' ) ) {
	vc_add_shortcode_param( 'us_imgradio', 'us_vc_field_imgradio', US_CORE_URI . '/plugins-support/js_composer/js/us_imgradio.js' );
	/**
	 * @param array $settings
	 * @param string $value
	 * @return string
	 */
	function us_vc_field_imgradio( $settings, $value ) {
		$param_name = us_arr_path( $settings, 'param_name', NULL );
		if ( empty( $param_name ) ) {
			return;
		}

		return us_get_template( 'usof/templates/fields/imgradio', array(
			'classes' => 'wpb_vc_param_value',
			'name' => $param_name,
			'value' => $value,
			'field' => array(
				'group' => us_arr_path( $settings, 'group', '' ),
				'options' => array_flip( us_arr_path( $settings, 'value', array() ) ),
				'preview_path' => us_arr_path( $settings, 'preview_path', '' ),
		) ) );
	}
}

// Add parameter for grouped Selection
if ( ! function_exists( 'us_vc_field_select' ) ) {
	vc_add_shortcode_param( 'us_select', 'us_vc_field_select', US_CORE_URI . '/plugins-support/js_composer/js/us_select.js' );
	/**
	 * @param array $field The field settings
	 * @param string $value The value
	 * @return string
	 */
	function us_vc_field_select( $field, $value ) {
		$param_name = us_arr_path( $field, 'param_name', NULL );
		if ( empty( $param_name ) ) {
			return;
		}

		$classes = us_arr_path( $field, 'edit_field_class', '' );
		$output = us_get_template( 'usof/templates/fields/select', array(
			'name' => $param_name,
			'value' => $value,
			'field' => array(
				'options' => us_arr_path( $field, 'settings', array() ),
				'data' => (array) us_arr_path( $field, 'data', array() )
			),
		));

		return '<div class="us_select_for_vc type_select '. esc_attr( $classes ) .'" data-name="'. esc_attr( $param_name ) .'">'. $output .'</div>';
	}
}

// Add script to fill inputs with examples from description
add_action( 'admin_enqueue_scripts', 'us_wpb_input_examples' );
function us_wpb_input_examples() {
	global $pagenow;
	$screen = get_current_screen();
	$current_post_type = $screen->post_type;
	$excluded_post_types = array(
		'us_header',
		'us_grid_layout',
	);

	if ( $pagenow != 'post.php' OR in_array( $current_post_type, $excluded_post_types ) ) {
		return;
	}

	wp_enqueue_script( 'us_input_examples_vc', US_CORE_URI . '/plugins-support/js_composer/js/us_input_examples.js', array( 'jquery' ), US_THEMEVERSION );
}

if ( wp_doing_ajax() ) {
	// AJAX request handler import data for shortcode
	// TODO: do we need this in US Builder?
	add_action( 'wp_ajax_us_import_shortcode_data', 'us_wpb_ajax_import_shortcode_data' );
	if ( ! function_exists( 'us_wpb_ajax_import_shortcode_data' ) ) {
		function us_wpb_ajax_import_shortcode_data() {
			if ( ! check_ajax_referer( 'us_ajax_import_shortcode_data', '_nonce', FALSE ) ) {
				wp_send_json_error(
					array(
						'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
					)
				);
				wp_die();
			}

			// The response data
			wp_send_json_success( us_import_grid_layout(
				us_arr_path( $_POST, 'post_content' ),
				us_arr_path( $_POST, 'post_type', /* Default */'us_grid_layout' )
			) );
		}
	}
}

// Add image preview for Image shortcode
if ( ! class_exists( 'WPBakeryShortCode_us_image' ) ) {
	class WPBakeryShortCode_us_image extends WPBakeryShortCode {
		public function singleParamHtmlHolder( $param, $value ) {
			$output = '';
			// Compatibility fixes
			$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
			$type = isset( $param['type'] ) ? $param['type'] : '';
			$class = isset( $param['class'] ) ? $param['class'] : '';
			if ( $type == 'attach_image' AND $param_name == 'image' ) {
				$output .= '<input type="hidden" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';
				$element_icon = $this->settings( 'icon' );
				$img = wpb_getImageBySize(
					array(
						'attach_id' => (int) preg_replace( '/[^\d]/', '', $value ),
						'thumb_size' => 'thumbnail',
					)
				);
				$logo_html = '';
				if ( $img ) {
					$logo_html .= $img['thumbnail'];
				} else {
					$logo_html .= '<img width="150" height="150" class="attachment-thumbnail icon-wpb-single-image vc_element-icon" data-name="' . $param_name . '" alt="' . $param_name . '" style="display: none;" />';
				}
				$logo_html .= '<span class="no_image_image vc_element-icon' . ( ! empty( $element_icon ) ? ' ' . $element_icon : '' ) . ( $img && ! empty( $img['p_img_large'][0] ) ? ' image-exists' : '' ) . '" />';
				$this->setSettings( 'logo', $logo_html );
				$output .= $this->outputTitleTrue( $this->settings['name'] );
			} elseif ( ! empty( $param['holder'] ) ) {
				if ( $param['holder'] == 'input' ) {
					$output .= '<' . $param['holder'] . ' readonly="true" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '">';
				} elseif ( in_array( $param['holder'], array( 'img', 'iframe' ) ) ) {
					$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" src="' . $value . '">';
				} elseif ( $param['holder'] !== 'hidden' ) {
					$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
				}
			}
			if ( ! empty( $param['admin_label'] ) && $param['admin_label'] === TRUE ) {
				$output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . __( $param['heading'], 'js_composer' ) . '</label>: ' . $value . '</span>'; // TODO: gettext function won't work with variables
			}

			return $output;
		}

		protected function outputTitle( $title ) {
			return '';
		}

		protected function outputTitleTrue( $title ) {
			return '<h4 class="wpb_element_title">' . __( $title, 'us' ) . ' ' . $this->settings( 'logo' ) . '</h4>';
		}
	}
}

// Add column UX behavior for us_hwrapper shortcode
if ( ! class_exists( 'WPBakeryShortCode_us_hwrapper' ) ) {
	class WPBakeryShortCode_us_hwrapper extends WPBakeryShortCodesContainer {
	}
}

// Add column UX behavior for us_vwrapper shortcode
if ( ! class_exists( 'WPBakeryShortCode_us_vwrapper' ) ) {
	class WPBakeryShortCode_us_vwrapper extends WPBakeryShortCodesContainer {
	}
}

/**
 * Extending shortcode: vc_row
 */
if ( ! class_exists( 'Us_WPBakeryShortCode_Vc_Row' ) ) {

	if ( ! class_exists( 'WPBakeryShortCode_Vc_Row' ) ) {
		require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-row.php' );
	}

	/**
	 * Extending the standard WPBakeryShortCode_Vc_Row class
	 */
	class Us_WPBakeryShortCode_Vc_Row extends WPBakeryShortCode_Vc_Row {
		/**
		 * Generate controls for row
		 * @param $controls
		 * @param string $extended_css
		 * @return string
		 * @throws \Exception
		 */
		public function getColumnControls( $controls, $extended_css = '' ) {
			$output = parent::getColumnControls( $controls, $extended_css = '' );

			// Adding a new controller to copy the shortcode to the clipboard
			return str_replace( '<a class="vc_control column_toggle', '<a class="vc_control column_copy_clipboard vc_column-copy-clipboard" href="#" title="' . us_translate( 'Copy' ) . '" data-vc-control="row-copy-clipboard"><i class="fas fa-copy"></i></a><a class="vc_control column_toggle', $output );
		}
	}

	vc_map_update( 'vc_row', array(
		// Assign a custom class to handle shortcode
		'php_class_name' => 'US_WPBakeryShortCode_Vc_Row'
	) );
}

// Add "Paste Copied Section" feature
add_filter( 'vc_nav_controls', 'us_vc_nav_controls_add_paste_section_btn' );
add_action( 'admin_enqueue_scripts', 'us_vc_add_paste_section_script', 10, 1 );
add_action( 'admin_footer-post.php', 'us_vc_add_paste_section_html' );
add_action( 'admin_footer-post-new.php', 'us_vc_add_paste_section_html' );

// "Paste Copied Section" button
function us_vc_nav_controls_add_paste_section_btn( $control_list ) {
	$control_list[] = array(
		'paste_section',
		'<li><a href="javascript:void(0);" class="vc_icon-btn" id="us_vc_paste_section_button"><span>' . strip_tags( __( 'Paste Row/Section', 'us' ) ) . '</span></a></li>',
	);

	return $control_list;
}

// "Paste Copied Section" script
function us_vc_add_paste_section_script( $hook ) {
	if ( $hook == 'post-new.php' OR $hook == 'post.php' ) {
		wp_enqueue_script( 'us_vc_paste_section_vc', US_CORE_URI . '/plugins-support/js_composer/js/us_paste_section.js', array( 'jquery' ), US_CORE_VERSION );
	}
}

// "Paste Copied Section" window
function us_vc_add_paste_section_html() {
	$data = array(
		'placeholder' => us_get_img_placeholder( 'full', TRUE ),
		'grid_post_types' => us_grid_available_post_types_for_import(),
		'post_type' => get_post_type(),
		'errors' => array(
			'empty' => us_translate( 'Invalid data provided.' ),
			'not_valid' => us_translate( 'Invalid data provided.' ),
		),
	);
	?>
	<div class="us-paste-section-window" style="display: none;" <?= us_pass_data_to_js( $data ) ?>
		 data-nonce="<?= wp_create_nonce( 'us_ajax_import_shortcode_data' ) ?>">
		<div class="vc_ui-panel-window-inner">
			<div class="vc_ui-panel-header-container">
				<div class="vc_ui-panel-header">
					<h3 class="vc_ui-panel-header-heading"><?= strip_tags( __( 'Paste Row/Section', 'us' ) ) ?></h3>
					<button type="button" class="vc_general vc_ui-control-button vc_ui-close-button" data-vc-ui-element="button-close">
						<i class="vc-composer-icon vc-c-icon-close"></i>
					</button>
				</div>
			</div>
			<div class="vc_ui-panel-content-container">
				<div class="vc_ui-panel-content vc_properties-list vc_edit_form_elements wpb_edit_form_elements">
					<div class="vc_column">
						<div class="edit_form_line">
							<textarea class="wpb_vc_param_value textarea_raw_html"></textarea>
							<span class="vc_description"><?= us_translate( 'Invalid data provided.' ) ?></span>
						</div>
					</div>
					<div class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded">
						<?= strip_tags( __( 'Append Section', 'us' ) ) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
