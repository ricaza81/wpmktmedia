<?php

class us_migration_6_0 extends US_Migration_Translator {
	/**
	 * @var bool Possibly dangerous translation that needs to be migrated manually
	 */
	public $should_be_manual = TRUE;

	private $previous_theme_options;

	// Get Grid Layout & Gap which were set in former Shop options
	private function get_products_grid( $type = '', $for_terms = FALSE ) {
		$layout = 'shop_standard';
		$gap = '1.2rem';
		if ( $this->previous_theme_options['shop_listing_style'] == 'modern' ) {
			$layout = 'shop_modern';
			$gap = '5px';
		} elseif ( $this->previous_theme_options['shop_listing_style'] == 'trendy' ) {
			$layout = ( $for_terms ) ? 'tile_21_right' : 'shop_trendy';
			$gap = ( $for_terms ) ? '10px' : '';
		} elseif ( $this->previous_theme_options['shop_listing_style'] == 'custom' ) {
			$layout = $this->previous_theme_options['shop_layout'];
			$gap = $this->previous_theme_options['shop_items_gap'];
		}

		return ( $type == 'gap' ) ? $gap : $layout;
	}

	/*
	 * CONTENT
	 * ==============================================================================================
	 */
	public function translate_content( &$content ) {
		$content = str_replace( '[cl-ib', '[cl_ib', $content );

		return $this->_translate_content( $content );
	}

