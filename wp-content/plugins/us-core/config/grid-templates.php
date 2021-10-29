<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Grid Templates
 */

$icon_comments = ( US_THEMENAME == 'Zephyr' ) ? 'material|question_answer' : 'far|comments';
$icon_tags = ( US_THEMENAME == 'Zephyr' ) ? 'material|turned_in' : 'far|tags';
$icon_quote = ( US_THEMENAME == 'Zephyr' ) ? 'material|format_quote' : 'far|quote-left';

return array(

/* Blog =========================================================================== */

'blog_1' => array(
	'title' => 'Image & Title',
	'group' => __( 'Blog Templates', 'us' ),
	'data' => array(
		'post_image:1' => array(
			'thumbnail_size' => 'us_600_600_crop',
			'css' => array(
				'default' => array(
					'margin-bottom' => '0.5rem',
				)
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1rem',
				)
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'post_title:1',
			),
		),
	),
),

'blog_4' => array(
	'title' => 'Top Image (date, comments)',
	'data' => array(
		'post_image:1' => array(
			'thumbnail_size' => 'us_600_600_crop',
			'css' => array(
				'default' => array(
					'margin-bottom' => '0.6rem',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1.2rem',
					'margin-bottom' => '0.3rem',
				),
			),
		),
		'hwrapper:1' => array(
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '14px',
					'line-height' => '1.6',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
		),
		'post_comments:1' => array(
			'number' => TRUE,
			'icon' => $icon_comments,
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'post_title:1',
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_date:1',
				'post_comments:1',
			),
		),
	),
),

'blog_6' => array(
	'title' => 'Corner Tile (category, date)',
	'cols' => '2',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'scale_hover' => '1.1',
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1.4rem',
					'color' => 'inherit',
				),
				'mobiles' => array(
					'font-size' => '1.2rem',
				),
			),
		),
		'post_taxonomy:1' => array(
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-size' => '14px',
					'font-weight' => '700',
					'text-transform' => 'uppercase',
					'margin-left' => is_rtl() ? '0.6rem' : '',
					'margin-right' => is_rtl() ? '' : '0.6rem',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
			'css' => array(
				'default' => array(
					'font-size' => '14px',
					'color' => '_content_faded',
				),
			),
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'background-color' => '_content_bg',
					'color' => '_content_heading',
					'position' => 'absolute',
					'left' => is_rtl() ? '2rem' : '0',
					'right' => is_rtl() ? '0' : '2rem',
					'bottom' => '0',
					'padding-top' => '0.5rem',
					'padding-left' => is_rtl() ? '2rem' : '',
					'padding-right' => is_rtl() ? '' : '2rem',
				),
			),
		),
		'hwrapper:1' => array(
			'css' => array(
				'default' => array(
					'margin-bottom' => '0',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'hwrapper:1',
				'post_title:1',
			),
			'hwrapper:1' => array(
				'post_taxonomy:1',
				'post_date:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
			'ratio' => '4x3',
		),
	),
),

'blog_5' => array(
	'title' => 'Side Image (date, comments)',
	'data' => array(
		'post_image:1' => array(
			'placeholder' => TRUE,
			'thumbnail_size' => 'thumbnail',
			'has_ratio' => TRUE,
			'css' => array(
				'default' => array(
					'width' => '30%',
					'margin-right' => is_rtl() ? '' : '5%',
					'margin-left' => is_rtl() ? '5%' : '',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1.2rem',
					'margin-bottom' => '0.3rem',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
		),
		'post_comments:1' => array(
			'number' => TRUE,
			'icon' => $icon_comments,
		),
		'hwrapper:1' => array(),
		'hwrapper:2' => array(
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '14px',
					'line-height' => '1.6',
				),
			),
		),
		'vwrapper:1' => array(),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'hwrapper:2',
			),
			'hwrapper:2' => array(
				'post_date:1',
				'post_comments:1',
			),
		),
	),
),

'blog_8' => array(
	'title' => 'Side Image overlapped by Title (category, date, excerpt)',
	'cols' => '2',
	'data' => array(
		'hwrapper:1' => array(),
		'post_image:1' => array(
			'placeholder' => TRUE,
			'thumbnail_size' => 'us_600_600_crop',
			'has_ratio' => TRUE,
			'css' => array(
				'default' => array(
					'width' => '50%',
				),
			),
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'width' => '50%',
					'margin-left' => is_rtl() ? '0' : '',
					'margin-right' => is_rtl() ? '' : '0',
				),
			),
		),
		'hwrapper:2' => array(
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'margin-bottom' => '0',
				),
			),
		),
		'post_taxonomy:1' => array(
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-size' => '14px',
					'line-height' => '1.4',
					'font-weight' => '700',
					'text-transform' => 'uppercase',
					'margin-left' => is_rtl() ? '0.8rem' : '',
					'margin-right' => is_rtl() ? '' : '0.8rem',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
			'hide_below' => '600px',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '14px',
					'line-height' => '1.4',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'color' => '_content_heading',
					'background-color' => '_content_bg',
					'font-size' => '1.8rem',
					'line-height' => '1.2',
					'margin-left' => is_rtl() ? '-35%' : '',
					'margin-right' => is_rtl() ? '' : '-35%',
					'margin-bottom' => '0',
					'padding-top' => '1rem',
					'padding-left' => is_rtl() ? '1.2rem' : '',
					'padding-right' => is_rtl() ? '' : '1.2rem',
					'padding-bottom' => '1rem',
				),
				'mobiles' => array(
					'font-size' => '1.2rem',
					'margin-left' => is_rtl() ? '0' : '',
					'margin-right' => is_rtl() ? '' : '0',
					'padding-bottom' => '0',
				),
			),
		),
		'post_content:1' => array(
			'length' => '24',
			'css' => array(
				'default' => array(
					'font-size' => '0.9rem',
					'line-height' => '1.6',
					'padding-left' => is_rtl() ? '1.5rem' : '',
					'padding-right' => is_rtl() ? '' : '1.5rem',
				),
			),
			'hide_below' => '600px',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'vwrapper:1',
				'post_image:1',
			),
			'vwrapper:1' => array(
				'hwrapper:2',
				'post_title:1',
				'post_content:1',
			),
			'hwrapper:2' => array(
				'post_taxonomy:1',
				'post_date:1',
			),
		),
		'options' => array(
			'overflow' => TRUE,
		),
	),
),

