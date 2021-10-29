<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'us_add_callbacks_to_search_used_icons' ) ) {
	add_filter( 'us_config_assets', 'us_add_callbacks_to_search_used_icons', 2, 1 );
	/**
	 * Adds check functions to the assets configuration to find icons
	 *
	 * This filter is necessary to add callback functions for assets with `search_icons=TRUE`
	 * to search for icons in the content through the already existing functionality for auto-optimization.
	 *
	 * @param array $config The asset configuration
	 * @return array
	 */
	function us_add_callbacks_to_search_used_icons( $config ) {
		/**
		 * Saving used icon sets
		 *
		 * @param array $icons The icons
		 * @param WP_Post $post
		 */
		$func_save_used_icons = function( $icons, $post ) {
			if ( empty( $icons ) ) {
				return;
			}

			global $_us_used_icons;
			if ( empty( $_us_used_icons ) ) {
				$_us_used_icons = array(
					'icons' => array(),
					'posts' => array(),
				);
			}

			// The list of icons should always be a unique array, let's check this and,
			// if necessary, convert to this format
			$icons = is_array( $icons )
				? array_unique( $icons )
				: array( $icons );

			foreach( $icons as $i => $icon ) {
				if (
					strpos( $icon, '|' ) !== FALSE
					AND ! in_array( $icon, $_us_used_icons['icons'] )
				) {
					$_us_used_icons['icons'][] = $icon;
				}
			}
			$_us_used_icons['icons'] = array_unique( $_us_used_icons['icons'] );

			// Save IDs of posts, where icons were found
			if ( property_exists( $post, 'ID' ) ) {
				foreach( $icons as $icon ) {
					$_us_used_icons['posts'][ $icon ][] = $post->ID;
				}
			}
		};

		/**
		 * Get a list of icons from socials link settings
		 *
		 * @param array $items The items
		 * @return array
		 */
		$func_get_socials_icons = function( $items ) {
			$icons = array();
			if ( ! empty( $items ) AND is_array( $items ) ) {
				foreach ( $items as $item ) {
					$type = us_arr_path( $item, 'type' );
					$map = array(
						's500px' => '500px',
						'vimeo' => 'vimeo-v',
						'wechat' => 'weixin',
					);

					if ( $type === 'custom' ) {
						$icons[] = us_arr_path( $item, 'icon' );

					} elseif ( $type === 'email' ) {
						$icons[] = ( US_THEMENAME == 'Zephyr' ) ? 'material|email' : 'fas|envelope';

					} elseif ( $type === 'rss' ) {
						$icons[] = ( US_THEMENAME == 'Zephyr' ) ? 'material|rss_feed' : 'fas|rss';

					} else {
						$icons[] = 'fab|' . us_arr_path( $map, $type, $type );
					}
				}
			}
			return $icons;
		};

		/**
		 * Decodes data from an attribute
		 *
		 * @param string $str The string of encoded data
		 * @return string
		 */
		$func_decode = function( $str ) {
			if ( empty( $str ) ) {
				return '';
			}
			return rawurldecode( base64_decode( wp_strip_all_tags( $str ) ) );
		};

		// Sets auto_optimize_callback for finding icons in content
		// item['option'] - Regular expressions to find icon option, example: `fas|home` etc.
		// item['html'] - Regular expressions to find html icon, example: `<i class="fas fa-home"></i>` etc.
		$icon_patterns = array(
			'font-awesome' => array(
				'option' => '/((fa[r|s|l|b|d])\|([\w\-\_]+))(\")/',
				'html' => '/<i.*?class=("|\').*?(fa[r|s|l|b|d])[\s]+fa-([\w\-\_]+).*("|\')/',
			),
			'font-awesome-duotone' => array(
				'option' => '/(fad\|([\w\-\_]+))(\")/',
				'html' => '/<i.*?class=("|\').*?(fad)[\s]+fa-([\w\-\_]+).*("|\')/',
			),
			'material' => array(
				'option' => '/(material\|([\w\-\_]+))(\")/',
				'html' => '/<.*?class=("|\').*?(material).*("|\').*>([\w\-\_]+)<\//',
			),
		);

		// Add functions to config
		foreach ( $icon_patterns as $asset => $icon_pattern ) {
			if ( ! empty( $config[ $asset ]['search_icons'] ) ) {
				$config[ $asset ]['auto_optimize_callback'] = array(
					/**
					 * @return bool Checking icons should certainly return FALSE to be able to collect all icons into an array
					 */
					'shortcodes' => function ( $shortcode_name, $atts, $post ) use ( $func_save_used_icons, $func_get_socials_icons, $func_decode, $icon_pattern ) {
						$post_content = $post->post_content;

						// Any shortcode attribute with icon-like value
						if ( preg_match_all( $icon_pattern['option'], $post_content, $matches ) ) {
							$func_save_used_icons( $matches[1], $post );
						}

						// Metabox setting of post
						if ( $icon = get_metadata( 'post', $post->ID, 'us_tile_icon', TRUE ) ) {
							$func_save_used_icons( $icon, $post );
						}

						// Default IconBox icon
						if (
							$shortcode_name == 'us_iconbox'
							AND ! isset( $atts['icon'] )
							AND ! isset( $atts['img'] )
						) {
							$func_save_used_icons( 'fas|star', $post );
						}

						// Default Breadcrumbs icon
						if (
							$shortcode_name == 'us_breadcrumbs'
							AND ! isset( $atts['separator_icon'] )
							AND ! isset( $atts['separator_type'] )
						) {
							$func_save_used_icons( 'fas|angle-right', $post );
						}

						// Default Search icon
						if (
							$shortcode_name == 'us_search'
							AND ! isset( $atts['icon'] )
						) {
							$func_save_used_icons( 'fas|search', $post );
						}

						// Pricing, Contact Form elements
						if (
							in_array( $shortcode_name, array( 'us_cform', 'us_pricing' ) )
							AND ! empty( $atts['items'] )
							AND preg_match_all( $icon_pattern['option'], urldecode( $atts['items'] ), $matches )
						) {
							$func_save_used_icons( $matches[1], $post );
						}

						// Person element
						if ( $shortcode_name == 'us_person' ) {
							$social_links = array(
								'email' => ( US_THEMENAME == 'Zephyr' ) ? 'material|email' : 'fas|envelope',
								'facebook' => 'fab|facebook',
								'twitter' => 'fab|twitter',
								'google_plus' => 'fab|google',
								'linkedin' => 'fab|linkedin',
								'skype' => 'fab|skype',
							);
							foreach( $social_links as $type => $icon ) {
								if ( ! empty( $atts[ $type ] ) ) {
									$func_save_used_icons( $icon, $post );
								}
							}
							if ( ! empty( $atts['custom_icon'] ) ) {
								$func_save_used_icons( $atts['custom_icon'], $post );
							}
						}

						// Sharing Buttons element
						if (
							$shortcode_name == 'us_sharing'
							AND ! empty( $atts['providers'] )
						) {
							foreach( explode( ',', $atts['providers'] ) as $type ) {
								if ( $type == 'email' ) {
									$icon = ( US_THEMENAME == 'Zephyr' ) ? 'material|email' : 'fas|envelope';
								} else {
									$icon = 'fab|' . $type;
								}
								$func_save_used_icons( $icon, $post );
							}

							// Add "Copy" icon
							if ( ! empty( $atts['text_selection'] ) ) {
								$icon = ( US_THEMENAME == 'Zephyr' ) ? 'material|file_copy' : 'fas|copy';
								$func_save_used_icons( $icon, $post );
							}
						}

						// Social Links element
						if (
							$shortcode_name == 'us_socials'
							AND $items = us_arr_path( $atts, 'items' )
							AND $items = json_decode( urldecode( $items ), TRUE )
						) {
							$func_save_used_icons( $func_get_socials_icons( $items ), $post );
						}

						// Adding content from `vc_raw_html` to $post_content for full search
						if (
							$post_content
							AND $shortcode_name === 'vc_raw_html'
							AND preg_match_all( '/' . get_shortcode_regex( array( 'vc_raw_html' ) ) . '/', $post_content, $matches )
						) {
							foreach ( us_arr_path( $matches, '5', array() ) as $raw_html ) {
								if ( $raw_html = $func_decode( $raw_html ) ) {
									$post_content .= $raw_html;
								}
							}
						}
						unset( $matches );

						// Adding content from `marker_text` to $post_content for full search
						if (
							$shortcode_name === 'us_gmaps'
							AND ! empty( $atts[ 'marker_text' ] )
							AND $marker_text = $func_decode( $atts[ 'marker_text' ] )
						) {
							$post_content .= $marker_text;
						}

						// Icon as a HTML code
						if (
							$post_content
							AND preg_match_all( $icon_pattern['html'], $post_content, $matches )
						) {
							$icons = array();
							if ( in_array( 'material', $matches[/* Sets */2] ) ) {
								foreach ( $matches[/* Icon names */4] as $icon_name ) {
									$icons[] = "material|{$icon_name}";
								}
							} else {
								foreach ( $matches[/* Icon sets */2] as $index => $icon_set ) {
									$icons[] = $icon_set . '|' . $matches[/* Icon names */3][ $index ];
								}
							}
							if ( $icons ) {
								$func_save_used_icons( $icons, $post );
							}
						}

						return FALSE;
					},
					/**
					 * @return bool Checking icons should certainly return FALSE to be able to collect all icons into an array
					 */
					'headers_or_grid_layouts' => function ( $element_name, $atts, $post ) use ( $func_save_used_icons, $func_get_socials_icons, $func_decode, $icon_pattern ) {
						// Social Link element
						if ( $element_name === 'socials' AND ! empty( $atts['items'] ) ) {
							$func_save_used_icons( $func_get_socials_icons( $atts['items'] ), $post );

							// Cart, Button, Search, Text elements
							// Post Date, Taxonomy, Author, Comments and Custom field elements
						} elseif (
							in_array( $element_name, array(
								// Regular elements
								'text',
								'search',
								'btn',
								'cart',
								// Post elements
								'post_date',
								'post_taxonomy',
								'post_author',
								'post_comments',
								'post_custom_field',
							) )
							AND ! empty( $atts['icon'] ) ) {
							$func_save_used_icons( $atts['icon'], $post );

							// Dropdown element
						} elseif ( $element_name === 'dropdown' ) {
							$_icons = array();
							if ( ! empty( $atts['link_icon'] )  ) {
								$_icons[] = $atts['link_icon'];
							}
							$links = us_arr_path( $atts, 'links' );
							if ( is_array( $links ) ) {
								foreach ( $links as $link ) {
									if ( $_icon = us_arr_path( $link, 'icon' ) ) {
										$_icons[] = $_icon;
									}
								}
							}
							$func_save_used_icons( $_icons, $post );

							// Finding icons in custom html
						} else if (
							$element_name === 'html'
							AND ! empty( $atts['content'] )
							AND $html_content = $func_decode( $atts['content'] )
							AND preg_match_all( $icon_pattern['html'], $html_content, $matches )
						) {
							$icons = array();
							if ( in_array( 'material', $matches[/* Sets */2] ) ) {
								foreach ( $matches[/* Icon names */4] as $icon_name ) {
									$icons[] = "material|{$icon_name}";
								}
							} else {
								foreach ( $matches[/* Icon sets */2] as $index => $icon_set ) {
									$icons[] = $icon_set . '|' . $matches[/* Icon names */3][ $index ];
								}
							}
							if ( $icons ) {
								$func_save_used_icons( $icons, $post );
							}
						}

						return FALSE;
					},
				);
			}
		}

		return $config;
	}
}

