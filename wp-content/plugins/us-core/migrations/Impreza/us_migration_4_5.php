<?php

class us_migration_4_5 extends US_Migration_Translator {

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( isset( $options['preloader'] ) AND $options['preloader'] == 2 ) {
			$options['preloader'] = 1;
			$changed = TRUE;
		}

		if ( ! empty( $options['heading_font_family'] ) ) {
			$result_variant = 0;
			$font = explode( '|', $options['heading_font_family'], 2 );
			if ( ! empty( $font[1] ) ) {
				$selected_font_variants = explode( ',', $font[1] );
				if ( is_array( $selected_font_variants ) ) {
					foreach ( $selected_font_variants as $variant ) {
						if ( strpos( $variant, 'italic' ) === FALSE AND ( $result_variant == 0 OR $result_variant > $variant ) ) {
							$result_variant = $variant;
						}
					}
				}
			}
			if ( $result_variant != 0 ) {
				$options['h1_fontweight'] = $result_variant;
				$options['h2_fontweight'] = $result_variant;
				$options['h3_fontweight'] = $result_variant;
				$options['h4_fontweight'] = $result_variant;
				$options['h5_fontweight'] = $result_variant;
				$options['h6_fontweight'] = $result_variant;
				$changed = TRUE;
			}
		}

		return $changed;
	}

}
