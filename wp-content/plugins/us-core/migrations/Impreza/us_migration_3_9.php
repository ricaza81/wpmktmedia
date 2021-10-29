<?php

class us_migration_3_9 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_vc_row( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['columns_type'] ) ) {
			if ( $params['columns_type'] == 'small' ) {
				unset( $params['columns_type'] );
				$changed = TRUE;
			} elseif ( $params['columns_type'] == 'medium' ) {
				unset( $params['columns_type'] );
				$params['gap'] = '20';
				$changed = TRUE;
			} elseif ( $params['columns_type'] == 'large' ) {
				unset( $params['columns_type'] );
				$params['gap'] = '35';
				$changed = TRUE;
			} elseif ( $params['columns_type'] == 'none' ) {
				$params['columns_type'] = 'boxes';
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_vc_row_inner( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['columns_type'] ) ) {
			if ( $params['columns_type'] == 'small' ) {
				unset( $params['columns_type'] );
				$changed = TRUE;
			} elseif ( $params['columns_type'] == 'medium' ) {
				unset( $params['columns_type'] );
				$params['gap'] = '20';
				$changed = TRUE;
			} elseif ( $params['columns_type'] == 'large' ) {
				unset( $params['columns_type'] );
				$params['gap'] = '35';
				$changed = TRUE;
			} elseif ( $params['columns_type'] == 'none' ) {
				$params['columns_type'] = 'boxes';
				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_testimonial( &$name, &$params, &$content ) {
		$existing_testimonials_posts = get_posts( array( 'post_type' => 'us_testimonial', 'posts_per_page' => -1 ) );
		$existing_testimonials = array();

		foreach ( $existing_testimonials_posts as $testimonials_post ) {
			$existing_testimonials[$testimonials_post->post_title] = $testimonials_post->post_content;
		}

		if ( in_array( $content, $existing_testimonials ) ) {
			return FALSE;
		}

		$testimonial_number = 100;

		$testimonial_title = 'Testimonial ' . $testimonial_number;

		while ( isset( $existing_testimonials[$testimonial_title] ) AND $testimonial_number > 0 ) {
			$testimonial_number--;
			$testimonial_title = 'Testimonial ' . $testimonial_number;
		}

		$testimonials_post_array = array(
			'post_type' => 'us_testimonial',
			'post_date' => date( 'Y-m-d H:i', time() - ( 101 - $testimonial_number ) * 86400 ),
			'post_title' => $testimonial_title,
			'post_content' => $content,
			'post_status' => 'publish',
		);

		$testimonials_post_id = wp_insert_post( $testimonials_post_array );

		if ( ! empty( $params['author'] ) ) {
			update_post_meta( $testimonials_post_id, 'us_testimonial_author', $params['author'] );
		}

		if ( ! empty( $params['company'] ) ) {
			update_post_meta( $testimonials_post_id, 'us_testimonial_role', $params['company'] );
		}

		if ( ! empty( $params['link'] ) ) {
			update_post_meta( $testimonials_post_id, 'us_testimonial_link', $params['link'] );
		}

		if ( ! empty( $params['img'] ) ) {
			set_post_thumbnail( $testimonials_post_id, $params['img'] );
		}

		return FALSE;
	}

}
