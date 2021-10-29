/**
 * USOF Field: Font
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'font' ] = {
		init: function( options ) {
			this.parentInit( options );
			// Elements
			this.$preview = this.$row.find( '.usof-font-preview' );
			this.$weightsContainer = this.$row.find( '.usof-checkbox-list' );
			this.$weightCheckboxes = this.$weightsContainer.find( '.usof-checkbox' );
			this.$weights = this.$weightsContainer.find( 'input' );
			// Variables
			this.fontStyleFields = this.$row.find( '.usof-font-style-fields-json' )[ 0 ].onclick() || {};
			this.notLoadedFonts = [];
			this.fontInited = false;
			this.$fieldFontName = {};

			// Init font autocomplete
			var $autocomplete = $( '.type_autocomplete', this.$row );
			this.fontsGroupKeys = $autocomplete[ 0 ].onclick() || {};
			$autocomplete.removeAttr( 'onclick' );

			if ( $autocomplete.length ) {
				this.$fieldFontName = new $usof.field( $autocomplete );
				this.$fieldFontName.trigger( 'beforeShow' );
			}

			this.curFont = this.$fieldFontName.getValue();
			this.isCurFontUploaded = this.fontHasGroup.call( this, this.curFont, 'uploaded' );

			if ( ! $usof.loadingFonts ) {
				$usof.loadingFonts = true;
				$.ajax( {
					type: 'POST',
					url: $usof.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'usof_get_google_fonts',
						_wpnonce: this.$row.closest( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
						_wp_http_referer: this.$row.closest( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()
					},
					success: function( result ) {
						$usof.googleLoaded = true;
						$usof.googleFonts = result.data.google_fonts || {};
					},
					error: function() {
						$usof.googleLoaded = true;
						$usof.googleFonts = {};
					}
				} );
			}

			var that = this,
				fontsTimeoutId = setTimeout( function fontsTimeout() {
					if ( $usof.googleLoaded ) {
						that.fonts = $usof.googleFonts;
						that._init();
						clearTimeout( fontsTimeoutId );
					} else {
						fontsTimeoutId = setTimeout( fontsTimeout, 500 );
					}
				}, 500 );
		},
		_init: function() {
			/**
			 * Initializes not loaded fonts.
			 */
			var initNotLoadedFonts = function() {
				['websafe', 'uploaded'].map( function( groupName ) {
					if ( this.fontsGroupKeys.hasOwnProperty( groupName ) ) {
						$( '[data-group="' + this.fontsGroupKeys[ groupName ] + '"] > *', this.fontsGroupKeys.$list )
							.each( function( _, item ) {
								var value = $( item ).data( 'value' ) || '';
								if ( value && $.inArray( value, this.notLoadedFonts || [] ) === - 1 ) {
									this.notLoadedFonts.push( value );
								}
							}.bind( this ) );
					}
				}.bind( this ) );
			};
			initNotLoadedFonts.call( this );

			if (
				this.curFont
				&& $.inArray( this.curFont, ['get_h1', 'none'] ) === - 1
				&& $.inArray( this.curFont, this.notLoadedFonts || [] ) === - 1
			) {
				$( 'head' ).append( '<link href="//fonts.googleapis.com/css?family=' + this.curFont.replace( /\s+/g, '+' ) + '" rel="stylesheet" class="usof_font_' + this.id + '" />' );
				this.$preview.css( 'font-family', this.curFont + '' );
			} else if ( this.curFont != 'none' && $.inArray( this.curFont, this.notLoadedFonts || [] ) !== - 1 ) {
				this.$preview.css( 'font-family', this.curFont + '' );
			}

			this.$fieldFontName
				.on( 'change', function() {
					this.isCurFontUploaded = this.fontHasGroup.call( this, this.$fieldFontName.getValue(), 'uploaded' );
					this.setValue( this._getValue.call( this ) );
				}.bind( this ) )
				.on( 'data.loaded', function() {
					initNotLoadedFonts.call( this );
				}.bind( this ) );

			this.$weights.on( 'change', function() {
				this.setValue( this._getValue.call( this ) );
			}.bind( this ) );

			if ( this.fontStyleFields.colorField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.colorField ].on( 'change', function() {
					this.$preview.css( 'color', $usof.instance.fields[ this.fontStyleFields.colorField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.sizeField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.sizeField ].on( 'change', function() {
					this.$preview.css( 'font-size', $usof.instance.fields[ this.fontStyleFields.sizeField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.lineheightField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.lineheightField ].on( 'change', function() {
					this.$preview.css( 'line-height', $usof.instance.fields[ this.fontStyleFields.lineheightField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.weightField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.weightField ].on( 'change', function() {
					this.$preview.css( 'font-weight', $usof.instance.fields[ this.fontStyleFields.weightField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.letterspacingField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.letterspacingField ].on( 'change', function() {
					this.$preview.css( 'letter-spacing', $usof.instance.fields[ this.fontStyleFields.letterspacingField ].getValue() );
				}.bind( this ) );
			}
			if ( this.fontStyleFields.transformField != undefined ) {
				$usof.instance.fields[ this.fontStyleFields.transformField ].on( 'change', function() {
					if ( $usof.instance.fields[ this.fontStyleFields.transformField ].getValue().indexOf( "uppercase" ) != - 1 ) {
						this.$preview.css( 'text-transform', 'uppercase' );
					} else {
						this.$preview.css( 'text-transform', '' );
					}
					if ( $usof.instance.fields[ this.fontStyleFields.transformField ].getValue().indexOf( "italic" ) != - 1 ) {
						this.$preview.css( 'font-style', 'italic' );
					} else {
						this.$preview.css( 'font-style', '' );
					}
				}.bind( this ) );
			}

			this.setValue( this._getValue(), true );
			this.fontInited = true;
		},
		/**
		 * Check whether the font belongs to the specified group
		 *
		 * @param string fontName
		 * @param string groupKey
		 * @return boolean
		 */
		fontHasGroup: function( fontName, groupKey ) {
			var $item = this.$fieldFontName.$list.find( '[data-value="' + fontName + '"]' ),
				$group = $item.closest( '.usof-autocomplete-list-group' );
			if ( $group.length && this.fontsGroupKeys.hasOwnProperty( groupKey ) ) {
				return $group.is( '[data-group="' + this.fontsGroupKeys[ groupKey ] + '"]' );
			}
			return false;
		},
		setValue: function( value, quiet ) {
			var h1_value, parts, fontName, fontWeights;
			// TODO: make this value-independent
			if ( value === 'get_h1|' ) {
				h1_value = $usof.instance.getValue( 'h1_font_family' );
				parts = h1_value.split( '|' );
			} else {
				parts = value.split( '|' );
			}

			fontName = parts[ 0 ] || 'none';
			fontWeights = parts[ 1 ] || '400,700';
			fontWeights = fontWeights.split( ',' );
			if ( fontName != this.curFont ) {
				$( '.usof_font_' + this.id ).remove();
				if ( fontName == 'none' ) {
					// Selected no-font
					this.$preview.css( 'font-family', '' );
				} else if ( $.inArray( fontName, this.notLoadedFonts || [] ) !== - 1 ) {
					// Web-safe font combination and uploaded fonts
					this.$preview.css( 'font-family', fontName );
				} else {
					// Selected some google font: show preview
					if ( this.curFont !== 'get_h1' ) {
						$( 'head' )
							.append( '<link href="//fonts.googleapis.com/css?family=' + fontName.replace( /\s+/g, '+' ) + '" rel="stylesheet" class="usof_font_' + this.id + '" />' );
					}
					this.$preview.css( 'font-family', fontName + ', sans-serif' );
				}
				// setValue may be called both from inside and outside, so checking to avoid recursion
				if ( value === 'get_h1|' ) {
					if ( this.$fieldFontName.getValue() !== 'get_h1' ) {
						this.$fieldFontName.setValue( 'get_h1' );
					}
				} else if ( this.$fieldFontName.getValue() !== fontName ) {
					this.$fieldFontName.setValue( fontName );
				}
				this.curFont = fontName;
			}
			if ( this.fontStyleFields.weightField == undefined ) {
				if ( fontWeights.length == 0 ) {
					this.$preview.css( 'font-weight', '' );
				} else {
					this.$preview.css( 'font-weight', parseInt( fontWeights[ 0 ] ) );
				}
			}
			// Show the available weights
			if ( value === 'get_h1|' || this.fonts[ fontName ] === undefined || this.isCurFontUploaded ) {
				this.$weightCheckboxes.addClass( 'hidden' );
			} else {
				this.$weightCheckboxes.each( function( index, elm ) {
					var $elm = $( elm ),
						weightValue = $elm.data( 'value' ) + '';
					$elm.toggleClass( 'hidden', $.inArray( weightValue, this.fonts[ fontName ].variants ) == - 1 );
					$elm.attr( 'checked', ( $.inArray( weightValue, fontWeights ) == - 1 ) ? 'checked' : false );
				}.bind( this ) );
			}
			this.parentSetValue( value, quiet );

			// TODO: make this value-independent
			if ( this.name === 'h1_font_family' ) {
				for ( var i = 2; i <= 6; i ++ ) {
					var fontFieldId = 'h' + i + '_font_family',
						fontField;
					if ( $usof.instance.fields.hasOwnProperty( fontFieldId ) && $usof.instance.fields[ fontFieldId ].fontInited ) {
						fontField = $usof.instance.fields[ fontFieldId ];
						fontField.setValue( fontField._getValue() );
					}
				}
			}
		},
		_getValue: function() {
			var fontName = this.$fieldFontName.getValue(),
				fontWeights = [];
			if ( this.fonts[ fontName ] !== undefined && this.fonts[ fontName ].variants !== undefined ) {
				this.$weights.filter( ':checked' ).each( function( index, elm ) {
					var weightValue = $( elm ).val() + '';
					if ( $.inArray( weightValue, this.fonts[ fontName ].variants ) != - 1 ) {
						fontWeights.push( weightValue );
					}
				}.bind( this ) );
			}
			return fontName + '|' + fontWeights.join( ',' );
		}

	};
}( jQuery );
