<?php

class us_migration_7_0 extends US_Migration_Translator {

	/**
	 * Content
	 *
	 */
	public function translate_content( &$content ) {
		$changed = $this->_translate_content( $content );

		// Need to insert a Post Content element after a Page Title, as description for taxonomies archives
		global $us_migration_current_post_id;
		if ( in_array( get_post_type( $us_migration_current_post_id ), array( 'us_page_block', 'us_content_template' ) ) ) {
			$content = preg_replace_callback( '%(\[us_post_title[^\]]+migration_add_content="1"[^\]]*\])%', array( $this, 'page_title_replace_callback' ), $content );
		}

		return $changed;
	}

	private function page_title_replace_callback( $matches ) {
		$result = $matches[1] . '[us_post_content type="excerpt_only"]';
		$result = str_replace( 'migration_add_content="1"', '', $result );

		return $result;
	}

	/**
	 * Row
	 *
	 */
	public function translate_vc_row( &$name, &$params, &$content ) {
		global $us_row_color_scheme;
		$us_row_color_scheme = '';
		$atts = array();
		if ( isset( $params['color_scheme'] ) ) {
			// Remember Row color scheme for further conditions
			$us_row_color_scheme = $params['color_scheme'];
			if ( $params['color_scheme'] == 'custom' ) {
				$atts = array(
					'us_text_color' => 'color',
					'us_bg_color' => 'background-color',
				);
				unset( $params['color_scheme'] );
			}
		}
		return $this->translate_shortcode_design_options( $params, $atts );
	}

