// Fixing contact form 7 semantics, when requested
jQuery( '.wpcf7' ).each( function() {
	var $form = jQuery( this );

	// Removing wrong newlines
	$form.find( 'br' ).remove();

	// Fixing quiz layout
	$form.find( '.w-form-row .wpcf7-quiz' ).each( function() {
		var $input = jQuery( this ),
			$row = $input.closest( '.w-form-row' ),
			$field = $row.find( '.w-form-row-field:first' ),
			$label = $row.find( '.wpcf7-quiz-label' );
		$label.insertBefore( $field ).attr( 'class', 'w-form-row-label' );
		$input.unwrap();
	} );

	// Removing excess wrappers
	$form.find( '.w-form-row-field > .wpcf7-form-control-wrap > .wpcf7-form-control' ).each( function() {
		var $input = jQuery( this );
		if ( ( $input.attr( 'type' ) || '' ).match( /^(text|email|url|tel|number|date|quiz|captcha)$/ ) || $input.is( 'textarea' ) ) {
			// Moving wrapper classes to .w-form-field, and removing the span wrapper
			var wrapperClasses = $input.parent().get( 0 ).className;
			$input.unwrap();
			$input.parent().get( 0 ).className += ' ' + wrapperClasses;
		}
	} );

	// Transforming submit button
	$form.find( '.w-form-row-field > .wpcf7-submit' ).each( function() {
		var $input = jQuery( this ),
			classes = $input.attr( 'class' ).split( ' ' ),
			value = $input.attr( 'value' ) || '';
		$input.siblings( 'p' ).remove();
		if ( jQuery.inArray( 'w-btn', classes ) == - 1 ) {
			classes.push( 'w-btn' );
		}
		var buttonHtml = '<button id="message_send" class="' + classes.join( ' ' ) + '">' +
			'<div class="g-preloader"></div>' +
			'<span class="w-btn-label">' + value + '</span>' +
			'<span class="ripple-container"></span>' +
			'</button>';
		$input.replaceWith( buttonHtml );
	} );

	// Adjusting proper wrapper for select controller
	$form.find( '.wpcf7-form-control-wrap > select' ).each( function() {
		var $select = jQuery( this );
		if ( ! $select.attr( 'multiple' ) ) {
			$select.parent().addClass( 'type_select' );
		}
	} );

	$form.on( 'mailsent.wpcf7', function() {
		$form.find( '.w-form-row.not-empty' ).removeClass( 'not-empty' );
	} );
} );