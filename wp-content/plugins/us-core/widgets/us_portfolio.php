<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Portfolio
 *
 * Class US_Widget_Portfolio
 */

class US_Widget_Portfolio extends US_Widget {

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
			'post_type' => 'us_portfolio',
		);

		// Providing proper post statuses
		$query_args['post_status'] = array( 'publish' => 'publish' );
		$query_args['post_status'] += (array) get_post_stati( array( 'public' => TRUE ) );
		$query_args['post_status'] = array_values( $query_args['post_status'] );

		if ( ! empty( $instance['categories'] ) ) {
			$query_args['us_portfolio_category'] = implode( ', ', $instance['categories'] );
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
		} else {
			$query_args['posts_per_page'] = -1;
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
			'columns' => $instance['columns'],
			'items_gap' => '1px', // fixed value for Portfolio widget
			'items_layout' => $instance['layout'],
			'pagination' => 'none',
			'overriding_link' => 'post',
			'filter' => FALSE,
			'is_widget' => TRUE,
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
		$us_portfolio_categories = array();
		$us_portfolio_categories_raw = get_categories(
			array(
				'taxonomy' => 'us_portfolio_category',
				'hierarchical' => 0,
			)
		);
		if ( $us_portfolio_categories_raw ) {
			foreach ( $us_portfolio_categories_raw as $portfolio_category_raw ) {
				if ( is_object( $portfolio_category_raw ) ) {
					$us_portfolio_categories[$portfolio_category_raw->name] = $portfolio_category_raw->slug;
				}
			}
		}

		if ( ! empty( $us_portfolio_categories ) ) {
			$this->config['params']['categories'] = array(
				'type' => 'checkbox',
				'heading' => __( 'Display Items of selected categories', 'us' ),
				'value' => $us_portfolio_categories,
			);
		}

		return parent::form( $instance );
	}

}
