<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

abstract class US_Widget extends WP_Widget {

	public function __construct() {

		// Widget name may be set either from overloading class attribute ...
		$id_base = $this->id_base;
		if ( empty( $id_base ) ) {
			// ... or fetched from the US_Widget_<name> part
			$id_base = preg_replace( '/(wp_)?widget_/', '', strtolower( get_class( $this ) ) );
		}

		$this->config = us_config( 'widgets.' . $id_base );
		if ( $this->config === NULL ) {
			wp_die( 'Wrong UpSolution widget definition: id_base should be set by attribute or by class name' );
		}

		parent::__construct(
			$id_base, $this->config['name'], array(
			'description' => $this->config['description'],
		)
		);
	}

	/**
	 * Common widgets' jobs
	 *
	 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function before_widget( &$args, &$instance ) {
		if ( isset( $this->config['params'] ) AND is_array( $this->config['params'] ) ) {
			foreach ( $this->config['params'] as $param_name => $param ) {
				if ( ! isset( $instance[$param_name] ) ) {
					$instance[$param_name] = isset( $param['std'] ) ? $param['std'] : '';
				}
			}
		}

		$instance['title'] = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
	}

	/**
	 * Output the settings update form.
	 *
	 * @param array $instance Current settings.
	 *
	 * @return string Form's output marker that could be used for further hooks
	 */
	public function form( $instance ) {
		if ( ! isset( $this->config['params'] ) OR ! is_array( $this->config['params'] ) OR empty( $this->config['params'] ) ) {
			// 'noform'
			return parent::form( $instance );
		}

		foreach ( $this->config['params'] as $param_name => $param ) {
			if ( ! isset( $param['type'] ) ) {
				$param['type'] = 'text';
			}
			if ( ! method_exists( $this, 'form_' . $param['type'] ) ) {
				continue;
			}
			$param['name'] = isset( $param['name'] ) ? $param['name'] : $param_name;
			$param['std'] = isset( $param['std'] ) ? $param['std'] : '';
			$value = isset( $instance[$param_name] ) ? $instance[$param_name] : $param['std'];
			$this->{'form_' . $param['type']}( $param, $value );
		}

		return 'usform';
	}

	/**
	 * Output form's text element
	 *
	 * @param array  $param Parameter from config
	 * @param string $value Current value
	 */
	public function form_text( $param, $value ) {
		$param['heading'] = isset( $param['heading'] ) ? $param['heading'] : $param['name'];
		$field_id = $this->get_field_id( $param['name'] );
		$output = '<p>';
		$output .= '<label for="' . esc_attr( $field_id ) . '">' . $param['heading'] . ':</label>';
		$output .= '<input class="widefat" id="' . esc_attr( $field_id ) . '" ';
		$output .= 'name="' . esc_attr( $this->get_field_name( $param['name'] ) ) . '" type="text" value="' . esc_attr( $value ) . '" />';
		$output .= '</p>';

		echo $output;
	}

	/**
	 * Output form's dropdown element
	 *
	 * @param array  $param Parameter from config
	 * @param string $value Current value
	 */
	public function form_dropdown( $param, $value ) {
		$param['heading'] = isset( $param['heading'] ) ? $param['heading'] : $param['name'];
		$field_id = $this->get_field_id( $param['name'] );
		$output = '<p><label for="' . esc_attr( $field_id ) . '">' . $param['heading'] . ':</label>';
		$output .= '<select name="' . esc_attr( $this->get_field_name( $param['name'] ) ) . '" id="' . esc_attr( $field_id ) . '" class="widefat">';
		if ( isset( $param['value'] ) AND is_array( $param['value'] ) ) {
			foreach ( $param['value'] as $option_key => $option_value ) {
				if ( is_string( $option_value ) ) {
					$output .= '<option value="' . esc_attr( $option_key ) . '"' . selected( $value, $option_key, FALSE ) . '>' . $option_value . '</option>';
				} else if ( ! empty( $option_value ) AND is_array( $option_value ) ) {
					$output .= '<optgroup label="' . esc_attr( $option_key ) . '">';
					foreach ( $option_value AS $item_key => $item_value ) {
						if ( empty( $item_value ) ) {
							continue;
						}
						$output .= '<option value="' . esc_attr( $item_key ) . '"' . selected( $value, $item_key, FALSE ) . '>' . $item_value . '</option>';
					}
					$output .= '</optgroup>';
				}
			}
		}
		$output .= '</select></p>';

		echo $output;
	}

	/**
	 * Output form's checkbox element
	 *
	 * @param array  $param Parameter from config
	 * @param string $value Current value
	 */
	public function form_checkbox( $param, $value ) {
		$output = '<p>';
		if ( $param['heading'] ) {
			$output .= '<span>' . $param['heading'] . ':</span><br>';
		}
		if ( isset( $param['value'] ) AND is_array( $param['value'] ) ) {
			foreach ( $param['value'] as $value_title => $value_key ) {
				$name = $param['name'] . '-' . esc_attr( $value_key );
				$checked = ( is_array( $value ) AND in_array( $value_key, $value ) ) ? ' checked="checked"' : '';
				$output .= '<label><input name="' . esc_attr( $this->get_field_name( $param['name'] ) ) . '[]" type="checkbox" value="' . esc_attr( $value_key ) . '"' . $checked . '>' . $value_title . '</label><br>';
			}
		}
		$output .= '</p>';

		echo $output;
	}

	/**
	 * Output form's textarea element
	 *
	 * @param array  $param Parameter from config
	 * @param string $value Current value
	 */
	public function form_textarea( $param, $value ) {
		$param['heading'] = isset( $param['heading'] ) ? $param['heading'] : $param['name'];
		$field_id = $this->get_field_id( $param['name'] );
		$output = '<p>';
		$output .= '<label for="' . esc_attr( $field_id ) . '">' . $param['heading'] . ':</label>';
		$output .= '<textarea class="widefat" rows="5" cols="20" id="' . esc_attr( $field_id ) . '" ';
		$output .= 'name="' . esc_attr( $this->get_field_name( $param['name'] ) ) . '" style="height: 47px;">' . esc_textarea( $value ) . '</textarea>';
		$output .= '</p>';

		echo $output;
	}

}

add_action( 'widgets_init', 'us_widgets_init' );
function us_widgets_init() {
	$config = us_config( 'widgets', array() );

	if ( us_get_option( 'enable_portfolio', 1 ) == 0 ) {
		unset( $config['us_portfolio'] );
	}

	foreach ( $config as $widget_id => $widget ) {
		if ( ! isset( $widget['class'] ) ) {
			wp_die( 'Widget class should be defined for ' . $widget_id );
		}
		if ( ! class_exists( $widget['class'] ) ) {
			$filepath = us_locate_file( 'widgets/' . $widget_id . '.php' );
			if ( $filepath === FALSE ) {
				continue;
			}
			include $filepath;
			if ( ! class_exists( $widget['class'] ) ) {
				wp_die( 'Widget class ' . $widget['class'] . ' is not found at ' . $filepath );
			}
		}
		register_widget( $widget['class'] );
	}

	//override WP gallery
	if ( class_exists( 'WP_Widget_Media_Gallery' ) ) {
		$filepath = us_locate_file( 'widgets/us_gallery.php' );
		include $filepath;
		unregister_widget( 'WP_Widget_Media_Gallery' );
		register_widget( 'US_Widget_Media_Gallery' );
	}

}
