<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Blog
 *
 * Class US_Widget_Blog
 */

class US_Widget_Blog extends US_Widget {

	/**
	 * Output the widget
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 * @return NULL
	 */
	function widget( $args, $instance ) {
		// If we are running US Grid loop already, return nothing
		global $us_grid_loop_running;
		if ( isset( $us_grid_loop_running ) AND $us_grid_loop_running ) {
			return NULL;
		}
		$us_grid_loop_running = TRUE;

		parent::before_widget( $args, $instance );

		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$output = $args['before_widget'];

		if ( $title ) {
			$output .= '<h3 class="widgettitle">' . $title . '</h3>';
		}

		// Preparing query
		$query_args = array(
			'post_type' => 'post',
		);

		// Providing proper post statuses
		$query_args['post_status'] = array( 'publish' => 'publish' );
		$query_args['post_status'] += (array) get_post_stati( array( 'public' => TRUE ) );
		$query_args['post_status'] = array_values( $query_args['post_status'] );

		if ( ! empty( $instance['ignore_sticky'] ) AND $instance['ignore_sticky'][0] ) {
			$query_args['ignore_sticky_posts'] = 1;
		}

		if ( ! empty( $instance['categories'] ) ) {
			$query_args['category_name'] = implode( ', ', $instance['categories'] );
		}

		// Setting posts order
		$orderby_translate = array(
			'date' => 'date',
			'date_asc' => 'date',
			'alpha' => 'title',
			'rand' => 'rand',
		);
		$order_translate = array(
			'date' => 'DESC',
			'date_asc' => 'ASC',
			'alpha' => 'ASC',
			'rand' => '',
		);
		$orderby = ( in_array( $instance['orderby'], array( 'date', 'date_asc', 'alpha', 'rand' ) ) ) ? $instance['orderby'] : 'date';
		if ( $orderby == 'rand' ) {
			$query_args['orderby'] = 'rand';
		} else {
			$query_args['orderby'] = array(
				$orderby_translate[$orderby] => $order_translate[$orderby],
			);
		}

		// Posts per page
		$instance['items'] = max( 0, intval( $instance['items'] ) );
		if ( $instance['items'] > 0 ) {
			$query_args['posts_per_page'] = $instance['items'];
		}

		// Exclude current post from grid
		if ( is_singular() ) {
			$current_ID = get_the_ID();
			if ( ! empty( $current_ID ) ) {
				$query_args['post__not_in'] = array( $current_ID );
			}
		}

		// Grid indexes, start from 1
		global $us_grid_index;
		$us_grid_index = isset( $us_grid_index ) ? ( $us_grid_index + 1 ) : 1;

		$template_vars = array(
			'query_args' => $query_args,
			'columns' => 1,
			'items_gap' => '1rem', // fixed value for Blog widget
			'items_layout' => $instance['layout'],
			'pagination' => 'none',
			'filter' => FALSE,
			'is_widget' => TRUE,
			'categories' => ( isset( $instance['categories'] ) AND is_array( $instance['categories'] ) ) ? implode( ', ', $instance['categories'] ) : NULL,
			'us_grid_index' => $us_grid_index,
		);

		ob_start();
		global $us_grid_loop_running;
		$us_grid_loop_running = TRUE;
		us_load_template( 'templates/us_grid/listing', $template_vars );
		$us_grid_loop_running = FALSE;
		$output .= ob_get_clean();

		$output .= $args['after_widget'];

		echo $output;

		$us_grid_loop_running = FALSE;
	}

	/**
	 * Output the settings update form.
	 *
	 * @param array $instance Current settings.
	 *
	 * @return string Form's output marker that could be used for further hooks
	 */
	public function form( $instance ) {
		$us_post_categories = array();
		$us_post_categories_raw = get_categories( "hierarchical=0" );
		foreach ( $us_post_categories_raw as $post_category_raw ) {
			$us_post_categories[$post_category_raw->name] = $post_category_raw->slug;
		}

		if ( ! empty( $us_post_categories ) ) {
			$this->config['params']['categories'] = array(
				'type' => 'checkbox',
				'heading' => __( 'Display Items of selected categories', 'us' ),
				'value' => $us_post_categories,
			);
		}

		return parent::form( $instance );
	}

}
