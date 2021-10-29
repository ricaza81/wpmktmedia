<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Template of website header HTML markup
 */

$us_layout = US_Layout::instance();
if ( $us_layout->header_show == 'never' ) {
	return;
}

global $us_header_settings;
us_load_header_settings_once();

if ( ! empty( $us_header_settings['is_hidden'] ) ) {
	return;
}

$options = us_arr_path( $us_header_settings, 'default.options', array() );
$layout = us_arr_path( $us_header_settings, 'default.layout', array() );

$header_atts = array(
	'id' => 'page-header',
	'class' => 'l-header ' . $us_layout->header_classes(),
);
if ( ! empty( $options['bg_img'] ) ) {
	$header_atts['class'] .= ' with_bgimg';
}
if ( ! empty( $us_header_settings['header_id'] ) ) {
	$header_atts['class'] .= ' id_' . $us_header_settings['header_id'];
}
if ( us_get_option( 'schema_markup' ) ) {
	$header_atts['itemscope'] = '';
	$header_atts['itemtype'] = 'https://schema.org/WPHeader';
}

// Output the header
echo '<header' . us_implode_atts( $header_atts ) . '>';

// Output header areas and cells
foreach ( array( 'top', 'middle', 'bottom' ) as $area ) {
	$show_state = FALSE;
	foreach ( (array) us_get_responsive_states( /* Only keys */TRUE ) as $state ) {
		if (
			! isset( $us_header_settings[ $state ]['options'][ $area . '_show' ] )
			OR $us_header_settings[ $state ]['options'][ $area . '_show' ]
		) {
			$show_state = TRUE;
			break;
		}
	}
	foreach ( array( 'left', 'center', 'right' ) as $cell ) {
		if (
			isset( $us_header_settings['default']['layout'][ $area . '_' . $cell ] )
			AND count( $us_header_settings['default']['layout'][ $area . '_' . $cell ] ) > 0
		) {
			$show_state = TRUE;
			break;
		}
	}
	if ( ! $show_state ) {
		continue;
	}

	$subheader_atts = array(
		'class' => 'l-subheader at_' . $area,
	);

	// Add width_full class, if option was enabled
	if ( ! empty( $options[ $area . '_fullwidth' ] ) ) {
		$subheader_atts['class'] .= ' width_full';
	}

	echo '<div' . us_implode_atts( $subheader_atts ) . '>';
	echo '<div class="l-subheader-h">';

	// For AMP output mobile state first
	$default_state = us_amp() ? 'mobiles' : 'default';

	foreach ( array( 'left', 'center', 'right' ) as $cell ) {
		echo '<div class="l-subheader-cell at_' . $cell . '">';
		if ( isset( $layout[ $area . '_' . $cell ] ) ) {
			us_output_builder_elms( $us_header_settings, $default_state, $area . '_' . $cell );
		}
		echo '</div>';
	}

	echo '</div>';
	echo '</div>';
}

// Output elements that are hidden in Default state but are visible in Laptops, Tablets and Mobiles states
$default_elms = us_get_builder_shown_elements_list( us_get_header_layout() );
$laptops_elms = us_get_builder_shown_elements_list( us_get_header_layout( 'laptops' ) );
$tablets_elms = us_get_builder_shown_elements_list( us_get_header_layout( 'tablets' ) );
$mobiles_elms = us_get_builder_shown_elements_list( us_get_header_layout( 'mobiles' ) );

$us_header_settings['default']['layout']['temporarily_hidden'] = array_diff(
	array_unique(
		array_merge(
			$laptops_elms,
			$tablets_elms,
			$mobiles_elms
		)
	),
	$default_elms
);

echo '<div class="l-subheader for_hidden hidden">';
us_output_builder_elms( $us_header_settings, 'default', 'temporarily_hidden' );
echo '</div>';

unset( $us_header_settings['default']['layout']['temporarily_hidden'] );

echo '</header>';
