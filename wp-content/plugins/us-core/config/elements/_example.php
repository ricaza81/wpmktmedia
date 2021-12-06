<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// Define variables to use them in the config below, if needed
$misc = us_config( 'elements_misc' );

// Structure template for all usage cases
return array(

	// Shows element's name in the editors UI
	'title' => 'Element name',

	// Defines tab in the "Add Element" list
	'category' => 'Post Elements',

	// Defines icon in the "Add Element" list
	'icon' => 'fas fa-text',

	// Hide element in the "Add Element" list
	'hide_on_adding_list' => TRUE,

	// Enables element for certain post types only
	'shortcode_post_type' => array( 'us_content_template', 'us_page_block' ),

	// Enables element only via condition
	'place_if' => class_exists( 'woocommerce' ),

	// Allows adding other elements inside this element. Used in BOTH builders
	'is_container' => TRUE,

	// Sets position in "Add element" lists. Used in BOTH builders
	'weight' => 400,

	// Preload the fieldset for Live Builder, if not set, settings will be loaded via AJAX
	'usb_preload' => TRUE,

	// Sets dependence on containers elements
	'as_child' => array(
		'only' => 'vc_column',
	),
	'as_parent' => array(
		'only' => 'vc_column_inner'
	),

	// Not used params, required for correct fallback while editing element
	'fallback_params' => array(
		'columns_type',
		'gap',
	),

	// WPBakery params which are not supported by the theme in "vc_" shortcodes
	'vc_remove_params' => array(
		'css_animation',
		'rtl_reverse',
	),

	// ONLY WPBakery: doesn't open editing window after adding element
	'show_settings_on_create' => FALSE,

	// ONLY WPBakery: Load JS file in the WPB element editing window
	'admin_enqueue_js' => '/plugins-support/js_composer/js/us_icon_view.js',

	// ONLY WPBakery: Defines JS class to apply custom appearance in the WPB editor UI
	'js_view' => 'ViewUsIcon',

	// Sets element's settings and default values
	'params' => array(

		// Common params, which can be used in all options types
		'option_name' => array(

			// Shows name of option, can be absent
			'title' => 'Option name',

			// Shows the title at side (at left on LTR, at right on RTL) of the control field.
			'title_pos' => 'side',

			// Sets type of option control. See all available types below
			'type' => 'text',

			// Shows description of option. Its appearance depends on "desc_" class
			'description' => 'Option description',

			// Sets default value
			'std' => '',

			// Adds css classes to customize appearance of option in the editing window
			'classes' => '',

			// Sets appearance of option via 2, 3, 4 columns in the editing window
			'cols' => 2,

			// Sets display conditions depending on other option's values
			'show_if' => array( 'some_option', '=', 'some_value' ),

			// Outputs the option depending on "if" condition, e.g. "plugin is active"
			'place_if' => class_exists( 'woocommerce' ),

			// Combines several options into separate tab in the editing window
			'group' => 'Tab Name',

			// Sets where the option can be used
			'context' => array( 'header', 'grid', 'shortcode', 'widget' ),

			// ONLY WPBakery: Shows option's name and value in the editors UI
			'admin_label' => TRUE,

			// ONLY WPBakery: Shows option's value inside a <div> in the editors UI
			'holder' => 'div',

			// Sets how the preview is rendering in US Builder. See all available values below
			'usb_preview' => TRUE,
		),

		/************ US BUILDER PREVIEW ************/

		// Renders the whole element
		'usb_preview' => TRUE,

		// Changes CSS class of the main container (between available values only)
		'usb_preview' => array(
			'mod' => 'align',
		),

		// Toggles CSS class of the main container
		'usb_preview' => array(
			'toggle_class' => 'no_view_cart_link',
		),

		// Toggles CSS class of the main container (inverse)
		'usb_preview' => array(
			'toggle_class_inverse' => 'no_view_cart_link',
		),

		// Changes inline CSS attribute of the main container
		'usb_preview' => array(
			'css' => 'width',
		),

		// Adds CSS class to the main container
		'usb_preview' => array(
			'attr' => 'class',
		),

		// Changes html in the main container
		'usb_preview' => array(
			'attr' => 'html',
		),

		// If 'elm' is set, applies changes to that container
		'usb_preview' => array(
			'css' => 'width',
			'elm' => '.w-counter-title',
		),
		'usb_preview' => array(
			'attr' => 'html',
			'elm' => '.w-counter-title',
		),

		// Multiple values
		'usb_preview' => array(
			array(
				'elm' => '.b-socials-link',
				'css' => 'height',
			),
			array(
				'elm' => '.b-socials-link',
				'css' => 'line-height',
			),
		),

		// JS code that will be executed when initializing an element in the builder
		'usb_init_js' => 'console.log( \'init element\' )',

		// Inclusion of settings for an element in the main output, otherwise all settings
		// will be loaded after AJAX ( AJAX loading is enabled by default )
		'usb_preload' => TRUE,

		// Metaboxes that are displayed in the context of the builder
		'usb_context' => TRUE,

		// The parameter is intended for TTA sections, which means updating the entire
		// element to which the section belongs
		'usb_update_parent' => TRUE,

		// By default, all movement occurs along axis 1, but for some elements it is
		// necessary to move along axis 2, this parameter enables this option.
		// For example, this is necessary for horizontal tabs.
		'usb_moving_child_x_direction' => TRUE,

		// IMPORTANT! This is required for all containers that have multiple children up to the target.
		// This is a selector to override the root container on an element, parameter is only used
		// in containers to traverse wrappers or extra markup.
		// Multiple containers can be written here, separated by commas `.container, .container> *`,
		// but only the first one found will be retrieved.
		'usb_root_container_selector' => '.w-tabs-sections:first', // with respect to `.w-tabs`

		/************ OPTIONS TYPES ************/

		// TEXT: single line text field with free user input, based on <input type="text">
		'option_name' => array(
			'type' => 'text',
			'placeholder' => '', // shows text inside a field
			'std' => '', // string
		),

		// TEXTAREA: multiple lines text field with free user input, based on <textarea>
		'option_name' => array(
			'type' => 'textarea',
			'placeholder' => '', // shows text inside a field
			'std' => '', // string
		),

		// SELECT: single selection between several values, based on <select>
		'option_name' => array(
			'type' => 'select',
			'options' => array( // shows possible values for selection
				'key1' => 'Value Name',
				'key2' => 'Value Name',
				'label' => array( // sets <optgroup> for several values
					'key3' => 'Value Name',
					'key4' => 'Value Name',
				),
			),
			'std' => 'key1', // string
		),

		// RADIO: single selection between several values, based on <input type="radio">
		'option_name' => array(
			'type' => 'radio',
			'options' => array( // shows possible values for selection
				'key1' => 'Value Name',
				'key2' => 'Value Name',
				'key3' => 'Value Name',
			),
			'std' => 'key1', // string
			'labels_as_icons' => 'fas fa-align-*', // output icons instead of labels, uses FA icon name where * is changed to the option key
		),

		// CHECKBOXES: multiple selection between several values, based on several <input type="checkbox">
		'option_name' => array(
			'type' => 'checkboxes',
			'options' => array( // shows possible values for selection
				'key1' => 'Value Name',
				'key2' => 'Value Name',
				'key3' => 'Value Name',
			),
			'std' => 'key1,key3', // string
		),

		// SWITCH: ON/OFF switch, based on a single <input type="checkbox">
		'option_name' => array(
			'type' => 'switch',
			'switch_text' => '', // shows text after switch, text is also clickable
			'std' => FALSE, // bool
		),

		// ICON: icon selection with preview, based on combined controls
		'option_name' => array(
			'type' => 'icon',
			'std' => 'fas|star', // string: "set|name"
		),

		// LINK: text field with checkboxes, based on combined controls
		'option_name' => array(
			'type' => 'link',
			'std' => array(), // array
			'shortcode_std' => '', // empty string for shortcode param
		),

		// COLOR: color picker, based on custom controls
		'option_name' => array(
			'type' => 'color',
			'std' => '#fff', // string: HEX, RGBA or "_content_text" value
			'clear_pos' => 'left', // enables "clear" button at the "left" or "right". If not set, clearing is disabled
			'with_gradient' => TRUE, // enables Gradients, TRUE by default
			'disable_dynamic_vars' => TRUE // disables list of variables from Theme Options > Colors
		),

		// UPLOAD: shows button with selection files from WordPress Media Library
		'option_name' => array(
			'type' => 'upload',
			'is_multiple' => TRUE, // enables slection of several files, default is FALSE
			'button_label' => 'Set image', // sets text on the button
			'extension' => 'png,jpg,jpeg,gif,svg', // sets available file types
		),

		// HEADING: used as visual separator between options
		'option_name' => array(
			'type' => 'heading',
		),

		// EDITOR: WordPress Classic Editor, used in shortcodes only
		'option_name' => array(
			'type' => 'editor',
			'std' => '', // string
		),

		// HTML: used for html code input, has a code highlight via WordPress CodeMirror
		'option_name' => array(
			'type' => 'html',
			'encoded' => TRUE, // encodes the value to the base64
			'std' => '', // string
		),

		// GROUP: Group of several items. Every item may have all other option types. Group allows to add/delete/reorder items
		'option_name_group' => array(
			'type' => 'group',
			'show_controls' => TRUE, // REQUIRED, enables adding items, shows "Add" and "Delete" buttons
			'is_duplicate' => FALSE, // enables duplicating items, shows "Clone" button
			'is_sortable' => TRUE, // enables drag & drop items, shows "Move" button
			'is_accordion' => FALSE, // enables heading sections for items, which work as toggles
			'accordion_title' => 'item_name_1', // enables dynamic title using one or several param's value, when 'is_accordion' => TRUE
			'params' => array( // items with their settings and default values
				'item_name_1' => array(
					'type' => 'upload',
					'std' => '',
				),
				'item_name_2' => array(
					'type' => 'text',
					'std' => '',
				),
			),
			'std' => array(), // array
		),

		// AUTOCOMPLETE: select value(s) with filtering and ajax loading
		'option_name' => array(
			'type' => 'us_autocomplete',
			'options_prepared_for_wpb' => TRUE, // needed for work in WPBakery Page Builder
			'options' => array(
				'Option 1' => 'option1',
				'Option 2' => 'option2',
				'Group Name' => array(
					'Group option 1' => 'group_option1',
					'Group option 2' => 'group_option1',
				),
			),
			'settings' => array(
				'action' => 'action_name',
				'nonce_name' => 'some text',
				'slug' => 'items_slug',
			),
			'is_multiple' => TRUE,
			'is_sortable' => TRUE,
			'params_separator' => ',', // Default: ','
		),

		// CSS The group of parameters that will be converted to inline css
		'option_name' => array(
			'type' => 'design_options',
			'params' => array(
				'font-size' => array(
					'type' => 'radio',
					'std' => '',
				),
				'height' => array(
					'type' => 'text',
					'std' => '',
				),
			),
			'std' => '',
		),
	),
);
