/**
 * USOF Field: Transfer
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'transfer' ] = {

		init: function() {
			if ( $usof.instance.$sections[ 'headerbuilder' ] !== undefined ) {
				$usof.instance.fireFieldEvent( $usof.instance.$sections[ 'headerbuilder' ], 'beforeShow' );
				$usof.instance.fireFieldEvent( $usof.instance.$sections[ 'headerbuilder' ], 'beforeHide' );
			}

			this.$textarea = this.$row.find( 'textarea' );
			this.translations = ( this.$row.find( '.usof-transfer-translations' )[ 0 ].onclick() || {} );
			this.$btnImport = this.$row.find( '.usof-button.type_import' ).on( 'click', this.importValues.bind( this ) );

			this.hiddenFieldsValues = $( '.usof-hidden-fields' )[ 0 ].onclick() || {};
			// If there are no hidden values (array length == 0), hiddenFieldsValues JSON equals [] isntead of {}, but
			// wee need {}, so we can extend values of options correctly
			if ( this.hiddenFieldsValues.length == 0 ) {
				this.hiddenFieldsValues = {};
			}

			this.exportValues();
			this.on( 'beforeShow', this.exportValues.bind( this ) );
		},

		exportValues: function() {
			var values = $.extend( this.hiddenFieldsValues, $usof.instance.getValues() );
			this.$textarea.val( JSON.stringify( values ) );
		},

		importValues: function() {
			var encoded = this.$textarea.val(),
				values;

			if ( encoded.charAt( 0 ) == '{' ) {
				this.$btnImport.addClass( 'loading' );
				// New USOF export: json-encoded
				$.ajax( {
					type: 'POST',
					url: $usof.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'usof_save',
						usof_options: encoded,
						_wpnonce: $usof.instance.$container.find( '[name="_wpnonce"]' ).val(),
						_wp_http_referer: $usof.instance.$container.find( '[name="_wp_http_referer"]' ).val()
					},
					success: function( result ) {
						this.$btnImport.removeClass( 'loading' );
						if ( result.success ) {
							alert( this.translations.importSuccess );
							location.reload();
						} else {
							alert( result.data.message );
						}
					}.bind( this )
				} );
			} else {
				try {
					// Old SMOF export: base64-encoded
					var serialized = _window.atob( encoded ),
						matches = serialized.match( /(s\:[0-9]+\:\"(.*?)\"\;)|(i\:[0-9]+\;)/g ),
						_key = null,
						_value;
					values = {};
					for ( var i = 0; i < matches.length; i ++ ) {
						_value = matches[ i ].replace( ( matches[ i ].charAt( 0 ) == 's' ) ? /^s\:[0-9]+\:\"(.*?)\"\;$/ : /^i\:([0-9]+)\;$/, '$1' );
						if ( _key === null ) {
							_key = _value;
						} else {
							values[ _key ] = _value;
							_key = null;
						}
					}
					$usof.instance.setValues( values );
					this.valuesChanged = values;
					$usof.instance.save();
				}
				catch ( error ) {
					return alert( this.translations.importError );
				}

			}

		}

	};
}( jQuery );
