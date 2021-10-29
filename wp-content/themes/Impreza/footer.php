<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying pages footers
 *
 * Do not overload this file directly. Instead have a look at templates/footer.php file in us-core plugin folder:
 * you should find all the needed hooks there.
 */

if ( function_exists( 'us_load_template' ) ) {

	us_load_template( 'templates/footer' );

} else {
	?>
		</div>
		<footer	class="l-footer">
			<section class="l-section color_footer-top">
				<div class="l-section-h i-cf align_center">
					<span><?php bloginfo( 'name' ); ?></span>
				</div>
			</section>
		</footer>
		<?php wp_footer(); ?>
	</body>
	</html>
	<?php
}
