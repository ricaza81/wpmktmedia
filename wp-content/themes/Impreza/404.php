<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying the 404 page
 *
 * Do not overload this file directly. Instead have a look at templates/404.php file in us-core plugin folder:
 * you should find all the needed hooks there.
 */

if ( function_exists( 'us_load_template' ) ) {

	us_load_template( 'templates/404' );

} else {
	get_header();
	?>
	<main id="page-content" class="l-main">
		<section class="l-section">
			<div class="l-section-h i-cf">
				<div class="page-404 align_center">
					<?php
					$the_content = '<h1>' . us_translate( 'Page not found' ) . '</h1>';
					$the_content .= '<p>' . __( 'The link you followed may be broken, or the page may have been removed.', 'us' ) . '</p>';
					echo apply_filters( 'us_404_content', $the_content );
					?>
				</div>
			</div>
		</section>
	</main>
	<?php
	get_footer();
}
