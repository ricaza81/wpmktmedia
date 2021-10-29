<?php

class us_migration_4_6 extends US_Migration_Translator {

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		// Default Sidebar options
		if ( isset( $options['page_sidebar_id'] ) ) {
			$options['sidebar_id'] = $options['page_sidebar_id'];
			unset( $options['page_sidebar_id'] );
			$changed = TRUE;
		}
		if ( isset( $options['page_sidebar'] ) AND in_array( $options['page_sidebar'], array( 'left', 'right' ) ) ) {
			$options['sidebar'] = 1;
			$options['sidebar_pos'] = $options['page_sidebar'];
			unset( $options['page_sidebar'] );
			$changed = TRUE;
		}
		// Portfolio Sidebar
		if ( isset( $options['portfolio_sidebar'] ) ) {
			if ( $options['portfolio_sidebar'] == 'none' ) {
				$options['portfolio_sidebar'] = 0;
				$changed = TRUE;
			} elseif ( in_array( $options['portfolio_sidebar'], array( 'left', 'right' ) ) ) {
				$options['portfolio_sidebar_pos'] = $options['portfolio_sidebar'];
				$options['portfolio_sidebar'] = 1;
				$changed = TRUE;
			}

		}
		// Portfolio Sided Nav
		if ( isset( $options['portfolio_sided_nav'] ) AND $options['portfolio_sided_nav'] == 1 ) {
			$options['portfolio_nav'] = 1;
			$options['portfolio_nav_invert'] = 1;
			$changed = TRUE;
		}
		if ( isset( $options['portfolio_prevnext_category'] ) AND $options['portfolio_prevnext_category'] == 1 ) {
			$options['portfolio_nav_category'] = 1;
			$changed = TRUE;
		}
		// Post Sidebar
		if ( isset( $options['post_sidebar'] ) ) {
			if ( $options['post_sidebar'] == 'none' ) {
				$options['post_sidebar'] = 0;
				$changed = TRUE;
			} elseif ( in_array( $options['post_sidebar'], array( 'left', 'right' ) ) ) {
				$options['post_sidebar_pos'] = $options['post_sidebar'];
				$options['post_sidebar'] = 1;
				$changed = TRUE;
			}

		}
		// Blog Sidebar
		if ( isset( $options['blog_sidebar'] ) ) {
			if ( $options['blog_sidebar'] == 'none' ) {
				$options['blog_sidebar'] = 0;
				$changed = TRUE;
			} elseif ( in_array( $options['blog_sidebar'], array( 'left', 'right' ) ) ) {
				$options['blog_sidebar_pos'] = $options['blog_sidebar'];
				$options['blog_sidebar'] = 1;
				$changed = TRUE;
			}

		}
		// Archive Sidebar
		if ( isset( $options['archive_sidebar'] ) ) {
			if ( $options['archive_sidebar'] == 'none' ) {
				$options['archive_sidebar'] = 0;
				$changed = TRUE;
			} elseif ( in_array( $options['archive_sidebar'], array( 'left', 'right' ) ) ) {
				$options['archive_sidebar_pos'] = $options['archive_sidebar'];
				$options['archive_sidebar'] = 1;
				$changed = TRUE;
			}

		}
		// Search Sidebar
		if ( isset( $options['search_sidebar'] ) ) {
			if ( $options['search_sidebar'] == 'none' ) {
				$options['search_sidebar'] = 0;
				$changed = TRUE;
			} elseif ( in_array( $options['search_sidebar'], array( 'left', 'right' ) ) ) {
				$options['search_sidebar_pos'] = $options['search_sidebar'];
				$options['search_sidebar'] = 1;
				$changed = TRUE;
			}

		}
		// Shop Sidebar
		if ( isset( $options['shop_sidebar'] ) ) {
			if ( $options['shop_sidebar'] == 'none' ) {
				$options['shop_sidebar'] = 0;
				$changed = TRUE;
			} elseif ( in_array( $options['shop_sidebar'], array( 'left', 'right' ) ) ) {
				$options['shop_sidebar_pos'] = $options['shop_sidebar'];
				$options['shop_sidebar'] = 1;
				$changed = TRUE;
			}

		}
		// Product Sidebar
		if ( isset( $options['product_sidebar'] ) ) {
			if ( $options['product_sidebar'] == 'none' ) {
				$options['product_sidebar'] = 0;
				$changed = TRUE;
			} elseif ( in_array( $options['product_sidebar'], array( 'left', 'right' ) ) ) {
				$options['product_sidebar_pos'] = $options['product_sidebar'];
				$options['product_sidebar'] = 1;
				$changed = TRUE;
			}

		}

