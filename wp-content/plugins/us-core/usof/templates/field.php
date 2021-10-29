<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output single USOF Field
 *
 * Multiple selector
 *
 * @var $name   string Field name
 * @var $id     string Field ID
 * @var $field  array Field options
 * @var $values array Set of values for the current and relevant fields
 */

if ( isset( $field['place_if'] ) AND ! $field['place_if'] ) {
	return;
}
if ( ! isset( $field['type'] ) ) {
	if ( WP_DEBUG ) {
		wp_die( $name . ' has no defined type' );
	}

	return;
}
$show_field = ( ! isset( $field['show_if'] ) OR usof_execute_show_if( $field['show_if'], $values ) );

// Change WPBakery "editor" field type to simple textarea, because TinyMCE is not yet supported in USOF fields
if ( $field['type'] == 'editor' ) {
	$field['type'] = 'textarea';
}

// Options Wrapper
if ( $field['type'] == 'wrapper_start' ) {
	$row_classes = '';
	if ( ! empty( $field['classes'] ) ) {
		$row_classes .= ' ' . $field['classes'];
	}
	echo '<div class="usof-form-wrapper ' . $row_classes . '" data-name="' . $name . '" ';
	echo 'style="display: ' . ( $show_field ? 'block' : 'none' ) . '">';
	if ( ! empty( $field['title'] ) ) {
		echo '<div class="usof-form-wrapper-title">' . $field['title'] . '</div>';
	}
	echo '<div class="usof-form-wrapper-content">';
	if ( ! empty( $field['show_if'] ) AND is_array( $field['show_if'] ) ) {
		// Showing conditions
		echo '<div class="usof-form-wrapper-showif hidden"' . us_pass_data_to_js( $field['show_if'] ) . '></div>';
	}

	return;
} elseif ( $field['type'] == 'wrapper_end' ) {
	echo '</div></div>';

	return;
}

$field['std'] = isset( $field['std'] ) ? $field['std'] : NULL;
$value = isset( $values[ $name ] ) ? $values[ $name ] : $field['std'];

// Options Group
if ( $field['type'] == 'group' ) {
	$group_classes = ! empty( $field['classes'] ) ? ' ' . $field['classes'] : '';

	if ( ! empty( $field['is_accordion'] ) ) {
		$group_classes .= ' type_accordion';
	} else {
		$group_classes .= ' type_simple';
	}

	if ( ! empty( $field['is_sortable'] ) ) {
		$group_classes .= ' sortable';
	}

	if ( ! empty( $field['preview'] ) ) {
		$group_classes .= ' preview_' . $field['preview'];
	}

	echo '<div class="usof-form-group' . $group_classes . '" data-name="' . $name . '"';
	if ( ! empty( $field['title'] ) ) {
		echo ' data-params_title="' . rawurlencode( $field['title'] ) . '"';
	}
	echo 'style="display: ' . ( $show_field ? 'block' : 'none' ) . '">';
	echo '<div class="usof-form-group-prototype hidden">';
	us_load_template(
		'usof/templates/fields/group_param', array(
			'params_values' => array(),
			'field' => $field,
		)
	);
	echo '</div>';

	if ( is_array( $value ) AND count( $value ) > 0 ) {
		foreach ( $value as $index => $params_values ) {
			us_load_template(
				'usof/templates/fields/group_param', array(
					'params_values' => $params_values,
					'field' => $field,
				)
			);
		}
	}

	// Output "Add" button, if "show_controls" is set
	if ( ! empty( $field['show_controls'] ) ) {
		echo '<span class="usof-form-group-add">';
		echo '<span class="usof-form-group-add-title">' . us_translate( 'Add' ) . '</span>';
		echo '<span class="usof-preloader"></span>';
		echo '</span>';
		$translations = array(
			'deleteConfirm' => __( 'Are you sure want to delete the element?', 'us' ),
			'style' => us_translate( 'Style' ),
		);
		echo '<span class="usof-form-group-translations hidden"' . us_pass_data_to_js( $translations ) . '></span>';
	}

	// Show_if conditions
	if ( ! empty( $field['show_if'] ) AND is_array( $field['show_if'] ) ) {
		echo '<div class="usof-form-row-showif hidden"' . us_pass_data_to_js( $field['show_if'] ) . '></div>';
	}
	echo '</div>';

	return;
}

