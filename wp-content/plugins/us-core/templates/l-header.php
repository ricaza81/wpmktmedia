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

if ( isset( $us_header_settings['is_hidden'] ) AND $us_header_settings['is_hidden'] ) {
	return;
}

$options = us_arr_path( $us_header_settings, 'default.options', array() );
$layout = us_arr_path( $us_header_settings, 'default.layout', array() );

// Output the header
echo '<header id="page-header" class="l-header ' . $us_layout->header_classes();
if ( ! empty( $options['bg_img'] ) ) {
	echo ' with_bgimg';
}
if ( ! empty( $us_header_settings['header_id'] ) ) {
	echo ' id_' . $us_header_settings['header_id'];
}
echo '"';
if ( us_get_option( 'schema_markup' ) ) {
	echo ' itemscope itemtype="https://schema.org/WPHeader"';
}
echo '>';

// Output header areas and cells
foreach ( array( 'top', 'middle', 'bottom' ) as $valign ) {
	$show_state = FALSE;
	foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
		if ( ! isset( $us_header_settings[ $state ]['options'][ $valign . '_show' ] ) OR $us_header_settings[ $state ]['options'][ $valign . '_show' ] == 1 ) {
			$show_state = TRUE;
			break;
		}
	}
	foreach ( array( 'left', 'center', 'right' ) as $halign ) {
		if ( isset( $us_header_settings['default']['layout'][ $valign . '_' . $halign ] ) AND count( $us_header_settings['default']['layout'][ $valign . '_' . $halign ] ) > 0 ) {
			$show_state = TRUE;
			break;
		}
	}
	if ( ! $show_state ) {
		continue;
	}

	echo '<div class="l-subheader at_' . $valign;

	// Add width_full class, if option was enabled
	if ( isset( $options[ $valign . '_fullwidth' ] ) AND $options[ $valign . '_fullwidth' ] ) {
		echo ' width_full';
	}

	// Add centering classes, if option was enabled on relevant state
	if ( isset( $options[ $valign . '_centering' ] ) AND $options[ $valign . '_centering' ] ) {
		echo ' with_centering';
	}
	if ( ! empty( $us_header_settings['tablets']['options'][ $valign . '_centering' ] ) ) {
		echo ' with_centering_tablets';
	}
	if ( ! empty( $us_header_settings['mobiles']['options'][ $valign . '_centering' ] ) ) {
		echo ' with_centering_mobiles';
	}

	echo '"><div class="l-subheader-h">';
	$default_state = us_amp() ? 'mobiles' : 'default';
	foreach ( array( 'left', 'center', 'right' ) as $halign ) {
		echo '<div class="l-subheader-cell at_' . $halign . '">';
		if ( isset( $layout[ $valign . '_' . $halign ] ) ) {
			us_output_builder_elms( $us_header_settings, $default_state, $valign . '_' . $halign );
		}
		echo '</div>';
	}
	echo '</div></div>';
}

// Output elements that are hidden in Default state but are visible in Tablets and Mobiles states
$default_elms = us_get_builder_shown_elements_list( us_get_header_layout() );
$tablets_elms = us_get_builder_shown_elements_list( us_get_header_layout( 'tablets' ) );
$mobiles_elms = us_get_builder_shown_elements_list( us_get_header_layout( 'mobiles' ) );

$us_header_settings['default']['layout']['temporarily_hidden'] = array_diff( array_unique( array_merge( $tablets_elms, $mobiles_elms ) ), $default_elms );

echo '<div class="l-subheader for_hidden hidden">';
us_output_builder_elms( $us_header_settings, 'default', 'temporarily_hidden' );
echo '</div>';

unset( $us_header_settings['default']['layout']['temporarily_hidden'] );

echo '</header>';
