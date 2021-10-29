<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The Events Calendar Support
 *
 * @link https://theeventscalendar.com/
 */

if ( ! class_exists( 'Tribe__Events__Query' ) ) {
	return;
}

if ( ! function_exists( 'us_enqueue_the_events_calendar_styles' ) ) {
	/**
	 * Enqueue css file
	 */
	function us_enqueue_the_events_calendar_styles() {
		if ( defined( 'US_DEV' ) OR ! us_get_option( 'optimize_assets', 0 ) ) {
			global $us_template_directory_uri;
			$min_ext = defined( 'US_DEV' ) ? '' : '.min';
			wp_register_style( 'us-tribe-events', $us_template_directory_uri . '/common/css/plugins/tribe-events' . $min_ext . '.css', array(), US_THEMEVERSION, 'all' );
			wp_enqueue_style( 'us-tribe-events' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'us_enqueue_the_events_calendar_styles', 14 );
}

if ( ! function_exists( 'us_tribe_events_us_grid_before_custom_query' ) ) {
	/**
	 * Remove The Events Calendar plugin filter
	 *
	 * @param array $vars
	 */
	function us_tribe_events_us_grid_before_custom_query( $vars ) {
		add_action( 'tribe_events_pre_get_posts', 'us_delete_events_calendar_filter', 10 );

		// Show past events if set in Grid settings
		if ( ! empty( $vars['events_calendar_show_past'] ) ) {
			add_action( 'pre_get_posts', 'us_the_events_calendar_display_past', /* 50 uses the plugin */ 49 );
		} else {
			add_action( 'pre_get_posts', 'us_the_events_calendar_dont_display_past', /* 50 uses the plugin */ 49 );
		}

		// Removing tribe events' own ordering for queries with events categories selected
		add_filter( 'tribe_query_can_inject_date_field', '__return_false' );

		// Preventing custom queries from messing main events query
		add_filter( 'tribe_events_views_v2_should_hijack_page_template', 'us_the_events_calendar_return_true_for_hijack', 10, 2 );
	}
	add_action( 'us_grid_before_custom_query', 'us_tribe_events_us_grid_before_custom_query', 1, 1 );
}

if ( ! function_exists( 'us_tribe_events_us_grid_after_custom_query' ) ) {
	/**
	 * Deleting actions after executing a custom request
	 */
	function us_tribe_events_us_grid_after_custom_query() {
		remove_action( 'pre_get_posts', 'us_the_events_calendar_display_past', 49 );
		remove_action( 'pre_get_posts', 'us_the_events_calendar_dont_display_past', 49 );
		remove_filter( 'tribe_query_can_inject_date_field', '__return_false' );
	}
	add_action( 'us_grid_after_custom_query', 'us_tribe_events_us_grid_after_custom_query', 1 );
}

if ( ! function_exists( 'us_delete_events_calendar_filter' ) ) {
	/**
	 * TEC filter overwrite grid query and posts displaying incorrect, remove filter for prevent this
	 */
	function us_delete_events_calendar_filter() {
		remove_filter( 'posts_orderby', array( 'Tribe__Events__Query', 'posts_orderby' ), 10 );
	}
}

if ( ! function_exists( 'us_pre_get_posts_for_events_calendar' ) ) {
	/**
	 * Adding search parameter for events calendar
	 *
	 * @param WP_Query $wp_query
	 */
	function us_pre_get_posts_for_events_calendar( $wp_query ) {
		if (
			is_search()
			AND $wp_query->get( 'tribe_suppress_query_filters' )
			AND ! $wp_query->get( 's' )
		) {
			$wp_query->set( 's', get_query_var('s') );
		}
	}
	add_action( 'pre_get_posts', 'us_pre_get_posts_for_events_calendar', 100, 1 );
}

if ( ! function_exists( 'us_the_events_calendar_display_past' ) ) {
	/**
	 * @param WP_Query $query
	 */
	function us_the_events_calendar_display_past( $query ) {
		if ( $query->tribe_is_event !== TRUE ) {
			return;
		}
		$query->tribe_is_past = TRUE;
		$query->set( 'tribe_suppress_query_filters', FALSE /* FALSE - apply inline filters */ );
		// When eventDisplay property is set as 'custom', events calendar plugin will NOT add default dates to the events query
		// So we are using it here to get all events including past ones
		$query->set( 'eventDisplay', 'custom' );
	}
}

if ( ! function_exists( 'us_the_events_calendar_dont_display_past' ) ) {
	/**
	 * @param WP_Query $query
	 */
	function us_the_events_calendar_dont_display_past( $query ) {
		if ( $query->tribe_is_event !== TRUE ) {
			return;
		}
		$query->tribe_is_past = FALSE;
		$query->set( 'tribe_suppress_query_filters', FALSE /* FALSE - apply inline filters */);
		// When eventDisplay property is set as 'list', events calendar plugin will add default dates to the events query
		// So we are setting `eventDisplay=list` here to get upcoming events
		$query->set( 'eventDisplay', 'list' );
	}
}

if ( ! function_exists( 'us_the_events_calendar_return_true_for_hijack' ) ) {
	/*
	 * We will add this function for 'tribe_events_views_v2_should_hijack_page_template' filter
	 * during output of grids with custom queries.
	 * This way we will ensure that events calendar will not fire its one-time actions in wrong place.
	 * And so all actions needed to output templates for events archive will run on correct query.
	 *
	 * @param bool $should_hijack
	 * @param WP_Query $query
	 * @return bool
	 */
	function us_the_events_calendar_return_true_for_hijack ( $should_hijack, $query ) {
		return TRUE;
	}
}
