<?php

class us_migration_6_1 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		$content = str_replace( '[cl-ib', '[cl_ib', $content );

		return $this->_translate_content( $content );
	}

	// Grid
	public function translate_us_grid( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['orderby'] ) ) {
			if ( $params['orderby'] == 'date_asc' ) {
				$params['orderby'] = 'date';
				$params['order_invert'] = '1';
				$changed = TRUE;
			} elseif ( $params['orderby'] == 'modified_asc' ) {
				$params['orderby'] = 'modified';
				$params['order_invert'] = '1';
				$changed = TRUE;
			} elseif ( $params['orderby'] == 'price_asc' ) {
				$params['orderby'] = 'price';
				$params['order_invert'] = '1';
				$changed = TRUE;
			} elseif ( $params['orderby'] == 'price_desc' ) {
				$params['orderby'] = 'price';
				$changed = TRUE;
			} elseif ( $params['orderby'] == 'menu_order' ) {
				$params['order_invert'] = '1';
				$changed = TRUE;
			}
		}

		return $changed;
	}

	// Carousel
	public function translate_us_carousel( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['orderby'] ) ) {
			if ( $params['orderby'] == 'date_asc' ) {
				$params['orderby'] = 'date';
				$params['order_invert'] = '1';
				$changed = TRUE;
			} elseif ( $params['orderby'] == 'modified_asc' ) {
				$params['orderby'] = 'modified';
				$params['order_invert'] = '1';
				$changed = TRUE;
			} elseif ( $params['orderby'] == 'price_asc' ) {
				$params['orderby'] = 'price';
				$params['order_invert'] = '1';
				$changed = TRUE;
			} elseif ( $params['orderby'] == 'price_desc' ) {
				$params['orderby'] = 'price';
				$changed = TRUE;
			} elseif ( $params['orderby'] == 'menu_order' ) {
				$params['order_invert'] = '1';
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_cl_ib( &$name, &$params, &$content ) {
		$name = 'us_ibanner';

		if ( ! isset( $params['textcolor'] ) ) {
			$params['textcolor'] = '#ffffff';
		}

		if ( isset( $params['desc_size'] ) ) {
			$params['desc_font_size'] = $params['desc_size'];
			unset( $params['desc_size'] );
		}

		return TRUE;
	}

	// Headers
	public function translate_header_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $name => $data ) {

			// Image element
			if ( substr( $name, 0, 5 ) == 'image' ) {
				if ( empty( $data['onclick'] ) OR $data['onclick'] != 'custom_link' ) {
					$settings['data'][ $name ]['onclick'] = 'custom_link';

					$settings_changed = TRUE;
				}
			}

			// Button element
			if ( substr( $name, 0, 3 ) == 'btn' ) {
				if ( empty( $data['link_type'] ) OR $data['link_type'] != 'custom' ) {
					$settings['data'][ $name ]['link_type'] = 'custom';

					$settings_changed = TRUE;
				}
			}
		}

		return $settings_changed;
	}

	// Theme Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		// Creating header if there was no any
		if ( ! get_posts(
			array(
				'post_type' => 'us_header',
				'post_status' => 'publish',
				'numberposts' => 1,
			)
		) AND ! empty( $options['header_layout'] ) ) {
			$header_settings = $this->get_header_settings( $options );

			if ( defined( 'JSON_UNESCAPED_UNICODE' ) ) {
				$post_content = json_encode( $header_settings, JSON_UNESCAPED_UNICODE );
			} else {
				$post_content = json_encode( $header_settings );
			}

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
			$options['header_archive_id'] = $default_header_id;
			$changed = TRUE;
		}

		$migration_transient = get_transient( 'us_migration_61_transient' );
		if ( $migration_transient == FALSE OR is_admin() ) {
			// Check Menu items for 'us_widget_area' item
			$menu_items = array();
			foreach( get_terms( array( 'taxonomy' => 'nav_menu', 'hide_empty' => TRUE ) ) as $menu_obj ) {
				$menu_items = array_merge(
					$menu_items, wp_get_nav_menu_items( $menu_obj->term_id, array( 'post_status' => 'any' ) )
				);
			}
			foreach( $menu_items as $menu_item ) {
				if ( $menu_item->object == 'us_widget_area' ) {
					$menu_sidebar_post = get_post( $menu_item->object_id );
					if ( ! empty( $menu_sidebar_post ) ) {

						// Check if we have already added a pageblock for this sidebar
						$page_block_name = 'us_page_block_for_' . $menu_sidebar_post->post_name;
						$menu_page_block = get_page_by_path( $page_block_name, OBJECT, 'us_page_block' );
						if ( empty( $menu_page_block ) ) {
							$page_block_content = '[vc_row][vc_column][vc_widget_sidebar sidebar_id="' . $menu_sidebar_post->post_name . '"][/vc_column][/vc_row]';
							$page_block_title = 'Widget Area: ' . $menu_sidebar_post->post_title;
							$page_block_post_data = array(
								'post_type' => 'us_page_block',
								'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
								'post_name' => $page_block_name,
								'post_title' => $page_block_title,
								'post_content' => $page_block_content,
								'post_status' => 'publish',
							);

							$page_block_post_id = wp_insert_post( $page_block_post_data );
						} else {
							$page_block_post_id = $menu_page_block->ID;
							$page_block_title = $menu_page_block->post_title;
						}

						if ( $page_block_post_id ) {
							$updated_menu_post_meta = array(
								'object_id' => $page_block_post_id,
								'object' => 'us_page_block',
								'url' => get_permalink( $page_block_post_id ),
								'remove_rows' => 1,
							);

							foreach( $updated_menu_post_meta as $meta_key => $meta_value ) {
								update_post_meta( $menu_item->ID, '_menu_item_' . $meta_key, $meta_value );
							}
						}

					}
				}
			}

			if ( is_admin() ) {
				delete_transient( 'us_migration_61_transient' );
			} else {
				set_transient( 'us_migration_61_transient', 1, 5 * MINUTE_IN_SECONDS );
			}
		}

		/* Add Interactive Banner checkbox if optimize CSS option is ON */
		if ( isset( $options['optimize_assets'] ) AND $options['optimize_assets'] AND is_array( $options['assets'] ) ) {
			$options['assets'] = array_unique( array_merge( $options['assets'], array( 'ibanner' ) ) );
			$changed = TRUE;
		}

		return $changed;
	}

	/* Creates Header based on former Theme Options, when Header Builder plugins is not used */
	private function get_header_settings( $options ) {

		$header_settings = array(
			'default' => array( 'options' => array(), 'layout' => array() ),
			'tablets' => array( 'options' => array(), 'layout' => array() ),
			'mobiles' => array( 'options' => array(), 'layout' => array() ),
			'data' => array(),
		);

		$header_templates = array(
			'simple_1' => array(
				'title' => 'Simple 1',
				'default' => array(
					'options' => array(
						'orientation' => 'hor',
						'top_show' => FALSE,
						'middle_height' => '100px',
						'middle_sticky_height' => '60px',
						'bottom_show' => FALSE,
					),
					'layout' => array(
						'middle_left' => array( 'image:1', 'text:1' ),
						'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
					),
				),
				'tablets' => array(
					'options' => array(
						'middle_height' => '80px',
						'middle_sticky_height' => '50px',
					),
				),
				'mobiles' => array(
					'options' => array(
						'breakpoint' => '600px',
						'scroll_breakpoint' => '50px',
						'middle_height' => '50px',
						'middle_sticky_height' => '50px',
					),
				),
				// Only the values that differ from the elements' defautls
				'data' => array(
					'image:1' => array(
						'img' => '',
						'link' => '/',
					),
					'text:1' => array(
						'text' => 'LOGO',
					),
				),
			),
			'extended_1' => array(
				'title' => 'Extended 1',
				'default' => array(
					'options' => array(
						'orientation' => 'hor',
						'top_show' => TRUE,
						'top_height' => '40px',
						'top_sticky_height' => '0px',
						'middle_height' => '100px',
						'middle_sticky_height' => '60px',
						'bottom_show' => FALSE,
					),
					'layout' => array(
						'top_left' => array( 'text:2', 'text:3' ),
						'top_right' => array( 'socials:1' ),
						'middle_left' => array( 'image:1', 'text:1' ),
						'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
					),
				),
				'tablets' => array(
					'options' => array(
						'middle_height' => '80px',
					),
					'layout' => array(
						'top_left' => array( 'text:2', 'text:3' ),
					),
				),
				'mobiles' => array(
					'options' => array(
						'breakpoint' => '600px',
						'scroll_breakpoint' => '50px',
						'top_show' => FALSE,
						'middle_height' => '50px',
						'middle_sticky_height' => '50px',
					),
				),
				'data' => array(
					'image:1' => array(
						'img' => '',
						'link' => '/',
					),
					'text:1' => array(
						'text' => 'LOGO',
					),
					'text:2' => array(
						'text' => '+321 123 4567',
						'icon' => 'fas|phone',
					),
					'text:3' => array(
						'text' => 'info@test.com',
						'link' => 'mailto:info@example.com',
						'icon' => 'fas|envelope',
					),
					'socials:1' => array(
						'items' => array(
							array(
								'type' => 'facebook',
								'url' => '#',
							),
							array(
								'type' => 'twitter',
								'url' => '#',
							),
							array(
								'type' => 'google',
								'url' => '#',
							),
							array(
								'type' => 'linkedin',
								'url' => '#',
							),
							array(
								'type' => 'youtube',
								'url' => '#',
							),
						),
					),
				),
			),
			'extended_2' => array(
				'title' => 'Extended 2',
				'default' => array(
					'options' => array(
						'orientation' => 'hor',
						'top_show' => FALSE,
						'middle_height' => '100px',
						'middle_sticky_height' => '0px',
						'bottom_show' => TRUE,
					),
					'layout' => array(
						'middle_left' => array( 'image:1', 'text:1' ),
						'middle_right' => array( 'text:2', 'text:3' ),
						'bottom_left' => array( 'menu:1' ),
						'bottom_right' => array( 'search:1', 'cart:1' ),
					),
				),
				'tablets' => array(
					'options' => array(
						'middle_height' => '50px',
						'middle_sticky_height' => '50px',
					),
					'layout' => array(
						'middle_left' => array(),
						'middle_center' => array( 'image:1', 'text:1' ),
						'middle_right' => array(),
					),
				),
				'mobiles' => array(
					'options' => array(
						'breakpoint' => '600px',
						'scroll_breakpoint' => '50px',
						'middle_height' => '50px',
					),
					'layout' => array(
						'middle_left' => array(),
						'middle_center' => array( 'image:1', 'text:1' ),
						'middle_right' => array(),
					),
				),
				'data' => array(
					'image:1' => array(
						'img' => '',
						'link' => '/',
					),
					'search:1' => array(
						'layout' => 'modern',
					),
					'text:1' => array(
						'text' => 'LOGO',
					),
					'text:2' => array(
						'text' => '+321 123 4567',
						'icon' => 'fas|phone',
					),
					'text:3' => array(
						'text' => 'info@test.com',
						'link' => 'mailto:info@example.com',
						'icon' => 'fas|envelope',
					),
				),
			),
			'centered_1' => array(
				'title' => 'Centered 1',
				'default' => array(
					'options' => array(
						'orientation' => 'hor',
						'top_show' => FALSE,
						'middle_height' => '100px',
						'middle_sticky_height' => '50px',
						'middle_centering' => TRUE,
						'bottom_show' => TRUE,
						'bottom_centering' => 1,
					),
					'layout' => array(
						'middle_center' => array( 'image:1', 'text:1' ),
						'bottom_center' => array( 'menu:1', 'search:1', 'cart:1' ),
					),
				),
				'tablets' => array(
					'options' => array(
						'middle_height' => '50px',
						'middle_sticky_height' => '0px',
					),
					'layout' => array(
						'bottom_left' => array( 'menu:1' ),
						'bottom_center' => array(),
						'bottom_right' => array( 'search:1', 'cart:1' ),
					),
				),
				'mobiles' => array(
					'options' => array(
						'breakpoint' => '600px',
						'scroll_breakpoint' => '50px',
						'middle_height' => '50px',
						'middle_sticky_height' => '0px',
					),
					'layout' => array(
						'bottom_left' => array( 'menu:1' ),
						'bottom_center' => array(),
						'bottom_right' => array( 'search:1', 'cart:1' ),
					),
				),
				'data' => array(
					'image:1' => array(
						'img' => '',
						'link' => '/',
					),
					'text:1' => array(
						'text' => 'LOGO',
					),
					'search:1' => array(
						'layout' => 'fullscreen',
					),
				),
			),
			'vertical_1' => array(
				'title' => 'Vertical 1',
				'default' => array(
					'options' => array(
						'orientation' => 'ver',
						'bottom_show' => FALSE,
					),
					'layout' => array(
						'middle_left' => array(
							'image:1',
							'text:1',
							'menu:1',
							'search:1',
							'cart:1',
							'text:2',
							'text:3',
						),
					),
				),
				'tablets' => array(
					'options' => array(
						'orientation' => 'hor',
						'middle_height' => '80px',
					),
					'layout' => array(
						'top_center' => array( 'text:2', 'text:3' ),
						'middle_left' => array( 'image:1', 'text:1' ),
						'middle_center' => array(),
						'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
					),
				),
				'mobiles' => array(
					'options' => array(
						'breakpoint' => '600px',
						'orientation' => 'hor',
						'middle_height' => '50px',
					),
					'layout' => array(
						'top_center' => array( 'text:2', 'text:3' ),
						'middle_left' => array( 'image:1', 'text:1' ),
						'middle_center' => array(),
						'middle_right' => array( 'menu:1', 'search:1', 'cart:1' ),
					),
				),
				'data' => array(
					'image:1' => array(
						'img' => '',
						'link' => '/',
						'design_options' => array(
							'margin_top_default' => '30px',
							'margin_bottom_default' => '30px',
						),
					),
					'menu:1' => array(
						'indents' => '0.7em',
						'design_options' => array(
							'margin_bottom_default' => '30px',
						),
					),
					'text:1' => array(
						'text' => 'LOGO',
					),
					'text:2' => array(
						'text' => '+321 123 4567',
						'icon' => 'fas|phone',
						'design_options' => array(
							'margin_bottom_default' => '10px',
						),
					),
					'text:3' => array(
						'text' => 'info@test.com',
						'link' => 'mailto:info@example.com',
						'icon' => 'fas|envelope',
						'design_options' => array(
							'margin_bottom_default' => '10px',
						),
					),
				),
			),
		);

		$side_options_config = array(
			'breakpoint' => '900px',
			'orientation' => 'hor',
			'sticky' => TRUE,
			'scroll_breakpoint' => '100px',
			'transparent' => FALSE,
			'width' => '300px',
			'elm_align' => 'center',
			'shadow' => 'thin',
			'top_show' => TRUE,
			'top_height' => '40px',
			'top_sticky_height' => '40px',
			'top_fullwidth' => FALSE,
			'top_centering' => FALSE,
			'middle_height' => '80px',
			'middle_sticky_height' => '60px',
			'middle_fullwidth' => FALSE,
			'middle_centering' => FALSE,
			'elm_valign' => 'top',
			'bg_img' => '',
			'bg_img_size' => 'cover',
			'bg_img_repeat' => 'repeat',
			'bg_img_position' => 'top left',
			'bg_img_attachment' => TRUE,
			'bottom_show' => TRUE,
			'bottom_height' => '50px',
			'bottom_sticky_height' => '50px',
			'bottom_fullwidth' => FALSE,
			'bottom_centering' => FALSE,
		);


		foreach ( $side_options_config as $field_name => $field_value ) {
			$header_settings['default']['options'][ $field_name ] = $field_value;
			$header_settings['tablets']['options'][ $field_name ] = $field_value;
			$header_settings['mobiles']['options'][ $field_name ] = $field_value;

		}

		// Layout-defined values
		if ( isset( $options['header_layout'] ) AND isset( $header_templates[ $options['header_layout'] ] ) ) {
			$header_template = us_fix_header_template_settings( $header_templates[ $options['header_layout'] ] );
			$header_settings = us_array_merge( $header_settings, $header_template );
		}

		// Filling elements' data with default values
		$header_settings = us_fix_header_settings( $header_settings );

		// Side options
		$rules = array(
			'header_transparent' => array(
				'new_name' => 'transparent',
			),
			'header_fullwidth' => array(
				'new_names' => array( 'top_fullwidth', 'middle_fullwidth', 'bottom_fullwidth' ),
			),
			'header_top_height' => array(
				'new_name' => 'top_height',
			),
			'header_top_sticky_height' => array(
				'new_name' => 'top_sticky_height',
			),
			'header_middle_height' => array(
				'new_name' => 'middle_height',
			),
			'header_middle_sticky_height' => array(
				'new_name' => 'middle_sticky_height',
			),
			'header_bottom_height' => array(
				'new_name' => 'bottom_height',
			),
			'header_bottom_sticky_height' => array(
				'new_name' => 'bottom_sticky_height',
			),
			'header_main_width' => array(
				'new_name' => 'width',
			),
		);

		foreach ( $rules as $old_name => $rule ) {
			if ( ! isset( $options[ $old_name ] ) AND ( isset( $rule['new_name'] ) OR isset( $rule['new_names'] ) ) ) {
				continue;
			}
			if ( isset( $rule['transfer_if'] ) AND ! usof_execute_show_if( $rule['transfer_if'], $options ) ) {
				continue;
			}
			$new_names = isset( $rule['new_names'] ) ? $rule['new_names'] : array( $rule['new_name'] );
			foreach ( $new_names as $new_name ) {
				$header_settings['default']['options'][ $new_name ] = $options[ $old_name ];
			}
		}

		// header_sticky => sticky
		if ( isset( $options['header_sticky'] ) ) {
			if ( is_array( $options['header_sticky'] ) ) {
				foreach ( array( 'default', 'tablets', 'mobiles' ) as $layout ) {
					$header_settings[ $layout ]['options']['sticky'] = in_array( $layout, $options['header_sticky'] );
				}
			} else {
				$header_settings['default']['options']['sticky'] = $options['header_sticky'];
				$header_settings['tablets']['options']['sticky'] = $options['header_sticky'];
				$header_settings['mobiles']['options']['sticky'] = $options['header_sticky'];
			}
		}

		// Transferring elements' values
		$rules = array(
			'image:1' => array(
				'show_if' => array( 'logo_type', '=', 'img' ),
				'values' => array(
					'img' => '=logo_image',
					'link' => array( 'url' => '/' ),
					'height' => '=logo_height',
					'height_tablets' => '=logo_height_tablets',
					'height_mobiles' => '=logo_height_mobiles',
					'height_sticky' => '=logo_height_sticky',
					'height_sticky_tablets' => '=logo_height_tablets',
					'height_sticky_mobiles' => '=logo_height_mobiles',
				),
			),
			'text:1' => array(
				'show_if' => array( 'logo_type', '=', 'text' ),
				'values' => array(
					'text' => '=logo_text',
					'link' => array( 'url' => '/' ),
					'size' => '=logo_font_size',
					'size_tablets' => '=logo_font_size_tablets',
					'size_mobiles' => '=logo_font_size_mobiles',
				),
			),
			'text:2' => array(
				'show_if' => array(
					array( 'header_contacts_show', '=', 1 ),
					'and',
					array( 'header_contacts_phone', '!=', '' ),
				),
				'values' => array(
					'text' => '=header_contacts_phone',
					'icon' => 'fas|phone',
				),
			),
			'text:3' => array(
				'show_if' => array(
					array( 'header_contacts_show', '=', 1 ),
					'and',
					array( 'header_contacts_email', '!=', '' ),
				),
				'values' => array(
					'text' => '=header_contacts_email',
					'icon' => 'fas|envelope',
				),
			),
			'menu:1' => array(
				'values' => array(
					'source' => '=menu_source',
					'font_size' => '=menu_fontsize',
					'indents' => '=menu_indents',
					'vstretch' => '=menu_height',
					'dropdown_font_size' => '=menu_sub_fontsize',
					'mobile_width' => '=menu_mobile_width',
					'mobile_behavior' => '=menu_togglable_type',
					'mobile_font_size' => '=menu_fontsize_mobile',
					'mobile_dropdown_font_size' => '=menu_sub_fontsize_mobile',
				),
			),
			'search:1' => array(
				'show_if' => array( 'header_search_show', '=', 1 ),
				'values' => array(
					'layout' => '=header_search_layout',
				),
			),
			'socials:1' => array(
				'values' => array(
					'items' => array(),
				),
			),
		);

		foreach ( $rules as $elm => $rule ) {
			if ( ! isset( $header_settings['data'][ $elm ] ) ) {
				$header_settings['data'][ $elm ] = array();
				$type = strtok( $elm, ':' );

				// Setting default values for fallback
				$elm_config = us_config( 'elements/' . $type, array() );
				foreach ( $elm_config['params'] as $field_name => $field ) {
					$value = isset( $field['std'] ) ? $field['std'] : '';

					// Some default header values may be based on main theme options' values
					if ( is_string( $value ) AND substr( $value, 0, 1 ) == '=' AND isset( $options[ substr( $value, 1 ) ] ) ) {
						$value = $options[ substr( $value, 1 ) ];
					}
					$header_settings['data'][ $elm ][ $field_name ] = $value;
				}
			}

			// Setting values
			if ( isset( $rule['values'] ) AND is_array( $rule['values'] ) ) {
				foreach ( $rule['values'] as $key => $value ) {
					if ( is_string( $value ) AND substr( $value, 0, 1 ) == '=' ) {
						$old_key = substr( $value, 1 );
						if ( ! isset( $options[ $old_key ] ) ) {
							continue;
						}
						$value = strip_tags( $options[ $old_key ] );
					}
					$header_settings['data'][ $elm ][ $key ] = $value;
				}
			}

			// Hiding the element when needed
			if ( isset( $rule['show_if'] ) AND ! usof_execute_show_if( $rule['show_if'], $options ) ) {
				foreach ( array( 'default', 'tablets', 'mobiles' ) as $layout ) {
					foreach ( $header_settings[ $layout ]['layout'] as $cell => $cell_elms ) {
						if ( $cell == 'hidden' ) {
							continue;
						}
						if ( ( $elm_pos = array_search( $elm, $cell_elms ) ) !== FALSE ) {
							array_splice( $header_settings[ $layout ]['layout'][ $cell ], $elm_pos, 1 );
							$header_settings[ $layout ]['layout']['hidden'][] = $elm;
							break;
						}
					}
				}
			}
		}

		// Logos for tablets and mobiles states
		if ( isset( $header_settings['data']['image:1'] ) ) {
			foreach ( array( 'tablets' => 'image:2', 'mobiles' => 'image:3' ) as $layout => $key ) {
				if ( isset( $header_settings['data'][ $key ] ) OR ! isset( $options[ 'logo_image_' . $layout ] ) OR empty( $options[ 'logo_image_' . $layout ] ) ) {
					continue;
				}
				$header_settings['data'][ $key ] = array_merge(
					$header_settings['data']['image:1'], array(
						'img' => $options[ 'logo_image_' . $layout ],
						'img_transparent' => '',
					)
				);
				foreach ( $header_settings[ $layout ]['layout'] as $cell => $cell_elms ) {
					if ( $cell == 'hidden' ) {
						continue;
					}
					if ( ( $elm_pos = array_search( 'image:1', $cell_elms ) ) !== FALSE ) {
						$header_settings[ $layout ]['layout'][ $cell ][ $elm_pos ] = $key;
						$header_settings[ $layout ]['layout']['hidden'][] = 'image:1';
						break;
					}
				}
				$header_settings['default']['layout']['hidden'][] = $key;
				$header_settings[ ( $layout == 'tablets' ) ? 'mobiles' : 'tablets' ]['layout']['hidden'][] = $key;
			}
		}

		// Fixing text links
		if ( isset( $header_settings['data']['text:3'] ) AND isset( $header_settings['data']['text:3']['text'] ) ) {
			$header_settings['data']['text:3']['link'] = 'mailto:' . $header_settings['data']['text:3']['text'];
		}

		// Inverting logo position
		if ( isset( $options['header_invert_logo_pos'] ) AND $options['header_invert_logo_pos'] ) {
			foreach ( array( 'default', 'tablets', 'mobiles' ) as $layout ) {
				if ( isset( $header_settings[ $layout ]['layout']['middle_left'] ) AND isset( $header_settings[ $layout ]['layout']['middle_left'] ) ) {
					$tmp = $header_settings[ $layout ]['layout']['middle_left'];
					$header_settings[ $layout ]['layout']['middle_left'] = $header_settings[ $layout ]['layout']['middle_right'];
					$header_settings[ $layout ]['layout']['middle_right'] = $tmp;
				}
			}
		}

		return $header_settings;
	}
}
