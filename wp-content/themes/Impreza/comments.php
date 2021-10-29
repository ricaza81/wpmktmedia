<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying comments
 */

if ( post_password_required() ) {
	return;
}

if ( function_exists( 'us_load_template' ) ) {

	us_load_template( 'templates/comments' );

} elseif ( comments_open() OR get_comments_number() ) {

	wp_enqueue_script( 'comment-reply' );
	?>
	<section class="l-section for_comments color_alternate">
		<div class="l-section-h i-cf">
			<div id="comments" class="w-comments">
				<?php
				// Comments List
				if ( have_comments() ) {
					?>
					<h4 class="w-comments-title"><?php comments_number(); ?></h4>
					<div class="w-comments-list">
						<?php
						wp_list_comments(
							array(
								'callback' => 'us_comment_start',
							)
						);
						?>
					</div>
					<div class="w-comments-pagination">
						<?php previous_comments_link(); ?>
						<?php next_comments_link(); ?>
					</div>
					<?php
				}

				// Comments form
				if ( comments_open() ) {
					comment_form();
				}
				?>
			</div>
		</div>
	</section>
	<?php
}
