<?php

class us_migration_4_8 extends US_Migration_Translator {

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
