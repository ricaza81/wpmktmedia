/**
 * UpSolution Shortcode: us_cform
 */
jQuery( function( $ ) {
	$us.WForm = function( context ) {
		var $context = $( context );
		if ( ! $context.is( '.for_cform' ) ) {
			$context = $( '.w-form.for_cform', $context );
		}
		$context.each( function() {
			var $form = $( this ),
				$submitBtn = $( '.w-btn', $form ),
				$resultField = $( '.w-form-message', $form ),
				options = $( '.w-form-json', $form )[ 0 ].onclick() || {},
				$dateField = $( '.w-form-row.for_date input', $form ),
				jQueryDatePickerPath = $form.data( 'jquery-ui' ),
				pickerOptions = {},
				$requiredCheckboxes = $( '.for_checkboxes.required', $form );
			$( '.w-form-json', $form ).remove();
			// Init date pickers
			if ( $dateField.length ) {
				if ( jQueryDatePickerPath !== undefined ) {
					$us.getScript( jQueryDatePickerPath, function() {
						// Extend options with localized data
						pickerOptions = $.extend( pickerOptions, options[ 'jquery-ui-locale' ] );
						initDateFields();
					} );
				} else {
					initDateFields();
				}

				// Just to DRY
				function initDateFields() {
					$dateField.each( function() {
						pickerOptions.dateFormat = $( this ).data( 'date-format' );

						// Remove "focused" class, because input loses focus each time you click calendar
						pickerOptions.onClose = function() {
							$( this ).closest( '.w-form-row' ).removeClass( 'focused' );
						};
						$( this ).datepicker( pickerOptions );
					} );
				}
			}
			$form.submit( function( event ) {
				event.preventDefault();

				// Prevent double-sending
				if ( $submitBtn.hasClass( 'loading' ) ) {
					return;
				}

				$resultField.usMod( 'type', false ).html( '' );
				// Validation
				var errors = 0;
				$( '[data-required="true"]', $form ).each( function() {
					var $input = $( this ),
						isEmpty = $input.is( '[type="checkbox"]' ) ? ( ! $input.is( ':checked' ) ) : ( $input.val() == '' ),
						$row = $input.closest( '.w-form-row' );
					// Skip checkboxes
					if ( $row.hasClass( 'for_checkboxes' ) ) {
						return true;
					}
					$row.toggleClass( 'check_wrong', isEmpty );
					if ( isEmpty ) {
						errors ++;
					}
				} );

				// Count required checkboxes separately
				if ( $requiredCheckboxes.length ) {
					$requiredCheckboxes.each( function() {
						var $input = $( 'input[type="checkbox"]', this ),
							$row = $input.closest( '.w-form-row' ),
							isEmpty = ! $input.is( ':checked' ) ? true : false;
						$row.toggleClass( 'check_wrong', isEmpty );
						if ( isEmpty ) {
							errors ++;
						}
					} );
				}

				if ( errors !== 0 ) {
					return;
				}

				$submitBtn.addClass( 'loading' );
				$.ajax( {
					type: 'POST',
					url: options.ajaxurl,
					dataType: 'json',
					data: $form.serialize(),
					success: function( result ) {
						if ( result.success ) {
							$resultField.usMod( 'type', 'success' ).html( result.data );
							$( '.w-form-row.check_wrong', $form ).removeClass( 'check_wrong' );
							$( '.w-form-row.not-empty', $form ).removeClass( 'not-empty' );
							$( 'input[type="text"], input[type="email"], textarea', $form ).val( '' );
							$form[ 0 ].reset();
							$form.trigger( 'usCformSuccess' );
						} else {
							$( '.w-form-row.check_wrong', $form ).removeClass( 'check_wrong' );
							if ( result.data && typeof result.data == 'object' ) {
								for ( var fieldName in result.data ) {
									if ( fieldName == 'empty_message' ) {
										$resultField.usMod( 'type', 'error' );
										continue;
									}

									if ( ! result.data.hasOwnProperty( fieldName ) ) {
										continue;
									}

									fieldName = result.data[ fieldName ].name;
									var $input = $( '[name="' + fieldName + '"]', $form );
									$input.closest( '.w-form-row' ).addClass( 'check_wrong' )
								}
							} else {
								$resultField.usMod( 'type', 'error' ).html( result.data );
							}
						}
					},
					complete: function() {
						$submitBtn.removeClass( 'loading' );
					}
				} );
			} );
		} );
		// Add not-empty class when filling form fields
		$( 'input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="date"], input[type="search"], input[type="url"], input[type="password"], textarea', $context )
			.each( function( index, input ) {
				var $input = $( input ),
					$row = $input.closest( '.w-form-row' );
				if ( $input.attr( 'type' ) == 'hidden' ) {
					return;
				}
				$row.toggleClass( 'not-empty', $input.val() != '' );
				$input.on( 'input change', function() {
					$row.toggleClass( 'not-empty', $input.val() != '' );
				} );
			} );
	};
	$.fn.wForm = function() {
		return this.each( function() {
			$( this ).data( 'wForm', new $us.WForm( this ) );
		} );
	};
	// Init wForm
	$( document ).wForm();
} );

/**
 * Form customs
 */
jQuery( function( $ ) {
	// Add "focused" class for form fields. Needed for "Move title on focus" option
	$( document ).on( 'focus', '.w-form-row-field input, .w-form-row-field textarea', function() {
		$( this ).closest( '.w-form-row' ).addClass( 'focused' );
	} );

	// Remove "focused" class for form fields, except Date field, which conrolled by dateField
	$( document ).on( 'blur', '.w-form-row:not(.for_date) input, .w-form-row-field textarea', function() {
		$( this ).closest( '.w-form-row' ).removeClass( 'focused' );
	} );
} );