	// Row
	public function translate_vc_row( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['gap'] ) ) {
			$params['gap'] = ( intval( $params['gap'] ) / 2 ) . 'px';
			$changed = TRUE;
		}

		return $changed;
	}

	// Inner Row
	public function translate_vc_inner_row( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['gap'] ) ) {
			$params['gap'] = ( intval( $params['gap'] ) / 2 ) . 'px';
			$changed = TRUE;
		}

		return $changed;
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

		// Grid into Carousel
		if ( ! empty( $params['type'] ) AND $params['type'] == 'carousel' ) {
			$name = 'us_carousel';
			unset( $params['type'] );
			$changed = TRUE;
		}

		return $changed;
	}


	// Product
	public function translate_product( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'ids';
		$params['no_items_message'] = '';
		$params['columns'] = '4';
		$params['items_layout'] = $this->get_products_grid();
		$params['items_gap'] = $this->get_products_grid( 'gap' );

		if ( ! empty( $params['id'] ) ) {
			$params['ids'] = $params['id'];
			unset( $params['id'] );
		}

		return TRUE;
	}

	// Products
	public function translate_products( &$name, &$params, &$content ) {
		$name = 'us_grid';
		if ( ! empty( $params['ids'] ) ) {
			$params['post_type'] = 'ids';
		} else {
			$params['post_type'] = 'product';
		}
		$params['no_items_message'] = '';
		$params['items_layout'] = $this->get_products_grid();
		$params['items_gap'] = $this->get_products_grid( 'gap' );

		if ( ! empty( $params['orderby'] ) AND $params['orderby'] == 'include' ) {
			$params['orderby'] = 'post__in';
		}

		return TRUE;
	}

	// Top Rated Products
	public function translate_top_rated_products( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'product';
		$params['orderby'] = 'rating';
		$params['items_layout'] = $this->get_products_grid();
		$params['items_gap'] = $this->get_products_grid( 'gap' );

		if ( isset( $params['per_page'] ) ) {
			$params['items_quantity'] = $params['per_page'];
			unset( $params['per_page'] );
		}

		return TRUE;
	}

	// Best Selling Products
	public function translate_best_selling_products( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'product';
		$params['orderby'] = 'popularity';
		$params['items_layout'] = $this->get_products_grid();
		$params['items_gap'] = $this->get_products_grid( 'gap' );

		if ( isset( $params['per_page'] ) ) {
			$params['items_quantity'] = $params['per_page'];
			unset( $params['per_page'] );
		}

		return TRUE;
	}

	// Product category
	public function translate_product_category( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'product';
		$params['items_layout'] = $this->get_products_grid();
		$params['items_gap'] = $this->get_products_grid( 'gap' );

		if ( isset( $params['per_page'] ) ) {
			$params['items_quantity'] = $params['per_page'];
			unset( $params['per_page'] );
		}
		if ( isset( $params['category'] ) ) {
			$params['taxonomy_product_cat'] = $params['category'];
			unset( $params['category'] );
		}
		if ( ! empty( $params['orderby'] ) ) {
			if ( $params['orderby'] == 'title' ) {
				$params['orderby'] = 'alpha';
			}
			if ( ! empty( $params['order'] ) AND $params['order'] == 'ASC' ) {
				if ( $params['orderby'] == 'date' ) {
					$params['orderby'] = 'date_asc';
				} elseif ( $params['orderby'] == 'modified' ) {
					$params['orderby'] = 'modified_asc';
				}
				unset( $params['order'] );
			}
		}

		return TRUE;
	}

	// Product categories
	public function translate_product_categories( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'taxonomy_terms';
		$params['related_taxonomy'] = 'product_cat';
		$params['terms_include'] = 'children';
		$params['terms_orderby'] = 'name';
		$params['no_items_message'] = '';
		$params['title_size'] = '1.4rem';
		$params['items_layout'] = $this->get_products_grid( '', TRUE );
		$params['items_gap'] = $this->get_products_grid( 'gap', TRUE );

		if ( isset( $params['number'] ) ) {
			$params['items_quantity'] = $params['number'];
			unset( $params['number'] );
		}
		if ( isset( $params['orderby'] ) ) {
			$params['terms_orderby'] = $params['orderby'];
		}

		return TRUE;
	}

	// Recent products
	public function translate_recent_products( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'product';
		$params['items_layout'] = $this->get_products_grid();
		$params['items_gap'] = $this->get_products_grid( 'gap' );

		if ( isset( $params['per_page'] ) ) {
			$params['items_quantity'] = $params['per_page'];
			unset( $params['per_page'] );
		}
		if ( ! empty( $params['orderby'] ) ) {
			if ( $params['orderby'] == 'title' ) {
				$params['orderby'] = 'alpha';
			}
			if ( ! empty( $params['order'] ) AND $params['order'] == 'ASC' ) {
				if ( $params['orderby'] == 'date' ) {
					$params['orderby'] = 'date_asc';
				} elseif ( $params['orderby'] == 'modified' ) {
					$params['orderby'] = 'modified_asc';
				}
				unset( $params['order'] );
			}
		}

		return TRUE;
	}

	// Featured products
	public function translate_featured_products( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'product';
		$params['products_include'] = 'featured';
		$params['items_layout'] = $this->get_products_grid();
		$params['items_gap'] = $this->get_products_grid( 'gap' );

		if ( isset( $params['per_page'] ) ) {
			$params['items_quantity'] = $params['per_page'];
			unset( $params['per_page'] );
		}
		if ( ! empty( $params['orderby'] ) ) {
			if ( $params['orderby'] == 'title' ) {
				$params['orderby'] = 'alpha';
			}
			if ( ! empty( $params['order'] ) AND $params['order'] == 'ASC' ) {
				if ( $params['orderby'] == 'date' ) {
					$params['orderby'] = 'date_asc';
				} elseif ( $params['orderby'] == 'modified' ) {
					$params['orderby'] = 'modified_asc';
				}
				unset( $params['order'] );
			}
		}

		return TRUE;
	}

	// Sale products
	public function translate_sale_products( &$name, &$params, &$content ) {
		$name = 'us_grid';
		$params['post_type'] = 'product';
		$params['products_include'] = 'sale';
		$params['items_layout'] = $this->get_products_grid();
		$params['items_gap'] = $this->get_products_grid( 'gap' );

		if ( isset( $params['per_page'] ) ) {
			$params['items_quantity'] = $params['per_page'];
			unset( $params['per_page'] );
		}
		if ( ! empty( $params['orderby'] ) ) {
			if ( $params['orderby'] == 'title' ) {
				$params['orderby'] = 'alpha';
			}
			if ( ! empty( $params['order'] ) AND $params['order'] == 'ASC' ) {
				if ( $params['orderby'] == 'date' ) {
					$params['orderby'] = 'date_asc';
				} elseif ( $params['orderby'] == 'modified' ) {
					$params['orderby'] = 'modified_asc';
				}
				unset( $params['order'] );
			}
		}

		return TRUE;
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

	public function translate_us_post_content( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_comments( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_taxonomy( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_custom_field( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_date( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_author( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_title( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_page_title( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_itext( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_counter( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_vc_tta_tabs( &$name, &$params, &$content ) {
		$params['stretch'] = '1';
		$params['title_transform'] = 'uppercase';

		if ( ! empty( $params['layout'] ) AND $params['layout'] == 'timeline' ) {
			$params['layout'] = 'timeline2';
		} else {
			$params['layout'] = 'trendy';
		}

		return TRUE;
	}

	public function translate_vc_tta_tour( &$name, &$params, &$content ) {
		$params['layout'] = 'trendy';
		$params['title_transform'] = 'uppercase';

		return TRUE;
	}

	public function translate_us_pricing( &$name, &$params, &$content ) {

		if ( ! empty( $params['style'] ) AND $params['style'] == '2' ) {
			$params['style'] = 'flat';
		} else {
			$params['style'] = 'cards';
		}

		return TRUE;
	}

	public function translate_us_person( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! isset( $params['layout'] ) OR empty( $params['layout'] ) ) {
			$params['layout'] = 'cards';
			$changed = TRUE;
		}

		return $changed;
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

	/*
	 * THEME OPTIONS
	 * ==============================================================================================
	 */
	public function translate_theme_options( &$options ) {
		$this->previous_theme_options = $options;

		// old string from migration_5_6
		$text_fix_56_css = ".wpb_text_column:not(:last-child) { margin-bottom: 1.5rem; } /* migration 5.6 fix */ \n";
		if ( strpos( $options['custom_css'], $text_fix_56_css ) !== FALSE ) {
			$options['custom_css'] = str_replace( $text_fix_56_css, '', $options['custom_css'] );
		} else {
			$options['text_bottom_indent'] = '0rem';
		}

		// Calculate width for NEW sidebar column
		$rem = intval( $options['body_fontsize'] );
		$content_width = intval( $options['site_content_width'] );
		$old_sidebar_width = intval( $options['sidebar_width'] );
		$new_sidebar_width = 100 * ( $content_width * $old_sidebar_width / 100 + 3 * $rem ) / ( $content_width + 3 * $rem );
		$options['sidebar_width'] = number_format( $new_sidebar_width, 2 ) . '%';

		// Force Titlebar & Sidebar option
		$options['enable_sidebar_titlebar'] = TRUE;

		// Reset Product & Shop content templates
		$options['content_product_id'] = '';
		$options['content_shop_id'] = '';

		// Enable Ripple effect
		$options['ripple_effect'] = TRUE;

		// Added Alternate Content colors
		$options['color_alt_content_bg'] = $options['color_content_bg_alt'];
		$options['color_alt_content_bg_alt'] = $options['color_content_bg'];
		$options['color_alt_content_border'] = $options['color_content_border'];
		$options['color_alt_content_heading'] = $options['color_content_heading'];
		$options['color_alt_content_text'] = $options['color_content_text'];
		$options['color_alt_content_link'] = $options['color_content_link'];
		$options['color_alt_content_link_hover'] = $options['color_content_link_hover'];
		$options['color_alt_content_primary'] = $options['color_content_primary'];
		$options['color_alt_content_secondary'] = $options['color_content_secondary'];
		$options['color_alt_content_faded'] = $options['color_content_faded'];

		/* Add Interactive Banner checkbox if optimize CSS option is ON */
		if ( isset( $options['optimize_assets'] ) AND $options['optimize_assets'] AND is_array( $options['assets'] ) ) {
			$options['assets'] = array_unique( array_merge( $options['assets'], array( 'ibanner' ) ) );
		}

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
		}

		/*
		 * MENU ITEMS
		 * ==============================================================================================
		 */
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

		// Set new content templates for WooCommerce pages
		if ( class_exists( 'woocommerce' ) ) {

			// Check metabox settings of Shop Page
			$shop_page = get_post( wc_get_page_id( 'shop' ) );
			if ( $shop_page ) {
				// Getting the shop page header ID from metaboxes
				$shop_meta = get_post_meta( $shop_page->ID );
				// Titlebar ID from metaboxes
				if ( ! empty( $shop_meta['us_titlebar'][0] ) ) {

					// Custom Titlebar
					if ( $shop_meta['us_titlebar'][0] == 'custom' AND isset( $shop_meta['us_titlebar_id'][0] ) ) {
						$options['titlebar_shop_id'] = $shop_meta['us_titlebar_id'][0];

						// Hide Titlebar
					} elseif ( $shop_meta['us_titlebar'][0] == 'hide' ) {
						$options['titlebar_shop_id'] = '';
					}
				}

				// Sidebar settings from metaboxes
				if ( ! empty( $shop_meta['us_sidebar'][0] ) ) {
					// Custom Sidebar
					if ( $shop_meta['us_sidebar'][0] == 'custom' AND isset( $shop_meta['us_sidebar_id'][0] ) ) {
						$options['sidebar_shop_id'] = $shop_meta['us_sidebar_id'][0];
						if ( isset( $shop_meta['us_sidebar_pos'][0] ) ) {
							$options['sidebar_shop_pos'] = $shop_meta['us_sidebar_pos'][0];
						}

						// Hide Sidebar
					} elseif ( $shop_meta['us_sidebar'][0] == 'hide' ) {
						$options['sidebar_shop_id'] = '';
					}
				}

				// Footer settings from metaboxes
				if ( isset( $shop_meta['us_footer'][0] ) ) {
					if ( $shop_meta['us_footer'][0] == '' ) {
						$shop_footer_id = '__defaults__';
					} elseif ( $shop_meta['us_footer'][0] == 'hide' ) {
						$shop_footer_id = '';
					} else {
						$shop_footer_id = isset( $shop_meta['us_footer_id'][0] ) ? $shop_meta['us_footer_id'][0] : '__defaults__';
					}
				} else {
					$shop_footer_id = '__defaults__';
				}
				$options['footer_shop_id'] = $shop_footer_id;
			}

			// Create templates for Shop Page and Products
			$this->create_shop_template( TRUE );
			$this->create_shop_template();
		}

		return TRUE;
	}

	/*
	 * META FIELDS
	 * ==============================================================================================
	 */
	public function translate_meta( &$meta, $post_type ) {
		if ( ! empty( $meta['us_migration_version'][0] ) AND $meta['us_migration_version'][0] == '6.0' ) {
			return FALSE;
		}

		// Header
		if ( isset( $meta['us_header'][0] ) ) {

			if ( $meta['us_header'][0] == '' ) {
				$meta['us_header_id'][0] = '__defaults__';
			} elseif ( $meta['us_header'][0] == 'hide' ) {
				$meta['us_header_id'][0] = '';
			}

			unset( $meta['us_header'] );
		} else {
			$meta['us_header_id'][0] = '__defaults__';
		}

		// Titlebar
		if ( isset( $meta['us_titlebar'][0] ) ) {

			if ( $meta['us_titlebar'][0] == '' ) {
				$meta['us_titlebar_id'][0] = '__defaults__';
			} elseif ( $meta['us_titlebar'][0] == 'hide' ) {
				$meta['us_titlebar_id'][0] = '';
			}

			unset( $meta['us_titlebar'] );
		} else {
			$meta['us_titlebar_id'][0] = '__defaults__';
		}

		// Content
		if ( isset( $meta['us_content'][0] ) ) {

			if ( $meta['us_content'][0] == '' ) {
				$meta['us_content_id'][0] = '__defaults__';
			} elseif ( $meta['us_content'][0] == 'hide' ) {
				$meta['us_content_id'][0] = '';
			}

			unset( $meta['us_content'] );
		} else {
			$meta['us_content_id'][0] = '__defaults__';
		}

		// Sidebar
		if ( isset( $meta['us_sidebar'][0] ) ) {

			if ( $meta['us_sidebar'][0] == '' ) {
				$meta['us_sidebar_id'][0] = '__defaults__';
			} elseif ( $meta['us_sidebar'][0] == 'hide' ) {
				$meta['us_sidebar_id'][0] = '';
			}

			unset( $meta['us_sidebar'] );
		} else {
			$meta['us_sidebar_id'][0] = '__defaults__';
			$meta['us_sidebar_pos'][0] = 'right';
		}

		// Footer
		if ( isset( $meta['us_footer'][0] ) ) {

			if ( $meta['us_footer'][0] == '' ) {
				$meta['us_footer_id'][0] = '__defaults__';
			} elseif ( $meta['us_footer'][0] == 'hide' ) {
				$meta['us_footer_id'][0] = '';
			}

			unset( $meta['us_footer'] );
		} else {
			$meta['us_footer_id'][0] = '__defaults__';
		}

		// Grid former ids
		if ( isset( $meta['us_grid_layout_ids'][0] ) ) {
			$meta['_us_in_content_ids'][0] = $meta['us_grid_layout_ids'][0];

			unset( $meta['us_grid_layout_ids'] );
		}

		// Remove extra meta, added by 5.7 migration
		if ( isset( $meta['us_special_page_type'][0] ) ) {
			unset( $meta['us_special_page_type'] );
		}

		$meta['us_migration_version'][0] = '6.0';

		return TRUE;
	}

	// Create Page Block for Shop & Products
	private function create_shop_template( $is_shop = FALSE ) {

		// Get Grid Layout & Gap which were set in former Shop options
		$products_grid_layout = $this->get_products_grid();
		$products_grid_gap = $this->get_products_grid( 'gap' );

		// Check if it's Shop or Product
		if ( $is_shop ) {

			$page_block_title = 'Shop template';
			$shop_page_content = '[vc_row height="auto" width="full"][vc_column][us_post_content type="full_content"][/vc_column][/vc_row]';

			$content_template = '[us_hwrapper alignment="justify" valign="middle"]';
			if ( in_array( 'shop_title', $this->previous_theme_options['shop_elements'] ) ) {
				$content_template .= '[us_page_title tag="h1" font="h1"]';
			}
			$content_template .= '[us_product_ordering align="right"]';
			$content_template .= '[/us_hwrapper][us_post_content type="excerpt_only"]';
			$content_template .= '[us_separator size="small"]';
			if ( get_option( 'woocommerce_shop_page_display', '' ) != '' ) {
				$content_template .= '[us_grid post_type="taxonomy_terms" related_taxonomy="product_cat" no_items_message=""';
				$content_template .= ' items_layout="' . $this->get_products_grid( '', TRUE ) . '" items_gap="' . $products_grid_gap . '"';
				$content_template .= ' columns="' . $this->previous_theme_options['shop_columns'] . '"';
				$content_template .= ' css=".vc_custom_1204201900001{margin-bottom: 3rem !important;}"';
				$content_template .= ']';
			}
			$content_template .= '[us_grid post_type="current_query" pagination="regular"';
			$content_template .= ' items_layout="' . $products_grid_layout . '" items_gap="' . $products_grid_gap . '"';
			$content_template .= ' columns="' . $this->previous_theme_options['shop_columns'] . '"';
			$content_template .= ']';

		} else {

			$page_block_title = 'Product template';
			$shop_page_content = '';

			$content_template = '[vc_row_inner][vc_column_inner width="1/2"]';
			$content_template .= '[us_product_gallery][/vc_column_inner]';
			$content_template .= '[vc_column_inner width="1/2"]';
			$content_template .= '[us_product_field type="sale_badge" font_size="13px" font_weight="700" text_transform="uppercase" css=".vc_custom_1204201900002{margin-bottom: 1rem !important;padding-right: 0.8rem !important;padding-left: 0.8rem !important;border-radius: 35px !important;}"]';
			// Breadcrumbs
			if ( in_array( 'breadcrumbs', $this->previous_theme_options['shop_elements'] ) ) {
				$content_template .= '[us_breadcrumbs show_current="1" font_size="0.9rem" separator_type="custom" css=".vc_custom_1204201900003{margin-bottom: 0.6rem !important;}"]';
			}
			// Title
			if ( in_array( 'product_title', $this->previous_theme_options['shop_elements'] ) ) {
				$content_template .= '[us_post_title tag="h1" font="h1" css=".vc_custom_1204201900004{margin-bottom: 0.5rem !important;padding-top: 0px !important;}"]';
			}
			$content_template .= '[us_hwrapper valign="middle" wrap="1" css=".vc_custom_1204201900005{margin-bottom: 1rem !important;}"][us_product_field type="rating"][us_post_comments layout="amount" hide_zero="1" color_link="" font_size="0.9rem"][/us_hwrapper]';
			$content_template .= '[us_product_field font_weight="600" font_size="1.6rem" css=".vc_custom_1204201900006{margin-bottom: 1.5rem !important;}"][us_post_content type="excerpt_only"][us_separator size="small"][us_add_to_cart][us_separator size="small"]';
			$content_template .= '[us_product_field type="sku" font_size="0.9rem"][us_post_taxonomy taxonomy_name="product_cat" color_link="" text_before="' . us_translate( 'Category', 'woocommerce' ) . ':" font_size="0.9rem"][us_post_taxonomy taxonomy_name="product_tag" color_link="" text_before="' . us_translate( 'Tags', 'woocommerce' ) . ':" font_size="0.9rem"]';
			$content_template .= '[/vc_column_inner][/vc_row_inner]';
			$content_template .= '[us_separator]';
			// Tabs for product
			$content_template .= '[vc_tta_tabs][vc_tta_section title="' . us_translate( 'Description', 'woocommerce' ) . '" tab_id="description"][us_post_content type="full_content"][/vc_tta_section]';
			$content_template .= '[vc_tta_section title="' . us_translate( 'Additional information', 'woocommerce' ) . '" tab_id="info"][us_product_field type="weight"][us_product_field type="dimensions"][us_product_field type="pa_color"][/vc_tta_section]';
			$content_template .= '[vc_tta_section title="' . us_translate( 'Reviews', 'woocommerce' ) . ' ({{comment_count}})" tab_id="reviews"][us_post_comments][/vc_tta_section][/vc_tta_tabs]';
			// Upsells
			$content_template .= '[us_separator show_line="1"]';
			$content_template .= '[vc_column_text]<h4>' . us_translate( 'You may also like&hellip;', 'woocommerce' ) . '</h4>[/vc_column_text]';
			$content_template .= '[us_separator size="small"]';
			$content_template .= '[us_grid post_type="product_upsells" no_items_message=""';
			$content_template .= ' items_quantity="' . $this->previous_theme_options['product_related_qty'] . '"';
			$content_template .= ' columns="' . $this->previous_theme_options['product_related_qty'] . '"';
			$content_template .= ' items_layout="' . $products_grid_layout . '" items_gap="' . $products_grid_gap . '"';
			$content_template .= ']';
			// Related products
			$content_template .= '[us_separator show_line="1"]';
			$content_template .= '[vc_column_text]<h4>' . us_translate( 'Related products', 'woocommerce' ) . '</h4>[/vc_column_text]';
			$content_template .= '[us_separator size="small"]';
			$content_template .= '[us_grid post_type="related" related_taxonomy="product_cat" no_items_message=""';
			$content_template .= ' items_quantity="' . $this->previous_theme_options['product_related_qty'] . '"';
			$content_template .= ' columns="' . $this->previous_theme_options['product_related_qty'] . '"';
			$content_template .= ' items_layout="' . $products_grid_layout . '" items_gap="' . $products_grid_gap . '"';
			$content_template .= ']';
			$content_template .= '[us_separator size="large"]';
		}

		// Combine content
		$page_block_content = $shop_page_content . '[vc_row height="medium"][vc_column]' . $content_template . '[/vc_column][/vc_row]';

		$content_hash = md5( $page_block_content );

		// Check for existing page block with same content
		global $wpdb;
		$existing_posts_results = $wpdb->get_results(
			$wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", 'us_migration_content_hash', $content_hash )
		);
		if ( count( $existing_posts_results ) > 0 ) {
			$existing_post = $existing_posts_results[0];
			return TRUE;
		}

		// Create Page Block and return its ID
		ob_start();
		$page_block_id = wp_insert_post(
			array(
				'post_type' => 'us_page_block',
				'post_date' => date( 'Y-m-d H:i', time() - 86400 ),
				'post_status' => 'publish',
				'post_title' => $page_block_title,
				'post_content' => $page_block_content,
			)
		);
		add_post_meta( $page_block_id, 'us_migration_content_hash', $content_hash );
		if ( $is_shop ) {
			add_post_meta( $page_block_id, 'us_migration_6_for', 'shop' );
		} else {
			add_post_meta( $page_block_id, 'us_migration_6_for', 'product' );
		}

		ob_end_clean();

		return TRUE;
	}

	// Common Text Styles migration
	private function migrate_text_styles( &$params ) {
		$changed = FALSE;

		if ( ! empty( $params['font'] ) AND $params['font'] == 'heading' ) {

			$params['font'] = 'h1';

			$changed = TRUE;
		}
		if ( ! empty( $params['text_styles'] ) ) {

			if ( strpos( $params['text_styles'], 'bold' ) !== FALSE ) {
				$params['font_weight'] = '700';
			}
			if ( strpos( $params['text_styles'], 'uppercase' ) !== FALSE ) {
				$params['text_transform'] = 'uppercase';
			}
			if ( strpos( $params['text_styles'], 'italic' ) !== FALSE ) {
				$params['font_style'] = 'italic';
			}

			unset( $params['text_styles'] );

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
