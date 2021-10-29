<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Page Block element
 */

if ( is_numeric( $id ) ) {
	if ( has_filter( 'us_tr_object_id' ) ) {
		$id = apply_filters( 'us_tr_object_id', $id, 'us_page_block', TRUE );
	}
	$page_block = get_post( $id );
} else {
	return;
}

if ( $page_block instanceof WP_Post AND $page_block->post_type == 'us_page_block' ) {
	us_add_to_page_block_ids( $page_block->ID );

	$page_block_content = $page_block->post_content;
	us_open_wp_query_context();

	us_add_page_shortcodes_custom_css( $id );

	us_close_wp_query_context();
} else {
	return;
}

// Remove [vc_row] and [vc_column] if set
if ( $remove_rows == '1' ) {
	$page_block_content = str_replace( array( '[vc_row]', '[/vc_row]', '[vc_column]', '[/vc_column]' ), '', $page_block_content );
	$page_block_content = preg_replace( '~\[vc_row (.+?)]~', '', $page_block_content );
	$page_block_content = preg_replace( '~\[vc_column (.+?)]~', '', $page_block_content );

	// Force fullwidth for all [vc_row] if set
} elseif ( $force_fullwidth_rows ) {
	$page_block_content = str_replace( '[vc_row]', '[vc_row width="full"]', $page_block_content );
	$page_block_content = str_replace( '[vc_row ', '[vc_row width="full" ', $page_block_content );
}

// Apply filters to Page Block content and echoing it out of us_open_wp_query_context
echo apply_filters( 'us_page_block_the_content', $page_block_content );

us_remove_from_page_block_ids();
