<?php

class us_migration_5_0 extends US_Migration_Translator {

	/**
	 * @var bool Possibly dangerous translation that needs to be migrated manually
	 */
	public $should_be_manual = TRUE;

	public function migration_completed_message() {
		global $help_portal_url;
		$output = '<div class="updated us-migration">';
		$output .= '<p><strong>Update to ' . US_THEMENAME . ' ' . US_THEMEVERSION . ' is completed</strong>. Now check your website. If you notice some issues, <a href="'. $help_portal_url .'/impreza/tickets/" target="_blank">go to the support</a>.</p>';
		$output .= '</div>';

		return $output;
	}

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_testimonials( &$name, &$params, &$content ) {
		$name = 'us_grid';

		$layout_name = ( ! empty( $params['style'] ) ) ? 'testimonial_' . $params['style'] : 'testimonial_1';
		$custom_layout = FALSE;
		// Create custom grid layout if text size is not empty
		if ( isset( $params['text_size'] ) AND $params['text_size'] != '' ) {
			$custom_layout = TRUE;
		}

		// Custom grid layout is needed
		if ( $custom_layout ) {
			// Global layout index for the grid layout name
			global $migrated_testimonial_layouts_count;
			$migrated_testimonial_layouts_count = ( isset( $migrated_testimonial_layouts_count ) ) ? $migrated_testimonial_layouts_count + 1 : 1;
			// Find appropriate grid template to copy defaults from
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
				$layout_id = $this->add_grid_layout( 'layout_' . $migrated_testimonial_layouts_count, $layout['title'] . '-' . $migrated_testimonial_layouts_count, $layout );

				// Set grid layout ID
				$params['items_layout'] = $layout_id;
			}
		// No custom grid layout needed, just set the grid template
		} else {
			$params['items_layout'] = $layout_name;
		}

		$params['post_type'] = 'us_testimonial';

		if ( isset( $params['categories'] ) AND ! empty( $params['categories'] ) ) {
			$params['us_testimonial_categories'] = $params['categories'];
		}

		if ( ! isset( $params['columns'] ) OR empty( $params['columns'] ) ) {
			$params['columns'] = 3;
		}

		if ( isset( $params['items'] ) AND ! empty( $params['items'] ) ) {
			$params['items_quantity'] = $params['items'];
		} else {
			$params['items_quantity'] = get_option( 'posts_per_page' );
		}

		unset( $params['categories'] );
		unset( $params['items'] );
		unset( $params['style'] );
		unset( $params['text_size'] );

