<?php

class us_migration_6_8 extends US_Migration_Translator {

	// Theme Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		/*
		 * Adding Content Templates to js_composer post types support
		 */
		if ( function_exists( 'vc_editor_post_types' ) AND function_exists( 'vc_editor_set_post_types' ) ) {
			$js_composer_post_types = vc_editor_post_types();
			if ( ! in_array( 'us_content_template', $js_composer_post_types ) ) {
				$js_composer_post_types[] = 'us_content_template';
				vc_editor_set_post_types( $js_composer_post_types );
			}
		}

		/*
		 * Change post type from Page Bock to Content Template
		 * for posts that are set as Content Templates in the options.
		 *
		 * Do not perform this change during Fallback. Only on actual migration.
		 */
		global $us_migration_doing_fallback;
		if ( ! $us_migration_doing_fallback ) {

			// Get names for 'Content Template' options first
			$content_template_options = array(
				'content_id',
				'content_archive_id',
				'content_author_id',
				'content_shop_id',
				'content_product_id',
			);

			// Option names for single pages
			foreach ( us_get_public_post_types( array( 'page' ) ) as $type => $title ) {

				// Rename "us_portfolio" suffix to be relevant to old theme options
				if ( $type == 'us_portfolio' ) {
					$type = 'portfolio';
				}
				$content_template_options[] = 'content_' . $type . '_id';
			}

			// Option names for archives
			$public_taxonomies = us_get_taxonomies( TRUE, FALSE, 'woocommerce_exclude' );
			foreach ( $public_taxonomies as $type => $title ) {
				$content_template_options[] = 'content_tax_' . $type . '_id';
			}

			// Option names for WooCommerce archives
			if ( class_exists( 'woocommerce' ) ) {
				$product_taxonomies = us_get_taxonomies( TRUE, FALSE, 'woocommerce_only' );
				foreach ( $product_taxonomies as $type => $title ) {
					$content_template_options[] = 'content_tax_' . $type . '_id';
				}
			}

			// Check post type for posts that are set as Content Template
			foreach ( $content_template_options as $option_name ) {
				if ( empty( $options[ $option_name ] ) OR $options[ $option_name ] == '__defaults__' ) {
					continue;
				}
				$post_type = get_post_type( intval( $options[ $option_name ] ) );

				// If the post type is 'us_page_block' ...
				if ( ! empty( $post_type ) AND $post_type == 'us_page_block' ) {

					// ... change it to 'us_content_template'.
					wp_update_post(
						array(
							'ID' => intval( $options[ $option_name ] ),
							'post_type' => 'us_content_template',
						)
					);
					if ( class_exists( 'SitePress' ) AND defined( 'ICL_LANGUAGE_CODE' ) ) {
						$this->update_wpml_translation_for_content_template( $options[ $option_name ] );
					}
				}

			}
		}

		return $changed;
	}

	// Meta settings
	public function translate_meta( &$meta, $post_type ) {
		/*
		 * If this post has a Page Block set as 'Content template',
		 * change its post type from Page Bock to Content Template.
		 *
		 * Do not perform this change during Fallback. Only on actual migration.
		 */
		global $us_migration_doing_fallback;
		if ( ! $us_migration_doing_fallback ) {
			// Check if a Page Block set as 'Content template'
			if ( ! empty( $meta['us_content_id'][0] ) AND $meta['us_content_id'][0] != '__defaults__' ) {
				$post_type = get_post_type( intval( $meta['us_content_id'][0] ) );
				// If its post type is still 'us_page_block' ...
				if ( ! empty( $post_type ) AND $post_type == 'us_page_block' ) {
					// ... change it to 'us_content_template'.
					wp_update_post(
						array(
							'ID' => intval( $meta['us_content_id'][0] ),
							'post_type' => 'us_content_template',
						)
					);
					if ( class_exists( 'SitePress' ) AND defined( 'ICL_LANGUAGE_CODE' ) ) {
						$this->update_wpml_translation_for_content_template( $meta['us_content_id'][0] );
					}
				}
			}
		}


	}

	/*
	 * Update post type for all WPML translations of content template
	 */
	private function update_wpml_translation_for_content_template( $post_id ) {
		global $wpdb;

		// Check if translations table exists first
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}icl_translations'" ) != "{$wpdb->prefix}icl_translations" ) {
			return;
		}

		// Get translation ID for the post
		$translation_id = $wpdb->get_var(
			$wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_type = 'post_us_content_template' AND element_id = %d LIMIT 0,1", $post_id )
		);

		if ( $translation_id ) {
			// Get IDs of all translated posts
			$translated_post_ids = $wpdb->get_col(
				$wpdb->prepare( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid = %d", $translation_id )
			);
			// Change translated posts post type
			if ( $translated_post_ids ) {
				foreach ( $translated_post_ids as $translated_post_id ) {
					if ( $translated_post_id != $post_id ) {
						$wpdb->query(
							$wpdb->prepare( "UPDATE {$wpdb->posts} SET post_type = 'us_content_template' WHERE ID = %d", $translated_post_id )
						);
					}
				}
				// Update post type in translations table
				$wpdb->query(
					$wpdb->prepare( "UPDATE {$wpdb->prefix}icl_translations SET element_type = 'post_us_content_template' WHERE trid = %d", $translation_id )
				);
			}
		}
	}

}
