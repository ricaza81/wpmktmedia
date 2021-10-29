<?php

class us_migration_3_8_2 extends US_Migration_Translator {

	// Dirty hack to use theme options function to migrate sliders
	public function translate_theme_options( &$options ) {
		$layers_changed = FALSE;

		global $wpdb;
		if($wpdb->get_var('SHOW TABLES LIKE "' . $wpdb->prefix . 'revslider_slides"') == $wpdb->prefix . 'revslider_slides' ) {
			$wpdb_query = 'SELECT `id`, `layers` FROM `' . $wpdb->prefix . 'revslider_slides`';

			foreach ( $wpdb->get_results( $wpdb_query ) as $row ) {
				$layers = $row->layers;

				$layers = json_decode( $layers, TRUE );

				foreach ( $layers as $id => $layer ) {
					if ( ! empty( $layer['text'] ) ) {
						$layer_text = $layer['text'];
						$text_changed = $this->_translate_content( $layer_text );
						if ( $text_changed ) {
							$layers[$id]['text'] = $layer_text;
							$layers_changed = TRUE;
						}
					}
				}

				if ( $layers_changed ) {
					$layers = json_encode( $layers );
					$wpdb->update(
						$wpdb->prefix . 'revslider_slides', array(
						'layers' => $layers,
					), array(
							'id' => $row->id,
						)
					);
				}
			}
		}

		return FALSE;
	}

	public function translate_us_btn( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['icon'] ) ) {
			$new_icon = $this->translate_icon_class( $params['icon'] );

			if ( $new_icon != $params['icon'] ) {
				$params['icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		if ( ! empty( $content ) ) {
			$content = '';
		}

		return $changed;
	}

	private function translate_icon_class( $icon_class ) {
		$icon_class = trim( $icon_class );
		if ( substr( $icon_class, 0, 4 ) == 'mdfi' ) {
			$icon_class = preg_replace( '/^mdfi_[^_]+_/', '', $icon_class );
		} elseif ( substr( $icon_class, 0, 3 ) != 'fa-' AND substr( $icon_class, 0, 3 ) != 'fa ' ) {
			$icon_class = 'fa-' . $icon_class;
		}

		return $icon_class;
	}
}