'blog_side_image' => array(
	'title' => 'Side Circle Image (date, comments, excerpt)',
	'cols' => '2',
	'data' => array(
		'hwrapper:1' => array(
			'wrap' => TRUE,
		),
		'post_image:1' => array(
			'placeholder' => TRUE,
			'circle' => TRUE,
			'thumbnail_size' => 'us_600_600_crop',
			'has_ratio' => TRUE,
			'css' => array(
				'default' => array(
					'width' => '30%',
					'margin-right' => is_rtl() ? '0' : '5%',
					'margin-left' => is_rtl() ? '5%' : '0',
				),
				'mobiles' => array(
					'width' => '100%',
					'margin-right' => '0',
					'margin-left' => '0',
					'margin-bottom' => '5%',
				),
			),
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'width' => '65%',
				),
				'mobiles' => array(
					'width' => '100%',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1.4rem',
				),
			),
		),
		'hwrapper:2' => array(
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
				),
			),
		),
		'post_content:1' => array(
		),
		'post_date:1' => array(
			'format' => 'smart',
		),
		'post_comments:1' => array(
			'icon' => $icon_comments,
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'hwrapper:2',
				'post_content:1',
			),
			'hwrapper:2' => array(
				'post_date:1',
				'post_comments:1',
			),
		),
	),
),

'blog_14' => array(
	'title' => 'Side Float Image Left (date, excerpt)',
	'cols' => '1',
	'items_gap' => '3rem',
	'data' => array(
		'hwrapper:1' => array(
			'valign' => 'center',
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_text',
					'background-color' => '_content_bg_alt',
					'margin-left' => is_rtl() ? '' : '10%',
					'margin-right' => is_rtl() ? '10%' : '',
					'padding-top' => '6%',
					'padding-bottom' => '6%',
				),
				'mobiles' => array(
					'margin-left' => '0',
					'margin-right' => '0',
					'padding-top' => '0',
					'padding-bottom' => '0',
				),
			),
		),
		'post_image:1' => array(
			'media_preview' => TRUE,
			'css' => array(
				'default' => array(
					'width' => '50%',
					'margin-left' => is_rtl() ? '0' : '-11%',
					'margin-right' => is_rtl() ? '-11%' : '0',
				),
				'mobiles' => array(
					'width' => '100%',
					'margin-left' => '0',
					'margin-right' => '0',
				),
			),
			'hide_below' => '480px',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'width' => '48%',
					'margin-left' => is_rtl() ? '' : '7%',
					'margin-right' => is_rtl() ? '7%' : '',
				),
				'mobiles' => array(
					'width' => '86%',
					'margin-top' => '5%',
					'margin-left' => '7%',
					'margin-right' => '7%',
					'margin-bottom' => '7%',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
			'css' => array(
				'default' => array(
					'font-size' => '0.9rem',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'line-height' => '1.2',
				),
			),
		),
		'post_content:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '0.9rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_date:1',
				'post_title:1',
				'post_content:1',
			),
		),
	),
),

'blog_13' => array(
	'title' => 'Side Float Image Right (date, excerpt)',
	'cols' => '1',
	'items_gap' => '3rem',
	'data' => array(
		'hwrapper:1' => array(
			'valign' => 'center',
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_text',
					'background-color' => '_content_bg_alt',
					'margin-left' => is_rtl() ? '10%' : '',
					'margin-right' => is_rtl() ? '' : '10%',
					'padding-top' => '6%',
					'padding-bottom' => '6%',
				),
				'mobiles' => array(
					'margin-left' => '0',
					'margin-right' => '0',
					'padding-top' => '0',
					'padding-bottom' => '0',
				),
			),
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'width' => '48%',
					'margin-left' => is_rtl() ? '0' : '6%',
					'margin-right' => is_rtl() ? '6%' : '0',
				),
				'mobiles' => array(
					'width' => '86%',
					'margin-top' => '7%',
					'margin-left' => '7%',
					'margin-right' => '7%',
					'margin-bottom' => '5%',
				),
			),
		),
		'post_image:1' => array(
			'media_preview' => TRUE,
			'css' => array(
				'default' => array(
					'width' => '50%',
					'margin-left' => is_rtl() ? '-11%' : '7%',
					'margin-right' => is_rtl() ? '7%' : '-11%',
				),
				'mobiles' => array(
					'width' => '100%',
					'margin-left' => '0',
					'margin-right' => '0',
				),
			),
			'hide_below' => '480px',
		),
		'post_date:1' => array(
			'format' => 'smart',
			'css' => array(
				'default' => array(
					'font-size' => '0.9rem',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'line-height' => '1.2',
				),
			),
		),
		'post_content:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '0.9rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'vwrapper:1',
				'post_image:1',
			),
			'vwrapper:1' => array(
				'post_date:1',
				'post_title:1',
				'post_content:1',
			),
		),
	),
),

'tile_21_left' => array(
	'title' => 'Side Image Left 2:1',
	'cols' => '2',
	'items_gap' => '5px',
	'data' => array(
		'post_image:1' => array(
			'placeholder' => TRUE,
			'thumbnail_size' => 'us_600_600_crop',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '50%',
					'bottom' => '0',
					'left' => '0',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.3rem',
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '50%',
					'padding-top' => '8%',
					'padding-right' => '8%',
					'padding-bottom' => '8%',
					'padding-left' => '8%',
				),
				'mobiles' => array(
					'font-size' => '1rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_title:1',
				'post_image:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
			'ratio' => 'custom',
			'ratio_width' => '2',
			'ratio_height' => '1',
			'color_bg' => '_content_bg_alt',
			'color_text' => '_content_heading',
		),
	),
),

'tile_21_right' => array(
	'title' => 'Side Image Right 2:1',
	'cols' => '2',
	'items_gap' => '5px',
	'data' => array(
		'post_image:1' => array(
			'placeholder' => TRUE,
			'thumbnail_size' => 'us_600_600_crop',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '50%',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.3rem',
					'position' => 'absolute',
					'top' => '0',
					'right' => '50%',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '8%',
					'padding-right' => '8%',
					'padding-bottom' => '8%',
					'padding-left' => '8%',
				),
				'mobiles' => array(
					'font-size' => '1rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_title:1',
				'post_image:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
			'ratio' => 'custom',
			'ratio_width' => '2',
			'ratio_height' => '1',
			'color_bg' => '_content_bg_alt',
			'color_text' => '_content_heading',
		),
	),
),

'blog_9' => array(
	'title' => 'Card White (category, date)',
	'items_gap' => '5px',
	'data' => array(
		'post_image:1' => array(
			'thumbnail_size' => 'us_600_600_crop',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'padding-top' => '0.8rem',
					'padding-right' => '1.5rem',
					'padding-bottom' => '1.5rem',
					'padding-left' => '1.5rem',
				),
			),
		),
		'hwrapper:1' => array(
			'css' => array(
				'default' => array(
					'margin-bottom' => '0.3rem',
				),
			),
		),
		'post_taxonomy:1' => array(
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-size' => '14px',
					'font-weight' => '700',
					'text-transform' => 'uppercase',
					'margin-left' => is_rtl() ? '0.6rem' : '',
					'margin-right' => is_rtl() ? '' : '0.6rem',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '14px',
					'margin-bottom' => '0.3rem',
				),
			),

		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-weight' => '700',
					'font-size' => '1.4rem',
				),
				'mobiles' => array(
					'font-size' => '1.2rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'hwrapper:1',
				'post_title:1',
			),
			'hwrapper:1' => array(
				'post_taxonomy:1',
				'post_date:1',
			),
		),
		'options' => array(
			'overflow' => TRUE,
			'color_bg' => '_content_bg',
			'color_text' => '_content_heading',
			'border_radius' => us_get_option( 'rounded_corners' ) ? '0.3rem' : '',
			'box_shadow' => '0.3rem',
			'box_shadow_hover' => '1.2rem',
		),
	),
),

