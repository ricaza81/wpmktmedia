<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * UpSolution Widget: Gallery
 *
 * Class US_Widget_Media_Gallery
 */

class US_Widget_Media_Gallery extends WP_Widget_Media_Gallery {

	public function get_instance_schema() {

		$schema = parent::get_instance_schema();

		$schema['indents'] = array(
			'type' => 'boolean',
			'default' => false,
			'media_prop' => 'indents',
			'should_preview_update' => false,
		);

		$schema['masonry'] = array(
			'type' => 'boolean',
			'default' => false,
			'media_prop' => 'masonry',
			'should_preview_update' => false,
		);

		$schema['meta'] = array(
			'type' => 'boolean',
			'default' => false,
			'media_prop' => 'meta',
			'should_preview_update' => false,
		);

		$schema['size']['enum'] = array_merge( array( 'full' ), get_intermediate_image_sizes() );

		return $schema;
	}

	public function render_media( $instance ){
		$instance = array_merge( wp_list_pluck( $this->get_instance_schema(), 'default' ), $instance );

		$shortcode_atts = $instance;

		// @codeCoverageIgnoreStart
		if ( $instance['orderby_random'] ) {
			$shortcode_atts['orderby'] = 'rand';
		}
		$shortcode_atts['link'] = isset( $instance['link_type'] ) ? $instance['link_type'] : '';

		if ( isset( $shortcode_atts['indents'] ) AND $shortcode_atts['indents'] ) {
			$shortcode_atts['indents'] = 'true';
		}

		if ( isset( $shortcode_atts['masonry'] ) AND $shortcode_atts['masonry'] ) {
			$shortcode_atts['masonry'] = 'true';
		}

		if ( isset( $shortcode_atts['meta'] ) AND $shortcode_atts['meta'] ) {
			$shortcode_atts['meta'] = 'true';
		}

		// @codeCoverageIgnoreEnd
		global $us_shortcodes;
		echo $us_shortcodes->gallery( $shortcode_atts );
	}

}
