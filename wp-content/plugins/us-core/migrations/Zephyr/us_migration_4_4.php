<?php

class us_migration_4_4 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_blog( &$name, &$params, &$content ) {
		$changed = FALSE;

		if (
			( empty( $params['type'] ) OR $params['type'] != 'masonry' ) AND (
			( ( empty( $params['layout'] ) OR $params['layout'] == 'classic' ) AND ( empty( $params['cols'] ) OR $params['cols'] != 1 ) ) OR
			( ! empty( $params['layout'] ) AND in_array( $params['layout'], array( 'flat', 'cards', 'tiles' ) ) )
			)
		) {
			$params['img_size'] = 'us_img_size_2';
			$changed = TRUE;
		}

		if (
			( ! empty( $params['type'] ) AND $params['type'] == 'masonry' ) AND
			( empty( $params['layout'] ) OR ! in_array( $params['layout'], array( 'smallcircle', 'smallsquare' ) ) )
		) {
			$params['img_size'] = 'us_img_size_1';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_portfolio( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['img_size'] ) AND $params['img_size'] == 'tnail-masonry' ) {
			$params['img_size'] = 'us_img_size_1';
			$changed = TRUE;
		} elseif ( ! empty( $params['img_size'] ) AND $params['img_size'] == 'tnail-3x2' ) {
			$params['img_size'] = 'us_img_size_2';
			$changed = TRUE;
		} elseif ( empty( $params['img_size'] ) ) {
			$params['img_size'] = 'us_img_size_1';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_gallery( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['layout'] ) AND $params['layout'] == 'masonry' AND $params['columns'] < 8 ) {
			$params['img_size'] = 'us_img_size_1';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_single_image( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['frame'] ) ) {
			$params['style'] = $params['frame'];
			unset( $params['frame'] );
			$changed = TRUE;
		}

		if ( ! empty( $params['lightbox'] ) AND $params['lightbox'] ) {
			$params['onclick'] = 'lightbox';
			unset( $params['lightbox'] );
			$changed = TRUE;
		}

		if ( ! empty( $params['link'] ) ) {
			$params['onclick'] = 'custom_link';
			$changed = TRUE;
		}

		if ( ! empty( $params['size'] ) AND $params['size'] == 'tnail-masonry' ) {
			$params['size'] = 'us_img_size_1';
			$changed = TRUE;
		} elseif ( ! empty( $params['size'] ) AND $params['size'] == 'tnail-3x2' ) {
			$params['size'] = 'us_img_size_2';
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_image_slider( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['frame'] ) ) {
			$params['style'] = $params['frame'];
			unset( $params['frame'] );
			$changed = TRUE;
		}

		if ( ! empty( $params['img_size'] ) AND $params['img_size'] == 'tnail-masonry' ) {
			$params['img_size'] = 'us_img_size_1';
			$changed = TRUE;
		} elseif ( ! empty( $params['img_size'] ) AND $params['img_size'] == 'tnail-3x2' ) {
			$params['img_size'] = 'us_img_size_2';
			$changed = TRUE;
		}

