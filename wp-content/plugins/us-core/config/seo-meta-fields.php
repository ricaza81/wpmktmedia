<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme SEO Meta Fields
 */

return array(
	'us_meta_title' => array(
		'title' => us_translate( 'Page title' ),
		'title_pos' => 'side',
		'description' => __( 'Leave blank to use the default.', 'us' ),
		'type' => 'text',
		'std' => '',
	),
	'us_meta_description' => array(
		'title' => us_translate( 'Description' ),
		'title_pos' => 'side',
		'description' => '<a href="https://support.google.com/webmasters/answer/35624" target="_blank" rel="noopener">' . __( 'Learn more', 'us' ). '</a>',
		'type' => 'textarea',
		'std' => '',
	),
	'us_meta_robots' => array(
		'title' => 'Robots',
		'title_pos' => 'side',
		'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">noindex</span>, <span class="usof-example">nofollow</span>, <span class="usof-example">none</span>. <a href="https://developers.google.com/search/reference/robots_meta_tag#directives" target="_blank" rel="noopener">' . __( 'Learn more', 'us' ). '</a>',
		'type' => 'text',
		'std' => '',
	),
);
