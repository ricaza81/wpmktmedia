<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// Improve comments list HTML for better appearance
if ( ! function_exists( 'us_comment_start' ) ) {
	function us_comment_start( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		$author_url = get_comment_author_url();
		?>
		<li <?php comment_class( 'w-comments-item' ) ?> id="comment-<?php comment_ID() ?>">
			<div class="w-comments-item-meta">
				<?php echo get_avatar( $comment, $size = '50' ); ?>
				<div class="w-comments-item-author">
					<?php if ( $author_url != '' ) { echo '<a href="' . esc_url( $author_url ) . '" target="_blank" rel="noopener nofollow">'; } ?>
					<span><?php echo get_comment_author(); ?></span>
					<?php if ( $author_url != '' ) { echo '</a>'; } ?>
				</div>
				<a class="w-comments-item-date smooth-scroll" href="#comment-<?php comment_ID() ?>"><?php echo get_comment_date() . ' ' . get_comment_time() ?></a>
			</div>
			<div class="w-comments-item-text">
				<?php if ( $comment->comment_approved == '0') { ?>
					<div class="w-message color_yellow"><?php echo us_translate( 'Your comment is awaiting moderation.' ) ?></div>
				<?php }
				comment_text(); ?>
			</div>

			<?php
			comment_reply_link(
				array_merge(
					$args,
					array( 'depth' => $depth )
				)
			);

	}
}
