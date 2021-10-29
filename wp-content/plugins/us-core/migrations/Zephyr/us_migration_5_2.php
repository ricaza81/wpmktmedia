<?php

class us_migration_5_2 extends US_Migration_Translator {

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

	public function translate_us_separator( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['type'] ) ) {
			if ( $params['type'] == 'default' ) {
				$params['show_line'] = '1';
				$params['line_width'] = 'default';
			} elseif ( $params['type'] == 'fullwidth' ) {
				$params['show_line'] = '1';
				$params['line_width'] = 'screen';
			} elseif ( $params['type'] == 'short' ) {
				$params['show_line'] = '1';
				$params['line_width'] = '30';
			}

			$changed = TRUE;

			unset( $params['type'] );
		}

		return $changed;
	}

	public function translate_vc_empty_space( &$name, &$params, &$content ) {

		$name = 'us_separator';
		$params['size'] = 'custom';
		if ( ! isset( $params['height'] ) ) {
			$params['height'] = '32px';
		}

		return TRUE;
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

				if ( ! isset( $item['btn_color'] ) OR ! in_array( $item['btn_color'], array_merge( array_keys( $this->button_colors ), array( 'custom' ) ) ) ) {
					$items[ $index ]['btn_color'] = 'primary';
				}
				if ( ! isset( $item['btn_style'] ) ) {
					$item['btn_style'] = 'raised';
				}

				$item['btn_color'] = $this->filter_btn_color( $item['btn_color'], $item['btn_style'] );

				if ( $item['btn_color'] == 'custom' ) {
					global $us_migration_btn_index;
					if ( empty( $us_migration_btn_index ) ) {
						$us_migration_btn_index = 1;
					} else {
						$us_migration_btn_index ++;
					}
					if ( ! isset( $item['btn_bg_color'] ) ) {
						$item['btn_bg_color'] = '';
					}
					if ( ! isset( $item['btn_text_color'] ) ) {
						$item['btn_text_color'] = '';
					}
					$item['btn_color'] = str_replace( '#', '', 'custom_' . $item['btn_bg_color'] . '_' . $item['btn_text_color'] );
					if ( ! isset( $this->button_colors[ $item['btn_color'] ] ) ) {
						$this->button_colors[ $item['btn_color'] ] = array(
							'name' => 'Custom ' . $us_migration_btn_index,
							'color_bg' => $item['btn_bg_color'],
							'color_text' => $item['btn_text_color'],
						);
					}

					unset( $items[ $index ]['btn_bg_color'] );
					unset( $items[ $index ]['btn_text_color'] );

				}

				if ( isset( $item['btn_style'] ) AND $item['btn_style'] == 'flat' ) {
					$items[ $index ]['btn_style'] = $this->maybe_add_button_style( $item['btn_color'], 'flat' );
				} else {
					$items[ $index ]['btn_style'] = $this->maybe_add_button_style( $item['btn_color'], 'raised' );
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

		if ( ! isset( $params['button_color'] ) OR ! in_array( $params['button_color'], array_merge( array_keys( $this->button_colors ), array( 'custom' ) ) ) ) {
			$params['button_color'] = 'primary';
		}
		if ( ! isset( $params['button_style'] ) ) {
			$params['button_style'] = 'raised';
		}

		$params['button_color'] = $this->filter_btn_color( $params['button_color'], $params['button_style'] );

		if ( $params['button_color'] == 'custom' ) {
			global $us_migration_btn_index;
			if ( empty( $us_migration_btn_index ) ) {
				$us_migration_btn_index = 1;
			} else {
				$us_migration_btn_index ++;
			}
			if ( ! isset( $params['button_bg_color'] ) ) {
				$params['button_bg_color'] = '';
			}
			if ( ! isset( $params['button_text_color'] ) ) {
				$params['button_text_color'] = '';
			}
			$params['button_color'] = str_replace( '#', '', 'custom_' . $params['button_bg_color'] . '_' . $params['button_text_color'] );
			if ( ! isset( $this->button_colors[ $params['button_color'] ] ) ) {
				$this->button_colors[ $params['button_color'] ] = array(
					'name' => 'Custom ' . $us_migration_btn_index,
					'color_bg' => $params['button_bg_color'],
					'color_text' => $params['button_text_color'],
				);
			}

			unset( $params['button_bg_color'] );
			unset( $params['button_text_color'] );

		}

		if ( isset( $params['button_style'] ) AND $params['button_style'] == 'flat' ) {
			$params['button_style'] = $this->maybe_add_button_style( $params['button_color'], 'flat' );
		} else {
			$params['button_style'] = $this->maybe_add_button_style( $params['button_color'], 'raised' );
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
				$btn_style = $this->filter_btn_color( 'primary', 'flat' );
				$params['pagination_btn_style'] = $this->maybe_add_button_style( $btn_style, 'flat' );
			} elseif ( isset( $params['pagination_btn_style'] ) AND $params['pagination_btn_style'] == 'btn' ) {
				$params['pagination_btn_style'] = $this->maybe_add_button_style( 'primary', 'raised' );
			} else {
				$params['pagination_btn_style'] = $this->maybe_add_button_style( 'loadmore', 'raised' );
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
		if ( ! isset( $params['btn_label'] ) OR $params['btn_label'] != '' ) {
			if ( ! isset( $params['btn_color'] ) OR ! in_array( $params['btn_color'], array_merge( array_keys( $this->button_colors ), array( 'custom' ) ) ) ) {
				$params['btn_color'] = 'white';
			}
			if ( ! isset( $params['btn_style'] ) ) {
				$params['btn_style'] = 'raised';
			}

			$params['btn_color'] = $this->filter_btn_color( $params['btn_color'], $params['btn_style'] );

			if ( $params['btn_color'] == 'custom' ) {
				global $us_migration_btn_index;
				if ( empty( $us_migration_btn_index ) ) {
					$us_migration_btn_index = 1;
				} else {
					$us_migration_btn_index ++;
				}
				if ( ! isset( $params['btn_bg_color'] ) ) {
					$params['btn_bg_color'] = '';
				}
				if ( ! isset( $params['btn_text_color'] ) ) {
					$params['btn_text_color'] = '';
				}
				$params['btn_color'] = str_replace( '#', '', 'custom_' . $params['btn_bg_color'] . '_' . $params['btn_text_color'] );
				if ( ! isset( $this->button_colors[ $params['btn_color'] ] ) ) {
					$this->button_colors[ $params['btn_color'] ] = array(
						'name' => 'Custom ' . $us_migration_btn_index,
						'color_bg' => $params['btn_bg_color'],
						'color_text' => $params['btn_text_color'],
					);
				}

				unset( $params['btn_bg_color'] );
				unset( $params['btn_text_color'] );

			}

			if ( isset( $params['btn_style'] ) AND $params['btn_style'] == 'flat' ) {
				$params['btn_style'] = $this->maybe_add_button_style( $params['btn_color'], 'flat' );
			} else {
				$params['btn_style'] = $this->maybe_add_button_style( $params['btn_color'], 'raised' );
			}

			unset( $params['btn_color'] );

			$changed = TRUE;
		}


		// Button 2
		if ( isset( $params['second_button'] ) AND $params['second_button'] AND ( ! isset( $params['btn2_label'] ) OR $params['btn2_label'] != '' ) ) {
			if ( ! isset( $params['btn2_color'] ) OR ! in_array( $params['btn2_color'], array_merge( array_keys( $this->button_colors ), array( 'custom' ) ) ) ) {
				$params['btn2_color'] = 'secondary';
			}
			if ( ! isset( $params['btn2_style'] ) ) {
				$params['btn2_style'] = 'raised';
			}

			$params['btn2_color'] = $this->filter_btn_color( $params['btn2_color'], $params['btn2_style'] );

			if ( $params['btn2_color'] == 'custom' ) {
				global $us_migration_btn_index;
				if ( empty( $us_migration_btn_index ) ) {
					$us_migration_btn_index = 1;
				} else {
					$us_migration_btn_index ++;
				}
				if ( ! isset( $params['btn2_bg_color'] ) ) {
					$params['btn2_bg_color'] = '';
				}
				if ( ! isset( $params['btn2_text_color'] ) ) {
					$params['btn2_text_color'] = '';
				}
				$params['btn2_color'] = str_replace( '#', '', 'custom_' . $params['btn2_bg_color'] . '_' . $params['btn2_text_color'] );
				if ( ! isset( $this->button_colors[ $params['btn_color'] ] ) ) {
					$this->button_colors[ $params['btn2_color'] ] = array(
						'name' => 'Custom ' . $us_migration_btn_index,
						'color_bg' => $params['btn2_bg_color'],
						'color_text' => $params['btn2_text_color'],
					);
				}

				unset( $params['btn2_bg_color'] );
				unset( $params['btn2_text_color'] );

			}

			if ( isset( $params['btn2_style'] ) AND $params['btn2_style'] == 'flat' ) {
				$params['btn2_style'] = $this->maybe_add_button_style( $params['btn2_color'], 'flat' );
			} else {
				$params['btn2_style'] = $this->maybe_add_button_style( $params['btn2_color'], 'raised' );
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

		if ( ! isset( $params['color'] ) OR ! in_array( $params['color'], array_merge( array_keys( $this->button_colors ), array( 'custom' ) ) ) ) {
			$params['color'] = 'primary';
		}
		if ( ! isset( $params['style'] ) ) {
			$params['style'] = 'raised';
		}

		$params['color'] = $this->filter_btn_color( $params['color'], $params['style'] );

		if ( $params['color'] == 'custom' ) {
			global $us_migration_btn_index;
			if ( empty( $us_migration_btn_index ) ) {
				$us_migration_btn_index = 1;
			} else {
				$us_migration_btn_index ++;
			}
			if ( ! isset( $params['bg_color'] ) ) {
				$params['bg_color'] = '';
			}
			if ( ! isset( $params['text_color'] ) ) {
				$params['text_color'] = '';
			}
			$params['color'] = str_replace( '#', '', 'custom_' . $params['bg_color'] . '_' . $params['text_color'] );
			if ( ! isset( $this->button_colors[ $params['color'] ] ) ) {
				$this->button_colors[ $params['color'] ] = array(
					'name' => 'Custom ' . $us_migration_btn_index,
					'color_bg' => $params['bg_color'],
					'color_text' => $params['text_color'],
				);
			}

			unset( $params['bg_color'] );
			unset( $params['text_color'] );

		}

		if ( isset( $params['style'] ) AND $params['style'] == 'flat' ) {
			$params['style'] = $this->maybe_add_button_style( $params['color'], 'flat' );
		} else {
			$params['style'] = $this->maybe_add_button_style( $params['color'], 'raised' );
		}

		unset( $params['color'] );

		return TRUE;
	}

	// Grid Layout Buttons
	public function translate_grid_layout_settings( &$settings ) {
		$settings_changed = FALSE;
		$grid_btn_index = 0;

		foreach ( $settings['data'] as $name => $data ) {

			// Button element
			if ( substr( $name, 0, 3 ) == 'btn' ) {
				// Fail-safe if this button was already migrated
				if ( ! empty( $data['style'] ) AND intval( $data['style'] ) > 0 ) {
					continue;
				}
				$grid_btn_index ++;
				$this->button_colors[ 'grid_' . $grid_btn_index ] = array(
					'name' => 'Grid Layout ' . $grid_btn_index,
					'color_bg' => $data['color_bg'],
					'color_text' => $data['color_text'],
					'color_bg_hover' => $data['color_bg'],
					'color_text_hover' => $data['color_text'],
				);

				if ( isset( $data['style'] ) AND $data['style'] == 'flat' ) {
					$settings['data'][ $name ]['style'] = $this->maybe_add_button_style( 'grid_' . $grid_btn_index, 'flat' );
				} else {
					$settings['data'][ $name ]['style'] = $this->maybe_add_button_style( 'grid_' . $grid_btn_index, 'raised' );
				}

				unset( $settings['data'][ $name ]['color_bg'] );
				unset( $settings['data'][ $name ]['color_text'] );

				$settings_changed = TRUE;
			}
			// HTML element
			if ( substr( $name, 0, 4 ) == 'html' ) {
				// Check if maybe the HTML was already encoded
				if ( preg_match( '%^[a-zA-Z0-9/+]*={0,2}$%', $data['content'] ) ) {
					continue;
				}
				$settings['data'][ $name ]['content'] = base64_encode( rawurlencode( $data['content'] ) );
				$settings_changed = TRUE;
			}

		}

		return $settings_changed;
	}

	// Headers
	public function translate_header_settings( &$settings ) {
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

				if ( isset( $data['style'] ) AND $data['style'] == 'flat' ) {
					$settings['data'][ $name ]['style'] = $this->maybe_add_button_style( 'header_' . $header_btn_index, 'flat' );
				} else {
					$settingss['data'][ $name ]['style'] = $this->maybe_add_button_style( 'header_' . $header_btn_index, 'raised' );
				}

				unset( $settings['data'][ $name ]['color_bg'] );
				unset( $settings['data'][ $name ]['color_text'] );
				unset( $settings['data'][ $name ]['color_hover_bg'] );
				unset( $settings['data'][ $name ]['color_hover_text'] );

				$settings_changed = TRUE;
			}
			// Cart element
			if ( substr( $name, 0, 4 ) == 'cart' ) {
				$settings['data'][ $name ]['quantity_color_bg'] = $options['color_menu_button_bg'];
				$settings['data'][ $name ]['quantity_color_text'] = $options['color_menu_button_text'];

				$settings_changed = TRUE;
			}
			// HTML element
			if ( substr( $name, 0, 4 ) == 'html' ) {
				// Check if maybe the HTML was already encoded
				if ( preg_match( '%^[a-zA-Z0-9/+]*={0,2}$%', $data['content'] ) ) {
					continue;
				}
				$settings['data'][ $name ]['content'] = base64_encode( rawurlencode( $data['content'] ) );
				$settings_changed = TRUE;
			}

		}

		// Change tablets & mobiles breakpoints plus one
		if ( isset( $settings['tablets']['options']['breakpoint'] ) and is_array( $settings['tablets'] ) ) {
			$settings['tablets']['options']['breakpoint'] = $settings['tablets']['options']['breakpoint'] + 1;
			$settings_changed = TRUE;
		}
		if ( isset( $settings['mobiles']['options']['breakpoint'] ) and is_array( $settings['mobiles'] ) ) {
			$settings['mobiles']['options']['breakpoint'] = $settings['mobiles']['options']['breakpoint'] + 1;
			$settings_changed = TRUE;
		}

		return $settings_changed;
	}

	// Theme Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		$this->set_button_colors();

		// Add Default button style always FIRST
		$this->button_colors['default'] = array(
			'name' => 'Default Button',
		);
		$this->maybe_add_button_style( 'default', 'raised' );

		// Add "Light" button style always SECOND
		$this->maybe_add_button_style( 'light', 'raised' );

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
	private function filter_btn_color( $color, $style ) {
		// We will need this filter only for Flat buttons
		if ( $style != 'flat' ) {
			return $color;
		}
		global $us_row_color_scheme;
		// Add styles for alternate row color scheme
		if ( ! empty( $us_row_color_scheme ) AND $us_row_color_scheme == 'alternate' AND in_array(
				$color, array(
					'primary',
					'secondary',
					'contrast',
					'light',
					'black',
				)
			) ) {
			$filtered_color = $color . '_alt';
			us_get_option( 'color_content_border' );
			// Adding alt colors based on regular colors and changing bg on hover and name
			$this->button_colors[ $filtered_color ] = $this->button_colors[ $color ];
			$this->button_colors[ $filtered_color ]['color_bg_hover'] = us_get_option( 'color_content_border' );
			$this->button_colors[ $filtered_color ]['name'] .= ' Alternate';
		}
		if ( ! empty( $us_row_color_scheme ) AND in_array(
				$us_row_color_scheme, array(
					'primary',
					'secondary',
					'custom',
				)
			) AND in_array(
				$color, array(
					'primary',
					'secondary',
					'contrast',
					'light',
					'black',
					'white',
				)
			) ) {
			$filtered_color = $color . '_alt2';
			us_get_option( 'color_content_border' );
			// Adding alt colors based on regular colors and changing bg on hover and name
			$this->button_colors[ $filtered_color ] = $this->button_colors[ $color ];
			$this->button_colors[ $filtered_color ]['color_bg_hover'] = 'rgba(255,255,255,0.12)';
			$this->button_colors[ $filtered_color ]['name'] .= ' Alternate 2';
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
				),
				'contrast' => array(
					'name' => 'Contrast',
					'color_bg' => us_get_option( 'color_content_text' ),
					'color_text' => us_get_option( 'color_content_bg' ),
				),
				'black' => array(
					'name' => 'Black',
					'color_bg' => '#000000',
					'color_text' => '#ffffff',
				),
				'white' => array(
					'name' => 'White',
					'color_bg' => '#ffffff',
					'color_text' => '#222222',
				),
				'loadmore' => array(
					'name' => 'Load More Button',
					'color_bg' => us_get_option( 'color_content_bg_alt' ),
					'color_text' => us_get_option( 'color_content_text' ),
				),
			);
		}
	}

	/**
	 * Check if button style is added, add if needed and return it's ID
	 */
	private function maybe_add_button_style( $color, $style ) {

		global $usof_options, $us_row_color_scheme;
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
		$style_name = ( $style == 'raised' ) ? '' : ' Flat';

		// Set colors for "Default Style" button style
		if ( $color == 'default' ) {
			$color_bg = us_get_option( 'color_content_primary' );
			$color_bg_hover = us_get_option( 'color_content_secondary' );
			$color_text = $color_text_hover = '#ffffff';

			// Set colors for Custom buttons
		} elseif ( strpos( $color, 'custom' ) !== FALSE ) {
			$color_bg = $this->button_colors[ $color ]['color_bg'];
			$color_bg_hover = ( empty( $color_bg ) ) ? us_get_option( 'color_content_bg_alt' ) : $color_bg;
			$color_text = $color_text_hover = $this->button_colors[ $color ]['color_text'];

			// Set colors for Header buttons
		} elseif ( strpos( $color, 'header' ) !== FALSE OR strpos( $color, 'grid' ) !== FALSE ) {
			$color_bg = $this->button_colors[ $color ]['color_bg'];
			$color_bg_hover = $this->button_colors[ $color ]['color_bg_hover'];
			$color_text = $this->button_colors[ $color ]['color_text'];
			$color_text_hover = $this->button_colors[ $color ]['color_text_hover'];

		} else {
			$color_bg = ( $style == 'raised' ) ? $this->button_colors[ $color ]['color_bg'] : '';
			if ( $style == 'raised' ) {
				$color_bg_hover = $this->button_colors[ $color ]['color_bg'];
			} elseif ( isset( $this->button_colors[ $color ]['color_bg_hover'] ) ) {
				$color_bg_hover = $this->button_colors[ $color ]['color_bg_hover'];
			} else {
				$color_bg_hover = us_get_option( 'color_content_bg_alt' );
			}
			if ( $style == 'raised' ) {
				$color_text = $color_text_hover = $this->button_colors[ $color ]['color_text'];
			} else {
				if ( substr( $color, 0, 5 ) == 'light' ) {
					$color_text = $color_text_hover = us_get_option( 'color_content_faded' );
				} elseif ( substr( $color, 0, 8 ) == 'contrast' ) {
					$color_text = $color_text_hover = 'inherit';
				} else {
					$color_text = $color_text_hover = $this->button_colors[ $color ]['color_bg'];
				}
			}
		}

		// Exception for "Load More" button
		if ( $color == 'loadmore' ) {
			$color_bg_hover = us_get_option( 'color_content_border' );
			$shadow = $shadow_hover = 0;
			$width = $height = 1.2;
			$font_weight = '700';
		} else {
			$shadow = ( $style == 'raised' ) ? 0.2 : 0;
			$shadow_hover = ( $style == 'raised' ) ? 0.5 : 0;
			$width = 1.5;
			$height = 0.8;
			$font_weight = '400';
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
			'hover' => 'fade',
			'color_bg' => $color_bg,
			'color_bg_hover' => $color_bg_hover,
			'color_border' => '',
			'color_border_hover' => '',
			'color_text' => $color_text,
			'color_text_hover' => $color_text_hover,
			'shadow' => $shadow,
			'shadow_hover' => $shadow_hover,
			'height' => $height,
			'width' => $width,
			'font' => 'body',
			'text_style' => array( 'uppercase' ),
			'font_weight' => $font_weight,
			'border_radius' => 0.2,
			'letter_spacing' => 0,
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
}
