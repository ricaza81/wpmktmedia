<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's WordPress widgets
 *
 * @filter us_config_widgets
 */

global $pagenow;
$us_is_widget_page = ( wp_doing_ajax() OR $pagenow == 'widgets.php' );
$grid_templates_config = us_config( 'grid-templates', array(), TRUE );

// Grid Layout
$grid_layout_option_key = __( 'Grid Layouts', 'us' );
$us_grid_layout_list = array(
	$grid_layout_option_key => ( $us_is_widget_page
		? us_get_posts_titles_for( 'us_grid_layout' )
		: array()
	),
);

// Grid templates
$current_option_key = '';
foreach ( $grid_templates_config as $template_name => $template ) {
	if ( ! empty( $template['group'] ) AND $current_option_key != $template['group'] ) {
		$current_option_key = $template['group'];
		$us_grid_layout_list[ $current_option_key ] = array();
	}
	$us_grid_layout_list[ $current_option_key ][ $template_name ] = $template['title'];
}

// Social Links fields
$old_social_links = array(
	'email' => 'Email',
	'facebook' => 'Facebook',
	'twitter' => 'Twitter',
	'google' => 'Google',
	'linkedin' => 'LinkedIn',
	'youtube' => 'YouTube',
	'vimeo' => 'Vimeo',
	'flickr' => 'Flickr',
	'behance' => 'Behance',
	'instagram' => 'Instagram',
	'xing' => 'Xing',
	'pinterest' => 'Pinterest',
	'skype' => 'Skype',
	'whatsapp' => 'WhatsApp',
	'dribbble' => 'Dribbble',
	'vk' => 'Vkontakte',
	'tumblr' => 'Tumblr',
	'soundcloud' => 'SoundCloud',
	'twitch' => 'Twitch',
	'yelp' => 'Yelp',
	'deviantart' => 'DeviantArt',
	'foursquare' => 'Foursquare',
	'github' => 'GitHub',
	'odnoklassniki' => 'Odnoklassniki',
	's500px' => '500px',
	'houzz' => 'Houzz',
	'medium' => 'Medium',
	'tripadvisor' => 'Tripadvisor',
	'rss' => 'RSS',
	'discord' => 'Discord',
	'imdb' => 'IMDb',
	'reddit' => 'Reddit',
	'telegram' => 'Telegram',
	'wechat' => 'WeChat',
);
$social_links_config = array();
foreach ( $old_social_links as $name => $title ) {
	$social_links_config[$name] = array(
		'type' => 'text',
		'heading' => $title,
		'std' => '',
	);
}

