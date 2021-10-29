<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Socials
 *
 * Class US_Widget_Socials
 */
class US_Widget_Socials extends US_Widget {

	/**
	 * Output the widget
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	function widget( $args, $instance ) {

		parent::before_widget( $args, $instance );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$output = $args['before_widget'];

		if ( $title ) {
			$output .= '<h3 class="widgettitle">' . $title . '</h3>';
		}

		// Basic variables for template
		$template_vars = array(
			'us_elm_context' => 'shortcode',
			'hide_tooltip' => 0,
			'nofollow' => 1,
			'css' => '',
			'gap' => ( $instance['style'] != 'default' ) ? '0.1em' : '',
		);

		$items = array();

		if ( isset( $this->config['params'] ) AND is_array( $this->config['params'] ) ) {
			foreach ( $this->config['params'] as $param_name => $param ) {
				if ( in_array(
					$param_name, array(
					'title',
					'custom_link',
					'custom_title',
					'custom_icon',
					'custom_color',
				)
				) ) {
					continue;

				} elseif ( in_array(
					$param_name, array(
					'size',
					'style',
					'color',
					'shape',
					'hover',
				)
				) ) {
					$template_vars[ $param_name ] = $instance[ $param_name ];

				} else {
					$items[] = array(
						'type' => $param_name,
						'url' => $instance[ $param_name ],
					);
				}
			}
		}

		// Add custom type item
		$items[] = array(
			'type' => 'custom',
			'url' => $instance['custom_link'],
			'title' => $instance['custom_title'],
			'icon' => $instance['custom_icon'],
			'color' => $instance['custom_color'],
		);

		$template_vars['items'] = json_encode( $items );

		$output .= us_get_template( 'templates/elements/socials', $template_vars );
		$output .= $args['after_widget'];

		echo $output;
	}
}
