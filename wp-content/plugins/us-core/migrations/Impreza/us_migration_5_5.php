<?php

class us_migration_5_5 extends US_Migration_Translator {

	private $button_colors;

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_vc_row( &$name, &$params, &$content ) {
		global $us_row_color_scheme;
		$us_row_color_scheme = ( ! empty( $params['color_scheme'] ) AND in_array(
				$params['color_scheme'], array(
					'primary',
					'secondary',
					'custom',
					'alternate',
				)
			) ) ? $params['color_scheme'] : 'other';

		return FALSE;
	}

	public function translate_us_pricing( &$name, &$params, &$content ) {
		$changed = FALSE;

		$this->set_button_colors();

		$items = json_decode( urldecode( $params['items'] ), TRUE );

		if ( is_array( $items ) AND count( $items ) ) {
			foreach ( $items as $index => $item ) {
				// If there is no button label, the button is not displayed, no need in migration
				if ( empty( $item['btn_text'] ) ) {
					continue;
				}
				// Fail-safe if this button was already migrated
				if ( ! empty( $item['btn_style'] ) AND intval( $item['btn_style'] ) > 0 ) {
					continue;
				}

				if ( ! isset( $item['btn_color'] ) OR ! in_array( $item['btn_color'], array_keys( $this->button_colors ) ) ) {
					$items[ $index ]['btn_color'] = 'primary';
				}
				$items[ $index ]['btn_color'] = $this->filter_btn_color( $items[ $index ]['btn_color'] );

				if ( isset( $item['btn_style'] ) AND $item['btn_style'] == 'outlined' AND $item['btn_color'] != 'transparent' ) {
					$items[ $index ]['btn_style'] = $this->maybe_add_button_style( $items[ $index ]['btn_color'], 'outlined' );
				} else {
					$items[ $index ]['btn_style'] = $this->maybe_add_button_style( $items[ $index ]['btn_color'], 'solid' );
				}

				if ( empty( $item['btn_size'] ) ) {
					$items[ $index ]['btn_size'] = $this->btn_size();
				}

				unset( $items[ $index ]['btn_color'] );

				$changed = TRUE;
			}
		}

		if ( $changed ) {
			$params['items'] = urlencode( json_encode( $items ) );
		}

		return $changed;
	}

	public function translate_us_cform( &$name, &$params, &$content ) {
		// Fail-safe if this form was already migrated
		if ( ! empty( $params['button_style'] ) AND intval( $params['button_style'] ) > 0 ) {
			return FALSE;
		}

		$this->set_button_colors();

		if ( ! isset( $params['button_color'] ) OR ! in_array( $params['button_color'], array_keys( $this->button_colors ) ) ) {
			$params['button_color'] = 'primary';
		}
		$params['button_color'] = $this->filter_btn_color( $params['button_color'] );

		if ( isset( $params['button_style'] ) AND $params['button_style'] == 'outlined' AND $params['button_color'] != 'transparent' ) {
			$params['button_style'] = $this->maybe_add_button_style( $params['button_color'], 'outlined' );
		} else {
			$params['button_style'] = $this->maybe_add_button_style( $params['button_color'], 'solid' );
		}

		if ( empty( $params['button_size'] ) ) {
			$params['button_size'] = $this->btn_size();
		}

		unset( $params['button_color'] );

		return TRUE;
	}

