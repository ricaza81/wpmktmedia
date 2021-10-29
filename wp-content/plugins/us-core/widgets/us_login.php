<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Login
 *
 * Class US_Widget_Login
 */
class US_Widget_Login extends US_Widget {

	/**
	 * Output the widget
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {

		parent::before_widget( $args, $instance );

		$output = $args['before_widget'];

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		// Output title
		if ( $title ) {
			$output .= '<h3 class="widgettitle">' . $title . '</h3>';
		}

		// Pass all values separately to avoid errors in element template
		$widget_data = array(
			'register' => $instance['register'],
			'lost_password' => $instance['lostpass'],
			'login_redirect' => $instance['login_redirect'],
			'logout_redirect' => $instance['logout_redirect'],
			'use_ajax' => TRUE,
		);

		$output .= us_get_template( 'templates/elements/login', $widget_data );
		$output .= $args['after_widget'];

		echo $output;
	}
}