'blog_cards' => array(
	'title' => 'Card White Full',
	'items_gap' => '5px',
	'data' => array(
		'post_image:1' => array(
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1.4rem',
					'font-weight' => '700',
				),
				'mobiles' => array(
					'font-size' => '1.2rem',
				),
			),
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'padding-top' => '9%',
					'padding-right' => '11%',
					'padding-bottom' => '11%',
					'padding-left' => '11%',
				),
			),
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'category',
			'style' => 'badge',
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'text-transform' => 'uppercase',
					'font-size' => '10px',
					'position' => 'absolute',
					'top' => '1.2rem',
					'right' => '1.2rem',
					'left' => '1.2rem',
				),
			),
		),
		'hwrapper:1' => array(
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
		),
		'post_comments:1' => array(
			'number' => TRUE,
			'icon' => $icon_comments,
		),
		'post_content:1' => array(
			'length' => '20',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'post_taxonomy:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'hwrapper:1',
				'post_content:1',
			),
			'hwrapper:1' => array(
				'post_date:1',
				'post_comments:1',
			),
		),
		'options' => array(
			'overflow' => TRUE,
			'color_bg' => '_content_bg',
			'color_text' => '_content_text',
			'border_radius' => us_get_option( 'rounded_corners' ) ? '0.3rem' : '',
			'box_shadow' => '0.3rem',
			'box_shadow_hover' => '1rem',
		),
	),
),

'blog_10' => array(
	'title' => 'Card Gradient (category)',
	'items_gap' => '5px',
	'data' => array(
		'post_image:1' => array(
			'placeholder' => TRUE,
			'thumbnail_size' => 'us_600_600_crop',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'background-color' => 'linear-gradient(transparent, rgba(30,30,30,0.8))',
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '15%',
					'padding-right' => '7%',
					'padding-bottom' => '7%',
					'padding-left' => '7%',
				),
			),
		),
		'post_taxonomy:1' => array(
			'style' => 'badge',
			'css' => array(
				'default' => array(
					'font-size' => '10px',
					'font-weight' => '700',
					'text-transform' => 'uppercase',
					'margin-bottom' => '0.5rem',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '1.4rem',
					'font-weight' => '700',
				),
				'mobiles' => array(
					'font-size' => '1.2rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_taxonomy:1',
				'post_title:1',
			),
		),
		'options' => array(
			'overflow' => TRUE,
			'color_bg' => '#333',
			'border_radius' => us_get_option( 'rounded_corners' ) ? '0.3rem' : '',
			'box_shadow' => '0.3rem',
			'box_shadow_hover' => '1.2rem',
		),
	),
),

'blog_tiles' => array(
	'title' => 'Tile Gradient (category, date, comments)',
	'items_gap' => '3px',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'placeholder' => TRUE,
			'thumbnail_size' => 'us_600_600_crop',
			'hover' => TRUE,
			'scale_hover' => '1.2',
		),
		'vwrapper:1' => array(
			'valign' => 'bottom',
			'css' => array(
				'default' => array(
					'background-color' => 'linear-gradient(transparent, rgba(30,30,30,0.8))',
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '5rem',
					'padding-right' => '8%',
					'padding-bottom' => '1.5rem',
					'padding-left' => '8%',
				),
			),
			'opacity' => '0',
			'transition_duration' => '0.45s',
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '1.2rem',
					'font-weight' => '700',
					'margin-bottom' => '0.3rem',
				),
			),
		),
		'hwrapper:1' => array(
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '0.9rem',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
		),
		'post_comments:1' => array(
			'number' => TRUE,
			'icon' => $icon_comments,
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'category',
			'style' => 'badge',
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'text-transform' => 'uppercase',
					'font-size' => '10px',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_taxonomy:1',
				'post_title:1',
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_date:1',
				'post_comments:1',
			),
		),
		'options' => array(
			'overflow' => TRUE,
		),
	),
),

'blog_11' => array(
	'title' => 'Title First (category, date, comments, excerpt)',
	'cols' => '2',
	'data' => array(
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1.4rem',
				),
				'mobiles' => array(
					'font-size' => '1.2rem',
				),
			),
		),
		'hwrapper:1' => array(
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '14px',
				),
			),
		),
		'post_taxonomy:1' => array(
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'text-transform' => 'uppercase',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
		),
		'post_comments:1' => array(
			'number' => TRUE,
			'icon' => $icon_comments,
		),
		'post_image:1' => array(
			'media_preview' => TRUE,
			'css' => array(
				'default' => array(
					'margin-top' => '0.6rem',
				),
			),
		),
		'post_content:1' => array(
			'length' => '50',
			'css' => array(
				'default' => array(
					'font-size' => '0.9rem',
					'line-height' => '1.7',
					'margin-top' => '0.8rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_title:1',
				'hwrapper:1',
				'post_image:1',
				'post_content:1',
			),
			'hwrapper:1' => array(
				'post_taxonomy:1',
				'post_date:1',
				'post_comments:1',
			),
		),
	),
),

'blog_12' => array(
	'title' => 'Full Content (date, comments, tags)',
	'cols' => '1',
	'items_gap' => '4rem',
	'data' => array(
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '3rem',
					'line-height' => '1.1',
					'margin-bottom' => '0.2rem',
				),
				'mobiles' => array(
					'font-size' => '1.8rem',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
				),
			),
		),
		'post_content:1' => array(
			'type' => 'full_content',
			'css' => array(
				'default' => array(
					'margin-top' => '0.5rem',
					'margin-bottom' => '1rem',
				),
			),
		),
		'hwrapper:1' => array(
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
				),
			),
		),
		'post_comments:1' => array(
			'color_link' => FALSE,
			'icon' => $icon_comments,
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'post_tag',
			'color_link' => FALSE,
			'icon' => $icon_tags,
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_title:1',
				'post_date:1',
				'post_content:1',
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_comments:1',
				'post_taxonomy:1',
			),
		),
	),
),