	/**
	 * Carousel
	 *
	 */
	public function translate_us_carousel( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * CForm
	 *
	 */
	public function translate_us_cform( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Contact Info
	 *
	 */
	public function translate_us_contacts( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Horizontal Wrapper
	 *
	 */
	public function translate_us_hwrapper( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * IconBox
	 *
	 */
	public function translate_us_iconbox( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Image
	 *
	 */
	public function translate_us_image( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Image Slider
	 *
	 */
	public function translate_us_image_slider( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Page Block
	 *
	 */
	public function translate_us_page_block( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Person
	 *
	 */
	public function translate_us_person( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Popup
	 *
	 */
	public function translate_us_popup( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Post Image
	 *
	 */
	public function translate_us_post_image( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Pricing
	 *
	 */
	public function translate_us_pricing( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Product Ordering
	 *
	 */
	public function translate_us_product_ordering( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Product Gallery
	 *
	 */
	public function translate_us_product_gallery( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Progress Bar
	 *
	 */
	public function translate_us_progbar( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array( 'title_size' => 'font-size' ) );
	}

	/**
	 * Page Scroller
	 *
	 */
	public function translate_us_scroller( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Column
	 *
	 */
	public function translate_vc_column( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'text_color' => 'color',
			'bg_column_fix' => '',
		) );
	}

	/**
	 * Column Inner
	 *
	 */
	public function translate_vc_column_inner( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'text_color' => 'color',
			'bg_column_fix' => '',
		) );
	}

	/**
	 * Text Block
	 *
	 */
	public function translate_vc_column_text( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Row Inner
	 *
	 */
	public function translate_vc_row_inner( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Add to Cart
	 *
	 */
	public function translate_us_add_to_cart( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array( 'font_size' => 'font-size' ) );
	}

	/**
	 * Breadcrumbs
	 *
	 */
	public function translate_us_breadcrumbs( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array( 'font_size' => 'font-size' ) );
	}

	/**
	 * Sharing Buttons
	 *
	 */
	public function translate_us_sharing( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array( 'font_size' => 'font-size' ) );
	}

	/**
	 * Tabs
	 *
	 */
	public function translate_vc_tta_tabs( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Tour
	 *
	 */
	public function translate_vc_tta_tour( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Accordion
	 *
	 */
	public function translate_vc_tta_accordion( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params );
	}

	/**
	 * Social Links
	 *
	 */
	public function translate_us_socials( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( isset( $params['color'] ) ) {
			$params['icons_color'] = $params['color'];
			unset( $params['color'] );
			$changed = TRUE;
		}

		// Force default values for correct work of "translate_shortcode_design_options" function
		if ( ! isset( $params['size'] ) OR empty( $params['size'] ) ) {
			$params['size'] = '20px';
		}
		$changed = $this->translate_shortcode_design_options( $params, array( 'size' => 'font-size' ) );

		return $changed;
	}

	/**
	 * Button
	 *
	 */
	public function translate_us_btn( &$name, &$params, &$content ) {
		$atts = array(
			'font_size' => 'font-size',
			'font_size_mobiles' => 'font-size',
		);

		// Force default values for correct work of "translate_shortcode_design_options" function
		if ( ! isset( $params['custom_width'] ) OR empty( $params['custom_width'] ) ) {
			$params['custom_width'] = '200px';
		}

		if ( isset( $params['width_type'] ) AND $params['width_type'] == 'custom' ) {
			$atts['custom_width'] = 'width';
			unset( $params['width_type'] );
		} elseif ( isset( $params['width_type'] ) AND $params['width_type'] == 'max' ) {
			$atts['custom_width'] = 'max-width';
			unset( $params['width_type'] );
		}

		return $this->translate_shortcode_design_options( $params, $atts );
	}

	/**
	 * Counter
	 *
	 */
	public function translate_us_counter( &$name, &$params, &$content ) {
		$changed = FALSE;

		// Force default values for correct work of "translate_shortcode_design_options" function
		if ( ! isset( $params['size'] ) OR empty( $params['size'] ) ) {
			$params['size'] = '5rem';
			$changed = TRUE;
		}

		$changed = $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'size' => 'font-size',
			'title_color' => 'color',
		) );

		return $changed;
	}

	/**
	 * ActionBox
	 *
	 */
	public function translate_us_cta( &$name, &$params, &$content ) {
		global $us_row_color_scheme;
		$atts = array();
		if ( isset( $params['color'] ) AND $params['color'] == 'custom' ) {
			if ( $us_row_color_scheme == 'custom' ) {
				$atts = array(
					'0' => 'padding',
				);
			} else {
				$atts = array(
					'text_color' => 'color',
					'bg_color' => 'background-color',
				);
				$params['color'] = 'light'; // change to "light" to avoid white text on default "primary"
			}
		}
		return $this->translate_shortcode_design_options( $params, $atts );
	}

	/**
	 * FlipBox
	 *
	 */
	public function translate_us_flipbox( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'custom_width' => 'width',
			'custom_height' => 'height',
		) );
	}

	/**
	 * Grid
	 *
	 */
	public function translate_us_grid( &$name, &$params, &$content ) {
		$changed = FALSE;
		if ( ! empty( $params['type'] ) AND $params['type'] == 'metro' ) {
			$params['items_gap'] = '0';
			$changed = TRUE;
		}
		if ( $this->translate_shortcode_design_options( $params ) ) {
			$changed = TRUE;
		}
		return $changed;
	}

	/**
	 * Map
	 *
	 */
	public function translate_us_gmaps( &$name, &$params, &$content ) {
		if ( ! empty( $params['height'] ) ) {
			$params['height'] = intval( $params['height'] ) . 'px';
		}
		return $this->translate_shortcode_design_options( $params, array(
			'map_bg_color' => 'background-color',
			'height' => 'height',
		 ) );
	}

	/**
	 * Interactive Banner
	 *
	 */
	public function translate_us_ibanner( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'desc_font_size' => 'font-size',
			'desc_line_height' => 'line-height',
			'bgcolor' => 'background-color',
			'textcolor' => 'color',
		) );
	}

	/**
	 * Interactive Text
	 *
	 */
	public function translate_us_itext( &$name, &$params, &$content ) {
		if ( isset( $params['animation_type'] ) AND $params['animation_type'] == 'flipInX' ) {
			unset( $params['animation_type'] );
		}
		if ( isset( $params['animation_type'] ) AND $params['animation_type'] == 'flipInXChars' ) {
			$params['animation_type'] = 'zoomInChars';
		}
		if ( isset( $params['animation_type'] ) AND $params['animation_type'] == 'zoomInChars' ) {
			if ( isset( $params['duration'] ) ) {
				$params['duration'] = floatval( $params['duration'] ) / 4;
			} else {
				$params['duration'] = '0.075';
			}
		}

		if ( ! isset( $params['font'] ) OR empty( $params['font'] ) ) {
			$params['font'] = 'body';
		}

		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
			'color' => 'color',
		) );
	}

	/**
	 * Message Box
	 *
	 */
	public function translate_us_message( &$name, &$params, &$content ) {
		$atts = array();
		if ( isset( $params['color'] ) AND $params['color'] == 'custom' ) {
			$atts = array(
				'text_color' => 'color',
				'bg_color' => 'background-color',
			);
		}
		return $this->translate_shortcode_design_options( $params, $atts );
	}

	/**
	 * Separator
	 *
	 */
	public function translate_us_separator( &$name, &$params, &$content ) {
		$atts = array(
			'title_size' => 'font-size',
		);
		if ( isset( $params['color'] ) AND $params['color'] == 'custom' ) {
			$atts['bdcolor'] = 'color';
			unset( $params['color'] );
		}
		return $this->translate_shortcode_design_options( $params, $atts );
	}

	/**
	 * Video
	 *
	 */
	public function translate_vc_video( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'max_width' => 'max-width',
		) );
	}

