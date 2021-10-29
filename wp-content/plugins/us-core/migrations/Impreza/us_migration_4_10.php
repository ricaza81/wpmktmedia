<?php

class us_migration_4_10 extends US_Migration_Translator {

	private $socials_config = array(
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
	);

	private $image_sizes = NULL;

	private function get_image_sizes() {
		if ( is_array( $this->image_sizes ) ) {
			return $this->image_sizes;
		}

		$this->image_sizes = array();

		$custom_tnail_sizes = us_get_option( 'img_size' );
		if ( is_array( $custom_tnail_sizes ) ) {
			foreach ( $custom_tnail_sizes as $size_index => $size ) {
				$crop = ( ! empty( $size['crop'][0] ) );
				$crop_str = ( $crop ) ? '_crop' : '';
				$width = ( ! empty( $size['width'] ) AND intval( $size['width'] ) > 0 ) ? intval( $size['width'] ) : 0;
				$height = ( ! empty( $size['height'] ) AND intval( $size['height'] ) > 0 ) ? intval( $size['height'] ) : 0;
				$old_name = 'us_img_size_' . $size_index;
				$new_name = 'us_' . $width . '_' . $height . $crop_str;
				$this->image_sizes[$old_name] = $new_name;
			}
		}

		return $this->image_sizes;
	}

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_social_links( &$name, &$params, &$content ) {
		$items = array();
		foreach ( $this->socials_config as $social_link => $label ) {
			if ( ! empty( $params[$social_link] ) ) {
				$items[] = array(
					'type' => $social_link,
					'url' => $params[$social_link],
				);
			}
			if ( isset( $params[$social_link] ) ) {
				unset( $params[$social_link] );
			}
		}

		if ( isset( $params['custom_icon'] ) ) {
			$params['custom_icon'] = trim( $params['custom_icon'] );
		}

		if ( ! empty( $params['custom_icon'] ) AND ! empty( $params['custom_link'] ) ) {
			$custom_link = array(
				'type' => 'custom',
				'url' => $params['custom_link'],
				'icon' => $params['custom_icon'],
				'color' => '#1abc9c',
			);
			if ( ! empty ( $params['custom_title'] ) ) {
				$custom_link['title'] = $params['custom_title'];
			}
			if ( ! empty ( $params['custom_color'] ) ) {
				$custom_link['color'] = $params['custom_color'];
			}
			$items[] = $custom_link;

		}

		if ( isset( $params['custom_icon'] ) ) {
			unset( $params['custom_icon'] );
		}
		if ( isset( $params['custom_link'] ) ) {
			unset( $params['custom_link'] );
		}
		if ( isset( $params['custom_title'] ) ) {
			unset( $params['custom_title'] );
		}
		if ( isset( $params['custom_color'] ) ) {
			unset( $params['custom_color'] );
		}

		if ( count( $items ) ) {
			$params['items'] = urlencode( json_encode( $items ) );
		}

		return TRUE;
	}
	public function translate_us_blog( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'tnail-1x1' ) {
			$params['img_size'] = 'us_600_600_crop';
			$changed = TRUE;
		}
		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'tnail-1x1-small' ) {
			$params['img_size'] = 'us_350_350_crop';
			$changed = TRUE;
		}

		$img_sizes = $this->get_image_sizes();
		foreach ( $img_sizes as $old_name => $new_name ) {
			if ( isset( $params['img_size'] ) AND $params['img_size'] == $old_name ) {
				$params['img_size'] = $new_name;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_portfolio( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'tnail-1x1' ) {
			$params['img_size'] = 'us_600_600_crop';
			$changed = TRUE;
		}
		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'tnail-1x1-small' ) {
			$params['img_size'] = 'us_350_350_crop';
			$changed = TRUE;
		}

		$img_sizes = $this->get_image_sizes();
		foreach ( $img_sizes as $old_name => $new_name ) {
			if ( isset( $params['img_size'] ) AND $params['img_size'] == $old_name ) {
				$params['img_size'] = $new_name;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_gallery( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'tnail-1x1' ) {
			$params['img_size'] = 'us_600_600_crop';
			$changed = TRUE;
		}
		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'tnail-1x1-small' ) {
			$params['img_size'] = 'us_350_350_crop';
			$changed = TRUE;
		}

		$img_sizes = $this->get_image_sizes();
		foreach ( $img_sizes as $old_name => $new_name ) {
			if ( isset( $params['img_size'] ) AND $params['img_size'] == $old_name ) {
				$params['img_size'] = $new_name;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_image_slider( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'tnail-1x1' ) {
			$params['img_size'] = 'us_600_600_crop';
			$changed = TRUE;
		}
		if ( isset( $params['img_size'] ) AND $params['img_size'] == 'tnail-1x1-small' ) {
			$params['img_size'] = 'us_350_350_crop';
			$changed = TRUE;
		}

		$img_sizes = $this->get_image_sizes();
		foreach ( $img_sizes as $old_name => $new_name ) {
			if ( isset( $params['img_size'] ) AND $params['img_size'] == $old_name ) {
				$params['img_size'] = $new_name;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_single_image( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['size'] ) AND $params['size'] == 'tnail-1x1' ) {
			$params['size'] = 'us_600_600_crop';
			$changed = TRUE;
		}
		if ( isset( $params['size'] ) AND $params['size'] == 'tnail-1x1-small' ) {
			$params['size'] = 'us_350_350_crop';
			$changed = TRUE;
		}

		$img_sizes = $this->get_image_sizes();
		foreach ( $img_sizes as $old_name => $new_name ) {
			if ( isset( $params['size'] ) AND $params['size'] == $old_name ) {
				$params['size'] = $new_name;
				$changed = TRUE;
			}
		}

		return $changed;
	}

	// Options
	public function translate_theme_options( &$options ) {

		$img_sizes = $this->get_image_sizes();

		if ( isset( $options['post_related_img_size'] ) AND $options['post_related_img_size'] == 'tnail-1x1' ) {
			$options['post_related_img_size'] = 'us_600_600_crop';
		}
		if ( isset( $options['post_related_img_size'] ) AND $options['post_related_img_size'] == 'tnail-1x1-small' ) {
			$options['post_related_img_size'] = 'us_350_350_crop';
		}

		if ( isset( $options['blog_img_size'] ) AND $options['blog_img_size'] == 'tnail-1x1' ) {
			$options['blog_img_size'] = 'us_600_600_crop';
		}
		if ( isset( $options['blog_img_size'] ) AND $options['blog_img_size'] == 'tnail-1x1-small' ) {
			$options['blog_img_size'] = 'us_350_350_crop';
		}

		if ( isset( $options['archive_img_size'] ) AND $options['archive_img_size'] == 'tnail-1x1' ) {
			$options['archive_img_size'] = 'us_600_600_crop';
		}
		if ( isset( $options['archive_img_size'] ) AND $options['archive_img_size'] == 'tnail-1x1-small' ) {
			$options['archive_img_size'] = 'us_350_350_crop';
		}

		if ( isset( $options['search_img_size'] ) AND $options['search_img_size'] == 'tnail-1x1' ) {
			$options['search_img_size'] = 'us_600_600_crop';
		}
		if ( isset( $options['search_img_size'] ) AND $options['search_img_size'] == 'tnail-1x1-small' ) {
			$options['search_img_size'] = 'us_350_350_crop';
		}

		foreach ( $img_sizes as $old_name => $new_name ) {
			if ( $options['post_related_img_size'] == $old_name ) {
				$options['post_related_img_size'] = $new_name;
			}

			if ( isset( $options['blog_img_size'] ) AND $options['blog_img_size'] == $old_name ) {
				$options['blog_img_size'] = $new_name;
			}

			if ( isset( $options['archive_img_size'] ) AND $options['archive_img_size'] == $old_name ) {
				$options['archive_img_size'] = $new_name;
			}

			if ( isset( $options['search_img_size'] ) AND $options['search_img_size'] == $old_name ) {
				$options['search_img_size'] = $new_name;
			}
		}

		$old_img_sizes = us_arr_path( $options, 'img_size', '' );
		if ( ! is_array( $old_img_sizes ) ) {
			$old_img_sizes = array();
		}

		$new_img_sizes = array(
			array(
				'width' => 350,
				'height' => 350,
				'crop' => array( '0' => 'crop' ),
			),
			array(
				'width' => 600,
				'height' => 600,
				'crop' => array( '0' => 'crop' ),
			),
		);

		foreach ( $old_img_sizes as $size ) {
			$new_img_sizes[] = $size;
		}

		$options['img_size'] = $new_img_sizes;

		// Regenerate sizes data for images
		$attachments = get_posts(
			array(
				'post_type' => 'attachment',
				'posts_per_page' => - 1,
				'post_status' => 'any',
				'numberposts' => - 1,
			)
		);
		foreach ( $attachments as $attachment ) {
			$attachment_ID = $attachment->ID;
			if ( is_array( $imagedata = wp_get_attachment_metadata( $attachment_ID ) ) ) {
				if ( isset ( $imagedata['sizes']['tnail-1x1-small'] ) ) {
					$imagedata['sizes']['us_350_350_crop'] = $imagedata['sizes']['tnail-1x1-small'];
				}
				if ( isset ( $imagedata['sizes']['tnail-1x1'] ) ) {
					$imagedata['sizes']['us_600_600_crop'] = $imagedata['sizes']['tnail-1x1'];
				}
				foreach ( $img_sizes as $old_name => $new_name ) {
					if ( isset ( $imagedata['sizes'][$old_name] ) ) {
						$imagedata['sizes'][$new_name] = $imagedata['sizes'][$old_name];
					}
				}
				wp_update_attachment_metadata( $attachment_ID, $imagedata );
			}
		}

		return TRUE;
	}

}
