<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: cform
 */

$receiver_email = get_option( 'admin_email' );
$email_subject = sprintf( __( 'Message from %s', 'us' ), get_bloginfo( 'name' ) );
$btn_styles = us_get_btn_styles();

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

// Default Form Fields
$default_fields = array(
	array(
		'type' => 'text',
		'label' => '',
		'placeholder' => us_translate( 'Name' ),
	),
	array(
		'type' => 'email',
		'label' => '',
		'placeholder' => us_translate( 'Email' ),
	),
	array(
		'type' => 'textarea',
		'label' => '',
		'placeholder' => us_translate( 'Text' ),
	),
);

/**
 * @return array
 */
return array(
	'title' => __( 'Contact Form', 'us' ),
	'icon' => 'fas fa-envelope',
	'params' => us_set_params_weight(

		// Fields
		array(
			'items' => array(
				'type' => 'group',
				'group' => __( 'Fields', 'us' ),
				'show_controls' => TRUE,
				'is_sortable' => TRUE,
				'is_accordion' => TRUE,
				'accordion_title' => 'type',
				'usb_preview' => TRUE,
				'params' => array(
					'type' => array(
						'title' => us_translate( 'Type' ),
						'type' => 'select',
						'options' => array(
							'text' => us_translate( 'Text' ) . ' ' . __( '(single line)', 'us' ),
							'textarea' => us_translate( 'Text' ) . ' ' . __( '(multiple lines)', 'us' ),
							'email' => us_translate( 'Email' ),
							'date' => us_translate( 'Date' ),
							'select' => __( 'Dropdown', 'us' ),
							'checkboxes' => __( 'Checkboxes', 'us' ),
							'radio' => __( 'Radio buttons', 'us' ),
							'info' => us_translate( 'Text Block', 'js_composer' ),
							'agreement' => __( 'Agreement checkbox', 'us' ),
							'captcha' => __( 'Captcha', 'us' ),
						),
						'std' => 'text',
						'admin_label' => TRUE,
					),
					'inputmode' => array(
						'title' => __( 'Input mode', 'us' ),
						'type' => 'select',
						'options' => array(
							'text' => 'text',
							'decimal' => 'decimal',
							'numeric' => 'numeric',
							'tel' => 'tel',
							'url' => 'url',
						),
						'std' => 'text',
						'show_if' => array( 'type', '=', 'text' ),
					),
					'date_format' => array(
						'title' => us_translate( 'Date Format' ),
						'type' => 'text',
						'std' => 'd MM yy',
						'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">yy-mm-dd</span>, <span class="usof-example">dd/mm/y</span>, <span class="usof-example">d MM, D</span>. <a href="https://api.jqueryui.com/datepicker/#utility-formatDate" target="_blank" rel="noopener">' . __( 'Learn more', 'us' ) . '</a>',
						'show_if' => array( 'type', '=', 'date' ),
					),
					'label' => array(
						'title' => us_translate( 'Title' ),
						'description' => __( 'Shown above the field', 'us' ),
						'type' => 'text',
						'std' => '',
						'cols' => 2,
						'show_if' => array( 'type', '!=', 'info' ),
						'admin_label' => TRUE,
					),
					'description' => array(
						'title' => us_translate( 'Description' ),
						'description' => __( 'Shown below the field', 'us' ),
						'type' => 'text',
						'std' => '',
						'cols' => 2,
						'show_if' => array( 'type', '!=', 'info' ),
					),
					'placeholder' => array(
						'title' => __( 'Placeholder', 'us' ),
						'description' => __( 'Shown inside the field', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea' ) ),
						'admin_label' => TRUE,
					),
					'values' => array(
						'title' => __( 'Values', 'us' ),
						'description' => __( 'Each value on a new line', 'us' ),
						'type' => 'textarea',
						'encoded' => TRUE,
						'std' => '',
						'show_if' => array( 'type', '=', array( 'select', 'checkboxes', 'radio' ) ),
					),
					'value' => array(
						'title' => us_translate( 'Text' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'type', '=', array( 'info', 'agreement' ) ),
					),
					'required' => array(
						'switch_text' => __( 'Required field', 'us' ),
						'type' => 'switch',
						'std' => FALSE,
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea', 'checkboxes' ), ),
					),
					'move_label' => array(
						'switch_text' => __( 'Move title on focus', 'us' ),
						'type' => 'switch',
						'std' => FALSE,
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea', 'captcha' ), ),
					),
					'is_used_as_from_email' => array(
						'switch_text' => __( 'Use the value of this field as sender\' address of emails', 'us' ),
						'type' => 'switch',
						'std' => FALSE,
						'show_if' => array( 'type', '=', array( 'email' ), ),
					),
					'is_used_as_from_name' => array(
						'switch_text' => __( 'Use the value of this field as sender\' name of emails', 'us' ),
						'type' => 'switch',
						'std' => FALSE,
						'show_if' => array( 'type', '=', array( 'text' ), ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'us' ),
						'type' => 'icon',
						'std' => '',
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea', 'select', 'captcha' ), ),
					),
					'cols' => array(
						'title' => us_translate( 'Width' ),
						'type' => 'radio',
						'options' => array(
							'1' => us_translate( 'Full' ),
							'2' => '1/2',
							'3' => '1/3',
							'4' => '1/4',
						),
						'std' => '1',
						'show_if' => array( 'type', '=', array( 'text', 'email', 'date', 'textarea', 'select', 'checkboxes', 'radio', 'captcha' ), ),
					),
				),
				'std' => $default_fields,
			),
		),

		// Button
		array(
			'button_text' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => us_translate( 'Submit' ),
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'attr' => 'text',
					'elm' => 'button[type=submit].w-btn .w-btn-label',
				),
			),
			'button_style' => array(
				'title' => us_translate( 'Style' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => $btn_styles,
				'std' => '1',
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'mod' => 'us-btn-style',
					'elm' => 'button[type=submit].w-btn',
				),
			),
			'button_size' => array(
				'title' => us_translate( 'Size' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'css' => 'font-size',
					'elm' => 'button[type=submit].w-btn',
				),
			),
			'button_size_mobiles' => array(
				'title' => __( 'Size on Mobiles', 'us' ),
				'description' => $misc['desc_font_size'],
				'type' => 'text',
				'std' => '',
				'cols' => 2,
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'css' => '--btn-size-mobiles',
					'elm' => '.w-form-row.for_submit',
				),
			),
			'button_fullwidth' => array(
				'type' => 'switch',
				'switch_text' => __( 'Stretch to the full width', 'us' ),
				'std' => FALSE,
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'toggle_class' => 'width_full',
					'elm' => '.w-form-row.for_submit',
				),
			),
			'button_align' => array(
				'title' => __( 'Button Alignment', 'us' ),
				'type' => 'radio',
				'labels_as_icons' => 'fas fa-align-*',
				'options' => array(
					'none' => us_translate( 'Default' ),
					'left' => us_translate( 'Left' ),
					'center' => us_translate( 'Center' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'none',
				'show_if' => array( 'button_fullwidth', '=', '' ),
				'group' => __( 'Button', 'us' ),
				'usb_preview' => array(
					'mod' => 'align',
					'elm' => '.w-form-row.for_submit',
				),
			),
			'icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'group' => __( 'Button', 'us' ),
				'usb_preview' => TRUE,
			),
			'iconpos' => array(
				'title' => __( 'Icon Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'left',
				'group' => __( 'Button', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		// More options
		array(
			'receiver_email' => array(
				'title' => __( 'Receiver Email', 'us' ),
				'description' => __( 'Requests will be sent to this email. You can insert multiple comma-separated emails as well.', 'us' ),
				'type' => 'text',
				'std' => $receiver_email,
				'admin_label' => TRUE,
				'group' => __( 'More Options', 'us' ),
			),
			'email_subject' => array(
				'title' => __( 'Subject of emails to be sent', 'us' ),
				'type' => 'text',
				'std' => $email_subject,
				'group' => __( 'More Options', 'us' ),
			),
			'success_message' => array(
				'title' => __( 'Message after sending', 'us' ),
				'type' => 'text',
				'std' => __( 'Thank you! Your message was sent.', 'us' ),
				'group' => __( 'More Options', 'us' ),
			),
			'fields_layout' => array(
				'title' => __( 'Fields Layout', 'us' ),
				'type' => 'radio',
				'options' => array(
					'ver' => __( 'Vertical', 'us' ),
					'hor' => __( 'Horizontal', 'us' ),
				),
				'std' => 'ver',
				'cols' => 2,
				'group' => __( 'More Options', 'us' ),
				'usb_preview' => array(
					'mod' => 'layout',
				),
			),
			'fields_gap' => array(
				'title' => __( 'Gap between Fields', 'us' ),
				'type' => 'slider',
				'std' => '1rem',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
					'rem' => array(
						'min' => 0.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'vh' => array(
						'min' => 0.0,
						'max' => 9.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'group' => __( 'More Options', 'us' ),
				'usb_preview' => array(
					'css' => '--fields-gap',
				),
			),
		),

		$design_options_params
	),
	'usb_init_js' => 'jQuery( $elm ).wForm()',
);
