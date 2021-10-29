/**
 * USOF Field: Color
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'color' ] = {

		init: function( options ) {
			// Elements
			this.$color = this.$row.find( '.usof-color' );
			this.$clear = $( '.usof-color-clear', this.$color );
			this.$list = $( '.usof-color-list', this.$color );
			this.$preview = $( '.usof-color-preview', this.$color );

			// Variables
			this.withGradient = !! this.$color.is( '.with-gradient' );
			this.isDynamicСolors = !! this.$color.is( '.dynamic_colors' );

			// Set white text color for dark backgrounds
			this._toggleInputColor( this.getColor() );

			// Init colpick on focus
			this.$input
				.off( 'focus' )
				.on( 'focus', this._events.initColpick.bind( this ) )
				.on( 'input', this._events.inputValue.bind( this ) )
				.on( 'change', this._events.changeValue.bind( this ) );

			this.$clear
				.on( 'click', this._events.inputClear.bind( this ) );

			// Init of a sheet of dynamic colors on click
			if ( this.isDynamicСolors ) {
				this.$color
					.on( 'click', '.usof-color-arrow', this._events.toggleList.bind( this ) )
					.on( 'click', '.usof-color-list-item', this._events._changeColorListItem.bind( this ) );
			}

			// If the sheet is open and there was a click outside the sheet, then close the sheet
			$( _document )
				.mouseup( this._events.hideList.bind( this ) );
		},
		_events: {
			/**
			 * Init colpick.
			 */
			initColpick: function() {
				this.$input
					.usof_colpick( {
						input: this.$input,
						value: this.getColor(),
						onChange: function( colors ) {
							this._invertInputColors( colors.color.first.rgba );
						}.bind( this ),
					} );
			},

			/**
			 * Init of a sheet of dynamic variables
			 * @param void
			 */
			toggleList: function( e ) {
				if ( ! this.$color.is( '.show' ) ) {
					this.initDynamicColors();
				}
				this.$color.toggleClass( 'show' );
			},

			/**
			 * Change color list item
			 *
			 * @private
			 * @event handler
			 *
			 * @param {Event} e The Event interface represents an event which takes place in the DOM.
			 */
			_changeColorListItem: function( e ) {
				this.сhooseColorVar( $( e.currentTarget ).data('name') || '' );
			},

			/**
			 * Hides the list.
			 * @param {Event} e
			 */
			hideList: function( e ) {
				if ( ! this.$color.is( '.show' ) ) {
					return;
				}
				if ( ! this.$color.is( e.target ) && this.$color.has( e.target ).length === 0 ) {
					this.$color.removeClass( 'show' );
				}
			},

			/**
			 * Input value
			 * @return value
			 */
			inputValue: function() {
				var value = this.getValue();
				// Preloading the list of variables if the value contains brackets
				if ( value.indexOf( '_' ) !== - 1 ) {
					this.initDynamicColors();
				}
			},

			/**
			 * Changed value.
			 */
			changeValue: function() {
				var value = this.getValue();
				// Check the value for dynamic variables
				if ( value.indexOf( '_' ) !== - 1 ) {
					$( '[data-name^="' + value + '"]:first', this.$list )
						.trigger( 'click' );
				} else {
					this.setValue( value );
					this.trigger( 'change', value );
				}
			},

			/**
			 * Clear value.
			 */
			inputClear: function() {
				if ( this.$color.hasClass( 'show' ) ) {
					this.$color.removeClass( 'show' );
				}
				this.setValue( '' );
			}
		},
		/**
		 * Choose a color from a list of variables.
		 *
		 * @param {string} name The color name from the list example `content_bg_alt`
		 * @param {boolean} quiet The quiet mode
		 */
		сhooseColorVar: function( name, quiet ) {
			var $target = $( '[data-name="' + name + '"]:first', this.$list ),
				value = $target.data( 'value' ) || '';

			$( '[data-name]', this.$list ) // Reset all selected
				.removeClass( 'selected' );
			$target // Selected color
				.addClass( 'selected' );
			this.$preview // Show preview
				.css( 'background', value );
			this.$input // Set current value
				.val( $target.data( 'name' ) || '' );
			if ( ! quiet ) {
				this.trigger( 'change', this.$input.val() );
			}
			// Set white text color for dark backgrounds
			this._toggleInputColor( value );
			this.$color
				.removeClass( 'show' );
		},

		/**
		 * Add dynamic colors to the list
		 */
		initDynamicColors: function() {
			if ( this.$color.is( '.list-inited' ) ) return;
			var /**
				 * Add item to list.
				 *
				 * @param {node} $el
				 * @param {node} item
				 */
				insertItem = function( $el, item ) {
					// Exclude yourself
					if ( this.name === item.name ) {
						return;
					}
					var $item = $( '<div></div>' ),
						$palette = $( '<div class="usof-colpick-palette-value"><span></span></div>' ),
						value = this.getValue();
					$palette
						.find( 'span' )
						.css( 'background', item.value )
						.attr( 'title', item.value );
					$item
						.addClass( 'usof-color-list-item' )
						.attr( 'data-name', item.name )
						.data( 'value', item.value )
						.append( $palette )
						.append( '<span class="usof-color-list-item-name">' + item.title + '</span>' );
					if ( value.indexOf( '_' ) !== - 1 && item.name === value ) {
						$item.addClass( 'selected' );
					}
					$el.append( $item );
				};

			// Add dynamic colors to the list
			$.each( $usof.getDynamicColors() || [], function( key, item ) {
				// Group options
				if ( $.isArray( item ) && item.length ) {
					$group = $( '> [data-group="' + key + '"]:first', this.$list );
					if ( ! $group.length ) {
						$group = $( '<div class="usof-color-list-group" data-group="' + key + '"></div>' );
						this.$list.append( $group );
					}
					$.each( item, function( _, _item ) {
						insertItem.call( this, $group, _item );
					}.bind( this ) );
					// Options
				} else {
					insertItem.call( this, this.$list, item );
				}
			}.bind( this ) );
			this.$color
				.addClass( 'list-inited' );
		},

		/**
		 * Set the value.
		 *
		 * @param {string} value
		 * @param {boolean} quiet
		 */
		setValue: function( value, quiet ) {
			value = value.trim();

			// Check the value for dynamic variables
			if ( value.indexOf( '_' ) !== - 1 ) {
				this.initDynamicColors();
				this.сhooseColorVar( value, quiet );
				return;
			}

			var r, g, b, a, hexR, hexG, hexB, gradient, rgba = {};

			this.convertRgbToHex = function( color ) {
				if ( m = /^([^0-9]{1,3})*(\d{1,3})[^,]*,([^0-9]{1,3})*(\d{1,3})[^,]*,([^0-9]{1,3})*(\d{1,3})[\s\S]*$/.exec( color ) ) {
					rgba = {
						r: m[ 2 ],
						g: m[ 4 ],
						b: m[ 6 ],
					};
					hexR = m[ 2 ] <= 255 ? ( "0" + parseInt( m[ 2 ], 10 ).toString( 16 ) ).slice( - 2 ) : 'ff';
					hexG = m[ 4 ] <= 255 ? ( "0" + parseInt( m[ 4 ], 10 ).toString( 16 ) ).slice( - 2 ) : 'ff';
					hexB = m[ 6 ] <= 255 ? ( "0" + parseInt( m[ 6 ], 10 ).toString( 16 ) ).slice( - 2 ) : 'ff';
					color = '#' + hexR + hexG + hexB;
					return color;
				}
			};

			if ( $.usof_colpick.isGradient( value ) ) {
				gradient = $.usof_colpick.gradientParser( value );
				rgba = $.usof_colpick.hexToRgba( gradient.hex );
			} else if ( ( m = /^[^,]*,[^,]*,[\s\S]*$/.exec( value ) ) ) {
				// Catch RGB and RGBa
				if ( m = /^[^,]*(,)[^,]*(,)[^,]*(,)[^.]*(\.|0)[\s\S]*$/.exec( value ) ) {
					// Catch only RGBa values
					if ( m[ 4 ] === '.' || m[ 4 ] == 0 ) {
						if ( m = /^([^0-9]{1,3})*(\d{1,3})[^,]*,([^0-9]{1,3})*(\d{1,3})[^,]*,([^0-9]{1,3})*(\d{1,3})[^,]*,[^.]*.([^0-9]{1,2})*(\d{1,2})[\s\S]*$/.exec( value ) ) {
							rgba = {
								r: m[ 2 ],
								g: m[ 4 ],
								b: m[ 6 ],
							};
							r = m[ 2 ] <= 255 ? m[ 2 ] : 255;
							g = m[ 4 ] <= 255 ? m[ 4 ] : 255;
							b = m[ 6 ] <= 255 ? m[ 6 ] : 255;
							a = m[ 8 ];
							value = 'rgba(' + r + ',' + g + ',' + b + ',0.' + a + ')';
						}
					} else {
						value = this.convertRgbToHex( value );
					}
				} else {
					value = this.convertRgbToHex( value );
				}
			} else {
				// Check Hex Colors
				if ( m = /^\#?[\s\S]*?([a-fA-F0-9]{1,6})[\s\S]*$/.exec( value ) ) {
					if ( value == 'inherit' || value == 'transparent' || $.usof_colpick.colorNameToHex( value ) ) {
						value = value;
					} else {
						value = $.usof_colpick.normalizeHex( m[ 1 ] );
						rgba = $.usof_colpick.hexToRgba( value );
					}
				}
			}

			if ( value == '' ) {
				this.$preview.removeAttr( 'style' );
				this.$input.removeClass( 'with_alpha' );
			} else {
				if ( value == 'inherit' || value == 'transparent' ) {
					this.$input.removeClass( 'white' );
					this.$preview.css( 'background', value );
				} else if ( gradient ) {
					if ( this.withGradient ) {
						this.$preview.css( 'background', gradient.gradient );
						this.$input.val( gradient.gradient );
					} else {
						// Don't allow to use gradient colors
						value = gradient.hex;
						this.$preview.css( 'background', value );
						this.$input.val( value );
					}
				} else {
					this.$preview.css( 'background', value );
					this.$input.val( value );
				}
			}

			if ( value == '' || value == 'inherit' || value == 'transparent' ) {
				this.$input.removeClass( 'white' );
			} else {
				this._invertInputColors( rgba );
			}

			this.parentSetValue( value, quiet );
		},

		/**
		 * Get the value.
		 * @return string
		 */
		getValue: function() {
			return $.trim( this.$input.val() ) || '';
		},

		/**
		 * Get color, variables will be replaced with value
		 * @return {string}
		 */
		getColor: function() {
			var value = this.getValue();
			if ( value.indexOf( '_' ) !== - 1 ) {
				var itemValue = $( '[data-name="' + value + '"]:first', this.$list ).data( 'value' ) || '';
				value = itemValue || this.$color.data( 'value' ) || value;
			}
			return $.trim( value );
		},

		/**
		 * Set white text color for dark backgrounds
		 *
		 * @param {string} value
		 */
		_toggleInputColor: function( value ) {
			if ( ! value ) {
				this.$input.removeClass( 'white' );
				return;
			}
			// If the HEX value is 3-digit, then convert it to 6-digit
			if ( value.slice( 0, 1 ) === '#' && value.length === 4 ) {
				value = value.replace( /^#([\dA-f])([\dA-f])([\dA-f])$/, "#$1$1$2$2$3$3" )
			}
			if (
				value !== 'inherit'
				&& value !== 'transparent'
				&& value.indexOf( 'linear-gradient' ) === - 1
			) {
				if ( $.usof_colpick.colorNameToHex( value ) ) {
					this._invertInputColors( $.usof_colpick.hexToRgba( $.usof_colpick.colorNameToHex( value ) ) );
				} else {
					this._invertInputColors( $.usof_colpick.hexToRgba( value ) );
				}
			} else if ( value.indexOf( 'linear-gradient' ) !== - 1 ) {
				var gradient = $.usof_colpick.gradientParser( value );
				// Make sure the gradient was parsed
				if ( gradient != false ) {
					this._invertInputColors( $.usof_colpick.hexToRgba( gradient.hex ) );
				}
			}
		},

		_invertInputColors: function( rgba ) {
			if ( ! rgba && ( typeof rgba != 'object' ) ) {
				return;
			}
			var r = rgba.r ? rgba.r : 0,
				g = rgba.g ? rgba.g : 0,
				b = rgba.b ? rgba.b : 0,
				a = ( rgba.a === 0 || rgba.a ) ? rgba.a : 1,
				light;
			// Determine lightness of color
			light = r * 0.213 + g * 0.715 + b * 0.072;
			// Increase lightness regarding color opacity
			if ( a < 1 ) {
				light = light + ( 1 - a ) * ( 1 - light / 255 ) * 235;
			}
			if ( light < 178 ) {
				this.$input.addClass( 'white' );
			} else {
				this.$input.removeClass( 'white' );
			}
		}
	};

}( jQuery );
