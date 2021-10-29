<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying search results pages
 *
 * Do not overload this file directly. Instead have a look at templates/search.php file in us-core plugin folder:
 * you should find all the needed hooks there.
 */

if ( function_exists( 'us_load_template' ) ) {

	us_load_template( 'templates/search' );

} else {
	get_header();
	?>
	<main id="page-content" class="l-main">
		<section class="l-section">
			<div class="l-section-h i-cf">
				<h1 class="page-title">
					<?php echo sprintf( us_translate( 'Search Results for &#8220;%s&#8221;' ), get_search_query() ); ?>
				</h1>
				<?php
				if ( have_posts() ) {

					// Load posts loop
					while ( have_posts() ) {
						the_post();
						get_template_part( 'content' );
					}

					// Pagination
					the_posts_pagination(
						array(
							'mid_size' => 3,
							'before_page_number' => '<span>',
							'after_page_number' => '</span>',
						)
					);

				} else {
					echo us_translate( 'No results found.' );
				}
				?>
			</div>
		</section>
	</main>
	<?php
	get_footer();
}