		// Menu
		$locations = get_theme_mod( 'nav_menu_locations' );
		if ( isset( $locations['us_main_menu'] ) ) {
			$menu = wp_get_nav_menu_object( $locations['us_main_menu'] );
			if ( $menu ) {
				// Setting menu for regular options
				$options['menu_source'] = $menu->slug;
				// Setting menu for HB
				if ( isset( $options['header']['data'] ) and is_array( $options['header']['data'] ) ) {
					foreach ( $options['header']['data'] as $name => $data ) {
						if ( substr( $name, 0, 4 ) == 'menu' ) {
							$options['header']['data'][$name]['source'] = $menu->slug;
						}
					}
				}
				$changed = TRUE;
			}
		}

		// Adding Header posts
		if ( isset( $options['header']['data'] ) ) {
			if ( ! post_type_exists( 'us_header' ) ) {
				register_post_type(
					'us_header', array(
						'labels' => array(
							'name' => 'Headers',
							'singular_name' => 'Header',
							'add_new' => 'Add New Header',
							'add_new_item' => 'Add New Header',
							'edit_item' => 'Edit Header',
						),
						'public' => TRUE,
						'show_in_menu' => FALSE,
						'exclude_from_search' => TRUE,
						'show_in_admin_bar' => FALSE,
						'publicly_queryable' => FALSE,
						'show_in_nav_menus' => FALSE,
						'capability_type' => array( 'us_footer', 'us_footers' ),
						'map_meta_cap' => TRUE,
						'supports' => FALSE,
						'has_archive' => FALSE,
						'register_meta_box_cb' => 'ushb_us_header_type_pages',
					)
				);
			}
			// If there are no header posts and we are actually performing migration
			if ( ! get_posts(
				array(
					'name' => 'site-header',
					'post_type' => 'us_header',
					'post_status' => 'publish',
					'numberposts' => 1,
				)
			) AND is_admin()
			) {
				$header_options = $options['header'];
				$header_options['tablets']['options']['breakpoint'] = 900;
				$header_options['mobiles']['options']['breakpoint'] = 600;

				foreach ( $header_options['data'] as $elm_key => $data ) {
					foreach ( $data as $data_key => $data_val ) {
						if ( is_array( $data_val ) ) {
							foreach ( $data_val as $data_subkey => $data_subval ) {
								if ( strpos( $data_subval, '"' ) !== FALSE ) {
									$header_options['data'][$elm_key][$data_key][$data_subkey] = str_replace( '"', '\"', $data_subval );
								}
							}
						} elseif ( strpos( $data_val, '"' ) !== FALSE ) {
							$header_options['data'][$elm_key][$data_key] = str_replace( '"', '\"', $data_val );
						}
					}
				}

				// Translating Headers if WPML is active
				if ( class_exists( 'SitePress' ) AND defined( 'ICL_LANGUAGE_CODE' ) ) {
					global $wpdb, $sitepress;

					$sitepress->switch_lang( $sitepress->get_default_language() );
					$translated_headers = array();
					$us_theme = wp_get_theme();
					if ( is_child_theme() ) {
						$us_theme = wp_get_theme( $us_theme->get( 'Template' ) );
					}
					$theme_name = $us_theme->get( 'Name' );

					$strings_query = "SELECT id, name FROM {$wpdb->prefix}icl_strings WHERE name LIKE '%[usof_options_{$theme_name}][header][data]%'";
					foreach ( $wpdb->get_results( $strings_query ) as $string ) {
						$param_string = str_replace( "[usof_options_{$theme_name}][header][data]", '', $string->name );
						if ( preg_match( '%\[([a-zA-Z0-9:_]+)\]\[([a-zA-Z0-9:_]+)\]([a-zA-Z0-9_]+)%', $param_string, $matches ) ) {
							$element_name = $matches[1];
							$param_name = $matches[2];
							$subparam_name = $matches[3];
						} elseif ( preg_match( '%\[([a-zA-Z0-9:_]+)\]([a-zA-Z0-9_]+)%', $param_string, $matches ) ) {
							$element_name = $matches[1];
							$param_name = $matches[2];
							$subparam_name = NULL;
						} else {
							continue;
						}
						$translations_query = "SELECT language, value FROM {$wpdb->prefix}icl_string_translations WHERE string_id = {$string->id} AND status != 0";
						foreach ( $wpdb->get_results( $translations_query ) as $translation ) {
							if ( ! isset( $translated_headers[$translation->language] ) ) {
								$translated_headers[$translation->language] = $header_options;
							}
							if ( $subparam_name !== NULL ) {
								$translated_headers[$translation->language]['data'][$element_name][$param_name][$subparam_name] = $translation->value;
							} else {
								$translated_headers[$translation->language]['data'][$element_name][$param_name] = $translation->value;
							}

						}
					}
				}

				if ( defined( 'JSON_UNESCAPED_UNICODE' ) ) {
					$post_content = json_encode( $header_options, JSON_UNESCAPED_UNICODE );
				} else {
					$post_content = json_encode( $header_options );
				}
				$post_content = str_replace( "\\n", "\\\\n", $post_content );

				$header_post_array = array(
					'post_type' => 'us_header',
					'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
					'post_name' => 'site-header',
					'post_title' => 'Site Header',
					'post_content' => $post_content,
					'post_status' => 'publish',
				);

				ob_start();
				$default_header_id = wp_insert_post( $header_post_array );
				ob_end_clean();

				$options['header_id'] = $default_header_id;
				$changed = TRUE;

				// Inserting translated Headers if any
				if ( class_exists( 'SitePress' ) AND defined( 'ICL_LANGUAGE_CODE' ) AND count( $translated_headers ) > 0 ) {
					$wpml_element_type = apply_filters( 'wpml_element_type', 'us_header' );
					$set_language_args = array(
						'element_id' => $default_header_id,
						'element_type' => $wpml_element_type,
						'language_code' => $sitepress->get_default_language(),
						'trid' => NULL,
					);
					do_action( 'wpml_set_element_language_details', $set_language_args );

					foreach ( $translated_headers as $lang => $translated_header_options ) {
						if ( defined( 'JSON_UNESCAPED_UNICODE' ) ) {
							$post_content = json_encode( $translated_header_options, JSON_UNESCAPED_UNICODE );
						} else {
							$post_content = json_encode( $translated_header_options );
						}
						$post_content = str_replace( "\\n", "\\\\n", $post_content );

						$translated_header_post_array = array(
							'post_type' => 'us_header',
							'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
							'post_name' => 'site-header-' . $lang,
							'post_title' => 'Site Header [' . $lang . ']',
							'post_content' => $post_content,
							'post_status' => 'publish',
						);

						ob_start();
						$translated_header_id = wp_insert_post( $translated_header_post_array );
						ob_end_clean();

						$get_language_args = array('element_id' => $default_header_id, 'element_type' => 'us_header' );
						$trid = apply_filters( 'wpml_element_trid', NULL, $default_header_id, $wpml_element_type );
						$source_lang_code = apply_filters( 'wpml_element_language_code', $sitepress->get_default_language(), $get_language_args );

						$set_language_args = array(
							'element_id' => $translated_header_id,
							'element_type' => $wpml_element_type,
							'trid' => $trid,
							'language_code' => $lang,
							'source_language_code' => $source_lang_code
						);

						do_action( 'wpml_set_element_language_details', $set_language_args );
					}
				}
			}
		}

