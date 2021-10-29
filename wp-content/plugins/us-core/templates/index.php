<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying the 404 page
 */

$posts_page = get_post( us_get_option( 'posts_page' ) );

// Output specific page
if ( $posts_page ) {
	if ( has_filter( 'us_tr_object_id' ) ) {
		$posts_page = get_post( (int) apply_filters( 'us_tr_object_id', $posts_page->ID, 'page', TRUE ) );
	}

	us_register_context_layout( 'header' );
	get_header();
	// If set custom page load its css
	if ( is_object( $posts_page ) ) {
		us_output_design_css( [ $posts_page ] );
	}

	us_register_context_layout( 'main' );
	?>
	<main id="page-content" class="l-main"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>

		<?php
		do_action( 'us_before_page' );

		if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) {

			// Titlebar, if it is enabled in Theme Options
			us_load_template( 'templates/titlebar' );

			// START wrapper for Sidebar
			us_load_template( 'templates/sidebar', array( 'place' => 'before' ) );
		}

		us_open_wp_query_context();

		us_add_page_shortcodes_custom_css( $posts_page->ID );

		us_close_wp_query_context();

		// Setting search page ID as $us_page_block_id for grid shortcodes
		us_add_to_page_block_ids( $posts_page->ID );

		echo apply_filters( 'the_content', $posts_page->post_content );

		us_remove_from_page_block_ids();

		if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) {
			// AFTER wrapper for Sidebar
			us_load_template( 'templates/sidebar', array( 'place' => 'after' ) );
		}

		do_action( 'us_after_page' );
		?>

	</main>
	<?php

	us_register_context_layout( 'footer' );
	get_footer();

	// Output default archive layout
} else {
	us_load_template( 'templates/archive' );
}
