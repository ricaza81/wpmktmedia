<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs Titlebar content
 *
 * @filter Template variables: 'us_template_vars:templates/content'
 */

global $us_iframe;
if ( $us_iframe ) {
	return;
}

// Get Titlebar content
$titlebar_content = '';
$titlebar_id = us_get_page_area_id( 'titlebar' );
if ( $titlebar_id != '' ) {

	$page_block = get_post( (int) $titlebar_id );

	us_open_wp_query_context();
	if ( $page_block ) {
		$translated_page_block_id = apply_filters( 'us_tr_object_id', $page_block->ID, 'us_page_block', TRUE );
		if ( $translated_page_block_id != $page_block->ID ) {
			$page_block = get_post( $translated_page_block_id );
		}

		us_add_to_page_block_ids( $translated_page_block_id );
		us_add_page_shortcodes_custom_css( $translated_page_block_id );

		$titlebar_content = $page_block->post_content;
	}
	us_close_wp_query_context();

	// Apply filters to Page Block content and echoing it ouside of us_open_wp_query_context,
	// so all WP widgets (like WP Nav Menu) would work as they should
	echo apply_filters( 'us_page_block_the_content', $titlebar_content );

	if ( $page_block ) {
		us_remove_from_page_block_ids();
	}
}
