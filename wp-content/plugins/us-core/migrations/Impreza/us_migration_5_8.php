<?php

class us_migration_5_8 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_post_content( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_comments( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_taxonomy( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_custom_field( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_date( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_author( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_post_title( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_page_title( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_itext( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_us_counter( &$name, &$params, &$content ) {
		$changed = $this->migrate_text_styles( $params ) OR FALSE;

		return $changed;
	}

	public function translate_vc_tta_tabs( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['title_text_styles'] ) ) {

			if ( strpos( $params['title_text_styles'], 'bold' ) !== FALSE ) {
				$params['title_weight'] = '700';
			}
			if ( strpos( $params['title_text_styles'], 'uppercase' ) !== FALSE ) {
				$params['title_transform'] = 'uppercase';
			}

			unset( $params['title_text_styles'] );

			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_vc_tta_tour( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['title_text_styles'] ) ) {

			if ( strpos( $params['title_text_styles'], 'bold' ) !== FALSE ) {
				$params['title_weight'] = '700';
			}
			if ( strpos( $params['title_text_styles'], 'uppercase' ) !== FALSE ) {
				$params['title_transform'] = 'uppercase';
			}

			unset( $params['title_text_styles'] );

			$changed = TRUE;
		}

		return $changed;
	}

	// Grid Layouts
	public function translate_grid_layout_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $name => $data ) {
			// Check font
			if ( ! empty( $data['font'] ) AND $data['font'] == 'heading' ) {
				$settings['data'][ $name ]['font'] = 'h1';

				$settings_changed = TRUE;
			}
			// Check text styles
			if ( ! empty( $data['text_styles'] ) AND is_array( $data['text_styles'] ) ) {
				if ( in_array( 'bold', $data['text_styles'] ) ) {
					$settings['data'][ $name ]['font_weight'] = '700';
				}
				if ( in_array( 'uppercase', $data['text_styles'] ) ) {
					$settings['data'][ $name ]['text_transform'] = 'uppercase';
				}
				if ( in_array( 'italic', $data['text_styles'] ) ) {
					$settings['data'][ $name ]['font_style'] = 'italic';
				}

				unset( $settings['data'][ $name ]['text_styles'] );

				$settings_changed = TRUE;
			}

		}

		return $settings_changed;
	}

	// Headers
	public function translate_header_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $name => $data ) {

			// Text element
			if ( substr( $name, 0, 4 ) == 'text' ) {
				if ( ! empty( $data['font'] ) AND $data['font'] == 'heading' ) {
					$settings['data'][ $name ]['font'] = 'h1';

					$settings_changed = TRUE;
				}
				// Check text styles
				if ( ! empty( $data['text_styles'] ) AND is_array( $data['text_styles'] ) ) {
					if ( in_array( 'bold', $data['text_styles'] ) ) {
						$settings['data'][ $name ]['font_weight'] = '700';
					}
					if ( in_array( 'uppercase', $data['text_styles'] ) ) {
						$settings['data'][ $name ]['text_transform'] = 'uppercase';
					}
					if ( in_array( 'italic', $data['text_styles'] ) ) {
						$settings['data'][ $name ]['font_style'] = 'italic';
					}

					unset( $settings['data'][ $name ]['text_styles'] );

					$settings_changed = TRUE;
				}
			}

			// Menu element
			if ( substr( $name, 0, 4 ) == 'menu' ) {
				if ( ! empty( $data['font'] ) AND $data['font'] == 'heading' ) {
					$settings['data'][ $name ]['font'] = 'h1';

					$settings_changed = TRUE;
				}
				// Check text style
				if ( ! empty( $data['text_style'] ) AND is_array( $data['text_style'] ) ) {
					if ( in_array( 'bold', $data['text_style'] ) ) {
						$settings['data'][ $name ]['font_weight'] = '700';
					}
					if ( in_array( 'uppercase', $data['text_style'] ) ) {
						$settings['data'][ $name ]['text_transform'] = 'uppercase';
					}
					if ( in_array( 'italic', $data['text_style'] ) ) {
						$settings['data'][ $name ]['font_style'] = 'italic';
					}

					unset( $settings['data'][ $name ]['text_style'] );

					$settings_changed = TRUE;
				}
			}

		}

		return $settings_changed;
	}

	// Theme Options
	public function translate_theme_options( &$options ) {

		if ( isset( $options['heading_font_family'] ) ) {
			$options['h1_font_family'] = $options['heading_font_family'];
			unset( $options['heading_font_family'] );
		}

		return TRUE;
	}

	// Common Text Styles migration
	private function migrate_text_styles( &$params ) {
		$changed = FALSE;

		if ( ! empty( $params['font'] ) AND $params['font'] == 'heading' ) {

			$params['font'] = 'h1';

			$changed = TRUE;
		}
		if ( ! empty( $params['text_styles'] ) ) {

			if ( strpos( $params['text_styles'], 'bold' ) !== FALSE ) {
				$params['font_weight'] = '700';
			}
			if ( strpos( $params['text_styles'], 'uppercase' ) !== FALSE ) {
				$params['text_transform'] = 'uppercase';
			}
			if ( strpos( $params['text_styles'], 'italic' ) !== FALSE ) {
				$params['font_style'] = 'italic';
			}

			unset( $params['text_styles'] );

			$changed = TRUE;
		}

		return $changed;
	}

}
