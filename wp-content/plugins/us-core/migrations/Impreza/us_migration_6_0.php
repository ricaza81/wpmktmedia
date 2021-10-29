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

	// Content
	public function translate_content( &$content ) {
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
	public function translate_vc_row_inner( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['gap'] ) ) {
			$params['gap'] = ( intval( $params['gap'] ) / 2 ) . 'px';
			$changed = TRUE;
		}

		return $changed;
	}

	// Grid into Carousel
	public function translate_us_grid( &$name, &$params, &$content ) {
		$changed = FALSE;

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

	// Theme Options
	public function translate_theme_options( &$options ) {
		$this->previous_theme_options = $options;

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

		// Calculate width for NEW sidebar column
		$rem = intval( $options['body_fontsize'] );
		$content_width = intval( $options['site_content_width'] );
		$old_sidebar_width = intval( $options['sidebar_width'] );
		$new_sidebar_width = 100 * ( $content_width * $old_sidebar_width / 100 + 3 * $rem ) / ( $content_width + 3 * $rem );
		$options['sidebar_width'] = number_format( $new_sidebar_width, 2 ) . '%';

		// Force Titlebar & Sidebar option
		$options['enable_sidebar_titlebar'] = TRUE;

		return TRUE;
	}

	// Meta settings
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
			if ( ! empty( $this->previous_theme_options['shop_elements'] ) AND in_array( 'shop_title', $this->previous_theme_options['shop_elements'] ) ) {
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
			if ( ! empty( $this->previous_theme_options['shop_elements'] ) AND in_array( 'breadcrumbs', $this->previous_theme_options['shop_elements'] ) ) {
				$content_template .= '[us_breadcrumbs show_current="1" font_size="0.9rem" separator_type="custom" css=".vc_custom_1204201900003{margin-bottom: 0.6rem !important;}"]';
			}
			// Title
			if ( ! empty( $this->previous_theme_options['shop_elements'] ) AND in_array( 'product_title', $this->previous_theme_options['shop_elements'] ) ) {
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
		$page_block_content = $shop_page_content . '[vc_row][vc_column]' . $content_template . '[/vc_column][/vc_row]';

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

}