return array(

	// Contact Info
	'us_contacts' => array(
		'class' => 'US_Widget_Contacts',
		'name' => us_translate( 'Contact Info' ),
		'description' => us_translate( 'Contact Info' ),
		'params' => us_set_params_weight(
			array(
				'title' => array(
					'type' => 'text',
					'heading' => us_translate( 'Title' ),
					'std' => '',
				),
				'address' => array(
					'type' => 'textarea',
					'heading' => __( 'Address', 'us' ),
					'std' => '',
				),
				'phone' => array(
					'type' => 'textarea',
					'heading' => __( 'Phone', 'us' ),
					'std' => '',
				),
				'fax' => array(
					'type' => 'text',
					'heading' => __( 'Mobiles', 'us' ),
					'std' => '',
				),
				'email' => array(
					'type' => 'text',
					'heading' => us_translate( 'Email' ),
					'std' => '',
				),
			)
		),
	),

	// Login
	'us_login' => array(
		'class' => 'US_Widget_Login',
		'name' => __( 'Login', 'us' ),
		'description' => __( 'Login Form', 'us' ),
		'params' => us_set_params_weight(
			array(
				'title' => array(
					'type' => 'text',
					'heading' => us_translate( 'Title' ),
					'std' => '',
				),
				'register' => array(
					'type' => 'text',
					'heading' => __( 'Register URL', 'us' ),
					'std' => '',
				),
				'lostpass' => array(
					'type' => 'text',
					'heading' => __( 'Lost Password URL', 'us' ),
					'std' => '',
				),
				'login_redirect' => array(
					'type' => 'text',
					'heading' => __( 'Login Redirect URL', 'us' ),
					'std' => '',
				),
				'logout_redirect' => array(
					'type' => 'text',
					'heading' => __( 'Logout Redirect URL', 'us' ),
					'std' => '',
				),
			)
		),
	),

	// Portfolio
	'us_portfolio' => array(
		'class' => 'US_Widget_Portfolio',
		'name' => __( 'Portfolio', 'us' ),
		'description' => __( 'Portfolio', 'us' ),
		'params' => us_set_params_weight(
			array(
				'title' => array(
					'type' => 'text',
					'heading' => us_translate( 'Title' ),
					'std' => '',
				),
				'layout' => array(
					'type' => 'dropdown',
					'heading' => __( 'Grid Layout', 'us' ),
					'value' => $us_grid_layout_list,
					'std' => 'portfolio_compact',
				),
				'orderby' => array(
					'type' => 'dropdown',
					'heading' => us_translate( 'Order' ),
					'value' => array(
						'date' => __( 'Date of creation', 'us' ),
						'date_asc' => __( 'Date of creation', 'us' ) . __( 'Invert order', 'us' ),
						'modified' => __( 'Date of update', 'us' ),
						'modified_asc' => __( 'Date of update', 'us' ) . __( 'Invert order', 'us' ),
						'alpha' => us_translate( 'Title' ),
						'rand' => us_translate( 'Random' ),
					),
					'std' => 'date',
				),
				'columns' => array(
					'type' => 'dropdown',
					'heading' => us_translate( 'Columns' ),
					'value' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
					),
					'std' => '3',
				),
				'items' => array(
					'type' => 'text',
					'heading' => __( 'Items Quantity', 'us' ),
					'std' => '6',
				),
			)
		),
	),

	// Blog
	'us_blog' => array(
		'class' => 'US_Widget_Blog',
		'name' => us_translate( 'Blog' ),
		'description' => us_translate( 'Blog' ),
		'params' => us_set_params_weight(
			array(
				'title' => array(
					'type' => 'text',
					'heading' => us_translate( 'Title' ),
					'std' => '',
				),
				'layout' => array(
					'type' => 'dropdown',
					'heading' => __( 'Grid Layout', 'us' ),
					'value' => $us_grid_layout_list,
					'std' => 'blog_1',
				),
				'orderby' => array(
					'type' => 'dropdown',
					'heading' => us_translate( 'Order' ),
					'value' => array(
						'date' => __( 'By date of creation (newer first)', 'us' ),
						'date_asc' => __( 'By date of creation (older first)', 'us' ),
						'modified' => __( 'By date of update (newer first)', 'us' ),
						'modified_asc' => __( 'By date of update (older first)', 'us' ),
						'alpha' => __( 'By title', 'us' ),
						'rand' => us_translate( 'Random' ),
					),
					'std' => 'date',
				),
				'items' => array(
					'type' => 'text',
					'heading' => __( 'Items Quantity', 'us' ),
					'std' => '3',
				),
				'ignore_sticky' => array(
					'type' => 'checkbox',
					'heading' => '',
					'value' => array(
						__( 'Ignore sticky posts', 'us' ) => TRUE,
					),
					'std' => array(),
				),
			)
		),
	),

	// Social Links
	'us_socials' => array(
		'class' => 'US_Widget_Socials',
		'name' => __( 'Social Links', 'us' ),
		'description' => __( 'Social Links', 'us' ),
		'params' => us_set_params_weight(
			array(
				'title' => array(
					'type' => 'text',
					'heading' => us_translate( 'Title' ),
					'std' => '',
				),
				'size' => array(
					'type' => 'text',
					'heading' => us_translate( 'Size' ),
					'std' => '20px',
				),
				'style' => array(
					'type' => 'dropdown',
					'heading' => __( 'Icons Style', 'us' ),
					'value' => array(
						'default' => __( 'Simple', 'us' ),
						'outlined' => __( 'With outline', 'us' ),
						'solid' => __( 'With light background', 'us' ),
						'colored' => __( 'With colored background', 'us' ),
					),
					'std' => 'default',
				),
				'color' => array(
					'type' => 'dropdown',
					'heading' => __( 'Icons Color', 'us' ),
					'value' => array(
						'brand' => __( 'Default brands colors', 'us' ),
						'text' => __( 'Text (theme color)', 'us' ),
						'link' => __( 'Link (theme color)', 'us' ),
					),
					'std' => 'brand',
				),
				'shape' => array(
					'type' => 'dropdown',
					'heading' => __( 'Icons Shape', 'us' ),
					'value' => array(
						'square' => __( 'Square', 'us' ),
						'rounded' => __( 'Rounded Square', 'us' ),
						'circle' => __( 'Circle', 'us' ),
					),
					'std' => 'square',
				),
				'hover' => array(
					'type' => 'dropdown',
					'heading' => __( 'Hover Style', 'us' ),
					'value' => array(
						'fade' => __( 'Fade', 'us' ),
						'slide' => __( 'Slide', 'us' ),
						'none' => us_translate( 'None' ),
					),
					'std' => 'fade',
				),
			),
			$social_links_config,
			array(
				'custom_link' => array(
					'type' => 'text',
					'heading' => __( 'Custom Link', 'us' ),
					'std' => '',
				),
				'custom_title' => array(
					'type' => 'text',
					'heading' => __( 'Custom Link Title', 'us' ),
					'std' => '',
				),
				'custom_icon' => array(
					'type' => 'text',
					'heading' => __( 'Custom Link Icon', 'us' ),
					'std' => '',
				),
				'custom_color' => array(
					'type' => 'text',
					'heading' => __( 'Custom Link Color', 'us' ),
					'std' => '#999',
				),
			)
		),
	),
);
