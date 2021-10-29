<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );


// Add categories checkboxes to Image dialog
add_filter( 'attachment_fields_to_edit', 'us_attachment_fields_to_edit_categories', 10, 2 );
function us_attachment_fields_to_edit_categories( $form_fields, $post ) {

	foreach ( get_attachment_taxonomies( $post->ID ) as $taxonomy ) {

		$t = (array) get_taxonomy( $taxonomy );
		if ( ! $t['public'] OR ! $t['show_ui'] ) {
			continue;
		}
		if ( empty( $t['label'] ) ) {
			$t['label'] = $taxonomy;
		}
		if ( empty( $t['args'] ) ) {
			$t['args'] = array();
		}

		$terms = get_object_term_cache( $post->ID, $taxonomy );
		if ( FALSE === $terms ) {
			$terms = wp_get_object_terms( $post->ID, $taxonomy, $t['args'] );
		}

		// Get the values in a list
		$values = array();
		foreach ( $terms as $term ) {
			$values[] = $term->slug;
		}
		$t['value'] = join( ', ', $values );

		$t['show_in_edit'] = FALSE;

		if ( $t['hierarchical'] OR $taxonomy == 'us_media_category' ) {
			ob_start();

			wp_terms_checklist(
				$post->ID, array(
					'taxonomy' => $taxonomy,
					'checked_ontop' => FALSE,
					'walker' => new US_Walker_Media_Categories_Checklist(),
				)
			);

			if ( ob_get_contents() != FALSE ) {
				$html = '<ul class="us-media-list">' . ob_get_contents() . '</ul>';
			} else {
				$html = '<div class="us-media-none">' . us_translate( 'No categories found.' ) . ' <a href="' . admin_url( '/edit-tags.php?taxonomy=us_media_category&post_type=attachment' ) . '">' . us_translate( 'Add new category' ) . '</a></div>';
			}

			ob_end_clean();

			$t['input'] = 'html';
			$t['html'] = $html;
		}

		$form_fields[ $taxonomy ] = $t;
	}

	return $form_fields;
}

class US_Walker_Media_Categories_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array(
		'parent' => 'parent',
		'id' => 'term_id',
	);

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {

		// Get taxonomy
		$taxonomy = empty( $args['taxonomy'] ) ? 'us_media_category' : $args['taxonomy'];

		$name = 'tax_input[' . $taxonomy . ']';

		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'>";
		$output .= '<label class="selectit">';
		$output .= '<input value="' . $category->slug . '" ';
		$output .= 'type="checkbox" ';
		$output .= 'name="' . $name . '[' . $category->slug . ']" ';
		$output .= 'id="in-' . $taxonomy . '-' . $category->term_id . '"';
		$output .= checked( in_array( $category->term_id, $args['selected_cats'] ), TRUE, FALSE );
		$output .= disabled( empty( $args['disabled'] ), FALSE, FALSE );
		$output .= ' /> ';
		$output .= esc_html( apply_filters( 'the_category', $category->name ) );
		$output .= '</label>';
	}

	public function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}

// Save categories from attachment edit menu
add_action( 'wp_ajax_save-attachment-compat', 'us_save_attachment_compat', 0 );
function us_save_attachment_compat() {
	if ( ! isset( $_REQUEST['id'] ) ) {
		wp_send_json_error();
	}

	if ( ! $id = absint( $_REQUEST['id'] ) ) {
		wp_send_json_error();
	}

	if ( empty( $_REQUEST['attachments'] ) OR empty( $_REQUEST['attachments'][ $id ] ) ) {
		wp_send_json_error();
	}
	$attachment_data = $_REQUEST['attachments'][ $id ];

	check_ajax_referer( 'update-post_' . $id, 'nonce' );

	if ( ! current_user_can( 'edit_post', $id ) ) {
		wp_send_json_error();
	}

	$post = get_post( $id, ARRAY_A );

	if ( 'attachment' != $post['post_type'] ) {
		wp_send_json_error();
	}

	// This filter is documented in wp-admin/includes/media.php
	$post = apply_filters( 'attachment_fields_to_save', $post, $attachment_data );

	if ( isset( $post['errors'] ) ) {
		$errors = $post['errors'];
		unset( $post['errors'] );
	}

	wp_update_post( $post );

	foreach ( get_attachment_taxonomies( $post ) as $taxonomy ) {
		if ( isset( $attachment_data[ $taxonomy ] ) ) {
			wp_set_object_terms( $id, array_map( 'trim', preg_split( '/,+/', $attachment_data[ $taxonomy ] ) ), $taxonomy, FALSE );
		} elseif ( isset( $_REQUEST['tax_input'] ) AND isset( $_REQUEST['tax_input'][ $taxonomy ] ) ) {
			wp_set_object_terms( $id, $_REQUEST['tax_input'][ $taxonomy ], $taxonomy, FALSE );
		} else {
			wp_set_object_terms( $id, '', $taxonomy, FALSE );
		}
	}

	if ( ! $attachment = wp_prepare_attachment_for_js( $id ) ) {
		wp_send_json_error();
	}

	wp_send_json_success( $attachment );
}

