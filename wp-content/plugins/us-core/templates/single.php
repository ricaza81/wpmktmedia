<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Template to show single page or any post type
 */

$us_layout = US_Layout::instance();

us_register_context_layout( 'header' );
get_header();

us_register_context_layout( 'main' );

// Check if the Content template is applied to this page
if ( $content_area_id = us_get_page_area_id( 'content' ) AND get_post_status( $content_area_id ) != FALSE ) {
	$has_content_template = TRUE;
	$usbid_container_attribute = '';
} else {
	$has_content_template = FALSE;

	// If no Content template, add the specific attribute to enable correct editing in USBuilder
	$usbid_container_attribute = apply_filters( 'usb_get_usbid_container', NULL );
}

?>
<main id="page-content" class="l-main"<?php echo $usbid_container_attribute . ( ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''); ?>>
	<?php
	do_action( 'us_before_page' );

	if ( us_get_option( 'enable_sidebar_titlebar', 0 ) ) {

		// Titlebar, if it is enabled in Theme Options
		us_load_template( 'templates/titlebar' );

		// START wrapper for Sidebar
		us_load_template( 'templates/sidebar', array( 'place' => 'before' ) );
	}

	while ( have_posts() ) {
		the_post();

		if ( $has_content_template ) {
			us_load_template( 'templates/content' );
		} else {
			$the_content = apply_filters( 'the_content', get_the_content() );

			// The page may be paginated itself via <!--nextpage--> tags
			$pagination = us_wp_link_pages();

			// If content has no sections, we'll create them manually
			if (
				! ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() )
				AND ( strpos( $the_content, ' class="l-section-h ' ) === FALSE OR get_post_type() == 'tribe_events' )
			) {
				echo '<section class="l-section height_' . us_get_option( 'row_height', 'medium' ) . '">';
				echo '<div class="l-section-h i-cf">';
				echo $the_content;
				echo $pagination; // append pagination into the same section
				echo '</div>';
				echo '</section>';

			} elseif ( ! empty( $pagination ) ) {
				echo $the_content;
				echo '<section class="l-section height_' . us_get_option( 'row_height', 'medium' ) . '">';
				echo '<div class="l-section-h i-cf">';
				echo $pagination; // append pagination in a separate section
				echo '</div>';
				echo '</section>';

			} else {
				echo $the_content;
			}

			// Post comments
			if (
				( comments_open() OR get_comments_number() )
				AND empty ( $usbid_container_attribute ) // Do not show the comments in live builder preview mode
			) {
				$show_comments = TRUE;

				// Check comments option of Events Calendar plugin
				if ( function_exists( 'tribe_get_option' ) AND get_post_type() == 'tribe_events' ) {
					$show_comments = tribe_get_option( 'showComments' );
				}

				if ( $show_comments ) {
					?>
				<section class="l-section height_<?php echo us_get_option( 'row_height', 'medium' ) ?> for_comments">
					<div class="l-section-h i-cf"><?php
						if ( ! us_amp() ) {
							wp_enqueue_script( 'comment-reply' );
						}
						comments_template();
						?></div>
					</section><?php
				}
			}
		}
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
get_footer()
?>
