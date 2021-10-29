<?php

class us_migration_4_9 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_logos( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( empty( $params['carousel_autoplay'] ) OR $params['carousel_autoplay'] == 0 ) {
			$params['breakpoint_1_autoplay'] = 0;
			$params['breakpoint_2_autoplay'] = 0;
			$params['breakpoint_3_autoplay'] = 0;
			$changed = TRUE;
		}

		return $changed;
	}

	// Options
	public function translate_theme_options( &$options ) {
		update_option( US_THEMENAME . '_editor_caps_set', 1 );

		$changed = FALSE;

		if ( isset( $options['h1_fontsize'] ) AND isset( $options['h1_letterspacing'] ) AND $options['h1_letterspacing'] != 0 ) {
			$options['h1_letterspacing'] = round( $options['h1_letterspacing'] / $options['h1_fontsize'], 2 );
			$changed = TRUE;
		}

		if ( isset( $options['h2_fontsize'] ) AND isset( $options['h2_letterspacing'] ) AND $options['h2_letterspacing'] != 0 ) {
			$options['h2_letterspacing'] = round( $options['h2_letterspacing'] / $options['h2_fontsize'], 2 );
			$changed = TRUE;
		}

		if ( isset( $options['h3_fontsize'] ) AND isset( $options['h3_letterspacing'] ) AND $options['h3_letterspacing'] != 0 ) {
			$options['h3_letterspacing'] = round( $options['h3_letterspacing'] / $options['h3_fontsize'], 2 );
			$changed = TRUE;
		}

		if ( isset( $options['h4_fontsize'] ) AND isset( $options['h4_letterspacing'] ) AND $options['h4_letterspacing'] != 0 ) {
			$options['h4_letterspacing'] = round( $options['h4_letterspacing'] / $options['h4_fontsize'], 2 );
			$changed = TRUE;
		}

		if ( isset( $options['h5_fontsize'] ) AND isset( $options['h5_letterspacing'] ) AND $options['h5_letterspacing'] != 0 ) {
			$options['h5_letterspacing'] = round( $options['h5_letterspacing'] / $options['h5_fontsize'], 2 );
			$changed = TRUE;
		}

		if ( isset( $options['h6_fontsize'] ) AND isset( $options['h6_letterspacing'] ) AND $options['h6_letterspacing'] != 0 ) {
			$options['h6_letterspacing'] = round( $options['h6_letterspacing'] / $options['h6_fontsize'], 2 );
			$changed = TRUE;
		}

		if ( isset( $options['button_fontsize'] ) AND isset( $options['button_letterspacing'] ) AND $options['button_letterspacing'] != 0 ) {
			$options['button_letterspacing'] = round( $options['button_letterspacing'] / $options['button_fontsize'], 2 );
			$changed = TRUE;
		}

		if ( isset( $options['button_hover'] ) AND $options['button_hover'] == 'shadow' ) {
			$options['button_hover'] = 'none';
			$options['button_shadow_hover'] = 1;
			$changed = TRUE;
		}

		return $changed;
	}

}
