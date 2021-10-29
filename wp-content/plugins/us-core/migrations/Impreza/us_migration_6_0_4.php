<?php

class us_migration_6_0_4 extends US_Migration_Translator {

	// Theme Options
	public function translate_theme_options( &$options ) {

		// old string from migration_5_6
		$text_fix_56_css = ".wpb_text_column:not(:last-child) { margin-bottom: 1.5rem; } /* migration 5.6 fix */ \n";

		if ( strpos( $options['custom_css'], $text_fix_56_css ) !== FALSE ) {
			$options['custom_css'] = str_replace( $text_fix_56_css, '', $options['custom_css'] );
		} else {
			$options['text_bottom_indent'] = '0rem';
		}

		return TRUE;
	}

}
