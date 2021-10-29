<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WPBakery Page Builder support
 *
 * @link http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431?ref=UpSolution
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
 * Check for CSS property in the shortcode attribute
 * @param string|array $subject
 * @param string|array $props
 * @param bool $strict
 * @return array
 */
function us_design_options_has_property( $subject, $props, $strict = FALSE ) {
	$result = [];

	if ( empty( $props ) ) {
		return $result;
	}

	if ( ! is_array( $props ) ) {
		$props = array( (string) $props );
	}

	$props = array_map( 'trim', $props );
	$props = array_map( 'strtolower', $props );

	if ( is_string( $subject ) ) {
		$subject = json_decode( urldecode( $subject ), TRUE );
	}

	if ( ! empty( $subject ) AND is_array( $subject ) ) {
		foreach ( $subject as $device_type => $values ) {
			$values = array_keys( $values );
			$values = array_map( 'strtolower', $values );

			foreach ( $props as $prop ) {
				if ( ! in_array( $device_type, $result ) AND array_search( $prop, $values, $strict ) !== FALSE ) {
					$result[] = $device_type;
				}
			}
		}
	}

	return array_unique( $result );
}

add_action( 'vc_before_init', 'us_vc_set_as_theme' );
function us_vc_set_as_theme() {
	vc_set_as_theme();
}

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
	add_action( 'admin_head', 'us_remove_js_composer_admin_assets', 1 );
	function us_remove_js_composer_admin_assets() {
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

	if ( ! vc_is_page_editable() ) {

		// Remove original VC styles and scripts
		add_action( 'wp_enqueue_scripts', 'us_remove_vc_base_css_js', 15 );
		function us_remove_vc_base_css_js() {
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
	} else {

		// Disable some of the shortcodes for frontend editor
		US_Shortcodes::instance()->vc_front_end_compatibility();

		// Add theme CSS for frontend editor
		add_action( 'wp_enqueue_scripts', 'us_process_css_for_frontend_js_composer', 15 );
		function us_process_css_for_frontend_js_composer() {
			wp_enqueue_style( 'us_js_composer_front', US_CORE_URI . '/plugins-support/js_composer/css/us_frontend_editor.css' );
		}
	}

	// Remove "Grid" admin menu item
	if ( is_admin() AND us_get_option( 'disable_extra_vc', 1 ) ) {

		add_action( 'admin_menu', 'us_remove_vc_grid_elements_submenu' );
		function us_remove_vc_grid_elements_submenu() {
			remove_submenu_page( VC_PAGE_MAIN_SLUG, 'edit.php?post_type=vc_grid_item' );
		}
	}

	// Disable Frontend Editor for Page Blocks and Content Template
	add_action( 'current_screen', 'us_disable_frontend_for_post_types' );

	// Disable Icon Picker assets
	if ( us_get_option( 'disable_extra_vc', 1 ) ) {
		remove_action( 'vc_backend_editor_enqueue_js_css', 'vc_iconpicker_editor_jscss' );
		remove_action( 'vc_frontend_editor_enqueue_js_css', 'vc_iconpicker_editor_jscss' );
	}

	do_action( 'us_after_js_composer_mappings' );
}

add_action( 'wp_loaded', 'us_vc_init_shortcodes', 11 );
function us_vc_init_shortcodes() {
	if ( ! function_exists( 'vc_mode' ) OR ! function_exists( 'vc_map' ) OR ! function_exists( 'vc_remove_element' ) ) {
		return;
	}

	global $pagenow;

	$shortcodes_config = us_config( 'shortcodes', array(), TRUE );

	// Mapping WPBakery Page Builder backend behaviour for used shortcodes
	if ( vc_mode() != 'page' ) {

		function us_vc_param( $param_name, $param ) {
			$related_types = array(
				'text' => 'textfield',
				'textarea' => 'textarea',
				'select' => 'dropdown',
				'radio' => 'dropdown',
				'color' => 'us_color',
				'slider' => 'textfield',
				'link' => 'vc_link',
				'icon' => 'us_icon',
				'switch' => 'checkbox',
				'checkboxes' => 'checkbox',
				'us_checkboxes' => 'us_checkboxes',
				'upload' => 'attach_image',
				'editor' => 'textarea_html',
				'html' => 'textarea_raw_html',
				'group' => 'param_group',
				'wrapper_start' => 'param_to_delete',
				'wrapper_end' => 'param_to_delete',
				'heading' => 'param_to_delete',
				'ult_param_heading' => 'ult_param_heading',
				'us_autocomplete' => 'us_autocomplete',
				'us_grid_layout' => 'us_grid_layout',
				'us_grouped_select' => 'us_grouped_select',
				'css_editor' => 'css_editor',
				'design_options' => 'us_design_options',
			);

			$type = ( isset( $param['type'] ) AND isset( $related_types[ $param['type'] ] ) )
				? $related_types[ $param['type'] ]
				: 'textfield';

			if ( $type == 'param_to_delete' ) {
				return NULL;
			}

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

			if ( $param['type'] == 'checkboxes' AND ! empty( $param['std'] ) AND is_array( $param['std'] ) ) {
				$param['std'] = implode( ',', $param['std'] );
			}

			$vc_param = array(
				'type' => $type,
				'param_name' => $param_name,
				'heading' => isset( $param['title'] ) ? $param['title'] : '',
				'description' => isset( $param['description'] ) ? $param['description'] : '',
				'std' => isset( $param['std'] ) ? $param['std'] : '',
				'holder' => isset( $param['holder'] ) ? $param['holder'] : '',
				'admin_label' => isset( $param['admin_label'] ) ? $param['admin_label'] : FALSE,
				'settings' => isset( $param['settings'] ) ? $param['settings'] : NULL,
				'params' => ( isset( $param['params'] ) AND $param['type'] === 'design_options' ) ? $param['params'] : NULL,
				'edit_field_class' => ! empty( $param['classes'] ) ? $param['classes'] : NULL,
			);

			// Add option CSS classes based on "cols" param
			if ( isset( $param['cols'] ) ) {
				$_cols_k = 12 / intval( $param['cols'] );

				if ( empty( $vc_param['edit_field_class'] ) ) {
					$vc_param['edit_field_class'] = 'vc_col-sm-' . $_cols_k;
				} else {
					$vc_param['edit_field_class'] .= ' vc_col-sm-' . $_cols_k;
				}
			}

			if ( ! empty( $param['group'] ) ) {
				$vc_param['group'] = $param['group'];
			}

			if ( $vc_param['type'] == 'attach_image' AND isset( $param['is_multiple'] ) AND $param['is_multiple'] ) {
				$vc_param['type'] = 'attach_images';
			}

			if ( in_array(
					$vc_param['type'], array(
						'dropdown',
						'us_autocomplete',
					)
				) AND isset( $param['options'] ) ) {
				$vc_param['value'] = array();
				foreach ( $param['options'] as $option_val => $option_name ) {
					$vc_param['value'][ $option_name . ' ' ] = $option_val . '';
				}
			}

			// US Checkboxes
			if ( $vc_param['type'] == 'us_checkboxes' ) {
				$vc_param['options'] = isset( $param['options'] ) ? ( array ) $param['options'] : NULL;
			}

			// VC Checkboxes
			if ( $vc_param['type'] == 'checkbox' ) {
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
						$group_vc_param = us_vc_param( $group_param_name, $group_param );
						if ( $group_vc_param != NULL ) {
							$vc_param['params'][] = $group_vc_param;
						}
					}
				}
				if ( isset( $vc_param['std'] ) AND is_array( $vc_param['std'] ) ) {
					$vc_param['std'] = rawurlencode( wp_json_encode( $vc_param['std'] ) );
				}
			}

			// US Color additional params
			if ( $param['type'] == 'color' ) {
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

			return $vc_param;
		}

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
			OR vc_is_page_editable()
			OR $is_edit_vc_roles
		) {
			foreach ( $shortcodes_config['theme_elements'] as $elm_name ) {
				$shortcode = 'us_' . $elm_name;
				$elm = us_config( 'elements/' . $elm_name );

				$vc_elm = array(
					'name' => isset( $elm['title'] ) ? $elm['title'] : $shortcode,
					'base' => $shortcode,
					'description' => isset( $elm['description'] ) ? $elm['description'] : '',
					'class' => 'elm-' . $shortcode,
					'category' => isset( $elm['category'] ) ? $elm['category'] : us_translate( 'Content', 'js_composer' ),
					'icon' => isset( $elm['icon'] ) ? $elm['icon'] : '',
					'weight' => 370, // all elements go after "Text Block" element
					'admin_enqueue_js' => isset( $elm['admin_enqueue_js'] ) ? $elm['admin_enqueue_js'] : NULL,
					'js_view' => isset( $elm['js_view'] ) ? $elm['js_view'] : NULL,
					'as_parent' => isset( $elm['as_parent'] ) ? $elm['as_parent'] : NULL,
					'show_settings_on_create' => isset( $elm['show_settings_on_create'] ) ? $elm['show_settings_on_create'] : NULL,
					'params' => array(),
				);

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
						$vc_param = us_vc_param( $param_name, $param );
						if ( $vc_param != NULL ) {
							$vc_elm['params'][] = $vc_param;
						}
					}
					unset( $param );
				}

				if ( isset( $elm['deprecated_params'] ) AND is_array( $elm['deprecated_params'] ) ) {
					foreach ( $elm['deprecated_params'] as $param_name ) {
						$vc_elm['params'][] = array(
							'type' => 'textfield',
							'param_name' => $param_name,
							'std' => '',
							'edit_field_class' => 'hidden',
						);
					}
				}

				vc_map( $vc_elm );

				// This is required for the access edit page on the vc-roles page
				if ( $is_edit_vc_roles ) {
					vc_lean_map( 'us_' . $elm_name, function() use( $vc_elm ) {
						return $vc_elm;
					} );
				}

			}
		}

		// Include custom map files based on shortcodes name. Only for vc_ shortcodes
		global $us_template_directory;

		foreach ( $shortcodes_config['modified'] as $shortcode => $config ) {
			if ( file_exists( $us_template_directory . '/plugins-support/js_composer/map/' . $shortcode . '.php' ) ) {
				require $us_template_directory . '/plugins-support/js_composer/map/' . $shortcode . '.php';
			} elseif ( file_exists( US_CORE_DIR . 'plugins-support/js_composer/map/' . $shortcode . '.php' ) ) {
				require US_CORE_DIR . 'plugins-support/js_composer/map/' . $shortcode . '.php';
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

	if ( us_get_option( 'disable_extra_vc', 1 ) ) {

		// Removing the elements that are not supported at the moment by the theme
		if ( is_admin() ) {
			foreach ( $shortcodes_config['disabled'] as $shortcode ) {
				vc_remove_element( $shortcode );
			}
		} else {
			add_action( 'template_redirect', 'us_vc_disable_extra_sc', 100 );
		}

	}
}

if ( ! function_exists( 'us_vc_shortcodes_custom_css_class' ) ) {
	add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'us_vc_shortcodes_custom_css_class', 10, 3 );
	/**
	 * Adding a unique class for custom styles
	 *
	 * @param string $class
	 * @param string $shortcode_base
	 * @param array $atts
	 *
	 * @return string
	 */
	function us_vc_shortcodes_custom_css_class( $class, $shortcode_base, $atts = array() ) {
		$shortcodes_config = us_config( 'shortcodes', array(), TRUE );
		$shortcodes_with_design_options = $shortcodes_config['added_design_options'];
		if ( in_array( $shortcode_base, $shortcodes_with_design_options )
			AND function_exists( 'us_get_design_css_class' )
			AND ( ! empty( $atts['css'] ) ) ) {
			$class .= ' ' . us_get_design_css_class( $atts['css'] );
		}

		if ( ! empty( $atts['css'] ) AND us_design_options_has_property( $atts['css'], 'border-radius' ) ) {
			$class .= ' has_border_radius';
		}

		return $class;
	}
}

