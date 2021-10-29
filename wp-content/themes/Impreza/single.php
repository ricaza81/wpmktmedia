<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying all single posts
 *
 * Do not overload this file directly. Instead have a look at templates/single.php file in us-core plugin folder:
 * you should find all the needed hooks there.
 */

if ( function_exists( 'us_load_template' ) ) {

	us_load_template( 'templates/single' );

} else {
	get_header();
	?>
	<main id="page-content" class="l-main">
		<?php
		while ( have_posts() ) {
			the_post();

			get_template_part( 'content' );
		}
		?>
	</main>
	<?php
	get_footer();
}