'blog_classic' => array(
	'title' => 'Blog Classic',
	'cols' => '2',
	'data' => array(
		'post_image:1' => array(
			'media_preview' => TRUE,
			'css' => array(
				'default' => array(
					'margin-bottom' => '1rem',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1.4rem',
					'margin-bottom' => '0.5rem',
				),
			),
		),
		'hwrapper:1' => array(
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
				),
			),
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'category',
		),
		'post_content:1' => array(
			'css' => array(
				'default' => array(
					'margin-top' => '0.5rem',
				),
			),
		),
		'btn:1' => array(
			'label' => __( 'Read More', 'us' ),
			'link_type' => 'post',
			'style' => '2',
			'css' => array(
				'default' => array(
					'margin-top' => '1.5rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'post_title:1',
				'hwrapper:1',
				'post_content:1',
				'btn:1',
			),
			'hwrapper:1' => array(
				'post_date:1',
				'post_taxonomy:1',
				'post_comments:1',
			),
		),
	),
),

'blog_flat' => array(
	'title' => 'Blog Flat Centered',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'media_preview' => TRUE,
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'css' => array(
				'default' => array(
					'padding-top' => '1.5rem',
					'padding-right' => '2.5rem',
					'padding-bottom' => '2.5rem',
					'padding-left' => '2.5rem',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1.2rem',
				),
			),
		),
		'hwrapper:2' => array(
			'alignment' => 'center',
			'wrap' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
				),
			),
		),
		'post_content:1' => array(
			'length' => '20',
		),
		'post_date:1' => array(
			'format' => 'smart',
		),
		'post_comments:1' => array(
			'number' => TRUE,
			'icon' => $icon_comments,
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'hwrapper:2' => array(
				'post_date:1',
				'post_comments:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'hwrapper:2',
				'post_content:1',
			),
		),
		'options' => array(
			'overflow' => TRUE,
			'color_bg' => '_content_bg',
			'color_text' => '_content_text',
			'box_shadow_hover' => '1.5rem',
		),
	),
),

/* News =========================================================================== */

'blog_3' => array(
	'title' => 'Title only',
	'group' => __( 'News Templates', 'us' ),
	'cols' => '4',
	'data' => array(
		'post_title:1' => array(
			'tag' => 'div',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_title:1',
			),
		),
	),
),

'blog_2' => array(
	'title' => 'Title & Date',
	'cols' => '4',
	'data' => array(
		'post_title:1' => array(
			'tag' => 'h2',
			'css' => array(
				'default' => array(
					'font-size' => '1rem',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.8rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_title:1',
				'post_date:1',
			),
		),
	),
),

'blog_compact' => array(
	'title' => 'Title, Date, Comments',
	'data' => array(
		'hwrapper:1' => array(
			'wrap' => TRUE,
		),
		'post_title:1' => array(
			'color_link' => FALSE,
			'tag' => 'div',
			'css' => array(
				'default' => array(
					'font-size' => '1rem',
					'margin-bottom' => '0',
				),
			),
		),
		'post_date:1' => array(
			'format' => 'smart',
			'css' => array(
				'default' => array(
					'font-size' => '0.9rem',
				),
			),
		),
		'post_comments:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '0.9rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_title:1',
				'post_date:1',
				'post_comments:1',
			),
		),
	),
),

/* Gallery =========================================================================== */

'gallery_default' => array(
	'title' => __( 'Image Gallery', 'us' ),
	'group' => __( 'Gallery Templates', 'us' ),
	'cols' => '6',
	'items_gap' => '',
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'thumbnail_size' => 'us_600_600_crop',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
			),
		),
	),
),

'gallery_with_titles_below' => array(
	'title' => __( 'Image Gallery with titles BELOW the image', 'us' ),
	'cols' => '6',
	'items_gap' => '',
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'has_ratio' => TRUE,
			'thumbnail_size' => 'us_600_600_crop',
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'css' => array(
				'default' => array(
					'padding-top' => '0.5rem',
					'padding-right' => '1rem',
					'padding-bottom' => '1rem',
					'padding-left' => '1rem',
				),
			),
		),
		'post_title:1' => array(
			'link' => 'none',
			'tag' => 'div',
			'el_class' => 'hide_if_not_first',
			'css' => array(
				'default' => array(
					'line-height' => '1.6',
					'margin-bottom' => '0',
				),
			),
		),
		'post_custom_field:1' => array(
			'key' => 'custom',
			'custom_key' => '_wp_attachment_image_alt',
			'hide_empty' => TRUE,
			'el_class' => 'hide_if_not_first',
			'css' => array(
				'default' => array(
					'line-height' => '1.6',
					'margin-bottom' => '0',
				),
			),
		),
		'post_content:1' => array(
			'type' => 'excerpt_only',
			'css' => array(
				'default' => array(
					'line-height' => '1.6',
					'margin-bottom' => '0',
				),
			),
		),
		'post_content:2' => array(
			'type' => 'full_content',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
					'line-height' => '1.6',
					'margin-top' => '0.2rem',
					'margin-bottom' => '0.2rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_content:1',
				'post_custom_field:1',
				'post_title:1',
				'post_content:2',
			),
		),
	),
),

'gallery_with_titles_over' => array(
	'title' => __( 'Image Gallery with titles OVER the image', 'us' ),
	'cols' => '6',
	'items_gap' => '',
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'has_ratio' => TRUE,
			'thumbnail_size' => 'us_600_600_crop',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'background-color' => 'linear-gradient(transparent, rgba(30,30,30,0.8))',
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '15%',
					'padding-right' => '7%',
					'padding-bottom' => '6%',
					'padding-left' => '7%',

				),
			),
		),
		'post_title:1' => array(
			'link' => 'none',
			'tag' => 'div',
			'el_class' => 'hide_if_not_first',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'line-height' => '1.6',
					'margin-bottom' => '0',
				),
			),
		),
		'post_custom_field:1' => array(
			'key' => 'custom',
			'custom_key' => '_wp_attachment_image_alt',
			'hide_empty' => TRUE,
			'el_class' => 'hide_if_not_first',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'line-height' => '1.6',
					'margin-bottom' => '0',
				),
			),
		),
		'post_content:1' => array(
			'type' => 'excerpt_only',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'line-height' => '1.6',
					'margin-bottom' => '0',
				),
			),
		),
		'post_content:2' => array(
			'type' => 'full_content',
			'css' => array(
				'default' => array(
					'color' => 'rgba(255,255,255,0.5)',
					'font-size' => '0.9rem',
					'line-height' => '1.6',
					'margin-top' => '0.2rem',
					'margin-bottom' => '0.2rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_content:1',
				'post_custom_field:1',
				'post_title:1',
				'post_content:2',
			),
		),
		'options' => array(
			'overflow' => TRUE,
		),
	),
),

/* Portfolio =========================================================================== */

