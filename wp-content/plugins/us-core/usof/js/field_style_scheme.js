/**
 * USOF Field: Style Scheme
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'style_scheme' ] = {

		init: function( options ) {
			this.$input = this.$row.find( 'input[name="' + this.name + '"]' );
			this.$schemesContainer = this.$row.find( '.usof-schemes-list' );
			this.$schemeItems = this.$row.find( '.usof-schemes-list > li' );
			this.$nameInput = this.$row.find( '#scheme_name' );
			this.$closeBtn = this.$row.find( '.us-bld-window-closer' );
			this.$saveNewBtn = this.$row.find( '#save_new_scheme' ).on( 'click', this.saveScheme.bind( this ) );
			this.colors = ( this.$row.find( '.usof-form-row-control-colors-json' )[ 0 ].onclick() || {} );
			this.i18n = ( this.$row.find( '.usof-form-row-control-i18n' )[ 0 ].onclick() || {} );
			this.$nameInput.on( 'keyup', this.setSchemeButtonStates.bind( this ) );
			this.initSchemes( true );
		},
		setSchemeButtonStates: function( action ) {
			if ( ! this.$schemesContainer ) {
				return;
			}
			if ( this.$nameInput.val().length ) {
				this.$saveNewBtn.removeAttr( 'disabled' );
				if ( action.key == 'Enter' ) {
					this.$saveNewBtn.click();
				}
			} else {
				this.$saveNewBtn.attr( 'disabled', '' );
			}
		},
		initSchemes: function( initialize, id ) {
			if ( initialize ) {
				// Hide Schemes on init
				this.$row.hide();

				// Close Schemes popup
				this.$closeBtn.on( 'click', function() {
					this.$row.hide();
				}.bind( this ) );

				$.ajax( {
					type: 'POST',
					url: $usof.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'usof_get_style_schemes',
						_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
						_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
					},
					success: function( result ) {
						this.schemes = result.data.schemes;
						this.customSchemes = result.data.custom_schemes;
					}.bind( this ),
					error: function() {
						this.schemes = {};
						this.customSchemes = {};
					}.bind( this ),
				} );
			}

			if ( id ) {
				var $savedScheme = this.$schemeItems.filter( '.type_custom[data-id="' + id + '"]' );
				$savedScheme.addClass( 'saved' );
				setTimeout( function() {
					$savedScheme.removeClass( 'saved' );
				}, 900 );
			}
			this.$schemeItems.each( function( index, item ) {
				var $item = $( item ),
					$deleteBtn = $item.find( '.usof-schemes-item-delete' ),
					schemeId = $item.data( 'id' ),
					isCustom = $item.hasClass( 'type_custom' ),
					colors;

				$item.find( '.usof-schemes-item-save' ).on( 'click', this.saveScheme.bind( this ) );

				$deleteBtn.on( 'click', function( event ) {
					event.preventDefault();
					event.stopPropagation();
					var $target = $( event.target ),
						$deletingScheme = $target.closest( '.usof-schemes-item' ),
						schemeId = $deletingScheme.data( 'id' );
					this.deleteScheme( schemeId, event );
				}.bind( this ) );

				$item.on( 'click', function() {
					if ( _window.$usof !== undefined && $usof.instance !== undefined ) {
						if ( ( ! isCustom && this.schemes[ schemeId ] === undefined ) || ( isCustom && this.customSchemes[ schemeId ] === undefined ) || ( ! isCustom && this.schemes[ schemeId ].values === undefined ) || ( isCustom && this.customSchemes[ schemeId ].values === undefined ) ) {
							return;
						}
						this.setSchemeButtonStates();
						if ( isCustom ) {
							colors = this.customSchemes[ schemeId ].values;
							this.$input.val( 'custom-' + schemeId );
							this.trigger( 'change', 'custom-' + schemeId );
						} else {
							colors = this.schemes[ schemeId ].values;
							this.$input.val( schemeId );
							this.trigger( 'change', schemeId );
						}
						$.each( colors, function( id, value ) {
							$usof.instance.setValue( id, value );
						} );
					}
					this.$row.hide();
				}.bind( this ) );
			}.bind( this ) );
		},
		getColorValues: function() {
			var colors = {};
			if ( _window.$usof === undefined || $usof.instance == undefined ) {
				return undefined;
			}
			if ( this.colors == undefined ) {
				return undefined;
			}
			$.each( this.colors, function( id, color ) {
				colors[ color ] = $usof.instance.getValue( color );
			} );

			return colors;
		},
		saveScheme: function( event ) {
			var colors = this.getColorValues(),
				name = this.$nameInput.val(),
				scheme = { name: name, colors: colors },
				$target = $( event.target ),
				$savingScheme = $target.closest( '.usof-schemes-item' ),
				$button = $( event.target.closest( 'button' ) );
			if ( ! $button.length ) {
				if ( $savingScheme.hasClass( 'type_custom' ) ) {
					scheme.name = $savingScheme.find( '.preview_heading' ).html();
					scheme.id = $savingScheme.data( 'id' );
					$savingScheme.addClass( 'saving' );
				} else {
					return false;
				}
			} else {
				this.$saveNewBtn.addClass( 'loading' );
			}
			$.ajax( {
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_save_style_scheme',
					scheme: JSON.stringify( scheme ),
					_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
				},
				success: function( result ) {
					this.setSchemes( result.data.schemes, result.data.customSchemes, result.data.schemesHtml, scheme.id );
					this.$nameInput.val( '' );
					this.$saveNewBtn.removeClass( 'loading' ).attr( 'disabled', '' );
				}.bind( this )
			} );
			return false;
		},
		deleteScheme: function( schemeId, event ) {
			event.stopPropagation();
			if ( ! confirm( this.i18n.delete_confirm ) ) {
				return false;
			}
			var $target = $( event.target );
			$target.closest( '.usof-schemes-item' ).addClass( 'deleting' );
			$.ajax( {
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_delete_style_scheme',
					scheme: schemeId,
					_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
				},
				success: function( result ) {
					this.setSchemes( result.data.schemes, result.data.customSchemes, result.data.schemesHtml );
				}.bind( this )
			} );
			return false;
		},
		setSchemes: function( schemes, customSchemes, schemesHtml, id ) {
			this.schemes = schemes;
			this.customSchemes = customSchemes;
			this.$schemesContainer.html( schemesHtml );
			this.$schemeItems = this.$row.find( '.usof-schemes-list > li' );
			this.initSchemes( false, id );
		}
	};
}( jQuery );
