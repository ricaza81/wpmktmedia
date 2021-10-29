<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: vc_column_inner
 *
 * @return array
 */
return array(
	'title' => __( 'Inner Column', 'us' ),
	'category' => __( 'Containers', 'us' ),
	'is_container' => TRUE,
	'hide_on_adding_list' => TRUE,
	'allowed_container_element' => TRUE,
	'as_child' => array(
		'only' => 'vc_row_inner',
	),

	// Import settings from vc_column
	'params' => us_config( 'elements/vc_column.params', array() ),
	'vc_remove_params' => us_config( 'elements/vc_column.vc_remove_params', array() ),
	'fallback_params' => us_config( 'elements/vc_column.fallback_params', array() )
);
