<?php

class us_migration_4_1_2 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_gmaps( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['api_key'] ) ) {
			global $usof_options;
			usof_load_options_once();

			$usof_options['gmaps_api_key'] = $params['api_key'];
			remove_action( 'usof_after_save', 'us_generate_asset_files' );
			usof_save_options( $usof_options );
			add_action( 'usof_after_save', 'us_generate_asset_files' );

			unset( $params['api_key'] );
			$changed = TRUE;
		}

		return $changed;
	}

}