add_action( 'current_screen', 'us_disable_post_type_specific_elements' );
function us_disable_post_type_specific_elements() {
	if ( function_exists( 'get_current_screen' ) ) {
		global $pagenow;
		// Receive data only on the edit page or create a record
		if ( wp_doing_ajax() OR ! in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			return;
		}

		$screen = get_current_screen();
		$shortcodes_config = us_config( 'shortcodes', array(), TRUE );

		foreach ( $shortcodes_config['theme_elements'] as $elm_name ) {
			$shortcode = 'us_' . $elm_name;
			$elm = us_config( 'elements/' . $elm_name );

			if ( isset( $elm['shortcode_post_type'] ) ) {
				if ( ! empty( $screen->post_type ) AND ! in_array( $screen->post_type, $elm['shortcode_post_type'] ) ) {
					vc_remove_element( $shortcode );
				}
			}
		}
	}
}

/**
 * Disable VC frontend editing for post types defined in the array $post_types
 */
if ( ! function_exists( 'us_disable_frontend_for_post_types' ) ) {
	function us_disable_frontend_for_post_types() {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			$post_types = array( 'us_page_block', 'us_content_template' );
			if ( in_array( $screen->post_type, $post_types ) ) {
				vc_disable_frontend();
			}
		}
	}
}