	public function translate_us_grid( &$name, &$params, &$content ) {
		// Fail-safe if this grid was already migrated
		if ( ! empty( $params['pagination_btn_style'] ) AND intval( $params['pagination_btn_style'] ) > 0 ) {
			return FALSE;
		}

		$changed = FALSE;

		$this->set_button_colors();

		if ( isset( $params['pagination'] ) AND $params['pagination'] == 'ajax' ) {

			if ( isset( $params['pagination_btn_style'] ) AND $params['pagination_btn_style'] == 'link' ) {
				$params['pagination_btn_style'] = $this->maybe_add_button_style( 'transparent', 'solid' );
			} elseif ( isset( $params['pagination_btn_style'] ) AND $params['pagination_btn_style'] == 'btn' ) {
				$params['pagination_btn_style'] = $this->maybe_add_button_style( 'primary', 'solid' );
				$params['pagination_btn_size'] = $this->btn_size();
			} else {
				$params['pagination_btn_style'] = $this->maybe_add_button_style( 'loadmore', 'solid' );
				$params['pagination_btn_size'] = '1.2rem';
				$params['pagination_btn_fullwidth'] = 1;
			}
			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_cta( &$name, &$params, &$content ) {
		// Fail-safe if this action box was already migrated
		if ( ! empty( $params['btn_style'] ) AND intval( $params['btn_style'] ) > 0 ) {
			return FALSE;
		}

		$changed = FALSE;

		$this->set_button_colors();

		// Button 1
		if ( ! empty( $params['btn_label'] ) ) {
			if ( ! isset( $params['btn_color'] ) OR ! in_array( $params['btn_color'], array_keys( $this->button_colors ) ) ) {
				$params['btn_color'] = 'white';
			}
			$params['btn_color'] = $this->filter_btn_color( $params['btn_color'] );

			if ( isset( $params['btn_style'] ) AND $params['btn_style'] == 'outlined' AND $params['btn_color'] != 'transparent' ) {
				$params['btn_style'] = $this->maybe_add_button_style( $params['btn_color'], 'outlined' );
			} else {
				$params['btn_style'] = $this->maybe_add_button_style( $params['btn_color'], 'solid' );
			}

			if ( empty( $params['btn_size'] ) ) {
				$params['btn_size'] = $this->btn_size();
			}

			unset( $params['btn_color'] );

			$changed = TRUE;
		}


		// Button 2
		if ( isset( $params['second_button'] ) AND $params['second_button'] AND ! empty( $params['btn2_label'] ) ) {
			if ( ! isset( $params['btn2_color'] ) OR ! in_array( $params['btn2_color'], array_keys( $this->button_colors ) ) ) {
				$params['btn2_color'] = 'secondary';
			}
			$params['btn2_color'] = $this->filter_btn_color( $params['btn2_color'] );

			if ( isset( $params['btn2_style'] ) AND $params['btn2_style'] == 'outlined' AND $params['btn2_color'] != 'transparent' ) {
				$params['btn2_style'] = $this->maybe_add_button_style( $params['btn2_color'], 'outlined' );
			} else {
				$params['btn2_style'] = $this->maybe_add_button_style( $params['btn2_color'], 'solid' );
			}

			if ( empty( $params['btn2_size'] ) ) {
				$params['btn2_size'] = $this->btn_size();
			}

			unset( $params['btn2_color'] );

			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_us_btn( &$name, &$params, &$content ) {
		// Fail-safe if this button was already migrated
		if ( ! empty( $params['style'] ) AND intval( $params['style'] ) > 0 ) {
			return FALSE;
		}

		$this->set_button_colors();

		if ( ! isset( $params['color'] ) OR ! in_array( $params['color'], array_keys( $this->button_colors ) ) ) {
			$params['color'] = 'primary';
		}
		$params['color'] = $this->filter_btn_color( $params['color'] );

		if ( isset( $params['style'] ) AND $params['style'] == 'outlined' AND $params['color'] != 'transparent' ) {
			$params['style'] = $this->maybe_add_button_style( $params['color'], 'outlined' );
		} else {
			$params['style'] = $this->maybe_add_button_style( $params['color'], 'solid' );
		}

		if ( empty( $params['size'] ) ) {
			$params['size'] = $this->btn_size();
		}

		unset( $params['color'] );

		return TRUE;
	}

	// Grid Layouts
	public function translate_grid_layout_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $name => $data ) {

			// Button element
			if ( substr( $name, 0, 3 ) == 'btn' ) {
				// Fail-safe if this button was already migrated
				if ( empty( $data['color'] ) and ! empty( $data['style'] ) AND intval( $data['style'] ) > 0 ) {
					continue;
				}

				if ( ! isset( $data['color'] ) OR ! in_array( $data['color'], array_keys( $this->button_colors ) ) ) {
					$data['color'] = 'light';
				}

				if ( isset( $data['style'] ) AND $data['style'] == 'outlined' ) {
					$settings['data'][ $name ]['style'] = $this->maybe_add_button_style( $data['color'], 'outlined' );
				} else {
					$settings['data'][ $name ]['style'] = $this->maybe_add_button_style( $data['color'], 'solid' );
				}

				unset( $settings['data'][ $name ]['color'] );

				$settings_changed = TRUE;
			}

		}

		return $settings_changed;
	}

	// Headers
	public function translate_header_settings( &$settings ) {
		global $usof_options;

		$settings_changed = FALSE;
		$header_btn_index = 0;

		foreach ( $settings['data'] as $name => $data ) {

			// Button element
			if ( substr( $name, 0, 3 ) == 'btn' ) {
				// Fail-safe if this button was already migrated
				if ( empty( $data['color_bg'] ) OR empty( $data['color_text'] ) ) {
					continue;
				}
				$header_btn_index ++;
				$this->button_colors[ 'header_' . $header_btn_index ] = array(
					'name' => 'Header ' . $header_btn_index,
					'color_bg' => $data['color_bg'],
					'color_text' => $data['color_text'],
					'color_bg_hover' => $data['color_hover_bg'],
					'color_text_hover' => $data['color_hover_text'],
				);

				if ( isset( $data['style'] ) AND $data['style'] == 'outlined' ) {
					$settings['data'][ $name ]['style'] = $this->maybe_add_button_style( 'header_' . $header_btn_index, 'outlined' );
				} else {
					$settings['data'][ $name ]['style'] = $this->maybe_add_button_style( 'header_' . $header_btn_index, 'solid' );
				}

				unset( $settings['data'][ $name ]['color_bg'] );
				unset( $settings['data'][ $name ]['color_text'] );
				unset( $settings['data'][ $name ]['color_hover_bg'] );
				unset( $settings['data'][ $name ]['color_hover_text'] );

				$settings_changed = TRUE;
			}
			// Cart element
			if ( substr( $name, 0, 4 ) == 'cart' ) {
				$settings['data'][ $name ]['quantity_color_bg'] = $usof_options['color_menu_button_bg'];
				$settings['data'][ $name ]['quantity_color_text'] = $usof_options['color_menu_button_text'];

				$settings_changed = TRUE;
			}

		}

		// Change tablets & mobiles breakpoints plus one
		if ( isset( $settings['tablets']['options']['breakpoint'] ) ) {
			$settings['tablets']['options']['breakpoint'] = intval( $settings['tablets']['options']['breakpoint'] ) + 1;
			$settings_changed = TRUE;
		}
		if ( isset( $settings['mobiles']['options']['breakpoint'] ) ) {
			$settings['mobiles']['options']['breakpoint'] = intval( $settings['mobiles']['options']['breakpoint'] ) + 1;
			$settings_changed = TRUE;
		}

		return $settings_changed;
	}

	// Theme Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		$this->del_previous_buttons();

		$this->set_button_colors();

		// Add Default button style always FIRST
		$this->button_colors['default'] = array(
			'name' => 'Default Button',
		);
		$this->maybe_add_button_style( 'default', 'solid' );

		// Add "Light" button style always SECOND
		$this->maybe_add_button_style( 'light', 'solid' );

		/*
		 * Adding "Leaflet" checkbox if Optimize JS and CSS option is ON
		 */
		if ( isset( $options['optimize_assets'] ) AND $options['optimize_assets'] == 1 AND is_array( $options['assets'] ) ) {
			$options['assets'] = array_unique( array_merge( $options['assets'], array( 'lmaps' ) ) );
		}

		return $changed;
	}

	// Widgets
	public function translate_widgets( &$name, &$instance ) {
		$changed = FALSE;

		if ( $name == 'text' ) {
			$text = $instance['text'];
			if ( $this->translate_content( $text ) ) {
				$instance['text'] = $text;

				$changed = TRUE;
			}
		}

		return $changed;
	}

	/**
	 * Apply substyle to button color depending on parent vc_row colors
	 */
	private function filter_btn_color( $color ) {
		global $us_row_color_scheme;
		if ( ! empty( $us_row_color_scheme ) AND $us_row_color_scheme == 'alternate' AND in_array(
				$color, array(
				'primary',
				'secondary',
				'contrast',
				'light',
				'transparent',
			)
			) ) {
			$filtered_color = $color . '_alt';
		}
		if ( ! empty( $us_row_color_scheme ) AND in_array(
				$us_row_color_scheme, array(
				'primary',
				'secondary',
				'custom',
			)
			) AND $color == 'light' ) {
			$filtered_color = 'semitransparent';
		}
		if ( empty( $filtered_color ) OR ! in_array( $filtered_color, array_keys( $this->button_colors ) ) ) {
			$filtered_color = $color;
		}

		return $filtered_color;
	}

	/**
	 * Set values to button_colors array
	 */
	private function set_button_colors() {
		if ( empty( $this->button_colors ) ) {
			$this->button_colors = array(
				'primary' => array(
					'name' => 'Primary',
					'color_bg' => us_get_option( 'color_content_primary' ),
					'color_text' => '#ffffff',
				),
				'secondary' => array(
					'name' => 'Secondary',
					'color_bg' => us_get_option( 'color_content_secondary' ),
					'color_text' => '#ffffff',
				),
				'light' => array(
					'name' => 'Light',
					'color_bg' => us_get_option( 'color_content_border' ),
					'color_text' => us_get_option( 'color_content_text' ),
					'color_bg_hover' => 'rgba(0,0,0,0.05)',
				),
				'contrast' => array(
					'name' => 'Dark',
					'color_bg' => us_get_option( 'color_content_text' ),
					'color_text' => us_get_option( 'color_content_bg' ),
				),
				'black' => array(
					'name' => 'Black',
					'color_bg' => '#000000',
					'color_text' => '#ffffff',
					'color_bg_hover' => 'rgba(255,255,255,0.1)',
				),
				'white' => array(
					'name' => 'White',
					'color_bg' => '#ffffff',
					'color_text' => '#333333',
					'color_bg_hover' => 'rgba(0,0,0,0.08)',
				),
				'purple' => array(
					'name' => 'Purple',
					'color_bg' => '#8560a8',
					'color_text' => '#ffffff',
				),
				'pink' => array(
					'name' => 'Pink',
					'color_bg' => '#ff6b77',
					'color_text' => '#ffffff',
				),
				'red' => array(
					'name' => 'Red',
					'color_bg' => '#ff4400',
					'color_text' => '#ffffff',
				),
				'yellow' => array(
					'name' => 'Yellow',
					'color_bg' => '#fac000',
					'color_text' => '#ffffff',
				),
				'lime' => array(
					'name' => 'Lime',
					'color_bg' => '#baeb59',
					'color_text' => '#606652',
					'color_bg_hover' => 'rgba(0,0,0,0.08)',
				),
				'green' => array(
					'name' => 'Green',
					'color_bg' => '#59ba41',
					'color_text' => '#ffffff',
				),
				'teal' => array(
					'name' => 'Teal',
					'color_bg' => '#008b83',
					'color_text' => '#ffffff',
				),
				'blue' => array(
					'name' => 'Blue',
					'color_bg' => '#5ac8ed',
					'color_text' => '#ffffff',
				),
				'navy' => array(
					'name' => 'Navy',
					'color_bg' => '#1265a8',
					'color_text' => '#ffffff',
				),
				'midnight' => array(
					'name' => 'Midnight',
					'color_bg' => '#2c3e50',
					'color_text' => '#ffffff',
					'color_bg_hover' => 'rgba(0,0,0,0.2)',
				),
				'brown' => array(
					'name' => 'Brown',
					'color_bg' => '#6a4530',
					'color_text' => '#ffffff',
				),
				'cream' => array(
					'name' => 'Cream',
					'color_bg' => '#ffe2bf',
					'color_text' => '#65584c',
					'color_bg_hover' => 'rgba(0,0,0,0.08)',
				),
				'transparent' => array(
					'name' => 'Text Link',
					'color_bg' => '',
					'color_text' => us_get_option( 'color_content_link' ),
					'color_bg_hover' => '',
					'color_text_hover' => us_get_option( 'color_content_link_hover' ),
				),
				'loadmore' => array(
					'name' => 'Load More Button',
					'color_bg' => us_get_option( 'color_content_bg_alt' ),
					'color_text' => us_get_option( 'color_content_text' ),
					'color_bg_hover' => us_get_option( 'color_content_border' ),
				),
				'semitransparent' => array(
					'name' => 'Semitransparent',
					'color_bg' => 'rgba(255,255,255,0.15)',
					'color_border' => 'rgba(255,255,255,0.33)',
					'color_text' => '#fff',
					'color_bg_hover' => 'rgba(255,255,255,0.33)',
				),
			);

			if ( us_get_option( 'color_content_primary' ) != us_get_option( 'color_alt_content_primary' ) ) {
				$this->button_colors['primary_alt'] = array(
					'name' => 'Alternate Primary',
					'color_bg' => us_get_option( 'color_alt_content_primary' ),
					'color_text' => '#ffffff',
				);
			}

			if ( us_get_option( 'color_content_secondary' ) != us_get_option( 'color_alt_content_secondary' ) ) {
				$this->button_colors['secondary_alt'] = array(
					'name' => 'Alternate Secondary',
					'color_bg' => us_get_option( 'color_alt_content_secondary' ),
					'color_text' => '#ffffff',
				);
			}

			if ( us_get_option( 'color_content_text' ) != us_get_option( 'color_alt_content_text' ) OR us_get_option( 'color_content_bg' ) != us_get_option( 'color_alt_content_bg' ) ) {
				$this->button_colors['contrast_alt'] = array(
					'name' => 'Alternate Dark',
					'color_bg' => us_get_option( 'color_alt_content_text' ),
					'color_text' => us_get_option( 'color_alt_content_bg' ),
				);
			}

			if ( us_get_option( 'color_content_text' ) != us_get_option( 'color_alt_content_text' ) OR us_get_option( 'color_content_border' ) != us_get_option( 'color_alt_content_border' ) ) {
				$this->button_colors['light_alt'] = array(
					'name' => 'Alternate Light',
					'color_bg' => us_get_option( 'color_alt_content_border' ),
					'color_text' => us_get_option( 'color_alt_content_text' ),
					'color_bg_hover' => 'rgba(0,0,0,0.05)',
				);
			}

			if ( us_get_option( 'color_content_link' ) != us_get_option( 'color_alt_content_link' ) OR us_get_option( 'color_content_link_hover' ) != us_get_option( 'color_alt_content_link_hover' ) ) {
				$this->button_colors['transparent_alt'] = array(
					'name' => 'Alternate Text Link',
					'color_bg' => '',
					'color_text' => us_get_option( 'color_alt_content_link' ),
					'color_bg_hover' => '',
					'color_text_hover' => us_get_option( 'color_alt_content_link_hover' ),
				);
			}
		}
	}

	/**
	 * Delete default buttons sets after install theme before migration
	 */
	private function del_previous_buttons() {
		global $usof_options;

		usof_load_options_once();

		$updated_options = $usof_options;

		if ( ! empty( $updated_options['buttons'] ) AND is_array( $updated_options['buttons'] ) ) {
			unset( $updated_options['buttons'] );
		}
		remove_action( 'usof_after_save', 'us_generate_asset_files' );
		usof_save_options( $updated_options );
		add_action( 'usof_after_save', 'us_generate_asset_files' );

	}

	/**
	 * Check if button style is added, add if needed and return it's ID
	 */
	private function maybe_add_button_style( $color, $style ) {

		global $usof_options;
		$updated_options = $usof_options;

		usof_load_options_once();

		$style_key = $color . '_' . $style;
		if ( ! empty( $usof_options['buttons'] ) AND is_array( $usof_options['buttons'] ) ) {
			foreach ( $usof_options['buttons'] as $_button_style ) {
				if ( ! empty( $_button_style['_migrated_key'] ) AND $_button_style['_migrated_key'] == $style_key ) {
					return $_button_style['id'];
				}
			}
		}

		// Add "Outlined" to the name if set
		$style_name = ( $style == 'solid' ) ? '' : ' Outlined';

		// Set colors for "Default Style" button style
		if ( $color == 'default' ) {
			$color_bg = us_get_option( 'color_content_primary' );
			$color_bg_hover = us_get_option( 'color_content_secondary' );
			$color_text = $color_text_hover = '#ffffff';
			$color_border = $color_border_hover = '';

			// Set colors for Header buttons
		} elseif ( strpos( $color, 'header' ) !== FALSE ) {
			if ( $usof_options['button_hover'] == 'none' ) {
				$color_bg = $color_bg_hover = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_bg'] : '';
				$color_border = $color_border_hover = ( $style == 'solid' ) ? '' : $this->button_colors[ $color ]['color_text'];
				$color_text = $color_text_hover = $this->button_colors[ $color ]['color_text'];
			} else {
				$color_bg = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_bg'] : '';
				$color_bg_hover = $this->button_colors[ $color ]['color_bg_hover'];
				$color_border = $color_border_hover = ( $style == 'solid' ) ? '' : $this->button_colors[ $color ]['color_text'];
				$color_text = $this->button_colors[ $color ]['color_text'];
				$color_text_hover = $this->button_colors[ $color ]['color_text_hover'];
			}

			// Set colors depending on former global hover style for all other buttons
		} else {
			// Set border color
			if ( ! isset( $this->button_colors[ $color ]['color_border'] ) ) {
				$this->button_colors[ $color ]['color_border'] = $this->button_colors[ $color ]['color_bg'];
			}

			if ( ! empty( $usof_options['button_hover'] ) AND $usof_options['button_hover'] == 'none' ) {
				$color_bg = $color_bg_hover = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_bg'] : '';
				$color_border = $color_border_hover = ( $style == 'solid' ) ? '' : $this->button_colors[ $color ]['color_border'];
				$color_text = $color_text_hover = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_text'] : $this->button_colors[ $color ]['color_bg'];
			} elseif ( ! empty( $usof_options['button_hover'] ) AND $usof_options['button_hover'] == 'reverse' ) {
				$color_bg = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_bg'] : '';
				$color_bg_hover = ( $style == 'solid' ) ? '' : $this->button_colors[ $color ]['color_bg'];
				$color_border = ( $style == 'solid' ) ? '' : $this->button_colors[ $color ]['color_border'];
				$color_border_hover = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_border'] : '';
				$color_text = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_text'] : $this->button_colors[ $color ]['color_bg'];
				$color_text_hover = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_bg'] : $this->button_colors[ $color ]['color_text'];
			} elseif ( ! empty( $usof_options['button_hover'] ) AND $usof_options['button_hover'] == 'slide' ) {
				$color_bg = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_bg'] : '';
				if ( ! isset( $this->button_colors[ $color ]['color_bg_hover'] ) ) {
					$this->button_colors[ $color ]['color_bg_hover'] = 'rgba(0,0,0,0.15)';
				}
				$color_bg_hover = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_bg_hover'] : $this->button_colors[ $color ]['color_bg'];
				$color_border = $color_border_hover = ( $style == 'solid' ) ? '' : $this->button_colors[ $color ]['color_border'];
				$color_text = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_text'] : $this->button_colors[ $color ]['color_bg'];
				$color_text_hover = $this->button_colors[ $color ]['color_text'];
			} else { /* if ( $usof_options['button_hover'] == 'fade' ) */
				$color_bg = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_bg'] : '';
				$color_bg_hover = ( $style == 'solid' ) ? us_shade_color( $this->button_colors[ $color ]['color_bg'] ) : $this->button_colors[ $color ]['color_bg'];
				$color_border = $color_border_hover = ( $style == 'solid' ) ? '' : $this->button_colors[ $color ]['color_border'];
				$color_text = ( $style == 'solid' ) ? $this->button_colors[ $color ]['color_text'] : $this->button_colors[ $color ]['color_bg'];
				$color_text_hover = $this->button_colors[ $color ]['color_text'];
			}
		}

		// Exception for "Light" button text color
		if ( in_array( $color, array( 'light', 'light_alt', 'semitransparent' ) ) ) {
			$color_text = $color_text_hover = $this->button_colors[ $color ]['color_text'];
		}

		// Exception for "Transparent" button
		if ( in_array( $color, array( 'transparent', 'transparent_alt' ) ) ) {
			$shadow = $shadow_hover = $width = $height = $letter_spacing = $border_radius = 0;
			$font = 'body';
			$text_style = array();
			$font_weight = '400';
			$hover = 'fade';
			// Exception for "Load More" button
		} elseif ( $color == 'loadmore' ) {
			$shadow = $shadow_hover = $letter_spacing = $border_radius = 0;
			$width = $height = 1.2;
			$font = 'body';
			$text_style = array();
			$font_weight = '700';
			$hover = 'fade';
		} else {
			$shadow = ( ! empty( $usof_options['button_shadow'] ) ) ? $usof_options['button_shadow'] : 0;
			$shadow_hover = ( ! empty( $usof_options['button_shadow_hover'] ) ) ? $usof_options['button_shadow_hover'] : $shadow;
			$width = $usof_options['button_width'];
			$height = ( $usof_options['button_height'] - 1.2 ) / 2;
			$font = $usof_options['button_font'];
			$text_style = $usof_options['button_text_style'];
			$font_weight = $usof_options['button_fontweight'];
			$letter_spacing = $usof_options['button_letterspacing'];
			$border_radius = $usof_options['button_border_radius'];
			$hover = ( ! empty( $usof_options['button_hover'] ) AND $usof_options['button_hover'] == 'slide' ) ? 'slide' : 'fade';
		}

		// Exception for buttons with text color on hover
		if ( ! empty( $this->button_colors[ $color ]['color_text_hover'] ) ) {
			$color_text_hover = $this->button_colors[ $color ]['color_text_hover'];
		}

		if ( ! isset( $updated_options['buttons'] ) OR ! is_array( $updated_options['buttons'] ) ) {
			$updated_options['buttons'] = array();
		}

		$id = 1;
		foreach ( $updated_options['buttons'] as $_button_style ) {
			$id = max( intval( $_button_style['id'] ) + 1, $id );
		}

		$button_style = array(
			'_migrated_key' => $style_key,
			'id' => $id,
			'name' => $this->button_colors[ $color ]['name'] . $style_name,
			'hover' => $hover,
			'color_bg' => $color_bg,
			'color_bg_hover' => $color_bg_hover,
			'color_border' => $color_border,
			'color_border_hover' => $color_border_hover,
			'color_text' => $color_text,
			'color_text_hover' => $color_text_hover,
			'shadow' => $shadow,
			'shadow_hover' => $shadow_hover,
			'height' => $height,
			'width' => $width,
			'font' => $font,
			'text_style' => $text_style,
			'font_weight' => $font_weight,
			'border_radius' => $border_radius,
			'letter_spacing' => $letter_spacing,
			'border_width' => 2,
		);

		$updated_options['buttons'][] = $button_style;

		// Filling the missed options with default values
		$updated_options = array_merge( usof_defaults(), $updated_options );
		// Saving the changed options
		remove_action( 'usof_after_save', 'us_generate_asset_files' );
		usof_save_options( $updated_options );
		add_action( 'usof_after_save', 'us_generate_asset_files' );

		return $id;
	}

	/**
	 * Set global button size if it was differ from body font-size
	 */
	private function btn_size() {
		$btn_size = '';

		if ( us_get_option( 'button_fontsize' ) !== us_get_option( 'body_fontsize' ) ) {
			$btn_size = us_get_option( 'button_fontsize' ) . 'px';
		}

		return $btn_size;
	}

}
