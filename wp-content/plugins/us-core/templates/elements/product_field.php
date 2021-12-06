<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WooCommerce Product data
 */

global $product, $us_grid_object_type;

// Never output this element inside Grids with terms
if ( $us_elm_context === 'grid' AND $us_grid_object_type === 'term' ) {
	return;
}

// Check if this element used as shortcode via Live Builder in Content Template / Page Block
$is_shortcode_template_preview = usb_is_preview_page_for_template() AND $us_elm_context == 'shortcode';

if (
	( ! class_exists( 'woocommerce' ) OR ! $product )
	AND ! $is_shortcode_template_preview
) {

	// Output placeholder for Live Builder
	if ( usb_is_preview_page() ) {
		echo '<div class="w-post-elm"></div>';
	}

	return;
}

$_atts['class'] = 'w-post-elm product_field ' . $type;
$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

// Get the product data value
$value = '';
$before_attr_value = '<span class="woocommerce-product-attributes-item__value">';
$after_attr_value = '</span>';

// Price
if ( $type == 'price' ) {
	if ( $is_shortcode_template_preview ) {
		$value .= us_config( 'elements/product_field.usb_preview_dummy_data.price', '' );
	} else {
		$value .= $product->get_price_html();
	}

	// SKU
} elseif (
	$type == 'sku'
	AND (
		$is_shortcode_template_preview
		OR ( $product->get_sku() OR $product->is_type( 'variable' ) )
	)
) {
	$_atts['class'] .= ' product_meta';
	if ( $is_shortcode_template_preview ) {
		$sku = us_translate( 'N/A', 'woocommerce' );
	} else {
		$sku = $product->get_sku();
		$sku = ( $sku ) ? $sku : us_translate( 'N/A', 'woocommerce' );
	}
	$value .= '<span class="w-post-elm-before">' . us_translate( 'SKU', 'woocommerce' ) . ': </span>';
	$value .= '<span class="sku">' . $sku . '</span>';

	// Rating
} elseif (
	$type == 'rating'
	AND (
		$is_shortcode_template_preview
		OR get_option( 'woocommerce_enable_reviews', 'yes' ) === 'yes'
	)
) {
	$rating = ( $is_shortcode_template_preview ) ? '3.5' : $product->get_average_rating();

	$value .= wc_get_rating_html( $rating );

	// SALE badge
} elseif (
	$type == 'sale_badge'
	AND (
		$is_shortcode_template_preview
		OR $product->is_on_sale()
	)
) {
	$_atts['class'] .= ' onsale';
	$value .= strip_tags( $sale_text );

	// Weight
} elseif (
	$type == 'weight'
	AND (
		$is_shortcode_template_preview
		OR $product->has_weight()
	)
) {
	$_atts['class'] .= ' woocommerce-product-attributes-item--' . $type;
	if ( $is_shortcode_template_preview ) {
		$weight = us_config( 'elements/product_field.usb_preview_dummy_data.weight', '' );
	} else {
		$weight = wc_format_weight( $product->get_weight() );
	}
	$value .= '<span class="w-post-elm-before">' . us_translate( 'Weight', 'woocommerce' ) . ': </span>';
	$value .= $before_attr_value . strip_tags( $weight ) . $after_attr_value;

	// Dimensions
} elseif (
	$type == 'dimensions'
	AND (
		$is_shortcode_template_preview
		OR $product->has_dimensions()
	)
) {
	$_atts['class'] .= ' woocommerce-product-attributes-item--' . $type;
	if ( $is_shortcode_template_preview ) {
		$dimensions = us_config( 'elements/product_field.usb_preview_dummy_data.dimensions', '' );
	} else {
		$dimensions = wc_format_dimensions( $product->get_dimensions( FALSE ) );
	}
	$value .= '<span class="w-post-elm-before">' . us_translate( 'Dimensions', 'woocommerce' ) . ': </span>';
	$value .= $before_attr_value . esc_html( $dimensions ) . $after_attr_value;

	// Stock status information
} elseif ( $type == 'stock' ) {
	if ( $is_shortcode_template_preview ) {
		if ( $out_of_stock_only ) {
			$_atts['class'] .= ' out-of-stock';
			$value = us_translate( 'Out of stock', 'woocommerce' );
		} else {
			$value = sprintf( us_translate( '%s in stock', 'woocommerce' ), '123' );
		}

	} elseif ( ! $product->is_in_stock() ) {
		$_atts['class'] .= ' out-of-stock';
		$value = us_translate( 'Out of stock', 'woocommerce' );
	} elseif ( ! $out_of_stock_only ) {
		$availability = $product->get_availability();
		$value = $availability['availability'] ? $availability['availability'] : wc_format_stock_for_display( $product );
	}

	// Attributes
} elseif ( $type == 'attributes' ) {

	if ( $display_type ) {
		$_atts['class'] .= ' display_table';
	}

	if ( $is_shortcode_template_preview ) {
		$product_attributes = us_config( 'elements/product_field.usb_preview_dummy_data.attributes', '' );
	} else {
		// Use part of wc_display_product_attributes() function to improve output
		$attributes = array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' );
		$product_attributes = array();

		foreach ( $attributes as $attribute ) {
			$values = array();

			if ( $attribute->is_taxonomy() ) {
				$attribute_taxonomy = $attribute->get_taxonomy_object();
				$attribute_values = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );

				foreach ( $attribute_values as $attribute_value ) {
					$value_name = esc_html( $attribute_value->name );

					if ( $attribute_taxonomy->attribute_public ) {
						$values[] = '<a href="' . esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) ) . '" rel="tag">' . $value_name . '</a>';
					} else {
						$values[] = $value_name;
					}
				}
			} else {
				$values = $attribute->get_options();

				foreach ( $values as &$_value ) {
					$_value = make_clickable( esc_html( $_value ) );
				}
			}

			$product_attributes[ 'attribute_' . sanitize_title_with_dashes( $attribute->get_name() ) ] = array(
				'label' => wc_attribute_label( $attribute->get_name() ),
				'value' => apply_filters( 'woocommerce_attribute', wptexturize( implode( ', ', $values ) ), $attribute, $values ),
			);
		}

		$product_attributes = apply_filters( 'woocommerce_display_product_attributes', $product_attributes, $product );
	}

	// improve HTML to output attributes
	foreach ( $product_attributes as $product_attribute_key => $product_attribute ) {
		$value .= '<div class="woocommerce-product-attributes-item--' . esc_attr( $product_attribute_key ) . '">';
		$value .= '<span class="w-post-elm-before">';
		$value .= wp_kses_post( $product_attribute['label'] );
		if ( ! $display_type ) {
			$value .= ': ';
		}
		$value .= '</span>';
		$value .= $before_attr_value . wp_kses_post( $product_attribute['value'] ) . $after_attr_value;
		$value .= '</div>';
	}

	// WooCommerce Default Actions for plugins compatibility
} elseif ( $type == 'default_actions' ) {
	if ( $us_elm_context == 'shortcode' ) {

		if ( ! $is_shortcode_template_preview ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 3 );

			if ( usb_is_preview_page() ) {
				echo '<div' . us_implode_atts( $_atts ) . '>';
			}

			do_action( 'woocommerce_single_product_summary' );

			if ( usb_is_preview_page() ) {
				echo '</div>';
			}

			return;
		}

	} else {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

		do_action( 'woocommerce_after_shop_loop_item_title' );
		do_action( 'woocommerce_after_shop_loop_item' );

		return;
	}
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= $value;
$output .= '</div>';

if ( $value != '' OR usb_is_preview_page() ) {
	echo $output;
}
