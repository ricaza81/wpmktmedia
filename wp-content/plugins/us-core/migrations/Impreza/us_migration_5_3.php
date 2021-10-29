<?php

class us_migration_5_3 extends US_Migration_Translator {

	private $woocommerce_shop_page_id = NULL;

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_gmaps( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['markers'] ) ) {
			try {
				$markers = json_decode( urldecode( $params['markers'] ), TRUE );

				if ( is_array( $markers ) AND count( $markers ) ) {
					foreach ( $markers as $index => $marker ) {
						if ( ! empty( $marker['marker_latitude'] ) AND ! empty( $marker['marker_longitude'] ) ) {
							$markers[ $index ]['marker_address'] = $marker['marker_latitude'] . ' ' . $marker['marker_longitude'];
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
			$params['marker_address'] = $params['latitude'] . ', ' . $params['longitude'];
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_grid( &$name, &$params, &$content ) {
		$changed = FALSE;

		$known_post_type_taxonomies = array(
			'post' => 'category',
			'us_portfolio' => 'us_portfolio_category',
			'us_testimonial' => 'us_testimonial_category',
			'product' => 'product_cat',
		);

		$post_type = isset( $params['post_type'] ) ? $params['post_type'] : 'post';

		if ( isset( $known_post_type_taxonomies[ $post_type ] ) ) {
			$taxonomy = $known_post_type_taxonomies[ $post_type ];

			if ( isset( $params[ $post_type . '_categories' ] ) ) {
				$params[ 'taxonomy_' . $taxonomy ] = $params[ $post_type . '_categories' ];
				unset( $params[ $post_type . '_categories' ] );

				$changed = TRUE;
			}

			if ( isset( $params['filter'] ) AND $params['filter'] == 'category' ) {
				$params[ 'filter_' . $post_type ] = $taxonomy;
				unset( $params['filter'] );

				$changed = TRUE;
			}
		}

		return $changed;
	}

	// Headers
	public function translate_header_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $name => $data ) {

			// Dropdown element
			if ( substr( $name, 0, 8 ) == 'dropdown' ) {
				if ( ! empty( $data['source'] ) AND $data['source'] = 'own' AND ! empty( $data['link_qty'] ) ) {
					$links = array();
					for ( $i = 0; $i < $data['link_qty']; $i ++ ) {
						$j = $i + 1;
						$links[ $i ] = array(
							'label' => $data[ 'link_' . $j . '_label' ],
							'url' => $data[ 'link_' . $j . '_url' ],
						);
					}
					$settings['data'][ $name ]['links'] = $links;
					unset( $settings['data'][ $name ]['link_qty'] );
					$settings_changed = TRUE;
				}
			}

		}

		return $settings_changed;
	}

	// Theme Options
	public function translate_theme_options( &$options ) {

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
		$footers = get_posts(
			array(
				'post_type' => 'us_footer',
				'posts_per_page' => - 1,
				'post_status' => 'any',
			)
		);
		foreach ( $footers as $footer ) {
			wp_update_post(
				array(
					'ID' => $footer->ID,
					'post_type' => 'us_page_block',
				)
			);
		}

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

		// Get Custom Post Types
		$post_type_args = array(
			'public' => TRUE,
			'_builtin' => FALSE,
		);
		$post_types = get_post_types( $post_type_args, 'objects', 'and' );
		$supported_post_types = array(
			// Theme
			'us_portfolio',
			'us_testimonial',
			'us_header',
			'us_footer',
			'us_grid_layout',
			// WooCommerce
			'product',
			// bbPress
			'forum',
			'topic',
			'reply',
			// The Events Calendar
			'tribe_events',
			'tribe-ea-record',
		);
		$cpt_as_posts = array();
		foreach ( $post_types as $post_type_name => $post_type ) {
			if ( ! in_array( $post_type_name, $supported_post_types ) ) {
				$cpt_as_posts[ $post_type_name ] = $post_type_name;
			}
		}

		if ( ! empty( $options['custom_post_types_support'] ) AND is_array( $options['custom_post_types_support'] ) ) {
			foreach ( $options['custom_post_types_support'] as $cpt ) {
				unset( $cpt_as_posts[ $cpt ] );
			}
		}

		if ( count( $cpt_as_posts ) > 0 ) {
			$options['cpt_as_posts'] = array_keys( $cpt_as_posts );
		}

		// Footer Settings
		$footer_fields_translate = array(
			// New field => Old field to copy from
			'footer_id' => 'footer_id',
			'footer_portfolio_id' => 'footer_portfolio_id',
			'footer_post_id' => 'footer_post_id',
			'footer_search_id' => 'footer_archive_id',
			// Dev note: footer_search_id should be set before footer_archive_id
			'footer_archive_id' => 'footer_archive_id',
			'footer_shop_id' => 'footer_shop_id',
			'footer_product_id' => 'footer_product_id',
		);

		foreach ( $footer_fields_translate as $new_field => $old_field ) {
			$defaults_field = str_replace( '_id', '_defaults', $old_field );
			if ( $old_field != 'footer_id' AND isset( $options[ $defaults_field ] ) AND $options[ $defaults_field ] ) {
				$options[ $new_field ] = '__defaults__';
			} else {
				if ( isset( $options[ $old_field ] ) ) {
					$args = array(
						'name' => $options[ $old_field ],
						'post_type' => 'us_page_block',
						'numberposts' => 1,
					);
					$footer_post = get_posts( $args );
					if ( $footer_post ) {
						$footer_post = $footer_post[0];
						$options[ $new_field ] = $footer_post->ID;
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
		if ( ! empty( $options['portfolio_sidebar_pos'] ) ) {
			$options['portfolio_sidebar_pos'] = $options['sidebar_portfolio_pos'];
		}

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

		// Sidebars for Tribe Events
		if ( isset( $options['event_sidebar'] ) AND $options['event_sidebar'] == 0 ) {
			if ( $show_default_sidebar ) {
				$options['sidebar_tribe_events_id'] = '';
			}
		} elseif ( isset( $options['event_sidebar'] ) ) {
			$options['sidebar_tribe_events_id'] = $options['event_sidebar_id'];
		}
		if ( ! empty( $options['event_sidebar_pos'] ) ) {
			$options['sidebar_tribe_events_pos'] = $options['event_sidebar_pos'];
		}

		// Sidebars for Forums
		if ( isset( $options['forum_sidebar'] ) AND $options['forum_sidebar'] == 0 ) {
			if ( $show_default_sidebar ) {
				$options['sidebar_forum_id'] = '';
				$options['sidebar_topic_id'] = '';
			}
		} else {
			$options['sidebar_forum_id'] = $options['forum_sidebar_id'];
			$options['sidebar_topic_id'] = $options['forum_sidebar_id'];
		}
		$options['sidebar_forum_pos'] = $options['forum_sidebar_pos'];
		$options['sidebar_topic_pos'] = $options['forum_sidebar_pos'];


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

		// Change portfolio type name to correspond to theme options
		if ( $post_type == 'us_portfolio' ) {
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
				if ( strpos( $post->post_content, $titlebar_content ) == FALSE ) {
					$content = $titlebar_content . $post->post_content;
					wp_update_post(
						array(
							'ID' => $post->ID,
							'post_content' => $content,
						)
					);

					$meta['us_titlebar'][0] = 'hide';
				}
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
		$post_content = $this->generate_titlebar_content( $title, $description, $size, $color, $breadcrumbs, $bg_image, $bg_size, $bg_repeat, $bg_position, $bg_parallax, $bg_overlay );
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
			'post_title' => $post_title,
			'post_content' => $post_content,
			'post_status' => 'publish',
		);
		ob_start();
		$block_post_id = wp_insert_post( $block_post_array );
		add_post_meta( $block_post_id, 'us_migration_content_hash', $content_hash );
		ob_end_clean();

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
			$content .= '[us_breadcrumbs show_current="1" font_size="0.9rem"';
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
}
