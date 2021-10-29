<?php

class us_migration_5_2 extends US_Migration_Translator {

	// Headers
	public function translate_header_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $name => $data ) {

			// Find the text element
			if ( substr( $name, 0, 4 ) == 'text' ) {
				if ( ! empty( $data['text'] ) AND strpos( $data['text'], '<strong' ) !== FALSE ) {
					if ( empty( $settings['data'][ $name ]['text_style'] ) ) {
						$settings['data'][ $name ]['text_style'] = array();
					}
					if ( ! in_array( 'bold', $settings['data'][ $name ]['text_style'] ) ) {
						$settings['data'][ $name ]['text_style'][] = 'bold';
					}
					$settings_changed = TRUE;

				}
			}

		}

		return $settings_changed;
	}
}
