<?php

class us_migration_4_8 extends US_Migration_Translator {

	// Options
	public function translate_theme_options( &$options ) {
		if ( isset( $options['generate_css_file'] ) AND $options['generate_css_file'] ) {
			$upload_dir = wp_upload_dir();
			if ( wp_is_writable( $upload_dir['basedir'] ) ) {
				$options['optimize_assets'] = TRUE;
			}
		}
		unset( $options['generate_css_file'] );

		return TRUE;
	}

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_message( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['color'] ) AND $params['color'] == 'info' ) {
			$params['color'] = 'blue';
			$changed = TRUE;
		}
		if ( ! empty( $params['color'] ) AND $params['color'] == 'attention' ) {
			$params['color'] = 'yellow';
			$changed = TRUE;
		}
		if ( ! empty( $params['color'] ) AND $params['color'] == 'success' ) {
			$params['color'] = 'green';
			$changed = TRUE;
		}
		if ( ! empty( $params['color'] ) AND $params['color'] == 'error' ) {
			$params['color'] = 'red';
			$changed = TRUE;
		}

		return $changed;
	}

}
