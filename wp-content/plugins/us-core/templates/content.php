<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs page's content Content Template (us_content_template)
 *
 * (!) Should be called after the current $wp_query is already defined
 *
 * @action Before the template: 'us_before_template:templates/content'
 * @action After the template: 'us_after_template:templates/content'
 * @filter Template variables: 'us_template_vars:templates/content'
 */

$content_template_content = '';
if ( $content_template_id = us_get_page_area_id( 'content' ) ) {

	if ( function_exists( 'us_register_context_layout' ) ) {
		us_register_context_layout( 'main' );
	}

	if ( $content_template = get_post( (int) $content_template_id ) ) {
		us_open_wp_query_context();

		// Some WPML tweaks
		$translated_content_template_id = apply_filters( 'us_tr_object_id', $content_template->ID, 'us_content_template', TRUE );
		// Fallback for case when post type is not yet migrated
		if ( $content_template->post_type == 'us_page_block' ) {
			$translated_content_template_id = apply_filters( 'us_tr_object_id', $content_template->ID, 'us_page_block', TRUE );
		}
		if ( $translated_content_template_id != $content_template->ID ) {
			$content_template = get_post( $translated_content_template_id );
		}

		us_add_to_page_block_ids( $translated_content_template_id );
		us_add_page_shortcodes_custom_css( $translated_content_template_id );

		$content_template_content = $content_template->post_content;

		us_close_wp_query_context();
	}

	// Apply filters to Content Template content and echoing it ouside of us_open_wp_query_context,
	// so all WP widgets (like WP Nav Menu) would work as they should
	$content_template_content = apply_filters( 'us_content_template_the_content', $content_template_content );

	// If content has no sections, we'll create them manually
	if ( strpos( $content_template_content, ' class="l-section' ) === FALSE ) {
		$content_template_content = '<section class="l-section height_' . us_get_option( 'row_height', 'medium' ) . '"><div class="l-section-h">' . $content_template_content . '</div></section>';
	}

	echo $content_template_content;

	if ( $content_template ) {
		us_remove_from_page_block_ids();
	}

}
