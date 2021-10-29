<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Breadcrumbs
 *
 * @param $delimiter string
 * @param $home string Home label text
 * @param $item_before string
 * @param $item_after string
 * @param $link_attr string
 * @param $name_attr string
 * @param $position_attr string
 *
 * @return string
 */
class US_Breadcrumbs {

	protected $config;
	protected $counter;

	public function __construct( $delimiter, $home, $item_before, $item_after, $link_attr, $name_attr, $position_attr ) {
		// Homepage Label
		$this->config['home'] = isset( $home ) ? $home : us_translate( 'Home' );
		// Separator between crumbs
		$this->config['delimiter'] = isset( $delimiter ) ? $delimiter : ' > ';
		// Code before the current crumb
		$this->config['before'] = isset( $item_before ) ? $item_before : '';
		$this->config['before_basic'] = '<li class="g-breadcrumbs-item">';
		// Code after the current crumb
		$this->config['after'] = isset( $item_after ) ? $item_after : '';
		$this->config['after_basic'] = '</li>';
		// Links attributes
		$this->config['link_attr'] = isset( $link_attr ) ? $link_attr : '';
		// Name attributes
		$this->config['name_attr'] = isset( $name_attr ) ? $name_attr : '';
		$this->config['name_before'] = $this->config['name_after'] = '';
		if ( ! empty( $this->config['name_attr'] ) ) {
			$this->config['name_before'] = '<span' . $this->config['name_attr'] . '>';
			$this->config['name_after'] = '</span>';
		}

		// Position attributes
		$this->config['position_attr'] = isset( $position_attr ) ? $position_attr : '';

		// Predefined link attributes
		$this->config['homeLink'] = ( has_filter( 'us_tr_home_url' ) )
			? apply_filters( 'us_tr_home_url', NULL )
			: trailingslashit( home_url() );

		// Predefined text
		$this->config['text']['search'] = __( 'Search Results', 'us' ); // text for a search results page
		$this->config['text']['404'] = us_translate( 'Page not found' ); // text for the 404 page
		$this->config['text']['forums'] = us_translate( 'Forums', 'bbpress' ); // text for the forums page

		$this->counter = 1;
	}

	private function replace_callback( $matches ) {
		$output = $matches[1] . '<meta ' . $this->config['position_attr'] . ' content="' . $this->counter ++ . '"/>';

		return $output;
	}

	private function get_breadcrumb_with_markup( $input = NULL ) {
		if ( empty( $this->config['position_attr'] ) AND $input ) {
			return $input;
		}

		if ( ! $input ) {
			return '<meta' . $this->config['position_attr'] . ' content="' . $this->counter ++ . '"/>';
		}

		$output = preg_replace_callback( '#(<\/a>)#', array( $this, 'replace_callback' ), $input );

		return $output;
	}

	private function get_breadcrumb( $link, $name ) {
		$output = $this->config['before'];
		$output .= '<a' . $this->config['link_attr'] . ' href="' . $link . '">';
		$output .= $this->config['name_before'] . $name . $this->config['name_after'];
		$output .= '</a>';
		$output .= $this->get_breadcrumb_with_markup();
		$output .= $this->config['after'];

		return $output;
	}

