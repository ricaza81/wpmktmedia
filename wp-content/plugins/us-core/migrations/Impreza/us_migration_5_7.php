<?php

class us_migration_5_7 extends US_Migration_Translator {

	/**
	 * @var bool Possibly dangerous translation that needs to be migrated manually
	 */
	public $should_be_manual = TRUE;

	private $page_blocks = NULL;
	private $current_post_id = NULL;
	private $content_has_row = NULL;

	// Content
	public function translate_content( &$content ) {
		global $us_migration_current_post_id;
		if ( $us_migration_current_post_id != $this->current_post_id ) {
			$this->current_post_id = $us_migration_current_post_id;
			$this->content_has_row = ( strpos( $content, '[vc_row' ) !== FALSE ) ? TRUE : FALSE;
		}

		return $this->_translate_content( $content );
	}

	public function translate_vc_row( &$name, &$params, &$content ) {
		global $us_row_is_fullwidth;
		$us_row_is_fullwidth = ( ! empty( $params['width'] ) AND $params['width'] == 'full' ) ? TRUE : FALSE;

		return FALSE;
	}

	// Contact Form
	public function translate_us_cform( &$name, &$params, &$content ) {
		$items = array();

		// Name
		if ( ! isset( $params['name_field'] ) ) {
			$items[] = array(
				'type' => 'text',
				'placeholder' => us_translate( 'Name' ),
				'required' => '1',
				'icon' => 'far|user',
			);
		} elseif ( $params['name_field'] == 'shown' ) {
			$items[] = array(
				'type' => 'text',
				'placeholder' => us_translate( 'Name' ),
				'icon' => 'far|user',
			);
		}

		// Email
		if ( ! isset( $params['email_field'] ) ) {
			$items[] = array(
				'type' => 'text',
				'placeholder' => us_translate( 'Email' ),
				'required' => '1',
				'icon' => 'far|envelope',
			);
		} elseif ( $params['email_field'] == 'shown' ) {
			$items[] = array(
				'type' => 'text',
				'placeholder' => us_translate( 'Email' ),
				'icon' => 'far|envelope',
			);
		}

		// Phone
		if ( ! isset( $params['phone_field'] ) ) {
			$items[] = array(
				'type' => 'text',
				'placeholder' => __( 'Phone', 'us' ),
				'required' => '1',
				'icon' => 'far|phone',
			);
		} elseif ( $params['phone_field'] == 'shown' ) {
			$items[] = array(
				'type' => 'text',
				'placeholder' => __( 'Phone', 'us' ),
				'icon' => 'far|phone',
			);
		}

		// Message
		if ( ! isset( $params['message_field'] ) ) {
			$items[] = array(
				'type' => 'textarea',
				'placeholder' => us_translate( 'Text' ),
				'required' => '1',
				'icon' => 'far|pencil',
			);
		} elseif ( $params['message_field'] == 'shown' ) {
			$items[] = array(
				'type' => 'textarea',
				'placeholder' => us_translate( 'Text' ),
				'icon' => 'far|pencil',
			);
		}

		// Captcha
		if ( isset( $params['captcha_field'] ) ) {
			$items[] = array(
				'type' => 'captcha',
				'label' => __( 'Enter the equation result to proceed', 'us' ),
				'icon' => 'far|question-circle',
			);
			unset( $params['captcha_field'] );
		}

		// Agreement
		if ( isset( $params['checkbox_field'] ) ) {
			$items[] = array(
				'type' => 'agreement',
				'value' => $content,
			);
			unset( $params['checkbox_field'] );
		}

		if ( ! isset( $params['button_text'] ) ) {
			$params['button_text'] = us_translate( 'Submit' );
		}

		// Remove params, they were moved to items param
		if ( isset( $params['name_field'] ) ) {
			unset( $params['name_field'] );
		}
		if ( isset( $params['email_field'] ) ) {
			unset( $params['email_field'] );
		}
		if ( isset( $params['phone_field'] ) ) {
			unset( $params['phone_field'] );
		}
		if ( isset( $params['message_field'] ) ) {
			unset( $params['message_field'] );
		}

		// Content was moved to agreement field so remove it from shortcode
		$content = '';
		$params['items'] = urlencode( json_encode( $items ) );

		return TRUE;
	}

