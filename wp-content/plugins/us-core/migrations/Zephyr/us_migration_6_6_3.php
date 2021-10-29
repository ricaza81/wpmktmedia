<?php

class us_migration_6_6_3 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	// Row
	public function translate_vc_row( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['us_bg_slider'] ) ) {
			$params['us_bg_show'] = 'rev_slider';
			$params['us_bg_rev_slider'] = $params['us_bg_slider'];

			unset( $params['us_bg_slider'] );

			$changed = TRUE;

		} elseif ( ! empty( $params['us_bg_video'] ) ) {
			$params['us_bg_show'] = 'video';

			$changed = TRUE;
		}

		return $changed;
	}

	// Product data
	public function translate_us_product_field( &$name, &$params, &$content ) {
		$changed = FALSE;

		// Translate "pa_" attributes to Post Taxonomy element
		if ( isset( $params['type'] ) AND strpos( $params['type'], 'pa_' ) !== FALSE ) {
			$name = 'us_post_taxonomy';
			$params['taxonomy_name'] = $params['type'];
			$params['link'] = 'none';

			// Add Taxonomy label in front of the values
			if ( $tax_object = get_taxonomy( $params['type'] ) ) {
				$tax_label = isset( $tax_object->labels->singular_name ) ? $tax_object->labels->singular_name : $tax_object->label;
				$params['text_before'] = $tax_label . ':';
			}

			unset( $params['type'] );

			$changed = TRUE;
		}

		if ( isset( $params['type'] ) AND $params['type'] == 'custom_atts' ) {
			$params['type'] = 'attributes';

			$changed = TRUE;
		}

		return $changed;
	}

	// Grid
	public function translate_us_grid( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['orderby'] ) AND $params['orderby'] == 'menu_order' ) {
			$params['order_invert'] = ( ! empty( $params['order_invert'] ) ) ? '' : '1';

			$changed = TRUE;
		}

		return $changed;
	}

	// Carousel
	public function translate_us_carousel( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['orderby'] ) AND $params['orderby'] == 'menu_order' ) {
			$params['order_invert'] = ( ! empty( $params['order_invert'] ) ) ? '' : '1';

			$changed = TRUE;
		}

		return $changed;
	}
}
