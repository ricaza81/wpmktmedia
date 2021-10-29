<?php

class us_migration_5_1 extends US_Migration_Translator {

	/**
	 * Content
	 *
	 * @param string $content
	 * @return bool
	 */
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	/**
	 * Migrate for vc_row
	 *
	 * @param string $name
	 * @param array $params
	 * @param string $content
	 * @return bool
	 */
	public function translate_vc_row( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['us_bg_image'] ) AND $params['us_bg_image'] ) {
			$params['us_bg_image_source'] = 'media';
			$changed = TRUE;
		}

		return $changed;
	}

	/**
	 * Updates us_counter
	 *
	 * @param string $name
	 * @param array $params
	 * @param string $content
	 * @return bool
	 */
	public function translate_us_counter( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['size'] ) AND $params['size'] == 'small' ) {
			$params['size'] = '4rem';
			$changed = TRUE;
		}
		if ( isset( $params['size'] ) AND $params['size'] == 'large' ) {
			$params['size'] = '6rem';
			$changed = TRUE;
		}

		return $changed;
	}

	/**
	 * Updates us_iconbox
	 *
	 * @param string $name
	 * @param array $params
	 * @param string $content
	 * @return bool
	 */
	public function translate_us_iconbox( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['iconpos'] ) AND $params['iconpos'] == 'left' ) {
			$params['alignment'] = 'left';
			$changed = TRUE;
		}
		if ( isset( $params['img'] ) AND ! empty( $params['img'] ) AND isset( $params['style'] ) AND $params['style'] == 'circle' ) {
			unset( $params['style'] );
			$changed = TRUE;
		}

		return $changed;
	}

	/**
	 * Theme Options
	 *
	 * @param array $options
	 * @return bool
	 */
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		// 404 error page
		$error_404 = get_page_by_path( 'error-404' );
		if ( $error_404 ) {
			$options['page_404'] = $error_404->ID;
			$changed = TRUE;
		}

		// Maintenance page slug -> ID
		if ( ! empty( $options['maintenance_page'] ) ) {
			$maintenance_page = get_page_by_path( $options['maintenance_page'] );
			if ( $maintenance_page ) {
				$options['maintenance_page'] = $maintenance_page->ID;
				$changed = TRUE;
			}
		}

		// Portfolio breadcrumbs page slug -> ID
		if ( ! empty( $options['portfolio_breadcrumbs_page'] ) ) {
			$portfolio_breadcrumbs_page = get_page_by_path( $options['portfolio_breadcrumbs_page'] );
			if ( $portfolio_breadcrumbs_page ) {
				$options['portfolio_breadcrumbs_page'] = $portfolio_breadcrumbs_page->ID;
				$changed = TRUE;
			}
		}

		// Shop Title and Breadcrumbs
		if ( isset( $options['shop_remove_title_breadcrumbs'] ) AND $options['shop_remove_title_breadcrumbs'] ) {
			$options['shop_elements'] = array();
			$changed = TRUE;
		}

		return $changed;
	}
}