function us_vc_disable_extra_sc() {
	$disabled_shortcodes = us_config( 'shortcodes.disabled', array() );

	foreach ( $disabled_shortcodes as $shortcode ) {
		remove_shortcode( $shortcode );
	}
}

// Disable redirect to VC welcome page
remove_action( 'init', 'vc_page_welcome_redirect' );

add_action( 'after_setup_theme', 'us_vc_init_vendor_woocommerce', 99 );
function us_vc_init_vendor_woocommerce() {
	remove_action( 'wp_enqueue_scripts', 'vc_woocommerce_add_to_cart_script' );
}

if ( ! function_exists( 'us_get_post_ids_for_autocomplete' ) ) {
	/**
	 * Get a list of records for an us_autocomplete WPB
	 *
	 * @param integer $limit The limit
	 * @return array
	 */
	function us_get_post_ids_for_autocomplete( $limit = 50 ) {
		if ( ! is_admin() ) {
			return array();
		} elseif ( ! check_ajax_referer( 'us_ajax_get_post_ids_for_autocomplete', '_nonce', FALSE ) ) {
			return array();
		}

		// US Autocomplete options
		$search = isset( $_GET['search'] ) ? $_GET['search'] : '';
		$offset = (int) isset( $_GET['offset'] ) ? intval( $_GET['offset'] ) : 0;

		// Remove media from post_type
		$post_type = array_keys( us_grid_available_post_types() );
		if ( ( $index = array_search( 'attachment', $post_type ) ) !== FALSE ) {
			unset( $post_type[ $index ] );
		}

		$query_args = array(
			'post_type' => $post_type,
			'posts_per_page' => $limit,
			'post_status' => 'any',
			'suppress_filters' => 0,
			'offset' => $offset,
		);

		// Get selected params
		if ( strpos( $search, 'params:' ) === 0 ) {
			$params = explode( ',', substr( $search, strlen( 'params:' ) ) );
			$query_args['post__in'] = array_map( 'intval', $params );
			$search = '';
		}

		if ( ! empty( $search ) ) {
			$query_args['s'] = $search;
		}

		$results = array();
		foreach ( get_posts( $query_args ) as $post ) {
			$results[ $post->ID ] = strlen( $post->post_title ) > 0
				? esc_attr( $post->post_title )
				: us_translate( '(no title)' );

			if ( $post_type = get_post_type_object( $post->post_type ) ) {
				$results[ $post->ID ] .= sprintf( ' <i>%s</i>', $post_type->labels->singular_name );
			}
		}

		return $results;
	}

	/**
	 * AJAX Request Handler
	 */
	function us_ajax_get_post_ids_for_autocomplete() {
		if ( ! check_ajax_referer( 'us_ajax_get_post_ids_for_autocomplete', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
			wp_die();
		}
		wp_send_json_success( array( 'items' => us_get_post_ids_for_autocomplete() ) );
		wp_die();
	}
	add_action( 'wp_ajax_us_get_post_ids_for_autocomplete', 'us_ajax_get_post_ids_for_autocomplete', 1 );
}

if ( ! function_exists( 'us_get_term_ids_for_autocomplete' ) ) {
	/**
	 * Get a list of records for an a us_autocomplete WPB
	 *
	 * @param integer $limit The limit
	 * @return array
	 */
	function us_get_term_ids_for_autocomplete( $limit = 50 ) {
		if ( ! is_admin() ) {
			return array();
		} elseif ( ! check_ajax_referer( 'us_ajax_get_term_ids_for_autocomplete', '_nonce', FALSE ) ) {
			return array();
		}

		// US Autocomplete options
		$search = isset( $_GET['search'] ) ? $_GET['search'] : '';
		$offset = isset( $_GET['offset'] ) ? intval( $_GET['offset'] ) : 0;

		$taxonomies = us_get_taxonomies( TRUE, FALSE );

		$query_args = array(
			'taxonomy' => array_keys( $taxonomies ),
			'hide_empty' => FALSE,
			'number' => $limit,
			'offset' => $offset,
		);

		// Get selected params
		if ( strpos( $search, 'params:' ) === 0 ) {
			$params = explode( ',', substr( $search, strlen( 'params:' ) ) );
			$query_args['include'] = array_map( 'intval', $params );
			$search = '';
		}

		if ( ! empty( $search ) ) {
			$query_args['name__like'] = $search;
		}

		$results = array();
		foreach ( get_terms( $query_args ) as $term ) {
			$results[ $term->term_id ] = strlen( $term->name ) > 0
				? esc_attr( $term->name )
				: us_translate( '(no title)' );

			if ( ! empty( $taxonomies [ $term->taxonomy ] ) ) {
				$results[ $term->term_id ] .= sprintf( ' <i>%s</i>', $taxonomies [ $term->taxonomy ] );
			}
		}

		return $results;
	}

	/**
	 * AJAX Request Handler
	 */
	function us_ajax_get_term_ids_for_autocomplete() {
		if ( ! check_ajax_referer( 'us_ajax_get_term_ids_for_autocomplete', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
			wp_die();
		}
		wp_send_json_success( array( 'items' => us_get_term_ids_for_autocomplete() ) );
		wp_die();
	}
	add_action( 'wp_ajax_us_get_term_ids_for_autocomplete', 'us_ajax_get_term_ids_for_autocomplete', 1 );
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
add_action( 'admin_notices', 'us_hide_js_composer_activation_notice', 100 );
function us_hide_js_composer_activation_notice() {
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
$list = array(
	'page',
	'us_portfolio',
	'us_page_block',
	'us_content_template',
);
vc_set_default_editor_post_types( $list );

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

add_action( 'current_screen', 'us_header_vc_check_post_type_validation_fix' );
function us_header_vc_check_post_type_validation_fix( $current_screen ) {
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
			'ajax_query_args' => array(
				'_nonce' => us_arr_path( $settings, 'settings._nonce', '' ),
				'action' => us_arr_path( $settings, 'settings.action', '' ),
				'slug' => us_arr_path( $settings, 'settings.slug', '' ),
			),
			'classes' => 'wpb_vc_param_value',
			'multiple' => (bool) us_arr_path( $settings, 'settings.multiple', FALSE ),
			'sortable' => (bool) us_arr_path( $settings, 'settings.sortable', FALSE ),
			'options' => array_map( 'trim', array_flip( $settings['value'] ) ),
			'value' => $values,
		) );

		return '<div class="type_autocomplete" data-name="'. esc_attr( $settings['param_name'] ) .'">'. $output .'</div>';
	}
}

if ( wp_doing_ajax() AND ! function_exists( 'us_get_taxonomies_autocomplete' ) ) {
	add_action( 'wp_ajax_us_get_taxonomies_autocomplete', 'us_get_taxonomies_autocomplete', 1 );
	/**
	 * Request AJAX handler for us_get_taxonomies_autocomplete
	 * @return string
	 */
	function us_get_taxonomies_autocomplete() {
		if ( ! check_ajax_referer( 'us_ajax_get_taxonomies_autocomplete', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
			wp_die();
		}

		// Query params
		if ( ! $slug = trim( $_GET['slug'] ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'Taxonomy cannot be empty' ),
				)
			);
			wp_die();
		}
		$offset = intval( $_GET['offset'] );
		$search_text = trim( $_GET['search'] );

		$response = array(
			'items' => array(),
		);

		// The method for obtaining data should be able to receive data in batches,
		// search the search field and load the list on the search field if it contains a separator `params:name,name2,name3`
		$response['items'] = us_get_terms_by_slug( $slug, $offset, 15, $search_text );

		wp_send_json_success( $response );
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

if ( ! function_exists( 'us_vc_field_checkboxes' ) ) {
	vc_add_shortcode_param( 'us_checkboxes', 'us_vc_field_checkboxes', US_CORE_URI . '/plugins-support/js_composer/js/us_checkboxes.js' );
	/**
	 * US Checkboxes
	 *
	 * @param array $settings The settings
	 * @param string $value The value
	 * @return string
	 */
	function us_vc_field_checkboxes( $settings, $value ) {
		$output = '<div class="us_checkboxes">';
		if ( isset( $settings['options'] ) AND is_array( $settings['options'] ) ) {
			$values = explode( ',', $value );
			$atts = array(
				'class' => 'wpb_vc_param_value us_checkboxes_value',
				'name' => esc_attr( $settings['param_name'] ),
				'type' => 'hidden',
				'value' => esc_attr( $value ),
			);
			$output .= '<input '. us_implode_atts( $atts ) .'>';
			foreach ( $settings['options'] as $value => $name ) {
				$output .= '<label class="vc_checkbox-label">';
				$atts = array(
					'class' => 'us_checkboxes_checkbox taxonomy_category checkbox',
					'id' => esc_attr( $value ),
					'name' => esc_attr( $settings['param_name'] ),
					'type' => 'checkbox',
					'value' => esc_attr( $value ),
				);
				if ( in_array( $value, $values ) ) {
					$atts['checked'] = 'checked';
				}
				$output .= '<input '. us_implode_atts( $atts ) .'>';
				$output .= esc_html( $name );
				$output .= '</label>';
			}
		}
		$output .= '</div>';
		return $output;
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

		if ( $group = us_arr_path( $settings, 'group', '' ) ) {
			$group = preg_replace( '/\s+/u', '-', strtolower( $group ) );
		}

		$output = '';
		foreach ( us_arr_path( $settings, 'value', array() ) as $name => $param ) {

			// Preview file check
			$preview_elm = '';
			if ( $preview_path = us_arr_path( $settings, 'preview_path', FALSE ) AND ! empty( $param ) ) {
				$preview_path = sprintf( $preview_path, $param );
				$preview_full_path = realpath( US_CORE_DIR . sprintf( $preview_path, $param ) );
				if ( file_exists( $preview_full_path ) ) {
					if ( 'svg' == pathinfo( $preview_full_path, PATHINFO_EXTENSION ) ) {
						ob_start();
						require( $preview_full_path );
						$preview = ob_get_clean();
					} else {
						$preview_url = US_CORE_URI . '/' . ltrim( $preview_path, '/' );
						$preview = '<img src="' . esc_url( $preview_url ) . '" alt="' . esc_attr( $name ) . '">';
					}

					$preview_elm = '<span class="usof-imgradio-item-image">' . $preview . '</span>';
				}
				unset( $preview, $preview_path, $preview_full_path, $preview_url );
			}

			// Input atts
			$field_params = array(
				'class' => 'usof-imgradio-item-image',
				'id' => sprintf( 'us-%s-%s-%s', $group, $param_name, $param ),
				'name' => esc_attr( '_' . $param_name . '_' ),
				'style' => 'display: none',
				'type' => 'radio',
				'value' => esc_attr( $param ),
			);

			if ( $param == $value ) {
				$field_params['checked'] = 'checked';
			}

			// Generate output html code
			$output .= '<div class="usof-imgradio-item ' . us_arr_path( $settings, 'classes', '' ) . '">';
			$output .= '<input ' . us_implode_atts( $field_params ) . '">';
			$output .= '<label for="' . esc_attr( $field_params['id'] ) . '" title="' . esc_attr( $name ) . '">';
			$output .= $preview_elm;
			$output .= '<span class="usof-imgradio-item-label">' . esc_html( $name ) . '</span>';
			$output .= '</label>';
			$output .= '</div>';

			$hidden_field = array(
				'type' => 'hidden',
				'name' => esc_attr( $param_name ),
				'class' => 'wpb_vc_param_value',
				'value' => esc_attr( $value ),
			);

			$output .= '<input ' . us_implode_atts( $hidden_field ) . '>';
		}

		return '<div class="usof-imgradio">' . $output . '</div>';
	}
}

// Add parameter for Grid Layout selection
if ( ! function_exists( 'us_vc_field_grid_layout' ) ) {
	vc_add_shortcode_param( 'us_grid_layout', 'us_vc_field_grid_layout', US_CORE_URI . '/plugins-support/js_composer/js/us_grid_layout.js' );

	function us_vc_field_grid_layout( $settings, $value ) {
		$templates_config = us_config( 'grid-templates', array(), TRUE );
		$custom_layouts = array_flip( us_get_posts_titles_for( 'us_grid_layout' ) );

		ob_start();
		?>
		<div class="us-grid-layout">
			<select name="<?php echo esc_attr( $settings['param_name'] ); ?>"
					class="wpb_vc_param_value wpb-input wpb-select <?php echo esc_attr( $settings['param_name'] ) ?> dropdown">
				<optgroup label="<?php _e( 'Grid Layouts', 'us' ); ?>">
					<?php foreach ( $custom_layouts as $title => $id ) { ?>
						<option value="<?php echo $id ?>"<?php if ( $value == $id ) {
							echo ' selected="selected"';
						} ?>
								data-edit-url="<?php echo admin_url( '/post.php?post=' . $id . '&action=edit' ); ?>"><?php echo $title; ?></option>
					<?php }
					$current_tmpl_group = '';
					foreach ( $templates_config

					as $template_name => $template ) {
					if ( ! empty( $template['group'] ) AND $current_tmpl_group != $template['group'] ) {
					$current_tmpl_group = $template['group'];
					?>
				</optgroup>
				<optgroup label="<?php echo $template['group']; ?>">
					<?php
					}
					?>
					<option value="<?php echo $template_name ?>"<?php if ( $value == $template_name ) {
						echo ' selected="selected"';
					} ?>><?php echo $template['title']; ?></option>
					<?php
					}
					?>
				</optgroup>
			</select>
			<div class="us-grid-layout-desc-edit">
				<?php echo sprintf( _x( '%sEdit selected%s or %screate a new one%s.', 'Grid Layout', 'us' ), '<a href="#" class="edit-link" target="_blank" rel="noopener">', '</a>', '<a href="' . admin_url() . 'post-new.php?post_type=us_grid_layout" target="_blank" rel="noopener">', '</a>' ); ?>
			</div>
			<div class="us-grid-layout-desc-add">
				<?php echo '<a href="' . admin_url() . 'post-new.php?post_type=us_grid_layout" target="_blank" rel="noopener">' . __( 'Add Grid Layout', 'us' ) . '</a>. ' . sprintf( __( 'See %s', 'us' ), '<a href="http://impreza.us-themes.com/grid-templates/" target="_blank" rel="noopener">' . __( 'Grid Layout Templates', 'us' ) . '</a>.' ); ?>
			</div>
		</div>
		<?php
		$result = ob_get_clean();

		return $result;
	}
}

// Add parameter for grouped Selection
if ( ! function_exists( 'us_vc_field_grouped_select' ) ) {
	vc_add_shortcode_param( 'us_grouped_select', 'us_vc_field_grouped_select' );

	function us_vc_field_grouped_select( $settings, $value ) {
		ob_start();
		?>
		<div class="us_grouped_select">
			<select name="<?= esc_attr( $settings['param_name'] ) ?>"
					class="wpb_vc_param_value wpb-input wpb-select <?php echo esc_attr( $settings['param_name'] ) ?> dropdown">
				<?php
				foreach ( $settings['settings'] as $group ) {
					if ( ! empty( $group['options'] ) ) {
						if ( ! empty( $group['label'] ) ) {
							?>
							<optgroup label="<?= esc_attr( $group['label'] ) ?>">
							<?php
						}
						foreach ( $group['options'] as $option_value => $option_label ) {
							?>
							<option <?= $option_value == $value ? 'selected="selected"' : '' ?>
								class="<?= $option_value ?>"
								value="<?= $option_value ?>"><?= $option_label ?></option>
							<?php
						}
						if ( ! empty( $group['label'] ) ) {
							?>
							</optgroup>
							<?php
						}
					}
				}
				?>
			</select>
			<span class="vc_description vc_clearfix"></span>
		</div>
		<?php
		return ob_get_clean();
	}
}

// Add script to fill inputs with examples from description
add_action( 'admin_enqueue_scripts', 'us_input_examples' );
function us_input_examples() {
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
	add_action( 'wp_ajax_us_import_shortcode_data', 'us_ajax_import_shortcode_data' );
	if ( ! function_exists( 'wp_ajax_us_import_shortcode_data' ) ) {
		function us_ajax_import_shortcode_data() {
			if ( ! check_ajax_referer( 'us_ajax_import_shortcode_data', '_nonce', FALSE ) ) {
				wp_send_json_error(
					array(
						'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
					)
				);
				wp_die();
			}

			$response = 'blog_1';
			$post_content = ( isset( $_POST['post_content'] ) OR ! empty( $_POST['post_content'] ) )
				? $_POST['post_content']
				: NULL;

			if ( $post_content ) {

				$post_content_data = explode( '|', $post_content );
				if ( count( $post_content_data ) != 2 ) {
					wp_send_json_error(
						array(
							'message' => us_translate( 'Invalid data provided.' ),
						)
					);
					wp_die();
				}

				$post_content = base64_decode( $post_content_data[1] );
				if ( json_decode( $post_content ) === NULL ) {
					$post_content = NULL;
				}
			}

			if ( $post_content AND isset( $post_content_data[0] ) ) {
				global $wpdb;
				$_post_type = ( isset( $_POST['post_type'] ) OR ! empty( $_POST['post_type'] ) )
					? $_POST['post_type']
					: 'us_grid_layout';

				// Preparing a query to find a duplicate us_grid_layout
				$sql = $wpdb->prepare( "SELECT id FROM $wpdb->posts WHERE post_type = %s AND TRIM(`post_content`) = %s LIMIT 1 ", $_post_type, $post_content );

				if ( $post_id = $wpdb->get_var( $sql ) ) {
					// If the record exists, we get the identifier
					$response = $post_id;
				} else {
					$post_id = wp_insert_post(
						array(
							'post_type' => $_post_type,
							'post_content' => $post_content,
							'post_author' => get_current_user_id(),
							'post_title' => trim( $post_content_data[0] ),
							'post_status' => 'publish',
							'comment_status' => 'closed',
							'ping_status' => 'closed',
						)
					);
					if ( $post_id > 0 ) {
						$response = $post_id;
					}
				}
			}

			wp_send_json_success( $response );
		}
	}
}

if ( ! function_exists( 'us_vc_frontend_editor_load_shortcode_ajax_output' ) ) {
	add_action( 'vc_frontend_editor_load_shortcode_ajax_output', 'us_vc_frontend_editor_load_shortcode_ajax_output' );
	/**
	 * Inject custom css for VC FrontEnd Editor
	 *
	 * @param string $output
	 * @return string
	 */
	function us_vc_frontend_editor_load_shortcode_ajax_output( $output ) {
		if ( preg_match( '/(data-model-id="(\w+)")(.*?)(us_custom_(\w+))/', $output, $matches ) ) {
			$jsoncss_collection = array();
			foreach ( us_arr_path( $_POST, 'shortcodes', array() ) as $data ) {
				if (
					us_arr_path( $data, 'id' ) == $matches[2]
					AND preg_match( '/\s?css="(.*?)"/i', stripslashes( $data['string'] ), $jsoncss )
				) {
					$jsoncss = rawurldecode( $jsoncss[1] );
					if ( $jsoncss AND $jsoncss = json_decode( $jsoncss, TRUE ) ) {
						foreach ( array( 'default', 'tablets', 'mobiles' ) as $device_type ) {
							if ( $css_options = us_arr_path( $jsoncss, $device_type, FALSE ) ) {
								$jsoncss_collection[ $device_type ][ $matches[4] ] = $css_options;
							}
						}
					}
				}
			}
		}
		if ( ! empty( $jsoncss_collection ) AND $styles = us_jsoncss_compile( $jsoncss_collection ) ) {
			$output = str_replace( 'data-type="files">', 'data-type="files"><style>' . $styles . '</style>', $output );
		}

		return $output;
	}
}

// Add design options CSS for shortcodes in custom pages and page blocks
function us_add_page_shortcodes_custom_css( $id ) {
	if ( class_exists( 'VC_Base' ) ) {
		// Output css styles
		$us_vc = new Us_Vc_Base;
		$us_vc->addPageCustomCss( $id );
		$us_vc->addShortcodesCustomCss( $id );
	}
}

if ( ! function_exists( 'us_get_post_metadata_for_custom_css' ) ) {
	/**
	 * Filter for preventing double output of Page's Custom CSS
	 *
	 * @param null $value
	 * @param int $object_id
	 * @param string $meta_key
	 *
	 * @return mixed
	 */
	function us_get_post_metadata_for_custom_css( $value, $object_id, $meta_key, $single ) {
		// Returning unchanged value for all meta except _wpb_post_custom_css
		if ( $meta_key !== '_wpb_post_custom_css' ) {
			return $value;
		}

		global $us_page_custom_css_ids, $wpdb;
		if ( ! isset( $us_page_custom_css_ids ) ) {
			$us_page_custom_css_ids = array();
		}
		// Checking if we have already received Custom CSS for this Page by it's ID...
		// and returning empty value in such case
		if ( array_key_exists( $object_id, $us_page_custom_css_ids ) ) {
			if ( $single ) {
				return '';
			} else {
				return array( '' );
			}
		}
		$meta_cache = wp_cache_get( $object_id, 'post_meta' );
		if ( ! empty( $meta_cache[ $meta_key ] ) ) {
			$value = $meta_cache[ $meta_key ];
		} else {
			// Taking the value of meta directly from database to prevent looping
			$value = $wpdb->get_col(
				"
				SELECT meta_value
				FROM {$wpdb->postmeta}
				WHERE
					post_id = {$object_id}
					AND meta_key = '{$meta_key}'
				LIMIT 1;
			"
			);
		}
		$value = ! empty( $value[0] )
			? $value[0]
			: '';
		// Checking if we have already received same Custom CSS for any other page by hash based on CSS value...
		// and returning empty value in such case
		$hash = hash( 'crc32', $value );
		if ( in_array( $hash, $us_page_custom_css_ids ) ) {
			if ( $single ) {
				return '';
			} else {
				return array( '' );
			}
		}
		// Adding Page's ID and CSS value hash to our stash
		$us_page_custom_css_ids[ $object_id ] = $hash;
		if ( $single ) {
			return $value;
		} else {
			return array( $value );
		}

	}

	add_filter( 'get_post_metadata', 'us_get_post_metadata_for_custom_css', 1001, 4 );
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
				$output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . __( $param['heading'], 'js_composer' ) . '</label>: ' . $value . '</span>';
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

// Add image preview for Person shortcode
if ( ! class_exists( 'WPBakeryShortCode_us_person' ) ) {
	class WPBakeryShortCode_us_person extends WPBakeryShortCode {
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
					$logo_html .= '<img width="150" height="150" class="attachment-thumbnail ' . $element_icon . ' vc_element-icon" data-name="' . $param_name . '" alt="' . $param_name . '" style="display: none;" />';
				}
				$logo_html .= '<span class="no_image_image vc_element-icon ' . $element_icon . ( $img AND ! empty( $img['p_img_large'][0] ) ? ' image-exists' : '' ) . '" />';
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
			if ( ! empty( $param['admin_label'] ) AND $param['admin_label'] === TRUE ) {
				$output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . __( $param['heading'], 'js_composer' ) . '</label>: ' . $value . '</span>';
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

	// These types shoudn't be replaced to posts
	$grid_available_post_types = array(
		'attachment',
		'related',
		'current_query',
		'taxonomy_terms',
		'current_child_terms',
		'product_upsells',
		'product_crosssell',
	);
	foreach ( array_keys( us_grid_available_post_types() ) as $post_type ) {
		if ( wp_count_posts( $post_type )->publish ) {
			$grid_available_post_types[] = $post_type;
		}
	}
	$data = array(
		'placeholder' => us_get_img_placeholder( 'full', TRUE ),
		'grid_post_types' => $grid_available_post_types,
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

/**
 * Link "fallback" file for correct work of deprecated shortcodes attributes.
 * This allows to avoid content migration after updates.
 */
require US_CORE_DIR . 'plugins-support/js_composer/fallback.php';