		// Menu items
		$menu_items = array();
		foreach ( get_terms( array( 'taxonomy' => 'nav_menu', 'hide_empty' => TRUE ) ) as $menu_obj ) {
			$menu_items = array_merge(
				$menu_items,
				wp_get_nav_menu_items( $menu_obj->term_id, array( 'post_status' => 'any' ) )
			);
		}
		foreach ($menu_items as $menu_item) {
			if ( isset( $menu_item->classes ) AND is_array( $menu_item->classes ) ) {
				$item_settings = array();
				$item_classes_meta = get_post_meta( $menu_item->ID, '_menu_item_classes' );

				if ( in_array( 'columns_2', $menu_item->classes ) ) {
					$item_settings['columns'] = 2;
					if ( ( $key = array_search( 'columns_2', $item_classes_meta[0] ) ) !== FALSE ) {
						$item_classes_meta[0][$key] = '';
					}
				}
				if ( in_array( 'columns_3', $menu_item->classes ) ) {
					$item_settings['columns'] = 3;
					if ( ( $key = array_search( 'columns_3', $item_classes_meta[0] ) ) !== FALSE ) {
						$item_classes_meta[0][$key] = '';
					}
				}
				if ( in_array( 'columns_4', $menu_item->classes ) ) {
					$item_settings['columns'] = 4;
					if ( ( $key = array_search( 'columns_4', $item_classes_meta[0] ) ) !== FALSE ) {
						$item_classes_meta[0][$key] = '';
					}
				}
				if ( in_array( 'columns_5', $menu_item->classes ) ) {
					$item_settings['columns'] = 5;
					if ( ( $key = array_search( 'columns_5', $item_classes_meta[0] ) ) !== FALSE ) {
						$item_classes_meta[0][$key] = '';
					}
				}
				if ( in_array( 'columns_6', $menu_item->classes ) ) {
					$item_settings['columns'] = 6;
					if ( ( $key = array_search( 'columns_6', $item_classes_meta[0] ) ) !== FALSE ) {
						$item_classes_meta[0][$key] = '';
					}
				}

				if ( isset( $item_settings['columns'] ) ) {
					$item_settings['padding'] = '15';
					$item_settings['width'] = 'full';
					$item_settings['direction'] = 0;
				}

				if ( in_array( 'drop_right', $menu_item->classes ) OR in_array( 'drop_left', $menu_item->classes ) ) {
					$item_settings['direction'] = 1;
					$item_settings['columns'] = 1;
					$item_settings['padding'] = 0;

					if ( ( $key = array_search( 'drop_left', $item_classes_meta[0] ) ) !== FALSE ) {
						$item_classes_meta[0][$key] = '';
					}
					if ( ( $key = array_search( 'drop_right', $item_classes_meta[0] ) ) !== FALSE ) {
						$item_classes_meta[0][$key] = '';
					}
				}

				if ( count( $item_settings ) > 0 ) {
					$item_settings['bg_image_size'] = 'cover';
					$item_settings['bg_image_repeat'] = 'repeat';
					$item_settings['bg_image_position'] = 'top left';
					$item_settings['bg_image'] = NULL;
					$item_settings['color_bg'] = '';
					$item_settings['color_text'] = '';
					update_post_meta( $menu_item->ID, 'us_mega_menu_settings', $item_settings );
					update_post_meta( $menu_item->ID, '_menu_item_classes', $item_classes_meta[0] );
				}
			}
		}

