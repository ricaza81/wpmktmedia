<?php

class us_migration_5_3 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		$content = str_replace(
			array( 'cl-counter', 'cl-popup', 'cl-itext', 'cl-flipbox' ), array(
			'cl_counter',
			'cl_popup',
			'cl_itext',
			'cl_flipbox',
		), $content
		);

		return $this->_translate_content( $content );
	}

	// IconBox
	public function translate_us_iconbox( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['bg_color'] ) ) {
			$params['circle_color'] = $params['bg_color'];
			unset( $params['bg_color'] );
			$changed = TRUE;
		}

		return $changed;
	}

	// CL Popup
	public function translate_cl_popup( &$name, &$params, &$content ) {
		$name = 'us_popup';

		if ( ( ! isset( $params['show_on'] ) OR in_array(
					$params['show_on'], array(
						'button',
						'text',
					)
				) ) AND ! isset( $params['btn_style'] ) ) {
			global $usof_options, $popup_btn_index;
			$updated_options = $usof_options;
			usof_load_options_once();

			$buttons_option_config = us_config( 'theme-options.buttons.fields.buttons.params', array() );
			$button_style = array();

			// Getting std values for default button
			if ( isset( $updated_options['buttons'] ) AND is_array( $updated_options['buttons'] ) AND count( $updated_options['buttons'] ) > 0 ) {
				$button_styles = array_values( $updated_options['buttons'] );
				$button_style = array_shift( $button_styles );
			} else {
				foreach ( $buttons_option_config as $btn_param_name => $btn_param ) {
					$button_style[ $btn_param_name ] = $btn_param['std'];
				}
			}


			$button_style['color_border'] = '';
			$button_style['color_border_hover'] = '';
			unset( $button_style['_migrated_key'], $button_style['id'], $button_style['name'] );

			if ( ! isset( $params['show_on'] ) OR $params['show_on'] == 'button' ) {
				if ( ! empty( $params['btn_bgcolor'] ) ) {
					$button_style['color_bg'] = $params['btn_bgcolor'];
					$button_style['color_bg_hover'] = $params['btn_bgcolor'];

					unset( $params['btn_bgcolor'] );
				}
				if ( ! empty( $params['btn_color'] ) ) {
					$button_style['color_text'] = $params['btn_color'];
					$button_style['color_text_hover'] = $params['btn_color'];

					unset( $params['btn_color'] );
				}
			} elseif ( $params['show_on'] == 'text' ) {
				$button_style['height'] = '0';
				$button_style['width'] = '0';
				$button_style['shadow'] = '0';
				$button_style['shadow_hover'] = '0';
				$button_style['color_bg'] = '';
				$button_style['color_bg_hover'] = '';
				$button_style['color_text'] = '';
				$button_style['color_text_hover'] = '';
				if ( ! empty( $params['text_color'] ) ) {
					$button_style['color_text'] = $params['text_color'];
					$button_style['color_text_hover'] = $params['text_color'];

					unset( $params['text_color'] );
				}
			}

			if ( ! isset( $updated_options['buttons'] ) OR ! is_array( $updated_options['buttons'] ) ) {
				$updated_options['buttons'] = array();
			}

			// Check for duplicate buttons
			foreach ( $updated_options['buttons'] as $btn ) {
				$current_btn_id = $btn['id'];
				unset( $btn['id'], $btn['name'], $btn['_migrated_key'], $button_style['id'], $button_style['name'] );
				if ( count( array_intersect_assoc( $btn, $button_style ) ) == count( $button_style ) ) {
					$params['btn_style'] = $current_btn_id;
					break;
				}
			}

			if ( ! isset( $params['btn_style'] ) ) {
				$btn_style_id = count( $updated_options['buttons'] ) + 1;
				foreach ( $updated_options['buttons'] as $_button_style ) {
					$btn_style_id = max( intval( $_button_style['id'] ) + 1, $btn_style_id );
				}
				$button_style['id'] = $btn_style_id;
				$popup_btn_index = ( ! empty( $popup_btn_index ) )
					? $popup_btn_index + 1
					: 1;
				$button_style['name'] = 'Popup Button ' . $popup_btn_index;
				$params['btn_style'] = $btn_style_id;
				$updated_options['buttons'][] = $button_style;
			}

			// Filling the missed options with default values
			$updated_options = array_merge( usof_defaults(), $updated_options );
			// Saving the changed options
			remove_action( 'usof_after_save', 'us_generate_asset_files' );
			usof_save_options( $updated_options );
			add_action( 'usof_after_save', 'us_generate_asset_files' );
		}

		if ( isset( $params['size'] ) ) {
			switch ( $params['size'] ) {
				case 's':
					$params['popup_width'] = '400px';
					$params['popup_padding'] = '40px';
					break;
				case 'm':
					$params['popup_width'] = '600px';
					$params['popup_padding'] = '40px';
					break;
				case 'l':
					$params['popup_width'] = '800px';
					break;
				case 'xl':
					$params['popup_width'] = '1000px';
					break;
				case 'f':
					$params['popup_width'] = '100%';
					break;
			}
			unset( $params['size'] );
		} else {
			$params['popup_width'] = '400px';
			$params['popup_padding'] = '40px';
		}

		if ( isset( $params['paddings'] ) AND $params['paddings'] == 'none' ) {
			$params['popup_padding'] = 0;
			unset( $params['paddings'] );
		}

		if ( isset( $params['border_radius'] ) ) {
			$params['popup_border_radius'] = $params['border_radius'];
			unset( $params['border_radius'] );
		}

		if ( ! isset( $params['btn_label'] ) ) {
			$params['btn_label'] = 'READ MORE';
		}

		if ( isset( $params['text_size'] ) ) {
			$params['btn_size'] = intval( $params['text_size'] ) . 'px';
			unset( $params['text_size'] );
		}

		return TRUE;
	}

	// CL FlipBox
	public function translate_cl_flipbox( &$name, &$params, &$content ) {

		$name = 'us_flipbox';

		if ( ! empty( $params['duration'] ) ) {
			$params['duration'] = intval( $params['duration'] ) / 1000;
		}

		if ( ! empty( $params['front_icon_name'] ) ) {
			global $us_template_directory;
			$_filename = trailingslashit( $us_template_directory ) . 'functions/migrations/us_migration_5_0.php';
			if ( file_exists( $_filename ) ) {
				include_once $_filename;
				$migration50 = new us_migration_5_0();
				$translated_icon = $migration50->translate_icon_name( 'fa-' . $params['front_icon_name'] );
				if ( $translated_icon != $params['front_icon_name'] ) {
					$params['front_icon_name'] = $translated_icon;
				}
			}
		}

		if ( ! empty( $params['front_elmorder'] ) AND $params['front_elmorder'] == 'tid' ) {
			$params['front_icon_pos'] = 'below_title';
			unset( $params['front_elmorder'] );
		} elseif ( ! empty( $params['front_elmorder'] ) AND $params['front_elmorder'] == 'tdi' ) {
			$params['front_icon_pos'] = 'below_desc';
			unset( $params['front_elmorder'] );
		}

		if ( ! empty( $params['border_size'] ) OR ! empty( $params['border_radius'] ) OR ! empty( $params['padding'] ) ) {
			$params['css'] = '.vc_custom_9999{';
			if ( ! empty( $params['border_size'] ) ) {
				$params['css'] .= 'border: ' . intval( $params['border_size'] ) . 'px solid ' . $params['border_color'] . ' !important;';
				unset( $params['border_size'] );
				unset( $params['border_color'] );
			}
			if ( ! empty( $params['border_radius'] ) ) {
				$params['css'] .= 'border-radius: ' . intval( $params['border_radius'] ) . 'px !important;';
				unset( $params['border_radius'] );
			}
			if ( ! empty( $params['padding'] ) ) {
				$params['css'] .= 'padding: ' . $params['padding'] . ' !important;';
				unset( $params['padding'] );
			}
			$params['css'] .= '}';
		}

		if ( isset( $params['link_type'] ) AND $params['link_type'] == 'btn' ) {
			global $usof_options, $flipbox_btn_index;
			$updated_options = $usof_options;
			usof_load_options_once();

			$buttons_option_config = us_config( 'theme-options.buttons.fields.buttons.params', array() );
			$button_style = array();

			// Getting std values for default button
			// Getting std values for default button
			if ( isset( $updated_options['buttons'] ) AND is_array( $updated_options['buttons'] ) AND count( $updated_options['buttons'] ) > 0 ) {
				$button_styles = array_values( $updated_options['buttons'] );
				$button_style = array_shift( $button_styles );
			} else {
				foreach ( $buttons_option_config as $btn_param_name => $btn_param ) {
					$button_style[ $btn_param_name ] = $btn_param['std'];
				}
			}

			$button_style['color_border'] = '';
			$button_style['color_border_hover'] = '';

			if ( ! empty( $params['back_btn_bgcolor'] ) ) {
				$button_style['color_bg'] = $params['back_btn_bgcolor'];
				$button_style['color_bg_hover'] = $params['back_btn_bgcolor'];

				unset( $params['back_btn_bgcolor'] );
			}
			if ( ! empty( $params['back_btn_color'] ) ) {
				$button_style['color_text'] = $params['back_btn_color'];
				$button_style['color_text_hover'] = $params['back_btn_color'];

				unset( $params['back_btn_color'] );
			}

			if ( ! isset( $updated_options['buttons'] ) OR ! is_array( $updated_options['buttons'] ) ) {
				$updated_options['buttons'] = array();
			}

			// Check for duplicate buttons
			foreach ( $updated_options['buttons'] as $btn ) {
				$current_btn_id = $btn['id'];
				unset( $btn['id'], $btn['name'], $btn['_migrated_key'], $button_style['id'], $button_style['name'] );
				if ( count( array_intersect_assoc( $btn, $button_style ) ) == count( $button_style ) ) {
					$params['btn_style'] = $current_btn_id;
					break;
				}
			}

			if ( ! isset( $params['btn_style'] ) ) {
				$btn_style_id = count( $updated_options['buttons'] ) + 1;
				foreach ( $updated_options['buttons'] as $_button_style ) {
					$btn_style_id = max( intval( $_button_style['id'] ) + 1, $btn_style_id );
				}
				$button_style['id'] = $btn_style_id;
				$flipbox_btn_index = ( ! empty( $flipbox_btn_index ) )
					? $flipbox_btn_index + 1
					: 1;
				$button_style['name'] = 'FlipBox Button ' . $flipbox_btn_index;
				$params['btn_style'] = $btn_style_id;
				$updated_options['buttons'][] = $button_style;
			}

			// Filling the missed options with default values
			$updated_options = array_merge( usof_defaults(), $updated_options );
			// Saving the changed options
			remove_action( 'usof_after_save', 'us_generate_asset_files' );
			usof_save_options( $updated_options );
			add_action( 'usof_after_save', 'us_generate_asset_files' );
		}

		if ( ! empty( $params['back_btn_label'] ) ) {
			$params['btn_label'] = $params['back_btn_label'];
			unset( $params['back_btn_label'] );
		}
		if ( isset( $params['back_elmorder'] ) ) {
			unset( $params['back_elmorder'] );
		}
		if ( isset( $params['valign'] ) ) {
			unset( $params['valign'] );
		}
		if ( ! empty( $params['front_icon_size'] ) ) {
			$params['front_icon_size'] = intval( $params['front_icon_size'] ) . 'px';
		}
		if ( ! empty( $params['front_icon_style'] ) AND $params['front_icon_style'] == 'square' ) {
			$params['front_icon_style'] = 'circle';
		}
		if ( ! empty( $params['height'] ) ) {
			$params['custom_height'] = intval( $params['height'] ) . 'px';
			unset( $params['height'] );
		}
		if ( ! empty( $params['width'] ) ) {
			$params['custom_width'] = $params['width'];
			unset( $params['width'] );
		}

		return TRUE;
	}

	// CL iText
	public function translate_cl_itext( &$name, &$params, &$content ) {

		$name = 'us_itext';

		if ( ! empty( $params['duration'] ) ) {
			$params['duration'] = intval( $params['duration'] ) / 1000;
		}
		if ( ! isset( $params['font_size'] ) OR empty( $params['font_size'] ) ) {
			$params['font_size'] = '50px';
		}
		if ( ! empty( $params['font_size_mobile'] ) ) {
			$params['font_size_mobiles'] = $params['font_size_mobile'];
			unset( $params['font_size_mobile'] );
		} else {
			$params['font_size_mobiles'] = '30px';
		}

		return TRUE;
	}

	// CL Counter
	public function translate_cl_counter( &$name, &$params, &$content ) {

		$name = 'us_counter';

		$params['title_tag'] = 'div';
		if ( ! isset( $params['final'] ) ) {
			$params['final'] = '100';
		}
		if ( ! isset( $params['title'] ) ) {
			$params['title'] = '';
		}
		if ( ! isset( $params['title_size'] ) ) {
			$params['title_size'] = '20px';
		}
		if ( ! empty( $params['duration'] ) ) {
			$params['duration'] = intval( $params['duration'] ) / 1000;
		}
		if ( ! empty( $params['value_size'] ) ) {
			$params['size'] = intval( $params['value_size'] ) . 'px';
			unset( $params['value_size'] );
		} else {
			$params['size'] = '50px';
		}
		if ( ! empty( $params['value_color'] ) ) {
			$params['color'] = 'custom';
			$params['custom_color'] = $params['value_color'];
			unset( $params['value_color'] );
		} else {
			$params['color'] = 'text';
		}

		return TRUE;
	}

	// Single Image
	public function translate_us_single_image( &$name, &$params, &$content ) {

		$name = 'us_image';

		return TRUE;
	}

	// Button
	public function translate_us_btn( &$name, &$params, &$content ) {

		if ( isset( $params['text'] ) ) {
			$params['label'] = $params['text'];
			unset( $params['text'] );
		}
		if ( ! empty( $params['size'] ) ) {
			$params['font_size'] = $params['size'];
			unset( $params['size'] );
		}
		if ( ! empty( $params['size_mobiles'] ) ) {
			$params['font_size_mobiles'] = $params['size_mobiles'];
			unset( $params['size_mobiles'] );
		}
		if ( ! empty( $params['width'] ) ) {
			$params['custom_width'] = $params['width'];
			unset( $params['width'] );
		}

		return TRUE;
	}

	// Counter
	public function translate_us_counter( &$name, &$params, &$content ) {

		if ( ! isset( $params['initial'] ) ) {
			$params['initial'] = '0';
		}
		if ( isset( $params['target'] ) ) {
			$params['final'] = $params['target'];
			unset( $params['target'] );
		}
		if ( isset( $params['prefix'] ) ) {
			$params['initial'] = $params['prefix'] . $params['initial'];
			$params['final'] = $params['prefix'] . $params['final'];
			unset( $params['prefix'] );
		}
		if ( isset( $params['suffix'] ) ) {
			$params['initial'] = $params['initial'] . $params['suffix'];
			$params['final'] = $params['final'] . $params['suffix'];
			unset( $params['suffix'] );
		}
		if ( ! isset( $params['color'] ) ) {
			$params['color'] = 'heading';
		}
		if ( ! isset( $params['font'] ) ) {
			$params['font'] = 'heading';
		}

		return TRUE;
	}

	// Social Links
	public function translate_us_social_links( &$name, &$params, &$content ) {

		$name = 'us_socials';

		if ( ! isset( $params['gap'] ) ) {
			$params['gap'] = '0.1em';
		}

		return TRUE;
	}

	// Sharing
	public function translate_us_sharing( &$name, &$params, &$content ) {
		if ( ! empty( $params['providers'] ) ) {
			return FALSE;
		}

		$params['providers'] = '';

		if ( isset( $params['email'] ) AND $params['email'] ) {
			$params['providers'] .= ',email';
			unset( $params['email'] );
		}
		if ( isset( $params['facebook'] ) AND $params['facebook'] ) {
			$params['providers'] .= ',facebook';
			unset( $params['facebook'] );
		}
		if ( isset( $params['twitter'] ) AND $params['twitter'] ) {
			$params['providers'] .= ',twitter';
			unset( $params['twitter'] );
		}
		if ( isset( $params['gplus'] ) AND $params['gplus'] ) {
			$params['providers'] .= ',gplus';
			unset( $params['gplus'] );
		}
		if ( isset( $params['linkedin'] ) AND $params['linkedin'] ) {
			$params['providers'] .= ',linkedin';
			unset( $params['linkedin'] );
		}
		if ( isset( $params['pinterest'] ) AND $params['pinterest'] ) {
			$params['providers'] .= ',pinterest';
			unset( $params['pinterest'] );
		}
		if ( isset( $params['vk'] ) AND $params['vk'] ) {
			$params['providers'] .= ',vk';
			unset( $params['vk'] );
		}

		$params['providers'] = substr( $params['providers'], 1 );

		if ( empty( $params['providers'] ) ) {
			unset( $params['providers'] );
		}

		return TRUE;
	}

	// Separator
	public function translate_us_separator( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['type'] ) ) {
			unset( $params['type'] );
			$changed = TRUE;
		}

		return $changed;
	}

	// Default WP Gallery
	public function translate_gallery( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( empty( $params['size'] ) ) {
			$columns = ( isset( $params['columns'] ) ) ? $params['columns'] : 3;

			if ( isset( $params['layout'] ) AND $params['layout'] == 'masonry' AND $columns > 1 ) {
				$params['size'] = ( $columns < 6 ) ? 'large' : 'medium';
				$changed = TRUE;
			} else/*if($layout == 'default')*/ {
				if ( $columns == 1 ) {
					$params['size'] = 'full';
					$changed = TRUE;
				} elseif ( $columns < 5 ) {
					$params['size'] = 'us_600_600_crop';
					$changed = TRUE;
				} elseif ( $columns < 8 ) {
					$params['size'] = 'us_350_350_crop';
					$changed = TRUE;
				}
			}
		}

		return $changed;
	}

	// Grid Layout
	public function translate_grid_layout_settings( &$settings ) {

		foreach ( $settings['data'] as $name => $data ) {

			// Button
			if ( substr( $name, 0, 3 ) == 'btn' ) {
				$settings['data'][ $name ]['font_size'] = ( empty( $data['size'] ) OR intval( $data['size'] ) == 0 )
					? ''
					: ( intval( $data['size'] ) . 'px' );
				$settings['data'][ $name ]['font_size_tablets'] = ( empty( $data['size_tablets'] ) OR intval( $data['size_tablets'] ) == 0 )
					? ''
					: ( intval( $data['size_tablets'] ) . 'px' );
				$settings['data'][ $name ]['font_size_mobiles'] = ( empty( $data['size_mobiles'] ) OR intval( $data['size_mobiles'] ) == 0 )
					? ''
					: ( intval( $data['size_mobiles'] ) . 'px' );
			}

			$settings['data'][ $name ]['transition_duration'] = isset( $data['transition_duration'] )
				? floatval( $data['transition_duration'] ) . 's'
				: '0.35s';

			if ( ! empty( $data['translateX'] ) ) {
				$settings['data'][ $name ]['translateX'] = intval( $data['translateX'] ) . '%';
			}
			if ( ! empty( $data['translateX_hover'] ) ) {
				$settings['data'][ $name ]['translateX_hover'] = intval( $data['translateX_hover'] ) . '%';
			}
			if ( ! empty( $data['translateY'] ) ) {
				$settings['data'][ $name ]['translateY'] = intval( $data['translateY'] ) . '%';
			}
			if ( ! empty( $data['translateY_hover'] ) ) {
				$settings['data'][ $name ]['translateY_hover'] = intval( $data['translateY_hover'] ) . '%';
			}

		}

		return TRUE;
	}

	// Headers
	public function translate_header_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $name => $data ) {

			// Image
			if ( substr( $name, 0, 5 ) == 'image' ) {
				$settings['data'][ $name ]['height'] = ( ! empty( $data['height'] ) AND intval( $data['height'] ) > 0 )
					? intval( $data['height'] ) . 'px'
					: '';
				$settings['data'][ $name ]['height_tablets'] = ( ! empty( $data['height_tablets'] ) AND intval( $data['height_tablets'] ) > 0 )
					? intval( $data['height_tablets'] ) . 'px'
					: '';
				$settings['data'][ $name ]['height_mobiles'] = ( ! empty( $data['height_mobiles'] ) AND intval( $data['height_mobiles'] ) > 0 )
					? intval( $data['height_mobiles'] ) . 'px'
					: '';
				$settings['data'][ $name ]['height_sticky'] = ( ! empty( $data['height_sticky'] ) AND intval( $data['height_sticky'] ) > 0 )
					? intval( $data['height_sticky'] ) . 'px'
					: '';
				$settings['data'][ $name ]['height_sticky_tablets'] = ( ! empty( $data['height_sticky_tablets'] ) AND intval( $data['height_sticky_tablets'] ) > 0 )
					? intval( $data['height_sticky_tablets'] ) . 'px'
					: '';
				$settings['data'][ $name ]['height_sticky_mobiles'] = ( ! empty( $data['height_sticky_mobiles'] ) AND intval( $data['height_sticky_mobiles'] ) > 0 )
					? intval( $data['height_sticky_mobiles'] ) . 'px'
					: '';

				$settings_changed = TRUE;
			}

			// Text
			if ( substr( $name, 0, 4 ) == 'text' OR substr( $name, 0, 3 ) == 'btn' ) {
				$settings['data'][ $name ]['font_size'] = ( ! empty( $data['size'] ) AND intval( $data['size'] ) > 0 )
					? intval( $data['size'] ) . 'px'
					: '';
				$settings['data'][ $name ]['font_size_tablets'] = ( ! empty( $data['size_tablets'] ) AND intval( $data['size_tablets'] ) > 0 )
					? intval( $data['size_tablets'] ) . 'px'
					: '';
				$settings['data'][ $name ]['font_size_mobiles'] = ( ! empty( $data['size_mobiles'] ) AND intval( $data['size_mobiles'] ) > 0 )
					? intval( $data['size_mobiles'] ) . 'px'
					: '';
				$settings['data'][ $name ]['text_styles'] = isset( $data['text_style'] )
					? $data['text_style']
					: array();

				$settings_changed = TRUE;
			}

			// Menu
			if ( substr( $name, 0, 4 ) == 'menu' ) {
				$settings['data'][ $name ]['font_size'] = ( ! empty( $data['font_size'] ) AND intval( $data['font_size'] ) > 0 )
					? intval( $data['font_size'] ) . 'px'
					: '';
				$settings['data'][ $name ]['indents'] = ( substr( $data['indents'], - 2 ) != 'px' )
					? ( intval( $data['indents'] ) / 2 ) . 'px'
					: $data['indents'];
				$settings['data'][ $name ]['dropdown_font_size'] = ( ! empty( $data['dropdown_font_size'] ) AND intval( $data['dropdown_font_size'] ) > 0 )
					? intval( $data['dropdown_font_size'] ) . 'px'
					: '';
				$settings['data'][ $name ]['mobile_font_size'] = ( ! empty( $data['mobile_font_size'] ) AND intval( $data['mobile_font_size'] ) > 0 )
					? intval( $data['mobile_font_size'] ) . 'px'
					: '';
				$settings['data'][ $name ]['mobile_dropdown_font_size'] = ( ! empty( $data['mobile_dropdown_font_size'] ) AND intval( $data['mobile_dropdown_font_size'] ) > 0 )
					? intval( $data['mobile_dropdown_font_size'] ) . 'px'
					: '';
				$settings['data'][ $name ]['mobile_icon_size'] = ( ! empty( $data['mobile_icon_size'] ) AND intval( $data['mobile_icon_size'] ) > 0 )
					? intval( $data['mobile_icon_size'] ) . 'px'
					: '';
				$settings['data'][ $name ]['mobile_icon_size_tablets'] = ( ! empty( $data['mobile_icon_size_tablets'] ) AND intval( $data['mobile_icon_size_tablets'] ) > 0 )
					? intval( $data['mobile_icon_size_tablets'] ) . 'px'
					: '';
				$settings['data'][ $name ]['mobile_icon_size_mobiles'] = ( ! empty( $data['mobile_icon_size_mobiles'] ) AND intval( $data['mobile_icon_size_mobiles'] ) > 0 )
					? intval( $data['mobile_icon_size_mobiles'] ) . 'px'
					: '';

				$settings_changed = TRUE;
			}

			// Links Menu
			if ( substr( $name, 0, 15 ) == 'additional_menu' ) {
				$settings['data'][ $name ]['size'] = ( ! empty( $data['size'] ) AND intval( $data['size'] ) > 0 )
					? intval( $data['size'] ) . 'px'
					: '';
				$settings['data'][ $name ]['size_tablets'] = ( ! empty( $data['size_tablets'] ) AND intval( $data['size_tablets'] ) > 0 )
					? intval( $data['size_tablets'] ) . 'px'
					: '';
				$settings['data'][ $name ]['size_mobiles'] = ( ! empty( $data['size_mobiles'] ) AND intval( $data['size_mobiles'] ) > 0 )
					? intval( $data['size_mobiles'] ) . 'px'
					: '';
				$settings['data'][ $name ]['indents'] = ( ! empty( $data['indents'] ) AND substr( $data['indents'], - 2 ) != 'px' )
					? ( intval( $data['indents'] ) / 2 ) . 'px'
					: $data['indents'];
				$settings['data'][ $name ]['indents_tablets'] = ( ! empty( $data['indents_tablets'] ) AND substr( $data['indents_tablets'], - 2 ) != 'px' )
					? ( intval( $data['indents_tablets'] ) / 2 ) . 'px'
					: $data['indents_tablets'];
				$settings['data'][ $name ]['indents_mobiles'] = ( ! empty( $data['indents_mobiles'] ) AND substr( $data['indents_mobiles'], - 2 ) != 'px' )
					? ( intval( $data['indents_mobiles'] ) / 2 ) . 'px'
					: $data['indents_mobiles'];

				$settings_changed = TRUE;
			}

			// Search
			if ( substr( $name, 0, 6 ) == 'search' ) {
				$settings['data'][ $name ]['icon_size'] = ( ! empty( $data['icon_size'] ) AND intval( $data['icon_size'] ) > 0 )
					? intval( $data['icon_size'] ) . 'px' :
					'';
				$settings['data'][ $name ]['icon_size_tablets'] = ( ! empty( $data['icon_size_tablets'] ) AND intval( $data['icon_size_tablets'] ) > 0 )
					? intval( $data['icon_size_tablets'] ) . 'px'
					: '';
				$settings['data'][ $name ]['icon_size_mobiles'] = ( ! empty( $data['icon_size_mobiles'] ) AND intval( $data['icon_size_mobiles'] ) > 0 )
					? intval( $data['icon_size_mobiles'] ) . 'px' :
					'';
				$settings['data'][ $name ]['field_width'] = ( ! empty( $data['width'] ) AND intval( $data['width'] ) > 0 )
					? intval( $data['width'] ) . 'px'
					: '';
				$settings['data'][ $name ]['field_width_tablets'] = ( ! empty( $data['width_tablets'] ) AND intval( $data['width_tablets'] ) > 0 )
					? intval( $data['width_tablets'] ) . 'px'
					: '';

				$settings_changed = TRUE;
			}

			// Dropdown, Social Links, Cart
			if ( substr( $name, 0, 8 ) == 'dropdown' OR substr( $name, 0, 7 ) == 'socials' OR substr( $name, 0, 4 ) == 'cart' ) {
				$settings['data'][ $name ]['size'] = ( ! empty( $data['size'] ) AND intval( $data['size'] ) > 0 )
					? intval( $data['size'] ) . 'px'
					: '';
				$settings['data'][ $name ]['size_tablets'] = ( ! empty( $data['size_tablets'] ) AND intval( $data['size_tablets'] ) > 0 )
					? intval( $data['size_tablets'] ) . 'px'
					: '';
				$settings['data'][ $name ]['size_mobiles'] = ( ! empty( $data['size_mobiles'] ) AND intval( $data['size_mobiles'] ) > 0 )
					? intval( $data['size_mobiles'] ) . 'px'
					: '';

				$settings_changed = TRUE;
			}

		}

		// Settings
		$states = (array) us_get_responsive_states( /* Only keys */TRUE );
		foreach ( $states as $state ) {
			if ( isset( $settings[ $state ] ) ) {
				$settings[ $state ]['options']['width'] = ( ! empty( $settings[ $state ]['options']['width'] ) AND intval( $settings[ $state ]['options']['width'] ) > 0 )
					? intval( $settings[ $state ]['options']['width'] ) . 'px'
					: '';
				$settings[ $state ]['options']['top_height'] = ( ! empty( $settings[ $state ]['options']['top_height'] ) AND intval( $settings[ $state ]['options']['top_height'] ) > 0 )
					? intval( $settings[ $state ]['options']['top_height'] ) . 'px'
					: '';
				$settings[ $state ]['options']['top_sticky_height'] = ( ! empty( $settings[ $state ]['options']['top_sticky_height'] ) AND intval( $settings[ $state ]['options']['top_sticky_height'] ) > 0 )
					? intval( $settings[ $state ]['options']['top_sticky_height'] ) . 'px'
					: '';
				$settings[ $state ]['options']['middle_height'] = ( ! empty( $settings[ $state ]['options']['middle_height'] ) AND intval( $settings[ $state ]['options']['middle_height'] ) > 0 )
					? intval( $settings[ $state ]['options']['middle_height'] ) . 'px'
					: '';
				$settings[ $state ]['options']['middle_sticky_height'] = ( ! empty( $settings[ $state ]['options']['middle_sticky_height'] ) AND intval( $settings[ $state ]['options']['middle_sticky_height'] ) > 0 )
					? intval( $settings[ $state ]['options']['middle_sticky_height'] ) . 'px'
					: '';
				$settings[ $state ]['options']['bottom_height'] = ( ! empty( $settings[ $state ]['options']['bottom_height'] ) AND intval( $settings[ $state ]['options']['bottom_height'] ) > 0 )
					? intval( $settings[ $state ]['options']['bottom_height'] ) . 'px'
					: '';
				$settings[ $state ]['options']['bottom_sticky_height'] = ( ! empty( $settings[ $state ]['options']['bottom_sticky_height'] ) AND intval( $settings[ $state ]['options']['bottom_sticky_height'] ) > 0 )
					? intval( $settings[ $state ]['options']['bottom_sticky_height'] ) . 'px'
					: '';

				$settings_changed = TRUE;
			}
		}

		return $settings_changed;
	}

	// Theme Options
	public function translate_theme_options( &$options ) {

		// Custom fix to avoid issues in older versions
		$options['custom_css'] = ".wpb_text_column:not(:last-of-type) { margin-bottom: 1.5rem; } /* migration 5.6 fix */ \n" . $options['custom_css'];

		/* Add new checkboxes if Optimize option is ON */
		if ( isset( $options['optimize_assets'] ) AND $options['optimize_assets'] == 1 AND is_array( $options['assets'] ) ) {
			$options['assets'] = array_unique( array_merge(
				array(
					'popups',
					'flipbox',
					'itext',
				), $options['assets']
			));
		}

		// Buttons
		$btn_styles = isset( $options['buttons'] ) ? $options['buttons'] : array();
		foreach ( $btn_styles as $key => $btn_style ) {
			$options['buttons'][ $key ]['shadow'] = ( substr( $btn_style['shadow'], - 2 ) != 'em' ) ? floatval( $btn_style['shadow'] ) . 'em' : $btn_style['shadow'];
			$options['buttons'][ $key ]['shadow_hover'] = ( substr( $btn_style['shadow_hover'], - 2 ) != 'em' ) ? floatval( $btn_style['shadow_hover'] ) . 'em' : $btn_style['shadow_hover'];
			$options['buttons'][ $key ]['height'] = ( substr( $btn_style['height'], - 2 ) != 'em' ) ? floatval( $btn_style['height'] ) . 'em' : $btn_style['height'];
			$options['buttons'][ $key ]['width'] = ( substr( $btn_style['width'], - 2 ) != 'em' ) ? floatval( $btn_style['width'] ) . 'em' : $btn_style['width'];
			$options['buttons'][ $key ]['border_radius'] = ( substr( $btn_style['border_radius'], - 2 ) != 'em' ) ? floatval( $btn_style['border_radius'] ) . 'em' : $btn_style['border_radius'];
			$options['buttons'][ $key ]['letter_spacing'] = ( substr( $btn_style['letter_spacing'], - 2 ) != 'em' ) ? floatval( $btn_style['letter_spacing'] ) . 'em' : $btn_style['letter_spacing'];
			$options['buttons'][ $key ]['border_width'] = ( substr( $btn_style['border_width'], - 2 ) != 'px' ) ? intval( $btn_style['border_width'] ) . 'px' : $btn_style['border_width'];
		}

		$slider_suffixes_map = array(
			'back_to_top_display' => 'vh',
			'smooth_scroll_duration' => 'ms',
			'site_canvas_width' => 'px',
			'site_content_width' => 'px',
			'sidebar_width' => '%',
			'content_width' => '%',
			'columns_stacking_width' => 'px',
			'disable_effects_width' => 'px',

			// Header settings
			'header_top_height' => 'px',
			'header_top_sticky_height' => 'px',
			'header_middle_height' => 'px',
			'header_middle_sticky_height' => 'px',
			'header_bottom_height' => 'px',
			'header_bottom_sticky_height' => 'px',
			'header_main_width' => 'px',
			'logo_font_size' => 'px',
			'logo_font_size_tablets' => 'px',
			'logo_font_size_mobiles' => 'px',
			'logo_height' => 'px',
			'logo_height_sticky' => 'px',
			'logo_height_tablets' => 'px',
			'logo_height_mobiles' => 'px',
			'menu_fontsize' => 'px',
			'menu_indents' => 'px',
			'menu_sub_fontsize' => 'px',
			'menu_mobile_width' => 'px',

			// Typography
			'body_fontsize' => 'px',
			'body_lineheight' => 'px',
			'body_fontsize_mobile' => 'px',
			'body_lineheight_mobile' => 'px',
			'h1_fontsize' => 'px',
			'h1_fontsize_mobile' => 'px',
			'h1_letterspacing' => 'em',
			'h2_fontsize' => 'px',
			'h2_fontsize_mobile' => 'px',
			'h2_letterspacing' => 'em',
			'h3_fontsize' => 'px',
			'h3_fontsize_mobile' => 'px',
			'h3_letterspacing' => 'em',
			'h4_fontsize' => 'px',
			'h4_fontsize_mobile' => 'px',
			'h4_letterspacing' => 'em',
			'h5_fontsize' => 'px',
			'h5_fontsize_mobile' => 'px',
			'h5_letterspacing' => 'em',
			'h6_fontsize' => 'px',
			'h6_fontsize_mobile' => 'px',
			'h6_letterspacing' => 'em',

			// Blog
			'post_related_items_gap' => 'rem',
			'blog_items_gap' => 'rem',
			'archive_items_gap' => 'rem',
			'search_items_gap' => 'rem',
			'shop_items_gap' => 'rem',
		);

		foreach ( $slider_suffixes_map as $param => $suffix ) {
			if ( isset( $options[ $param ] ) AND substr( $options[ $param ], - strlen( $suffix ) ) != $suffix ) {
				if ( in_array( $suffix, array( 'em', 'rem' ) ) ) {
					$options[ $param ] = floatval( $options[ $param ] ) . $suffix;
				} else {
					$options[ $param ] = intval( $options[ $param ] ) . $suffix;
				}
			}
		}

		// Menu items
		$menu_items = array();
		foreach ( get_terms( array( 'taxonomy' => 'nav_menu', 'hide_empty' => TRUE ) ) as $menu_obj ) {
			$menu_items = array_merge(
				$menu_items, wp_get_nav_menu_items( $menu_obj->term_id, array( 'post_status' => 'any' ) )
			);
		}
		foreach ( $menu_items as $menu_item ) {
			$menu_item_changed = FALSE;
			$mega_menu_settings = get_post_meta( $menu_item->ID, 'us_mega_menu_settings', TRUE );

			if ( ! empty( $mega_menu_settings['custom_width'] ) AND substr( $mega_menu_settings['custom_width'], - 2 ) != 'px' ) {
				$mega_menu_settings['custom_width'] = intval( $mega_menu_settings['custom_width'] ) . 'px';
				$menu_item_changed = TRUE;
			}

			if ( ! empty( $mega_menu_settings['padding'] ) AND substr( $mega_menu_settings['padding'], - 2 ) != 'px' ) {
				$mega_menu_settings['padding'] = intval( $mega_menu_settings['padding'] ) . 'px';
				$menu_item_changed = TRUE;
			}

			if ( $menu_item_changed ) {
				update_post_meta( $menu_item->ID, 'us_mega_menu_settings', $mega_menu_settings );
			}
		}

		return TRUE;
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
}
