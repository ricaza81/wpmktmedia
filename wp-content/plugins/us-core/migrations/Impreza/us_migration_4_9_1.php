<?php

class us_migration_4_9_1 extends US_Migration_Translator {

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( isset( $options['disable_effects_width'] ) AND $options['disable_effects_width'] == 1024 ) {
			$options['disable_effects_width'] = 1025;
			$changed = TRUE;
		}
		if ( isset( $options['columns_stacking_width'] ) AND $options['columns_stacking_width'] == 1024 ) {
			$options['columns_stacking_width'] = 1025;
			$changed = TRUE;
		}
		if ( isset( $options['columns_stacking_width'] ) AND $options['columns_stacking_width'] == 767 ) {
			$options['columns_stacking_width'] = 768;
			$changed = TRUE;
		}

		return $changed;
	}

}
