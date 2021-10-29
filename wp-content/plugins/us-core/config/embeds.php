<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Defines a proper embeddable rules for vc_video (in future may be used for custom usages as well)
 *
 * Note: in 'html' field '<id>' entry will be replaced by match_index's entrance from regex matches.
 *
 * @filter us_config_embeds
 */

return array(
	'youtube' => array(
		// Source: http://stackoverflow.com/a/10524505
		'type' => 'video',
		'regex' => '~^(?:https?://)?(?:www\.|m\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$~x',
		'match_index' => 1,
		'url_params' => array(
			'autoplay' => 0,
			'controls' => 1
		),
		'html' => '<iframe src="//www.youtube.com/embed/<id><url_params>" allowfullscreen="1"></iframe>',
	),
	'vimeo' => array(
		'type' => 'video',
		'regex' => '/^http(?:s)?:\/\/(?:.*?)\.?vimeo\.com\/(\d+).*$/i',
		'match_index' => 1,
		'url_params' => array(
			'autopause' => 0,
			'autoplay' => 0,
			'color' => '00adef',
			'portrait' => 0
		),
		'html' => '<iframe src="//player.vimeo.com/video/<id><url_params>" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',
	),
);