	// Grid
	public function translate_us_grid( &$name, &$params, &$content ) {
		global $us_row_is_fullwidth;

		if ( ! empty( $params['carousel_arrows'] ) ) {
			if ( $us_row_is_fullwidth ) {
				$params['carousel_arrows_style'] = 'block';
				$params['carousel_arrows_pos'] = 'inside';
				$params['carousel_arrows_size'] = '3rem';
			} else {
				$params['carousel_arrows_offset'] = '1rem';
			}
		}

		// Migrating Overriding Link param from Grid Layout to the shortcode
		if ( ! empty( $params['items_layout'] ) ) {

			// If Grid Layout is a template and it is a portfolio
			if ( $templates_config = us_config( 'grid-templates', array(), TRUE ) AND isset( $templates_config[ $params['items_layout'] ] ) AND substr( $params['items_layout'], 0, 9 ) == 'portfolio' ) {
				// Overriding link param changes to 'post'
				$params['overriding_link'] = 'post';
			} elseif ( $grid_layout = get_post( (int) $params['items_layout'] ) ) {

				// In other case - checking settings of Grid Layout post
				if ( $grid_layout instanceof WP_Post AND $grid_layout->post_type === 'us_grid_layout' ) {
					if ( ! empty( $grid_layout->post_content ) AND strpos( $grid_layout->post_content, '{' ) === 0 ) {
						try {
							$grid_layout_settings = json_decode( $grid_layout->post_content, TRUE );
							if ( ! empty ( $grid_layout_settings['default']['options']['link'] ) AND $grid_layout_settings['default']['options']['link'] != 'none' ) {
								$params['overriding_link'] = $grid_layout_settings['default']['options']['link'];
								if ( ! empty ( $grid_layout_settings['default']['options']['popup_width'] ) ) {
									$params['popup_width'] = $grid_layout_settings['default']['options']['popup_width'];
								}
							}
						}
						catch ( Exception $e ) {
						}
					}
				}
			}
		} else {
			$params['items_layout'] = 'blog_classic';
		}

		return TRUE;
	}

	// Gallery to Grid
	public function translate_us_gallery( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'attachment';
		$params['items_quantity'] = '';
		$masonry = FALSE;

		if ( ! empty( $params['ids'] ) ) {
			$params['images'] = $params['ids'];
			unset( $params['ids'] );
		}
		if ( isset( $params['layout'] ) AND $params['layout'] == 'masonry' ) {
			$params['type'] = 'masonry';
			$masonry = TRUE;
			unset( $params['layout'] );
		}
		if ( isset( $params['orderby'] ) AND $params['orderby'] == '1' ) {
			$params['orderby'] = 'rand';
		} else {
			$params['orderby'] = 'post__in';
		}
		if ( isset( $params['indents'] ) AND $params['indents'] == '1' ) {
			$params['items_gap'] = '4px';
			unset( $params['indents'] );
		} else {
			$params['items_gap'] = '';
		}
		if ( ! isset( $params['columns'] ) OR empty( $params['columns'] ) ) {
			$params['columns'] = '6';
		}
		if ( isset( $params['meta'] ) AND $params['meta'] == '1' ) {
			if ( isset( $params['meta_style'] ) AND $params['meta_style'] == 'modern' ) {
				$params['items_layout'] = 'gallery_with_titles_over';
				unset( $params['meta_style'] );
			} else {
				$params['items_layout'] = 'gallery_with_titles_below';
			}
			unset( $params['meta'] );
		} else {
			$params['items_layout'] = 'gallery_default';
		}
		if ( ! isset( $params['img_size'] ) OR empty( $params['img_size'] ) OR $params['img_size'] == 'default' ) {
			if ( $masonry ) {
				$params['img_size'] = ( $params['columns'] < 6 ) ? 'large' : 'medium';
			} else {
				if ( $params['columns'] == 1 ) {
					$params['img_size'] = 'full';
				} elseif ( $params['columns'] < 5 ) {
					$params['img_size'] = 'us_600_600_crop';
				} elseif ( $params['columns'] < 8 ) {
					$params['img_size'] = 'us_350_350_crop';
				} else {
					$params['img_size'] = 'thumbnail';
				}
			}
		}
		if ( isset( $params['link'] ) AND $params['link'] == '1' ) {
			unset( $params['link'] );
		} else {
			$params['overriding_link'] = 'popup_post_image';
		}

		$params['breakpoint_1_cols'] = $params['columns'];
		$params['breakpoint_2_width'] = '768px';
		$params['breakpoint_3_width'] = '480px';

		if ( $params['columns'] > 7 ) {
			$params['breakpoint_2_cols'] = '4';
			$params['breakpoint_3_cols'] = '3';
		} elseif ( $params['columns'] == 3 ) {
			$params['breakpoint_2_cols'] = '3';
			$params['breakpoint_3_cols'] = '2';
		} else {
			$params['breakpoint_2_cols'] = '4';
			$params['breakpoint_3_cols'] = '2';
		}

		return TRUE;
	}

