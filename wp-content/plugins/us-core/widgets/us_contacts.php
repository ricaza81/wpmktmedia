<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Contacts
 *
 * Class US_Widget_Contacts
 */
class US_Widget_Contacts extends US_Widget {

	/**
	 * Output the widget
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {

		parent::before_widget( $args, $instance );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$output = $args['before_widget'];

		if ( $title ) {
			$output .= '<h3 class="widgettitle">' . $title . '</h3>';
		}

		$template_vars = array(
			'address' => $instance['address'],
			'phone' => $instance['phone'],
			'fax' => $instance['fax'],
			'email' => $instance['email'],
		);

		$output .= us_get_template( 'templates/elements/contacts', $template_vars );
		$output .= $args['after_widget'];

		echo $output;
	}
}