	/**
	 * Page Title
	 *
	 */
	public function translate_us_page_title( &$name, &$params, &$content ) {
		$name = 'us_post_title';

		if ( ! isset( $params['tag'] ) OR empty( $params['tag'] ) ) {
			$params['tag'] = 'h1';
		}

		if ( isset( $params['description'] ) ) {
			unset( $params['description'] );
			$params['migration_add_content'] = 1;
		}

		$this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
			'color' => 'color',
			'0' => 'margin-bottom',
		) );

		return TRUE;
	}

	/**
	 * Post Title
	 *
	 */
	public function translate_us_post_title( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
		) );
	}

	/**
	 * Post Author
	 *
	 */
	public function translate_us_post_author( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
		) );
	}

	/**
	 * Post Comments
	 *
	 */
	public function translate_us_post_comments( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
		) );
	}

	/**
	 * Post Content
	 *
	 */
	public function translate_us_post_content( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
		) );
	}

	/**
	 * Post Custom Field
	 *
	 */
	public function translate_us_post_custom_field( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
		) );
	}

	/**
	 * Post Date
	 *
	 */
	public function translate_us_post_date( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
		) );
	}

	/**
	 * Post Navigation
	 *
	 */
	public function translate_us_post_navigation( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array( 'size' => 'font-size' ) );
	}

	/**
	 * Post Taxonomy
	 *
	 */
	public function translate_us_post_taxonomy( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
		) );
	}

	/**
	 * Product Data
	 *
	 */
	public function translate_us_product_field( &$name, &$params, &$content ) {
		return $this->translate_shortcode_design_options( $params, array(
			'font' => 'font-family',
			'font_weight' => 'font-weight',
			'text_transform' => 'text-transform',
			'font_style' => 'font-style',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
		) );
	}

	/**
	 * Row
	 *
	 */
	public function translate_vc_wp_custommenu( &$name, &$params, &$content ) {
		$changed = FALSE;
		global $us_migration_current_post_id, $us_row_color_scheme, $usof_options;

		if ( isset( $params['layout'] ) AND $params['layout'] == 'hor' ) {
			$name = 'us_additional_menu';
			$params['source'] = $params['nav_menu'];

			// When horizontal menu was used in content (not in footer), it had "blocks" style
			if ( get_post_type( $us_migration_current_post_id ) != 'us_page_block' ) {
				$params['responsive_width'] = '';
				$params['main_style'] = 'blocks';
				$params['main_gap'] = '';
				$params['main_color_bg'] = '';
				$params['main_color_text'] = ( $us_row_color_scheme == 'primary' ) ? 'rgba(255,255,255,0.66)' : 'inherit';
				$params['main_color_text_hover'] = ( $us_row_color_scheme == 'primary' ) ? '#fff' : '';
				$params['main_color_bg_active'] = ( $us_row_color_scheme == 'primary' ) ? '' : $usof_options['color_content_primary'];
				$params['main_color_text_active'] = '#fff';
			} else {
				$params['main_color_text'] = '';
			}

			$this->translate_shortcode_design_options( $params, array( 'font_size' => 'font-size' ) );

			unset( $params['nav_menu'] );
			$changed = TRUE;
		}

		return $changed;
	}

	/**
	 * Custom Heading
	 *
	 */
	public function translate_vc_custom_heading( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['font_container'] ) ) {
			foreach ( explode( '|' , urldecode( $params[ 'font_container' ] ) ) as $_param ) {
				list( $param_name, $param_value ) = explode( ':' , $_param, 2 );

				if ( ! in_array( $param_name, array( 'tag', 'text_align' ) ) ) {
					$params[ $param_name ] = trim( $param_value );
				}
			}
		}

		$changed = $this->translate_shortcode_design_options( $params, array(
			'color' => 'color',
			'font_size' => 'font-size',
			'line_height' => 'line-height',
		));

		return $changed;
	}

	/**
	 * Theme Options
	 *
	 */
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		// Product Gallery
		if ( isset( $options['product_gallery'] ) AND is_array( $options['product_gallery'] ) ) {
			$options['product_gallery_options'] = $options['product_gallery'];
			$options['product_gallery'] = in_array( 'slider', $options['product_gallery'] ) ? 'slider' : 'gallery';
			$options['product_gallery_thumbs_gap'] = '0px';
			$changed = TRUE;
		}

		return $changed;
	}

	/**
	 * Headers
	 *
	 */
	public function translate_header_settings( &$settings ) {
		$settings_changed = FALSE;
		// Correction of the lower indentation that is set in header.css so as not to set with zero
		foreach ( us_arr_path( $settings, 'tablets.layout', array() ) as $elm_id => $child_elms ) {
			if ( strpos( $elm_id, 'vwrapper' ) !== FALSE ) {
				unset( $child_elms[ count( $child_elms ) - 1 ] );
				foreach ( $child_elms as $elm_id ) {
					if ( $settings['data'][$elm_id]['design_options']['margin_bottom_tablets'] === '' ) {
						$settings['data'][$elm_id]['design_options']['margin_bottom_tablets'] = '0.7rem';
					}
				}
			}
		}
		foreach ( $settings['data'] as $elm_id => &$data ) {
			if ( $this->translate_hb_gb_design_options( $data, $elm_id ) ) {
				$settings_changed = TRUE;
			}
		}

		return $settings_changed;
	}

	/**
	 * Grid layout
	 *
	 */
	public function translate_grid_layout_settings( &$settings ) {
		$settings_changed = FALSE;

		foreach ( $settings['data'] as $elm_id => &$data ) {
			if ( $this->translate_hb_gb_design_options( $data, $elm_id ) ) {
				$settings_changed = TRUE;
			}
		}

		return $settings_changed;
	}

	/**
	 * Migration HB/GB for new design options
	 *
	 */
	private function translate_hb_gb_design_options( &$data, $elm_id = '' ) {
		$changed = FALSE;
		$design_options = array();

		// Migration to new design options
		if ( ! empty( $data['design_options'] ) ) {
			foreach ( $data['design_options'] as $prop_name => $prop_value ) {
				if ( preg_match( '/^([a-z\_?]+)_(default|tablets|mobiles)$/' , $prop_name, $matches ) ) {
					$prop_name = str_replace( '_', '-', $matches[ 1 ] );
					$state = $matches[ 2 ];

					// Position
					if ( strpos( $prop_name, 'position-') !== FALSE AND $prop_value !== '' ) {
						$design_options[ $state ]['position'] = 'absolute';
						$prop_name = str_replace( 'position-', '', $prop_name );
					}

					// Border
					if ( strpos( $prop_name, 'border-') !== FALSE ) {
						$prop_name .= '-width';
						if ( ! isset( $design_options[ $state ][ 'border-style' ] ) AND $prop_value !== '' ) {
							$design_options[ $state ][ 'border-style' ] = 'solid';
						}
					}

					if (
						$state !== 'default'
						AND strpos( $elm_id, 'btn' ) === FALSE
						AND isset( $design_options[ 'default' ][ $prop_name ] )
						AND $design_options[ 'default' ][ $prop_name ] !== ''
						AND $prop_value == ''
					) {
						$prop_value = '0';
					}

					// Get image id and size
					$design_options[ $state ][ $prop_name ] = trim( $prop_value );

					$changed = TRUE;
				}
			}
		}

		// Move text styles to new design options
		foreach ( array( 'font', 'font_size', 'font_weight', 'text_transform', 'font_style', 'line_height' ) as $prop_name ) {
			if ( ! empty( $data[ $prop_name ] ) ) {
				if ( 'font' == $prop_name ) {
					$css_prop_name = 'font-family';
				} else {
					$css_prop_name = str_replace( '_', '-', $prop_name );
				}

				$design_options[ 'default' ][ $css_prop_name ] = trim( $data[ $prop_name ] );

				unset( $data[ $prop_name ] );
				$changed = TRUE;
			}
		}

		// Move font_size & line_height to new design options
		foreach ( array( 'tablets', 'mobiles' ) as $state ) {
			foreach ( array( 'font_size', 'line_height' ) as $prop_name ) {
				$option_name = $prop_name . '_' . $state;
				if ( ! empty( $data[ $option_name ] ) ) {
					$design_options[ $state ][ str_replace( '_', '-', $prop_name ) ] = trim( $data[ $option_name ] );

					unset( $data[ $option_name ] );
					$changed = TRUE;
				}
			}
		}

		// Move Grid Layout old design options to new
		foreach ( array( 'color_bg', 'color_border', 'color_text', 'width', 'border_radius' ) as $prop_name ) {
			if ( ! empty( $data[ $prop_name ] ) ) {
				switch ( $prop_name ) {
					case 'color_bg':
						$css_prop_name = 'background-color';
						break;
					case 'color_border':
						$css_prop_name = 'border-color';
						break;
					case 'color_text':
						$css_prop_name = 'color';
						break;
					case 'border_radius':
						$css_prop_name = 'border-radius';
						break;
					default:
						$css_prop_name = $prop_name;
						break;
				}

				$design_options[ 'default' ][ $css_prop_name ] = trim( $data[ $prop_name ] );

				unset( $data[ $prop_name ] );
				$changed = TRUE;
			}
		}

		// Move additional_menu, dropdown, socials options to new design options
		foreach ( array( 'additional_menu', 'dropdown', 'socials' ) as $elm_name ) {
			if ( strpos( $elm_id, $elm_name ) !== FALSE ) {
				foreach ( array( 'size', 'size_tablets', 'size_mobiles' ) as $prop_name ) {
					if ( ! empty( $data[ $prop_name ] ) AND preg_match( '/size(_(\w+))?/', $prop_name, $matches ) ) {
						$state = isset( $matches[2] ) ? $matches[2] : 'default';
						$design_options[ $state ]['font-size'] = trim( $data[ $prop_name ] );

						unset( $data[ $prop_name ] );
						$changed = TRUE;
					}
				}
			}
		}

		// Change image "Height" option name
		if ( strpos( $elm_id, 'image' ) !== FALSE ) {
			if ( ! empty( $data['height'] ) ) {
				$data['height_default'] = $data['height'];
				unset( $data['height'] );
				$changed = TRUE;
			}
		}

		// Move text options to new design options
		if ( strpos( $elm_id, 'text' ) !== FALSE ) {
			if ( ! empty( $data['color'] ) ) {
				$design_options['default']['color'] = trim( $data['color'] );
				$changed = TRUE;
			}
		}

		// Change "color" param to "icons_color"
		if ( strpos( $elm_id, 'socials' ) !== FALSE ) {
			if ( ! empty( $data['color'] ) ) {
				$data['icons_color'] = $data['color'];
				unset( $data['color'] );
				$changed = TRUE;
			}
		}

		// Move gradient overlay of Vertical Wrapper to new design options
		if ( strpos( $elm_id, 'vwrapper' ) !== FALSE ) {
			if ( ! empty( $data['bg_gradient'] ) AND ! empty( $data['color_grad'] ) ) {

				$design_options['default']['background-color'] = 'linear-gradient(180deg,rgba(0,0,0,0),' . $data['color_grad'] . ')';

				unset( $data['bg_gradient'] );
				unset( $data['color_grad'] );
				$changed = TRUE;
			}
		}

		// Migrate additional_menu indents
		if ( strpos( $elm_id, 'additional_menu' ) !== FALSE ) {
			if ( isset( $data['indents'] ) ) {
				$data['main_gap'] = $data['indents'];

				unset( $data['indents'] );
				$changed = TRUE;
			}
		}

		// Saving parameters as an array
		$data[ 'css' ] = $design_options;
		unset( $data[ 'design_options' ] );

		return $changed;
	}

	/**
	 * Migration of parameters and styles to a new format of design options
	 *
	 */
	private function translate_shortcode_design_options( &$params, $atts = array() ) {
		$changed = FALSE;
		$props = array();

		// Parsing css parameters
		if ( ! empty( $params['css'] ) ) {
			// Directions for expanding CSS properties
			// NOTE: The order of the elements in the array is important.
			$css_directions = array( 'top', 'right', 'bottom', 'left' );

			$params['css'] = preg_replace( '/(.?vc_custom_([a-z0-9]+)\{(.*?))([^\}]+)(\})/', "$4", $params['css'] );
			$params['css'] = str_replace( 'background:', 'background-color:', $params['css'] );

			if ( preg_match( '/url\((.*)\)\s?/', $params['css'], $matches ) ) {
				if ( $url = $matches[1] ) {
					$params['css'] = str_replace( $matches[0] , '', $params['css'] );
					$params['css'] .= sprintf( 'background-image: %s;', trim( $matches[0] ) );
				}
			}

			foreach ( explode( ';', $params['css'] ) as $param ) {
				if ( ! empty( $param ) AND strpos( $param, ':' ) !== FALSE ) {
					list( $prop_name, $prop_value ) = explode( ':', $param, 2 );
					$prop_name = trim( $prop_name );
					$prop_value = trim( str_replace( '!important', '', $prop_value ) );

					// We save the values in one parameter
					if ( preg_match( '/border-(\w+)-([style|color]+)/' , $prop_name, $matches ) ) {
						$props[ sprintf( 'border-%s', $matches[2] ) ] = $prop_value;
						continue;
					}

					// Extract border if it is set in one parameter
					if ( $prop_name == 'border' ) {
						$arr_prop_value = explode( ' ', $prop_value );
						$count_values = count( $arr_prop_value );
						if ( $count_values == 3 ) {
							$border_width = trim( $arr_prop_value[0] );
							$border_style = trim( $arr_prop_value[1] );
							$border_color = trim( $arr_prop_value[2] );
							if ( ! in_array( $border_style, array( 'solid', 'dashed', 'dotted', 'double' ) ) ) {
								$border_style = 'solid';
							}
							$props['border-style'] = $border_style;
							$props['border-color'] = $border_color;
							foreach ( $css_directions as $index => $position ) {
								$border_width_prop_name = 'border-' . $position . '-width';
								$props[ $border_width_prop_name ] = $border_width;
							}
						}
						// Migrate padding, margin and border-width
					} elseif ( in_array( $prop_name, array( 'padding', 'margin', 'border-width' ) ) ) {

						$arr_prop_value = strpos( $prop_value, 'calc' ) === FALSE
							? explode( ' ', $prop_value )
							: array( $prop_value );
						$count_values = count( $arr_prop_value );

						if ( $count_values === 4 OR $count_values === 1 ) {
							foreach ( $css_directions as $index => $position ) {

								switch ( $prop_name ) {
									case 'border-width':
										$new_prop_name = sprintf( 'border-%s-width', $position );
										break;
									default:
										$new_prop_name = $prop_name . '-' . $position;
										break;
								}

								$new_value = isset( $arr_prop_value[ $index ] )
									? $arr_prop_value[ $index ]
									: $arr_prop_value[ 0 ];

								$props[ $new_prop_name ] = trim( $new_value );
							}
							unset( $props[ $prop_name ] );
						}
					} else {
						// Image get id and size
						$props[ trim( $prop_name ) ] = trim( $prop_value );
					}
				}
			}
			$changed = TRUE;
		}
		$css = array( 'default' => $props );
		unset( $props );

		// Move params to design options
		foreach ( $atts as $attr_name => $css_prop ) {
			if ( isset( $params[ $attr_name ] ) ) {
				if ( $value = trim( $params[ $attr_name ] ) ) {
					$state = preg_match( '/^([a-z\_?]+)_(default|tablets|mobiles)$/' , $attr_name, $matches )
						? $matches[2]
						: 'default';

					$css[ $state ][ $css_prop ] = $value;
				}
				unset( $params[ $attr_name ] );
				$changed = TRUE;

				// Used in us_cta to reset padding and margin-bottom to 0
			} elseif ( $attr_name == '0' ) {
				if ( $css_prop == 'padding' ) {
					$css['default']['padding-top'] = '0';
					$css['default']['padding-right'] = '0';
					$css['default']['padding-bottom'] = '0';
					$css['default']['padding-left'] = '0';
				} else {
					$css['default'][ $css_prop ] = $attr_name;
				}
				$changed = TRUE;

				// Used in vc_column to fix background size and position
			} elseif ( $attr_name == 'bg_column_fix' AND ! empty( $css['default']['background-image'] ) ) {
				$css['default']['background-position'] = '50%';
				if ( ! empty( $css['default']['background-size'] ) ) {
					$css['default']['background-size'] = 'cover';
				}

				$changed = TRUE;
			}
		}

		if ( is_array( $css ) AND ! empty( $css ) AND ! empty( $css['default'] ) ) {
			$params['css'] = rawurlencode( json_encode( $css ) );
		}

		return $changed;
	}

}
