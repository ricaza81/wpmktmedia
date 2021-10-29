<?php

class us_migration_6_7 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	// Row
	public function translate_vc_row( &$name, &$params, &$content ) {
		global $us_row_is_fullwidth;
		$us_row_is_fullwidth = ( ! empty( $params['width'] ) AND $params['width'] == 'full' ) ? TRUE : FALSE;

		return FALSE;
	}

	// Column
	public function translate_vc_column( &$name, &$params, &$content ) {
		global $us_column_is_fullwidth;
		$us_column_is_fullwidth = ( ! isset( $params['width'] ) OR $params['width'] == '1/1' ) ? TRUE : FALSE;

		return FALSE;
	}

	// Grid
	public function translate_us_grid( &$name, &$params, &$content ) {
		global $us_row_is_fullwidth, $us_column_is_fullwidth;
		$changed = FALSE;

		// Add margins, only if the Row and Column is fullwidth
		if ( $us_row_is_fullwidth AND $us_column_is_fullwidth ) {

			if ( isset( $params['type'] ) AND $params['type'] == 'metro' ) {

				return FALSE; // in case METRO type, migration is not required

			} elseif ( isset( $params['items_gap'] ) ) {
				if ( empty( $params['items_gap'] ) ) {

					return FALSE; // in case no gap, migration is not required

				} else {
					$gap = preg_replace_callback( '/^(\d+(\.\d+)?)(.*?)$/u', function( $matches ) {
						$unit = $matches[1] * 2;
						return ( is_float( $unit ) ? rtrim( sprintf( "%.2f", $unit ), '.00' ) : $unit ) . $matches[3];
					}, trim( $params['items_gap'] ) );
				}
			} else {
				$gap = '3rem';
			}

			// If Design options were set append margins to the end of attribute's value
			if ( ! empty( $params['css'] ) ) {
				$params['css'] = str_replace( '}', 'padding-right: ' . $gap . ' !important;padding-left: ' . $gap . ' !important;}', $params['css'] );
			} else {
				$random_number = mt_rand( 111111, 999999 ); // generate random class
				$params['css'] = '.vc_custom_' . $random_number . '{padding-right: ' . $gap . ' !important;padding-left: ' . $gap . ' !important;}';
			}

			$changed = TRUE;
		}

		return $changed;
	}

}