		return $changed;
	}

	// Meta
	public function translate_meta( &$meta, $post_type ) {
		$changed = FALSE;

		$custom_post_types = us_get_option( 'custom_post_types_support' );

		$translate_meta_for = array_unique( array_merge( array( 'page', 'product', 'post', 'us_portfolio' ), $custom_post_types ) );

		if ( ! in_array( $post_type, $translate_meta_for ) ) {
			return FALSE;
		}

		if ( ( ! empty( $meta['us_titlebar_content'][0] ) AND in_array( $meta['us_titlebar_content'][0], array( 'all', 'caption' ) ) ) OR
			( empty( $meta['us_titlebar_content'][0] ) AND ( ! empty( $meta['us_titlebar_size'][0] ) OR ! empty( $meta['us_titlebar_color'][0] ) OR ! empty( $meta['us_titlebar_image'][0] ) ) ) ) {
			$meta['us_titlebar'][0] = 'custom';
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_titlebar_content'][0] ) AND $meta['us_titlebar_content'][0] == 'hide' ) {
			$meta['us_titlebar'][0] = 'hide';
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_titlebar_content'][0] ) AND $meta['us_titlebar_content'][0] == 'all' ) {
			$meta['us_titlebar_breadcrumbs'][0] = 'show';
			$changed = TRUE;
		} elseif ( ! empty( $meta['us_titlebar_content'][0] ) AND $meta['us_titlebar_content'][0] == 'caption' ) {
			$meta['us_titlebar_breadcrumbs'][0] = 'hide';
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_titlebar_image_size'][0] ) ) {
			$meta['us_titlebar_bg_size'][0] = $meta['us_titlebar_image_size'][0];
			$changed = TRUE;
		}

		if ( ! empty( $meta['us_titlebar_image_parallax'][0] ) AND $meta['us_titlebar_image_parallax'][0] != 'cover' ) {
			$meta['us_titlebar_bg_parallax'][0] = $meta['us_titlebar_image_parallax'][0];
			$changed = TRUE;
		}

		if ( isset( $meta['us_titlebar_content'] ) ) {
			unset( $meta['us_titlebar_content'] );
			unset( $meta['us_titlebar_image_size'] );
			unset( $meta['us_titlebar_image_parallax'] );
			$changed = TRUE;
		}

		// Also remove all unused metabox options from old theme versions
		if ( isset( $meta['us_subtitle'] ) ) {
			unset( $meta['us_subtitle'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_breadcrumbs'] ) ) {
			unset( $meta['us_breadcrumbs'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_header_type'] ) ) {
			unset( $meta['us_header_type'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_header_layout_type'] ) ) {
			unset( $meta['us_header_layout_type'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_header_image_stretch'] ) ) {
			unset( $meta['us_header_image_stretch'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_header_show_onscroll'] ) ) {
			unset( $meta['us_header_show_onscroll'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_show_subfooter_widgets'] ) ) {
			unset( $meta['us_show_subfooter_widgets'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_show_footer'] ) ) {
			unset( $meta['us_show_footer'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_footer_show_top'] ) ) {
			unset( $meta['us_footer_show_top'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_footer_show_bottom'] ) ) {
			unset( $meta['us_footer_show_bottom'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_portfolio_filter'] ) ) {
			unset( $meta['us_portfolio_filter'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_additional_image'] ) ) {
			unset( $meta['us_additional_image'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_tile_action'] ) ) {
			unset( $meta['us_tile_action'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_title_bg_color'] ) ) {
			unset( $meta['us_title_bg_color'] );
			$changed = TRUE;
		}
		if ( isset( $meta['us_title_text_color'] ) ) {
			unset( $meta['us_title_text_color'] );
			$changed = TRUE;
		}

		return $changed;
	}

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( ! empty( $options['body_bg_image_attachment'] ) ) {
			if ( $options['body_bg_image_attachment'] == 'fixed' ) {
				$options['body_bg_image_attachment'] = 0;
			} elseif ( $options['body_bg_image_attachment'] == 'scroll' ) {
				$options['body_bg_image_attachment'] = 1;
			} else {
				unset( $options['body_bg_image_attachment'] );
			}
			$changed = TRUE;
		}

		foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
			if ( ! empty( $options['header'][$state]['options']['bg_img_attachment'] ) ) {
				if ( $options['header'][$state]['options']['bg_img_attachment'] == 'fixed' ) {
					$options['header'][$state]['options']['bg_img_attachment'] = 0;
				} elseif ( $options['header'][$state]['options']['bg_img_attachment'] == 'scroll' ) {
					$options['header'][$state]['options']['bg_img_attachment'] = 1;
				} else {
					unset( $options['header'][$state]['options']['bg_img_attachment'] );
				}
				$changed = TRUE;
			}
		}

		if ( isset( $options['post_preview_layout'] ) AND in_array( $options['post_preview_layout'], array( 'modern', 'trendy' ) ) ) {
			$options['post_preview_img_size'] = 'full';
			$changed = TRUE;
		}

		// Title Bar
		if ( isset( $options['titlebar_content'] ) AND $options['titlebar_content'] == 'caption' ) {
			$options['titlebar_breadcrumbs'] = 0;
			$changed = TRUE;
		}
		if ( isset( $options['titlebar_content'] ) AND $options['titlebar_content'] == 'hide' ) {
			$options['titlebar'] = 0;
			$changed = TRUE;
		}
		if ( isset( $options['titlebar_portfolio_content'] ) AND in_array( $options['titlebar_portfolio_content'], array( 'all', 'caption' ) ) ) {
			$options['titlebar_portfolio'] = 1;
			$changed = TRUE;
		}
		if ( isset( $options['titlebar_post_content'] ) AND $options['titlebar_post_content'] == 'all' ) {
			$options['titlebar_post'] = 1;
			$options['titlebar_post_breadcrumbs'] = 1;
			$changed = TRUE;
		}
		if ( isset( $options['titlebar_post_content'] ) AND $options['titlebar_post_content'] == 'caption' ) {
			$options['titlebar_post'] = 1;
			$changed = TRUE;
		}
		if ( isset( $options['titlebar_archive_content'] ) AND $options['titlebar_archive_content'] == 'all' ) {
			$options['titlebar_archive_breadcrumbs'] = 1;
			$changed = TRUE;
		}
		if ( isset( $options['titlebar_archive_content'] ) AND $options['titlebar_archive_content'] == 'hide' ) {
			$options['titlebar_archive'] = 0;
			$changed = TRUE;
		}
		if ( isset( $options['shop_titlebar_content'] ) AND $options['shop_titlebar_content'] == 'all' ) {
			$options['titlebar_shop'] = 1;
			$options['titlebar_shop_breadcrumbs'] = 1;
			$changed = TRUE;
		}
		if ( isset( $options['shop_titlebar_content'] ) AND $options['shop_titlebar_content'] == 'caption' ) {
			$options['titlebar_shop'] = 1;
			$changed = TRUE;
		}
		if ( $options['titlebar_archive_content'] != $options['titlebar_content'] OR $options['titlebar_archive_size'] != $options['titlebar_size'] OR $options['titlebar_archive_color'] != $options['titlebar_color'] ) {
			$options['titlebar_archive_defaults'] = 0;
			$changed = TRUE;
		}
		if ( $options['titlebar_post_content'] != $options['titlebar_content'] OR $options['titlebar_post_size'] != $options['titlebar_size'] OR $options['titlebar_post_color'] != $options['titlebar_color'] ) {
			$options['titlebar_post_defaults'] = 0;
			$changed = TRUE;
		}

		if ( $options['blog_type'] != 'masonry' AND ( ( $options['blog_layout'] == 'classic' AND $options['blog_cols'] != 1 ) OR in_array( $options['blog_layout'], array( 'flat', 'cards', 'tiles' ) ) ) ) {
			$options['blog_img_size'] = 'us_img_size_2';
			$changed = TRUE;
		} elseif ( $options['blog_type'] == 'masonry' AND ! in_array( $options['blog_layout'], array( 'smallcircle', 'smallsquare' ) ) ) {
			$options['blog_img_size'] = 'us_img_size_1';
			$changed = TRUE;
		}

		if ( $options['archive_type'] != 'masonry' AND ( ( $options['archive_layout'] == 'classic' AND $options['archive_cols'] != 1 ) OR in_array( $options['archive_layout'], array( 'flat', 'cards', 'tiles' ) ) ) ) {
			$options['archive_img_size'] = 'us_img_size_2';
			$changed = TRUE;
		} elseif ( $options['archive_type'] == 'masonry' AND ! in_array( $options['archive_layout'], array( 'smallcircle', 'smallsquare' ) ) ) {
			$options['archive_img_size'] = 'us_img_size_1';
			$changed = TRUE;
		}

		if ( $options['search_type'] != 'masonry' AND ( ( $options['search_layout'] == 'classic' AND $options['search_cols'] != 1 ) OR in_array( $options['search_layout'], array( 'flat', 'cards', 'tiles' ) ) ) ) {
			$options['search_img_size'] = 'us_img_size_2';
			$changed = TRUE;
		} elseif ( $options['search_type'] == 'masonry' AND ! in_array( $options['search_layout'], array( 'smallcircle', 'smallsquare' ) ) ) {
			$options['search_img_size'] = 'us_img_size_1';
			$changed = TRUE;
		}

		$options['img_size'] = array(
			1 => array(
				'width' => 600,
				'height' => 0,
				'crop' => array(),
			),
			2 => array(
				'width' => ( ! empty( $options['blog_img_width'] ) ) ? $options['blog_img_width'] : 600,
				'height' => ( ! empty( $options['blog_img_height'] ) ) ? $options['blog_img_height'] : 400,
				'crop' => array( '0' => 'crop' ),
			),
		);

		unset( $options['blog_img_width'] );
		unset( $options['blog_img_height'] );
		unset( $options['titlebar_content'] );
		unset( $options['titlebar_portfolio_content'] );
		unset( $options['titlebar_post_content'] );
		unset( $options['titlebar_archive_content'] );
		unset( $options['shop_titlebar_content'] );
		$options['titlebar_portfolio_defaults'] = 0;
		$options['post_related_img_size'] = 'us_img_size_2';

		$changed = TRUE;

		// Regenerate sizes data for images
		$attachments = get_posts( array(
			'post_type' => 'attachment',
			'posts_per_page' => - 1,
			'post_status' => 'any',
			'numberposts' => - 1,
		) );
		foreach ( $attachments as $attachment ) {
			$attachment_ID = $attachment->ID;
			if ( is_array( $imagedata = wp_get_attachment_metadata( $attachment_ID ) ) ) {
				if ( isset ( $imagedata['sizes']['tnail-masonry'] ) ) {
					$imagedata['sizes']['us_img_size_1'] = $imagedata['sizes']['tnail-masonry'];
				}
				if ( isset ( $imagedata['sizes']['tnail-3x2'] ) ) {
					$imagedata['sizes']['us_img_size_2'] = $imagedata['sizes']['tnail-3x2'];
				}

				wp_update_attachment_metadata( $attachment_ID, $imagedata );
			}

		}

		return $changed;
	}

}