'portfolio_1' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 1',
	'group' => __( 'Portfolio Templates', 'us' ),
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'translateY_hover' => '-10%',
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'css' => array(
				'default' => array(
					'background-color' => 'inherit',
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '-1px',
					'left' => '0',
					'padding-top' => '1.2rem',
					'padding-right' => '1.5rem',
					'padding-bottom' => '1.2rem',
					'padding-left' => '1.5rem',
				),
			),
			'el_class' => 'grid_arrow_top',
			'hover' => TRUE,
			'translateY' => '101%',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
					'margin-bottom' => '0.3rem',
				),
			),
		),
		'post_custom_field:1' => array(
			'key' => 'us_tile_additional_image',
			'hide_empty' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'translateY' => '100%',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'post_custom_field:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
			'color_bg' => '_content_bg',
			'color_text' => '_content_text',
		),
	),
),

'portfolio_2' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 2',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
			'scale_hover' => '1.1',
			'transition_duration' => '0.35s',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'background-color' => 'linear-gradient(transparent, rgba(30,30,30,0.8))',
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '4rem',
					'padding-right' => '2rem',
					'padding-bottom' => '1.5rem',
					'padding-left' => '2rem',
				),
			),
			'transition_duration' => '0.35s',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '1.2rem',
				),
			),
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '0.9rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
			'color_bg' => '#333',
		),
	),
),

'portfolio_3' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 3',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity' => '0.25',
			'transition_duration' => '0.4s',
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0',
			'translateY_hover' => '-100%',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'hover' => TRUE,
			'opacity_hover' => '0',
			'translateY_hover' => '100%',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_4' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 4',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
		),
		'vwrapper:1' => array(
			'valign' => 'bottom',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateY' => '-40px',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'hover' => TRUE,
			'opacity' => '0',
			'opacity_hover' => '0.75',
			'translateY' => '-20px',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_5' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 5',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'scale_hover' => '1.2',
			'transition_duration' => '0.4s',
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'background-color' => 'inherit',
					'position' => 'absolute',
					'top' => '1.3rem',
					'right' => '1.3rem',
					'bottom' => '1.3rem',
					'left' => '1.3rem',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'opacity_hover' => '0.95',
			'scale' => '0',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
				),
			),
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
			'color_bg' => '_content_bg',
			'color_text' => '_content_text',
		),
	),
),

'portfolio_6' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 6',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'scale' => '1.5',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
				),
			),
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_7' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 7',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
			'scale' => '1.1',
			'transition_duration' => '0.4s',
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '2.6rem',
					'padding-right' => '2.6rem',
					'padding-bottom' => '2.6rem',
					'padding-left' => '2.6rem',
				),
			),
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateY' => '-50%',
			'transition_duration' => '0.4s',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'hover' => TRUE,
			'opacity' => '0',
			'translateY' => '50%',
			'transition_duration' => '0.4s',
		),
		'html:1' => array(
			'content' => '',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '1.3rem',
					'right' => '1.3rem',
					'bottom' => '1.3rem',
					'left' => '1.3rem',
					'border-style' => 'solid',
					'border-top-width' => '2px',
					'border-right-width' => '2px',
					'border-bottom-width' => '2px',
					'border-left-width' => '2px',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'scale' => '1.1',
			'transition_duration' => '0.4s',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'html:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_8' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 8',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'width' => '110%',
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
			'translateX' => is_rtl() ? '8%' : '-8%',
			'transition_duration' => '0.4s',
		),
		'vwrapper:1' => array(
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateX' => '-33%',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'hover' => TRUE,
			'opacity' => '0',
			'opacity_hover' => '0.75',
			'translateX' => '40%',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_9' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 9',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0',
			'scale_hover' => '4',
			'transition_duration' => '0.4s',
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
			'hover' => TRUE,
			'scale' => '0',
			'transition_duration' => '0.5s',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
				),
			),
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_10' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 10',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'background-color' => 'linear-gradient(transparent, rgba(30,30,30,0.8))',
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '-1px',
					'left' => '0',
					'padding-top' => '5rem',
					'padding-right' => '2rem',
					'padding-bottom' => '1.5rem',
					'padding-left' => '2rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'transition_duration' => '0.4s',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '1.4rem',
				),
			),
			'hover' => TRUE,
			'translateY' => '35%',
			'transition_duration' => '0.35s',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '0.9rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'opacity_hover' => '0.75',
			'translateY' => '100%',
			'transition_duration' => '0.35s',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_11' => array (
	'title' => __( 'Portfolio', 'us' ) . ' 11',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
			'transition_duration' => '0.35s',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateY' => '-25%',
			'transition_duration' => '0.35s',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
				),
			),
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
		),
		'html:1' => array(
			'content' => '',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '10px',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'translateY' => '100%',
			'transition_duration' => '0.35s',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
				'html:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_12' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 12',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
			'transition_duration' => '0.25s',
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '4rem',
					'padding-right' => '4rem',
					'padding-bottom' => '4rem',
					'padding-left' => '4rem',
				),
			),
			'el_class' => 'grid_style_12',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.4rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateY' => '-50%',
			'transition_duration' => '0.25s',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'hover' => TRUE,
			'opacity' => '0',
			'opacity_hover' => '0.75',
			'translateY' => '75%',
			'transition_duration' => '0.25s',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_13' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 13',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.5rem',
					'margin-bottom' => '1.3rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateY' => '20px',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'left' => is_rtl() ? '' : '0',
					'right' => is_rtl() ? '0' : '',
					'bottom' => '0',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateY' => '100px',
		),
		'html:1' => array(
			'content' => '',
			'css' => array(
				'default' => array(
					'width' => '100%',
					'border-style' => 'solid',
					'border-top-width' => '3px',
					'border-bottom-width' => '0',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateY' => '60px',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'left' => '0',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-left' => '2rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
				'post_taxonomy:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'html:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_14' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 14',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
			'scale' => '1.15',
			'transition_duration' => '0.35s',
			'transform_origin_X' => '100%',
			'transform_origin_Y' => '100%',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.5rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateX' => '-2rem',
			'transition_duration' => '0.35s',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'hover' => TRUE,
			'opacity' => '0',
			'translateX' => '-1rem',
			'transition_duration' => '0.35s',
		),
		'vwrapper:1' => array(
			'valign' => 'bottom',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '10%',
					'padding-right' => '10%',
					'padding-bottom' => '10%',
					'padding-left' => '10%',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_taxonomy:1',
				'post_title:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_15' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 15',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
			'el_class' => 'grid_style_15',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.5rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'translateY' => '20px',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'hover' => TRUE,
			'opacity' => '0',
			'translateX' => '20px',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_16' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 16',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'circle' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'el_class' => 'grid_corner_image',
			'hover' => TRUE,
			'scale' => '0.3',
			'scale_hover' => '1',
			'transform_origin_X' => '90%',
			'transform_origin_Y' => '90%',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'left' => '0',
					'padding-top' => '10%',
					'padding-right' => '30%',
					'padding-left' => '10%',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0',
			'scale_hover' => '2',
			'translateX_hover' => '-50%',
			'translateY_hover' => '-50%',
			'transition_duration' => '0.4s',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1.5rem',
					'font-weight' => '700',
				),
			),
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
			'color_bg' => '_content_bg_alt',
			'color_text' => '_content_heading',
		),
	),
),

