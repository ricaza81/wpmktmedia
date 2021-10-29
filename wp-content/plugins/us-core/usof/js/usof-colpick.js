! function( $ ) {
	"use strict";

	var US_Colpick = {
		init: function( options ) {
			// Get initial values
			this.value = options.value;

			var defaults = {
				state: 'solid',
				height: 160, // Height of colorpicker area
				width: 160,
				angle: 220, // Width of angle box
				inputHeight: options.input.height(),
				initialSecondColor: {
					hex: '#000000',
					rgba: {
						r: 0,
						g: 0,
						b: 0,
						a: 1
					},
					hsba: {
						h: 360,
						s: 0,
						b: 0,
						a: 1,
					},
				}, // Default black color for gradient
				onChange: function() {
				}, // Default callback for USOF
				color: {
					first: {},
					second: {},
				}, // There might be more than one color
				initialColor: this.value,
			}, that = this; // Used by select timeout

			// Palette AJAX status to don't sent requests several times
			that.sending = false;

			// Main object for all the stuff
			that.colors = $.extend( {}, defaults, options );

			// Main input for storing color value
			that.$input = options.input;

			// Check whether the input accepts gradient values
			that.withGradient = that.$input.closest( '.usof-color' ).is( '.with-gradient' ) ? true : false;

			// Use a single instance for all the inputs
			that.$colpickTemplate = $( '.usof-colpick.usof-colpick-template' );

			// Remove all previous instances of the clone in case they weren't removed
			$( '.usof-colpick:not(.usof-colpick-template)' ).remove();

			// Clone and insert a template to a certain color input
			that.$colpick = that.$colpickTemplate.clone().removeClass( 'usof-colpick-template' );
			that.$colpick.insertAfter( that.$input );

			// Box for picking colors, changes along with hue
			that.$curentColorBox = that.$colpick.find( '.first .usof-colpick-color' );
			that.$gradientColorBox = that.$colpick.find( '.second .usof-colpick-color' );

			// Arrow of HUE bar
			that.$hueArr = that.$colpick.find( '.first .usof-colpick-hue-selector' );
			that.$hueArr2 = that.$colpick.find( '.second .usof-colpick-hue-selector' );

			// Alpha arrows
			that.$alphaArr = that.$colpick.find( '.first .usof-colpick-alpha-selector' );
			that.$gradientAlphaArr = that.$colpick.find( '.second .usof-colpick-alpha-selector' );

			// Alpha Containers
			that.$alphaContainer = that.$colpick.find( '.first .usof-colpick-alpha' );
			that.$gradientAlphaContainer = that.$colpick.find( '.second .usof-colpick-alpha' );

			// HUE containers
			that.$hueContainer = that.$colpick.find( '.first .usof-colpick-hue' );
			that.$gradientHueContainer = that.$colpick.find( '.second .usof-colpick-hue' );

			// Angle Container
			that.$angleContainer = that.$colpick.find( '.usof-colpick-angle' );

			// Angle Arrow
			that.$angle = that.$colpick.find( '.usof-colpick-angle-selector' );

			// Color Palette
			that.$palette = that.$colpick.find( '.usof-colpick-palette' );

			// Color dots
			that.$selector = that.$colpick.find( '.first .usof-colpick-color-selector' );
			that.$gradientDot = that.$colpick.find( '.second .usof-colpick-color-selector' );

			// State switchers Solid/Gradient
			that.$switchers = that.$colpick.find( '.usof-colpick-palette + .usof-radio input[type="radio"]' );
			that.$switchersBox = that.$colpick.find( '.usof-radio' );

			// Do not proceed if the color value is not valid
			if ( ! that.isValidColor( that.value ) ) {
				return;
			}

			// If the gradient is disabled but the value can hold the gradient, then we will convert it to HEX
			if ( ! that.withGradient && that.isGradient( that.value ) ) {
				that.value = that.gradientParser( that.value ).hex;
			}

			// Deactivate gradient colorpicker for certain inputs
			if ( ! that.withGradient ) {
				that.$switchersBox.remove();
				// Remove just in case, probably someone will want to cheat
				that.$angleContainer.remove();
				that.$colpick.find( '.second' ).remove();
			}

			that.setHuePosition();
			that.setCurrentColor();
			that.setDotPosition();
			that.setAlpha();
			that.$colpick.addClass( 'type_solid' );
			that.$colpick.removeClass( 'type_gradient' );

			if ( that.isGradient( that.value ) ) {
				that.setDotPosition( true );
				that.setCurrentColor( true );
				that.setHuePosition( true );
				that.setAlpha( true );
				that.setAngle();
				that.colors.state = 'gradient';
				that.$colpick.addClass( 'type_gradient' );
				that.$colpick.removeClass( 'type_solid' );
			}

			// HUE movement handler
			that.$hueContainer.off( 'mousedown touchstart' ).on( 'mousedown touchstart', function( e ) {
				e.preventDefault();
				that.downHue( e );
			} );

			that.$gradientHueContainer.off( 'mousedown touchstart' ).on( 'mousedown touchstart', function( e ) {
				e.preventDefault();
				that.downHue( e, true );
			} );

			// Selector movement handler
			that.$curentColorBox.off( 'mousedown touchstart' ).on( 'mousedown touchstart', function( e ) {
				e.preventDefault();
				that.downSelector( e );
			} );

			that.$gradientColorBox.off( 'mousedown touchstart' ).on( 'mousedown touchstart', function( e ) {
				e.preventDefault();
				that.downSelector( e, true );
			} );

			// Alpha movement handler
			that.$alphaContainer.off( 'mousedown touchstart' ).on( 'mousedown touchstart', function( e ) {
				e.preventDefault();
				that.downAlpha( e );
			} );

			that.$gradientAlphaContainer.off( 'mousedown touchstart' ).on( 'mousedown touchstart', function( e ) {
				e.preventDefault();
				that.downAlpha( e, true );
			} );

			that.$angleContainer.off( 'mousedown touchstart' ).on( 'mousedown touchstart', function( e ) {
				e.preventDefault();
				that.downAngle( e );
			} );

			// Make colpick visible on init
			that.$colpick.css( 'display', 'flex' );

			// Color palette handler
			that.$palette.on( 'mousedown', function( e ) {
				if ( e ) {
					e.preventDefault();
					e.stopPropagation();
				}
				that.colorPalette( e );
			} );

			// Set colpick fixed position
			that.setPosition( true );

			// Recount colpick position on scroll
			$( document ).on( 'scroll', function() {
				that.setPosition();
			} );

			// Recount colpick position on window resize
			$( window ).on( 'resize', function() {
				that.setPosition();
			} );

			// Set radio button Solid/Gradient state
			that.$switchers.removeAttr( 'checked' );
			that.$switchers.filter( '[value="' + that.colors.state + '"]' ).prop( 'checked', 'checked' );

			// Solid/Gradient handler
			$( document ).off( 'change', '.usof-colpick-palette + .usof-radio input[type="radio"]' )
				.on( 'change', '.usof-colpick-palette + .usof-radio input[type="radio"]', function( e ) {
				e.preventDefault();
				e.stopPropagation();

				var $target = $( e.target ).closest( 'input' ),
					value = $target.val();
				that.toggleGradient( value, true );
			} );

			// Hide colpick on blur
			that.$input.off( 'blur' ).on( 'blur', function() {
				that.hide();
			} );

			// Don't close the colorpicker when click gradient switcher
			that.$switchersBox.off( 'mousedown' ).on( 'mousedown', function( e ) {
				e.preventDefault();
				e.stopPropagation();
			} );

			// Select text on first click
			that.timeout = setTimeout( function() {
				that.$input.select();
			}, 5 );
		},
		colorPalette: function( ev ) {
			var color, palette, colorId, max, currId,
				$target = $( ev.target ),
				m, state = 'solid';
			if ( $target.is( 'span' ) ) {
				color = $target.attr( 'style' );
				if ( m = /^[^:]*:([\s\S]*)$/.exec( color ) ) {
					if ( this.isValidColor( m[ 1 ] ) ) {
						this.$input.val( m[ 1 ] );
						if ( this.withGradient ) {
							state = this.isGradient( m[ 1 ] ) ? 'gradient' : 'solid';
						}
						this.toggleGradient( state, true );
						this.$switchers.filter( '[value="' + this.colors.state + '"]' ).prop( 'checked', 'checked' );
					}
				}
			}

			// Do nothing outside USOF
			if ( ! $( '.usof-form' ).length ) {
				return false;
			}

			this.paletteSend = function( data ) {
				if ( ! data || this.sending ) {
					return;
				}
				this.sending = true;
				$.ajax( {
					type: 'POST',
					url: $usof.ajaxUrl,
					dataType: 'json',
					data: {
						action: 'usof_color_palette',
						color: JSON.stringify( data ),
						_wpnonce: $( '.usof-form' ).find( '[name="_wpnonce"]' ).val(),
						_wp_http_referer: $( '.usof-form' ).find( '[name="_wp_http_referer"]' ).val()

					},
					success: function( result ) {
						$( '.usof-colpick-palette' ).html( result.data.output );
						this.sending = false;
					}.bind( this )
				} );
			}.bind( this );

			if ( $target.hasClass( 'usof-colpick-palette-add' ) ) {
				$target.addClass( 'adding' );
				palette = { value: this.$input.val() };
				max = this.$palette.children( '.usof-colpick-palette-value' ).length;
				if ( max < 8 ) {
					this.paletteSend( palette );
				}
			}

			if ( $target.hasClass( 'usof-colpick-palette-delete' ) ) {
				currId = $.inArray( $target.closest( '.usof-colpick-palette-value' )[ 0 ], this.$palette.find( '.usof-colpick-palette-value' ) );
				if ( currId >= 0 ) {
					colorId = { colorId: currId };
					this.paletteSend( colorId );
					$target.closest( '.usof-colpick-palette-value' ).addClass( 'deleting' );
				}
			}
		},
		setPosition: function( init ) {
			var coordinates = this.$input.offset(),
				bottomSpace = document.body.clientHeight - ( coordinates.top - window.pageYOffset ),
				calWrapH = this.$colpick.outerHeight(),
				top = this.colors.inputHeight,
				right = 'auto';

			if ( bottomSpace < calWrapH ) {
				top = - calWrapH;
			}

			if ( coordinates.left + this.colors.width * 2 > document.body.clientWidth ) {
				right = 0;
			}

			this.$colpick.css( {
				'right': right,
				'top': top,
			} );
		},
		downAngle: function( ev ) {
			var $target = $( ev.target ),
				current, pageX, newVal;

			if ( $target.hasClass( 'usof-colpick-angle-selector' ) ) {
				$target = $target.parent();
			}
			current = {
				left: $target.offset().left,
			};

			$( document ).on( 'mouseup touchend', current, this.upAngle );
			$( document ).on( 'mousemove touchmove', function( ev ) {
				this.moveAngle( ev, current );
			}.bind( this ) );

			pageX = ( ( ev.type == 'touchstart' ) ? ev.originalEvent.changedTouches[ 0 ].pageX : ev.pageX );
			newVal = parseInt( 360 * ( pageX - current.left ) / this.colors.angle, 10 );
			this.colors.gradient.angle = newVal;

			this.change();
			return false;
		},
		upAngle: function() {
			$( document ).off( 'mouseup touchend', this.upAngle );
			$( document ).off( 'mousemove touchmove', this.moveAngle );

			return false;
		},
		moveAngle: function( ev, current ) {
			var pageX = ( ( ev.type == 'touchstart' ) ? ev.originalEvent.changedTouches[ 0 ].pageX : ev.pageX ),
				newVal;

			newVal = parseInt( 360 * ( pageX - current.left ) / this.colors.angle, 10 );

			if ( newVal < 0 ) {
				newVal = 0
			} else if ( newVal > 360 ) {
				newVal = 360;
			}

			newVal = this.round2precision( newVal, 5 );
			this.colors.gradient.angle = newVal;

			this.change();
			return false;

		},
		downSelector: function( ev, gradient ) {
			var current = {
				pos: gradient ? this.$gradientColorBox.offset() : this.$curentColorBox.offset(),
				number: gradient ? 'second' : 'first',
			}, pageX, pageY;

			$( document ).on( 'mouseup touchend', current, this.upSelector );
			$( document ).on( 'mousemove touchmove', function( ev ) {
				this.moveSelector( ev, current, gradient );
			}.bind( this ) );

			if ( ev.type == 'touchstart' ) {
				pageX = ev.originalEvent.changedTouches[ 0 ].pageX;
				pageY = ev.originalEvent.changedTouches[ 0 ].pageY;
			} else {
				pageX = ev.pageX;
				pageY = ev.pageY;
			}

			this.colors.color[ current.number ].hsba.b = parseInt( 100 * ( this.colors.height - ( pageY - current.pos.top ) ) / this.colors.height, 10 );
			this.colors.color[ current.number ].hsba.s = parseInt( 100 * ( pageX - current.pos.left ) / this.colors.height, 10 );

			this.change( gradient );
			return false;
		},
		upSelector: function( ev ) {
			$( document ).off( 'mouseup touchend', this.upSelector );
			$( document ).off( 'mousemove touchmove', this.moveSelector );

			return false;
		},
		moveSelector: function( ev, current, gradient ) {
			var pageX, pageY;
			if ( ev.type == 'touchmove' ) {
				pageX = ev.originalEvent.changedTouches[ 0 ].pageX;
				pageY = ev.originalEvent.changedTouches[ 0 ].pageY;
			} else {
				pageX = ev.pageX;
				pageY = ev.pageY;
			}

			this.colors.color[ current.number ].hsba.b = parseInt( 100 * ( this.colors.height - Math.max( 0, Math.min( this.colors.height, ( pageY - current.pos.top ) ) ) ) / this.colors.height, 10 );
			this.colors.color[ current.number ].hsba.s = parseInt( 100 * ( Math.max( 0, Math.min( this.colors.height, ( pageX - current.pos.left ) ) ) ) / this.colors.height, 10 );

			this.change( gradient );
			return false;
		},
		downHue: function( ev, gradient ) {
			var $target = $( ev.target ),
				current, pageY, newVal;

			if ( $target.hasClass( 'usof-colpick-hue-selector' ) ) {
				$target = $target.parent();
			}

			current = {
				top: $( $target ).offset().top,
				number: gradient ? 'second' : 'first'
			};

			$( document ).on( 'mouseup touchend', current, this.upHue );
			$( document ).on( 'mousemove touchmove', function( ev ) {
				this.moveHue( ev, current, gradient );
			}.bind( this ) );

			pageY = ( ( ev.type == 'touchstart' ) ? ev.originalEvent.changedTouches[ 0 ].pageY : ev.pageY );
			newVal = parseInt( 360 * ( this.colors.height - ( pageY - current.top ) ) / this.colors.height, 10 );

			this.colors.color[ current.number ].hsba.h = newVal;

			this.change( gradient, true );
			return false;
		},
		moveHue: function( ev, data, gradient ) {
			var pageY = ( ( ev.type == 'touchmove' ) ? ev.originalEvent.changedTouches[ 0 ].pageY : ev.pageY ),
				newVal = parseInt( 360 * ( this.colors.height - Math.max( 0, Math.min( this.colors.height, ( pageY - data.top ) ) ) ) / this.colors.height, 10 );

			this.colors.color[ data.number ].hsba.h = newVal;

			this.change( gradient, true );
			return false;
		},
		upHue: function( ev ) {
			// Detach event listeners
			$( document ).off( 'mouseup touchend', this.upHue );
			$( document ).off( 'mousemove touchmove', this.moveHue );

			return false;
		},
		setAngle: function() {
			if ( this.isEmptyObject( this.colors.gradient ) ) {
				this.colors.gradient = { angle: 90 };
				return false;
			}
			var angle = this.colors.gradient.angle ? parseInt( this.colors.gradient.angle, 10 ) : 0;
			if ( angle >= 0 && angle <= 360 ) {
				angle = angle * this.colors.angle / 360;
			} else {
				return false;
			}

			this.$angle.css( 'left', angle );
		},
		setCurrentColor: function( gradient ) {
			if ( gradient ) {
				this.$gradientColorBox.css( 'backgroundColor', this.hsbaToHex( {
					h: this.colors.color.second.hsba.h,
					s: 100,
					b: 100
				} ) );
			}
			this.$curentColorBox.css( 'backgroundColor', this.hsbaToHex( {
				h: this.colors.color.first.hsba.h,
				s: 100,
				b: 100
			} ) );
		},
		setAlpha: function( gradient ) {
			var rgba = this.colors.color.first.rgba,
				hsba = this.colors.color.first.hsba,
				rgbaG, alphaStyle, alphaStyleG;

			if ( hsba.a === undefined ) {
				hsba.a = 1.;
			}

			// Create Alpha style
			alphaStyle = 'background: linear-gradient(to bottom, rgb(' + rgba.r + ', ' + rgba.g + ', ' + rgba.b + ') 0%, ';
			alphaStyle += 'rgba(' + rgba.r + ', ' + rgba.g + ', ' + rgba.b + ', 0) 100%)';

			this.$alphaContainer.attr( 'style', alphaStyle );

			if ( gradient ) {
				rgbaG = this.colors.color.second.rgba;
				alphaStyleG = 'background: linear-gradient(to bottom, rgb(' + rgbaG.r + ', ' + rgbaG.g + ', ' + rgbaG.b + ') 0%, ';
				alphaStyleG += 'rgba(' + rgbaG.r + ', ' + rgbaG.g + ', ' + rgbaG.b + ', 0) 100%)';

				// Set Alpha background
				this.$gradientAlphaContainer.attr( 'style', alphaStyleG );
				// Set Alpha position
				this.$gradientAlphaArr.css( 'top', parseInt( this.colors.height * ( 1. - this.colors.color.second.hsba.a ) ) );
			}
			this.$alphaArr.css( 'top', parseInt( this.colors.height * ( 1. - this.colors.color.first.hsba.a ) ) );
		},
		downAlpha: function( ev, gradient ) {
			var $target = $( ev.target ),
				current, pageY, alpha;

			if ( $target.hasClass( 'usof-colpick-alpha-selector' ) ) {
				$target = $target.parent();
			}

			current = {
				top: $target.offset().top,
				number: gradient ? 'second' : 'first',
			};

			$( document ).on( 'mouseup touchend', current, this.upAlpha );
			$( document ).on( 'mousemove touchmove', function( ev ) {
				this.moveAlpha( ev, current, gradient );
			}.bind( this ) );

			pageY = ( ( ev.type == 'touchstart' ) ? ev.originalEvent.changedTouches[ 0 ].pageY : ev.pageY );
			alpha = ( this.colors.height - ( pageY - current.top ) ) / this.colors.height;

			this.colors.color[ current.number ].rgba.a = alpha;
			this.colors.color[ current.number ].hsba.a = alpha;

			this.change( gradient );
			return false;
		},
		moveAlpha: function( ev, current, gradient ) {
			var pageY = ( ( ev.type == 'touchmove' ) ? ev.originalEvent.changedTouches[ 0 ].pageY : ev.pageY ),
				alpha = ( this.colors.height - ( pageY - current.top ) ) / this.colors.height;


			if ( alpha > 1 ) {
				alpha = 1;
			} else if ( alpha < 0 ) {
				alpha = 0;
			}
			alpha = this.round2precision( alpha, 0.05 );
			alpha = parseFloat( alpha ).toFixed( 2 );
			this.colors.color[ current.number ].rgba.a = alpha;
			this.colors.color[ current.number ].hsba.a = alpha;
			this.change( gradient );
			return false;
		},
		upAlpha: function() {
			$( document ).off( 'mouseup touchend', this.upAlpha );
			$( document ).off( 'mousemove touchmove', this.moveAlpha );

			return false;
		},
		change: function( gradient, setColor ) {
			this.colors.color.first.rgba = this.hsbaToRgba( this.colors.color.first.hsba );
			this.colors.color.first.hex = this.hsbaToHex( this.colors.color.first.hsba );

			if ( ! this.isEmptyObject( this.colors.color.second ) ) {
				this.colors.color.second.rgba = this.hsbaToRgba( this.colors.color.second.hsba );
				this.colors.color.second.hex = this.hsbaToHex( this.colors.color.second.hsba );
			}
			this.setHuePosition( gradient );
			if ( setColor ) {
				this.setCurrentColor( gradient );
			}
			this.setAngle();
			this.setDotPosition( gradient );
			this.setAlpha( gradient );
			this.setColor();

			// Pass colors object to USOF via onChange callback
			this.colors.onChange.apply( this.colors, [this.colors] );
		},
		toggleGradient: function( state, setColor ) {
			var gradient = state == 'gradient' ? true : false;
			if ( state === 'solid' ) {
				this.$colpick.removeClass( 'type_gradient' );
				this.$colpick.addClass( 'type_solid' );
				this.colors.color.second = {};
				this.colors.gradient = {};
			} else if ( state === 'gradient' ) {
				this.$colpick.addClass( 'type_gradient' );
				this.$colpick.removeClass( 'type_solid' );

				if ( this.isEmptyObject( this.colors.color.second.hsba ) || this.isEmptyObject( this.colors.color.second.rgba ) ) {
					this.colors.color.second.hsba = this.colors.initialSecondColor.hsba;
					this.colors.color.second.rgba = this.colors.initialSecondColor.rgba;
				}

				if ( this.isEmptyObject( this.colors.gradient ) ) {
					this.colors.gradient = { angle: 90 };
				}
			}
			this.colors.state = state;
			this.$switchers.filter( '[value="' + this.colors.state + '"]' ).prop( 'checked', 'checked' );
			this.change( gradient, setColor );
		},
		hide: function() {
			var that = this;
			that.$colpick.css( 'display', 'none' ).removeClass( 'type_gradient' );
			clearTimeout( that.timeout );

			if ( that.colors.initialColor != that.$input.val() ) {
				that.$input.trigger( 'change' );
			}
			// Detach event listeners
			that.$hueContainer.off( 'mousedown touchstart' );
			that.$gradientHueContainer.off( 'mousedown touchstart' );
			that.$curentColorBox.off( 'mousedown touchstart' );
			that.$gradientColorBox.off( 'mousedown touchstart' );
			that.$alphaContainer.off( 'mousedown touchstart' );
			that.$gradientAlphaContainer.off( 'mousedown touchstart' );
			that.$angleContainer.off( 'mousedown touchstart' );
			that.$palette.off( 'mousedown' );
			that.$switchers.off( 'change' );
			that.$input.off( 'blur' ).trigger( 'blur' );

			// Delete cloned element
			that.$colpick.remove();
		},
		isValidColor: function( value ) {
			var gradient, valueG2, valueG1;
			// Check color and fill HSBa in colors object
			if ( typeof value == 'string' ) {
				if ( this.colorNameToHex( value ) ) {
					value = this.hexToHsba( this.colorNameToHex( value ) );
				} else if ( this.isGradient( value ) ) {
					gradient = this.gradientParser( value );
					value = this.hexToHsba( gradient.hex );
					this.colors.gradient = gradient;
				} else if ( value == 'transparent' ) {
					value = {
						h: 360,
						s: 0,
						b: 0,
						a: 0,
					}
				} else {
					value = this.hexToHsba( this.normalizeHex( value ) );
				}
			} else if ( value.r != undefined && value.g != undefined && value.b != undefined ) {
				value = this.rgbaToHsba( value );
			} else {
				return false;
			}


			if ( gradient ) {
				this.colors.state = 'gradient';

				valueG1 = gradient.colors[ 0 ];
				valueG1 = this.value2Hsba( valueG1 );
				this.colors.color.first.hsba = valueG1;
				this.colors.color.first.hex = this.hsbaToHex( valueG1 );
				this.colors.color.first.rgba = this.hsbaToRgba( valueG1 );

				this.colors.color.first.dot = {
					left: parseInt( this.colors.height * valueG1.s / 100, 10 ),
					top: parseInt( this.colors.height * ( 100 - valueG1.b ) / 100, 10 ),
				};

				valueG2 = gradient.colors[ 1 ];
				valueG2 = this.value2Hsba( valueG2 );
				this.colors.color.second.hsba = valueG2;
				this.colors.color.second.hex = this.hsbaToHex( valueG2 );
				this.colors.color.second.rgba = this.hsbaToRgba( valueG2 );

				this.colors.color.second.dot = {
					left: parseInt( this.colors.height * valueG2.s / 100, 10 ),
					top: parseInt( this.colors.height * ( 100 - valueG2.b ) / 100, 10 ),
				};

			} else {
				this.colors.state = 'solid';
				this.colors.color.first.hsba = value;
				this.colors.color.first.hex = this.hsbaToHex( value );
				this.colors.color.first.rgba = this.hsbaToRgba( value );

				// Detect dot coordinates
				this.colors.color.first.dot = {
					left: parseInt( this.colors.height * value.s / 100, 10 ),
					top: parseInt( this.colors.height * ( 100 - value.b ) / 100, 10 ),
				};
			}

			return true;
		},
		value2Hsba: function( value ) {
			if ( typeof value == 'string' ) {
				if ( this.colorNameToHex( value ) ) {
					value = this.hexToHsba( this.colorNameToHex( value ) );
				} else if ( value.indexOf( 'rgb' ) > 0 ) {
					value = this.rgbaToHsba( value );
				} else {
					value = this.hexToHsba( value );
				}
			} else if ( value.r != undefined && value.g != undefined && value.b != undefined ) {
				value = this.rgbaToHsba( value );
			}

			return value;
		},
		setHuePosition: function( gradient ) {
			// Set hue on init
			if ( gradient ) {
				this.$hueArr2.css( 'top', this.colors.height - this.colors.height * this.colors.color.second.hsba.h / 360 );
			}
			this.$hueArr.css( 'top', this.colors.height - this.colors.height * this.colors.color.first.hsba.h / 360 );
		},
		// Set the round selector position
		setDotPosition: function( gradient ) {
			if ( gradient ) {
				this.$gradientDot.css( {
					top: parseInt( this.colors.height * ( 100 - this.colors.color.second.hsba.b ) / 100, 10 ),
					left: parseInt( this.colors.height * this.colors.color.second.hsba.s / 100, 10 ),
				} );
			}

			this.$selector.css( {
				top: parseInt( this.colors.height * ( 100 - this.colors.color.first.hsba.b ) / 100, 10 ),
				left: parseInt( this.colors.height * this.colors.color.first.hsba.s / 100, 10 ),
			} );
		},
		hexToHsba: function( hex ) {
			return this.rgbaToHsba( this.hexToRgba( hex ) );
		},
		hexToRgba: function( hex ) {
			if ( hex.substr( 0, 5 ) == 'rgba(' ) {
				var parts = hex.substring( 5, hex.length - 1 ).split( ',' ).map( parseFloat );
				if ( parts.length == 4 ) {
					return { r: parts[ 0 ], g: parts[ 1 ], b: parts[ 2 ], a: parts[ 3 ] };
				}
			}
			if ( hex.length == 3 ) {
				hex = hex.charAt( 0 ) + hex.charAt( 0 ) + hex.charAt( 1 ) + hex.charAt( 0 ) + hex.charAt( 2 ) + hex.charAt( 2 );
			}
			hex = parseInt( ( ( hex.indexOf( '#' ) > - 1 ) ? hex.substring( 1 ) : hex ), 16 );

			return { r: hex >> 16, g: ( hex & 0x00FF00 ) >> 8, b: ( hex & 0x0000FF ), a: 1. };
		},
		rgbaToHsba: function( rgba ) {
			var hsba = { h: 0, s: 0, b: 0 },
				min = Math.min( rgba.r, rgba.g, rgba.b ),
				max = Math.max( rgba.r, rgba.g, rgba.b ),
				delta = max - min;

			hsba.b = max;
			hsba.s = max != 0 ? 255 * delta / max : 0;
			if ( hsba.s != 0 ) {
				if ( rgba.r == max ) {
					hsba.h = ( rgba.g - rgba.b ) / delta;
				} else if ( rgba.g == max ) {
					hsba.h = 2 + ( rgba.b - rgba.r ) / delta;
				} else {
					hsba.h = 4 + ( rgba.r - rgba.g ) / delta;
				}
			} else {
				hsba.h = - 1;
			}
			hsba.h *= 60;
			if ( hsba.h < 0 ) {
				hsba.h += 360;
			}
			hsba.s *= 100 / 255;
			hsba.b *= 100 / 255;
			hsba.a = rgba.a;

			return hsba;
		},
		hsbaToHex: function( hsba ) {
			return this.rgbaToHex( this.hsbaToRgba( hsba ) );
		},
		rgbaToHex: function( rgba ) {
			var hex = [
				rgba.r.toString( 16 ),
				rgba.g.toString( 16 ),
				rgba.b.toString( 16 )
			];
			$.each( hex, function( nr, val ) {
				if ( val.length == 1 ) {
					hex[ nr ] = '0' + val;
				}
			} );

			return '#' + hex.join( '' );
		},
		hsbaToRgba: function( hsba ) {
			var rgb = {},
				h = hsba.h,
				s = hsba.s * 255 / 100,
				v = hsba.b * 255 / 100;

			if ( s === 0 ) {
				rgb.r = rgb.g = rgb.b = v;
			} else {
				var t1 = v,
					t2 = ( 255 - s ) * v / 255,
					t3 = ( t1 - t2 ) * ( h % 60 ) / 60;
				if ( h === 360 ) {
					h = 0;
				}
				if ( h < 60 ) {
					rgb.r = t1;
					rgb.b = t2;
					rgb.g = t2 + t3
				} else if ( h < 120 ) {
					rgb.g = t1;
					rgb.b = t2;
					rgb.r = t1 - t3
				} else if ( h < 180 ) {
					rgb.g = t1;
					rgb.r = t2;
					rgb.b = t2 + t3
				} else if ( h < 240 ) {
					rgb.b = t1;
					rgb.r = t2;
					rgb.g = t1 - t3
				} else if ( h < 300 ) {
					rgb.b = t1;
					rgb.g = t2;
					rgb.r = t2 + t3
				} else if ( h < 360 ) {
					rgb.r = t1;
					rgb.g = t2;
					rgb.b = t1 - t3
				} else {
					rgb.r = 0;
					rgb.g = 0;
					rgb.b = 0
				}
			}
			return { r: Math.round( rgb.r ), g: Math.round( rgb.g ), b: Math.round( rgb.b ), a: hsba.a };
		},
		gradientParser: function( color ) {
			var m;
			if ( m = /^linear-gradient\(([\D\d]+)\);?$/.exec( color ) ) {
				var gradient = m[ 1 ].split( ',' ),
					directions = ['to', 'top', 'right', 'bottom', 'left', 'turn', 'deg'],
					index,
					colors = {
						colors: [],
						gradient: color,
					};

				// Find gradient direction
				for ( var d = 0; d < directions.length; d ++ ) {
					index = gradient[ 0 ].indexOf( directions[ d ] );
					if ( index !== - 1 ) {
						colors.direction = gradient[ 0 ];
						if ( directions[ d ] === 'deg' ) {
							colors.angle = parseInt( gradient[ 0 ], 10 );
						}
					}
				}

				// Find color values
				for ( var i = 0; i < gradient.length; i ++ ) {
					if ( gradient[ i ].indexOf( '%' ) !== - 1 ) {
						// Remove percents to work only with colors
						gradient[ i ] = gradient[ i ].replace( /^(.+)(\s[0-9]+%)/, '$1' );
					}
					gradient[ i ] = gradient[ i ].trim().toLowerCase();

					var hex = gradient[ i ].indexOf( '#' ),
						rgb = gradient[ i ].indexOf( 'rgb(' ),
						rgba = gradient[ i ].indexOf( 'rgba(' );

					// Look for hex values
					if ( hex !== - 1 ) {
						var normalizedHex = this.normalizeHex( gradient[ i ].replace( '#', '' ) );
						colors.colors.push( normalizedHex );
					} else if ( rgb !== - 1 ) {
						// Look for RGB
						var rgbColor = {};
						rgbColor.r = parseInt( gradient[ i ].replace( 'rgb(', '' ).trim() );
						rgbColor.g = parseInt( gradient[ i + 1 ].trim() );
						rgbColor.b = parseInt( gradient[ i + 2 ].replace( ')', '' ).trim() );
						colors.colors.push( rgbColor );
						// Skip the next values since they are already added
						i += 2;
					} else if ( rgba !== - 1 ) {
						// Look for RGBa
						var rgbaColor = {};
						rgbaColor.r = parseInt( gradient[ i ].replace( 'rgba(', '' ).trim() );
						rgbaColor.g = parseInt( gradient[ i + 1 ].trim() );
						rgbaColor.b = parseInt( gradient[ i + 2 ].trim() );
						rgbaColor.a = parseFloat( gradient[ i + 3 ].trim().replace( ')', '' ).trim() );
						colors.colors.push( rgbaColor );
						// Skip the next values since they are already added
						i += 3;
					} else if ( m = /^[a-z0-9]*$/.exec( gradient[ i ] ) ) {
						if ( gradient[ i ] !== colors.direction ) {
							colors.colors.push( gradient[ i ] );
						}
					}
				}

				if ( typeof colors.colors[ 0 ] == 'string' ) {
					if ( colors.colors[ 0 ].indexOf( '#' ) !== - 1 ) {
						colors.hex = this.normalizeHex( colors.colors[ 0 ].replace( '#', '' ) );
					} else if ( this.colorNameToHex( colors.colors[ 0 ] ) ) {
						colors.hex = this.colorNameToHex( colors.colors[ 0 ] );
					} else {
						// Maybe it is not a color at all, so make it white
						colors.hex = '#ffffff';
					}
				} else {
					// Can be returned as rgba string if rgba object is passed
					colors.hex = this.rgbaToHex( colors.colors[ 0 ] );
				}
				return colors;
			} else {
				return false;
			}
		},
		isGradient: function( color ) {
			var m;
			return !! ( m = /^linear-gradient\(.+\)$/.exec( color ) );
		},
		colorNameToHex: function( color ) {
			if ( ! color ) {
				return false;
			}
			var colors = {
				'aliceblue': '#f0f8ff',
				'antiquewhite': '#faebd7',
				'aqua': '#00ffff',
				'aquamarine': '#7fffd4',
				'azure': '#f0ffff',
				'beige': '#f5f5dc',
				'bisque': '#ffe4c4',
				'black': '#000000',
				'blanchedalmond': '#ffebcd',
				'blue': '#0000ff',
				'blueviolet': '#8a2be2',
				'brown': '#a52a2a',
				'burlywood': '#deb887',
				'cadetblue': '#5f9ea0',
				'chartreuse': '#7fff00',
				'chocolate': '#d2691e',
				'coral': '#ff7f50',
				'cornflowerblue': '#6495ed',
				'cornsilk': '#fff8dc',
				'crimson': '#dc143c',
				'cyan': '#00ffff',
				'darkblue': '#00008b',
				'darkcyan': '#008b8b',
				'darkgoldenrod': '#b8860b',
				'darkgray': '#a9a9a9',
				'darkgreen': '#006400',
				'darkkhaki': '#bdb76b',
				'darkmagenta': '#8b008b',
				'darkolivegreen': '#556b2f',
				'darkorange': '#ff8c00',
				'darkorchid': '#9932cc',
				'darkred': '#8b0000',
				'darksalmon': '#e9967a',
				'darkseagreen': '#8fbc8f',
				'darkslateblue': '#483d8b',
				'darkslategray': '#2f4f4f',
				'darkturquoise': '#00ced1',
				'darkviolet': '#9400d3',
				'deeppink': '#ff1493',
				'deepskyblue': '#00bfff',
				'dimgray': '#696969',
				'dodgerblue': '#1e90ff',
				'firebrick': '#b22222',
				'floralwhite': '#fffaf0',
				'forestgreen': '#228b22',
				'fuchsia': '#ff00ff',
				'gainsboro': '#dcdcdc',
				'ghostwhite': '#f8f8ff',
				'gold': '#ffd700',
				'goldenrod': '#daa520',
				'gray': '#808080',
				'green': '#008000',
				'greenyellow': '#adff2f',
				'honeydew': '#f0fff0',
				'hotpink': '#ff69b4',
				'indianred': '#cd5c5c',
				'indigo': '#4b0082',
				'ivory': '#fffff0',
				'khaki': '#f0e68c',
				'lavender': '#e6e6fa',
				'lavenderblush': '#fff0f5',
				'lawngreen': '#7cfc00',
				'lemonchiffon': '#fffacd',
				'lightblue': '#add8e6',
				'lightcoral': '#f08080',
				'lightcyan': '#e0ffff',
				'lightgoldenrodyellow': '#fafad2',
				'lightgrey': '#d3d3d3',
				'lightgreen': '#90ee90',
				'lightpink': '#ffb6c1',
				'lightsalmon': '#ffa07a',
				'lightseagreen': '#20b2aa',
				'lightskyblue': '#87cefa',
				'lightslategray': '#778899',
				'lightsteelblue': '#b0c4de',
				'lightyellow': '#ffffe0',
				'lime': '#00ff00',
				'limegreen': '#32cd32',
				'linen': '#faf0e6',
				'magenta': '#ff00ff',
				'maroon': '#800000',
				'mediumaquamarine': '#66cdaa',
				'mediumblue': '#0000cd',
				'mediumorchid': '#ba55d3',
				'mediumpurple': '#9370d8',
				'mediumseagreen': '#3cb371',
				'mediumslateblue': '#7b68ee',
				'mediumspringgreen': '#00fa9a',
				'mediumturquoise': '#48d1cc',
				'mediumvioletred': '#c71585',
				'midnightblue': '#191970',
				'mintcream': '#f5fffa',
				'mistyrose': '#ffe4e1',
				'moccasin': '#ffe4b5',
				'navajowhite': '#ffdead',
				'navy': '#000080',
				'oldlace': '#fdf5e6',
				'olive': '#808000',
				'olivedrab': '#6b8e23',
				'orange': '#ffa500',
				'orangered': '#ff4500',
				'orchid': '#da70d6',
				'palegoldenrod': '#eee8aa',
				'palegreen': '#98fb98',
				'paleturquoise': '#afeeee',
				'palevioletred': '#d87093',
				'papayawhip': '#ffefd5',
				'peachpuff': '#ffdab9',
				'peru': '#cd853f',
				'pink': '#ffc0cb',
				'plum': '#dda0dd',
				'powderblue': '#b0e0e6',
				'purple': '#800080',
				'rebeccapurple': '#663399',
				'red': '#ff0000',
				'rosybrown': '#bc8f8f',
				'royalblue': '#4169e1',
				'saddlebrown': '#8b4513',
				'salmon': '#fa8072',
				'sandybrown': '#f4a460',
				'seagreen': '#2e8b57',
				'seashell': '#fff5ee',
				'sienna': '#a0522d',
				'silver': '#c0c0c0',
				'skyblue': '#87ceeb',
				'slateblue': '#6a5acd',
				'slategray': '#708090',
				'snow': '#fffafa',
				'springgreen': '#00ff7f',
				'steelblue': '#4682b4',
				'tan': '#d2b48c',
				'teal': '#008080',
				'thistle': '#d8bfd8',
				'tomato': '#ff6347',
				'turquoise': '#40e0d0',
				'violet': '#ee82ee',
				'wheat': '#f5deb3',
				'white': '#ffffff',
				'whitesmoke': '#f5f5f5',
				'yellow': '#ffff00',
				'yellowgreen': '#9acd32'
			};

			if ( typeof colors[ color.toLowerCase() ] !== undefined ) {
				return colors[ color.toLowerCase() ];
			}

			return false;
		},
		normalizeHex: function( hex ) {
			hex = hex.replace( '#', '' );
			var hashString;
			if ( hex.length === 3 ) {
				hex = '#' + hex[ 0 ] + hex[ 0 ] + hex[ 1 ] + hex[ 1 ] + hex[ 2 ] + hex[ 2 ];
			} else if ( hex.length <= 6 ) {
				hashString = hex.split( '' );
				while ( hashString.length < 6 ) {
					hashString.unshift( '0' );
				}
				hex = '#' + hashString.join( '' );
			}

			return hex;
		},
		round2precision: function( x, precision ) {
			var y = + x + ( precision === undefined ? 0.5 : precision / 2 );
			return y - ( y % ( precision === undefined ? 1 : + precision ) );
		},
		isEmptyObject: function( obj ) {
			for ( var key in obj ) {
				if ( obj.hasOwnProperty( key ) ) {
					return false;
				}
			}
			return true;
		},
		// Write color to input
		setColor: function() {
			var color, firstColor, secondColor, rgbaS, rgbaF;

			if ( ! this.isEmptyObject( this.colors.color.second ) ) {
				// Create linear-gradient
				if ( this.colors.color.second.hsba.a < 1 ) {
					rgbaS = this.hsbaToRgba( this.colors.color.second.hsba );
					secondColor = 'rgba(' + rgbaS.r + ',' + rgbaS.g + ',' + rgbaS.b + ',' + rgbaS.a + ')';
				} else {
					secondColor = this.hsbaToHex( this.colors.color.second.hsba );
				}

				if ( this.colors.color.first.hsba.a < 1 ) {
					rgbaF = this.hsbaToRgba( this.colors.color.first.hsba );
					firstColor = 'rgba(' + rgbaF.r + ',' + rgbaF.g + ',' + rgbaF.b + ',' + rgbaF.a + ')';
				} else {
					firstColor = this.hsbaToHex( this.colors.color.first.hsba );
				}

				color = 'linear-gradient(' + this.colors.gradient.angle + 'deg,' + firstColor + ',' + secondColor + ')';
			} else {
				// Create single color
				if ( this.colors.color.first.hsba.a < 1 ) {
					rgbaF = this.hsbaToRgba( this.colors.color.first.hsba );
					color = 'rgba(' + rgbaF.r + ',' + rgbaF.g + ',' + rgbaF.b + ',' + rgbaF.a + ')';
				} else {
					color = this.hsbaToHex( this.colors.color.first.hsba );
				}
			}

			if ( this.colors.initialColor !== 'color' ) {
				// Set Input value
				this.$input.val( color );
				// Trigger change event to change preview according to input value
				this.$input.trigger( 'change' );
			}
		},
	};
	$.fn.extend( {
		usof_colpick: function( options ) {
			return US_Colpick.init( options );
		},
	} );
	$.extend( {
		usof_colpick: {
			isGradient: function( color ) {
				return US_Colpick.isGradient( color );
			},
			gradientParser: function( color ) {
				return US_Colpick.gradientParser( color );
			},
			hexToRgba: function( color ) {
				return US_Colpick.hexToRgba( color );
			},
			colorNameToHex: function( color ) {
				return US_Colpick.colorNameToHex( color );
			},
			normalizeHex: function( color ) {
				return US_Colpick.normalizeHex( color );
			},
			hide: function() {
				return US_Colpick.hide();
			},
		}
	} );
}( jQuery );
