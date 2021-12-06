/**
 * USOF Button Preview
 */
;! function( $ ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.ButtonPreview = function( container ) {
		this.init( container );
	};
	$usof.ButtonPreview.prototype = {
		init: function( container ) {

			// Elements
			this.$container = $( container );
			this.$btn = this.$container.find( '.usof-btn' );
			this.$groupParams = this.$container.closest( '.usof-form-group-item' );
			this.$style = $( 'style:first', this.$groupParams );

			// Variables
			this.groupParams = this.$groupParams.data( 'usofGroupParams' );
			this.dependsOn = [
				'h1_font_family',
				'h2_font_family',
				'h3_font_family',
				'h4_font_family',
				'h5_font_family',
				'h6_font_family',
				'body_font_family',
			];

			// Apply style to button preview on dependant fields change
			for ( var fieldId in $usof.instance.fields ) {
				if ( ! $usof.instance.fields.hasOwnProperty( fieldId ) ) {
					continue;
				}
				if ( $.inArray( $usof.instance.fields[ fieldId ].name, this.dependsOn ) === - 1 ) {
					continue;
				}
				$usof.instance.fields[ fieldId ]
					.on( 'change', this.applyStyle.bind( this ) );
			}
			// Apply style to button preview on button's group params change
			for ( var fieldId in this.groupParams.fields ) {
				if ( ! this.groupParams.fields.hasOwnProperty( fieldId ) ) {
					continue;
				}
				this.groupParams.fields[ fieldId ]
					.on( 'change', this.applyStyle.bind( this ) );
			}

			// Apply style to button preview on the init
			this.applyStyle();
		},
		/**
		 * Get the color value.
		 * @param {String} key
		 * @return string.
		 */
		_getColorValue: function( key ) {
			if (
				this.groupParams instanceof $usof.GroupParams
				&& this.groupParams.fields[ key ] !== undefined
				&& this.groupParams.fields[ key ].type === 'color'
				&& this.groupParams.fields[ key ].hasOwnProperty( 'getColor' )
			) {
				return this.groupParams.fields[ key ].getColor();
			}
			return '';
		},
		/**
		 * Apply styles for form elements a preview
		 */
		applyStyle: function() {
			// Add unique class
			var classRandomPart = $usof.uniqid(),
				className = '.usof-btn_' + classRandomPart,
				style = {
					default: '',
					hover: '',
				};
			this.$btn.usMod( 'usof-btn', classRandomPart );

			// Font family
			var buttonFont = this.groupParams.getValue( 'font' ),
				fontFamily;
			if ( $.inArray( buttonFont, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body'] ) !== - 1 ) {
				fontFamily = $usof.instance.getValue( buttonFont + '_font_family' ).split( '|' )[ 0 ];
			} else {
				fontFamily = buttonFont;
			}
			if ( fontFamily === 'none' ) {
				fontFamily = '';
			}
			if ( fontFamily ) {
				style.default += 'font-family: ' + fontFamily + ' !important;';
			}

			// Text style
			if ( this.groupParams.getValue( 'text_style' ).indexOf( 'italic' ) !== - 1 ) {
				style.default += 'font-style: italic !important;';
			} else {
				style.default += 'font-style: normal !important;';
			}

			if ( this.groupParams.getValue( 'text_style' ).indexOf( 'uppercase' ) !== - 1 ) {
				style.default += 'text-transform: uppercase !important;';
			} else {
				style.default += 'text-transform: none !important;';
			}

			// Font size
			// Used min() for correct appearance of the button with huge font-size value, e.g. 20em
			style.default += 'font-size: min(' + this.groupParams.getValue( 'font_size' ) + ', 50px) !important;';

			// Line height
			style.default += 'line-height:' + this.groupParams.getValue( 'line_height' ) + ' !important;';

			// Font weight
			style.default += 'font-weight:' + this.groupParams.getValue( 'font_weight' ) + ' !important;';

			// Height & Width
			style.default += 'padding:' + this.groupParams.getValue( 'height' ) + ' ' + this.groupParams.getValue( 'width' ) + ' !important;';

			// Corners radius
			style.default += 'border-radius:' + this.groupParams.getValue( 'border_radius' ) + ' !important;';

			// Letter spacing
			style.default += 'letter-spacing:' + this.groupParams.getValue( 'letter_spacing' ) + ' !important;';

			// Colors
			var colorBg = this._getColorValue( 'color_bg' ),
				colorBorder = this._getColorValue( 'color_border' ),
				colorBgHover = this._getColorValue( 'color_bg_hover' ),
				colorBorderHover = this._getColorValue( 'color_border_hover' ),
				colorShadow = this._getColorValue( 'color_shadow' ),
				colorShadowHover = this._getColorValue( 'color_shadow_hover' ),
				color;

			// Set default values if colors are empty
			if ( colorBg == '' ) {
				colorBg = 'transparent';
			}
			if ( colorBorder == '' ) {
				colorBorder = 'transparent';
			}
			if ( colorBgHover == '' ) {
				colorBgHover = 'transparent';
			}
			if ( colorBorderHover == '' ) {
				colorBorderHover = 'transparent';
			}
			if ( colorShadow == '' ) {
				colorShadow = 'rgba(0,0,0,0.2)';
			}
			if ( colorShadowHover == '' ) {
				colorShadowHover = 'rgba(0,0,0,0.2)';
			}

			style.default += 'background:' + colorBg + ' !important;';
			if ( colorBorder.indexOf( 'gradient' ) !== - 1 ) {
				style.default += 'border-image:' + colorBorder + ' 1 !important;';
			} else {
				style.default += 'border-color:' + colorBorder + ' !important;';
			}

			if ( this._getColorValue( 'color_text' ).indexOf( 'gradient' ) !== - 1 ) {
				color = $.usof_colpick.gradientParser( this._getColorValue( 'color_text' ) ).hex;
				style.default += 'color:' + color + ' !important;';
			} else {
				this.$btn.css( 'color', this._getColorValue( 'color_text' ) );
			}

			// Shadow
			style.default += 'box-shadow: 0 ' + parseFloat( this.groupParams.getValue( 'shadow' ) ) / 2 + 'em '
				+ this.groupParams.getValue( 'shadow' ) + ' ' + colorShadow + ' !important;';

			// Hover class
			this.$container.usMod( 'hov', this.groupParams.getValue( 'hover' ) );

			// Background;
			if ( this.groupParams.getValue( 'hover' ) == 'fade' ) {
				style.hover += 'background:' + colorBgHover + ' !important;';
			} else if ( colorBgHover == 'transparent' ) {
				style.hover += 'background:' + colorBgHover + ' !important;';
			}

			// Shadow
			style.hover += 'box-shadow: 0 ' + parseFloat( this.groupParams.getValue( 'shadow_hover' ) ) / 2
				+ 'em ' + this.groupParams.getValue( 'shadow_hover' ) + ' ' + colorShadowHover + ' !important;';

			// Border color
			if ( colorBorderHover.indexOf( 'gradient' ) !== - 1 ) {
				style.hover += 'border-image:' + colorBorderHover + ' 1 !important;';
			} else {
				style.hover += 'border-color:' + colorBorderHover + ' !important;';
			}

			// Text color
			var colorHover;
			if ( this._getColorValue( 'color_text_hover' ).indexOf( 'gradient' ) !== - 1 ) {
				colorHover = ( $.usof_colpick.gradientParser( this._getColorValue( 'color_text_hover' ) ) ).hex;
			} else {
				colorHover = this._getColorValue( 'color_text_hover' );
			}
			style.hover += 'color:' + colorHover + ' !important;';

			var compiledStyle = className + '{%s}'.replace( '%s', style.default );

			// Border Width
			compiledStyle += className + ':before {border-width:' + this.groupParams.getValue( 'border_width' ) + ' !important;}';
			compiledStyle += className + ':hover{%s}'.replace( '%s', style.hover );

			// Extra layer for "Slide" hover type OR for gradient backgrounds (cause gradients don't support transition)
			if (
				this.groupParams.getValue( 'hover' ) == 'slide'
				|| (
					colorBorder.indexOf( 'gradient' ) !== - 1
					|| colorBgHover.indexOf( 'gradient' ) !== - 1
				)
			) {
				compiledStyle += className + ':after {background:' + colorBgHover + '!important;}';
			}

			this.$style.text( compiledStyle );
		}
	};
}( jQuery );
