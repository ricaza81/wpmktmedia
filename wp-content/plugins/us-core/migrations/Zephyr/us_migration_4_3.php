<?php

class us_migration_4_3 extends US_Migration_Translator {

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( ! empty( $options['enable_unsupported_vc_shortcodes'] ) AND $options['enable_unsupported_vc_shortcodes'] == 1 ) {
			$options['disable_extra_vc'] = 0;
		} else {
			$options['disable_extra_vc'] = 1;
		}

		unset( $options['enable_unsupported_vc_shortcodes'] );
		$changed = TRUE;


		return $changed;
	}

}