'portfolio_17' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 17',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity_hover' => '0.1',
			'scale_hover' => '1.3',
			'transition_duration' => '0.8s',
			'transform_origin_X' => '100%',
			'transform_origin_Y' => '100%',
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '10%',
					'padding-right' => '10%',
					'padding-bottom' => '10%',
					'padding-left' => '10%',
				),
			),
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '2rem',
					'margin-bottom' => '1rem',
				),
			),
			'hover' => TRUE,
			'scale' => '0.5',
			'transition_duration' => '0.4s',
			'transform_origin_X' => is_rtl() ? '100%' : '0%',
			'transform_origin_Y' => '0%',
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'line_height' => '1.2',
			'hover' => TRUE,
			'opacity' => '0',
			'scale' => '0.5',
			'translateY' => '-150px',
			'transition_duration' => '0.4s',
			'transform_origin_X' => '0%',
			'transform_origin_Y' => '0%',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_18' => array(
	'title' => __( 'Portfolio', 'us' ) . ' 18',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
		),
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'background-color' => 'linear-gradient(transparent, rgba(30,30,30,0.8))',
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '-1px',
					'left' => '0',
					'padding-top' => '5rem',
					'padding-right' => '2rem',
					'padding-bottom' => '1.5rem',
					'padding-left' => '2rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'transition_duration' => '1s',
		),
		'post_title:1' => array(
			'link' => 'none',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '1.4rem',
				),
			),
		),
		'post_taxonomy:1' => array(
			'taxonomy_name' => 'us_portfolio_category',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '0.9rem',
				),
			),
		),
		'post_custom_field:1' => array(
			'key' => 'us_tile_additional_image',
			'hide_empty' => TRUE,
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
			'transition_duration' => '1s',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'post_custom_field:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'post_taxonomy:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

'portfolio_compact' => array(
	'title' => __( 'Portfolio', 'us' ) . ' ' . __( 'Compact', 'us' ),
	'cols' => '10',
	'items_gap' => '0.5px',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'link' => 'none',
			'placeholder' => TRUE,
			'thumbnail_size' => 'thumbnail',
			'css' => array(
				'default' => array(
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'background-color' => 'rgba(0,0,0,0.8)',
					'position' => 'absolute',
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'padding-top' => '0.5rem',
					'padding-left' => '0.5rem',
					'padding-right' => '0.5rem',
					'padding-bottom' => '0.5rem',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
		),
		'post_title:1' => array(
			'link' => 'none',
			'tag' => 'div',
			'hide_empty' => TRUE,
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '11px',
					'line-height' => '1.4',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
			),
		),
		'options' => array(
			'fixed' => TRUE,
		),
	),
),

/* Testimonial =========================================================================== */

'testimonial_1' => array(
	'title' => __( 'Testimonial', 'us' ) . ' 1',
	'group' => __( 'Testimonial Templates', 'us' ),
	'hover_effect' => 1,
	'data' => array(
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'border-style' => 'solid',
					'border-top-width' => '2px',
					'border-right-width' => '2px',
					'border-bottom-width' => '2px',
					'border-left-width' => '2px',
					'border-color' => '_content_border',
					'border-radius' => us_get_option( 'rounded_corners' ) ? '0.3rem' : '',
					'padding-top' => '2rem',
					'padding-right' => '2rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2rem',
				),
			),
			'hover' => TRUE,
			'color_border_hover' => '_content_primary',
		),
		'post_content:1' => array(
			'type' => 'full_content',
		),
		'hwrapper:1' => array(
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'margin-bottom' => '1rem',
				),
			),
		),
		'post_image:1' => array(
			'link' => 'custom',
			'custom_link' => array(
				'url' => '{{us_testimonial_link}}',
				'target' => '',
			),
			'circle' => TRUE,
			'thumbnail_size' => 'thumbnail',
			'css' => array(
				'default' => array(
					'width' => '4rem',
					'margin-left' => is_rtl() ? '1rem' : '0',
					'margin-right' => is_rtl() ? '0' : '1rem',
				),
			),
		),
		'vwrapper:2' => array(
		),
		'post_custom_field:1' => array(
			'key' => 'us_testimonial_author',
			'link' => 'us_testimonial_link',
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'line-height' => '1.5',
					'margin-bottom' => '0',
				),
			),
		),
		'post_custom_field:2' => array(
			'key' => 'us_testimonial_role',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
					'line-height' => '1.5',
				),
			),
		),
		'post_custom_field:3' => array(
			'key' => 'us_testimonial_rating',
			'css' => array(
				'default' => array(
					'color' => '_content_primary',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_custom_field:3',
				'post_content:1',
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_image:1',
				'vwrapper:2',
			),
			'vwrapper:2' => array(
				'post_custom_field:1',
				'post_custom_field:2',
			),
		),
	),
),

'testimonial_2' => array(
	'title' => __( 'Testimonial', 'us' ) . ' 2',
	'data' => array(
		'post_content:1' => array(
			'type' => 'full_content',
		),
		'hwrapper:1' => array(
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'margin-top' => '0.5rem',
				),
			),
		),
		'post_image:1' => array(
			'link' => 'custom',
			'custom_link' => array(
				'url' => '{{us_testimonial_link}}',
				'target' => '',
			),
			'circle' => TRUE,
			'thumbnail_size' => 'thumbnail',
			'css' => array(
				'default' => array(
					'width' => '4rem',
					'margin-left' => is_rtl() ? '1rem' : '',
					'margin-right' => is_rtl() ? '' : '1rem',
				),
			),
		),
		'vwrapper:1' => array(
		),
		'post_custom_field:1' => array(
			'key' => 'us_testimonial_author',
			'link' => 'us_testimonial_link',
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'line-height' => '1.5',
					'margin-bottom' => '0',
				),
			),
		),
		'post_custom_field:2' => array(
			'key' => 'us_testimonial_role',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
					'line-height' => '1.5',
					'margin-bottom' => '0',
				),
			),
		),
		'vwrapper:2' => array(
			'css' => array(
				'default' => array(
					'padding-top' => '3.5rem',
					'padding-left' => is_rtl() ? '' : '2rem',
					'padding-right' => is_rtl() ? '2rem' : '',
				),
			),
		),
		'post_custom_field:3' => array(
			'key' => 'custom',
			'icon' => $icon_quote,
			'css' => array(
				'default' => array(
					'color' => '_content_primary',
					'font-size' => '3rem',
					'line-height' => '1',
					'position' => 'absolute',
					'top' => '0',
					'left' => is_rtl() ? '' : '0',
					'right' => is_rtl() ? '0' : '',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'vwrapper:2',
			),
			'hwrapper:1' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_custom_field:1',
				'post_custom_field:2',
			),
			'vwrapper:2' => array(
				'post_custom_field:3',
				'post_content:1',
				'hwrapper:1',
			),
		),
	),
),

