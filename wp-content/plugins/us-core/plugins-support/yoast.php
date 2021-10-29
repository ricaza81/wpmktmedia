<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Yoast SEO Support
 *
 * @link https://yoast.com
 */


if ( ! function_exists( 'yoast_breadcrumb' ) ) {
	return FALSE;
}

if ( ! function_exists( 'us_wpseo_sitemap_urlimages' ) ) {
	/**
	 * Checking and adding images from Page Blocks
	 *
	 * @param array $images
	 * @param integer $post_id
	 * @return array
	 */
	function us_wpseo_sitemap_urlimages( $images, $post_id ) {
		if ( ! function_exists( 'us_get_recursive_parse_page_block' ) ) {
			return $images;
		}
		$post = get_post( (int) $post_id );
		us_get_recursive_parse_page_block(
			$post, function( $post ) use ( &$images ) {
			if ( preg_match_all( '/\[us_image\simage="(\d+)"/', $post->post_content, $matches ) ) {
				foreach ( us_arr_path( $matches, '1', array() ) as $attachment_id ) {
					$images[] = array(
						'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', TRUE ),
						'src' => wp_get_attachment_image_src( $attachment_id, 'full' )[0],
						'title' => get_the_title( $attachment_id ),
					);
				}
			}
		}
		);
		return $images;
	}
	add_filter( 'wpseo_sitemap_urlimages', 'us_wpseo_sitemap_urlimages', 10, 2 );
}
