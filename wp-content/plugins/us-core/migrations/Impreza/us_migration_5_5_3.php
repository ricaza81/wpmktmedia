<?php

class us_migration_5_5_3 extends US_Migration_Translator {

	// Grid Layouts
	public function translate_grid_layout_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $name => $data ) {

			// HTML element
			if ( substr( $name, 0, 4 ) == 'html' ) {
				// Check if maybe the HTML was already encoded
				if ( preg_match( '%^[a-zA-Z0-9/+]*={0,2}$%', $data['content'] ) ) {
					continue;
				}
				$settings['data'][ $name ]['content'] = base64_encode( rawurlencode( $data['content'] ) );
				$settings_changed = TRUE;
			}

		}

		return $settings_changed;
	}

	// Headers
	public function translate_header_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $name => $data ) {

			// HTML element
			if ( substr( $name, 0, 4 ) == 'html' ) {
				// Check if maybe the HTML was already encoded
				if ( preg_match( '%^[a-zA-Z0-9/+]*={0,2}$%', $data['content'] ) ) {
					continue;
				}
				$settings['data'][ $name ]['content'] = base64_encode( rawurlencode( $data['content'] ) );
				$settings_changed = TRUE;
			}

		}

		return $settings_changed;
	}

}
