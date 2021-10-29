/**
 * USOF Field: Slider with units
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'slider' ] = {
		init: function( options ) {
			// Elements
			this.$slider = this.$row.find( '.usof-slider' );
			this.$textfield = this.$row.find( 'input[type="text"]' );
			this.$box = this.$row.find( '.usof-slider-box' );
			this.$range = this.$row.find( '.usof-slider-range' );
			this.$unitsSelector = this.$row.find( '.usof-slider-selector-units' );
			this.$units = this.$row.find( '.usof-slider-selector-unit' );
			this.$body = $( _document.body );
			this.$window = $( _window );
			this.$usofContainer = $( '.usof-container' );

			// Variables
			var defaultUnit = {
				std: '',
				unit: 'px',
				step: 1,
				min: 0,
				max: 60
			};

			// Needed box dimensions
			this.sz = {};
			var draggedValue;

			// Default unit options
			this.defaultUnit = {};
			// Unit options (max, min, step, unit)
			this.unitsOptions = {};
			this.isFocused = false;

			/**
			 * Mouse and trackpad scroll events
			 */
			this._mouseScrollEvents = [ 'wheel', 'mousewheel', 'DOMMouseScroll' ];

			// Get data for units and default unit
			this.$units
				.each( function( index, item ) {
					var data = $( item ).data() || {};
					this.unitsOptions[ data.unit ] = $.extend( {}, defaultUnit, data );
					if ( index === 0 ) {
						$.extend( this.defaultUnit, this.unitsOptions[ data.unit ] );
					}
				}.bind( this ) );
			this.setUnit( this.$unitsSelector.data( 'unit' ) || this.defaultUnit.unit || '' );

			// Params
			this.unitsExpression = this.$unitsSelector.data( 'units_expression' ) || '\w+';
			
			/**
			 * Event handlers
			 */
			this._events = {
				dragstart: function( e ) {
					e.stopPropagation();
					this.$usofContainer.addClass( 'dragged' );
					this.$box.addClass( 'dragged' );
					this.sz = {
						left: this.$box.offset().left,
						right: this.$box.offset().left + this.$box.width(),
						width: this.$box.width()
					};
					this.$body.on( 'mousemove', this._events.dragmove );
					this.$window.on( 'mouseup', this._events.dragstop );
					this._events.dragmove( e );
				}.bind( this ),
				dragmove: function( e ) {
					e.stopPropagation();
					var x, value;
					if ( this.$body.hasClass( 'rtl' ) ) {
						x = Math.max( 0, Math.min( 1, ( this.sz == 0 ) ? 0 : ( ( this.sz.right - e.pageX ) / this.sz.width ) ) );
					} else {
						x = Math.max( 0, Math.min( 1, ( this.sz == 0 ) ? 0 : ( ( e.pageX - this.sz.left ) / this.sz.width ) ) )
					}
					value = parseFloat( this.min + x * ( this.max - this.min ) );
					value = Math.round( value / this.step ) * this.step;
					this.renderValue( value );
					draggedValue = value;
				}.bind( this ),
				dragstop: function( e ) {
					e.preventDefault();
					e.stopPropagation();
					this.$usofContainer.removeClass( 'dragged' );
					this.$box.removeClass( 'dragged' );
					this.$body.off( 'mousemove', this._events.dragmove );
					this.$window.off( 'mouseup', this._events.dragstop );
					this.setValue( draggedValue );
				}.bind( this ),
				mousewheel: function( e ) {
					e.preventDefault
						? e.preventDefault()
						: ( e.returnValue = false );
					e.stopPropagation();
					if ( ! this.isFocused ) {
						return false;
					}
					// wheelDelta doesn't let you know the number of pixels
					var direction = e.deltaY || e.detail || e.wheelDelta;

					if ( direction < 0 ) {
						var value = Math.min( this.max, parseFloat( this.getValue() ) + this.step );
					} else {
						var value = Math.max( this.min, parseFloat( this.getValue() ) - this.step );
					}
					value = Math.round( value / this.step ) * this.step;
					if ( $.isNumeric( value ) ) {
						value = this.getDecimal( value );
					}

					this.setValue( value );
				}.bind( this ),
				mouseenter: function( e ) {
					// https://developers.google.com/web/updates/2017/01/scrolling-intervention
					$.each( this._mouseScrollEvents, function( _, eventName ) {
						this.$window[0].addEventListener( eventName, this._events.mousewheel, { passive: false } );
					}.bind( this ) );
				}.bind( this ),
				mouseleave: function( e ) {
					// https://developers.google.com/web/updates/2017/01/scrolling-intervention
					$.each( this._mouseScrollEvents, function( _, eventName ) {
						this.$window[0].removeEventListener( eventName, this._events.mousewheel );
					}.bind( this ) );
				}.bind( this )
			};

			// Events
			this.$unitsSelector
				.on( 'mousedown', function( e ) {
					var $target = $( e.target )
						.closest( '.usof-slider-selector-unit' );
					// Do nothing if unit wasn't selected
					if ( ! $target.length ) {
						return;
					}
					var value = parseFloat( ( this.$textfield.val() || '' ).replace( '[^0-9.]+', '' ) ),
						unit = $target.data( 'unit' ) || '';
					this.setUnit( unit );
					this.$textfield.val( value + unit );
				}.bind( this ) );

			this.$box
				.on( 'mousedown', this._events.dragstart );

			this.$textfield
				.on( 'mouseenter', this._events.mouseenter )
				.on( 'mouseleave', this._events.mouseleave )
				.on( 'keyup', function( e ) {
					if ( ( '' + e.key ).toLowerCase() !== 'enter' ) return;
					this.$textfield.blur();
				}.bind( this ) )
				.on( 'focus', function() {
					var value = this.getValue();
					this.$textfield.val( value );
					this.oldTextFieldValue = value;
					this.isFocused = true;
				}.bind( this ) )
				.on( 'blur', function() {
					var rawValue = this.$textfield.val(),
						value = parseFloat( rawValue.replace( '[^0-9.]+', '' ) ),
						defaultUnit = this.defaultUnit.unit;;
					this.isFocused = false;
					if ( ! $.isNumeric( rawValue ) ) {
						var matches = this.$textfield.val()
								.match( new RegExp( '^(-?\\d+)(\\.)?(\\d+)?(' + this.unitsExpression + ')?$' ) );
						if ( matches && matches[ 4 ] ) {
							for ( var unit in this.unitsOptions ) {
								if ( unit !== matches[ 4 ] ) continue;
								var matchValue = matches[ 1 ],
									options = this.unitsOptions[ unit ];
								this.setUnit( unit );
								this.renderValue( value );
							}
						}
					} else {
						this.setUnit( defaultUnit );
					}
					if ( ! this.unit ) {
						this.setUnit( defaultUnit );
					}
					if ( ( value || parseFloat( value ) === 0 ) && value != this.oldTextFieldValue ) {
						this.setValue( value );
					} else {
						this.renderValue( this.oldTextFieldValue );
					}
				}.bind( this ) );
		},

		/**
		 * Get the decimal
		 *
		 * @param {string} value The value
		 * @return {numaric}
		 */
		getDecimal: function( value ) {
			value = parseFloat( value );
			var valueDecimalPart = Math.abs( value ) % 1 + '';

			if ( valueDecimalPart.charAt( 3 ) !== '' && valueDecimalPart.charAt( 3 ) !== '0' ) { // Decimal part has 1/100 part
				value = value.toFixed( 2 );
			} else if ( valueDecimalPart.charAt( 2 ) !== '' && valueDecimalPart.charAt( 2 ) !== '0' ) { // Decimal part has 1/10 part
				value = value.toFixed( 1 );
			} else { // Decimal part is less than 1/100 or it is just 0
				value = value.toFixed( 0 );
			}
			return value;
		},

		/**
		 * Set the unit
		 *
		 * @param {string} unit The unit name
		 */
		setUnit: function( unit ) {
			if ( ! this.unitsOptions[ unit ] || unit === this.unit ) {
				return;
			}
			// Set unit data
			$.extend( this, this.defaultUnit, this.unitsOptions[ unit ] || {} );
			this.$unitsSelector.data( 'unit', unit );
		},

		/**
		 * Render a value to determine a unit
		 *
		 * @param {(number|string)} value The value
		 * @return {string}
		 */
		renderValue: function( value ) {
			if ( ! $.isNumeric( value ) ) {
				value = parseFloat( value.replace( '[^0-9.]+', '' ) ) || 0;
			}
			var x = Math.max( 0, Math.min( 1, ( value - this.min ) / ( this.max - this.min ) ) );
			this.$range
				.css( this.$body.hasClass( 'rtl' ) ? 'right' : 'left', x * 100 + '%' );
			if ( $.isNumeric( value ) ) {
				value = this.getDecimal( value );
			}
			value = value + this.unit;
			this.$textfield.val( value );
			return value;
		},

		/**
		 * Set the value
		 *
		 * @param {string} value The value
		 * @param {boolean} quiet The quiet mode
		 */
		setValue: function( value, quiet ) {
			if ( this.unit ) {
				var valueStr = value + '',
					pattern = new RegExp( '^(-?\\d+)(\\.)?(\\d+)?(' + this.unitsExpression + ')?$' ),
					matches = valueStr.match( pattern );
				if ( matches != null ) {
					this.setUnit( matches[ 4 ] || this.unit );
					if ( matches[ 4 ] == undefined ) {
						value += this.unit;
					}
				}
			}
			value = this.renderValue( value );
			this.parentSetValue( value, quiet );
		}
	};
}( jQuery );
