<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

$design_options = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Product ordering', 'us' ),
	'category' => __( 'Post Elements', 'us' ),
	'params' => $design_options,
	'show_settings_on_create' => FALSE,
);