	// IconBox
	public function translate_us_iconbox( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['bg_color'] ) ) {
			$params['circle_color'] = $params['bg_color'];

			unset( $params['bg_color'] );
			$changed = TRUE;
		}

		return $changed;
	}

	// Logos Showcase
	public function translate_us_logos( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'attachment';
		$params['items_quantity'] = '';
		$params['items_gap'] = '';
		$params['items_valign'] = '1';

		if ( empty( $params['columns'] ) ) {
			$params['columns'] = 5;
		}
		if ( empty( $params['type'] ) ) {
			$params['type'] = 'carousel';
		}
		if ( empty( $params['img_size'] ) ) {
			$params['img_size'] = 'medium';
		}

		$images = array();

		if ( ! empty( $params['items'] ) ) {
			$items = json_decode( urldecode( $params['items'] ), TRUE );
			if ( ! is_array( $items ) ) {
				$items = array();
			}
			foreach ( $items as $index => $item ) {
				$item['image'] = isset( $item['image'] ) ? $item['image'] : '';
				$item['link'] = isset( $item['link'] ) ? $item['link'] : '';
				$img_id = intval( $item['image'] );
				if ( ! $img_id ) {
					continue;
				}

				$images[] = $img_id;

				// save only url from link to attachement meta
				$link_array = array( 'url' => '', 'title' => '', 'target' => '', 'rel' => '' );
				$params_pairs = explode( '|', $item['link'] );
				if ( ! empty( $params_pairs ) ) {
					foreach ( $params_pairs as $pair ) {
						$param = explode( ':', $pair, 2 );
						if ( ! empty( $param[0] ) AND isset( $param[1] ) ) {
							$link_array[ $param[0] ] = rawurldecode( $param[1] );
						}
					}
				}
				update_post_meta( intval( $item['image'] ), 'us_attachment_link', $link_array['url'] );
			}

			$params['images'] = implode( ',', $images );
			unset( $params['items'] );
		}

		if ( isset ( $params['with_indents'] ) ) {
			$with_indents = $params['with_indents'];
			unset( $params['with_indents'] );
		} else {
			$with_indents = FALSE;
		}

		if ( isset( $params['style'] ) ) {
			$params['items_layout'] = $this->get_logos_grid_layout( $params['style'], $with_indents );
			unset( $params['style'] );
		} else {
			$params['items_layout'] = $this->get_logos_grid_layout( '1', $with_indents );
		}

		return TRUE;
	}

	/* Create Grid Layout based on former Logos Showcase settings */
	private function get_logos_grid_layout( $style, $with_indents ) {

		if ( $with_indents ) {
			$indents_options = array(
				'padding_top_default' => '2rem',
				'padding_right_default' => '2rem',
				'padding_bottom_default' => '2rem',
				'padding_left_default' => '2rem',
			);
		} else {
			$indents_options = array();
		}
		if ( $style == 1 ) {
			$outline_options = array(
				'border_top_default' => '2px',
				'border_right_default' => '2px',
				'border_bottom_default' => '2px',
				'border_left_default' => '2px',
			);
		} else {
			$outline_options = array();
		}

		$logos_grid_layout = array(
			'data' => array(
				'post_image:1' => array(
					'link' => 'custom',
					'custom_link' => array(
						'url' => '{{us_attachment_link}}',
						'target' => '_blank',
					),
					'stretch' => FALSE,
					'design_options' => array_merge( $indents_options, $outline_options ),
					'color_border' => ( $style == 1 ) ? 'transparent' : '',
					'color_border_hover' => ( $style == 1 ) ? us_get_option( 'color_content_link' ) : '',
					'border_radius' => us_get_option( 'rounded_corners' ) ? '0.3rem' : '',
					'hover' => ( $style == 3 ) ? FALSE : TRUE,
					'opacity' => ( $style == 3 ) ? '1' : '0.66',
					'opacity_hover' => '1',
					'transition_duration' => '0.3s',
				),
			),
			'default' => array(
				'layout' => array(
					'middle_center' => array(
						'post_image:1',
					),
				),
			),
		);

		$layout_title = 'Logos Showcase';
		if ( $style == 1 ) {
			$layout_title .= ' Fade + Outline hover';
		} elseif ( $style == 2 ) {
			$layout_title .= ' Fade hover';
		}
		if ( $with_indents ) {
			$layout_title .= ' (with indents)';
		}

		$exist_grid_layout = get_page_by_title( $layout_title, OBJECT, 'us_grid_layout' );
		if ( ! empty( $exist_grid_layout ) ) {
			return $exist_grid_layout->ID;
		} else {
			ob_start();
			$post_id = wp_insert_post(
				array(
					'post_type' => 'us_grid_layout',
					'post_status' => 'publish',
					'post_title' => $layout_title,
					'post_content' => json_encode( $logos_grid_layout ),
				)
			);
			ob_end_clean();

			return $post_id;
		}
	}

	// Theme Options
	public function translate_theme_options( &$options ) {
		global $us_migration_doing_fallback;

		// Set Archives
		if ( $options['header_archive_id'] == '__defaults__' ) {
			$options['header_archive_id'] = $options['header_id'];
		}
		if ( $options['titlebar_archive_id'] == '__defaults__' ) {
			$options['titlebar_archive_id'] = $options['titlebar_id'];
		}
		if ( $options['sidebar_archive_id'] == '__defaults__' ) {
			$options['sidebar_archive_id'] = $options['sidebar_id'];
		}
		if ( $options['footer_archive_id'] == '__defaults__' ) {
			$options['footer_archive_id'] = $options['footer_id'];
		}

		// Set Search Results page
		if ( empty( $options['search_page'] ) ) {
			$options['search_page'] = $this->add_posts_page( 'search', $options );
			if ( isset( $us_migration_doing_fallback ) AND $us_migration_doing_fallback ) {
				$this->update_usof_option( 'search_page', $options['search_page'] );
			}
		}

		// Set Posts page
		if ( empty( $options['posts_page'] ) ) {
			$options['posts_page'] = $this->add_posts_page( 'blog', $options );
			if ( isset( $us_migration_doing_fallback ) AND $us_migration_doing_fallback ) {
				$this->update_usof_option( 'posts_page', $options['posts_page'] );
			}
		}

		// Set Archives content
		if ( ! isset( $options['content_archive_id'] ) ) {
			$options['content_archive_id'] = $this->add_archive_content_template();
			if ( isset( $us_migration_doing_fallback ) AND $us_migration_doing_fallback ) {
				$this->update_usof_option( 'content_archive_id', $options['content_archive_id'] );
			}
		}

		// Set Authors content
		if ( ! isset( $options['content_author_id'] ) ) {
			$options['content_author_id'] = $this->add_archive_content_template( TRUE );
			if ( isset( $us_migration_doing_fallback ) AND $us_migration_doing_fallback ) {
				$this->update_usof_option( 'content_author_id', $options['content_author_id'] );
			}
		}

		// Create template for Portfolio Pages with prev/next navigation
		if ( isset( $options['portfolio_nav'] ) AND $options['portfolio_nav'] AND ! isset( $options['content_portfolio_id'] ) ) {
			$options['content_portfolio_id'] = $this->add_portfolio_content_template( $options['portfolio_nav_invert'], $options['portfolio_nav_category'] );
			if ( isset( $us_migration_doing_fallback ) AND $us_migration_doing_fallback ) {
				$this->update_usof_option( 'content_portfolio_id', $options['content_portfolio_id'] );
			}
		}

		// Create template once and use it for Posts and CPTs
		if ( ! isset( $options['content_post_id'] ) ) {
			$posts_template_id = $this->add_post_content_template();
			$options['content_post_id'] = $posts_template_id;
			if ( isset( $us_migration_doing_fallback ) AND $us_migration_doing_fallback ) {
				$this->update_usof_option( 'content_post_id', $posts_template_id );
			}
			if ( ! empty( $options['cpt_as_posts'] ) ) {
				foreach ( $options['cpt_as_posts'] as $cpt ) {
					$options[ 'content_' . $cpt . '_id' ] = $posts_template_id;
					if ( isset( $us_migration_doing_fallback ) AND $us_migration_doing_fallback ) {
						$this->update_usof_option( 'content_' . $cpt . '_id', $posts_template_id );
					}
				}
			}
		}

		/* Add grid CSS checkbox if optimize CSS option is ON */
		if ( isset( $options['optimize_assets'] ) AND $options['optimize_assets'] AND is_array( $options['assets'] ) ) {
			$options['assets'] = array_merge( $options['assets'], array( 'pagination', 'grid' ) );
		}

		if ( $options['shop_listing_style'] == 'custom' AND ! empty( $options['shop_layout'] ) ) {
			// Migrating Overriding Link param from Grid Layout for Shop Options
			// If Grid Layout is a template and it is a portfolio
			if ( $templates_config = us_config( 'grid-templates', array(), TRUE ) AND isset( $templates_config[ $options['shop_layout'] ] ) AND substr( $options['shop_layout'] , 0, 9 ) == 'portfolio' ) {
				// Overriding link param changes to 'post'
				$options['shop_overriding_link'] = 'post';
			} elseif ( $grid_layout = get_post( (int) $options['shop_layout'] ) ) {

				// In other case - checking settings of Grid Layout post
				if ( $grid_layout instanceof WP_Post AND $grid_layout->post_type === 'us_grid_layout' ) {
					if ( ! empty( $grid_layout->post_content ) AND strpos( $grid_layout->post_content, '{' ) === 0 ) {
						try {
							$grid_layout_settings = json_decode( $grid_layout->post_content, TRUE );
							if ( ! empty ( $grid_layout_settings['default']['options']['link'] ) AND $grid_layout_settings['default']['options']['link'] != 'none' ) {
								$options['shop_overriding_link'] = $grid_layout_settings['default']['options']['link'];
								if ( ! empty ( $grid_layout_settings['default']['options']['popup_width'] ) ) {
									$options['shop_popup_width'] = $grid_layout_settings['default']['options']['popup_width'];
								}
							}
						}
						catch ( Exception $e ) {
						}
					}
				}
			}

		}

		unset( $options['portfolio_nav'] );
		unset( $options['portfolio_nav_invert'] );
		unset( $options['portfolio_nav_category'] );

		// Former Blog options
		unset( $options['cpt_as_posts'] );
		unset( $options['blog_layout'] );
		unset( $options['blog_img_size'] );
		unset( $options['blog_type'] );
		unset( $options['blog_cols'] );
		unset( $options['blog_items_gap'] );
		unset( $options['blog_pagination'] );
		unset( $options['blog_pagination_btn_style'] );
		unset( $options['archive_layout'] );
		unset( $options['archive_img_size'] );
		unset( $options['archive_type'] );
		unset( $options['archive_cols'] );
		unset( $options['archive_items_gap'] );
		unset( $options['archive_pagination'] );
		unset( $options['archive_pagination_btn_style'] );
		unset( $options['search_layout'] );
		unset( $options['search_img_size'] );
		unset( $options['search_type'] );
		unset( $options['search_cols'] );
		unset( $options['search_items_gap'] );
		unset( $options['search_pagination'] );
		unset( $options['search_pagination_btn_style'] );
		unset( $options['blog_breakpoint_1_width'] );
		unset( $options['blog_breakpoint_2_width'] );
		unset( $options['blog_breakpoint_3_width'] );
		unset( $options['blog_breakpoint_1_cols'] );
		unset( $options['blog_breakpoint_2_cols'] );
		unset( $options['blog_breakpoint_3_cols'] );

		return TRUE;
	}

	// Meta settings
	public function translate_meta( &$meta, $post_type ) {
		$changed = FALSE;

		if ( $post_type == 'post' AND ( ! empty( $meta['us_post_preview_layout'][0] ) OR ! empty( $meta['us_sidebar'][0] ) OR $this->content_has_row ) ) {
			global $us_migration_doing_fallback, $us_migration_current_post_id;
			if ( $us_migration_doing_fallback AND ! empty( $meta['us_content_id'][0] ) ) {
				return FALSE;
			}
			if ( ! empty( $meta['us_post_preview_layout'][0] ) ) {
				$featured_image_layout = $meta['us_post_preview_layout'][0];
				unset( $meta['us_post_preview_layout'] );
			} else {
				$featured_image_layout = NULL;
			}

			if ( empty( $meta['us_content_id'][0] ) ) {
				$has_sidebar = NULL;

				if ( ! empty( $meta['us_sidebar'][0] ) ) {
					if ( $meta['us_sidebar'][0] == 'custom' ) {
						$has_sidebar = TRUE;
					} elseif ( $meta['us_sidebar'][0] == 'hide' ) {
						$has_sidebar = FALSE;
					}
				}

				$meta['us_content'][0] = 'custom';
				$meta['us_content_id'][0] = $this->add_post_content_template( $featured_image_layout, $has_sidebar );

				if ( $us_migration_doing_fallback ) {
					global $wpdb;
					$existing_postmeta_results = $wpdb->get_row($wpdb->prepare( "
							SELECT COUNT(*) AS cnt
							FROM {$wpdb->postmeta}
							WHERE
								meta_key = %s
								AND post_id = %s
							LIMIT 0,1
						",
					'us_content_id', $us_migration_current_post_id ) );

					if ( ! $existing_postmeta_results->cnt ) {
						$wpdb->insert( $wpdb->postmeta, array(
							'post_id' => $us_migration_current_post_id,
							'meta_key' => 'us_content_id',
							'meta_value' => $meta['us_content_id'][0],
						) );
					}
				}
			}

			$changed = TRUE;
		}

		return $changed;
	}

	private function update_usof_option( $option, $value ) {
		global $usof_options;
		usof_load_options_once();

		$updated_options = $usof_options;

		if ( $updated_options[ $option ] != $value ) {
			$updated_options[ $option ] = $value;
			$updated_options = array_merge( usof_defaults(), $updated_options );
			remove_action( 'usof_after_save', 'us_generate_asset_files' );
			usof_save_options( $updated_options );
			add_action( 'usof_after_save', 'us_generate_asset_files' );
		}

	}

	// Create Page Block for Posts based on former Posts settings
	private function add_post_content_template( $featured_image_layout = NULL, $has_sidebar = NULL ) {
		global $usof_options;
		usof_load_options_once();

		// If featured_image_layout is not set, get it from Theme Options
		if ( $featured_image_layout === NULL ) {
			$featured_image_layout = $usof_options['post_preview_layout'];
		}

		// If sidebar is not set, get it from Theme Options
		if ( $has_sidebar === NULL ) {
			if ( ! empty( $usof_options['sidebar_post_id'] ) ) {
				if ( $usof_options['sidebar_post_id'] == '__defaults__' AND empty( $usof_options['sidebar_id'] ) ) {
					$has_sidebar = FALSE;
				} else {
					$has_sidebar = TRUE;
				}
			} else {
				$has_sidebar = FALSE;
			}
		}

		// Get row size for margin
		if ( ! empty( $usof_options['row_height'] ) AND $usof_options['row_height'] == 'small' ) {
			$row_height = '2rem';
		} else {
			$row_height = '4rem';
		}

		// Generate Page Block title
		$post_title = 'Blog Post with ' . $featured_image_layout . ' preview';

		// Generate Page Block content
		$post_content = '[vc_row';

		if ( $featured_image_layout == 'basic' ) { // Standard
			$post_content .= ' el_class="for_blogpost" css=".vc_custom_12345{padding-bottom: 0px !important;}"][vc_column]';
			$post_content .= '[us_post_image link="none" media_preview="1" thumbnail_size="' . $usof_options['post_preview_img_size'] . '"]';
		} elseif ( $featured_image_layout == 'modern' ) { // Modern
			$post_content .= ' el_class="for_blogpost gradient_overlay" color_scheme="primary" us_bg_image_source="featured" us_bg_overlay_color="rgba(0,0,0,0.5)"';
			if ( $has_sidebar ) {
				$post_content .= ' css=".vc_custom_12345{margin-top: ' . $row_height . ' !important;padding-top: 40% !important;padding-right: 2.5rem !important;padding-bottom: 2rem !important;padding-left: 2.5rem !important;}"]';
			} else {
				$post_content .= ' css=".vc_custom_12345{padding-top: 18% !important;padding-right: 2.5rem !important;padding-bottom: 2rem !important;padding-left: 2.5rem !important;}"]';
			}
			$post_content .= '[vc_column]';
		} elseif ( $featured_image_layout == 'trendy' ) { // Trendy
			$post_content .= ' el_class="for_blogpost" content_placement="middle" color_scheme="primary" us_bg_image_source="featured" us_bg_overlay_color="rgba(0,0,0,0.5)"';
			if ( $has_sidebar ) {
				$post_content .= ' css=".vc_custom_12345{margin-top: ' . $row_height . ' !important;padding-top: 24% !important;padding-right: 2.5rem !important;padding-bottom: 24% !important;padding-left: 2.5rem !important;}"]';
			} else {
				$post_content .= ' css=".vc_custom_12345{padding-top: 8% !important;padding-right: 2.5rem !important;padding-bottom: 8% !important;padding-left: 2.5rem !important;}" us_bg_parallax="vertical"]';
			}
			$post_content .= '[vc_column el_class="align_center"]';
			// Categories
			if ( in_array( 'categories', $usof_options['post_meta'] ) ) {
				$post_content .= '[us_post_taxonomy style="badge" font_size="0.7rem" text_styles="bold,uppercase"]';
			}
		} else { // None
			$post_content .= ' el_class="for_blogpost" css=".vc_custom_12345{padding-bottom: 0px !important;}"][vc_column]';
		}
		// Post Title
		$post_content .= '[us_post_title link="none" tag="h1" font="heading"';
		if ( $featured_image_layout == 'trendy' ) {
			$post_content .= ' text_styles="bold"';
		}
		$post_content .= ']';
		// Post Elements
		$post_content .= '[us_hwrapper wrap="1" el_class="highlight_faded"';
		if ( $featured_image_layout == 'trendy' ) {
			$post_content .= ' alignment="center"';
		}
		$post_content .= ']';
		// Date
		$post_content .= '[us_post_date icon="far|clock" font_size="0.9rem"';
		if ( ! in_array( 'date', $usof_options['post_meta'] ) ) {
			$post_content .= ' el_class="hidden"';
		}
		$post_content .= ']';
		// Author
		$post_content .= '[us_post_author icon="far|user" font_size="0.9rem"';
		if ( ! in_array( 'author', $usof_options['post_meta'] ) ) {
			$post_content .= ' el_class="hidden"';
		}
		$post_content .= ']';
		// Categories
		if ( in_array( 'categories', $usof_options['post_meta'] ) AND $featured_image_layout != 'trendy' ) {
			$post_content .= '[us_post_taxonomy icon="far|folder-open" font_size="0.9rem"]';
		}
		// Comments Amount
		if ( in_array( 'comments', $usof_options['post_meta'] ) ) {
			$post_content .= '[us_post_comments layout="amount" icon="far|comments" font_size="0.9rem"]';
		}
		$post_content .= '[/us_hwrapper][/vc_column][/vc_row]';

		// Content
		$post_content .= '[vc_row';
		if ( $this->content_has_row ) {
			$post_title .= ' & fullwidth content';
			$post_content .= ' height="auto" width="full"';
			if ( $has_sidebar ) {
				$post_content .= ' css=".vc_custom_1122334{padding-top: 0px !important;}"';
			}
		} elseif ( $has_sidebar OR in_array( $featured_image_layout, array( 'basic', 'none' ) ) ) {
			$post_content .= ' height="small"';
		}
		$post_content .= '][vc_column]';
		$post_content .= '[us_post_content type="full_content"]';
		$post_content .= '[/vc_column][/vc_row]';
		$post_content .= '[vc_row height="auto"][vc_column]';

		// Tags
		if ( in_array( 'tags', $usof_options['post_meta'] ) ) {
			$post_content .= '[us_post_taxonomy taxonomy_name="post_tag" color_link="" icon="far|tags" font_size="0.9rem"]';
			$post_content .= '[us_separator size="small"]';
		}
		// Sharing
		if ( ! empty( $usof_options['post_sharing'] ) ) {
			$post_content .= '[us_sharing type="' . $usof_options['post_sharing_type'] . '"';
			$post_content .= ' providers="' . implode( ',', $usof_options['post_sharing_providers'] ) . '"]';
			$post_content .= '[us_separator size="small"]';
		}
		// Author Box
		if ( ! empty( $usof_options['post_author_box'] ) ) {
			$post_content .= '[us_post_author color_link="" avatar="1" avatar_pos="left" website="1" info="1" font_size="1.2rem" css=".vc_custom_11223345{padding: 2rem !important;border: 2px solid ' . $usof_options['color_content_border'] . ' !important;border-radius: 0.3rem !important;}"]';
			$post_content .= '[us_separator size="small"]';
		} else {
			$post_content .= '[us_separator size="small" show_line="1"]';
		}
		// Prev/Next navigation
		if ( ! empty( $usof_options['post_nav'] ) ) {
			$post_content .= '[us_post_navigation';
			$post_content .= ( ! empty( $usof_options['post_nav_layout'] ) AND $usof_options['post_nav_layout'] == 'sided' ) ? ' layout="sided"' : '';
			$post_content .= ( ! empty( $usof_options['post_nav_invert'] ) ) ? ' invert="1"' : '';
			$post_content .= ( ! empty( $usof_options['post_nav_category'] ) ) ? ' in_same_term="1"' : '';
			$post_content .= ']';
			$post_content .= ( ! empty( $usof_options['post_nav_layout'] ) AND $usof_options['post_nav_layout'] == 'sided' ) ? '' : '[us_separator size="small" show_line="1"]';
		}
		// Related Posts
		if ( ! empty( $usof_options['post_related'] ) ) {
			$post_content .= ( ! empty( $usof_options['post_related_title'] ) ) ? '[vc_column_text]<h4>' . strip_tags( $usof_options['post_related_title'] ) . '</h4>[/vc_column_text]' : '';
			$post_content .= '[us_separator size="small"]';
			$post_content .= '[us_grid post_type="related"';
			$post_content .= ( ! empty( $usof_options['post_related_type'] ) AND $usof_options['post_related_type'] == 'category' ) ? '' : ' related_taxonomy="post_tag"';
			$post_content .= ( ! empty( $usof_options['post_related_orderby'] ) ) ? ' orderby="' . $usof_options['post_related_orderby'] . '"' : '';
			$post_content .= ( ! empty( $usof_options['post_related_quantity'] ) ) ? ' items_quantity="' . $usof_options['post_related_quantity'] . '"' : '';
			$post_content .= ( ! empty( $usof_options['post_related_layout'] ) ) ? ' items_layout="' . $usof_options['post_related_layout'] . '"' : '';
			$post_content .= ( ! empty( $usof_options['post_related_img_size'] ) ) ? ' img_size="' . $usof_options['post_related_img_size'] . '"' : '';
			$post_content .= ( ! empty( $usof_options['post_related_cols'] ) ) ? ' columns="' . $usof_options['post_related_cols'] . '"' : '';
			$post_content .= ( ! empty( $usof_options['post_related_items_gap'] ) ) ? ' items_gap="' . $usof_options['post_related_items_gap'] . '"]' : '';
			$post_content .= '[us_separator size="small" show_line="1"]';
		}
		// Comments
		$post_content .= '[us_post_comments]';
		$post_content .= '[us_separator size="large"][/vc_column][/vc_row]';
		$content_hash = md5( $post_content );

		// Check for existing page block with same content
		global $wpdb;
		$existing_posts_results = $wpdb->get_results( $wpdb->prepare( "
				SELECT post_id
				FROM {$wpdb->postmeta}
				WHERE
					meta_key = %s
					AND meta_value = %s
				LIMIT 0,1
			",
		'us_migration_content_hash', $content_hash ) );

		if ( count( $existing_posts_results ) > 0 ) {
			$existing_post = $existing_posts_results[0];
			return $existing_post->post_id;
		}

		// Modify Page Block title if has sidebar
		if ( $has_sidebar ) {
			$post_title .= ' (for posts with sidebar)';
		}

		$block_post_array = array(
			'post_type' => 'us_page_block',
			'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
			'post_status' => 'publish',
			'post_title' => $post_title,
			'post_content' => $post_content,
		);

		ob_start();
		$wpdb->insert( $wpdb->posts, $block_post_array );
		$block_post_id = $wpdb->insert_id;
		update_post_meta( $block_post_id, 'us_migration_content_hash', $content_hash );
		ob_end_clean();

		return $block_post_id;
	}

	// Create Page Block with params, based on former Archive Pages settings
	private function add_archive_content_template( $for_author_pages = FALSE ) {
		global $usof_options;
		usof_load_options_once();

		$grid_params = array(
			'items_layout' => $usof_options['archive_layout'],
			'img_size' => $usof_options['archive_img_size'],
			'type' => $usof_options['archive_type'],
			'columns' => $usof_options['archive_cols'],
			'items_gap' => $usof_options['archive_items_gap'],
			'pagination' => $usof_options['archive_pagination'],
			'pagination_btn_style' => $usof_options['archive_pagination_btn_style'],
		);

		$post_content = '[vc_row][vc_column]';

		// Author pages
		if ( $for_author_pages ) {
			$post_title = 'Authors template';
			$post_content .= '[us_post_author link="none" avatar="1" avatar_pos="left" posts_count="1" website="1" info="1" font_size="1.2rem" css=".vc_custom_1122334455{padding: 2rem !important;border: 2px solid ' . $usof_options['color_content_border'] . ' !important;border-radius: 0.3rem !important;}"]';
			$post_content .= '[us_separator]';
		} else {
			$post_title = 'Archives template';
		}

		$post_content .= '[us_grid post_type="current_query" ';
		foreach ( $grid_params as $key => $value ) {
			$post_content .= $key . '="' . $value . '" ';
		}
		$post_content .= '][/vc_column][/vc_row]';
		$content_hash = md5( $post_content );

		// Check for existing page block with same content
		global $wpdb;
		$existing_posts_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", 'us_migration_content_hash', $content_hash )
		);
		if ( count( $existing_posts_results ) > 0 ) {
			$existing_post = $existing_posts_results[0];
			return $existing_post->post_id;
		}

		ob_start();
		$post_id = wp_insert_post(
			array(
				'post_type' => 'us_page_block',
				'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
				'post_status' => 'publish',
				'post_title' => $post_title,
				'post_content' => $post_content,
			)
		);
		add_post_meta( $post_id, 'us_migration_content_hash', $content_hash );
		ob_end_clean();

		return $post_id;
	}

	// Create page with Grid, based on former Theme Options > Blog
	private function add_posts_page( $type, $options ) {

		$post_title = ( $type == 'blog' ) ? 'Blog Home Page' : 'Search Results Page';

		// Check if the page already exists
		$existing_posts_page = get_posts(
			array(
				'meta_key' => 'us_special_page_type',
				'meta_value' => $type,
				'post_type' => 'page',
				'post_status' => 'publish',
				'posts_per_page' => 1,
			)
		);
		if ( count( $existing_posts_page ) > 0 ) {
			$existing_posts_page = $existing_posts_page[0];

			return $existing_posts_page->ID;
		}

		$grid_params = array(
			'items_layout' => $options[ $type . '_layout' ],
			'img_size' => $options[ $type . '_img_size' ],
			'type' => $options[ $type . '_type' ],
			'columns' => $options[ $type . '_cols' ],
			'items_gap' => $options[ $type . '_items_gap' ],
			'pagination' => $options[ $type . '_pagination' ],
			'pagination_btn_style' => $options[ $type . '_pagination_btn_style' ],
		);

		$post_content = '[vc_row][vc_column]';
		$post_content .= '[us_grid post_type="current_query" ';
		foreach ( $grid_params as $key => $value ) {
			$post_content .= $key . '="' . $value . '" ';
		}
		$post_content .= '][/vc_column][/vc_row]';

		ob_start();
		$post_id = wp_insert_post(
			array(
				'post_type' => 'page',
				'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
				'post_status' => 'publish',
				'post_title' => $post_title,
				'post_content' => $post_content,
			)
		);
		ob_end_clean();

		if ( isset( $options[ 'header_' . $type . '_id' ] ) ) {
			if ( empty( $options[ 'header_' . $type . '_id' ] ) ) {
				update_post_meta( $post_id, 'us_header', 'hide' );
			} elseif ( $options[ 'header_' . $type . '_id' ] != '__defaults__' ) {
				update_post_meta( $post_id, 'us_header', 'custom' );
				update_post_meta( $post_id, 'us_header_id', $options[ 'header_' . $type . '_id' ] );
			}
		}
		if ( isset( $options[ 'titlebar_' . $type . '_id' ] ) ) {
			if ( empty( $options[ 'titlebar_' . $type . '_id' ] ) ) {
				update_post_meta( $post_id, 'us_titlebar', 'hide' );
			} elseif ( $options[ 'titlebar_' . $type . '_id' ] != '__defaults__' ) {
				update_post_meta( $post_id, 'us_titlebar', 'custom' );
				update_post_meta( $post_id, 'us_titlebar_id', $options[ 'titlebar_' . $type . '_id' ] );
			}
		}
		if ( isset( $options[ 'sidebar_' . $type . '_id' ] ) ) {
			if ( empty( $options[ 'sidebar_' . $type . '_id' ] ) ) {
				update_post_meta( $post_id, 'us_sidebar', 'hide' );
			} elseif ( $options[ 'sidebar_' . $type . '_id' ] != '__defaults__' ) {
				update_post_meta( $post_id, 'us_sidebar', 'custom' );
				update_post_meta( $post_id, 'us_sidebar_id', $options[ 'sidebar_' . $type . '_id' ] );
			}
		}
		if ( isset( $options[ 'sidebar_' . $type . '_pos' ] ) ) {
			update_post_meta( $post_id, 'us_sidebar_pos', $options[ 'sidebar_' . $type . '_pos' ] );
		}
		if ( isset( $options[ 'footer_' . $type . '_id' ] ) ) {
			if ( empty( $options[ 'footer_' . $type . '_id' ] ) ) {
				update_post_meta( $post_id, 'us_footer', 'hide' );
			} elseif ( $options[ 'footer_' . $type . '_id' ] != '__defaults__' ) {
				update_post_meta( $post_id, 'us_footer', 'custom' );
				update_post_meta( $post_id, 'us_footer_id', $options[ 'footer_' . $type . '_id' ] );
			}
		}

		// Setting marker for posts page
		update_post_meta( $post_id, 'us_special_page_type', $type );

		return $post_id;
	}

	// Create Page Block for Portfolio Pages with Prev/Next navigation
	private function add_portfolio_content_template( $invert, $within_category ) {

		// Generate Page Block content
		$post_content = '[vc_row height="auto" width="full"][vc_column]';
		$post_content .= '[us_post_content type="full_content"]';
		$post_content .= '[us_post_navigation layout="sided"';
		if ( $invert ) {
			$post_content .= ' invert="1"';
		}
		if ( $within_category ) {
			$post_content .= ' in_same_term="1" taxonomy="us_portfolio_category"';
		}
		$post_content .= '][/vc_column][/vc_row]';
		$content_hash = md5( $post_content );

		// Check for existing page block with same content
		global $wpdb;
		$existing_posts_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", 'us_migration_content_hash', $content_hash )
		);
		if ( count( $existing_posts_results ) > 0 ) {
			$existing_post = $existing_posts_results[0];
			return $existing_post->post_id;
		}

		$block_post_array = array(
			'post_type' => 'us_page_block',
			'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
			'post_status' => 'publish',
			'post_title' => 'Content template: Portfolio with Prev/Next navigation',
			'post_content' => $post_content,
		);
		ob_start();
		$block_post_id = wp_insert_post( $block_post_array );
		add_post_meta( $block_post_id, 'us_migration_content_hash', $content_hash );
		ob_end_clean();

		return $block_post_id;
	}

}