		return $changed;
	}

	// Meta
	public function translate_meta( &$meta, $post_type ) {
		$changed = FALSE;
		$breakpoints_keys = (array) us_get_responsive_states( /* Only keys */TRUE );

		if ( ! empty( $meta['us_header_remove'][0] ) AND $meta['us_header_remove'][0] == 1 ) {
			$meta['us_header'][0] = 'hide';
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_header_pos'][0] ) OR ( ! empty( $meta['us_header_bg'][0] ) ) OR ( ! empty( $meta['us_header_sticky_pos'][0] ) ) ) {
			$meta['us_header'][0] = 'custom';
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_header_pos'][0] ) AND $meta['us_header_pos'][0] == 'sticky' ) {
			$meta['us_header_sticky_override'][0] = 1;
			$meta['us_header_sticky'][0] = $breakpoints_keys;
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_header_pos'][0] ) AND $meta['us_header_pos'][0] == 'static' ) {
			$meta['us_header_sticky_override'][0] = 1;
			$meta['us_header_sticky'][0] = array();
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_header_bg'][0] ) AND $meta['us_header_bg'][0] == 'transparent' ) {
			$meta['us_header_transparent_override'][0] = 1;
			$meta['us_header_transparent'][0] = $breakpoints_keys;
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_header_bg'][0] ) AND $meta['us_header_bg'][0] == 'solid' ) {
			$meta['us_header_transparent_override'][0] = 1;
			$meta['us_header_transparent'][0] = array();
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_sidebar'][0] ) AND $meta['us_sidebar'][0] == 'none' ) {
			$meta['us_sidebar'][0] = 'hide';
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_sidebar'][0] ) AND in_array( $meta['us_sidebar'][0], array( 'right', 'left' ) ) ) {
			$meta['us_sidebar_pos'][0] = $meta['us_sidebar'][0];
			$meta['us_sidebar'][0] = 'custom';
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_sidebar_id'][0] ) ) {
			$meta['us_sidebar'][0] = 'custom';
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_footer_remove'][0] ) AND $meta['us_footer_remove'][0] == 1 ) {
			$meta['us_footer'][0] = 'hide';
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_footer_id'][0] ) ) {
			$meta['us_footer'][0] = 'custom';
			$changed = TRUE;
		}

		if ( isset( $meta['us_header_remove'][0] ) ) {
			unset( $meta['us_header_remove'] );
			$changed = TRUE;
		}

		if ( isset( $meta['us_header_pos'][0] ) ) {
			unset( $meta['us_header_pos'] );
			$changed = TRUE;
		}

		if ( isset( $meta['us_header_bg'][0] ) ) {
			unset( $meta['us_header_bg'] );
			$changed = TRUE;
		}

		if ( isset( $meta['us_footer_remove'][0] ) ) {
			unset( $meta['us_footer_remove'] );
			$changed = TRUE;
		}

		return $changed;
	}

}