'testimonial_3' => array(
	'title' => __( 'Testimonial', 'us' ) . ' 3',
	'data' => array(
		'post_content:1' => array(
			'type' => 'full_content',
		),
		'hwrapper:1' => array(
			'valign' => 'middle',
		),
		'post_image:1' => array(
			'link' => 'custom',
			'custom_link' => array(
				'url' => '{{us_testimonial_link}}',
				'target' => '',
			),
			'circle' => TRUE,
			'thumbnail_size' => 'thumbnail',
			'css' => array(
				'default' => array(
					'width' => '4rem',
					'margin-left' => is_rtl() ? '1rem' : '',
					'margin-right' => is_rtl() ? '' : '1rem',
				),
			),
		),
		'vwrapper:1' => array(
		),
		'post_custom_field:1' => array(
			'key' => 'us_testimonial_author',
			'link' => 'us_testimonial_link',
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'line-height' => '1.5',
					'margin-bottom' => '0',
				),
			),
		),
		'post_custom_field:2' => array(
			'key' => 'us_testimonial_role',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
					'line-height' => '1.5',
				),
			),
		),
		'vwrapper:2' => array(
			'css' => array(
				'default' => array(
					'padding-left' => is_rtl() ? '' : '2rem',
					'padding-right' => is_rtl() ? '2rem' : '',
				),
			),
		),
		'post_custom_field:3' => array(
			'key' => 'custom',
			'icon' => $icon_quote,
			'css' => array(
				'default' => array(
					'font-size' => '1.4rem',
					'line-height' => '1',
					'position' => 'absolute',
					'top' => '0',
					'left' => is_rtl() ? '' : '0',
					'right' => is_rtl() ? '0' : '',
				),
			),
			'hover' => TRUE,
			'opacity' => '0.2',
			'opacity_hover' => '0.2',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'vwrapper:2',
			),
			'hwrapper:1' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_custom_field:1',
				'post_custom_field:2',
			),
			'vwrapper:2' => array(
				'post_custom_field:3',
				'post_content:1',
				'hwrapper:1',
			),
		),
	),
),

'testimonial_4' => array(
	'title' => __( 'Testimonial', 'us' ) . ' 4',
	'data' => array(
		'post_content:1' => array(
			'type' => 'full_content',
		),
		'hwrapper:1' => array(
		),
		'post_image:1' => array(
			'link' => 'custom',
			'custom_link' => array(
				'url' => '{{us_testimonial_link}}',
				'target' => '',
			),
			'placeholder' => TRUE,
			'circle' => TRUE,
			'thumbnail_size' => 'thumbnail',
			'css' => array(
				'default' => array(
					'width' => '5.5rem',
				),
			),
			'el_class' => 'with_quote_icon',
		),
		'vwrapper:1' => array(
		),
		'post_custom_field:1' => array(
			'key' => 'us_testimonial_author',
			'link' => 'us_testimonial_link',
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'line-height' => '1.5',
					'margin-bottom' => '0',
				),
			),
		),
		'post_custom_field:2' => array(
			'key' => 'us_testimonial_role',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
					'line-height' => '1.5',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_content:1',
				'post_custom_field:1',
				'post_custom_field:2',
			),
		),
	),
),

'testimonial_5' => array(
	'title' => __( 'Testimonial', 'us' ) . ' 5',
	'data' => array(
		'post_content:1' => array(
			'type' => 'full_content',
		),
		'post_image:1' => array(
			'link' => 'custom',
			'custom_link' => array(
				'url' => '{{us_testimonial_link}}',
				'target' => '',
			),
			'circle' => TRUE,
			'thumbnail_size' => 'thumbnail',
			'css' => array(
				'default' => array(
					'width' => '7rem',
				),
			),
		),
		'post_custom_field:1' => array(
			'key' => 'us_testimonial_author',
			'link' => 'us_testimonial_link',
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'line-height' => '1.5',
					'margin-bottom' => '0',
				),
			),
		),
		'post_custom_field:2' => array(
			'key' => 'us_testimonial_role',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
					'line-height' => '1.5',
				),
			),
		),
		'vwrapper:2' => array(
			'alignment' => 'center',
		),
		'post_custom_field:3' => array(
			'key' => 'us_testimonial_rating',
			'css' => array(
				'default' => array(
					'color' => '_content_primary',
					'font-size' => '1.2rem',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'vwrapper:2',
			),
			'vwrapper:2' => array(
				'post_custom_field:3',
				'post_content:1',
				'post_image:1',
				'post_custom_field:1',
				'post_custom_field:2',
			),
		),
	),
),

