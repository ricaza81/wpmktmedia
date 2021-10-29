<?php

class us_migration_5_0 extends US_Migration_Translator {

	private $woocommerce_shop_page_id = NULL;

	/**
	 * @var bool Possibly dangerous translation that needs to be migrated manually
	 */
	public $should_be_manual = TRUE;

	public function migration_completed_message() {
		global $help_portal_url;
		$output = '<div class="updated us-migration">';
		$output .= '<p><strong>Update to ' . US_THEMENAME . ' ' . US_THEMEVERSION . ' is completed</strong>. Now check your website. If you notice some issues, <a href="'. $help_portal_url .'/' . strtolower( US_THEMENAME ) . '/tickets/" target="_blank">go to the support</a>.</p>';
		$output .= '</div>';

		return $output;
	}

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	// TESTIMONIALS
	public function translate_us_testimonials( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'us_testimonial';
		$params['filter_style'] = 'style_3';

		// Global layout index for the grid layout name
		global $migrated_testimonial_layouts_count;
		$migrated_testimonial_layouts_count = ( isset( $migrated_testimonial_layouts_count ) ) ? $migrated_testimonial_layouts_count + 1 : 1;

		$layout_name = ( ! empty( $params['style'] ) ) ? 'testimonial_' . $params['style'] : 'testimonial_1';

		// Find apropriate grid template to copy defaults from
		if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$layout_name] ) ) {
			$layout = $templates_config[$layout_name];

			if ( isset( $params['text_size'] ) AND $params['text_size'] != '' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						$layout['data'][$elm_name]['font_size'] = $params['text_size'];
						$layout['data'][$elm_name]['line_height'] = '1.7';
						break;
					}
				}
			}

			// Fill missing values for the layout
			$layout = us_fix_grid_settings( $layout );

			// Create the grid layout post
			$layout_id = $this->add_grid_layout( 'layout_' . $migrated_testimonial_layouts_count, $layout['title'] . ' #' . $migrated_testimonial_layouts_count, $layout );

			// Set grid layout ID
			$params['items_layout'] = $layout_id;
		}

		if ( isset( $params['categories'] ) AND ! empty( $params['categories'] ) ) {
			$params['taxonomy_us_testimonial_category'] = $params['categories'];
		}

		if ( ! isset( $params['columns'] ) OR empty( $params['columns'] ) ) {
			$params['columns'] = 3;
		}

		if ( isset( $params['items'] ) AND ! empty( $params['items'] ) ) {
			$params['items_quantity'] = $params['items'];
		} else {
			$params['items_quantity'] = get_option( 'posts_per_page' );
		}

		if ( isset( $params['filter'] ) AND $params['filter'] == 'category' ) {
			$params['filter_us_testimonial'] = 'us_testimonial_category';
		}

		unset( $params['categories'] );
		unset( $params['items'] );
		unset( $params['style'] );
		unset( $params['text_size'] );
		unset( $params['filter'] );

		return TRUE;
	}

	// PORTFOLIO
	public function translate_us_portfolio( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'us_portfolio';
		$params['filter_style'] = 'style_3';

		// Global layout index for the grid layout name
		global $migrated_portfolio_layouts_count;
		$migrated_portfolio_layouts_count = ( isset( $migrated_portfolio_layouts_count ) ) ? $migrated_portfolio_layouts_count + 1 : 1;

		$layout_name = ( ! empty( $params['style'] ) ) ? $params['style'] : 'style_1';

		if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$layout_name] ) ) {
			$layout = $templates_config[$layout_name];

			if ( isset( $params['ratio'] ) AND $params['ratio'] != '1x1' ) {
				$layout['default']['options']['ratio'] = $params['ratio'];
			}

			if ( isset( $params['text_color'] ) AND $params['text_color'] != '' ) {
				$layout['default']['options']['color_text'] = $params['text_color'];
			}

			if ( isset( $params['bg_color'] ) AND $params['bg_color'] != '' ) {
				$layout['default']['options']['color_bg'] = $params['bg_color'];
			}

			if ( isset( $params['items_action'] ) AND $params['items_action'] != 'default' ) {
				if ( $params['items_action'] == 'lightbox_page' ) {
					$layout['default']['options']['link'] = 'popup_post';
				} elseif ( $params['items_action'] == 'lightbox_image' ) {
					$layout['default']['options']['link'] = 'popup_post_image';
				}
			}

			if ( isset( $params['popup_width'] ) AND $params['popup_width'] != '' ) {
				$layout['default']['options']['popup_width'] = $params['popup_width'];
			}

			// Reset Aspect Ratio & Post Image position if masonry enabled
			if ( isset( $params['type'] ) AND $params['type'] == 'masonry' ) {
				$layout['default']['options']['fixed'] = 0;
				$layout['default']['options']['overflow'] = 1;
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_image' ) {
						$layout['data'][$elm_name]['design_options']['position_top_default'] = '';
						$layout['data'][$elm_name]['design_options']['position_left_default'] = '';
						$layout['data'][$elm_name]['design_options']['position_right_default'] = '';
						$layout['data'][$elm_name]['design_options']['position_bottom_default'] = '';
						break;
					}
				}
			}

			// Change meta alignment
			if ( ! isset( $params['align'] ) ) {
				$params['align'] = 'center';
			}
			if ( isset( $params['align'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 8 ) == 'vwrapper' ) {
						$layout['data'][$elm_name]['alignment'] = $params['align'];
						break;
					}
				}
			}

			// Change meta size
			if ( isset( $params['meta_size'] ) AND $params['meta_size'] != '' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 9 ) == 'post_date' ) {
						$layout['data'][$elm_name]['font_size'] = $params['meta_size'];
						break;
					}
				}
			}

			// Remove meta
			if ( ! isset( $params['meta'] ) OR $params['meta'] == '' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 9 ) == 'post_date' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Replace meta
			if ( isset( $params['meta'] ) AND $params['meta'] != 'date' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 9 ) == 'post_date' ) {
						if ( $params['meta'] == 'categories' ) {
							$new_elm_name = 'post_taxonomy:1';
							$elm['taxonomy_name'] = 'us_portfolio_category';
							if ( isset( $params['meta_size'] ) AND $params['meta_size'] != '' ) {
								$elm['font_size'] = $params['meta_size'];
							}
						} elseif ( $params['meta'] == 'desc' ) {
							$new_elm_name = 'post_content:1';
							$elm['type'] = 'excerpt_only';
							$elm['length'] = '99';
							if ( isset( $params['meta_size'] ) AND $params['meta_size'] != '' ) {
								$elm['font_size'] = $params['meta_size'];
								$elm['line_height'] = '1.6';
							}
						} else {
							break;
						}
						$layout['data'][$new_elm_name] = $elm;
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								$layout['default']['layout'][$pos][$key] = $new_elm_name;
							}
						}
						break;
					}
				}
			}

			// Fill missing values for the layout
			$layout = us_fix_grid_settings( $layout );

			// Create the grid layout post
			$layout_id = $this->add_grid_layout( 'layout_' . $migrated_portfolio_layouts_count, $layout['title'] . '-' . $migrated_portfolio_layouts_count, $layout );

			// Set grid layout ID
			$params['items_layout'] = $layout_id;
		}

		if ( isset( $params['categories'] ) AND ! empty( $params['categories'] ) ) {
			$params['taxonomy_us_portfolio_category'] = $params['categories'];
		}

		if ( ! isset( $params['columns'] ) OR empty( $params['columns'] ) ) {
			$params['columns'] = 3;
		}

		if ( isset( $params['items'] ) AND ! empty( $params['items'] ) ) {
			$params['items_quantity'] = $params['items'];
		} else {
			$params['items_quantity'] = '';
		}

		if ( isset( $params['with_indents'] ) AND $params['with_indents'] == '1' ) {
			$params['items_gap'] = '2px';
		} else {
			$params['items_gap'] = '';
		}

		if ( isset( $params['filter'] ) AND $params['filter'] == 'category' ) {
			$params['filter_us_portfolio'] = 'us_portfolio_category';
		}

		// Responsive options
		if ( us_get_option( 'portfolio_breakpoint_1_width' ) != 1200 ) {
			$params['breakpoint_1_width'] = us_get_option( 'portfolio_breakpoint_1_width' ) . 'px';
		}
		if ( us_get_option( 'portfolio_breakpoint_1_cols' ) != 3 ) {
			$params['breakpoint_1_cols'] = us_get_option( 'portfolio_breakpoint_1_cols' );
		}
		if ( us_get_option( 'portfolio_breakpoint_2_width' ) != 900 ) {
			$params['breakpoint_2_width'] = us_get_option( 'portfolio_breakpoint_2_width' ) . 'px';
		}
		if ( us_get_option( 'portfolio_breakpoint_2_cols' ) != 2 ) {
			$params['breakpoint_2_cols'] = us_get_option( 'portfolio_breakpoint_2_cols' );
		}
		if ( us_get_option( 'portfolio_breakpoint_3_width' ) != 600 ) {
			$params['breakpoint_3_width'] = us_get_option( 'portfolio_breakpoint_3_width' ) . 'px';
		}
		if ( us_get_option( 'portfolio_breakpoint_3_cols' ) != 1 ) {
			$params['breakpoint_3_cols'] = us_get_option( 'portfolio_breakpoint_3_cols' );
		}

		// If there are 1x2/2x1/2x2 tiles and grid type is regular grid, we should switch grid type from regular to masonry
		if ( empty( $params['type'] ) OR $params['type'] == 'grid' ) {
			us_open_wp_query_context();
			$items_ids = array();
			$get_posts_args = array(
				'post_type' => 'us_portfolio',
				'numberposts' => 50,
			);

			$categories = ( isset( $params['categories'] ) AND ! empty( $params['categories'] ) ) ? array_filter( explode( ',', $params['categories'] ) ) : array();
			if ( ! empty( $categories ) ) {
				$get_posts_args['us_portfolio_category'] = implode( ',', $categories );
			}

			foreach ( get_posts( $get_posts_args ) as $post ) {
				if ( ! isset( $items_categories[$post->ID] ) ) {
					$items_ids[] = $post->ID;
				}
			}

			if ( count( $items_ids ) > 0 ) {
				global $wpdb;
				$items_ids = implode( ',', $items_ids );
				$wpdb_query = 'SELECT `post_id`, `meta_value` FROM `' . $wpdb->postmeta . '` ';
				$wpdb_query .= 'WHERE `post_id` IN (' . $items_ids . ') AND `meta_key`=\'us_tile_size\' AND `meta_value` NOT IN (\'\', \'1x1\')';

				if ( count( $wpdb->get_results( $wpdb_query ) ) > 0 ) {
					$params['type'] = 'masonry';
					$params['img_size'] = 'large';
				}
			}
			us_close_wp_query_context();
		}

		unset( $params['categories'] );
		unset( $params['ratio'] );
		unset( $params['items'] );
		unset( $params['items_action'] );
		unset( $params['popup_width'] );
		unset( $params['style'] );
		unset( $params['align'] );
		unset( $params['with_indents'] );
		unset( $params['no_indents'] );
		unset( $params['meta'] );
		unset( $params['meta_size'] );
		unset( $params['text_color'] );
		unset( $params['bg_color'] );
		unset( $params['filter'] );

		return TRUE;
	}

	// BLOG
	public function translate_us_blog( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['filter_style'] = 'style_3';

		// Global layout index for the grid layout name
		global $migrated_blog_layouts_count;
		$migrated_blog_layouts_count = ( isset( $migrated_blog_layouts_count ) ) ? $migrated_blog_layouts_count + 1 : 1;

		$layout_name = ( ! empty( $params['layout'] ) ) ? $params['layout'] : 'classic';

		if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$layout_name] ) ) {
			$layout = $templates_config[$layout_name];

			// Change Title size if column is single
			if ( isset( $params['columns'] ) AND $params['columns'] == 1 ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_title' ) {
						$layout['data'][$elm_name]['font_size'] = '';
						break;
					}
				}
			}

			// Change post content length if it is not default
			$excerpt_length_option = us_get_option( 'excerpt_length' );
			if ( $excerpt_length_option != 30 ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						$layout['data'][$elm_name]['length'] = $excerpt_length_option;
						break;
					}
				}
			}

			// Change post content type or even remove if it is not default
			if ( isset( $params['content_type'] ) AND $params['content_type'] != 'excerpt' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						if ( $params['content_type'] == 'content' ) {
							$layout['data'][$elm_name]['type'] = 'full_content';
						} elseif ( $params['content_type'] == 'none' ) {
							unset( $layout['data'][$elm_name] );
							foreach ( $layout['default']['layout'] as $pos => $elms ) {
								if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
									unset( $layout['default']['layout'][$pos][$key] );
								}
							}
						}
						break;
					}
				}
			}

			// Remove read more button element if it is hidden
			if ( isset( $params['show_read_more'] ) AND $params['show_read_more'] == FALSE ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 3 ) == 'btn' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post date element if it is hidden
			if ( isset( $params['show_date'] ) AND $params['show_date'] == FALSE ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 9 ) == 'post_date' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
					}
				}
			}

			// Remove post author element if it is hidden
			if ( isset( $params['show_author'] ) AND $params['show_author'] == FALSE ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 11 ) == 'post_author' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post comments element if it is hidden
			if ( isset( $params['show_comments'] ) AND $params['show_comments'] == FALSE ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_comments' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post categories element if it is hidden
			if ( isset( $params['show_categories'] ) AND $params['show_categories'] == FALSE ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'category' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post tags element if it is hidden
			if ( isset( $params['show_tags'] ) AND $params['show_tags'] == FALSE ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'post_tag' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove empty wrappers
			foreach ( $layout['default']['layout'] as $wrapper_name => $wrapper_elms ) {
				if ( ( substr( $wrapper_name, 0, 8 ) == 'hwrapper' OR substr( $wrapper_name, 0, 8 ) == 'vwrapper' ) AND count( $wrapper_elms ) == 0 ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( $elm_name == $wrapper_name ) {
							unset( $layout['data'][$elm_name] );
							break;
						}
					}
					foreach ( $layout['default']['layout'] as $pos => $elms ) {
						if ( ( $key = array_search( $wrapper_name, $elms ) ) !== FALSE ) {
							unset( $layout['default']['layout'][$pos][$key] );
						}
						if ( $pos == $wrapper_name ) {
							unset( $layout['default']['layout'][$pos] );
						}
					}
				}
			}

			// Fill missing values for the layout
			$layout = us_fix_grid_settings( $layout );

			// Create the grid layout post
			$layout_id = $this->add_grid_layout( 'layout_' . $migrated_blog_layouts_count, $layout['title'] . ' #' . $migrated_blog_layouts_count, $layout );

			// Set grid layout ID
			$params['items_layout'] = $layout_id;
		}

		if ( ( empty( $params['layout'] ) OR in_array( $params['layout'], array( 'classic', 'smallcircle', 'smallsquare' ) ) ) AND ( isset( $params['columns'] ) AND $params['columns'] == 1 ) ) {
			$params['items_gap'] = '5rem';
		} elseif ( isset( $params['layout'] ) AND $params['layout'] == 'tiles' ) {
			$params['items_gap'] = '2px';
		} elseif ( isset( $params['layout'] ) AND $params['layout'] == 'flat' ) {
			$params['items_gap'] = '4px';
		} elseif ( isset( $params['layout'] ) AND $params['layout'] == 'compact' ) {
			$params['items_gap'] = '1rem';
		}

		if ( ( empty( $params['layout'] ) OR in_array( $params['layout'], array( 'classic', 'flat', 'tiles' ) ) )
			AND ( empty( $params['type'] ) OR $params['type'] != 'masonry' )
			AND ( empty( $params['columns'] ) OR $params['columns'] != 1 )
			AND ( empty( $params['img_size'] ) OR $params['img_size'] == 'default' )
		) {
			$params['img_size'] = 'us_600_600_crop';
		}

		if ( isset( $params['categories'] ) AND ! empty( $params['categories'] ) ) {
			$params['post_categories'] = $params['categories'];
		}

		if ( isset( $params['items'] ) AND ! empty( $params['items'] ) ) {
			$params['items_quantity'] = $params['items'];
		} else {
			$params['items_quantity'] = get_option( 'posts_per_page' );
		}

		if ( isset( $params['filter'] ) AND $params['filter'] == 'category' ) {
			$params['filter_post'] = 'category';
		}

		// Responsive options
		if ( us_get_option( 'blog_breakpoint_1_width' ) != 1200 ) {
			$params['breakpoint_1_width'] = us_get_option( 'blog_breakpoint_1_width' ) . 'px';
		}
		if ( us_get_option( 'blog_breakpoint_1_cols' ) != 3 ) {
			$params['breakpoint_1_cols'] = us_get_option( 'blog_breakpoint_1_cols' );
		}
		if ( us_get_option( 'blog_breakpoint_2_width' ) != 900 ) {
			$params['breakpoint_2_width'] = us_get_option( 'blog_breakpoint_2_width' ) . 'px';
		}
		if ( us_get_option( 'blog_breakpoint_2_cols' ) != 2 ) {
			$params['breakpoint_2_cols'] = us_get_option( 'blog_breakpoint_2_cols' );
		}
		if ( us_get_option( 'blog_breakpoint_3_width' ) != 600 ) {
			$params['breakpoint_3_width'] = us_get_option( 'blog_breakpoint_3_width' ) . 'px';
		}
		if ( us_get_option( 'blog_breakpoint_3_cols' ) != 1 ) {
			$params['breakpoint_3_cols'] = us_get_option( 'blog_breakpoint_3_cols' );
		}

		unset( $params['categories'] );
		unset( $params['items'] );
		unset( $params['layout'] );
		unset( $params['show_date'] );
		unset( $params['show_author'] );
		unset( $params['show_categories'] );
		unset( $params['show_tags'] );
		unset( $params['show_comments'] );
		unset( $params['show_read_more'] );
		unset( $params['content_type'] );
		unset( $params['filter'] );

		return TRUE;
	}

	public function translate_gallery( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['size'] ) AND $params['size'] == 'medium_large' ) {
			$params['size'] = 'us_768_0';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_gallery( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'medium_large' ) {
			$params['img_size'] = 'us_768_0';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_gmaps( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['markers'] ) ) {
			try {
				$markers = json_decode( urldecode( $params['markers'] ), TRUE );

				if ( is_array( $markers ) AND count( $markers ) ) {
					foreach ( $markers as $index => $marker ) {
						if ( ! empty( $marker['marker_latitude'] ) AND ! empty( $marker['marker_longitude'] ) ) {
							$markers[$index]['marker_address'] = $marker['marker_latitude'] . ' ' . $marker['marker_longitude'];
							$changed = TRUE;
						}
					}
				}
				if ( $changed ) {
					$params['markers'] = urlencode( json_encode( $markers ) );
				}
			}
			catch ( Exception $e ) {
			}
		}
		if ( ! empty( $params['latitude'] ) AND ! empty( $params['longitude'] ) ) {
			$params['marker_address'] = $params['latitude'] . ' ' . $params['longitude'];
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_image_slider( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'medium_large' ) {
			$params['img_size'] = 'us_768_0';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_logos( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'medium_large' ) {
			$params['img_size'] = 'us_768_0';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_single_image( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['size'] ) AND $params['size'] == 'medium_large' ) {
			$params['size'] = 'us_768_0';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_btn( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['icon'] ) ) {
			$translated_icon = $this->translate_icon_name( $params['icon'] );
			if ( $translated_icon != $params['icon'] ) {
				$params['icon'] = $translated_icon;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_cta( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['btn_icon'] ) ) {
			$translated_icon = $this->translate_icon_name( $params['btn_icon'] );
			if ( $translated_icon != $params['btn_icon'] ) {
				$params['btn_icon'] = $translated_icon;
				$changed = TRUE;
			}
		}

		if ( ! empty( $params['btn2_icon'] ) ) {
			$translated_icon = $this->translate_icon_name( $params['btn2_icon'] );
			if ( $translated_icon != $params['btn2_icon'] ) {
				$params['btn2_icon'] = $translated_icon;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_iconbox( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['icon'] ) ) {
			$translated_icon = $this->translate_icon_name( $params['icon'] );
			if ( $translated_icon != $params['icon'] ) {
				$params['icon'] = $translated_icon;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_message( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['icon'] ) ) {
			$translated_icon = $this->translate_icon_name( $params['icon'] );
			if ( $translated_icon != $params['icon'] ) {
				$params['icon'] = $translated_icon;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_person( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['custom_icon'] ) ) {
			$translated_icon = $this->translate_icon_name( $params['custom_icon'] );
			if ( $translated_icon != $params['custom_icon'] ) {
				$params['custom_icon'] = $translated_icon;
				$changed = TRUE;
			}
		}
		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'medium_large' ) {
			$params['img_size'] = 'us_768_0';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_pricing( &$name, &$params, &$content ) {
		$changed = FALSE;

		$items = json_decode( urldecode( $params['items'] ), TRUE );

		if ( is_array( $items ) AND count( $items ) ) {
			foreach ( $items as $index => $item ) {
				if ( ! empty( $item['btn_icon'] ) ) {
					$translated_icon = $this->translate_icon_name( $item['btn_icon'] );
					if ( $translated_icon != $item['btn_icon'] ) {
						$items[$index]['btn_icon'] = $translated_icon;
						$changed = TRUE;
					}
				}
			}
		}

		if ( $changed ) {
			$params['items'] = urlencode( json_encode( $items ) );
		}

		return $changed;
	}

	public function translate_us_separator( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['icon'] ) ) {
			$translated_icon = $this->translate_icon_name( $params['icon'] );
			if ( $translated_icon != $params['icon'] ) {
				$params['icon'] = $translated_icon;
				$changed = TRUE;
			}
		}

		if ( ! isset( $params['type'] ) OR empty( $params['type'] ) ) {
			$params['type'] = 'default';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_social_links( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['items'] ) AND strpos( $params['items'], '[' ) === 0 ) {
			try {
				$items = json_decode( urldecode( $params['items'] ), TRUE );

				if ( is_array( $items ) AND count( $items ) ) {
					foreach ( $items as $index => $item ) {
						if ( $item['type'] == 'custom' AND isset( $item['icon'] ) ) {
							$translated_icon = $this->translate_icon_name( $item['icon'] );
							if ( $translated_icon != $item['icon'] ) {
								$items[$index]['icon'] = $translated_icon;
								$changed = TRUE;
							}
						}
					}
				}

				if ( $changed ) {
					$params['items'] = urlencode( json_encode( $items ) );
				}
			}
			catch ( Exception $e ) {
			}
		}

		if ( ! empty( $params['color'] ) AND $params['color'] == 'desaturated' ) {
			$params['style'] = 'solid';
			$params['color'] = 'text';
		} elseif ( ! empty( $params['color'] ) AND $params['color'] == 'brand_inv' ) {
			$params['style'] = 'colored';
		} elseif ( ! empty( $params['color'] ) AND $params['color'] == 'desaturated_inv' ) {
			$params['style'] = 'solid';
			$params['color'] = 'text';
		} else {
			$params['style'] = 'solid';
		}

		$params['hover'] = 'fade';
		$params['shape'] = 'circle';

		return TRUE;
	}

	public function translate_vc_tta_section( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['icon'] ) ) {
			$translated_icon = $this->translate_icon_name( $params['icon'] );
			if ( $translated_icon != $params['icon'] ) {
				$params['icon'] = $translated_icon;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	// THEME OPTIONS
	public function translate_theme_options( &$options ) {

		/*
		 * Replace medim_large image size for Theme Options
		 */
		if ( isset( $options['post_preview_img_size'] ) AND $options['post_preview_img_size'] == 'medium_large' ) {
			$options['post_preview_img_size'] = 'us_768_0';
		}
		if ( isset( $options['post_related_img_size'] ) AND $options['post_related_img_size'] == 'medium_large' ) {
			$options['post_related_img_size'] = 'us_768_0';
		}
		if ( isset( $options['blog_img_size'] ) AND $options['blog_img_size'] == 'medium_large' ) {
			$options['blog_img_size'] = 'us_768_0';
		}
		if ( isset( $options['archive_img_size'] ) AND $options['archive_img_size'] == 'medium_large' ) {
			$options['archive_img_size'] = 'us_768_0';
		}
		if ( isset( $options['search_img_size'] ) AND $options['search_img_size'] == 'medium_large' ) {
			$options['search_img_size'] = 'us_768_0';
		}
		$new_img_size_present = FALSE;
		if ( empty( $options['img_size'] ) OR ! is_array( $options['img_size'] ) ) {
			$options['img_size'] = array();
		}
		foreach( $options['img_size'] as $i => $size ) {
			if ( $size['width'] == 768 AND $size['height'] == 0 AND ( $size['crop'] == array() OR empty( $size['crop'] ) ) ) {
				$new_img_size_present = TRUE;
			}
		}
		if ( ! $new_img_size_present ) {
			$options['img_size'][] = array(
				'width' => 768,
				'height' => 0,
				'crop' => array(),
			);
		}

		/*
		 * Blog Home Page
		 */
		if ( isset( $options['blog_layout'] ) ) {
			if (
				in_array( $options['blog_layout'], array( 'classic', 'smallcircle', 'smallsquare' ) )
				AND ! empty( $options['blog_cols'] )
				AND $options['blog_cols'] != 1
			) {
				$options['blog_items_gap'] = 1.5;
			} elseif ( $options['blog_layout'] == 'tiles' ) {
				$options['blog_items_gap'] = 0.15;
			} elseif ( $options['blog_layout'] == 'flat' ) {
				$options['blog_items_gap'] = 0.3;
			} elseif ( $options['blog_layout'] == 'compact' ) {
				$options['blog_items_gap'] = 1;
			}
		}

		$blog_layout_name = isset( $options['blog_layout'] ) ? $options['blog_layout'] : 'classic';
		if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$blog_layout_name] ) ) {
			$layout = $templates_config[$blog_layout_name];

			// Reset Title font size if column is single
			if ( isset( $options['blog_cols'] ) AND $options['blog_cols'] == 1 ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_title' ) {
						$layout['data'][$elm_name]['font_size'] = '';
						break;
					}
				}
			}

			// Change post content length if it is not default
			if ( isset( $options['excerpt_length'] ) AND $options['excerpt_length'] != 30 ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						$layout['data'][$elm_name]['length'] = $options['excerpt_length'];
						break;
					}
				}
			}

			// Change post content type or even remove if it is not default
			if ( isset( $options['blog_content_type'] ) AND $options['blog_content_type'] != 'excerpt' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						if ( $options['blog_content_type'] == 'content' ) {
							$layout['data'][$elm_name]['type'] = 'full_content';
						} elseif ( $options['blog_content_type'] == 'none' ) {
							unset( $layout['data'][$elm_name] );
							foreach ( $layout['default']['layout'] as $pos => $elms ) {
								if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
									unset( $layout['default']['layout'][$pos][$key] );
								}
							}
						}
						break;
					}
				}
			}

			// Remove read more button element if it is hidden
			if ( ! in_array( 'read_more', $options['blog_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 3 ) == 'btn' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post date element if it is hidden
			if ( ! in_array( 'date', $options['blog_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 9 ) == 'post_date' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
					}
				}
			}

			// Remove post author element if it is hidden
			if ( ! in_array( 'author', $options['blog_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 11 ) == 'post_author' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post comments element if it is hidden
			if ( ! in_array( 'comments', $options['blog_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_comments' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post categories element if it is hidden
			if ( ! in_array( 'categories', $options['blog_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'category' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post tags element if it is hidden
			if ( ! in_array( 'tags', $options['blog_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'post_tag' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove empty wrappers
			foreach ( $layout['default']['layout'] as $wrapper_name => $wrapper_elms ) {
				if ( ( substr( $wrapper_name, 0, 8 ) == 'hwrapper' OR substr( $wrapper_name, 0, 8 ) == 'vwrapper' ) AND count( $wrapper_elms ) == 0 ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( $elm_name == $wrapper_name ) {
							unset( $layout['data'][$elm_name] );
							break;
						}
					}
					foreach ( $layout['default']['layout'] as $pos => $elms ) {
						if ( ( $key = array_search( $wrapper_name, $elms ) ) !== FALSE ) {
							unset( $layout['default']['layout'][$pos][$key] );
						}
						if ( $pos == $wrapper_name ) {
							unset( $layout['default']['layout'][$pos] );
						}
					}
				}
			}

			// Fill missing values for the layout
			$layout = us_fix_grid_settings( $layout );

			// Create the grid layout post
			$layout_id = $this->add_grid_layout( 'layout_blog_home', $layout['title'] . ' - Blog Home Page', $layout );

			// Set grid layout ID
			$options['blog_layout'] = $layout_id;
		}

		/*
		 * Archive Pages
		 */
		if ( isset( $options['archive_layout'] ) ) {
			if (
				in_array( $options['archive_layout'], array( 'classic', 'smallcircle', 'smallsquare' ) )
				AND ! empty( $options['archive_cols'] )
				AND $options['archive_cols'] != 1
			) {
				$options['archive_items_gap'] = 1.5;
			} elseif ( $options['archive_layout'] == 'tiles' ) {
				$options['archive_items_gap'] = 0.15;
			} elseif ( $options['archive_layout'] == 'flat' ) {
				$options['archive_items_gap'] = 0.3;
			} elseif ( $options['archive_layout'] == 'compact' ) {
				$options['archive_items_gap'] = 1;
			}
		}

		$blog_layout_name = isset( $options['archive_layout'] ) ? $options['archive_layout'] : 'classic';
		if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$blog_layout_name] ) ) {
			$layout = $templates_config[$blog_layout_name];

			// Reset Title font size if column is single
			if ( isset( $options['archive_cols'] ) AND $options['archive_cols'] == 1 ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_title' ) {
						$layout['data'][$elm_name]['font_size'] = '';
						break;
					}
				}
			}

			// Change post content length if it is not default
			if ( isset( $options['excerpt_length'] ) AND $options['excerpt_length'] != 30 ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						$layout['data'][$elm_name]['length'] = $options['excerpt_length'];
						break;
					}
				}
			}

			// Change post content type or even remove if it is not default
			if ( isset( $options['archive_content_type'] ) AND $options['archive_content_type'] != 'excerpt' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						if ( $options['archive_content_type'] == 'content' ) {
							$layout['data'][$elm_name]['type'] = 'full_content';
						} elseif ( $options['archive_content_type'] == 'none' ) {
							unset( $layout['data'][$elm_name] );
							foreach ( $layout['default']['layout'] as $pos => $elms ) {
								if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
									unset( $layout['default']['layout'][$pos][$key] );
								}
							}
						}
						break;
					}
				}
			}

			// Remove read more button element if it is hidden
			if ( ! in_array( 'read_more', $options['archive_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 3 ) == 'btn' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post date element if it is hidden
			if ( ! in_array( 'date', $options['archive_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 9 ) == 'post_date' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
					}
				}
			}

			// Remove post author element if it is hidden
			if ( ! in_array( 'author', $options['archive_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 11 ) == 'post_author' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post comments element if it is hidden
			if ( ! in_array( 'comments', $options['archive_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_comments' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post categories element if it is hidden
			if ( ! in_array( 'categories', $options['archive_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'category' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post tags element if it is hidden
			if ( ! in_array( 'tags', $options['archive_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'post_tag' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove empty wrappers
			foreach ( $layout['default']['layout'] as $wrapper_name => $wrapper_elms ) {
				if ( ( substr( $wrapper_name, 0, 8 ) == 'hwrapper' OR substr( $wrapper_name, 0, 8 ) == 'vwrapper' ) AND count( $wrapper_elms ) == 0 ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( $elm_name == $wrapper_name ) {
							unset( $layout['data'][$elm_name] );
							break;
						}
					}
					foreach ( $layout['default']['layout'] as $pos => $elms ) {
						if ( ( $key = array_search( $wrapper_name, $elms ) ) !== FALSE ) {
							unset( $layout['default']['layout'][$pos][$key] );
						}
						if ( $pos == $wrapper_name ) {
							unset( $layout['default']['layout'][$pos] );
						}
					}
				}
			}

			// Fill missing values for the layout
			$layout = us_fix_grid_settings( $layout );

			// Create the grid layout post
			$layout_id = $this->add_grid_layout( 'layout_archive', $layout['title'] . ' - Archive Pages', $layout );

			// Set grid layout ID
			$options['archive_layout'] = $layout_id;
		}

		/*
		 * Search Results Page
		 */
		if ( isset( $options['search_layout'] ) ) {
			if (
				in_array( $options['search_layout'], array( 'classic', 'smallcircle', 'smallsquare' ) )
				AND ! empty( $options['search_cols'] )
				AND $options['search_cols'] != 1
			) {
				$options['search_items_gap'] = 1.5;
			} elseif ( $options['search_layout'] == 'tiles' ) {
				$options['search_items_gap'] = 0.15;
			} elseif ( $options['search_layout'] == 'flat' ) {
				$options['search_items_gap'] = 0.3;
			} elseif ( $options['search_layout'] == 'compact' ) {
				$options['search_items_gap'] = 1;
			}
		}

		$blog_layout_name = isset( $options['search_layout'] ) ? $options['search_layout'] : 'classic';
		if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$blog_layout_name] ) ) {
			$layout = $templates_config[$blog_layout_name];

			// Reset Title font size if column is single
			if ( isset( $options['search_cols'] ) AND $options['search_cols'] == 1 ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_title' ) {
						$layout['data'][$elm_name]['font_size'] = '';
						break;
					}
				}
			}

			// Change post content length if it is not default
			if ( isset( $options['excerpt_length'] ) AND $options['excerpt_length'] != 30 ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						$layout['data'][$elm_name]['length'] = $options['excerpt_length'];
						break;
					}
				}
			}

			// Change post content type or even remove if it is not default
			if ( isset( $options['search_content_type'] ) AND $options['search_content_type'] != 'excerpt' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						if ( $options['search_content_type'] == 'content' ) {
							$layout['data'][$elm_name]['type'] = 'full_content';
						} elseif ( $options['search_content_type'] == 'none' ) {
							unset( $layout['data'][$elm_name] );
							foreach ( $layout['default']['layout'] as $pos => $elms ) {
								if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
									unset( $layout['default']['layout'][$pos][$key] );
								}
							}
						}
						break;
					}
				}
			}

			// Remove read more button element if it is hidden
			if ( ! in_array( 'read_more', $options['search_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 3 ) == 'btn' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post date element if it is hidden
			if ( ! in_array( 'date', $options['search_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 9 ) == 'post_date' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
					}
				}
			}

			// Remove post author element if it is hidden
			if ( ! in_array( 'author', $options['search_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 11 ) == 'post_author' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post comments element if it is hidden
			if ( ! in_array( 'comments', $options['search_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_comments' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post categories element if it is hidden
			if ( ! in_array( 'categories', $options['search_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'category' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove post tags element if it is hidden
			if ( ! in_array( 'tags', $options['search_meta'] ) ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'post_tag' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}
			}

			// Remove empty wrappers
			foreach ( $layout['default']['layout'] as $wrapper_name => $wrapper_elms ) {
				if ( ( substr( $wrapper_name, 0, 8 ) == 'hwrapper' OR substr( $wrapper_name, 0, 8 ) == 'vwrapper' ) AND count( $wrapper_elms ) == 0 ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( $elm_name == $wrapper_name ) {
							unset( $layout['data'][$elm_name] );
							break;
						}
					}
					foreach ( $layout['default']['layout'] as $pos => $elms ) {
						if ( ( $key = array_search( $wrapper_name, $elms ) ) !== FALSE ) {
							unset( $layout['default']['layout'][$pos][$key] );
						}
						if ( $pos == $wrapper_name ) {
							unset( $layout['default']['layout'][$pos] );
						}
					}
				}
			}

			// Fill missing values for the layout
			$layout = us_fix_grid_settings( $layout );

			// Create the grid layout post
			$layout_id = $this->add_grid_layout( 'layout_search_results', $layout['title'] . ' - Search Results Page', $layout );

			// Set grid layout ID
			$options['search_layout'] = $layout_id;
		}

		/*
		 * Related Posts
		 */
		if ( isset( $options['post_related_layout'] ) ) {
			// For former "compact" layout set grid layout template and 1 column
			if ( $options['post_related_layout'] == 'compact' ) {
				$options['post_related_cols'] = 1;

			// For former "related" layout create custom grid layout
			} elseif ( $options['post_related_layout'] == 'related' ) {
				$layout_related = array(
					'data' => array(
						'post_image:1' => array(
							'placeholder' => '1',
							'thumbnail_size' => 'us_350_350_crop',
						),
						'post_title:1' => array(
							'font_size' => '1rem',
							'design_options' => array(
								'margin_top_default' => '0.6rem',
								'margin_bottom_default' => '0',
							),
						),
						'post_date:1' => array(
							'font_size' => '0.9rem',
							'color_text' => us_get_option( 'color_content_faded' ),
						),
					),
					'default' => array(
						'layout' => array(
							'middle_center' => array(
								0 => 'post_image:1',
								1 => 'post_title:1',
								2 => 'post_date:1',
							),
						),
					),
				);

				// Fill missing values for the layout
				$layout_related = us_fix_grid_settings( $layout_related );

				// Create the grid layout post
				$layout_id = $this->add_grid_layout( 'layout_related', 'Related Posts', $layout_related );

				// Set grid layout ID
				$options['post_related_layout'] = $layout_id;
				$options['post_related_cols'] = 3;
			}
		}

		/*
		 * Menu font family
		 */
		$menu_font_family = '';
		if ( $options['menu_font_family'] != 'none' ) {
			$menu_font_family = explode( '|', $options['menu_font_family'], 2 );
			$options['custom_font'] = array(
				array(
					'font_family' => $options['menu_font_family'],
				),
			);
		}

		// Quick fix for fallback migration: we run following code only once in 5 minutes on front or during actual migration
		$icons_migration_transient = get_transient( 'us_icons_migration_transient' );
		if ( $icons_migration_transient == FALSE OR is_admin() ) {
			/*
			 * Header icons
			 */
			ob_start();
			$headers = get_posts( array( 'post_type' => 'us_header' ) );
			ob_end_clean();
			foreach ( $headers as $header ) {
				if ( ! empty( $header->post_content ) AND strpos( $header->post_content, '{' ) === 0 ) {
					try {
						$header_settings = json_decode( $header->post_content, TRUE );
						$header_changed = FALSE;

						if ( isset( $header_settings['data'] ) and is_array( $header_settings['data'] ) ) {
							foreach ( $header_settings['data'] as $name => $data ) {
								// Design options => hide when sticky / nonsticky
								if ( isset( $data['design_options']['hide_for_sticky'] ) AND $data['design_options']['hide_for_sticky'] ) {
									$header_settings['data'][$name]['hide_for_sticky'] = TRUE;
									$header_changed = TRUE;
								}
								if ( isset( $data['design_options']['hide_for_not-sticky'] ) AND $data['design_options']['hide_for_not-sticky'] ) {
									$header_settings['data'][$name]['hide_for_not_sticky'] = TRUE;
									$header_changed = TRUE;
								}
								// Text element
								if ( substr( $name, 0, 4 ) == 'text' ) {
									if ( ! empty( $data['text'] ) AND strpos( $data['text'], '<strong' ) !== FALSE ) {
										if ( empty( $header_settings['data'][$name]['text_style'] ) ) {
											$header_settings['data'][$name]['text_style'] = array();
										}
										if ( ! in_array( 'bold', $header_settings['data'][$name]['text_style'] ) ) {
											$header_settings['data'][$name]['text_style'][] = 'bold';
										}
									}
									if ( ! empty( $data['icon'] ) ) {
										$translated_icon = $this->translate_icon_name( $data['icon'] );
										if ( $translated_icon != $data['icon'] ) {
											$header_settings['data'][$name]['icon'] = $translated_icon;
										}
									}
									$header_settings['data'][$name]['font'] = 'body';
									$header_changed = TRUE;
								// Dropdown element
								} elseif ( substr( $name, 0, 8 ) == 'dropdown' ) {
									if ( ! empty( $data['source'] ) AND $data['source'] = 'own' AND ! empty( $data['link_qty'] ) ) {
										$links = array();
										for ( $i = 0; $i < $data['link_qty']; $i ++ ) {
											$j = $i +1;
											$links[$i] = array(
												'label' => $data['link_' . $j . '_label'],
												'url' => $data['link_' . $j . '_url'],
											);
										}
										$header_settings['data'][$name]['links'] = $links;
										unset( $header_settings['data'][$name]['link_qty'] );
										$header_changed = TRUE;
									}
								// Cart element
								} elseif ( substr( $name, 0, 4 ) == 'cart' ) {
									if ( ! empty( $data['icon'] ) ) {
										$translated_icon = $this->translate_icon_name( $data['icon'] );
										if ( $translated_icon != $data['icon'] ) {
											$header_settings['data'][$name]['icon'] = $translated_icon;
											$header_changed = TRUE;
										}
									}
								// Button element
								} elseif ( substr( $name, 0, 3 ) == 'btn' ) {
									if ( ! empty( $data['icon'] ) ) {
										$translated_icon = $this->translate_icon_name( $data['icon'] );
										if ( $translated_icon != $data['icon'] ) {
											$header_settings['data'][$name]['icon'] = $translated_icon;
											$header_changed = TRUE;
										}
									}
								// Menu element
								} elseif ( substr( $name, 0, 4 ) == 'menu' ) {
									if ( $menu_font_family != '' ) {
										$header_settings['data'][$name]['font'] = $menu_font_family[0];
									} else {
										$header_settings['data'][$name]['font'] = 'body';
									}
									$header_changed = TRUE;
								// Social Links element
								} elseif ( substr( $name, 0, 7 ) == 'socials' ) {
									$social_items = array();
									foreach ( $this->old_social_links as $link_type => $link_title ) {
										if ( ! empty( $data[$link_type] ) ) {
											$social_items[] = array(
												'type' => $link_type,
												'url' => $data[$link_type],
											);
										}
									}
									if ( ! empty( $data['custom_url'] ) AND ! empty( $data['custom_icon'] ) ) {
										$translated_icon = $this->translate_icon_name( $data['custom_icon'] );
										$social_items[] = array(
											'type' => 'custom',
											'url' => $data['custom_url'],
											'icon' => $translated_icon,
											'title' => ( ! empty ( $data['custom_title'] ) ) ? $data['custom_title'] : '',
											'color' => ( ! empty ( $data['custom_color'] ) ) ? $data['custom_color'] : '#1abc9c',
										);
									}
									if ( count( $social_items ) > 0 ) {
										$header_settings['data'][$name]['items'] = $social_items;
									}
									$header_settings['data'][$name]['hover'] = 'fade';
									$header_settings['data'][$name]['shape'] = 'circle';
									$header_changed = TRUE;
								}
							}
						}

						if ( $header_changed ) {
							ob_start();
							wp_update_post(
								array(
									'ID' => $header->ID,
									'post_content' => str_replace( "\\'", "'", json_encode( wp_slash( $header_settings ), JSON_UNESCAPED_UNICODE ) ),
								)
							);
							ob_end_clean();
						}
					}
					catch ( Exception $e ) {
					}
				}
			}

			/*
			 * Menu Icons
			 */
			$menu_items = array();
			foreach ( get_terms( array( 'taxonomy' => 'nav_menu', 'hide_empty' => TRUE ) ) as $menu_obj ) {
				$menu_items = array_merge(
					$menu_items,
					wp_get_nav_menu_items( $menu_obj->term_id, array( 'post_status' => 'any' ) )
				);
			}
			foreach ($menu_items as $menu_item) {
				$updated_post = array();
				foreach ( array( 'post_title', 'title' ) as $field ) {
					if ( ! empty ( $menu_item->$field ) AND preg_match( '%<i[^>]class=["\']fa fa-([^"\']+)["\']%i', $menu_item->$field, $matches ) ) {
						$icon = $matches[1];
						$fa_match_found = FALSE;

						foreach ( $this->fa5_shim as $shim ) {
							if ( $icon == $shim[0] ) {
								if ( $shim[1] == NULL ) {
									$shim[1] = 'fas';
								}
								if ( $shim[2] == NULL ) {
									$shim[2] = $icon;
								}
								$icon = $shim[1] . ' fa-' . $shim[2];
								$fa_match_found = TRUE;
								break;
							}
						}

						if ( ! $fa_match_found ) {
							$icon = 'fas fa-' . $icon;
						}

						$updated_post[$field] = str_replace( 'fa fa-' . $matches[1], $icon, $menu_item->$field );

					}
				}
				if ( count( $updated_post ) > 0 ) {
					$updated_post['ID'] = $menu_item->ID;
					wp_update_post( $updated_post );
				}
			}

			if ( is_admin() ) {
				delete_transient( 'us_icons_migration_transient' );
			} else {
				set_transient( 'us_icons_migration_transient', 1, 5 * MINUTE_IN_SECONDS );
			}

		}

		/*
		 * Regenerate sizes data for images
		 */
		$attachments = get_posts(
			array(
				'post_type' => 'attachment',
				'posts_per_page' => - 1,
				'post_status' => 'any',
				'numberposts' => - 1,
			)
		);
		foreach ( $attachments as $attachment ) {
			$attachment_ID = $attachment->ID;
			if ( is_array( $imagedata = wp_get_attachment_metadata( $attachment_ID ) ) ) {
				if ( isset ( $imagedata['sizes']['medium_large'] ) ) {
					$imagedata['sizes']['us_768_0'] = $imagedata['sizes']['medium_large'];
				}
				wp_update_attachment_metadata( $attachment_ID, $imagedata );
			}
		}

		// Migrate CPT us_footer to us_page_block
		ob_start();
		register_post_type(
			'us_footer', array(
				'labels' => array(
					'name' => 'Page Blocks',
				),
				'public' => FALSE,
			)
		);

		// Deregister us_footer CPT
		global $wp_post_types;
		if ( isset( $wp_post_types['us_footer'] ) ) {
			unset( $wp_post_types['us_footer'] );
		}

		// In case WP mechanism didn't work, change us_footer post type to us_page_block with SQL
		global $wpdb;
		$wpdb_query = "UPDATE `" . $wpdb->posts . "` SET `post_type` = 'us_page_block' WHERE `post_type` = 'us_footer'";
		$wpdb->query( $wpdb_query );

		ob_end_clean();

		// Footer Settings
		$footer_fields_translate = array(
			// New field => Old field to copy from
			'footer_id' => 'footer_id',
			'footer_portfolio_id' => 'footer_portfolio_id',
			'footer_post_id' => 'footer_post_id',
			'footer_search_id' => 'footer_archive_id', // Dev note: footer_search_id should be set before footer_archive_id
			'footer_archive_id' => 'footer_archive_id',
			'footer_shop_id' => 'footer_shop_id',
			'footer_product_id' => 'footer_product_id',
		);

		foreach ( $footer_fields_translate as $new_field => $old_field ) {
			$defaults_field = str_replace( '_id', '_defaults', $old_field );
			if ( $old_field != 'footer_id' AND isset( $options[$defaults_field] ) AND $options[$defaults_field] ) {
				$options[$new_field] = '__defaults__';
			} else {
				if ( isset( $options[$old_field] ) ) {
					$args = array(
						'name' => $options[$old_field],
						'post_type' => 'us_page_block',
						'numberposts' => 1
					);
					$footer_post = get_posts( $args );
					if ( $footer_post ) {
						$footer_post = $footer_post[0];
						$options[$new_field] = $footer_post->ID;
					}
				}
			}
		}

		// Sidebar for Defaults
		$show_default_sidebar = TRUE;
		if ( isset( $options['sidebar'] ) AND $options['sidebar'] == 0 ) {
			$options['sidebar_id'] = '';
			$show_default_sidebar = FALSE;
		}

		// Sidebars for Portfolio Pages
		if ( isset( $options['portfolio_sidebar'] ) AND $options['portfolio_sidebar'] == 0 ) {
			if ( $show_default_sidebar ) {
				$options['sidebar_portfolio_id'] = '';
			}
		} else {
			$options['sidebar_portfolio_id'] = $options['portfolio_sidebar_id'];
		}
		$options['sidebar_portfolio_pos'] = $options['portfolio_sidebar_pos'];

		// Sidebars for Posts
		if ( isset( $options['post_sidebar'] ) AND $options['post_sidebar'] == 0 ) {
			if ( $show_default_sidebar ) {
				$options['sidebar_post_id'] = '';
			}
		} else {
			$options['sidebar_post_id'] = $options['post_sidebar_id'];
		}
		$options['sidebar_post_pos'] = $options['post_sidebar_pos'];

		// Sidebars for Blog Home page
		if ( isset( $options['blog_sidebar'] ) AND $options['blog_sidebar'] == 0 ) {
			if ( $show_default_sidebar ) {
				$options['sidebar_blog_id'] = '';
			}
		} else {
			$options['sidebar_blog_id'] = $options['blog_sidebar_id'];
		}
		$options['sidebar_blog_pos'] = $options['blog_sidebar_pos'];

		// Sidebars for Archives
		if ( isset( $options['archive_sidebar'] ) AND $options['archive_sidebar'] == 0 ) {
			if ( $show_default_sidebar ) {
				$options['sidebar_archive_id'] = '';
			}
		} else {
			$options['sidebar_archive_id'] = $options['archive_sidebar_id'];
		}
		$options['sidebar_archive_pos'] = $options['archive_sidebar_pos'];

		// Sidebars for Search Results
		if ( isset( $options['search_sidebar'] ) AND $options['search_sidebar'] == 0 ) {
			if ( $show_default_sidebar ) {
				$options['sidebar_search_id'] = '';
			}
		} else {
			$options['sidebar_search_id'] = $options['search_sidebar_id'];
		}
		$options['sidebar_search_pos'] = $options['search_sidebar_pos'];

		// Sidebars for Shop pages
		if ( isset( $options['shop_sidebar'] ) AND $options['shop_sidebar'] == 0 ) {
			if ( $show_default_sidebar ) {
				$options['sidebar_shop_id'] = '';
			}
		} else {
			$options['sidebar_shop_id'] = $options['shop_sidebar_id'];
		}
		$options['sidebar_shop_pos'] = $options['shop_sidebar_pos'];

		// Sidebars for Products
		if ( isset( $options['product_sidebar'] ) AND $options['product_sidebar'] == 0 ) {
			if ( $show_default_sidebar ) {
				$options['sidebar_product_id'] = '';
			}
		} else {
			$options['sidebar_product_id'] = $options['product_sidebar_id'];
		}
		$options['sidebar_product_pos'] = $options['product_sidebar_pos'];



		// Titlebar for Defaults
		$show_default_titlebar = FALSE;
		$post_title = 'Default Titlebar';
		$title = $description = NULL;
		$size = isset( $options['titlebar_size'] ) ? $options['titlebar_size'] : NULL;
		$color = isset( $options['titlebar_color'] ) ? $options['titlebar_color'] : NULL;
		$breadcrumbs = isset( $options['titlebar_breadcrumbs'] ) ? $options['titlebar_breadcrumbs'] : NULL;
		$bg_image = isset( $options['titlebar_bg_image'] ) ? $options['titlebar_bg_image'] : NULL;
		$bg_size = isset( $options['titlebar_bg_size'] ) ? $options['titlebar_bg_size'] : NULL;
		$bg_repeat = isset( $options['titlebar_bg_repeat'] ) ? $options['titlebar_bg_repeat'] : NULL;
		$bg_position = isset( $options['titlebar_bg_position'] ) ? $options['titlebar_bg_position'] : NULL;
		$bg_parallax = isset( $options['titlebar_bg_parallax'] ) ? $options['titlebar_bg_parallax'] : NULL;
		$bg_overlay = isset( $options['titlebar_overlay_color'] ) ? $options['titlebar_overlay_color'] : NULL;

		$titlebar_id = $this->add_titlebar( $post_title, $title, $description, $size, $color, $breadcrumbs, $bg_image, $bg_size, $bg_repeat, $bg_position, $bg_parallax, $bg_overlay );

		if ( isset( $options['titlebar'] ) AND $options['titlebar'] ) {
			$options['titlebar_id'] = $titlebar_id;
			$show_default_titlebar = TRUE;
		}

		// Titlebar for Portfolio Pages
		if ( isset( $options['titlebar_portfolio'] ) AND $options['titlebar_portfolio'] ) {

			if ( isset( $options['titlebar_portfolio_defaults'] ) AND $options['titlebar_portfolio_defaults'] == 0 ) {
				$post_title = 'Titlebar for Portfolio';
				$title = $description = NULL;
				$size = isset( $options['titlebar_portfolio_size'] ) ? $options['titlebar_portfolio_size'] : NULL;
				$color = isset( $options['titlebar_portfolio_color'] ) ? $options['titlebar_portfolio_color'] : NULL;
				$breadcrumbs = isset( $options['titlebar_portfolio_breadcrumbs'] ) ? $options['titlebar_portfolio_breadcrumbs'] : NULL;
				$bg_image = isset( $options['titlebar_portfolio_bg_image'] ) ? $options['titlebar_portfolio_bg_image'] : NULL;
				$bg_size = isset( $options['titlebar_portfolio_bg_size'] ) ? $options['titlebar_portfolio_bg_size'] : NULL;
				$bg_repeat = isset( $options['titlebar_portfolio_bg_repeat'] ) ? $options['titlebar_portfolio_bg_repeat'] : NULL;
				$bg_position = isset( $options['titlebar_portfolio_bg_position'] ) ? $options['titlebar_portfolio_bg_position'] : NULL;
				$bg_parallax = isset( $options['titlebar_portfolio_bg_parallax'] ) ? $options['titlebar_portfolio_bg_parallax'] : NULL;
				$bg_overlay = isset( $options['titlebar_portfolio_overlay_color'] ) ? $options['titlebar_portfolio_overlay_color'] : NULL;

				$titlebar_portfolio_id = $this->add_titlebar( $post_title, $title, $description, $size, $color, $breadcrumbs, $bg_image, $bg_size, $bg_repeat, $bg_position, $bg_parallax, $bg_overlay );

				$options['titlebar_portfolio_id'] = $titlebar_portfolio_id;
			} elseif ( ! $show_default_titlebar ) {
				$options['titlebar_portfolio_id'] = $titlebar_id;
			}

		} elseif ( $show_default_titlebar ) {
			$options['titlebar_portfolio_id'] = '';
		}

		// Titlebar for Posts
		if ( isset( $options['titlebar_post'] ) AND $options['titlebar_post'] ) {

			$post_title = 'Titlebar for Posts';
			$title = isset( $options['titlebar_post_title'] ) ? $options['titlebar_post_title'] : NULL;
			$description = NULL;

			if ( isset( $options['titlebar_post_defaults'] ) AND $options['titlebar_post_defaults'] == 0 ) {
				$size = isset( $options['titlebar_post_size'] ) ? $options['titlebar_post_size'] : NULL;
				$color = isset( $options['titlebar_post_color'] ) ? $options['titlebar_post_color'] : NULL;
				$breadcrumbs = isset( $options['titlebar_post_breadcrumbs'] ) ? $options['titlebar_post_breadcrumbs'] : NULL;
				$bg_image = isset( $options['titlebar_post_bg_image'] ) ? $options['titlebar_post_bg_image'] : NULL;
				$bg_size = isset( $options['titlebar_post_bg_size'] ) ? $options['titlebar_post_bg_size'] : NULL;
				$bg_repeat = isset( $options['titlebar_post_bg_repeat'] ) ? $options['titlebar_post_bg_repeat'] : NULL;
				$bg_position = isset( $options['titlebar_post_bg_position'] ) ? $options['titlebar_post_bg_position'] : NULL;
				$bg_parallax = isset( $options['titlebar_post_bg_parallax'] ) ? $options['titlebar_post_bg_parallax'] : NULL;
				$bg_overlay = isset( $options['titlebar_post_overlay_color'] ) ? $options['titlebar_post_overlay_color'] : NULL;
			} else {
				$size = isset( $options['titlebar_size'] ) ? $options['titlebar_size'] : NULL;
				$color = isset( $options['titlebar_color'] ) ? $options['titlebar_color'] : NULL;
				$breadcrumbs = isset( $options['titlebar_breadcrumbs'] ) ? $options['titlebar_breadcrumbs'] : NULL;
				$bg_image = isset( $options['titlebar_bg_image'] ) ? $options['titlebar_bg_image'] : NULL;
				$bg_size = isset( $options['titlebar_bg_size'] ) ? $options['titlebar_bg_size'] : NULL;
				$bg_repeat = isset( $options['titlebar_bg_repeat'] ) ? $options['titlebar_bg_repeat'] : NULL;
				$bg_position = isset( $options['titlebar_bg_position'] ) ? $options['titlebar_bg_position'] : NULL;
				$bg_parallax = isset( $options['titlebar_bg_parallax'] ) ? $options['titlebar_bg_parallax'] : NULL;
				$bg_overlay = isset( $options['titlebar_overlay_color'] ) ? $options['titlebar_overlay_color'] : NULL;
			}

			$titlebar_post_id = $this->add_titlebar( $post_title, $title, $description, $size, $color, $breadcrumbs, $bg_image, $bg_size, $bg_repeat, $bg_position, $bg_parallax, $bg_overlay );

			$options['titlebar_post_id'] = $titlebar_post_id;
		} elseif ( $show_default_titlebar ) {
			$options['titlebar_post_id'] = '';
		}

		// Titlebar for Archive pages
		if ( isset( $options['titlebar_archive'] ) AND $options['titlebar_archive'] ) {

			if ( isset( $options['titlebar_archive_defaults'] ) AND $options['titlebar_archive_defaults'] == 0 ) {
				$post_title = 'Titlebar for Archives';
				$title = $description = NULL;
				$size = isset( $options['titlebar_archive_size'] ) ? $options['titlebar_archive_size'] : NULL;
				$color = isset( $options['titlebar_archive_color'] ) ? $options['titlebar_archive_color'] : NULL;
				$breadcrumbs = isset( $options['titlebar_archive_breadcrumbs'] ) ? $options['titlebar_archive_breadcrumbs'] : NULL;
				$bg_image = isset( $options['titlebar_archive_bg_image'] ) ? $options['titlebar_archive_bg_image'] : NULL;
				$bg_size = isset( $options['titlebar_archive_bg_size'] ) ? $options['titlebar_archive_bg_size'] : NULL;
				$bg_repeat = isset( $options['titlebar_archive_bg_repeat'] ) ? $options['titlebar_archive_bg_repeat'] : NULL;
				$bg_position = isset( $options['titlebar_archive_bg_position'] ) ? $options['titlebar_archive_bg_position'] : NULL;
				$bg_parallax = isset( $options['titlebar_archive_bg_parallax'] ) ? $options['titlebar_archive_bg_parallax'] : NULL;
				$bg_overlay = isset( $options['titlebar_archive_overlay_color'] ) ? $options['titlebar_archive_overlay_color'] : NULL;

				$titlebar_archive_id = $this->add_titlebar( $post_title, $title, $description, $size, $color, $breadcrumbs, $bg_image, $bg_size, $bg_repeat, $bg_position, $bg_parallax, $bg_overlay );

				$options['titlebar_archive_id'] = $titlebar_archive_id;
				$options['titlebar_search_id'] = $titlebar_archive_id;
			} elseif ( ! $show_default_titlebar ) {
				$options['titlebar_archive_id'] = $titlebar_id;
				$options['titlebar_search_id'] = $titlebar_id;
			}

		} elseif ( $show_default_titlebar ) {
			$options['titlebar_archive_id'] = '';
			$options['titlebar_search_id'] = '';
		}
		unset( $options['titlebar_archive'] );
		unset( $options['titlebar_archive_defaults'] );
		unset( $options['titlebar_archive_size'] );
		unset( $options['titlebar_archive_color'] );
		unset( $options['titlebar_archive_breadcrumbs'] );
		unset( $options['titlebar_archive_bg_image'] );
		unset( $options['titlebar_archive_bg_size'] );
		unset( $options['titlebar_archive_bg_repeat'] );
		unset( $options['titlebar_archive_bg_position'] );
		unset( $options['titlebar_archive_bg_parallax'] );
		unset( $options['titlebar_archive_overlay_color'] );

		// Titlebar for Shop pages
		if ( isset( $options['titlebar_shop'] ) AND $options['titlebar_shop'] ) {

			if ( isset( $options['titlebar_shop_defaults'] ) AND $options['titlebar_shop_defaults'] == 0 ) {
				$post_title = 'Titlebar for Shop';
				$title = $description = NULL;
				$size = isset( $options['titlebar_shop_size'] ) ? $options['titlebar_shop_size'] : NULL;
				$color = isset( $options['titlebar_shop_color'] ) ? $options['titlebar_shop_color'] : NULL;
				$breadcrumbs = isset( $options['titlebar_shop_breadcrumbs'] ) ? $options['titlebar_shop_breadcrumbs'] : NULL;
				$bg_image = isset( $options['titlebar_shop_bg_image'] ) ? $options['titlebar_shop_bg_image'] : NULL;
				$bg_size = isset( $options['titlebar_shop_bg_size'] ) ? $options['titlebar_shop_bg_size'] : NULL;
				$bg_repeat = isset( $options['titlebar_shop_bg_repeat'] ) ? $options['titlebar_shop_bg_repeat'] : NULL;
				$bg_position = isset( $options['titlebar_shop_bg_position'] ) ? $options['titlebar_shop_bg_position'] : NULL;
				$bg_parallax = isset( $options['titlebar_shop_bg_parallax'] ) ? $options['titlebar_shop_bg_parallax'] : NULL;
				$bg_overlay = isset( $options['titlebar_shop_overlay_color'] ) ? $options['titlebar_shop_overlay_color'] : NULL;

				$titlebar_shop_id = $this->add_titlebar( $post_title, $title, $description, $size, $color, $breadcrumbs, $bg_image, $bg_size, $bg_repeat, $bg_position, $bg_parallax, $bg_overlay );

				$options['titlebar_shop_id'] = $titlebar_shop_id;
				$options['titlebar_product_id'] = $titlebar_shop_id;
			} elseif ( ! $show_default_titlebar ) {
				$options['titlebar_shop_id'] = $titlebar_id;
				$options['titlebar_product_id'] = $titlebar_id;
			}
			$options['shop_remove_title_breadcrumbs'] = 1;

		} elseif ( $show_default_titlebar ) {
			$options['titlebar_shop_id'] = '';
			$options['titlebar_product_id'] = '';
		}

		// Titlebar for Blog Home & Events
		if ( $show_default_titlebar ) {
			$options['titlebar_blog_id'] = '';
			$options['titlebar_tribe_events_id'] = '';
		}


		// Headers
		if ( isset( $options['header_portfolio_defaults'] ) AND $options['header_portfolio_defaults'] == 1 ) {
			$options['header_portfolio_id'] = '__defaults__';
		}
		if ( isset( $options['header_post_defaults'] ) AND $options['header_post_defaults'] == 1 ) {
			$options['header_post_id'] = '__defaults__';
		}
		if ( isset( $options['header_archive_defaults'] ) AND $options['header_archive_defaults'] == 1 ) {
			$options['header_archive_id'] = '__defaults__';
		}
		if ( isset( $options['header_shop_defaults'] ) AND $options['header_shop_defaults'] == 1 ) {
			$options['header_shop_id'] = '__defaults__';
		}
		if ( isset( $options['header_product_defaults'] ) AND $options['header_product_defaults'] == 1 ) {
			$options['header_product_id'] = $options['header_shop_id'];
		}

		return TRUE;
	}

	// Meta
	public function translate_meta( &$meta, $post_type ) {

		global $us_migration_current_post_id;

		// Get WooCommerce Shop page ID
		if ( $this->woocommerce_shop_page_id === NULL ) {
			$this->woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id', 0 );
		}

		if ( $post_type == 'us_portfolio' ) {
			// Portfolio Pages Excerpt
			if ( ! empty( $meta['us_tile_description'][0] ) ) {
				wp_update_post( array(
					'ID' => $us_migration_current_post_id,
					'post_excerpt' => $meta['us_tile_description'][0],
				) );
			}
			if ( isset( $meta['us_tile_description'][0] ) ) {
				unset( $meta['us_tile_description'] );
			}

			// Change portfolio type name to correspond to theme options
			$post_type = 'portfolio';

		// Change type from page to shop if the page is assigned as shop root for WooCommerce
		} elseif ( $post_type == 'page' AND $us_migration_current_post_id == $this->woocommerce_shop_page_id ) {
			$post_type = 'shop';
		}

		// Headers from metabox
		if ( ! empty( $meta['us_header'][0] ) AND $meta['us_header'][0] == 'custom' AND empty( $meta['us_header_id'][0] ) ) {
			if ( us_get_option( 'header_' . $post_type . '_id', '__defaults__' ) != '__defaults__' ) {
				$meta['us_header_id'][0] = us_get_option( 'header_' . $post_type . '_id' );
			} else {
				$meta['us_header_id'][0] = us_get_option( 'header_id' );
			}
		}

		// Custom Titlebar from metabox
		if ( ! empty( $meta['us_titlebar'][0] ) AND $meta['us_titlebar'][0] == 'custom' ) {

			// Check if the post has sidebar
			$has_sidebar = FALSE;
			if (
				// Set as custom in metabox
				( ! empty( $meta['us_sidebar'][0] ) AND $meta['us_sidebar'][0] == 'custom' )
				// For this post type the default value is used and the default value is not empty
				OR ( us_get_option( 'sidebar_' . $post_type . '_id', '__defaults__' ) == '__defaults__' AND us_get_option( 'sidebar_id', '' ) != '' )
				// For this post type the specific sidebar is set
				OR us_get_option( 'sidebar_' . $post_type . '_id', '' ) != ''
			) {
				$has_sidebar = TRUE;
			}
			$has_vc_row = FALSE;
			$post = get_post( $us_migration_current_post_id );
			if ( strpos( $post->post_content, '[vc_row' ) !== FALSE ) {
				$has_vc_row = TRUE;
			}

			// Titlebar data
			$title = ( $post_type == 'post' ) ? us_get_option( 'titlebar_post_title' ) : NULL;
			$description = isset( $meta['us_titlebar_subtitle'][0] ) ? $meta['us_titlebar_subtitle'][0] : '';

			// Change product type name to correspond to "shop" theme options
			if ( $post_type == 'product' ) {
				$post_type = 'shop';
			}

			if ( ! empty( $meta['us_titlebar_size'][0] ) ) {
				$size = $meta['us_titlebar_size'][0];
			} elseif ( us_get_option( 'titlebar_' . $post_type . '_defaults', 1 ) == 0 ) {
				$size = us_get_option( 'titlebar_' . $post_type . '_size' );
			} else {
				$size = us_get_option( 'titlebar_size' );
			}

			if ( ! empty( $meta['us_titlebar_color'][0] ) ) {
				$color = $meta['us_titlebar_color'][0];
			} elseif ( us_get_option( 'titlebar_' . $post_type . '_defaults', 1 ) == 0 ) {
				$color = us_get_option( 'titlebar_' . $post_type . '_color' );
			} else {
				$color = us_get_option( 'titlebar_color' );
			}

			if ( ! empty( $meta['us_titlebar_breadcrumbs'][0] ) ) {
				$breadcrumbs = ( $meta['us_titlebar_breadcrumbs'][0] == 'show' ) ? 1 : 0;
			} elseif ( us_get_option( 'titlebar_' . $post_type . '_defaults', 1 ) == 0 ) {
				$breadcrumbs = us_get_option( 'titlebar_' . $post_type . '_breadcrumbs' );
			} else {
				$breadcrumbs = us_get_option( 'titlebar_breadcrumbs' );
			}

			if ( ! empty( $meta['us_titlebar_image'][0] ) ) {
				$bg_image = $meta['us_titlebar_image'][0];
			} elseif ( us_get_option( 'titlebar_' . $post_type . '_defaults', 1 ) == 0 ) {
				$bg_image = us_get_option( 'titlebar_' . $post_type . '_bg_image' );
			} else {
				$bg_image = us_get_option( 'titlebar_bg_image' );
			}

			if ( ! empty( $meta['us_titlebar_bg_size'][0] ) ) {
				$bg_size = $meta['us_titlebar_bg_size'][0];
			} elseif ( us_get_option( 'titlebar_' . $post_type . '_defaults', 1 ) == 0 ) {
				$bg_size = us_get_option( 'titlebar_' . $post_type . '_bg_size' );
			} else {
				$bg_size = us_get_option( 'titlebar_bg_size' );
			}

			if ( ! empty( $meta['us_titlebar_bg_repeat'][0] ) ) {
				$bg_repeat = $meta['us_titlebar_bg_repeat'][0];
			} elseif ( us_get_option( 'titlebar_' . $post_type . '_defaults', 1 ) == 0 ) {
				$bg_repeat = us_get_option( 'titlebar_' . $post_type . '_bg_repeat' );
			} else {
				$bg_repeat = us_get_option( 'titlebar_bg_repeat' );
			}

			if ( ! empty( $meta['us_titlebar_bg_position'][0] ) ) {
				$bg_position = $meta['us_titlebar_bg_position'][0];
			} elseif ( us_get_option( 'titlebar_' . $post_type . '_defaults', 1 ) == 0 ) {
				$bg_position = us_get_option( 'titlebar_' . $post_type . '_bg_position' );
			} else {
				$bg_position = us_get_option( 'titlebar_bg_position' );
			}

			if ( ! empty( $meta['us_titlebar_bg_parallax'][0] ) ) {
				$bg_parallax = $meta['us_titlebar_bg_parallax'][0];
			} elseif ( us_get_option( 'titlebar_' . $post_type . '_defaults', 1 ) == 0 ) {
				$bg_parallax = us_get_option( 'titlebar_' . $post_type . '_bg_parallax' );
			} else {
				$bg_parallax = us_get_option( 'titlebar_bg_parallax' );
			}

			if ( ! empty( $meta['us_titlebar_overlay_color'][0] ) ) {
				$bg_overlay = $meta['us_titlebar_overlay_color'][0];
			} elseif ( us_get_option( 'titlebar_' . $post_type . '_defaults', 1 ) == 0 ) {
				$bg_overlay = us_get_option( 'titlebar_' . $post_type . '_overlay_color' );
			} else {
				$bg_overlay = us_get_option( 'titlebar_overlay_color' );
			}

			// If the post has sidebar or has no [vc_row] shortcode(s) in the content, we create titlebar post for it
			if ( $has_sidebar OR ( ! $has_vc_row ) ) {
				$the_post_title = get_the_title( $us_migration_current_post_id );
				if ( empty( $the_post_title ) ) {
					$the_post_title = '#' . $us_migration_current_post_id;
				}

				$post_title = 'Titlebar for page "' . $the_post_title . '"';
				$titlebar_meta_id = $this->add_titlebar( $post_title, $title, $description, $size, $color, $breadcrumbs, $bg_image, $bg_size, $bg_repeat, $bg_position, $bg_parallax, $bg_overlay );

				$meta['us_titlebar_id'][0] = $titlebar_meta_id;

			// If the post has no sidebar we prepend post's content with titlebar content
			} else {
				$titlebar_content = $this->generate_titlebar_content( $title, $description, $size, $color, $breadcrumbs, $bg_image, $bg_size, $bg_repeat, $bg_position, $bg_parallax, $bg_overlay );
				$content = $titlebar_content . $post->post_content;
				wp_update_post(
					array(
						'ID' => $post->ID,
						'post_content' => $content,
					)
				);

				$meta['us_titlebar'][0] = 'hide';
			}

			unset( $meta['us_titlebar_subtitle'] );
			unset( $meta['us_titlebar_size'] );
			unset( $meta['us_titlebar_color'] );
			unset( $meta['us_titlebar_breadcrumbs'] );
			unset( $meta['us_titlebar_image'] );
			unset( $meta['us_titlebar_bg_size'] );
			unset( $meta['us_titlebar_bg_repeat'] );
			unset( $meta['us_titlebar_bg_position'] );
			unset( $meta['us_titlebar_bg_parallax'] );
			unset( $meta['us_titlebar_overlay_color'] );
		}

		// Sidebars from metabox
		if ( ! empty( $meta['us_sidebar'][0] ) AND $meta['us_sidebar'][0] == 'custom' AND empty( $meta['us_sidebar_id'][0] ) ) {
			$meta['us_sidebar_id'][0] = 'default_sidebar';
		}

		// Footers from metabox
		if ( ! empty( $meta['us_footer'][0] ) AND $meta['us_footer'][0] == 'custom' ) {
			$args = array(
				'name' => $meta['us_footer_id'][0],
				'post_type' => 'us_page_block',
				'numberposts' => 1,
			);
			$footer_post = get_posts( $args );
			if ( $footer_post ) {
				$footer_post = $footer_post[0];
				$meta['us_footer_id'][0] = $footer_post->ID;
			}
		}

		return TRUE;
	}

	// Widgets
	public function translate_widgets( &$name, &$instance ) {
		$changed = FALSE;

		if ( $name == 'us_socials' ) {
			if ( ! empty( $instance['color'] ) AND $instance['color'] == 'desaturated' ) {
				$instance['style'] = 'solid';
				$instance['color'] = 'text';
			} elseif ( ! empty( $instance['color'] ) AND $instance['color'] == 'brand_inv' ) {
				$instance['style'] = 'colored';
			} elseif ( ! empty( $instance['color'] ) AND $instance['color'] == 'desaturated_inv' ) {
				$instance['style'] = 'solid';
				$instance['color'] = 'text';
			} else {
				$instance['style'] = 'solid';
			}
			$instance['hover'] = 'fade';
			$instance['shape'] = 'circle';

			if ( $instance['custom_icon'] != '' ) {
				$translated_icon = $this->translate_icon_name( $instance['custom_icon'] );
				if ( $translated_icon != $instance['custom_icon'] ) {
					$instance['custom_icon'] = $translated_icon;
				}
			}
			$changed = TRUE;
		} elseif ( $name == 'us_portfolio' ) {
			$instance['layout'] = 'portfolio_compact';
			$changed = TRUE;
		} elseif ( $name == 'us_blog' ) {
			global $migrated_blog_layouts_count;
			$migrated_blog_layouts_count = ( isset( $migrated_blog_layouts_count ) ) ? $migrated_blog_layouts_count + 1 : 1;

			// Find apropriate grid template to copy defaults from
			$blog_layout_name = isset( $instance['layout'] ) ? $instance['layout'] : 'classic';
			if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$blog_layout_name] ) ) {
				$layout = $templates_config[$blog_layout_name];

				// Set image size
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_image' ) {
						$img_size = ( in_array( $blog_layout_name, array( 'classic', 'tiles' ) ) ) ? 'medium' : 'thumbnail';
						$layout['data'][$elm_name]['thumbnail_size'] = $img_size;
						$layout['data'][$elm_name]['media_preview'] = 0;
						break;
					}
				}

				// Set Title size
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_title' ) {
						$layout['data'][$elm_name]['font_size'] = '1rem';
						$layout['data'][$elm_name]['design_options']['margin_bottom_default'] = '0';
						break;
					}
				}

				// Remove post content
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 12 ) == 'post_content' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}

				// Remove read more button element
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 3 ) == 'btn' ) {
						unset( $layout['data'][$elm_name] );
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
						}
						break;
					}
				}

				if ( ! isset( $instance['meta'] ) OR ! is_array( $instance['meta'] ) ) {
					$instance['meta'] = array();
				}

				// Remove post date element if it is hidden
				if ( ! in_array( 'date', $instance['meta'] ) ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( substr( $elm_name, 0, 9 ) == 'post_date' ) {
							unset( $layout['data'][$elm_name] );
							foreach ( $layout['default']['layout'] as $pos => $elms ) {
								if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
									unset( $layout['default']['layout'][$pos][$key] );
								}
							}
						}
					}
				}

				// Remove post author element if it is hidden
				if ( ! in_array( 'author', $instance['meta'] ) ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( substr( $elm_name, 0, 11 ) == 'post_author' ) {
							unset( $layout['data'][$elm_name] );
							foreach ( $layout['default']['layout'] as $pos => $elms ) {
								if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
									unset( $layout['default']['layout'][$pos][$key] );
								}
							}
							break;
						}
					}
				}

				// Remove post comments element if it is hidden
				if ( ! in_array( 'comments', $instance['meta'] ) ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( substr( $elm_name, 0, 13 ) == 'post_comments' ) {
							unset( $layout['data'][$elm_name] );
							foreach ( $layout['default']['layout'] as $pos => $elms ) {
								if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
									unset( $layout['default']['layout'][$pos][$key] );
								}
							}
							break;
						}
					}
				}

				// Remove post categories element if it is hidden
				if ( ! in_array( 'categories', $instance['meta'] ) ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'category' ) {
							unset( $layout['data'][$elm_name] );
							foreach ( $layout['default']['layout'] as $pos => $elms ) {
								if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
									unset( $layout['default']['layout'][$pos][$key] );
								}
							}
							break;
						}
					}
				}

				// Remove post tags element if it is hidden
				if ( ! in_array( 'tags', $instance['meta'] ) ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( substr( $elm_name, 0, 13 ) == 'post_taxonomy' AND $elm['taxonomy_name'] == 'post_tag' ) {
							unset( $layout['data'][$elm_name] );
							foreach ( $layout['default']['layout'] as $pos => $elms ) {
								if ( ( $key = array_search( $elm_name, $elms ) ) !== FALSE ) {
									unset( $layout['default']['layout'][$pos][$key] );
								}
							}
							break;
						}
					}
				}

				// Remove empty wrappers
				foreach ( $layout['default']['layout'] as $wrapper_name => $wrapper_elms ) {
					if ( ( substr( $wrapper_name, 0, 8 ) == 'hwrapper' OR substr( $wrapper_name, 0, 8 ) == 'vwrapper' ) AND count( $wrapper_elms ) == 0 ) {
						foreach ( $layout['data'] as $elm_name => $elm ) {
							if ( $elm_name == $wrapper_name ) {
								unset( $layout['data'][$elm_name] );
								break;
							}
						}
						foreach ( $layout['default']['layout'] as $pos => $elms ) {
							if ( ( $key = array_search( $wrapper_name, $elms ) ) !== FALSE ) {
								unset( $layout['default']['layout'][$pos][$key] );
							}
							if ( $pos == $wrapper_name ) {
								unset( $layout['default']['layout'][$pos] );
							}
						}
					}
				}

				// Fill missing values for the layout
				$layout = us_fix_grid_settings( $layout );

				// Create the grid layout post
				$layout_id = $this->add_grid_layout( 'layout_' . $migrated_blog_layouts_count, $layout['title'] . ' #' . $migrated_blog_layouts_count, $layout );

				// Set grid layout ID
				$instance['layout'] = $layout_id;

			}

			$changed = TRUE;
		}

		return $changed;
	}

	/*
	 * Additional migration functions
	 */

	// Create the Grid Layout post if it doesn't exist
	private function add_grid_layout( $name, $title, $content ) {
		global $us_migration_widget_layouts;
		if ( empty( $us_migration_widget_layouts ) ) {
			$us_migration_widget_layouts = array();
			$grid_posts = get_posts( array(
				'post_type' => 'us_grid_layout',
				'posts_per_page' => -1,
				'post_status' => 'publish',
			) );
			foreach ( $grid_posts as $grid_post ) {
				$us_migration_widget_layouts[$grid_post->ID] = $grid_post->post_content;
			}
		}

		if ( isset( $content['title'] ) ) {
			unset ( $content['title'] );
		}
		$content = json_encode( $content, JSON_UNESCAPED_UNICODE );

		foreach ( $us_migration_widget_layouts as $layout_id => $grid_layout ) {
			if ( $grid_layout == $content ) {
				return $layout_id;
			}
		}

		$layout_post_array = array(
			'post_type' => 'us_grid_layout',
			'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
			'post_name' => $name,
			'post_title' => $title,
			'post_content' => $content,
			'post_status' => 'publish',
		);
		ob_start();
		$layout_id = wp_insert_post( $layout_post_array );
		ob_end_clean();

		$us_migration_widget_layouts[$layout_id] = $content;

		return $layout_id;
	}

	// Grid Layouts for older Zephyr layouts
	private function get_grid_templates() {
		return array(

			// Blog Classic
			'classic' => array(
				'title' => 'Blog Classic',
				'data' => array(
					'post_image:1' => array(
						'media_preview' => 1,
						'design_options' => array(
							'margin_bottom_default' => '1rem',
						),
					),
					'post_title:1' => array(
						'design_options' => array(
							'margin_bottom_default' => '1rem',
						),
					),
					'post_date:1' => array(
						'icon' => 'material|access_time',
					),
					'post_author:1' => array(
						'icon' => 'material|person',
					),
					'post_taxonomy:1' => array(
						'taxonomy_name' => 'category',
						'icon' => 'material|folder-open',
					),
					'post_taxonomy:2' => array(
						'taxonomy_name' => 'post_tag',
						'icon' => 'material|turned_in_not',
					),
					'post_comments:1' => array(
						'icon' => 'material|comment',
					),
					'post_content:1' => array(
						'design_options' => array(
							'margin_top_default' => '1rem',
						),
					),
					'btn:1' => array(
						'design_options' => array(
							'margin_top_default' => '1.5rem',
						),
						'color_bg' => '#fff',
						'color_text' => '#222',
					),
					'hwrapper:1' => array(
						'wrap' => 1,
						'color_text' => us_get_option( 'color_content_faded' ),
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'post_image:1',
							1 => 'post_title:1',
							2 => 'hwrapper:1',
							3 => 'post_content:1',
							4 => 'btn:1',
						),
						'hwrapper:1' => array(
							0 => 'post_date:1',
							1 => 'post_author:1',
							2 => 'post_taxonomy:1',
							3 => 'post_taxonomy:2',
							4 => 'post_comments:1',
						),
					),
				),
			),

			// Blog Tiles
			'tiles' => array(
				'title' => 'Blog Tiles',
				'data' => array(
					'post_image:1' => array(
						'placeholder' => 1,
						'hover' => 1,
						'scale_hover' => '1.2',
					),
					'vwrapper:1' => array(
						'valign' => 'bottom',
						'bg_gradient' => 1,
						'design_options' => array(
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
							'padding_top_default' => '5rem',
							'padding_right_default' => '2rem',
							'padding_bottom_default' => '1.5rem',
							'padding_left_default' => '2rem',
						),
						'opacity' => '0',
						'transition_duration' => '0.45',
					),
					'post_title:1' => array(
						'text_styles' => array(
							0 => 'bold',
						),
						'color_text' => '#ffffff',
					),
					'hwrapper:1' => array(
						'wrap' => 1,
					),
					'post_date:1' => array(
						'font_size' => '0.9rem',
						'icon' => 'material|access_time',
						'color_text' => '#ffffff',
					),
					'post_author:1' => array(
						'font_size' => '0.9rem',
						'icon' => 'material|person',
						'color_text' => '#ffffff',
					),
					'post_comments:1' => array(
						'font_size' => '0.9rem',
						'icon' => 'material|comment',
						'color_text' => '#ffffff',
					),
					'post_taxonomy:1' => array(
						'taxonomy_name' => 'category',
						'style' => 'badge',
						'text_styles' => array(
							0 => 'bold',
							1 => 'uppercase',
						),
						'font_size' => '10px',
					),
					'post_taxonomy:2' => array(
						'taxonomy_name' => 'post_tag',
						'font_size' => '0.9rem',
						'icon' => 'material|turned_in_not',
						'color_text' => '#ffffff',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'post_image:1',
							1 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_taxonomy:1',
							1 => 'post_title:1',
							2 => 'hwrapper:1',
						),
						'hwrapper:1' => array(
							0 => 'post_date:1',
							1 => 'post_author:1',
							2 => 'post_taxonomy:2',
							3 => 'post_comments:1',
						),
					),
					'options' => array(
						'overflow' => 1,
					),
				),
			),

			// Blog Cards
			'flat' => array(
				'title' => 'Blog Cards',
				'data' => array(
					'post_image:1' => array(
						'media_preview' => 1,
					),
					'post_title:1' => array(
					),
					'vwrapper:1' => array(
						'design_options' => array(
							'padding_top_default' => '32px',
							'padding_right_default' => '40px',
							'padding_bottom_default' => '40px',
							'padding_left_default' => '40px',
						),
					),
					'hwrapper:1' => array(
						'wrap' => 1,
						'color_text' => us_get_option( 'color_content_faded' ),
					),
					'post_date:1' => array(
						'font_size' => '0.9rem',
						'icon' => 'material|access_time',
					),
					'post_author:1' => array(
						'font_size' => '0.9rem',
						'icon' => 'material|person',
					),
					'post_taxonomy:1' => array(
						'taxonomy_name' => 'category',
						'font_size' => '0.9rem',
						'icon' => 'material|folder-open',
					),
					'post_taxonomy:2' => array(
						'taxonomy_name' => 'post_tag',
						'font_size' => '0.9rem',
						'icon' => 'material|turned_in_not',
					),
					'post_comments:1' => array(
						'font_size' => '0.9rem',
						'icon' => 'material|comment',
					),
					'post_content:1' => array(
						'length' => '20',
					),
					'btn:1' => array(
						'design_options' => array(
							'margin_top_default' => '1rem',
						),
						'color_bg' => '#fff',
						'color_text' => '#222',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'post_image:1',
							2 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_title:1',
							1 => 'hwrapper:1',
							2 => 'post_content:1',
							3 => 'btn:1',
						),
						'hwrapper:1' => array(
							0 => 'post_date:1',
							1 => 'post_author:1',
							2 => 'post_taxonomy:1',
							3 => 'post_taxonomy:2',
							4 => 'post_comments:1',
						),
					),
					'options' => array(
						'overflow' => 1,
						'color_bg' => us_get_option( 'color_content_bg' ),
						'border_radius' => '0.15',
						'box_shadow' => '0.3',
						'box_shadow_hover' => '2',
					),
				),
			),

			// Blog Small Square Image
			'smallsquare' => array(
				'title' => 'Blog Small Square Image',
				'data' => array(
					'hwrapper:1' => array(
					),
					'post_image:1' => array(
						'placeholder' => 1,
						'thumbnail_size' => 'us_350_350_crop',
						'width' => '30%',
						'design_options' => array(
							'margin_right_default' => is_rtl() ? '0' : '5%',
							'margin_left_default' => is_rtl() ? '5%' : '0',
						),
					),
					'vwrapper:1' => array(
					),
					'post_title:1' => array(
					),
					'hwrapper:2' => array(
						'wrap' => 1,
						'color_text' => us_get_option( 'color_content_faded' ),
					),
					'post_date:1' => array(
						'icon' => 'material|access_time',
					),
					'post_author:1' => array(
						'icon' => 'material|person',
					),
					'post_taxonomy:1' => array(
						'taxonomy_name' => 'category',
						'icon' => 'material|folder-open',
					),
					'post_taxonomy:2' => array(
						'taxonomy_name' => 'post_tag',
						'icon' => 'material|turned_in_not',
					),
					'post_comments:1' => array(
						'icon' => 'material|comment',
					),
					'post_content:1' => array(
					),
					'btn:1' => array(
						'design_options' => array(
							'margin_top_default' => '1rem',
						),
						'color_bg' => '#fff',
						'color_text' => '#222',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'hwrapper:1',
						),
						'hwrapper:1' => array(
							0 => 'post_image:1',
							1 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_title:1',
							1 => 'hwrapper:2',
							2 => 'post_content:1',
							3 => 'btn:1',
						),
						'hwrapper:2' => array(
							0 => 'post_date:1',
							1 => 'post_author:1',
							2 => 'post_taxonomy:2',
							3 => 'post_taxonomy:1',
							4 => 'post_comments:1',
						),
					),
				),
			),

			// Blog Small Circle Image
			'smallcircle' => array(
				'title' => 'Blog Small Circle Image',
				'data' => array(
					'hwrapper:1' => array(
					),
					'post_image:1' => array(
						'placeholder' => 1,
						'circle' => 1,
						'thumbnail_size' => 'us_350_350_crop',
						'width' => '30%',
						'design_options' => array(
							'margin_right_default' => is_rtl() ? '0' : '5%',
							'margin_left_default' => is_rtl() ? '5%' : '0',
						),
					),
					'vwrapper:1' => array(
					),
					'post_title:1' => array(
					),
					'hwrapper:2' => array(
						'wrap' => 1,
						'color_text' => us_get_option( 'color_content_faded' ),
					),
					'post_date:1' => array(
						'icon' => 'material|access_time',
					),
					'post_author:1' => array(
						'icon' => 'material|person',
					),
					'post_taxonomy:1' => array(
						'taxonomy_name' => 'category',
						'icon' => 'material|folder-open',
					),
					'post_taxonomy:2' => array(
						'taxonomy_name' => 'post_tag',
						'icon' => 'material|turned_in_not',
					),
					'post_comments:1' => array(
						'icon' => 'material|comment',
					),
					'post_content:1' => array(
					),
					'btn:1' => array(
						'design_options' => array(
							'margin_top_default' => '1rem',
						),
						'color_bg' => '#fff',
						'color_text' => '#222',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'hwrapper:1',
						),
						'hwrapper:1' => array(
							0 => 'post_image:1',
							1 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_title:1',
							1 => 'hwrapper:2',
							2 => 'post_content:1',
							3 => 'btn:1',
						),
						'hwrapper:2' => array(
							0 => 'post_date:1',
							1 => 'post_author:1',
							2 => 'post_taxonomy:2',
							3 => 'post_taxonomy:1',
							4 => 'post_comments:1',
						),
					),
				),
			),

			// Blog Latest Posts
			'latest' => array(
				'title' => 'Blog Latest Posts',
				'data' => array(
					'hwrapper:1' => array(
					),
					'vwrapper:1' => array(
					),
					'post_title:1' => array(
					),
					'hwrapper:2' => array(
						'wrap' => 1,
						'color_text' => us_get_option( 'color_content_faded' ),
					),
					'post_date:1' => array(
						'format' => 'custom',
						'format_custom' => 'M',
						'text_styles' => array(
							0 => 'uppercase',
						),
						'font_size' => '14px',
						'design_options' => array(
							'margin_bottom_default' => '0',
						),
					),
					'post_date:2' => array(
						'format' => 'custom',
						'format_custom' => 'j',
						'text_styles' => array(
							0 => 'uppercase',
						),
						'font_size' => '26px',
						'design_options' => array(
							'margin_bottom_default' => '0',
						),
					),
					'post_author:1' => array(
						'icon' => 'material|person',
					),
					'post_taxonomy:1' => array(
						'taxonomy_name' => 'category',
						'icon' => 'material|folder-open',
					),
					'post_taxonomy:2' => array(
						'taxonomy_name' => 'post_tag',
						'icon' => 'material|turned_in_not',
					),
					'post_comments:1' => array(
						'icon' => 'material|comment',
					),
					'post_content:1' => array(
					),
					'vwrapper:2' => array(
						'alignment' => 'center',
						'valign' => 'middle',
						'color_bg' => us_get_option( 'color_content_bg_alt' ),
						'border_radius' => '5',
						'width' => '80px',
						'design_options' => array(
							'padding_top_default' => '12px',
							'padding_bottom_default' => '18px',
						),
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'hwrapper:1',
						),
						'hwrapper:1' => array(
							0 => 'vwrapper:2',
							1 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_title:1',
							1 => 'hwrapper:2',
							2 => 'post_content:1',
						),
						'hwrapper:2' => array(
							0 => 'post_author:1',
							1 => 'post_taxonomy:2',
							2 => 'post_taxonomy:1',
							3 => 'post_comments:1',
						),
						'vwrapper:2' => array(
							0 => 'post_date:1',
							1 => 'post_date:2',
						),
					),
				),
			),

			// Blog Compact
			'compact' => array(
				'title' => 'Blog Compact',
				'data' => array(
					'post_title:1' => array(
						'font_size' => '1rem',
						'design_options' => array(
							'margin_bottom_default' => '0.2rem',
						),
					),
					'hwrapper:1' => array(
						'wrap' => 1,
						'design_options' => array(
							'margin_bottom_default' => '0.2rem',
						),
						'color_text' => us_get_option( 'color_content_faded' ),
					),
					'post_date:1' => array(
					),
					'post_author:1' => array(
					),
					'post_comments:1' => array(
					),
					'post_taxonomy:1' => array(
						'taxonomy_name' => 'category',
					),
					'post_taxonomy:2' => array(
						'taxonomy_name' => 'post_tag',
					),
					'post_content:1' => array(
					),
					'btn:1' => array(
						'design_options' => array(
							'margin_top_default' => '1.5rem',
						),
						'color_bg' => '#fff',
						'color_text' => '#222',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'post_title:1',
							1 => 'hwrapper:1',
							2 => 'post_content:1',
							3 => 'btn:1',
						),
						'hwrapper:1' => array(
							0 => 'post_date:1',
							1 => 'post_author:1',
							2 => 'post_taxonomy:1',
							3 => 'post_taxonomy:2',
							4 => 'post_comments:1',
						),
					),
				),
			),

			// Portfolio 1
			'style_1' => array(
				'title' => 'Portfolio 1',
				'data' => array(
					'post_image:1' => array(
						'link' => 'none',
						'placeholder' => 1,
						'design_options' => array(
							'position_top_default' => '0',
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
						),
					),
					'post_custom_field:1' => array(
						'key' => 'custom',
						'link' => 'none',
						'design_options' => array(
							'position_top_default' => '0',
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
							'padding_top_default' => '2rem',
							'padding_right_default' => '2rem',
							'padding_bottom_default' => '2rem',
							'padding_left_default' => '2rem',
						),
						'color_bg' => 'inherit',
						'border_radius' => '50',
						'hover' => 1,
						'opacity' => '0',
						'scale' => '0',
						'scale_hover' => '1.5',
						'transition_duration' => '0.4',
					),
					'vwrapper:1' => array(
						'alignment' => 'center',
						'valign' => 'middle',
						'design_options' => array(
							'position_top_default' => '0',
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
							'padding_top_default' => '2rem',
							'padding_right_default' => '2rem',
							'padding_bottom_default' => '2rem',
							'padding_left_default' => '2rem',
						),
						'hover' => 1,
						'opacity' => '0',
						'scale' => '0',
						'scale_hover' => '1',
						'transition_duration' => '0.4',
					),
					'post_title:1' => array(
						'link' => 'none',
						'color_text' => 'inherit',
					),
					'post_date:1' => array(
						'font_size' => '13px',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'post_image:1',
							1 => 'post_custom_field:1',
							2 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_title:1',
							1 => 'post_date:1',
						),
					),
					'options' => array(
						'fixed' => 1,
						'link' => 'post',
					),
				),
			),

			// Portfolio 2
			'style_2' => array(
				'title' => 'Portfolio 2',
				'data' => array(
					'post_image:1' => array(
						'link' => 'none',
						'placeholder' => 1,
						'design_options' => array(
							'position_top_default' => '0',
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
						),
						'hover' => 1,
						'opacity_hover' => '0.15',
					),
					'vwrapper:1' => array(
						'bg_gradient' => 1,
						'design_options' => array(
							'position_top_default' => '50%',
							'position_right_default' => '0',
							'position_bottom_default' => '-1px',
							'position_left_default' => '0',
						),
						'hover' => 1,
						'opacity_hover' => '0',
					),
					'vwrapper:2' => array(
						'design_options' => array(
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
							'padding_right_default' => '2rem',
							'padding_bottom_default' => '1.5rem',
							'padding_left_default' => '2rem',
						),
						'color_text' => '#fff',
						'hover' => 1,
						'color_text_hover' => 'inherit',
						'translateY_hover' => '-20',
					),
					'post_title:1' => array(
						'link' => 'none',
						'color_text' => 'inherit',
					),
					'post_date:1' => array(
						'font_size' => '13px',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'post_image:1',
							1 => 'vwrapper:1',
							2 => 'vwrapper:2',
						),
						'vwrapper:2' => array(
							0 => 'post_title:1',
							1 => 'post_date:1',
						),
					),
					'options' => array(
						'fixed' => 1,
						'link' => 'post',
					),
				),
			),

			// Portfolio 3
			'style_3' => array(
				'title' => 'Portfolio 3',
				'data' => array(
					'post_image:1' => array(
						'link' => 'none',
						'placeholder' => 1,
						'design_options' => array(
							'position_top_default' => '-1px',
							'position_right_default' => '-1px',
							'position_bottom_default' => '-1px',
							'position_left_default' => '-1px',
						),
						'hover' => 1,
						'opacity' => '0.35',
						'transition_duration' => '0.4',
					),
					'vwrapper:1' => array(
						'alignment' => 'center',
						'valign' => 'middle',
						'design_options' => array(
							'position_top_default' => '0',
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
							'padding_top_default' => '2rem',
							'padding_right_default' => '2rem',
							'padding_bottom_default' => '2rem',
							'padding_left_default' => '2rem',
						),
					),
					'post_title:1' => array(
						'link' => 'none',
						'color_text' => 'inherit',
						'hover' => 1,
						'opacity_hover' => '0',
						'translateY_hover' => '-100',
					),
					'post_date:1' => array(
						'font_size' => '13px',
						'hover' => 1,
						'opacity' => '0.66',
						'opacity_hover' => '0',
						'translateY_hover' => '100',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'post_image:1',
							1 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_title:1',
							1 => 'post_date:1',
						),
					),
					'options' => array(
						'fixed' => 1,
						'link' => 'post',
					),
				),
			),

			// Portfolio 4
			'style_4' => array(
				'title' => 'Portfolio 4',
				'data' => array(
					'post_image:1' => array(
						'link' => 'none',
						'placeholder' => 1,
						'design_options' => array(
							'position_top_default' => '0',
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
						),
					),
					'vwrapper:1' => array(
						'alignment' => 'center',
						'valign' => 'middle',
						'design_options' => array(
							'position_top_default' => '0',
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
							'padding_top_default' => '2rem',
							'padding_right_default' => '2rem',
							'padding_bottom_default' => '2rem',
							'padding_left_default' => '2rem',
						),
						'color_bg' => 'rgba(0,0,0,0.66)',
						'color_text' => '#fff',
						'hover' => 1,
						'opacity' => '0',
						'scale' => '1.5',
					),
					'post_title:1' => array(
						'link' => 'none',
						'color_text' => 'inherit',
					),
					'post_date:1' => array(
						'font_size' => '13px',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'post_image:1',
							1 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_title:1',
							1 => 'post_date:1',
						),
					),
					'options' => array(
						'fixed' => 1,
						'link' => 'post',
					),
				),
			),

			// Portfolio 5
			'style_5' => array(
				'title' => 'Portfolio 5',
				'data' => array(
					'post_image:1' => array(
						'link' => 'none',
						'placeholder' => 1,
						'design_options' => array(
							'position_top_default' => '0',
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
						),
						'hover' => 1,
						'translateY_hover' => '-10',
					),
					'vwrapper:1' => array(
						'alignment' => 'center',
						'valign' => 'middle',
						'design_options' => array(
							'position_top_default' => '0',
							'position_right_default' => '0',
							'position_bottom_default' => '0',
							'position_left_default' => '0',
							'padding_top_default' => '2rem',
							'padding_right_default' => '2rem',
							'padding_bottom_default' => '2rem',
							'padding_left_default' => '2rem',
						),
						'color_bg' => 'inherit',
						'hover' => 1,
						'translateY' => '100',
					),
					'post_title:1' => array(
						'link' => 'none',
						'color_text' => 'inherit',
					),
					'post_date:1' => array(
						'font_size' => '13px',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'post_image:1',
							1 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_title:1',
							1 => 'post_date:1',
						),
					),
					'options' => array(
						'fixed' => 1,
						'link' => 'post',
					),
				),
			),

			// Testimonial 1
			'testimonial_1' => array(
				'title' => 'Testimonial Card Style',
				'data' => array(
					'post_content:1' => array(
						'type' => 'full_content',
					),
					'hwrapper:1' => array(
						'valign' => 'middle',
						'design_options' => array(
							'margin_top_default' => '1rem',
						),
					),
					'post_image:1' => array(
						'link' => 'custom',
						'custom_link' => array(
							'url' => '{{us_testimonial_link}}',
							'target' => '',
						),
						'circle' => 1,
						'thumbnail_size' => 'thumbnail',
						'width' => '4rem',
						'design_options' => array(
							'margin_right_default' => '1rem',
						),
					),
					'vwrapper:1' => array(
					),
					'post_custom_field:1' => array(
						'key' => 'us_testimonial_author',
						'link' => 'custom',
						'custom_link' => array(
							'url' => '{{us_testimonial_link}}',
							'target' => '',
						),
						'color_link' => '0',
						'text_styles' => array(
							0 => 'bold',
						),
						'design_options' => array(
							'margin_bottom_default' => '0',
						),
					),
					'post_custom_field:2' => array(
						'key' => 'us_testimonial_role',
						'font_size' => '0.9rem',
						'color_text' => us_get_option( 'color_content_faded' ),
					),
					'vwrapper:2' => array(
						'design_options' => array(
							'padding_top_default' => '2rem',
							'padding_right_default' => '2rem',
							'padding_bottom_default' => '2rem',
							'padding_left_default' => '2rem',
						),
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'vwrapper:2',
						),
						'hwrapper:1' => array(
							0 => 'post_image:1',
							1 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_custom_field:1',
							1 => 'post_custom_field:2',
						),
						'vwrapper:2' => array(
							0 => 'post_content:1',
							1 => 'hwrapper:1',
						),
					),
					'options' => array(
						'overflow' => 1,
						'color_bg' => us_get_option( 'color_content_bg_alt' ),
						'border_radius' => '0.15',
						'box_shadow' => '0.3',
						'box_shadow_hover' => '2',
					),
				),
			),

			'testimonial_2' => array(
				'title' => 'Testimonial Flat Style',
				'data' => array(
					'post_content:1' => array(
						'type' => 'full_content',
					),
					'hwrapper:1' => array(
						'valign' => 'middle',
					),
					'post_image:1' => array(
						'link' => 'custom',
						'custom_link' => array(
							'url' => '{{us_testimonial_link}}',
							'target' => '',
						),
						'circle' => 1,
						'thumbnail_size' => 'thumbnail',
						'width' => '4rem',
						'design_options' => array(
							'margin_right_default' => '1rem',
						),
					),
					'vwrapper:1' => array(
					),
					'post_custom_field:1' => array(
						'key' => 'us_testimonial_author',
						'link' => 'custom',
						'custom_link' => array(
							'url' => '{{us_testimonial_link}}',
							'target' => '',
						),
						'color_link' => '0',
						'text_styles' => array(
							0 => 'bold',
						),
						'design_options' => array(
							'margin_bottom_default' => '0',
						),
					),
					'post_custom_field:2' => array(
						'key' => 'us_testimonial_role',
						'font_size' => '0.9rem',
						'color_text' => us_get_option( 'color_content_faded' ),
					),
					'vwrapper:2' => array(
						'design_options' => array(
							'padding_left_default' => '5.6rem',
						),
					),
					'post_custom_field:3' => array(
						'key' => 'custom',
						'font_size' => '7rem',
						'icon' => 'material|format_quote',
						'design_options' => array(
							'position_top_default' => '1rem',
							'position_left_default' => '-1.4rem',
						),
						'hover' => 1,
						'opacity' => '0.2',
						'opacity_hover' => '0.2',
					),
				),
				'default' => array(
					'layout' => array(
						'middle_center' => array(
							0 => 'vwrapper:2',
						),
						'hwrapper:1' => array(
							0 => 'post_image:1',
							1 => 'vwrapper:1',
						),
						'vwrapper:1' => array(
							0 => 'post_custom_field:1',
							1 => 'post_custom_field:2',
						),
						'vwrapper:2' => array(
							0 => 'post_custom_field:3',
							1 => 'post_content:1',
							2 => 'hwrapper:1',
						),
					),
				),
			),

		);
	}

	// Migrate FA icon names
	public function translate_icon_name( $icon ) {
		if ( trim( $icon ) == '' OR preg_match( '/(fas|far|fal|fab|material)\|[a-z0-9-]/i', $icon ) ) {
			return $icon;
		}
		$icon = trim( $icon );

		if ( substr( $icon, 0, 3 ) == 'fa-' || substr( $icon, 0, 6 ) == 'fa fa-' ) {
			$fa_match_found = FALSE;
			if ( substr( $icon, 0, 3 ) == 'fa-' ) {
				$icon = substr( $icon, 3 );
			} elseif ( substr( $icon, 0, 6 ) == 'fa fa-' ) {
				$icon = substr( $icon, 6 );
			}
			foreach ( $this->fa5_shim as $shim ) {
				if ( $icon == $shim[0] ) {
					if ( $shim[1] == NULL ) {
						$shim[1] = 'fas';
					}
					if ( $shim[2] == NULL ) {
						$shim[2] = $icon;
					}
					$icon = $shim[1] . '|' . $shim[2];
					$fa_match_found = TRUE;
					break;
				}
			}
			if ( ! $fa_match_found ) {
				$icon = 'fas|' . $icon;
			}
		} else {
			$icon = 'material|' . $icon;
		}

		return $icon;
	}

	// Create the Titlebar post if it doesn't exist
	private function add_titlebar(
		// Default values
		$post_title = 'Default Titlebar',
		$title = '', // can be set in Theme Options > Title Bars > Posts only
		$description = '', // can be set in metabox only
		$size = 'small',
		$color = 'default',
		$breadcrumbs = TRUE,
		$bg_image = '',
		$bg_size = 'cover',
		$bg_repeat = 'repeat',
		$bg_position = 'center center',
		$bg_parallax = 'none',
		$bg_overlay = ''
	) {
		global $us_migration_page_blocks;
		if ( empty( $us_migration_page_blocks ) ) {
			$us_migration_page_blocks = array();
			$block_posts = get_posts(
				array(
					'post_type' => 'us_page_block',
					'posts_per_page' => -1,
					'post_status' => 'publish',
				)
			);
			foreach ( $block_posts as $block_post ) {
				$us_migration_page_blocks[$block_post->ID] = $block_post->post_content;
			}
		}

		$post_content = $this->generate_titlebar_content( $title, $description, $size, $color, $breadcrumbs, $bg_image, $bg_size, $bg_repeat, $bg_position, $bg_parallax, $bg_overlay );

		foreach ( $us_migration_page_blocks as $block_post_id => $block_post_content ) {
			if ( $post_content == $block_post_content ) {
				return $block_post_id;
			}
		}

		$block_post_array = array(
			'post_type' => 'us_page_block',
			'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
			'post_title' => $post_title,
			'post_content' => $post_content,
			'post_status' => 'publish',
		);
		ob_start();
		$block_post_id = wp_insert_post( $block_post_array );
		ob_end_clean();

		$us_migration_page_blocks[$block_post_id] = $post_content;

		return $block_post_id;
	}

	// Generate content for Page Block based on Title Bar settings
	private function generate_titlebar_content(
		// Default values
		$title = '', // can be set in Theme Options > Title Bars > Posts only
		$description = '', // can be set in metabox only
		$size = 'small',
		$color = 'default',
		$breadcrumbs = TRUE,
		$bg_image = '',
		$bg_size = 'cover',
		$bg_repeat = 'repeat',
		$bg_position = 'center center',
		$bg_parallax = 'none',
		$bg_overlay = ''
	) {
		// Row
		$content = '[vc_row content_placement="middle"';
		if ( $size == 'huge' ) {
			$content .= ' height="large"';
		} elseif ( $size == 'large' ) {
			$content .= ' height="medium"';
		} else {
			$content .= ' height="small"';
		}
		if ( $color == 'default' ) {
			$content .= ' css=".vc_custom_777{padding-bottom: 0px !important;}"';
		} else {
			$content .= ' color_scheme="' . $color . '"';
		}
		if ( $bg_image != '' ) {
			$content .= ' us_bg_image="' . intval( $bg_image ) . '"';
			$content .= ' us_bg_size="' . $bg_size . '"';
			$content .= ' us_bg_repeat="' . $bg_repeat . '"';
			$content .= ' us_bg_pos="' . $bg_position . '"';
			if ( $bg_parallax == 'vertical_reversed' ) {
				$content .= ' us_bg_parallax="vertical" us_bg_parallax_reverse="1"';
			} else {
				$content .= ' us_bg_parallax="' . $bg_parallax . '"';
			}
		}
		if ( $bg_overlay != '' ) {
			$content .= ' us_bg_overlay_color="' . $bg_overlay . '"';
		}
		$content .= ']';

		// First column
		if ( $breadcrumbs AND in_array( $size, array( 'small', 'medium' ) ) ) {
			$content .= '[vc_column width="1/2"]';
		} else {
			$content .= '[vc_column]';
		}

		// Page Title
		if ( $title == '' ) {
			$content .= '[us_page_title description="1"';
			if ( in_array( $size, array( 'small', 'medium' ) ) ) {
				$content .= ' font_size="1.8rem" inline="1"';
			} elseif ( $size == 'huge' ) {
				$content .= ' font_size="3rem" line_height="1.1" align="center"';
			} else {
				$content .= ' align="center"';
			}
			$content .= ']';
		} else {
			$content .= '[vc_column_text]<h1 style="';
			if ( in_array( $size, array( 'small', 'medium' ) ) ) {
				$content .= 'font-size: 1.8rem;">';
			} else {
				$content .= 'text-align: center;">';
			}
			$content .= $title;
			$content .= '</h1>[/vc_column_text]';
		}

		// Description
		if ( $description != '' ) {
			$content .= '[vc_column_text]';
			if ( in_array( $size, array( 'small', 'medium' ) ) ) {
				$content .= $description;
			} else {
				$content .= '<p style="text-align: center;">' . $description . '</p>';
			}
			$content .= '[/vc_column_text]';
		}

		// Second column
		if ( $breadcrumbs AND in_array( $size, array( 'small', 'medium' ) ) ) {
			$content .= '[/vc_column][vc_column width="1/2"]';
		}

		// Breadcrumbs
		if ( $breadcrumbs ) {
			$content .= '[us_breadcrumbs show_current="1" font_size="0.9rem" separator_icon="material|chevron_right"';
			if ( in_array( $size, array( 'small', 'medium' ) ) ) {
				$content .= ' align="right"]';
			} else {
				$content .= ' align="center"]';
			}
		}

		$content .= '[/vc_column]';
		$content .= '[/vc_row]';

		return $content;
	}

	private $old_social_links = array(
		'email' => 'Email',
		'facebook' => 'Facebook',
		'twitter' => 'Twitter',
		'google' => 'Google+',
		'linkedin' => 'LinkedIn',
		'youtube' => 'YouTube',
		'vimeo' => 'Vimeo',
		'flickr' => 'Flickr',
		'behance' => 'Behance',
		'instagram' => 'Instagram',
		'xing' => 'Xing',
		'pinterest' => 'Pinterest',
		'skype' => 'Skype',
		'whatsapp' => 'WhatsApp',
		'dribbble' => 'Dribbble',
		'vk' => 'Vkontakte',
		'tumblr' => 'Tumblr',
		'soundcloud' => 'SoundCloud',
		'twitch' => 'Twitch',
		'yelp' => 'Yelp',
		'deviantart' => 'DeviantArt',
		'foursquare' => 'Foursquare',
		'github' => 'GitHub',
		'odnoklassniki' => 'Odnoklassniki',
		's500px' => '500px',
		'houzz' => 'Houzz',
		'medium' => 'Medium',
		'tripadvisor' => 'Tripadvisor',
		'rss' => 'RSS',
	);

	private $fa5_shim = array(
		0 => array(
			0 => 'glass',
			1 => NULL,
			2 => 'glass-martini',
		),
		1 => array(
			0 => 'meetup',
			1 => 'fab',
			2 => NULL,
		),
		2 => array(
			0 => 'star-o',
			1 => 'far',
			2 => 'star',
		),
		3 => array(
			0 => 'remove',
			1 => NULL,
			2 => 'times',
		),
		4 => array(
			0 => 'close',
			1 => NULL,
			2 => 'times',
		),
		5 => array(
			0 => 'gear',
			1 => NULL,
			2 => 'cog',
		),
		6 => array(
			0 => 'trash-o',
			1 => 'far',
			2 => 'trash-alt',
		),
		7 => array(
			0 => 'file-o',
			1 => 'far',
			2 => 'file',
		),
		8 => array(
			0 => 'clock-o',
			1 => 'far',
			2 => 'clock',
		),
		9 => array(
			0 => 'arrow-circle-o-down',
			1 => 'far',
			2 => 'arrow-alt-circle-down',
		),
		10 => array(
			0 => 'arrow-circle-o-up',
			1 => 'far',
			2 => 'arrow-alt-circle-up',
		),
		11 => array(
			0 => 'play-circle-o',
			1 => 'far',
			2 => 'play-circle',
		),
		12 => array(
			0 => 'repeat',
			1 => NULL,
			2 => 'redo',
		),
		13 => array(
			0 => 'rotate-right',
			1 => NULL,
			2 => 'redo',
		),
		14 => array(
			0 => 'refresh',
			1 => NULL,
			2 => 'sync',
		),
		15 => array(
			0 => 'list-alt',
			1 => 'far',
			2 => NULL,
		),
		16 => array(
			0 => 'dedent',
			1 => NULL,
			2 => 'outdent',
		),
		17 => array(
			0 => 'video-camera',
			1 => NULL,
			2 => 'video',
		),
		18 => array(
			0 => 'picture-o',
			1 => 'far',
			2 => 'image',
		),
		19 => array(
			0 => 'photo',
			1 => 'far',
			2 => 'image',
		),
		20 => array(
			0 => 'image',
			1 => 'far',
			2 => 'image',
		),
		21 => array(
			0 => 'pencil',
			1 => NULL,
			2 => 'pencil-alt',
		),
		22 => array(
			0 => 'map-marker',
			1 => NULL,
			2 => 'map-marker-alt',
		),
		23 => array(
			0 => 'pencil-square-o',
			1 => 'far',
			2 => 'edit',
		),
		24 => array(
			0 => 'share-square-o',
			1 => 'far',
			2 => 'share-square',
		),
		25 => array(
			0 => 'check-square-o',
			1 => 'far',
			2 => 'check-square',
		),
		26 => array(
			0 => 'arrows',
			1 => NULL,
			2 => 'arrows-alt',
		),
		27 => array(
			0 => 'times-circle-o',
			1 => 'far',
			2 => 'times-circle',
		),
		28 => array(
			0 => 'check-circle-o',
			1 => 'far',
			2 => 'check-circle',
		),
		29 => array(
			0 => 'mail-forward',
			1 => NULL,
			2 => 'share',
		),
		30 => array(
			0 => 'eye-slash',
			1 => 'far',
			2 => NULL,
		),
		31 => array(
			0 => 'warning',
			1 => NULL,
			2 => 'exclamation-triangle',
		),
		32 => array(
			0 => 'calendar',
			1 => NULL,
			2 => 'calendar-alt',
		),
		33 => array(
			0 => 'arrows-v',
			1 => NULL,
			2 => 'arrows-alt-v',
		),
		34 => array(
			0 => 'arrows-h',
			1 => NULL,
			2 => 'arrows-alt-h',
		),
		35 => array(
			0 => 'bar-chart',
			1 => 'far',
			2 => 'chart-bar',
		),
		36 => array(
			0 => 'bar-chart-o',
			1 => 'far',
			2 => 'chart-bar',
		),
		37 => array(
			0 => 'twitter-square',
			1 => 'fab',
			2 => NULL,
		),
		38 => array(
			0 => 'facebook-square',
			1 => 'fab',
			2 => NULL,
		),
		39 => array(
			0 => 'gears',
			1 => NULL,
			2 => 'cogs',
		),
		40 => array(
			0 => 'thumbs-o-up',
			1 => 'far',
			2 => 'thumbs-up',
		),
		41 => array(
			0 => 'thumbs-o-down',
			1 => 'far',
			2 => 'thumbs-down',
		),
		42 => array(
			0 => 'heart-o',
			1 => 'far',
			2 => 'heart',
		),
		43 => array(
			0 => 'sign-out',
			1 => NULL,
			2 => 'sign-out-alt',
		),
		44 => array(
			0 => 'linkedin-square',
			1 => 'fab',
			2 => 'linkedin',
		),
		45 => array(
			0 => 'thumb-tack',
			1 => NULL,
			2 => 'thumbtack',
		),
		46 => array(
			0 => 'external-link',
			1 => NULL,
			2 => 'external-link-alt',
		),
		47 => array(
			0 => 'sign-in',
			1 => NULL,
			2 => 'sign-in-alt',
		),
		48 => array(
			0 => 'github-square',
			1 => 'fab',
			2 => NULL,
		),
		49 => array(
			0 => 'lemon-o',
			1 => 'far',
			2 => 'lemon',
		),
		50 => array(
			0 => 'square-o',
			1 => 'far',
			2 => 'square',
		),
		51 => array(
			0 => 'bookmark-o',
			1 => 'far',
			2 => 'bookmark',
		),
		52 => array(
			0 => 'twitter',
			1 => 'fab',
			2 => NULL,
		),
		53 => array(
			0 => 'facebook',
			1 => 'fab',
			2 => 'facebook-f',
		),
		54 => array(
			0 => 'facebook-f',
			1 => 'fab',
			2 => 'facebook-f',
		),
		55 => array(
			0 => 'github',
			1 => 'fab',
			2 => NULL,
		),
		56 => array(
			0 => 'credit-card',
			1 => 'far',
			2 => NULL,
		),
		57 => array(
			0 => 'feed',
			1 => NULL,
			2 => 'rss',
		),
		58 => array(
			0 => 'hdd-o',
			1 => 'far',
			2 => 'hdd',
		),
		59 => array(
			0 => 'hand-o-right',
			1 => 'far',
			2 => 'hand-point-right',
		),
		60 => array(
			0 => 'hand-o-left',
			1 => 'far',
			2 => 'hand-point-left',
		),
		61 => array(
			0 => 'hand-o-up',
			1 => 'far',
			2 => 'hand-point-up',
		),
		62 => array(
			0 => 'hand-o-down',
			1 => 'far',
			2 => 'hand-point-down',
		),
		63 => array(
			0 => 'arrows-alt',
			1 => NULL,
			2 => 'expand-arrows-alt',
		),
		64 => array(
			0 => 'group',
			1 => NULL,
			2 => 'users',
		),
		65 => array(
			0 => 'chain',
			1 => NULL,
			2 => 'link',
		),
		66 => array(
			0 => 'scissors',
			1 => NULL,
			2 => 'cut',
		),
		67 => array(
			0 => 'files-o',
			1 => 'far',
			2 => 'copy',
		),
		68 => array(
			0 => 'floppy-o',
			1 => 'far',
			2 => 'save',
		),
		69 => array(
			0 => 'navicon',
			1 => NULL,
			2 => 'bars',
		),
		70 => array(
			0 => 'reorder',
			1 => NULL,
			2 => 'bars',
		),
		71 => array(
			0 => 'pinterest',
			1 => 'fab',
			2 => NULL,
		),
		72 => array(
			0 => 'pinterest-square',
			1 => 'fab',
			2 => NULL,
		),
		73 => array(
			0 => 'google-plus-square',
			1 => 'fab',
			2 => NULL,
		),
		74 => array(
			0 => 'google-plus',
			1 => 'fab',
			2 => 'google-plus-g',
		),
		75 => array(
			0 => 'money',
			1 => 'far',
			2 => 'money-bill-alt',
		),
		76 => array(
			0 => 'unsorted',
			1 => NULL,
			2 => 'sort',
		),
		77 => array(
			0 => 'sort-desc',
			1 => NULL,
			2 => 'sort-down',
		),
		78 => array(
			0 => 'sort-asc',
			1 => NULL,
			2 => 'sort-up',
		),
		79 => array(
			0 => 'linkedin',
			1 => 'fab',
			2 => 'linkedin-in',
		),
		80 => array(
			0 => 'rotate-left',
			1 => NULL,
			2 => 'undo',
		),
		81 => array(
			0 => 'legal',
			1 => NULL,
			2 => 'gavel',
		),
		82 => array(
			0 => 'tachometer',
			1 => NULL,
			2 => 'tachometer-alt',
		),
		83 => array(
			0 => 'dashboard',
			1 => NULL,
			2 => 'tachometer-alt',
		),
		84 => array(
			0 => 'comment-o',
			1 => 'far',
			2 => 'comment',
		),
		85 => array(
			0 => 'comments-o',
			1 => 'far',
			2 => 'comments',
		),
		86 => array(
			0 => 'flash',
			1 => NULL,
			2 => 'bolt',
		),
		87 => array(
			0 => 'clipboard',
			1 => 'far',
			2 => NULL,
		),
		88 => array(
			0 => 'paste',
			1 => 'far',
			2 => 'clipboard',
		),
		89 => array(
			0 => 'lightbulb-o',
			1 => 'far',
			2 => 'lightbulb',
		),
		90 => array(
			0 => 'exchange',
			1 => NULL,
			2 => 'exchange-alt',
		),
		91 => array(
			0 => 'cloud-download',
			1 => NULL,
			2 => 'cloud-download-alt',
		),
		92 => array(
			0 => 'cloud-upload',
			1 => NULL,
			2 => 'cloud-upload-alt',
		),
		93 => array(
			0 => 'bell-o',
			1 => 'far',
			2 => 'bell',
		),
		94 => array(
			0 => 'cutlery',
			1 => NULL,
			2 => 'utensils',
		),
		95 => array(
			0 => 'file-text-o',
			1 => 'far',
			2 => 'file-alt',
		),
		96 => array(
			0 => 'building-o',
			1 => 'far',
			2 => 'building',
		),
		97 => array(
			0 => 'hospital-o',
			1 => 'far',
			2 => 'hospital',
		),
		98 => array(
			0 => 'tablet',
			1 => NULL,
			2 => 'tablet-alt',
		),
		99 => array(
			0 => 'mobile',
			1 => NULL,
			2 => 'mobile-alt',
		),
		100 => array(
			0 => 'mobile-phone',
			1 => NULL,
			2 => 'mobile-alt',
		),
		101 => array(
			0 => 'circle-o',
			1 => 'far',
			2 => 'circle',
		),
		102 => array(
			0 => 'mail-reply',
			1 => NULL,
			2 => 'reply',
		),
		103 => array(
			0 => 'github-alt',
			1 => 'fab',
			2 => NULL,
		),
		104 => array(
			0 => 'folder-o',
			1 => 'far',
			2 => 'folder',
		),
		105 => array(
			0 => 'folder-open-o',
			1 => 'far',
			2 => 'folder-open',
		),
		106 => array(
			0 => 'smile-o',
			1 => 'far',
			2 => 'smile',
		),
		107 => array(
			0 => 'frown-o',
			1 => 'far',
			2 => 'frown',
		),
		108 => array(
			0 => 'meh-o',
			1 => 'far',
			2 => 'meh',
		),
		109 => array(
			0 => 'keyboard-o',
			1 => 'far',
			2 => 'keyboard',
		),
		110 => array(
			0 => 'flag-o',
			1 => 'far',
			2 => 'flag',
		),
		111 => array(
			0 => 'mail-reply-all',
			1 => NULL,
			2 => 'reply-all',
		),
		112 => array(
			0 => 'star-half-o',
			1 => 'far',
			2 => 'star-half',
		),
		113 => array(
			0 => 'star-half-empty',
			1 => 'far',
			2 => 'star-half',
		),
		114 => array(
			0 => 'star-half-full',
			1 => 'far',
			2 => 'star-half',
		),
		115 => array(
			0 => 'code-fork',
			1 => NULL,
			2 => 'code-branch',
		),
		116 => array(
			0 => 'chain-broken',
			1 => NULL,
			2 => 'unlink',
		),
		117 => array(
			0 => 'shield',
			1 => NULL,
			2 => 'shield-alt',
		),
		118 => array(
			0 => 'calendar-o',
			1 => 'far',
			2 => 'calendar',
		),
		119 => array(
			0 => 'maxcdn',
			1 => 'fab',
			2 => NULL,
		),
		120 => array(
			0 => 'html5',
			1 => 'fab',
			2 => NULL,
		),
		121 => array(
			0 => 'css3',
			1 => 'fab',
			2 => NULL,
		),
		122 => array(
			0 => 'ticket',
			1 => NULL,
			2 => 'ticket-alt',
		),
		123 => array(
			0 => 'minus-square-o',
			1 => 'far',
			2 => 'minus-square',
		),
		124 => array(
			0 => 'level-up',
			1 => NULL,
			2 => 'level-up-alt',
		),
		125 => array(
			0 => 'level-down',
			1 => NULL,
			2 => 'level-down-alt',
		),
		126 => array(
			0 => 'pencil-square',
			1 => NULL,
			2 => 'pen-square',
		),
		127 => array(
			0 => 'external-link-square',
			1 => NULL,
			2 => 'external-link-square-alt',
		),
		128 => array(
			0 => 'compass',
			1 => 'far',
			2 => NULL,
		),
		129 => array(
			0 => 'caret-square-o-down',
			1 => 'far',
			2 => 'caret-square-down',
		),
		130 => array(
			0 => 'toggle-down',
			1 => 'far',
			2 => 'caret-square-down',
		),
		131 => array(
			0 => 'caret-square-o-up',
			1 => 'far',
			2 => 'caret-square-up',
		),
		132 => array(
			0 => 'toggle-up',
			1 => 'far',
			2 => 'caret-square-up',
		),
		133 => array(
			0 => 'caret-square-o-right',
			1 => 'far',
			2 => 'caret-square-right',
		),
		134 => array(
			0 => 'toggle-right',
			1 => 'far',
			2 => 'caret-square-right',
		),
		135 => array(
			0 => 'eur',
			1 => NULL,
			2 => 'euro-sign',
		),
		136 => array(
			0 => 'euro',
			1 => NULL,
			2 => 'euro-sign',
		),
		137 => array(
			0 => 'gbp',
			1 => NULL,
			2 => 'pound-sign',
		),
		138 => array(
			0 => 'usd',
			1 => NULL,
			2 => 'dollar-sign',
		),
		139 => array(
			0 => 'dollar',
			1 => NULL,
			2 => 'dollar-sign',
		),
		140 => array(
			0 => 'inr',
			1 => NULL,
			2 => 'rupee-sign',
		),
		141 => array(
			0 => 'rupee',
			1 => NULL,
			2 => 'rupee-sign',
		),
		142 => array(
			0 => 'jpy',
			1 => NULL,
			2 => 'yen-sign',
		),
		143 => array(
			0 => 'cny',
			1 => NULL,
			2 => 'yen-sign',
		),
		144 => array(
			0 => 'rmb',
			1 => NULL,
			2 => 'yen-sign',
		),
		145 => array(
			0 => 'yen',
			1 => NULL,
			2 => 'yen-sign',
		),
		146 => array(
			0 => 'rub',
			1 => NULL,
			2 => 'ruble-sign',
		),
		147 => array(
			0 => 'ruble',
			1 => NULL,
			2 => 'ruble-sign',
		),
		148 => array(
			0 => 'rouble',
			1 => NULL,
			2 => 'ruble-sign',
		),
		149 => array(
			0 => 'krw',
			1 => NULL,
			2 => 'won-sign',
		),
		150 => array(
			0 => 'won',
			1 => NULL,
			2 => 'won-sign',
		),
		151 => array(
			0 => 'btc',
			1 => 'fab',
			2 => NULL,
		),
		152 => array(
			0 => 'bitcoin',
			1 => 'fab',
			2 => 'btc',
		),
		153 => array(
			0 => 'file-text',
			1 => NULL,
			2 => 'file-alt',
		),
		154 => array(
			0 => 'sort-alpha-asc',
			1 => NULL,
			2 => 'sort-alpha-down',
		),
		155 => array(
			0 => 'sort-alpha-desc',
			1 => NULL,
			2 => 'sort-alpha-up',
		),
		156 => array(
			0 => 'sort-amount-asc',
			1 => NULL,
			2 => 'sort-amount-down',
		),
		157 => array(
			0 => 'sort-amount-desc',
			1 => NULL,
			2 => 'sort-amount-up',
		),
		158 => array(
			0 => 'sort-numeric-asc',
			1 => NULL,
			2 => 'sort-numeric-down',
		),
		159 => array(
			0 => 'sort-numeric-desc',
			1 => NULL,
			2 => 'sort-numeric-up',
		),
		160 => array(
			0 => 'youtube-square',
			1 => 'fab',
			2 => 'youtube',
		),
		161 => array(
			0 => 'youtube',
			1 => 'fab',
			2 => NULL,
		),
		162 => array(
			0 => 'xing',
			1 => 'fab',
			2 => NULL,
		),
		163 => array(
			0 => 'xing-square',
			1 => 'fab',
			2 => NULL,
		),
		164 => array(
			0 => 'youtube-play',
			1 => 'fab',
			2 => 'youtube',
		),
		165 => array(
			0 => 'dropbox',
			1 => 'fab',
			2 => NULL,
		),
		166 => array(
			0 => 'stack-overflow',
			1 => 'fab',
			2 => NULL,
		),
		167 => array(
			0 => 'instagram',
			1 => 'fab',
			2 => NULL,
		),
		168 => array(
			0 => 'flickr',
			1 => 'fab',
			2 => NULL,
		),
		169 => array(
			0 => 'adn',
			1 => 'fab',
			2 => NULL,
		),
		170 => array(
			0 => 'bitbucket',
			1 => 'fab',
			2 => NULL,
		),
		171 => array(
			0 => 'bitbucket-square',
			1 => 'fab',
			2 => 'bitbucket',
		),
		172 => array(
			0 => 'tumblr',
			1 => 'fab',
			2 => NULL,
		),
		173 => array(
			0 => 'tumblr-square',
			1 => 'fab',
			2 => NULL,
		),
		174 => array(
			0 => 'long-arrow-down',
			1 => NULL,
			2 => 'long-arrow-alt-down',
		),
		175 => array(
			0 => 'long-arrow-up',
			1 => NULL,
			2 => 'long-arrow-alt-up',
		),
		176 => array(
			0 => 'long-arrow-left',
			1 => NULL,
			2 => 'long-arrow-alt-left',
		),
		177 => array(
			0 => 'long-arrow-right',
			1 => NULL,
			2 => 'long-arrow-alt-right',
		),
		178 => array(
			0 => 'apple',
			1 => 'fab',
			2 => NULL,
		),
		179 => array(
			0 => 'windows',
			1 => 'fab',
			2 => NULL,
		),
		180 => array(
			0 => 'android',
			1 => 'fab',
			2 => NULL,
		),
		181 => array(
			0 => 'linux',
			1 => 'fab',
			2 => NULL,
		),
		182 => array(
			0 => 'dribbble',
			1 => 'fab',
			2 => NULL,
		),
		183 => array(
			0 => 'skype',
			1 => 'fab',
			2 => NULL,
		),
		184 => array(
			0 => 'foursquare',
			1 => 'fab',
			2 => NULL,
		),
		185 => array(
			0 => 'trello',
			1 => 'fab',
			2 => NULL,
		),
		186 => array(
			0 => 'gratipay',
			1 => 'fab',
			2 => NULL,
		),
		187 => array(
			0 => 'gittip',
			1 => 'fab',
			2 => 'gratipay',
		),
		188 => array(
			0 => 'sun-o',
			1 => 'far',
			2 => 'sun',
		),
		189 => array(
			0 => 'moon-o',
			1 => 'far',
			2 => 'moon',
		),
		190 => array(
			0 => 'vk',
			1 => 'fab',
			2 => NULL,
		),
		191 => array(
			0 => 'weibo',
			1 => 'fab',
			2 => NULL,
		),
		192 => array(
			0 => 'renren',
			1 => 'fab',
			2 => NULL,
		),
		193 => array(
			0 => 'pagelines',
			1 => 'fab',
			2 => NULL,
		),
		194 => array(
			0 => 'stack-exchange',
			1 => 'fab',
			2 => NULL,
		),
		195 => array(
			0 => 'arrow-circle-o-right',
			1 => 'far',
			2 => 'arrow-alt-circle-right',
		),
		196 => array(
			0 => 'arrow-circle-o-left',
			1 => 'far',
			2 => 'arrow-alt-circle-left',
		),
		197 => array(
			0 => 'caret-square-o-left',
			1 => 'far',
			2 => 'caret-square-left',
		),
		198 => array(
			0 => 'toggle-left',
			1 => 'far',
			2 => 'caret-square-left',
		),
		199 => array(
			0 => 'dot-circle-o',
			1 => 'far',
			2 => 'dot-circle',
		),
		200 => array(
			0 => 'vimeo-square',
			1 => 'fab',
			2 => NULL,
		),
		201 => array(
			0 => 'try',
			1 => NULL,
			2 => 'lira-sign',
		),
		202 => array(
			0 => 'turkish-lira',
			1 => NULL,
			2 => 'lira-sign',
		),
		203 => array(
			0 => 'plus-square-o',
			1 => 'far',
			2 => 'plus-square',
		),
		204 => array(
			0 => 'slack',
			1 => 'fab',
			2 => NULL,
		),
		205 => array(
			0 => 'wordpress',
			1 => 'fab',
			2 => NULL,
		),
		206 => array(
			0 => 'openid',
			1 => 'fab',
			2 => NULL,
		),
		207 => array(
			0 => 'institution',
			1 => NULL,
			2 => 'university',
		),
		208 => array(
			0 => 'bank',
			1 => NULL,
			2 => 'university',
		),
		209 => array(
			0 => 'mortar-board',
			1 => NULL,
			2 => 'graduation-cap',
		),
		210 => array(
			0 => 'yahoo',
			1 => 'fab',
			2 => NULL,
		),
		211 => array(
			0 => 'google',
			1 => 'fab',
			2 => NULL,
		),
		212 => array(
			0 => 'reddit',
			1 => 'fab',
			2 => NULL,
		),
		213 => array(
			0 => 'reddit-square',
			1 => 'fab',
			2 => NULL,
		),
		214 => array(
			0 => 'stumbleupon-circle',
			1 => 'fab',
			2 => NULL,
		),
		215 => array(
			0 => 'stumbleupon',
			1 => 'fab',
			2 => NULL,
		),
		216 => array(
			0 => 'delicious',
			1 => 'fab',
			2 => NULL,
		),
		217 => array(
			0 => 'digg',
			1 => 'fab',
			2 => NULL,
		),
		218 => array(
			0 => 'pied-piper-pp',
			1 => 'fab',
			2 => NULL,
		),
		219 => array(
			0 => 'pied-piper-alt',
			1 => 'fab',
			2 => NULL,
		),
		220 => array(
			0 => 'drupal',
			1 => 'fab',
			2 => NULL,
		),
		221 => array(
			0 => 'joomla',
			1 => 'fab',
			2 => NULL,
		),
		222 => array(
			0 => 'spoon',
			1 => NULL,
			2 => 'utensil-spoon',
		),
		223 => array(
			0 => 'behance',
			1 => 'fab',
			2 => NULL,
		),
		224 => array(
			0 => 'behance-square',
			1 => 'fab',
			2 => NULL,
		),
		225 => array(
			0 => 'steam',
			1 => 'fab',
			2 => NULL,
		),
		226 => array(
			0 => 'steam-square',
			1 => 'fab',
			2 => NULL,
		),
		227 => array(
			0 => 'automobile',
			1 => NULL,
			2 => 'car',
		),
		228 => array(
			0 => 'cab',
			1 => NULL,
			2 => 'taxi',
		),
		229 => array(
			0 => 'spotify',
			1 => 'fab',
			2 => NULL,
		),
		230 => array(
			0 => 'envelope-o',
			1 => 'far',
			2 => 'envelope',
		),
		231 => array(
			0 => 'soundcloud',
			1 => 'fab',
			2 => NULL,
		),
		232 => array(
			0 => 'file-pdf-o',
			1 => 'far',
			2 => 'file-pdf',
		),
		233 => array(
			0 => 'file-word-o',
			1 => 'far',
			2 => 'file-word',
		),
		234 => array(
			0 => 'file-excel-o',
			1 => 'far',
			2 => 'file-excel',
		),
		235 => array(
			0 => 'file-powerpoint-o',
			1 => 'far',
			2 => 'file-powerpoint',
		),
		236 => array(
			0 => 'file-image-o',
			1 => 'far',
			2 => 'file-image',
		),
		237 => array(
			0 => 'file-photo-o',
			1 => 'far',
			2 => 'file-image',
		),
		238 => array(
			0 => 'file-picture-o',
			1 => 'far',
			2 => 'file-image',
		),
		239 => array(
			0 => 'file-archive-o',
			1 => 'far',
			2 => 'file-archive',
		),
		240 => array(
			0 => 'file-zip-o',
			1 => 'far',
			2 => 'file-archive',
		),
		241 => array(
			0 => 'file-audio-o',
			1 => 'far',
			2 => 'file-audio',
		),
		242 => array(
			0 => 'file-sound-o',
			1 => 'far',
			2 => 'file-audio',
		),
		243 => array(
			0 => 'file-video-o',
			1 => 'far',
			2 => 'file-video',
		),
		244 => array(
			0 => 'file-movie-o',
			1 => 'far',
			2 => 'file-video',
		),
		245 => array(
			0 => 'file-code-o',
			1 => 'far',
			2 => 'file-code',
		),
		246 => array(
			0 => 'vine',
			1 => 'fab',
			2 => NULL,
		),
		247 => array(
			0 => 'codepen',
			1 => 'fab',
			2 => NULL,
		),
		248 => array(
			0 => 'jsfiddle',
			1 => 'fab',
			2 => NULL,
		),
		249 => array(
			0 => 'life-ring',
			1 => 'far',
			2 => NULL,
		),
		250 => array(
			0 => 'life-bouy',
			1 => 'far',
			2 => 'life-ring',
		),
		251 => array(
			0 => 'life-buoy',
			1 => 'far',
			2 => 'life-ring',
		),
		252 => array(
			0 => 'life-saver',
			1 => 'far',
			2 => 'life-ring',
		),
		253 => array(
			0 => 'support',
			1 => 'far',
			2 => 'life-ring',
		),
		254 => array(
			0 => 'circle-o-notch',
			1 => NULL,
			2 => 'circle-notch',
		),
		255 => array(
			0 => 'rebel',
			1 => 'fab',
			2 => NULL,
		),
		256 => array(
			0 => 'ra',
			1 => 'fab',
			2 => 'rebel',
		),
		257 => array(
			0 => 'resistance',
			1 => 'fab',
			2 => 'rebel',
		),
		258 => array(
			0 => 'empire',
			1 => 'fab',
			2 => NULL,
		),
		259 => array(
			0 => 'ge',
			1 => 'fab',
			2 => 'empire',
		),
		260 => array(
			0 => 'git-square',
			1 => 'fab',
			2 => NULL,
		),
		261 => array(
			0 => 'git',
			1 => 'fab',
			2 => NULL,
		),
		262 => array(
			0 => 'hacker-news',
			1 => 'fab',
			2 => NULL,
		),
		263 => array(
			0 => 'y-combinator-square',
			1 => 'fab',
			2 => 'hacker-news',
		),
		264 => array(
			0 => 'yc-square',
			1 => 'fab',
			2 => 'hacker-news',
		),
		265 => array(
			0 => 'tencent-weibo',
			1 => 'fab',
			2 => NULL,
		),
		266 => array(
			0 => 'qq',
			1 => 'fab',
			2 => NULL,
		),
		267 => array(
			0 => 'weixin',
			1 => 'fab',
			2 => NULL,
		),
		268 => array(
			0 => 'wechat',
			1 => 'fab',
			2 => 'weixin',
		),
		269 => array(
			0 => 'send',
			1 => NULL,
			2 => 'paper-plane',
		),
		270 => array(
			0 => 'paper-plane-o',
			1 => 'far',
			2 => 'paper-plane',
		),
		271 => array(
			0 => 'send-o',
			1 => 'far',
			2 => 'paper-plane',
		),
		272 => array(
			0 => 'circle-thin',
			1 => 'far',
			2 => 'circle',
		),
		273 => array(
			0 => 'header',
			1 => NULL,
			2 => 'heading',
		),
		274 => array(
			0 => 'sliders',
			1 => NULL,
			2 => 'sliders-h',
		),
		275 => array(
			0 => 'futbol-o',
			1 => 'far',
			2 => 'futbol',
		),
		276 => array(
			0 => 'soccer-ball-o',
			1 => 'far',
			2 => 'futbol',
		),
		277 => array(
			0 => 'slideshare',
			1 => 'fab',
			2 => NULL,
		),
		278 => array(
			0 => 'twitch',
			1 => 'fab',
			2 => NULL,
		),
		279 => array(
			0 => 'yelp',
			1 => 'fab',
			2 => NULL,
		),
		280 => array(
			0 => 'newspaper-o',
			1 => 'far',
			2 => 'newspaper',
		),
		281 => array(
			0 => 'paypal',
			1 => 'fab',
			2 => NULL,
		),
		282 => array(
			0 => 'google-wallet',
			1 => 'fab',
			2 => NULL,
		),
		283 => array(
			0 => 'cc-visa',
			1 => 'fab',
			2 => NULL,
		),
		284 => array(
			0 => 'cc-mastercard',
			1 => 'fab',
			2 => NULL,
		),
		285 => array(
			0 => 'cc-discover',
			1 => 'fab',
			2 => NULL,
		),
		286 => array(
			0 => 'cc-amex',
			1 => 'fab',
			2 => NULL,
		),
		287 => array(
			0 => 'cc-paypal',
			1 => 'fab',
			2 => NULL,
		),
		288 => array(
			0 => 'cc-stripe',
			1 => 'fab',
			2 => NULL,
		),
		289 => array(
			0 => 'bell-slash-o',
			1 => 'far',
			2 => 'bell-slash',
		),
		290 => array(
			0 => 'trash',
			1 => NULL,
			2 => 'trash-alt',
		),
		291 => array(
			0 => 'copyright',
			1 => 'far',
			2 => NULL,
		),
		292 => array(
			0 => 'eyedropper',
			1 => NULL,
			2 => 'eye-dropper',
		),
		293 => array(
			0 => 'area-chart',
			1 => NULL,
			2 => 'chart-area',
		),
		294 => array(
			0 => 'pie-chart',
			1 => NULL,
			2 => 'chart-pie',
		),
		295 => array(
			0 => 'line-chart',
			1 => NULL,
			2 => 'chart-line',
		),
		296 => array(
			0 => 'lastfm',
			1 => 'fab',
			2 => NULL,
		),
		297 => array(
			0 => 'lastfm-square',
			1 => 'fab',
			2 => NULL,
		),
		298 => array(
			0 => 'ioxhost',
			1 => 'fab',
			2 => NULL,
		),
		299 => array(
			0 => 'angellist',
			1 => 'fab',
			2 => NULL,
		),
		300 => array(
			0 => 'cc',
			1 => 'far',
			2 => 'closed-captioning',
		),
		301 => array(
			0 => 'ils',
			1 => NULL,
			2 => 'shekel-sign',
		),
		302 => array(
			0 => 'shekel',
			1 => NULL,
			2 => 'shekel-sign',
		),
		303 => array(
			0 => 'sheqel',
			1 => NULL,
			2 => 'shekel-sign',
		),
		304 => array(
			0 => 'meanpath',
			1 => 'fab',
			2 => 'font-awesome',
		),
		305 => array(
			0 => 'buysellads',
			1 => 'fab',
			2 => NULL,
		),
		306 => array(
			0 => 'connectdevelop',
			1 => 'fab',
			2 => NULL,
		),
		307 => array(
			0 => 'dashcube',
			1 => 'fab',
			2 => NULL,
		),
		308 => array(
			0 => 'forumbee',
			1 => 'fab',
			2 => NULL,
		),
		309 => array(
			0 => 'leanpub',
			1 => 'fab',
			2 => NULL,
		),
		310 => array(
			0 => 'sellsy',
			1 => 'fab',
			2 => NULL,
		),
		311 => array(
			0 => 'shirtsinbulk',
			1 => 'fab',
			2 => NULL,
		),
		312 => array(
			0 => 'simplybuilt',
			1 => 'fab',
			2 => NULL,
		),
		313 => array(
			0 => 'skyatlas',
			1 => 'fab',
			2 => NULL,
		),
		314 => array(
			0 => 'diamond',
			1 => 'far',
			2 => 'gem',
		),
		315 => array(
			0 => 'intersex',
			1 => NULL,
			2 => 'transgender',
		),
		316 => array(
			0 => 'facebook-official',
			1 => 'fab',
			2 => 'facebook',
		),
		317 => array(
			0 => 'pinterest-p',
			1 => 'fab',
			2 => NULL,
		),
		318 => array(
			0 => 'whatsapp',
			1 => 'fab',
			2 => NULL,
		),
		319 => array(
			0 => 'hotel',
			1 => NULL,
			2 => 'bed',
		),
		320 => array(
			0 => 'viacoin',
			1 => 'fab',
			2 => NULL,
		),
		321 => array(
			0 => 'medium',
			1 => 'fab',
			2 => NULL,
		),
		322 => array(
			0 => 'y-combinator',
			1 => 'fab',
			2 => NULL,
		),
		323 => array(
			0 => 'yc',
			1 => 'fab',
			2 => 'y-combinator',
		),
		324 => array(
			0 => 'optin-monster',
			1 => 'fab',
			2 => NULL,
		),
		325 => array(
			0 => 'opencart',
			1 => 'fab',
			2 => NULL,
		),
		326 => array(
			0 => 'expeditedssl',
			1 => 'fab',
			2 => NULL,
		),
		327 => array(
			0 => 'battery-4',
			1 => NULL,
			2 => 'battery-full',
		),
		328 => array(
			0 => 'battery',
			1 => NULL,
			2 => 'battery-full',
		),
		329 => array(
			0 => 'battery-3',
			1 => NULL,
			2 => 'battery-three-quarters',
		),
		330 => array(
			0 => 'battery-2',
			1 => NULL,
			2 => 'battery-half',
		),
		331 => array(
			0 => 'battery-1',
			1 => NULL,
			2 => 'battery-quarter',
		),
		332 => array(
			0 => 'battery-0',
			1 => NULL,
			2 => 'battery-empty',
		),
		333 => array(
			0 => 'object-group',
			1 => 'far',
			2 => NULL,
		),
		334 => array(
			0 => 'object-ungroup',
			1 => 'far',
			2 => NULL,
		),
		335 => array(
			0 => 'sticky-note-o',
			1 => 'far',
			2 => 'sticky-note',
		),
		336 => array(
			0 => 'cc-jcb',
			1 => 'fab',
			2 => NULL,
		),
		337 => array(
			0 => 'cc-diners-club',
			1 => 'fab',
			2 => NULL,
		),
		338 => array(
			0 => 'clone',
			1 => 'far',
			2 => NULL,
		),
		339 => array(
			0 => 'hourglass-o',
			1 => 'far',
			2 => 'hourglass',
		),
		340 => array(
			0 => 'hourglass-1',
			1 => NULL,
			2 => 'hourglass-start',
		),
		341 => array(
			0 => 'hourglass-2',
			1 => NULL,
			2 => 'hourglass-half',
		),
		342 => array(
			0 => 'hourglass-3',
			1 => NULL,
			2 => 'hourglass-end',
		),
		343 => array(
			0 => 'hand-rock-o',
			1 => 'far',
			2 => 'hand-rock',
		),
		344 => array(
			0 => 'hand-grab-o',
			1 => 'far',
			2 => 'hand-rock',
		),
		345 => array(
			0 => 'hand-paper-o',
			1 => 'far',
			2 => 'hand-paper',
		),
		346 => array(
			0 => 'hand-stop-o',
			1 => 'far',
			2 => 'hand-paper',
		),
		347 => array(
			0 => 'hand-scissors-o',
			1 => 'far',
			2 => 'hand-scissors',
		),
		348 => array(
			0 => 'hand-lizard-o',
			1 => 'far',
			2 => 'hand-lizard',
		),
		349 => array(
			0 => 'hand-spock-o',
			1 => 'far',
			2 => 'hand-spock',
		),
		350 => array(
			0 => 'hand-pointer-o',
			1 => 'far',
			2 => 'hand-pointer',
		),
		351 => array(
			0 => 'hand-peace-o',
			1 => 'far',
			2 => 'hand-peace',
		),
		352 => array(
			0 => 'registered',
			1 => 'far',
			2 => NULL,
		),
		353 => array(
			0 => 'creative-commons',
			1 => 'fab',
			2 => NULL,
		),
		354 => array(
			0 => 'gg',
			1 => 'fab',
			2 => NULL,
		),
		355 => array(
			0 => 'gg-circle',
			1 => 'fab',
			2 => NULL,
		),
		356 => array(
			0 => 'tripadvisor',
			1 => 'fab',
			2 => NULL,
		),
		357 => array(
			0 => 'odnoklassniki',
			1 => 'fab',
			2 => NULL,
		),
		358 => array(
			0 => 'odnoklassniki-square',
			1 => 'fab',
			2 => NULL,
		),
		359 => array(
			0 => 'get-pocket',
			1 => 'fab',
			2 => NULL,
		),
		360 => array(
			0 => 'wikipedia-w',
			1 => 'fab',
			2 => NULL,
		),
		361 => array(
			0 => 'safari',
			1 => 'fab',
			2 => NULL,
		),
		362 => array(
			0 => 'chrome',
			1 => 'fab',
			2 => NULL,
		),
		363 => array(
			0 => 'firefox',
			1 => 'fab',
			2 => NULL,
		),
		364 => array(
			0 => 'opera',
			1 => 'fab',
			2 => NULL,
		),
		365 => array(
			0 => 'internet-explorer',
			1 => 'fab',
			2 => NULL,
		),
		366 => array(
			0 => 'television',
			1 => NULL,
			2 => 'tv',
		),
		367 => array(
			0 => 'contao',
			1 => 'fab',
			2 => NULL,
		),
		368 => array(
			0 => '500px',
			1 => 'fab',
			2 => NULL,
		),
		369 => array(
			0 => 'amazon',
			1 => 'fab',
			2 => NULL,
		),
		370 => array(
			0 => 'calendar-plus-o',
			1 => 'far',
			2 => 'calendar-plus',
		),
		371 => array(
			0 => 'calendar-minus-o',
			1 => 'far',
			2 => 'calendar-minus',
		),
		372 => array(
			0 => 'calendar-times-o',
			1 => 'far',
			2 => 'calendar-times',
		),
		373 => array(
			0 => 'calendar-check-o',
			1 => 'far',
			2 => 'calendar-check',
		),
		374 => array(
			0 => 'map-o',
			1 => 'far',
			2 => 'map',
		),
		375 => array(
			0 => 'commenting',
			1 => NULL,
			2 => 'comment-alt',
		),
		376 => array(
			0 => 'commenting-o',
			1 => 'far',
			2 => 'comment-alt',
		),
		377 => array(
			0 => 'houzz',
			1 => 'fab',
			2 => NULL,
		),
		378 => array(
			0 => 'vimeo',
			1 => 'fab',
			2 => 'vimeo-v',
		),
		379 => array(
			0 => 'black-tie',
			1 => 'fab',
			2 => NULL,
		),
		380 => array(
			0 => 'fonticons',
			1 => 'fab',
			2 => NULL,
		),
		381 => array(
			0 => 'reddit-alien',
			1 => 'fab',
			2 => NULL,
		),
		382 => array(
			0 => 'edge',
			1 => 'fab',
			2 => NULL,
		),
		383 => array(
			0 => 'credit-card-alt',
			1 => NULL,
			2 => 'credit-card',
		),
		384 => array(
			0 => 'codiepie',
			1 => 'fab',
			2 => NULL,
		),
		385 => array(
			0 => 'modx',
			1 => 'fab',
			2 => NULL,
		),
		386 => array(
			0 => 'fort-awesome',
			1 => 'fab',
			2 => NULL,
		),
		387 => array(
			0 => 'usb',
			1 => 'fab',
			2 => NULL,
		),
		388 => array(
			0 => 'product-hunt',
			1 => 'fab',
			2 => NULL,
		),
		389 => array(
			0 => 'mixcloud',
			1 => 'fab',
			2 => NULL,
		),
		390 => array(
			0 => 'scribd',
			1 => 'fab',
			2 => NULL,
		),
		391 => array(
			0 => 'pause-circle-o',
			1 => 'far',
			2 => 'pause-circle',
		),
		392 => array(
			0 => 'stop-circle-o',
			1 => 'far',
			2 => 'stop-circle',
		),
		393 => array(
			0 => 'bluetooth',
			1 => 'fab',
			2 => NULL,
		),
		394 => array(
			0 => 'bluetooth-b',
			1 => 'fab',
			2 => NULL,
		),
		395 => array(
			0 => 'gitlab',
			1 => 'fab',
			2 => NULL,
		),
		396 => array(
			0 => 'wpbeginner',
			1 => 'fab',
			2 => NULL,
		),
		397 => array(
			0 => 'wpforms',
			1 => 'fab',
			2 => NULL,
		),
		398 => array(
			0 => 'envira',
			1 => 'fab',
			2 => NULL,
		),
		399 => array(
			0 => 'wheelchair-alt',
			1 => 'fab',
			2 => 'accessible-icon',
		),
		400 => array(
			0 => 'question-circle-o',
			1 => 'far',
			2 => 'question-circle',
		),
		401 => array(
			0 => 'volume-control-phone',
			1 => NULL,
			2 => 'phone-volume',
		),
		402 => array(
			0 => 'asl-interpreting',
			1 => NULL,
			2 => 'american-sign-language-interpreting',
		),
		403 => array(
			0 => 'deafness',
			1 => NULL,
			2 => 'deaf',
		),
		404 => array(
			0 => 'hard-of-hearing',
			1 => NULL,
			2 => 'deaf',
		),
		405 => array(
			0 => 'glide',
			1 => 'fab',
			2 => NULL,
		),
		406 => array(
			0 => 'glide-g',
			1 => 'fab',
			2 => NULL,
		),
		407 => array(
			0 => 'signing',
			1 => NULL,
			2 => 'sign-language',
		),
		408 => array(
			0 => 'viadeo',
			1 => 'fab',
			2 => NULL,
		),
		409 => array(
			0 => 'viadeo-square',
			1 => 'fab',
			2 => NULL,
		),
		410 => array(
			0 => 'snapchat',
			1 => 'fab',
			2 => NULL,
		),
		411 => array(
			0 => 'snapchat-ghost',
			1 => 'fab',
			2 => NULL,
		),
		412 => array(
			0 => 'snapchat-square',
			1 => 'fab',
			2 => NULL,
		),
		413 => array(
			0 => 'pied-piper',
			1 => 'fab',
			2 => NULL,
		),
		414 => array(
			0 => 'first-order',
			1 => 'fab',
			2 => NULL,
		),
		415 => array(
			0 => 'yoast',
			1 => 'fab',
			2 => NULL,
		),
		416 => array(
			0 => 'themeisle',
			1 => 'fab',
			2 => NULL,
		),
		417 => array(
			0 => 'google-plus-official',
			1 => 'fab',
			2 => 'google-plus',
		),
		418 => array(
			0 => 'google-plus-circle',
			1 => 'fab',
			2 => 'google-plus',
		),
		419 => array(
			0 => 'font-awesome',
			1 => 'fab',
			2 => NULL,
		),
		420 => array(
			0 => 'fa',
			1 => 'fab',
			2 => 'font-awesome',
		),
		421 => array(
			0 => 'handshake-o',
			1 => 'far',
			2 => 'handshake',
		),
		422 => array(
			0 => 'envelope-open-o',
			1 => 'far',
			2 => 'envelope-open',
		),
		423 => array(
			0 => 'linode',
			1 => 'fab',
			2 => NULL,
		),
		424 => array(
			0 => 'address-book-o',
			1 => 'far',
			2 => 'address-book',
		),
		425 => array(
			0 => 'vcard',
			1 => NULL,
			2 => 'address-card',
		),
		426 => array(
			0 => 'address-card-o',
			1 => 'far',
			2 => 'address-card',
		),
		427 => array(
			0 => 'vcard-o',
			1 => 'far',
			2 => 'address-card',
		),
		428 => array(
			0 => 'user-circle-o',
			1 => 'far',
			2 => 'user-circle',
		),
		429 => array(
			0 => 'user-o',
			1 => 'far',
			2 => 'user',
		),
		430 => array(
			0 => 'id-badge',
			1 => 'far',
			2 => NULL,
		),
		431 => array(
			0 => 'drivers-license',
			1 => NULL,
			2 => 'id-card',
		),
		432 => array(
			0 => 'id-card-o',
			1 => 'far',
			2 => 'id-card',
		),
		433 => array(
			0 => 'drivers-license-o',
			1 => 'far',
			2 => 'id-card',
		),
		434 => array(
			0 => 'quora',
			1 => 'fab',
			2 => NULL,
		),
		435 => array(
			0 => 'free-code-camp',
			1 => 'fab',
			2 => NULL,
		),
		436 => array(
			0 => 'telegram',
			1 => 'fab',
			2 => NULL,
		),
		437 => array(
			0 => 'thermometer-4',
			1 => NULL,
			2 => 'thermometer-full',
		),
		438 => array(
			0 => 'thermometer',
			1 => NULL,
			2 => 'thermometer-full',
		),
		439 => array(
			0 => 'thermometer-3',
			1 => NULL,
			2 => 'thermometer-three-quarters',
		),
		440 => array(
			0 => 'thermometer-2',
			1 => NULL,
			2 => 'thermometer-half',
		),
		441 => array(
			0 => 'thermometer-1',
			1 => NULL,
			2 => 'thermometer-quarter',
		),
		442 => array(
			0 => 'thermometer-0',
			1 => NULL,
			2 => 'thermometer-empty',
		),
		443 => array(
			0 => 'bathtub',
			1 => NULL,
			2 => 'bath',
		),
		444 => array(
			0 => 's15',
			1 => NULL,
			2 => 'bath',
		),
		445 => array(
			0 => 'window-maximize',
			1 => 'far',
			2 => NULL,
		),
		446 => array(
			0 => 'window-restore',
			1 => 'far',
			2 => NULL,
		),
		447 => array(
			0 => 'times-rectangle',
			1 => NULL,
			2 => 'window-close',
		),
		448 => array(
			0 => 'window-close-o',
			1 => 'far',
			2 => 'window-close',
		),
		449 => array(
			0 => 'times-rectangle-o',
			1 => 'far',
			2 => 'window-close',
		),
		450 => array(
			0 => 'bandcamp',
			1 => 'fab',
			2 => NULL,
		),
		451 => array(
			0 => 'grav',
			1 => 'fab',
			2 => NULL,
		),
		452 => array(
			0 => 'etsy',
			1 => 'fab',
			2 => NULL,
		),
		453 => array(
			0 => 'imdb',
			1 => 'fab',
			2 => NULL,
		),
		454 => array(
			0 => 'ravelry',
			1 => 'fab',
			2 => NULL,
		),
		455 => array(
			0 => 'eercast',
			1 => 'fab',
			2 => 'sellcast',
		),
		456 => array(
			0 => 'snowflake-o',
			1 => 'far',
			2 => 'snowflake',
		),
		457 => array(
			0 => 'superpowers',
			1 => 'fab',
			2 => NULL,
		),
		458 => array(
			0 => 'wpexplorer',
			1 => 'fab',
			2 => NULL,
		),
		459 => array(
			0 => 'deviantart',
			1 => 'fab',
			2 => NULL,
		),
	);

}
