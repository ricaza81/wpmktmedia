<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying the 404 page
 */

// Output specific page
if ( $page_404 = get_post( us_get_option( 'page_404' ) ) ) {

	if ( has_filter( 'us_tr_object_id' ) ) {
		$page_404 = get_post( apply_filters( 'us_tr_object_id', $page_404->ID, 'page', TRUE ) );
	}

	us_register_context_layout( 'header' );
	get_header();

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

		$content_area_id = us_get_page_area_id( 'content' );

		if ( $content_area_id != '' AND get_post_status( $content_area_id ) != FALSE ) {
			us_load_template( 'templates/content' );
		} else {
			us_open_wp_query_context();

			us_add_page_shortcodes_custom_css( $page_404->ID );

			remove_filter( 'the_content', 'prepend_attachment' );
			echo apply_filters( 'the_content', $page_404->post_content );

			us_close_wp_query_context();
		}

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

	// Output predefined layout
} else {
	$us_layout = US_Layout::instance();

	us_register_context_layout( 'header' );
	get_header();
	us_register_context_layout( 'main' );
	?>
	<main id="page-content" class="l-main">
		<section class="l-section height_<?php echo us_get_option( 'row_height', 'medium' ); ?>">
			<div class="l-section-h i-cf">

				<?php do_action( 'us_before_404' ) ?>

				<div class="page-404 align_center">
					<?php
					$the_content = '<h1>' . us_translate( 'Page not found' ) . '</h1>';
					$the_content .= '<p>' . __( 'The link you followed may be broken, or the page may have been removed.', 'us' ) . '</p>';
					echo apply_filters( 'us_404_content', $the_content );
					?>
				</div>

				<?php do_action( 'us_after_404' ) ?>

			</div>
		</section>
	</main>
	<?php
	us_register_context_layout( 'footer' );
	get_footer();
}