if ( class_exists( 'US_Auto_Optimize_Assets' ) AND ! class_exists( 'US_Get_Used_Icons' ) ) {
	/**
	 * Handler class for data retrieval based on US_Auto Optimize Assets functionality
	 * @dependency US_Auto_Optimize_Assets
	 */
	class US_Get_Used_Icons extends US_Auto_Optimize_Assets {
		/**
		 * The key by which data will be stored in the db
		 *
		 * @var string
		 */
		const OPTION_NAME = 'us_used_icons';

		/**
		 * Allowed names (assets)
		 *
		 * Assets and their callback functions that will work within this class,
		 * all others will be ignored.
		 *
		 * @var array
		 */
		public static $allowed_names = array(
			'font-awesome',
			'font-awesome-duotone',
			'material',
		);

		/**
		 * Class initialization
		 *
		 * Overriding the method and clearing the configuration from
		 * unnecessary assets in accordance with the allowed assets
		 */
		public function __construct() {

			// Prefix overrides
			$this->hook_prefix = 'used_icons';
			parent::__construct();

			// Removing unnecessary callbacks, leave only for searching icons
			foreach ( $this->callbacks as &$callbacks ) {
				foreach ( $callbacks as $name => $callback ) {
					if ( ! in_array( $name, self::$allowed_names ) ) {
						unset( $callbacks[ $name ] );
					}
				}
			}
			unset( $callbacks );
		}

		/**
		 * @var mixed
		 */
		private static $data;

		/**
		 * Get group from found data
		 *
		 * @param string $group
		 * @return array
		 */
		public static function get_data( $group ) {
			if ( ! static::$data ) {
				static::$data = get_option( '_' . self::OPTION_NAME );
			}
			return ( ! empty( static::$data[ $group ] ) AND is_array( static::$data[ $group ] ) )
				? static::$data[ $group ]
				: array();
		}
	}
}

