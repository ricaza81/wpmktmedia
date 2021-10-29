<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Working with post metadata
 */

if ( ! function_exists( 'us_document_title_parts' ) ) {
	add_filter( 'document_title_parts', 'us_document_title_parts', 101, 1 );
	/**
	 * Set page title from meta-boxes data
	 *
	 * @param array parts
	 * @return array
	 */
	function us_document_title_parts( $parts ) {
		if ( ! us_get_option( 'og_enabled' ) ) {
			return $parts;
		}

		// Define function name to get metadata
		$queried_object = get_queried_object();

		// The terms meta
		if ( $queried_object instanceof WP_Term ) {
			$meta_type = 'term';

			// The user meta
		} elseif ( $queried_object instanceof WP_User ) {
			$meta_type = 'user';

			// Default
		} else {
			$meta_type = 'post';
		}

		if ( $meta_title = get_metadata( $meta_type, get_queried_object_id(), 'us_meta_title', TRUE ) ) {
			$parts['title'] = trim( strip_tags( wp_specialchars_decode( $meta_title ) ) );

			if ( isset( $parts['site'] ) ) {
				unset( $parts['site'] );
			}
			if ( isset( $parts['tagline'] ) ) {
				unset( $parts['tagline'] );
			}
		}

		return $parts;
	}
}

if ( ! function_exists( 'us_output_meta_tags' ) ) {
	add_action( 'wp_head', 'us_output_meta_tags', 5 );
	/**
	 * Get and output metadata for a page
	 */
	function us_output_meta_tags() {

		// Default meta tags
		$meta_tags = array(
			'viewport' => 'width=device-width, initial-scale=1',
			'SKYPE_TOOLBAR' => 'SKYPE_TOOLBAR_PARSER_COMPATIBLE',
		);

		// Set color of address bar in Chrome for Android
		if ( $theme_color = us_get_option( 'color_chrome_toolbar', '' ) ) {
			$meta_tags['theme-color'] = $theme_color;
		}

		// Add SEO tags, if enabled
		if ( us_get_option( 'og_enabled', 1 ) ) {

			// Define function name to get metadata
			$queried_object = get_queried_object();
			// The terms meta
			if ( $queried_object instanceof WP_Term ) {
				$meta_type = 'term';

				// The user meta
			} elseif ( $queried_object instanceof WP_User ) {
				$meta_type = 'user';

				// Default
			} else {
				$meta_type = 'post';
			}

			// Get current id from request
			$the_id = get_queried_object_id();

			// TODO: add hreflang attributes, if post has several language versions

			// The `title` from meta-boxe settings
			if ( $meta_title = get_metadata( $meta_type, $the_id, 'us_meta_title', TRUE ) ) {
				$meta_tags['og:title'] = $meta_title;

				// or default page title
			} else {
				$meta_tags['og:title'] = wp_get_document_title();
			}

			// The `description` from meta-box settings
			if ( $meta_description = get_metadata( $meta_type, $the_id, 'us_meta_description', TRUE ) ) {
				$meta_tags['description'] = $meta_description;

				// or Post Excerpt
			} elseif (
				$meta_type === 'post'
				AND has_excerpt()
				AND $the_excerpt = get_the_excerpt()
			) {
				$meta_tags['description'] = $the_excerpt;

				// or Term Description
			} elseif ( $term_description = term_description() ) {
				$meta_tags['description'] = $term_description;
			}

			// The `robots` from meta-box settings
			if (
				get_option( 'blog_public' )
				AND $robots = get_metadata( $meta_type, $the_id, 'us_meta_robots', TRUE )
			) {
				$meta_tags['robots'] = $robots;
			}

			/*
			 * The Open Graph data
			 * @link https://ogp.me/
			 */
			$meta_tags['og:url'] = site_url( $_SERVER['REQUEST_URI'] );
			$meta_tags['og:locale'] = get_locale();
			$meta_tags['og:site_name'] = get_option( 'blogname' );

			// The og:type data
			if ( function_exists( 'is_product' ) AND is_product() ) {
				$meta_tags['og:type'] = 'product';
			} elseif ( is_single() ) {
				$meta_tags['og:type'] = 'article';
			} else {
				$meta_tags['og:type'] = 'website';
			}

			// The og:image data
			if ( has_post_thumbnail() ) {
				$meta_tags['og:image'] = get_the_post_thumbnail_url( NULL, 'large' );

			} elseif ( $meta_image = get_metadata( $meta_type, $the_id, 'us_og_image', TRUE ) ) {
				$meta_tags['og:image'] = (string) $meta_image;
			}
		}

		// Output the tags
		if ( $meta_tags = (array) apply_filters( 'us_meta_tags', $meta_tags ) ) {
			foreach ( $meta_tags as $tag_name => $tag_content ) {
				if (
					! is_string( $tag_content )
					// The filtering values
					OR ! $tag_content = trim( strip_tags( wp_specialchars_decode( $tag_content ) ) )
				) {
					continue;
				}

				if ( strpos( $tag_name, 'og:' ) === 0 ) {
					$tag_atts = array(
						'property' => $tag_name,
						'content' => $tag_content,
					);
					// Add specific attribute for WhatsApp
					if ( $tag_name === 'og:image' ) {
						$tag_atts['itemprop'] = 'image';
					}
				} else {
					$tag_atts = array(
						'name' => $tag_name,
						'content' => $tag_content,
					);
				}
				echo "<meta" . us_implode_atts( $tag_atts ) . ">\n";
			}
		}
	}
}