if ( ! function_exists( 'us_ajax_media_categories_query_attachments' ) ) {
	/**
	 * Ajax handler for querying attachments.
	 */
	function us_ajax_media_categories_query_attachments() {

		// Bail if user cannot upload files
		if ( ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error();
		}

		// Get names of media taxonomies
		$taxonomies = get_object_taxonomies( 'attachment', 'names' );

		// Look for query
		$query = isset( $_REQUEST['query'] ) ? (array) $_REQUEST['query'] : array();

		// Default arguments
		$defaults = array(
			'monthnum',
			'order',
			'orderby',
			'paged',
			'post__in',
			'post__not_in',
			'post_mime_type',
			'post_parent',
			'posts_per_page',
			's',
			'year',
		);

		$query = array_intersect_key( $query, array_flip( array_merge( $defaults, $taxonomies ) ) );

		$query['post_type'] = 'attachment';
		$query['post_status'] = 'inherit';
		if ( current_user_can( get_post_type_object( 'attachment' )->cap->read_private_posts ) ) {
			$query['post_status'] .= ',private';
		}

		// Filter query clauses to include filenames.
		if ( isset( $query['s'] ) ) {
			add_filter( 'posts_clauses', '_filter_query_attachment_filenames' );
		}

		if ( ! empty( $taxonomies ) ) {
			$query['tax_query'] = array( 'relation' => 'AND' );
			foreach ( $taxonomies as $taxonomy ) {
				if ( isset( $query[ $taxonomy ] ) ) {

					// Filter a specific category
					if ( is_numeric( $query[ $taxonomy ] ) ) {
						array_push(
							$query['tax_query'], array(
								'taxonomy' => $taxonomy,
								'field' => 'id',
								'terms' => $query[ $taxonomy ],
							)
						);
					}

					// Filter No category
					if ( $query[ $taxonomy ] == 'no_category' ) {
						$all_terms_ids = us_media_categories_get_terms_values( 'ids' );
						array_push(
							$query['tax_query'], array(
								'taxonomy' => $taxonomy,
								'field' => 'id',
								'terms' => $all_terms_ids,
								'operator' => 'NOT IN',
							)
						);
					}
				}

				unset( $query[ $taxonomy ] );
			}
		}

		$query = apply_filters( 'ajax_query_attachments_args', $query );
		$query = new WP_Query( $query );

		$posts = array_map( 'wp_prepare_attachment_for_js', $query->posts );
		$posts = array_filter( $posts );
		$total_posts = $query->found_posts;

		if ( $total_posts < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $query['paged'] );

			$count_query = new WP_Query();
			$count_query->query( $query );
			$total_posts = $count_query->found_posts;
		}

		$max_pages = ceil( $total_posts / (int) $query->query['posts_per_page'] );

		header( 'X-WP-Total: ' . (int) $total_posts );
		header( 'X-WP-TotalPages: ' . (int) $max_pages );

		wp_send_json_success( $posts );
	}

	add_action( 'wp_ajax_query-attachments', 'us_ajax_media_categories_query_attachments', 0 );
}

// Get media categories
function us_media_categories_get_terms_values( $keys = 'ids' ) {

	$media_terms = get_terms(
		'us_media_category', array(
			'hide_empty' => 0,
			'fields' => 'id=>slug',
		)
	);

	$media_values = array();
	foreach ( $media_terms as $key => $value ) {
		$media_values[] = ( $keys === 'ids' ) ? $key : $value;
	}

	return $media_values;
}