if ( ! function_exists( 'us_is_fallback_icon' ) ) {
	/**
	 * Icons located in fallback font and do not require CSS
	 *
	 * @param string $icon This is the name without icon set
	 * @return bool
	 */
	function us_is_fallback_icon( $icon_name ) {
		return in_array( $icon_name, array(
			'angle-down',
			'angle-left',
			'angle-right',
			'angle-up',
			'bars',
			'check',
			'comments',
			'copy',
			'envelope',
			'map-marker-alt',
			'mobile',
			'phone',
			'play',
			'quote-left',
			'search',
			'search-plus',
			'shopping-cart',
			'star',
			'tags',
			'times',
		) );
	}
}

if ( ! function_exists( 'us_save_found_icons_to_db' ) ) {
	// Hooks that are called after searching for icons for each iteration
	add_action( 'us_auto_optimize_assets_run', 'us_save_found_icons_to_db' );
	add_action( 'us_used_icons_run', 'us_save_found_icons_to_db' );
	/**
	 * Save all found icons to the database
	 *
	 * This method will check global variables for the presence of icons in the iteration,
	 * then filter and add them to the general list of found icons, which will be saved to the database.
	 *
	 * @param US_Auto_Optimize_Assets|US_Get_Used_Icons $self This is an instance of the class from which the method was called
	 */
	function us_save_found_icons_to_db( $self ) {
		global $_us_used_icons;
		$option_name = '_' . US_Get_Used_Icons::OPTION_NAME;

		$used_icons = array();

		// In the first iteration, forcibly clear the cache
		if ( $self->get_current_page() === 1 ) {
			delete_option( $option_name );

			// Get data from a temporary cache
		} elseif ( $data = get_option( $option_name ) AND is_array( $data ) ) {
			$used_icons = (array) $data;
		}

		// Save data to temporary cache
		if ( ! empty( $_us_used_icons ) AND is_array( $_us_used_icons ) ) {

			// List of icons
			if ( ! empty( $_us_used_icons['icons'] ) ) {
				if ( empty( $used_icons['icons'] ) ) {
					$used_icons['icons'] = array();
				}
				$used_icons['icons'] = array_merge( $used_icons['icons'], $_us_used_icons['icons'] );
				$used_icons['icons'] = array_unique( $used_icons['icons'] );
			}

			// List of posts ids
			if ( ! empty( $_us_used_icons['posts'] ) ) {
				if ( empty( $used_icons['posts'] ) ) {
					$used_icons['posts'] = array();
				}
				foreach ( $_us_used_icons['posts'] as $icon => $posts ) {
					if (
						! empty( $used_icons['posts'][ $icon ] )
						AND is_array( $used_icons['posts'][ $icon ] )
					) {
						$posts = array_merge( $posts, $used_icons['posts'][ $icon ] );
					}
					$posts = array_unique( $posts );
					$used_icons['posts'][ $icon ] = $posts;
				}
			}

			update_option( $option_name, $used_icons );
		}
	}
}