$row_classes = ' type_' . $field['type'];
if ( ! in_array(
		$field['type'], array(
			'message',
			'heading',
		)
	) AND ( ! isset( $field['classes'] ) OR strpos( $field['classes'], 'desc_' ) === FALSE ) ) {
	$row_classes .= ' desc_1';
}
if ( isset( $field['cols'] ) ) {
	$row_classes .= ' cols_' . $field['cols'];
}
if ( ! empty( $field['classes'] ) ) {
	$row_classes .= ' ' . $field['classes'];
}
if ( ! empty( $field['disabled'] ) ) {
	$row_classes .= ' disabled';
}

// Output option row
echo '<div class="usof-form-row' . $row_classes . '" data-name="' . $name . '" ';

// HTML data output for js
if ( isset( $field['html-data'] ) ) {
	echo us_pass_data_to_js( $field['html-data'] );
}
echo 'style="display: ' . ( $show_field ? 'block' : 'none' ) . '">';
if ( ! empty( $field['title'] ) ) {
	echo '<div class="usof-form-row-title"><span>' . $field['title'] . '</span>';
	if ( ! empty( $field['description'] ) AND ( ! empty( $field['classes'] ) AND strpos( $field['classes'], 'desc_4' ) !== FALSE ) ) {
		echo '<div class="usof-form-row-desc">';
		echo '<div class="usof-form-row-desc-icon"></div>';
		echo '<div class="usof-form-row-desc-text">' . $field['description'] . '</div>';
		echo '</div>';
	}
	echo '</div>';
}
echo '<div class="usof-form-row-field"><div class="usof-form-row-control">';
// Including the field control itself
us_load_template(
	'usof/templates/fields/' . $field['type'], array(
		'name' => $name,
		'id' => $id,
		'field' => $field,
		'value' => $value,
		'is_metabox' => ( isset( $is_metabox ) ) ? $is_metabox : FALSE,
	)
);
echo '</div>';
if ( ! empty( $field['description'] ) AND ( empty( $field['classes'] ) OR strpos( $field['classes'], 'desc_4' ) === FALSE ) ) {
	echo '<div class="usof-form-row-desc">';
	echo '<div class="usof-form-row-desc-icon"></div>';
	echo '<div class="usof-form-row-desc-text">' . $field['description'] . '</div>';
	echo '</div>';
}

if ( isset( $field['hints_for'] ) ) {
	// Check if post type exist
	$post_type_obj = get_post_type_object( $field['hints_for'] );

	if ( $post_type_obj ) {

		$hint_text = '';

		// Get post labels for hints
		$hints = array(
			'edit_url' => '<a href="' . admin_url( 'post.php?post={{post_id}}&action=edit' ) . '" target="_blank" rel="noopener">{{hint}}</a>',
			// for JS
			'add' => $post_type_obj->labels->add_new,
			'edit' => __( 'Edit selected', 'us' ),
		);

		// Count published posts
		$count_posts = wp_count_posts( $field['hints_for'] );
		$published_posts = $count_posts->publish;
		if ( $published_posts ) {

			$edit_link = get_edit_post_link( $value );

			// Output "Edit" link if post exists and assigned
			if ( $edit_link AND $value ) {
				$hint_text = '<a href="' . $edit_link . '" target="_blank" rel="noopener">' . $hints['edit'] . '</a>';
			}

			// Output "Add New" link if there are no published posts
		} else {
			$hint_text = '<a href="' . admin_url( 'post-new.php?post_type=' . $field['hints_for'] ) . '" target="_blank" rel="noopener">' . $hints['add'] . '</a>';
			$hints['no_posts'] = TRUE;
		}

		unset( $hints['add'] );

		echo '<div class="usof-form-row-hint-text">' . $hint_text . '</div>';
		echo '<div class="usof-form-row-hint-json hidden"' . us_pass_data_to_js( $hints ) . '></div>';
	}
}

echo '</div>'; // .usof-form-row-field
if ( ! empty( $field['show_if'] ) AND is_array( $field['show_if'] ) ) {
	// Showing conditions
	echo '<div class="usof-form-row-showif"' . us_pass_data_to_js( $field['show_if'] ) . '></div>';
}
echo '</div>';