		return TRUE;
	}

	public function translate_us_portfolio( &$name, &$params, &$content ) {

		$name = 'us_grid';

		$layout_name = ( ! empty( $params['style'] ) ) ? str_replace( 'style', 'portfolio', $params['style'] ) : 'portfolio_1';
		$custom_layout = FALSE;
		// Create custom grid layout if Layout params migrated from shortcode are not default
		$default_layout_params = array(
			'ratio' => '1x1',
			'items_action' => 'default',
			'popup_width' => '',
			'meta_size' => '',
			'text_color' => '',
			'bg_color' => '',
		);
		foreach ( $default_layout_params as $_param => $_value ) {
			if ( isset( $params[$_param] ) AND $params[$_param] != $_value ) {
				$custom_layout = TRUE;
				break;
			}
		}
		if ( isset( $params['type'] ) AND $params['type'] == 'masonry' ) {
			$custom_layout = TRUE;
		}
		if ( ! isset( $params['style'] ) OR in_array( $params['style'], array( 'style_1', 'style_3', 'style_5', 'style_6', 'style_7', 'style_9', 'style_12' ) ) ) {
			if ( isset( $params['align'] ) AND $params['align'] != 'center' ) {
				$custom_layout = TRUE;
			}
		} else {
			if ( ! isset( $params['align'] ) OR $params['align'] != 'left' ) {
				$custom_layout = TRUE;
			}
		}
		if ( ! isset( $params['meta'] ) OR $params['meta'] != 'date' ) {
			$custom_layout = TRUE;
		}

		// Custom grid layout is needed
		if ( $custom_layout ) {
			// Global layout index for the grid layout name
			global $migrated_portfolio_layouts_count;
			$migrated_portfolio_layouts_count = ( isset( $migrated_portfolio_layouts_count ) ) ? $migrated_portfolio_layouts_count + 1 : 1;
			// Find apropriate grid template to copy defaults from
			if ( $templates_config = us_config( 'grid-templates', array(), TRUE ) AND isset( $templates_config[$layout_name] ) ) {
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
		// No custom grid layout needed, just set the grid template
		} else {
			$params['items_layout'] = $layout_name;
		}

		$params['post_type'] = 'us_portfolio';

		if ( isset( $params['categories'] ) AND ! empty( $params['categories'] ) ) {
			$params['us_portfolio_categories'] = $params['categories'];
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
			$params['items_gap'] = '4px';
		} else {
			$params['items_gap'] = '';
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

		return TRUE;
	}

	public function translate_us_blog( &$name, &$params, &$content ) {
		$name = 'us_grid';

		$layout_translate = array(
			'classic' => 'blog_classic',
			'flat' => 'blog_flat',
			'tiles' => 'blog_tiles',
			'cards' => 'blog_cards',
			'smallcircle' => 'blog_side_image',
			'smallsquare' => 'blog_side_image',
			'latest' => 'blog_side_date',
			'compact' => 'blog_no_image',
		);

		$custom_layout = FALSE;
		// Create custom grid layout if:
		// 1. Layout params migrated from shortcode are not default
		$default_layout_params = array(
			'show_date' => TRUE,
			'show_author' => TRUE,
			'show_categories' => TRUE,
			'show_tags' => TRUE,
			'show_comments' => TRUE,
			'show_read_more' => TRUE,
			'content_type' => 'excerpt',
		);
		foreach ( $default_layout_params as $_param => $_value ) {
			if ( isset( $params[$_param] ) AND $params[$_param] != $_value ) {
				$custom_layout = TRUE;
				break;
			}
		}
		// 2. Single Column
		if ( isset( $params['columns'] ) AND $params['columns'] == 1 ) {
			$custom_layout = TRUE;
		}
		// 3. Layout is Small Square, Latest Posts, Compact
		if ( isset( $params['layout'] ) AND in_array( $params['layout'], array( 'smallsquare', 'latest', 'compact' ) ) ) {
			$custom_layout = TRUE;
		}
		// 4. Excerpt length option is not default
		$excerpt_length_option = us_get_option( 'excerpt_length' );
		if ( $excerpt_length_option != 30 ) {
			$custom_layout = TRUE;
		}
		// 5. Read more button options are not default
		if ( ! isset( $params['layout'] ) OR $params['layout'] != 'tiles' ) {
			$read_more_btn_style_option = us_get_option( 'read_more_btn_style' );
			if ( $read_more_btn_style_option != 'outlined' ) {
				$custom_layout = TRUE;
			}

			$read_more_btn_color_option = us_get_option( 'read_more_btn_color' );
			if ( $read_more_btn_color_option != 'light' ) {
				$custom_layout = TRUE;
			}

			$read_more_btn_size_option = us_get_option( 'read_more_btn_size' );
			if ( $read_more_btn_size_option != '' ) {
				$custom_layout = TRUE;
			}
		}

		// Custom grid layout is needed
		if ( $custom_layout ) {
			// Global layout index for the grid layout name
			global $migrated_blog_layouts_count;
			$migrated_blog_layouts_count = ( isset( $migrated_blog_layouts_count ) ) ? $migrated_blog_layouts_count + 1 : 1;
			// Find apropriate grid template to copy defaults from
			$layout_name = ( isset( $params['layout'] ) AND isset( $layout_translate[$params['layout']] ) ) ? $layout_translate[$params['layout']] : 'blog_classic';
			if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$layout_name] ) ) {
				$layout = $templates_config[$layout_name];

				// Change post image appearance from circle to square if layout is Small square
				if ( isset( $params['layout'] ) AND $params['layout'] == 'smallsquare' ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( substr( $elm_name, 0, 10 ) == 'post_image' ) {
							$layout['data'][$elm_name]['circle'] = 0;
							break;
						}
					}
				}

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
				// If the button is present, check if it's params need to be changed
				} else {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( substr( $elm_name, 0, 3 ) == 'btn' ) {
							// Style
							if ( isset( $read_more_btn_style_option ) AND $read_more_btn_style_option != 'outlined' ) {
								$layout['data'][$elm_name]['style'] = $read_more_btn_style_option;
							}
							// Color
							if ( isset( $read_more_btn_color_option ) AND $read_more_btn_color_option != 'light' ) {
								$layout['data'][$elm_name]['color'] = $read_more_btn_color_option;
							}
							// Font size
							if ( isset( $read_more_btn_size_option ) AND $read_more_btn_size_option != '' ) {
								$layout['data'][$elm_name]['font_size'] = $read_more_btn_size_option;
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

		// No custom grid layout needed, just set the grid template
		} else {
			$params['items_layout'] = ( isset( $params['layout'] ) AND isset( $layout_translate[$params['layout']] ) ) ? $layout_translate[$params['layout']] : 'blog_classic';
		}

		if ( ( empty( $params['layout'] ) OR in_array( $params['layout'], array( 'classic', 'smallcircle', 'smallsquare' ) ) ) AND ( isset( $params['columns'] ) AND $params['columns'] == 1 ) ) {
			$params['items_gap'] = '5rem';
		} elseif ( isset( $params['layout'] ) AND $params['layout'] == 'flat' ) {
			$params['items_gap'] = '';
		} elseif ( isset( $params['layout'] ) AND $params['layout'] == 'tiles' ) {
			$params['items_gap'] = '2px';
		} elseif ( isset( $params['layout'] ) AND $params['layout'] == 'cards' ) {
			$params['items_gap'] = '5px';
		} elseif ( isset( $params['layout'] ) AND $params['layout'] == 'compact' ) {
			$params['items_gap'] = '1rem';
		}

		if ( ( empty( $params['layout'] ) OR in_array( $params['layout'], array( 'classic', 'flat', 'cards', 'tiles' ) ) )
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

		return TRUE;
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

		return $changed;
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

	// Headers
	public function translate_header_settings( &$settings ) {
		$settings_changed = FALSE;

		if ( isset( $settings['data'] ) and is_array( $settings['data'] ) ) {
			foreach ( $settings['data'] as $name => $data ) {
				// Design options => hide when sticky / nonsticky
				if ( isset( $data['design_options']['hide_for_sticky'] ) AND $data['design_options']['hide_for_sticky'] ) {
					$settings['data'][$name]['hide_for_sticky'] = TRUE;
					$settings_changed = TRUE;
				}
				if ( isset( $data['design_options']['hide_for_not-sticky'] ) AND $data['design_options']['hide_for_not-sticky'] ) {
					$settings['data'][$name]['hide_for_not_sticky'] = TRUE;
					$settings_changed = TRUE;
				}
				// Text, Cart element
				if ( in_array( substr( $name, 0, 4 ), array( 'text', 'cart' ) ) ) {
					if ( ! empty( $data['icon'] ) ) {
						$translated_icon = $this->translate_icon_name( $data['icon'] );
						if ( $translated_icon != $data['icon'] ) {
							$settings['data'][$name]['icon'] = $translated_icon;
							$settings_changed = TRUE;
						}
					}
					// Button element
				} elseif ( substr( $name, 0, 3 ) == 'btn' ) {
					if ( ! empty( $data['icon'] ) ) {
						$translated_icon = $this->translate_icon_name( $data['icon'] );
						if ( $translated_icon != $data['icon'] ) {
							$settings['data'][$name]['icon'] = $translated_icon;
							$settings_changed = TRUE;
						}
					}
					// Social Links element
				} elseif ( substr( $name, 0, 7 ) == 'socials' ) {
					if ( ! empty( $data['custom_icon'] ) ) {
						$translated_icon = $this->translate_icon_name( $data['custom_icon'] );
						if ( $translated_icon != $data['custom_icon'] ) {
							$settings['data'][$name]['custom_icon'] = $translated_icon;
							$settings_changed = TRUE;
						}
					}
					// Menu element
				} elseif ( substr( $name, 0, 4 ) == 'menu' ) {
					if ( ! isset( $menu_font_family ) ) {
						$menu_font_family = '';
						if ( us_get_option( 'menu_font_family' ) != 'none' ) {
							$menu_font_family = explode( '|', us_get_option( 'menu_font_family' ), 2 );
						}
					}
					if ( $menu_font_family != '') {
						$settings['data'][$name]['font'] = $menu_font_family[0];
						$settings_changed = TRUE;
					}
				}
			}
		}

		return $settings_changed;
	}

	// Theme Options
	public function translate_theme_options( &$options ) {
		/*
		 * Blog Home Page
		 */
		$layout_translate = array(
			'classic' => 'blog_classic',
			'flat' => 'blog_flat',
			'tiles' => 'blog_tiles',
			'cards' => 'blog_cards',
			'smallcircle' => 'blog_side_image',
			'smallsquare' => 'blog_side_image',
			'latest' => 'blog_side_date',
			'compact' => 'blog_no_image',
		);

		if ( empty( $options['blog_layout'] ) OR ( in_array( $options['blog_layout'], array( 'classic', 'smallcircle', 'smallsquare' ) ) AND $options['blog_cols'] != 1 ) ) {
			$options['blog_items_gap'] = 1.5;
		} elseif ( $options['blog_layout'] == 'flat' ) {
			$options['blog_items_gap'] = 0;
		} elseif ( $options['blog_layout'] == 'tiles' ) {
			$options['blog_items_gap'] = 0.15;
		} elseif ( $options['blog_layout'] == 'cards' ) {
			$options['blog_items_gap'] = 0.3;
		} elseif ( $options['blog_layout'] == 'compact' ) {
			$options['blog_items_gap'] = 1;
		}

		$layout_name = ( isset( $options['blog_layout'] ) AND isset( $layout_translate[$options['blog_layout']] ) ) ? $layout_translate[$options['blog_layout']] : 'blog_classic';
		if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$layout_name] ) ) {
			$layout = $templates_config[$layout_name];

			// Change post image appearance from circle to square if layout is Small square
			if ( isset( $options['blog_layout'] ) AND $options['blog_layout'] == 'smallsquare' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_image' ) {
						$layout['data'][$elm_name]['circle'] = 0;
						break;
					}
				}
			}

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
				// If the button is present, check if it's params need to be changed
			} else {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 3 ) == 'btn' ) {
						// Style
						if ( isset( $options['read_more_btn_style'] ) AND $options['read_more_btn_style'] != 'outlined' ) {
							$layout['data'][$elm_name]['style'] = $options['read_more_btn_style'];
						}
						// Color
						if ( isset( $options['read_more_btn_color'] ) AND $options['read_more_btn_color'] != 'light' ) {
							$layout['data'][$elm_name]['color'] = $options['read_more_btn_color'];
						}
						// Font size
						if ( isset( $options['read_more_btn_size'] ) AND $options['read_more_btn_size'] != '' ) {
							$layout['data'][$elm_name]['font_size'] = $options['read_more_btn_size'];
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
		if ( empty( $options['archive_layout'] ) OR ( in_array( $options['archive_layout'], array( 'classic', 'smallcircle', 'smallsquare' ) ) AND $options['archive_cols'] != 1 ) ) {
			$options['archive_items_gap'] = 1.5;
		} elseif ( $options['archive_layout'] == 'flat' ) {
			$options['archive_items_gap'] = 0;
		} elseif ( $options['archive_layout'] == 'tiles' ) {
			$options['archive_items_gap'] = 0.15;
		} elseif ( $options['archive_layout'] == 'cards' ) {
			$options['archive_items_gap'] = 0.3;
		} elseif ( $options['archive_layout'] == 'compact' ) {
			$options['archive_items_gap'] = 1;
		}

		$layout_name = ( isset( $options['archive_layout'] ) AND isset( $layout_translate[$options['archive_layout']] ) ) ? $layout_translate[$options['archive_layout']] : 'blog_classic';
		if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$layout_name] ) ) {
			$layout = $templates_config[$layout_name];

			// Change post image appearance from circle to square if layout is Small square
			if ( isset( $options['archive_layout'] ) AND $options['archive_layout'] == 'smallsquare' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_image' ) {
						$layout['data'][$elm_name]['circle'] = 0;
						break;
					}
				}
			}

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
				// If the button is present, check if it's params need to be changed
			} else {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 3 ) == 'btn' ) {
						// Style
						if ( isset( $options['read_more_btn_style'] ) AND $options['read_more_btn_style'] != 'outlined' ) {
							$layout['data'][$elm_name]['style'] = $options['read_more_btn_style'];
						}
						// Color
						if ( isset( $options['read_more_btn_color'] ) AND $options['read_more_btn_color'] != 'light' ) {
							$layout['data'][$elm_name]['color'] = $options['read_more_btn_color'];
						}
						// Font size
						if ( isset( $options['read_more_btn_size'] ) AND $options['read_more_btn_size'] != '' ) {
							$layout['data'][$elm_name]['font_size'] = $options['read_more_btn_size'];
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
		if ( empty( $options['search_layout'] ) OR ( in_array( $options['search_layout'], array( 'classic', 'smallcircle', 'smallsquare' ) ) AND $options['search_cols'] != 1 ) ) {
			$options['search_items_gap'] = 1.5;
		} elseif ( $options['search_layout'] == 'flat' ) {
			$options['search_items_gap'] = 0;
		} elseif ( $options['search_layout'] == 'tiles' ) {
			$options['search_items_gap'] = 0.15;
		} elseif ( $options['search_layout'] == 'cards' ) {
			$options['search_items_gap'] = 0.3;
		} elseif ( $options['search_layout'] == 'compact' ) {
			$options['search_items_gap'] = 1;
		}

		$layout_name = ( isset( $options['search_layout'] ) AND isset( $layout_translate[$options['search_layout']] ) ) ? $layout_translate[$options['search_layout']] : 'blog_classic';
		if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$layout_name] ) ) {
			$layout = $templates_config[$layout_name];

			// Change post image appearance from circle to square if layout is Small square
			if ( isset( $options['search_layout'] ) AND $options['search_layout'] == 'smallsquare' ) {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_image' ) {
						$layout['data'][$elm_name]['circle'] = 0;
						break;
					}
				}
			}

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
				// If the button is present, check if it's params need to be changed
			} else {
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 3 ) == 'btn' ) {
						// Style
						if ( isset( $options['read_more_btn_style'] ) AND $options['read_more_btn_style'] != 'outlined' ) {
							$layout['data'][$elm_name]['style'] = $options['read_more_btn_style'];
						}
						// Color
						if ( isset( $options['read_more_btn_color'] ) AND $options['read_more_btn_color'] != 'light' ) {
							$layout['data'][$elm_name]['color'] = $options['read_more_btn_color'];
						}
						// Font size
						if ( isset( $options['read_more_btn_size'] ) AND $options['read_more_btn_size'] != '' ) {
							$layout['data'][$elm_name]['font_size'] = $options['read_more_btn_size'];
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
				$options['post_related_layout'] = 'blog_compact';
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
			if ( $options['button_font'] == 'menu' ) {
				$options['button_font'] = $menu_font_family[0];
			}

		}

		/*
		 * Adding grid CSS checkbox if optimize CSS option is ON
		 */
		if ( isset( $options['optimize_assets'] ) AND $options['optimize_assets'] == 1 AND is_array( $options['assets'] ) ) {
			$options['assets'] = array_unique( array_merge( $options['assets'], array( 'grid' ) ) );
		}

		// Quick fix for fallback migration: we run following code only once in 5 minutes on front or during actual migration
		$icons_migration_transient = get_transient( 'us_icons_migration_transient' );
		if ( $icons_migration_transient == FALSE OR is_admin() ) {
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

		return TRUE;
	}

	// Meta
	public function translate_meta( &$meta, $post_type ) {
		$changed = FALSE;
		global $us_migration_current_post_id;

		if ( $post_type == 'us_portfolio' ) {
			if ( ! empty( $meta['us_tile_description'][0] ) ) {
				wp_update_post( array(
					'ID' => $us_migration_current_post_id,
					'post_excerpt' => $meta['us_tile_description'][0],
				) );
			}
			if ( isset( $meta['us_tile_description'][0] ) ) {
				unset( $meta['us_tile_description'] );
				$changed = TRUE;
			}
		}

		return $changed;
	}

	// Widgets
	public function translate_widgets( &$name, &$instance ) {
		$changed = FALSE;

		if ( $name == 'us_portfolio' ) {
			$instance['layout'] = 'portfolio_compact';
			$changed = TRUE;
		} elseif ( $name == 'us_blog' ) {
			$layout_translate = array(
				'classic' => 'blog_classic',
				'tiles' => 'blog_tiles',
				'smallcircle' => 'blog_side_image',
				'smallsquare' => 'blog_side_image',
				'compact' => 'blog_no_image',
			);

			global $migrated_blog_layouts_count;
			$migrated_blog_layouts_count = ( isset( $migrated_blog_layouts_count ) ) ? $migrated_blog_layouts_count + 1 : 1;

			// Find apropriate grid template to copy defaults from
			$layout_name = ( isset( $instance['layout'] ) AND isset( $layout_translate[$instance['layout']] ) ) ? $layout_translate[$instance['layout']] : 'blog_classic';
			if ( $templates_config = $this->get_grid_templates() AND isset( $templates_config[$layout_name] ) ) {
				$layout = $templates_config[$layout_name];

				// Change post image appearance from circle to square if layout is Small square
				if ( isset( $instance['layout'] ) AND $instance['layout'] == 'smallsquare' ) {
					foreach ( $layout['data'] as $elm_name => $elm ) {
						if ( substr( $elm_name, 0, 10 ) == 'post_image' ) {
							$layout['data'][$elm_name]['circle'] = 0;
							break;
						}
					}
				}

				// Set image size
				foreach ( $layout['data'] as $elm_name => $elm ) {
					if ( substr( $elm_name, 0, 10 ) == 'post_image' ) {
						$img_size = ( in_array( $layout_name, array( 'blog_classic', 'blog_tiles' ) ) ) ? 'medium' : 'thumbnail';
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
		if ( isset( $content['title'] ) ) {
			unset ( $content['title'] );
		}
		$content = json_encode( $content, JSON_UNESCAPED_UNICODE );
		$content_hash = md5( $content );

		// Check if such layout exists
		global $wpdb;
		$existing_posts_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", 'us_migration_content_hash', $content_hash )
		);
		if ( count( $existing_posts_results ) > 0 ) {
			$existing_post = $existing_posts_results[0];
			return $existing_post->post_id;
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
		add_post_meta( $layout_id, 'us_migration_content_hash', $content_hash );
		ob_end_clean();

		return $layout_id;
	}

	// Get grid templates from config and add templates needed for migration only
	private function get_grid_templates() {
		$templates_config = us_config( 'grid-templates', array(), TRUE );
		$templates_config = array_merge(
			$templates_config,
			array(
				'blog_no_image' => array(
					'title' => 'Blog Without Image',
					'data' => array(
						'post_title:1' => array(
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
							'font_size' => '0.9rem',
						),
						'post_author:1' => array(
							'font_size' => '0.9rem',
						),
						'post_comments:1' => array(
							'font_size' => '0.9rem',
						),
						'post_taxonomy:1' => array(
							'taxonomy_name' => 'category',
							'font_size' => '0.9rem',
						),
						'post_taxonomy:2' => array(
							'taxonomy_name' => 'post_tag',
							'font_size' => '0.9rem',
						),
						'post_content:1' => array(
						),
						'btn:1' => array(
							'style' => 'outlined',
							'design_options' => array(
								'margin_top_default' => '1.5rem',
							),
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
				'blog_side_date' => array(
					'title' => 'Blog Side Date',
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
						'post_content:1' => array(
						),
						'post_date:1' => array(
							'format' => 'custom',
							'format_custom' => 'M',
							'text_styles' => array(
								0 => 'uppercase',
							),
							'font_size' => '0.9rem',
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
							'font_size' => '1.5rem',
							'design_options' => array(
								'margin_bottom_default' => '0.4rem',
							),
						),
						'post_author:1' => array(
							'font_size' => '0.9rem',
							'icon' => 'far|user',
						),
						'post_comments:1' => array(
							'font_size' => '0.9rem',
							'icon' => 'far|comments',
						),
						'post_taxonomy:1' => array(
							'taxonomy_name' => 'post_tag',
							'font_size' => '0.9rem',
							'icon' => 'far|tags',
						),
						'post_taxonomy:2' => array(
							'taxonomy_name' => 'category',
							'font_size' => '0.9rem',
							'icon' => 'far|folder-open',
						),
						'vwrapper:2' => array(
							'alignment' => 'center',
							'valign' => 'middle',
							'design_options' => array(
								'border_top_default' => '2px',
								'border_right_default' => '2px',
								'border_bottom_default' => '2px',
								'border_left_default' => '2px',
							),
							'color_border' => us_get_option( 'color_content_faded' ),
							'border_radius' => '5',
							'el_class' => 'grid_wrapper_circle',
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
			)
		);

		return $templates_config;
	}

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