	public function render() {
		global $post;
		$output = '';

		// "Home" crumb
		if ( ! empty( $this->config['home'] ) ) {
			$output .= $this->get_breadcrumb( $this->config['homeLink'], $this->config['home'] );
			$output .= $this->config['delimiter'];
		}

		// Check whether The Events Calendar is activated and we're within it's archive
		$is_events_calendar = FALSE;
		if ( function_exists( 'tribe_is_month' ) AND function_exists( 'tribe_is_upcoming' ) AND function_exists( 'tribe_is_past' ) AND function_exists( 'tribe_is_day' ) ) {
			if ( tribe_is_month() OR tribe_is_upcoming() OR tribe_is_past() OR tribe_is_day() ) {
				$is_events_calendar = TRUE;
			}
		}

		// Posts page
		if ( is_home() ) {
			$home_page_id = get_option( 'page_for_posts', TRUE );
			$output .= $this->get_breadcrumb( get_permalink( $home_page_id ), get_the_title( $home_page_id ) );

			// Category
		} elseif ( is_category() ) {
			$thisCat = get_category( get_query_var( 'cat' ), FALSE );
			if ( $thisCat->parent != 0 ) {
				$cats = get_category_parents( $thisCat->parent, TRUE, $this->config['delimiter'] );
				$cats = preg_replace( '#(<a\shref[^>]+>)([^<\/]+)(<\/a>)#', '${1}' . $this->config['name_before'] . '${2}' . $this->config['name_after'] . '${3}', $cats );
				$cats = str_replace( '<a', $this->config['before'] . '<a' . $this->config['link_attr'], $cats );
				$cats = str_replace( '</a>', '</a>' . $this->config['after'], $cats );
				$cats = $this->get_breadcrumb_with_markup( $cats );
				$output .= $cats;
			}
			$output .= $this->get_breadcrumb( get_category_link( get_query_var( 'cat' ) ), single_cat_title( '', FALSE ) );

			// Tag
		} elseif ( is_tag() ) {
			$output .= $this->get_breadcrumb( get_term_link( get_query_var( 'tag_id' ) ), single_tag_title( '', FALSE ) );

			// Author
		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata( $author );
			$output .= $this->get_breadcrumb( get_author_posts_url( $userdata->ID ), $userdata->display_name );

			// 404 page
		} elseif ( is_404() ) {
			$output .= $this->config['before_basic'] . $this->config['text']['404'] . $this->config['after_basic'];

			// Search Results
		} elseif ( is_search() ) {
			$output .= $this->config['before_basic'] . $this->config['text']['search'] . $this->config['after_basic'];

			// Day
		} elseif ( is_day() ) {
			$day_link = get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) );
			$output .= $this->get_breadcrumb( get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) );
			$output .= $this->config['delimiter'];
			$output .= $this->get_breadcrumb( get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), __( get_the_time( 'F' ), 'us' ) );
			$output .= $this->config['delimiter'];
			$output .= $this->get_breadcrumb( $day_link, get_the_time( 'd' ) );

			// Month
		} elseif ( is_month() ) {
			$output .= $this->get_breadcrumb( get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) );
			$output .= $this->config['delimiter'];
			$output .= $this->get_breadcrumb( get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ), __( get_the_time( 'F' ), 'us' ) );

			// Year
		} elseif ( is_year() ) {
			$output .= $this->get_breadcrumb( get_year_link( get_the_time( 'Y' ) ), get_the_time( 'Y' ) );

			// The Events Calendar
		} elseif ( ( is_post_type_archive( 'tribe_events' ) AND $is_events_calendar ) ) {

			// Set post type manually because the plugin returns wrong post type
			$post_type_obj = get_post_type_object( 'tribe_events' );
			if ( is_tax() ) {
				$output .= $this->get_breadcrumb( esc_url( tribe_get_events_link() ), $post_type_obj->labels->name );
				$category_name = get_queried_object()->name; // calendar category page
				$output .= $this->config['delimiter'];
				$output .= $this->get_breadcrumb( esc_url( get_term_link( get_queried_object() ), 'tribe_events_cat' ), $category_name );
			} elseif ( tribe_is_month() OR tribe_is_past() OR tribe_is_upcoming() OR tribe_is_day() ) {
				$output .= $this->get_breadcrumb( esc_url( tribe_get_events_link() ), $post_type_obj->labels->name );
			}

			// Single post type
		} elseif ( is_single() ) {

			// Portfolio Pages
			if ( get_post_type() == 'us_portfolio' AND us_get_option( 'portfolio_breadcrumbs_page' ) != '' ) {
				$portfolio_breadcrumbs_page = get_post( us_get_option( 'portfolio_breadcrumbs_page' ) );
				if ( $portfolio_breadcrumbs_page ) {
					$portfolio_breadcrumbs_page_ID = $portfolio_breadcrumbs_page->ID;

					if ( has_filter( 'us_tr_object_id' ) ) {
						$portfolio_breadcrumbs_page_ID = apply_filters(
							'us_tr_object_id',
							$portfolio_breadcrumbs_page->ID,
							$portfolio_breadcrumbs_page->post_type,
							/* return_original_if_missing */ TRUE
						);
					}

					$portfolio_breadcrumbs_page_title = get_the_title( $portfolio_breadcrumbs_page_ID );
					$output .= $this->get_breadcrumb( get_permalink( $portfolio_breadcrumbs_page_ID ), $portfolio_breadcrumbs_page_title );
					$output .= $this->config['delimiter'];
				}

				// Posts
			} elseif ( get_post_type() == 'post' ) {
				$cat = get_the_category();
				if ( $cat = us_arr_path( $cat, '0' ) ) {
					$cats = get_category_parents( $cat, TRUE, $this->config['delimiter'] );
					$cats = preg_replace( "#^(.+)" . $this->config['delimiter'] . "$#", "$1", $cats );
					$cats = preg_replace( '#(<a\shref[^>]+>)([^<\/]+)(<\/a>)#', '${1}' . $this->config['name_before'] . '${2}' . $this->config['name_after'] . '${3}', $cats );
					$cats = str_replace( '<a', $this->config['before'] . '<a' . $this->config['link_attr'], $cats );
					$cats = str_replace( '</a>', '</a>' . $this->config['after'], $cats );
					$cats = $this->get_breadcrumb_with_markup( $cats );
					$output .= $cats;
					$output .= $this->config['delimiter'];
				}

				// The Events Calendar
			} elseif ( get_post_type() == 'tribe_events' ) {
				$post_type_obj = get_post_type_object( get_post_type() );
				$output .= $this->get_breadcrumb( esc_url( tribe_get_events_link() ), $post_type_obj->labels->name );
				$output .= $this->config['delimiter'];

				// CPT
			} else {
				$post_type_name = get_post_type();
				$taxonomies_found = FALSE;
				$taxonomy_names = get_object_taxonomies( $post_type_name, 'objects' );

				if ( $taxonomy_names != NULL ) {
					foreach ( $taxonomy_names as $taxonomy_name ) {
						if ( ! $taxonomy_name->public ) {
							continue;
						}
						$post_taxonomy = get_the_terms( get_the_ID(), $taxonomy_name->name );
						if ( is_array( $post_taxonomy ) AND count( $post_taxonomy ) > 0 ) {
							$post_taxonomy = $post_taxonomy[0];
							$get_term_parents_args = array(
								'separator' => $this->config['delimiter'],
								'link' => TRUE,
								'format' => 'name',
							);
							$post_taxonomies = get_term_parents_list(
								$post_taxonomy, $taxonomy_name->name, $get_term_parents_args
							);
							$post_taxonomies = preg_replace( "#^(.+)" . $this->config['delimiter'] . "$#", "$1", $post_taxonomies );
							$post_taxonomies = preg_replace( '#(<a\shref[^>]+>)([^<\/]+)(<\/a>)#', '${1}' . $this->config['name_before'] . '${2}' . $this->config['name_after'] . '${3}', $post_taxonomies );
							$post_taxonomies = str_replace( '<a', $this->config['before'] . '<a' . $this->config['link_attr'], $post_taxonomies );
							$post_taxonomies = str_replace( '</a>', '</a>' . $this->config['after'], $post_taxonomies );
							$post_taxonomies = $this->get_breadcrumb_with_markup( $post_taxonomies );
							$output .= $post_taxonomies;
							$output .= $this->config['delimiter'];
							$taxonomies_found = TRUE;
							break;
						}
					}
				}

				if ( ! $taxonomies_found ) {
					$cpt_post_type = get_post_type_object( get_post_type() );
					if ( $cpt_post_type->has_archive AND ! empty( $cpt_post_type->labels->name ) ) {
						$cpt_slug = is_string( $cpt_post_type->has_archive )
							? $cpt_post_type->has_archive
							: $cpt_post_type->name;
						$output .= $this->get_breadcrumb( get_option( 'siteurl' ) . '/' . $cpt_slug . '/', $cpt_post_type->labels->name );
						$output .= $this->config['delimiter'];
					}
				}
			}

			$output .= $this->get_breadcrumb( get_permalink( $post->ID ), get_the_title( $post->ID ) );

			// WooCommerce Shop page
		} elseif ( function_exists( 'is_shop' ) and is_shop() ) {
			if ( ! $post->post_parent ) {
				$output .= $this->config['before'] . get_the_title() . $this->config['after'];
			} elseif ( $post->post_parent ) {
				$parent_id = $post->post_parent;
				$parent_ids = array();
				$breadcrumbs = array();
				while ( $parent_id ) {
					$page = get_post( $parent_id );
					$parent_ids[] = $page->ID;
					$parent_id = $page->post_parent;
				}
				foreach ( array_reverse( $parent_ids ) as $id ) {
					$breadcrumbs[] = $this->get_breadcrumb( get_permalink( $id ), get_the_title( $id ) );
				}
				for ( $i = 0; $i < count( $breadcrumbs ); $i ++ ) {
					$output .= $breadcrumbs[ $i ];
					if ( $i != count( $breadcrumbs ) - 1 ) {
						$output .= $this->config['delimiter'];
					}
				}
				$output .= $this->config['delimiter'];
				$output .= $this->config['before'] . get_the_title() . $this->config['after'];
			}

			// Page without parent
		} elseif ( is_page() AND ! $post->post_parent ) {
			$output .= $this->get_breadcrumb( get_permalink( $post->ID ), get_the_title( $post->ID ) );

			// Page with parent
		} elseif ( is_page() AND $post->post_parent ) {
			$parent_id = $post->post_parent;
			$breadcrumbs = array();
			$parent_ids = array();
			$front_page_id = get_option( 'page_on_front' );
			$isFrontParent = FALSE;
			while ( $parent_id ) {
				$page = get_post( $parent_id );
				if ( $front_page_id == $parent_id ) {
					// Remove double home page when home is a parent
					$parent_id = $page->post_parent;
					if ( ! $parent_id ) {
						$isFrontParent = TRUE;
					}
					continue;
				}
				$parent_ids[] = $page->ID;
				$parent_id = $page->post_parent;
			}
			foreach ( array_reverse( $parent_ids ) as $id ) {
				$breadcrumbs[] = $this->get_breadcrumb( get_permalink( $id ), get_the_title( $id ) );
			}
			for ( $i = 0; $i < count( $breadcrumbs ); $i ++ ) {
				$output .= $breadcrumbs[ $i ];
				if ( $i != count( $breadcrumbs ) - 1 ) {
					$output .= $this->config['delimiter'];
				}
			}
			if ( $isFrontParent AND ! count( $breadcrumbs ) ) {
				$this->config['delimiter'] = '';
			}
			$output .= $this->config['delimiter'];
			$output .= $this->get_breadcrumb( get_permalink( $post->ID ), get_the_title( $post->ID ) );

			// Any other page
		} else {
			$post_type_obj = get_post_type_object( get_post_type() );
			if ( isset( $post_type_obj->labels->name ) ) {
				// Don't add any markup to unknown posts
				$output .= $this->config['before_basic'] . $post_type_obj->labels->name . $this->config['after_basic'];
			}
		}

		// Add pagination numbers
		if ( get_query_var( 'paged' ) AND ! ( get_post_type() == 'topic' OR get_post_type() == 'forum' ) ) {
			if ( is_category() OR is_day() OR is_month() OR is_year() OR is_search() OR is_tag() OR is_author() ) {
				$output .= ' (';
			} else {
				$output .= $this->config['delimiter'];
			}
			$output .= us_translate_x( 'Page', 'post type singular name' ) . ' ' . get_query_var( 'paged' );
			if ( is_category() OR is_day() OR is_month() OR is_year() OR is_search() OR is_tag() OR is_author() ) {
				$output .= ')';
			}
		}

		return $output;
	}
}