'testimonial_6' => array(
	'title' => __( 'Testimonial', 'us' ) . ' 6',
	'hover_effect' => 1,
	'data' => array(
		'post_content:1' => array(
			'type' => 'full_content',
		),
		'hwrapper:1' => array(
			'valign' => 'middle',
			'css' => array(
				'default' => array(
					'padding-top' => '1.5rem',
					'padding-right' => '2.5rem',
					'padding-left' => '2.5rem',
				),
			),
		),
		'post_image:1' => array(
			'link' => 'custom',
			'custom_link' => array(
				'url' => '{{us_testimonial_link}}',
				'target' => '',
			),
			'circle' => TRUE,
			'thumbnail_size' => 'thumbnail',
			'css' => array(
				'default' => array(
					'width' => '4rem',
					'margin-left' => is_rtl() ? '1rem' : '0',
					'margin-right' => is_rtl() ? '0' : '1rem',
				),
			),
		),
		'vwrapper:1' => array(
		),
		'post_custom_field:1' => array(
			'key' => 'us_testimonial_author',
			'link' => 'us_testimonial_link',
			'color_link' => FALSE,
			'css' => array(
				'default' => array(
					'font-weight' => '700',
					'line-height' => '1.5',
					'margin-bottom' => '0',
				),
			),
		),
		'post_custom_field:2' => array(
		'key' => 'us_testimonial_role',
			'css' => array(
				'default' => array(
					'color' => '_content_faded',
					'font-size' => '0.9rem',
					'line-height' => '1.5',
				),
			),
		),
		'vwrapper:2' => array(
			'css' => array(
				'default' => array(
					'background-color' => '_content_bg_alt',
					'color' => '_content_text',
					'padding-top' => '2rem',
					'padding-right' => '2.5rem',
					'padding-bottom' => '2rem',
					'padding-left' => '2.5rem',
					'border-radius' => us_get_option( 'rounded_corners' ) ? '0.3rem' : '',
				),
			),
			'el_class' => 'grid_arrow_bottom',
		),
		'post_custom_field:3' => array(
			'key' => 'us_testimonial_rating',
			'css' => array(
				'default' => array(
					'color' => '#fb0',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'vwrapper:2',
				'hwrapper:1',
			),
			'hwrapper:1' => array(
				'post_image:1',
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_custom_field:1',
				'post_custom_field:2',
			),
			'vwrapper:2' => array(
				'post_custom_field:3',
				'post_content:1',
			),
		),
	),
),

/* Shop =========================================================================== */

'shop_standard' => array(
	'title' => 'Shop Standard',
	'group' => __( 'Shop Templates', 'us' ),
	'cols' => '4',
	'items_gap' => '1rem',
	'data' => array(
		'post_image:1' => array(
			'placeholder' => TRUE,
			'thumbnail_size' => 'shop_catalog',
		),
		'product_field:1' => array(
			'type' => 'sale_badge',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '12px',
					'font-weight' => '700',
					'text-transform' => 'uppercase',
					'background-color' => '_content_primary',
					'position' => 'absolute',
					'top' => '10px',
					'left' => is_rtl() ? '' : '10px',
					'right' => is_rtl() ? '10px' : '',
					'padding-left' => '0.8rem',
					'padding-right' => '0.8rem',
					'border-radius' => '2rem',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '1rem',
					'margin-top' => '0.8rem',
					'margin-bottom' => '0.2rem',
				),
			),
		),
		'product_field:2' => array(
			'type' => 'rating',
			'css' => array(
				'default' => array(
					'margin-bottom' => '0.2rem',
				),
			),
		),
		'product_field:3' => array(
			'css' => array(
				'default' => array(
					'font-weight' => '700',
				),
			),
		),
		'add_to_cart:1' => array(
			'view_cart_link' => TRUE,
			'css' => array(
				'default' => array(
					'font-size' => '0.8rem',
					'margin-top' => '0.4rem',
					'border-radius' => us_get_option( 'rounded_corners' ) ? '0.2rem' : '',
				),
			),
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'product_field:1',
				'post_title:1',
				'product_field:2',
				'product_field:3',
				'add_to_cart:1',
			),
		),
	),
),

'shop_modern' => array(
	'title' => 'Shop Modern',
	'cols' => '4',
	'items_gap' => '5px',
	'hover_effect' => 1,
	'data' => array(
		'post_image:1' => array(
			'placeholder' => TRUE,
			'thumbnail_size' => 'shop_catalog',
		),
		'product_field:1' => array(
			'type' => 'sale_badge',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '12px',
					'font-weight' => '700',
					'text-transform' => 'uppercase',
					'background-color' => '_content_primary',
					'position' => 'absolute',
					'top' => '10px',
					'left' => is_rtl() ? '' : '10px',
					'right' => is_rtl() ? '10px' : '',
					'padding-right' => '0.8rem',
					'padding-left' => '0.8rem',
					'border-radius' => '2rem',
				),
			),
		),
		'vwrapper:1' => array(
			'alignment' => 'center',
			'css' => array(
				'default' => array(
					'background-color' => 'inherit',
					'padding-top' => '1rem',
					'padding-right' => '1.2rem',
					'padding-bottom' => '1rem',
					'padding-left' => '1.2rem',
				),
			),
			'hover' => TRUE,
			'translateY_hover' => '-2.4rem',
			'transition_duration' => '0.2s',
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1rem',
					'margin-bottom' => '0.3rem',
				),
			),
		),
		'product_field:2' => array(
			'type' => 'rating',
			'css' => array(
				'default' => array(
					'margin-bottom' => '0.3rem',
				),
			),
		),
		'product_field:3' => array(
			'css' => array(
				'default' => array(
					'font-weight' => '700',
				),
			),
		),
		'add_to_cart:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '0.8rem',
					'width' => '100%',
					'border-radius' => '0',
					'position' => 'absolute',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				),
			),
			'hover' => TRUE,
			'opacity' => '0',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'post_image:1',
				'product_field:1',
				'vwrapper:1',
				'add_to_cart:1',
			),
			'vwrapper:1' => array(
				'post_title:1',
				'product_field:2',
				'product_field:3',
			),
		),
		'options' => array(
			'overflow' => TRUE,
			'color_bg' => '_content_bg',
			'color_text' => '_content_text',
			'border_radius' => us_get_option( 'rounded_corners' ) ? '0.3rem' : '',
			'box_shadow' => '0.3rem',
			'box_shadow_hover' => '1rem',
		),
	),
),

'shop_trendy' => array(
	'title' => 'Shop Trendy',
	'cols' => '4',
	'items_gap' => '',
	'hover_effect' => 1,
	'data' => array(
		'vwrapper:1' => array(
			'css' => array(
				'default' => array(
					'padding-right' => '10px',
					'padding-top' => '10px',
					'padding-left' => '10px',
				),
			),
		),
		'post_image:1' => array(
			'placeholder' => TRUE,
			'media_preview' => TRUE,
			'thumbnail_size' => 'shop_catalog',
		),
		'product_field:1' => array(
			'type' => 'sale_badge',
			'css' => array(
				'default' => array(
					'color' => '#fff',
					'font-size' => '12px',
					'font-weight' => '700',
					'text-transform' => 'uppercase',
					'background-color' => '_content_primary',
					'position' => 'absolute',
					'top' => '10px',
					'left' => is_rtl() ? '' : '10px',
					'right' => is_rtl() ? '10px' : '',
					'padding-right' => '0.8rem',
					'padding-left' => '0.8rem',
				),
			),
		),
		'post_title:1' => array(
			'css' => array(
				'default' => array(
					'color' => 'inherit',
					'font-size' => '1rem',
					'margin-bottom' => '0.4rem',
				),
			),
		),
		'product_field:2' => array(
			'type' => 'rating',
			'css' => array(
				'default' => array(
					'margin-bottom' => '0.2rem',
				),
			),
		),
		'product_field:3' => array(
			'css' => array(
				'default' => array(
					'font-weight' => '700',
				),
			),
		),
		'add_to_cart:1' => array(
			'css' => array(
				'default' => array(
					'font-size' => '15px',
					'width' => '100%',
					'border-radius' => '0',
					'position' => 'absolute',
					'top' => '100%',
					'right' => '0',
					'left' => '0',
				),
			),
			'hide_below' => '600px',
			'hover' => TRUE,
			'opacity' => '0',
		),
	),
	'default' => array(
		'layout' => array(
			'middle_center' => array(
				'vwrapper:1',
			),
			'vwrapper:1' => array(
				'post_image:1',
				'product_field:1',
				'post_title:1',
				'product_field:2',
				'product_field:3',
				'add_to_cart:1',
			),
		),
		'options' => array(
			'color_bg' => '_content_bg',
			'color_text' => '_content_text',
			'box_shadow_hover' => '1rem',
		),
	),
),

);
