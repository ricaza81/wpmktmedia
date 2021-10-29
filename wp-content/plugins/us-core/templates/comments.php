<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * List of Post Comments with response form
 */

$comments_number = get_comments_number();
?>
<div id="comments" class="w-comments">
	<?php if ( have_comments() ) { ?>
		<h4 class="w-comments-title">
			<?php
			$comments_label = '<span>';
			$comments_label .= sprintf( us_translate_n( '%s <span class="screen-reader-text">Comment</span>', '%s <span class="screen-reader-text">Comments</span>', $comments_number ), $comments_number );
			$comments_label .= '.</span>';
			if ( comments_open() ) {
				$comments_label .= ' <a href="#respond">' . __( 'Leave new', 'us' ) . '</a>';
			}
			comments_number( us_translate( 'No Comments' ), $comments_label, $comments_label );
			?>
		</h4>

		<ul class="w-comments-list">
			<?php
			wp_list_comments(
				array(
					'callback' => 'us_comment_start',
					'style' => 'ul',
				)
			);
			?>
		</ul>

		<div class="w-comments-pagination">
			<?php previous_comments_link() ?>
			<?php next_comments_link() ?>
		</div>
	<?php } ?>

	<?php if ( comments_open() ) {
		if ( get_option( 'comment_registration' ) AND ! is_user_logged_in() ) {
			?>
			<div class="w-comments-form-text"><?php printf( us_translate( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( get_permalink() ) ); ?></div>
			<?php
		} else {
			$commenter = wp_get_current_commenter();

			$fields = array(
				'comment' => array(
					'type' => 'textarea',
					'name' => 'comment',
					'placeholder' => us_translate_x( 'Comment', 'noun' ),
					'required' => TRUE,
				),
				'author' => array(
					'type' => 'text',
					'name' => 'author',
					'placeholder' => us_translate( 'Name' ),
					'required' => get_option( 'require_name_email' ),
					'value' => $commenter['comment_author'],
				),
				'email' => array(
					'type' => 'email',
					'name' => 'email',
					'placeholder' => us_translate( 'Email' ),
					'required' => get_option( 'require_name_email' ),
					'value' => $commenter['comment_author_email'],
				),
			);

			// Add Cookie Consent field if it's enabled at Settings > Discussion
			if ( has_action( 'set_comment_cookies', 'wp_set_comment_cookies' ) && get_option( 'show_comments_cookies_opt_in' ) ) {
				$fields['cookies'] = array(
					'type' => 'agreement',
					'value' => us_translate( 'Save my name, email, and website in this browser for the next time I comment.' ),
					'name' => 'wp-comment-cookies-consent',
					'checked' => FALSE,
				);
			}

			$fields = apply_filters( 'us_comment_form_fields', $fields );

			$json_data = array(
				'no_content_msg' => __( 'Fill out this field', 'us' ),
				'no_name_msg' => __( 'Fill out this field', 'us' ),
				'no_email_msg' => us_translate( 'Please enter a valid email address.' ),
			);

			$comment_form_args = array( 'fields' => array() );
			foreach ( $fields as $field_name => $field ) {
				if ( $field_name == 'comment' ) {
					$comment_form_args['comment_field'] = us_get_template( 'templates/form/' . $field['type'], $field );
				} else {
					$comment_form_args['fields'][ $field_name ] = us_get_template( 'templates/form/' . $field['type'], $field );
				}
			}

			$comment_form_args['fields'] = apply_filters( 'comment_form_default_fields', $comment_form_args['fields'] );
			$comment_form_args['submit_button'] = '<button type="submit" class="w-btn us-btn-style_1"><span class="w-btn-label">' . us_translate( 'Post Comment' ) . '</span></button>';

			comment_form( $comment_form_args );

			// Echoing json to comments.js to pass admin-ajax.php URL and messages
			if ( ! us_amp() ) {
				echo '<div class="us-comments-json hidden"' . us_pass_data_to_js( $json_data ) . '></div>';
			}
		}
	}
	?>
</div>
