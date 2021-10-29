<?php

class us_migration_4_2 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_blog( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['masonry'] ) AND $params['masonry'] == 1 ) {
			$params['type'] = 'masonry';
			unset( $params['masonry'] );
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_portfolio( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['ratio'] ) AND $params['ratio'] == 'initial' ) {
			$params['type'] = 'masonry';
			unset( $params['ratio'] );
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_testimonials( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['arrows'] ) ) {
			$params['carousel_arrows'] = $params['arrows'];
			unset( $params['arrows'] );
			$changed = TRUE;
		}

		if ( isset( $params['dots'] ) ) {
			$params['carousel_dots'] = $params['dots'];
			unset( $params['dots'] );
			$changed = TRUE;
		}

		if ( isset( $params['auto_scroll'] ) ) {
			$params['carousel_autoplay'] = $params['auto_scroll'];
			unset( $params['auto_scroll'] );
			$changed = TRUE;
		}

		if ( isset( $params['interval'] ) ) {
			$params['carousel_interval'] = $params['interval'];
			unset( $params['interval'] );
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_logos( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['arrows'] ) ) {
			$params['carousel_arrows'] = $params['arrows'];
			unset( $params['arrows'] );
			$changed = TRUE;
		}

		if ( isset( $params['dots'] ) ) {
			$params['carousel_dots'] = $params['dots'];
			unset( $params['dots'] );
			$changed = TRUE;
		}

		if ( isset( $params['auto_scroll'] ) ) {
			$params['carousel_autoplay'] = $params['auto_scroll'];
			unset( $params['auto_scroll'] );
			$changed = TRUE;
		}

		if ( isset( $params['interval'] ) ) {
			$params['carousel_interval'] = $params['interval'];
			unset( $params['interval'] );
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_image_slider( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['autoplay_period'] ) AND intval( $params['autoplay_period'] ) != 0 ) {
			$params['autoplay_period'] = intval( $params['autoplay_period'] ) / 1000;
			$changed = TRUE;
		}

		return $changed;
	}

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( ! empty( $options['blog_masonry'] ) AND $options['blog_masonry'] == 1 ) {
			$options['blog_type'] = 'masonry';
			unset( $options['blog_masonry'] );
			$changed = TRUE;
		}

		if ( ! empty( $options['archive_masonry'] ) AND $options['archive_masonry'] == 1 ) {
			$options['archive_type'] = 'masonry';
			unset( $options['archive_masonry'] );
			$changed = TRUE;
		}

		if ( ! empty( $options['search_masonry'] ) AND $options['search_masonry'] == 1 ) {
			$options['search_type'] = 'masonry';
			unset( $options['search_masonry'] );
			$changed = TRUE;
		}


		return $changed;
	}

}
