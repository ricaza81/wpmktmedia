/**
 * UpSolution Shortcode: us_cform
 */
jQuery( function( $ ) {

	$( '.w-form.for_cform' ).each( function() {
		var $form = $( this ),
			$submitBtn = $form.find( '.w-btn' ),
			$resultField = $form.find( '.w-form-message' ),
			options = $form.find( '.w-form-json' )[ 0 ].onclick(),
			$dateField = $form.find( '.w-form-row.for_date input' ),
			jQueryDatePickerPath = $form.data( 'jquery-ui' ),
			pickerOptions = {},
			$requiredCheckboxes = $form.find( '.for_checkboxes.required' );
		$form.find( '.w-form-json' ).remove();

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
			$form.find( '[data-required="true"]' ).each( function() {
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
					var $input = $( this ).find( 'input[type="checkbox"]' ),
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
						$form.find( '.w-form-row.check_wrong' ).removeClass( 'check_wrong' );
						$form.find( '.w-form-row.not-empty' ).removeClass( 'not-empty' );
						$form.find( 'input[type="text"], input[type="email"], textarea' ).val( '' );
						$form[ 0 ].reset();
						$form.trigger( 'usCformSuccess' );
					} else {
						$form.find( '.w-form-row.check_wrong' ).removeClass( 'check_wrong' );
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
								var $input = $form.find( '[name="' + fieldName + '"]' );
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
} );

/**
 * Form customs
 */
jQuery( function( $ ) {

	// Add not-empty class when filling form fields
	$( 'input[type="text"], input[type="email"], input[type="tel"], input[type="number"], input[type="date"], input[type="search"], input[type="url"], input[type="password"], textarea' ).each( function( index, input ) {
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

	// Add "focused" class for form fields. Needed for "Move title on focus" option
	$( document ).on( 'focus', '.w-form-row-field input, .w-form-row-field textarea', function() {
		$( this ).closest( '.w-form-row' ).addClass( 'focused' );
	} );

	// Remove "focused" class for form fields, except Date field, which conrolled by dateField
	$( document ).on( 'blur', '.w-form-row:not(.for_date) input, .w-form-row-field textarea', function() {
		$( this ).closest( '.w-form-row' ).removeClass( 'focused' );
	} );
} );