// update_count_callback
function us_media_category_update_count_callback( $terms = array(), $media_taxonomy = 'us_media_category' ) {
	global $wpdb;

	// select id & count from taxonomy
	$sql = "SELECT term_taxonomy_id, MAX(total) AS total FROM ((
				SELECT tt.term_taxonomy_id, COUNT(*) AS total
					FROM {$wpdb->term_relationships} tr, {$wpdb->term_taxonomy} tt
					WHERE tr.term_taxonomy_id = tt.term_taxonomy_id
						AND tt.taxonomy = %s
					GROUP BY tt.term_taxonomy_id
				) UNION ALL (
					SELECT term_taxonomy_id, 0 AS total
						FROM {$wpdb->term_taxonomy}
						WHERE taxonomy = %s
				)) AS unioncount GROUP BY term_taxonomy_id";

	$prepared = $wpdb->prepare( $sql, $media_taxonomy->name, $media_taxonomy->name );
	$count = $wpdb->get_results( $prepared );

	// update all count values from taxonomy
	foreach ( $count as $row_count ) {
		$wpdb->update(
			$wpdb->term_taxonomy, array( 'count' => $row_count->total ), array( 'term_taxonomy_id' => $row_count->term_taxonomy_id )
		);
	}
}

// Media category filter
add_action( 'admin_enqueue_scripts', 'us_media_categories_enqueue_admin_scripts' );
function us_media_categories_enqueue_admin_scripts() {
	global $pagenow;

	$pages_to_add = array( 'upload.php', 'post.php', 'post-new.php' );

	if ( wp_script_is( 'media-editor' ) AND in_array( $pagenow, $pages_to_add ) ) {
		// Dropdown
		$attachment_terms = wp_dropdown_categories(
			array(
				'taxonomy' => 'us_media_category',
				'hide_empty' => FALSE,
				'hierarchical' => TRUE,
				'orderby' => 'name',
				'show_count' => TRUE,
				'walker' => new US_Walker_Media_Categories_Media_Grid(),
				'value' => 'id',
				'echo' => FALSE,
			)
		);
		// No select
		$attachment_terms = preg_replace( array( '/<select([^>]*)>/', '/<\/select>/' ), '', $attachment_terms );

		// Add an attachment_terms for No category
		$no_category_term = ' ,{"term_id":"' . 'no_category' . '","term_name":"' . __( 'Without categories', 'us' ) . '"}';
		$attachment_terms = $no_category_term . substr( $attachment_terms, 1 );

		echo '<script type="text/javascript">';
		echo '/* <![CDATA[ */';
		echo 'var us_media_categories_taxonomies = {"' . 'us_media_category' . '":';
		echo '{"list_title":"&ndash; ' . html_entity_decode( __( 'Filter by Categories', 'us' ), ENT_QUOTES, 'UTF-8' ) . ' &ndash;",';
		echo '"term_list":[' . substr( $attachment_terms, 2 ) . ']}};';
		echo '/* ]]> */';
		echo '</script>';

		// Script
		wp_enqueue_script( 'us-media-categories-media-views', US_CORE_URI . '/admin/js/media-views.js', array( 'media-views' ) );
	}
}

// Custom walker for wp_dropdown_categories for media grid view filter
class US_Walker_Media_Categories_Media_Grid extends Walker_CategoryDropdown {

	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$pad = str_repeat( '&nbsp;', $depth * 3 );
		$cat_name = apply_filters( 'list_cats', $category->name, $category );

		$output .= ',{"term_id":"' . $category->term_id . '","term_name":"' . $pad . esc_attr( $cat_name );
		if ( $args['show_count'] ) {
			$output .= '&nbsp;(' . $category->count . ')';
		}
		$output .= '"}';
	}
}

add_action( 'wp_ajax_us_ajax_set_category_on_upload', 'us_ajax_media_categories_set_attachment_category' );
function us_ajax_media_categories_set_attachment_category() {

	$post_ID = (int) $_POST['post_id'];
	$category = $_POST['category'];

	// Check whether this user can edit this post
	if ( ! current_user_can( 'edit_post', $post_ID ) ) {
		return;
	}

	if ( $category != 'all' OR $category != 'no_category' ) {
		$category = (int) $category;
		wp_set_object_terms( $post_ID, $category, 'us_media_category', TRUE );
	}


}
