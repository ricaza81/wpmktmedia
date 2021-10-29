<?php

class us_migration_4_0 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_vc_row( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ( ! empty( $params['columns_type'] ) AND $params['columns_type'] == 'boxes' ) AND ( empty( $params['content_placement'] ) OR $params['content_placement'] == 'default' ) ) {
			$params['content_placement'] = 'middle';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_vc_row_inner( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ( ! empty( $params['columns_type'] ) AND $params['columns_type'] == 'boxes' ) AND ( empty( $params['content_placement'] ) OR $params['content_placement'] == 'default' ) ) {
			$params['content_placement'] = 'middle';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_cta( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( empty( $params['btn_color'] ) ) {
			$params['btn_color'] = 'primary';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_progbar( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['color'] ) AND $params['color'] == 'contrast' ) {
			$params['color'] = 'heading';
			$changed = TRUE;
		}

		if ( empty( $params['style'] ) OR $params['style'] == '1' ) {
			if ( empty( $params['size'] ) OR $params['size'] == 'medium' ) {
				$params['size'] = '10px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'small' ) {
				$params['size'] = '5px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'large' ) {
				$params['size'] = '15px';
				$changed = TRUE;
			}
		} elseif ( $params['style'] == '2' ) {
			if ( empty( $params['size'] ) OR $params['size'] == 'medium' ) {
				$params['size'] = '32px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'small' ) {
				$params['size'] = '24px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'large' ) {
				$params['size'] = '40px';
				$changed = TRUE;
			}
		} elseif ( $params['style'] == '3' ) {
			if ( empty( $params['size'] ) OR $params['size'] == 'medium' ) {
				$params['size'] = '6px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'small' ) {
				$params['size'] = '4px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'large' ) {
				$params['size'] = '8px';
				$changed = TRUE;
			}
		} elseif ( $params['style'] == '4' ) {
			if ( empty( $params['size'] ) OR $params['size'] == 'medium' ) {
				$params['size'] = '6px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'small' ) {
				$params['size'] = '4px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'large' ) {
				$params['size'] = '8px';
				$changed = TRUE;
			}
		} elseif ( $params['style'] == '5' ) {
			if ( empty( $params['size'] ) OR $params['size'] == 'medium' ) {
				$params['size'] = '4px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'small' ) {
				$params['size'] = '2px';
				$changed = TRUE;
			} elseif ( $params['size'] == 'large' ) {
				$params['size'] = '6px';
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_theme_options( &$options ) {
		// Adding default footer post
		$footer_sidebars_names = array(
			1 => 'footer_first',
			2 => 'footer_second',
			3 => 'footer_third',
			4 => 'footer_fourth',
		);

		if (
			! get_posts(
				array(
					'name' => 'default-footer',
					'post_type' => 'us_footer',
					'post_status' => 'publish',
					'numberposts' => 1,
				)
			)
			// Small fix for updating to later versions
			AND ! get_posts(
				array(
					'name' => 'default-footer',
					'post_type' => 'us_page_block',
					'post_status' => 'publish',
					'numberposts' => 1,
				)
			)
		) {
			$footer_content = '';

			if ( isset( $options['footer_show_top'] ) AND $options['footer_show_top'] ) {
				$footer_content .= '[vc_row gap="20" color_scheme="footer-top"]';
				$footer_columns = ( isset( $options['footer_columns'] ) AND in_array(
						$options['footer_columns'], array(
							1,
							2,
							3,
							4,
						)
					) ) ? $options['footer_columns'] : 3;
				$width_attribute = '';
				if ( $footer_columns != 1 ) {
					$width_attribute = ' width="1/' . $footer_columns . '"';
				}
				for ( $i = 1; $i <= $footer_columns; $i ++ ) {
					$footer_content .= '[vc_column' . $width_attribute . '][vc_widget_sidebar sidebar_id="' . $footer_sidebars_names[ $i ] . '"][/vc_column]';
				}
				$footer_content .= '[/vc_row]';
			}

			$disabled_attribute = $copyright_text = '';
			if ( ! isset( $options['footer_show_bottom'] ) OR ( ! $options['footer_show_bottom'] ) ) {
				$disabled_attribute = ' disable_element="yes"';
			}

			if ( ! empty( $options['footer_copyright'] ) ) {
				$copyright_text = $options['footer_copyright'];
			}

			$menu_name = 'us_footer_menu';
			$locations = get_nav_menu_locations();

			if ( isset( $locations[ $menu_name ] ) ) {
				$footer_content .= '[vc_row height="small" color_scheme="footer-bottom" el_class="align_center_xs"' . $disabled_attribute . ']
[vc_column width="1/2"][vc_column_text]' . $copyright_text . '[/vc_column_text][/vc_column]
[vc_column width="1/2"][us_additional_menu layout="hor" align="right" source="' . $locations[ $menu_name ] . '"][/vc_column]
[/vc_row]';
			} else {
				$footer_content .= '[vc_row height="small" color_scheme="footer-bottom"' . $disabled_attribute . ']
[vc_column][vc_column_text]<p style="text-align: center;">' . $copyright_text . '</p>[/vc_column_text][/vc_column]
[/vc_row]';
			}

			$footer_post_array = array(
				'post_type' => 'us_footer',
				'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
				'post_name' => 'default-footer',
				'post_title' => 'Default Footer',
				'post_content' => $footer_content,
				'post_status' => 'publish',
			);

			wp_insert_post( $footer_post_array );
		}


		$options['footer_id'] = 'default-footer';

		// Colors migration
		if ( ! empty( $options['color_subfooter_bg'] ) ) {
			$options['color_footer_bg_alt'] = $options['color_subfooter_bg'];
		}

		if ( ! empty( $options['color_subfooter_border'] ) ) {
			$options['color_footer_border'] = $options['color_subfooter_border'];
		}

		// Create custom sidebars
		$widget_areas = get_option( 'us_widget_areas' );
		if ( empty( $widget_areas ) ) {
			$widget_areas = array();
		}

		$widget_areas = array_merge(
			$widget_areas, array(
				'footer_first' => 'Footer Column 1',
				'footer_second' => 'Footer Column 2',
				'footer_third' => 'Footer Column 3',
				'footer_fourth' => 'Footer Column 4',
			)
		);

		update_option( 'us_widget_areas', $widget_areas );

		return TRUE;
	}

	// Meta
	public function translate_meta( &$meta, $post_type ) {
		$changed = FALSE;

		$translate_meta_for = array(
			'post',
			'page',
			'us_portfolio',
			'product',
		);

		if ( ! in_array( $post_type, $translate_meta_for ) ) {
			return FALSE;
		}

		if ( isset( $meta['us_tile_action'][0] ) AND $meta['us_tile_action'][0] == 'lightbox' ) {
			global $us_migration_current_post_id;
			$thumb_url = get_the_post_thumbnail_url( $us_migration_current_post_id, 'full' );
			if ( $thumb_url ) {
				$meta['us_tile_link'][0] = '{"url":"' . esc_url( $thumb_url ) . '","target":""}';
				$changed = TRUE;
			}
		}

		if ( isset( $meta['us_footer_show_top'][0] ) AND $meta['us_footer_show_top'][0] == 'hide' AND isset( $meta['us_footer_show_bottom'][0] ) AND $meta['us_footer_show_bottom'][0] == 'hide' ) {
			$meta['us_footer_remove'][0] = 1;
			$changed = TRUE;
		}

		return $changed;
	}

}
