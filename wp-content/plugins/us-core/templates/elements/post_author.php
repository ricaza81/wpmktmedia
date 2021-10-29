<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Author element
 *
 * @var $link string Link type: 'post' / 'author' / 'custom' / 'none'
 * @var $custom_link array
 * @var $color string Custom color
 * @var $icon string Icon name
 * @var $design_options array
 *
 * @var $classes string
 * @var $id string
 */

global $us_grid_object_type;

// Cases when the element shouldn't be shown
if ( $us_elm_context == 'grid' AND $us_grid_object_type == 'term' ) {
	return;
} elseif ( $us_elm_context == 'shortcode' AND is_archive() AND ! is_author() ) {
	return;
}

$_atts['class'] = 'w-post-elm post_author';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' vcard author'; // needed for Google structured data

if ( $color_link ) {
	$_atts['class'] .= ' color_link_inherit';
}

if ( $avatar ) {
	$_atts['class'] .= ' with_ava avapos_' . $avatar_pos;
}

// When text color is set in Design Options, add the specific class
if ( us_design_options_has_property( $css, 'color' ) ) {
	$_atts['class'] .= ' has_text_color';
}

if ( ! empty( $el_class ) ) {
	$_atts['class'] .= ' ' . $el_class;
}
if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

$author_id = get_the_author_meta( 'ID' );

// Generate anchor semantics
$ava_link_start_tag = $ava_link_end_tag = '';
if ( $link === 'author_page' ) {
	$link_atts['href'] = get_author_posts_url( $author_id, get_the_author_meta( 'user_nicename' ) );
} elseif ( $link === 'author_website' ) {
	$link_atts['href'] = get_the_author_meta( 'url' );
	$link_atts['target'] = '_blank';
	$link_atts['rel'] = 'noopener nofollow';
} elseif ( $link === 'post' ) {
	$link_atts['href'] = apply_filters( 'the_permalink', get_permalink() );
	if ( get_post_format() == 'link' ) {
		$link_atts['target'] = '_blank';
		$link_atts['rel'] = 'noopener';
	}
} elseif ( $link === 'custom' ) {
	$link_atts = us_generate_link_atts( $custom_link );
} else {
	$link_atts = array();
}
if ( ! empty( $link_atts['href'] ) ) {
	$ava_link_atts['class'] = 'fn';
	$ava_link_atts['aria-hidden'] = 'true';
	$ava_link_atts['tabindex'] = '-1';

	$ava_link_start_tag = '<a ' . us_implode_atts( $link_atts + $ava_link_atts ) . '>';
	$ava_link_end_tag = '</a>';
}

// Output the element
$output = '<div ' . us_implode_atts( $_atts ) . '>';
$output .= us_prepare_icon_tag( $icon );

// Avatar
if ( $avatar ) {
	$args = array(
		'force_display' => TRUE, // always show avatar
	);
	$output .= $ava_link_start_tag;
	$output .= '<div class="post-author-ava"' . us_prepare_inline_css( array( 'font-size' => $avatar_width ) ) . '>';
	$avatar_width = intval( $avatar_width );
	$output .= get_avatar( $author_id, $avatar_width, NULL, '', $args );
	$output .= '</div>';
	$output .= $ava_link_end_tag;
}

$output .= '<div class="post-author-meta">';

// Name
if ( ! empty( $link_atts['href'] ) ) {
	$link_atts['class'] = 'post-author-name fn';
	$output .= '<a ' . us_implode_atts( $link_atts ) . '>' . get_the_author() . '</a>';
} else {
	$output .= '<div class="post-author-name">' . get_the_author() . '</div>';
}

if ( ! empty( $link_atts['href'] ) ) {
	$output .= '</a>';
}

// Posts count
if ( $posts_count ) {
	$author_posts_amount = count_user_posts( $author_id, 'post', TRUE );
	$output .= '<div class="post-author-posts">';
	$output .= sprintf( _n( '%s post', '%s posts', $author_posts_amount, 'us' ), $author_posts_amount );
	$output .= '</div>';
}

// Website
if ( $website AND $website_url = get_the_author_meta( 'url' ) ) {
	$output .= '<a class="post-author-website" href="' . $website_url . '" target="_blank" rel="noopener nofollow">';
	$output .= $website_url;
	$output .= '</a>';
}

// Bio Info
if ( $info AND $info_text = get_the_author_meta( 'description' ) ) {
	$output .= '<div class="post-author-info">' . $info_text . '</div>';
}

$output .= '</div>';
$output .= '</div>';

echo $output;