if ( ! function_exists( 'us_save_post_add_og_image' ) ) {
	add_action( 'save_post', 'us_save_post_add_og_image' );
	/**
	 * Save og_image for the post if there is a setting
	 *
	 * @param int $post_id The post identifier
	 */
	function us_save_post_add_og_image( $post_id ) {

		// If the post has thumbnail, clear og_image meta data
		if ( has_post_thumbnail( $post_id ) ) {
			update_post_meta( $post_id, 'us_og_image', '' );

			// in other case try to find an image inside post content
		} elseif ( $post = get_post( $post_id ) AND ! empty( $post->post_content ) ) {
			$the_content = apply_filters( 'us_content_template_the_content', $post->post_content );

			if ( preg_match( '/<img [^>]*src=["|\']([^"|\']+)/i', $the_content, $matches ) ) {
				update_post_meta( $post_id, 'us_og_image', $matches[1] );
			} else {
				update_post_meta( $post_id, 'us_og_image', '' );
			}
		}
	}
}

if (
	! function_exists( 'us_term_custom_fields' )
	AND ! function_exists( 'us_save_term_custom_fields' )
) {

	/**
	 * Add custom fields to terms of taxonomies on the "Edit" admin screen
	 *
	 * @param object $term Term object
	 */
	function us_term_custom_fields( $term ) {
		$misc = us_config( 'elements_misc' );

		/**
		 * @var bool
		 */
		$is_public = TRUE;

		// The taxonomy publication validation
		if ( $taxonomy = get_taxonomy( $term->taxonomy ) ) {
			$is_public = $taxonomy->public;
		}

		$options = array( '__defaults__' => sprintf( '&ndash; %s &ndash;', __( 'As in Theme Options', 'us' ) ) );
		$options = $options + us_get_posts_titles_for( 'us_content_template' );

		// Set default value for "Pages Content template"
		if ( ! $pages_content_id = get_term_meta( $term->term_id, 'pages_content_id', TRUE ) ) {
			$pages_content_id = '__defaults__';
		}

		// Output "Arhive Content template" setting, only if the taxonomy is available for frontend visitors
		if ( $tax = get_taxonomy( $term->taxonomy ) AND $tax->publicly_queryable ) {
			// Set default value for "Arhive Content template"
			if ( ! $archive_content_id = get_term_meta( $term->term_id, 'archive_content_id', TRUE ) ) {
				$archive_content_id = '__defaults__';
			}
		?>
		<!-- Begin UpSolution meta settings -->
		<tr class="form-field term-display-archive_content_id-wrap">
			<th scope="row" valign="top">
				<label for="archive_content_id">
					<?= strip_tags( __( 'Archive Content template', 'us' ) ) ?>
				</label>
			</th>
			<td>
				<select id="archive_content_id" name="archive_content_id" class="postform">
					<?php foreach( $options as $value => $name ): ?>
						<option value="<?= esc_attr( $value ) ?>" <?php selected( $value, $archive_content_id ) ?>>
							<?= strip_tags( $name ) ?>
						</option>
					<?php endforeach ?>
				</select>
				<p class="description">
					<?= sprintf( __( 'Will apply to the "%s" archive page.', 'us' ), $term->name ) ?>
				</p>
			</td>
		</tr>
		<?php } ?>
		<tr class="form-field term-pages_content_id-wrap">
			<th scope="row" valign="top">
				<label for="pages_content_id">
					<?= strip_tags( __( 'Pages Content template', 'us' ) ) ?>
				</label>
			</th>
			<td>
				<select id="pages_content_id" name="pages_content_id" class="postform">
					<?php foreach ( $options as $value => $name ): ?>
						<option value="<?= esc_attr( $value ) ?>" <?php selected( $value, $pages_content_id ) ?>>
							<?= strip_tags( $name ) ?>
						</option>
					<?php endforeach ?>
				</select>
				<p class="description">
					<?= sprintf( __( 'Will apply to all pages with the "%s" taxonomy.', 'us' ), $term->name ) ?>
					<br>
					<?= $misc['content_description']; ?>
				</p>
			</td>
		</tr>
		<?php if ( us_get_option( 'og_enabled' ) AND $is_public ) {
			$seo_meta_fields = us_config( 'seo-meta-fields', array() );
			foreach ( array_keys( $seo_meta_fields ) as $meta_key ) {
				$$meta_key = get_term_meta( $term->term_id, $meta_key, TRUE );
			}
		?>
		<tr class="us-term-meta-title">
			<td colspan="2"><?= __( 'SEO meta tags', 'us' ) ?></td>
		</tr>
		<?php foreach ( $seo_meta_fields as $meta_key => $meta_options ) { ?>
		<tr class="form-field term-<?= $meta_key ?>-wrap">
			<th scope="row" valign="top">
				<?php if ( ! empty( $meta_options['title'] ) ){ ?>
				<label for="<?php esc_attr_e( $meta_key ) ?>">
					<?= strip_tags( $meta_options['title'] ) ?>
				</label>
				<?php } ?>
			</th>
			<td>
				<?php $_atts = array(
					'type' => 'text',
					'id' => $meta_key,
					'name' => $meta_key,
				); ?>
				<?php if ( $meta_options['type'] === 'text' ) { ?>
					<input<?= us_implode_atts( array_merge( $_atts, array( 'value' => $$meta_key ) ) ) ?> >
				<?php } else { ?>
					<textarea<?= us_implode_atts( array_merge( $_atts, array(
						'rows' => 5,
						'cols' => 50,
						'class' => 'large-text',
					) ) ) ?>><?= $$meta_key ?></textarea>
				<?php } ?>
				<?php if ( ! empty( $meta_options['description'] ) ) { ?>
					<p class="description"><?= $meta_options['description'] ?></p>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
		<script type="text/javascript">
			;(function( $, undefined ) {
				$( '.usof-example' ).on( 'click', function( e ) {
					var $target = $( e.currentTarget );
					$target
						.closest( 'tr' )
						.find( 'input[type="text"], textarea' )
						.val( $target.text() );
				} );
			})(jQuery);
		</script>
		<!-- End UpSolution meta settings -->
		<?php }
	}

	/**
	 * Save terms custom fields
	 *
	 * @param mixed $term_id Term ID being saved.
	 */
	function us_save_term_custom_fields( $term_id ) {
		$meta_keys = array_merge(
			array( 'pages_content_id', 'archive_content_id' ),
			array_keys( us_config( 'seo-meta-fields', array() ) )
		);
		foreach ( $meta_keys as $meta_key ) {
			if ( isset( $_POST[ $meta_key ] ) ) {
				update_term_meta( $term_id, $meta_key, esc_attr( $_POST[ $meta_key ] ) );
			}
		}
	}

	// Action assignments for all available taxonomies
	add_action( 'init', function () {
		foreach ( array_keys( us_get_taxonomies() ) as $tax_slug ) {
			add_action( "{$tax_slug}_edit_form_fields", 'us_term_custom_fields', 9 );
			add_action( "edited_{$tax_slug}", 'us_save_term_custom_fields', 10, 3 );
		}
	} );
}
