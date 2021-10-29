<?php

class us_migration_3_8 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
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

		return $changed;
	}

	public function translate_us_cta( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['btn_icon'] ) ) {
			$new_icon = $this->translate_icon_class( $params['btn_icon'] );

			if ( $new_icon != $params['btn_icon'] ) {
				$params['btn_icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		if ( ! empty( $params['btn2_icon'] ) ) {
			$new_icon = $this->translate_icon_class( $params['btn2_icon'] );

			if ( $new_icon != $params['btn2_icon'] ) {
				$params['btn2_icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_iconbox( &$name, &$params, &$content ) {
		if ( ! empty( $params['icon'] ) ) {
			$new_icon = $this->translate_icon_class( $params['icon'] );

			if ( $new_icon != $params['icon'] ) {
				$params['icon'] = $new_icon;
			}
		}

		if ( ! empty( $params['style'] ) AND ( $params['style'] == 'circle' OR $params['style'] == 'outlined' ) ) {
			if ( ! empty( $params['size'] ) AND $params['size'] == 'tiny' ) {
				$params['size'] = '20px';
				if ( empty( $params['title_size'] ) ) {
					$params['title_size'] = '20px';
				}
			} elseif ( ! empty( $params['size'] ) AND $params['size'] == 'small' ) {
				$params['size'] = '28px';
			} elseif ( empty( $params['size'] ) OR $params['size'] == 'medium' ) {
				$params['size'] = '36px';
			} elseif ( ! empty( $params['size'] ) AND $params['size'] == 'large' ) {
				$params['size'] = '44px';
			} elseif ( ! empty( $params['size'] ) AND $params['size'] == 'huge' ) {
				$params['size'] = '52px';
			}
		} else {
			if ( ! empty( $params['size'] ) AND $params['size'] == 'tiny' ) {
				$params['size'] = '24px';
				if ( empty( $params['title_size'] ) ) {
					$params['title_size'] = '20px';
				}
			} elseif ( ! empty( $params['size'] ) AND $params['size'] == 'small' ) {
				$params['size'] = '36px';
			} elseif ( empty( $params['size'] ) OR $params['size'] == 'medium' ) {
				$params['size'] = '48px';
			} elseif ( ! empty( $params['size'] ) AND $params['size'] == 'large' ) {
				$params['size'] = '70px';
			} elseif ( ! empty( $params['size'] ) AND $params['size'] == 'huge' ) {
				$params['size'] = '90px';
				if ( ! empty( $params['img'] ) AND $params['iconpos'] != 'left' ) {
					$params['size'] = '350px';
				}
			}
		}

		return TRUE;
	}

	public function translate_us_message( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['icon'] ) ) {
			$new_icon = $this->translate_icon_class( $params['icon'] );

			if ( $new_icon != $params['icon'] ) {
				$params['icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_person( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['custom_icon'] ) ) {
			$new_icon = $this->translate_icon_class( $params['custom_icon'] );

			if ( $new_icon != $params['custom_icon'] ) {
				$params['custom_icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_pricing( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['items'] ) ) {
			$items = json_decode( urldecode( $params['items'] ), TRUE );
			if ( is_array( $items ) ) {
				foreach ( $items as $index => $item ) {
					if ( ! empty( $item['btn_icon'] ) ) {
						$new_icon = $this->translate_icon_class( $item['btn_icon'] );

						if ( $new_icon != $item['btn_icon'] ) {
							$items[$index]['btn_icon'] = $new_icon;

							$changed = TRUE;
						}
					}
				}

				if ( $changed ) {
					$params['items'] = urlencode( json_encode( $items ) );
				}
			}
		}

		return $changed;
	}

	public function translate_us_separator( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['icon'] ) ) {
			$new_icon = $this->translate_icon_class( $params['icon'] );

			if ( $new_icon != $params['icon'] ) {
				$params['icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		return $changed;
	}

	public function translate_us_social_links( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['custom_icon'] ) ) {
			$new_icon = $this->translate_icon_class( $params['custom_icon'] );

			if ( $new_icon != $params['custom_icon'] ) {
				$params['custom_icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		if ( ! empty( $params['style'] ) AND $params['style'] == 'colored' ) {
			$params['color'] = 'brand';

			$changed = TRUE;
		} elseif ( empty( $params['style'] ) OR $params['style'] == 'desaturated' ) {
			$params['color'] = 'text';

			$changed = TRUE;
		}

		if ( empty( $params['style'] ) OR $params['style'] != 'outlined_square' ) {
			$params['style'] = 'outlined_square';

			$changed = TRUE;
		}

		return $changed;
	}

	public function translate_vc_tta_section( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['icon'] ) ) {
			$new_icon = $this->translate_icon_class( $params['icon'] );

			if ( $new_icon != $params['icon'] ) {
				$params['icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		return $changed;
	}

	// Options
	public function translate_theme_options( &$options ) {
		$changed = FALSE;

		if ( ! empty( $options['header_contacts_custom_icon'] ) ) {
			$new_icon = $this->translate_icon_class( $options['header_contacts_custom_icon'] );

			if ( $new_icon != $options['header_contacts_custom_icon'] ) {
				$options['header_contacts_custom_icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		if ( ! empty( $options['header_socials_custom_icon'] ) ) {
			$new_icon = $this->translate_icon_class( $options['header_socials_custom_icon'] );

			if ( $new_icon != $options['header_socials_custom_icon'] ) {
				$options['header_socials_custom_icon'] = $new_icon;

				$changed = TRUE;
			}
		}

		if ( isset( $options['header']['data'] ) AND is_array( $options['header']['data'] ) ) {
			foreach ( $options['header']['data'] as $index => $item ) {
				if ( ! empty( $item['icon'] ) ) {
					$new_icon = $this->translate_icon_class( $item['icon'] );

					if ( $new_icon != $item['icon'] ) {
						$options['header']['data'][$index]['icon'] = $new_icon;

						$changed = TRUE;
					}
				}

				if ( ! empty( $item['custom_icon'] ) ) {
					$new_icon = $this->translate_icon_class( $item['custom_icon'] );

					if ( $new_icon != $item['custom_icon'] ) {
						$options['header']['data'][$index]['custom_icon'] = $new_icon;

						$changed = TRUE;
					}
				}

				if ( substr( $index, 0, 7 ) == 'socials' ) {

					if ( ! empty( $item['style'] ) ) {
						if ( $item['style'] == 'colored' ) {
							$options['header']['data'][$index]['color'] = 'brand';

							$changed = TRUE;
						} elseif ( $item['style'] == 'desaturated' ) {
							$options['header']['data'][$index]['color'] = 'text';

							$changed = TRUE;
						}
					}

				}
			}
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
		} elseif ( $name == 'us_socials' ) {
			if ( ! empty( $instance['color'] ) ) {
				if ( $instance['color'] == 'colored' ) {
					$instance['color'] = 'brand';

					$changed = TRUE;
				} elseif ( $instance['color'] == 'desaturated' ) {
					$instance['color'] = 'text';

					$changed = TRUE;
				}
			}

			if ( empty( $instance['style'] ) OR $instance['style'] != 'outlined_square' ) {
				$instance['style'] = 'outlined_square';

				$changed = TRUE;
			}

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
