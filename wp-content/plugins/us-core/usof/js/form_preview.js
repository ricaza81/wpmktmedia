/**
 * USOF Form Fields Preview
 */
;! function( $ ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.FormElmsPreview = function( container ) {
		this.init( container );
	};
	$usof.FormElmsPreview.prototype = {
		init: function( container ) {
			// Elements
			this.$container = $( container );
			this.$group = this.$container.closest( '.usof-form-group-item' );
			this.$style = $( 'style:first', this.$group );
			this.$elms = $( '> *', this.$container );
			// Variables
			this.group = this.$group.data( 'usofGroupParams' ) || {};
			this.dependsOn = [
				'h1_font_family',
				'h2_font_family',
				'h3_font_family',
				'h4_font_family',
				'h5_font_family',
				'h6_font_family',
				'body_font_family',
			];
			// Watches all parameters
			if ( this.group instanceof $usof.GroupParams && this.group.hasOwnProperty( 'fields' ) ) {
				for ( var i in this.group.fields ) {
					this.group.fields[ i ].on( 'change', this.applyStyle.bind( this ) );
				}
			}
			// The apply styles
			this.applyStyle();
		},
		/**
		 * Get the color value.
		 * @param {String} key
		 * @return string.
		 */
		_getColorValue: function( key ) {
			if (
				this.group instanceof $usof.GroupParams
				&& this.group.fields[ key ] !== undefined
				&& this.group.fields[ key ].type === 'color'
				&& this.group.fields[ key ].hasOwnProperty( 'getColor' )
			) {
				return this.group.fields[ key ].getColor();
			}
			return '';
		},
		/**
		 * Apply styles for form elements a preview
		 */
		applyStyle: function() {
			var className = '.usof-input-preview-elm',
				style = {
					default: '',
					focus: ''
				};

			// Font family
			var buttonFont = this.group.getValue( 'font' ),
				fontFamily;
			if ( $.inArray( buttonFont, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body'] ) !== - 1 ) {
				fontFamily = $usof.instance.getValue( buttonFont + '_font_family' ).split( '|' )[ 0 ];
			} else {
				fontFamily = buttonFont;
			}
			if ( fontFamily == 'none' ) {
				fontFamily = '';
			}
			style.default += 'font-family: ' + fontFamily + '!important;';

			// Font Size
			style.default += 'font-size:' + this.group.getValue( 'font_size' ) + '!important;';

			// Font Weight
			style.default += 'font-weight:' + this.group.getValue( 'font_weight' ) + '!important;';

			// Letter spacing
			style.default += 'letter-spacing:' + this.group.getValue( 'letter_spacing' ) + '!important;';

			// Height
			style.default += 'line-height:' + this.group.getValue( 'height' ) + '!important;';

			// Padding
			style.default += 'padding: 0 ' + this.group.getValue( 'padding' ) + '!important;';

			// Border radius
			style.default += 'border-radius:' + this.group.getValue( 'border_radius' ) + '!important;';

			// Border Width
			style.default += 'border-width:' + this.group.getValue( 'border_width' ) + '!important;';

			// Colors
			if ( this._getColorValue( 'color_bg' ) ) {
				style.default += 'background:' + this._getColorValue( 'color_bg' ) + '!important;';
			}
			if ( this._getColorValue( 'color_border' ) ) {
				style.default += 'border-color:' + this._getColorValue( 'color_border' ) + '!important;';
			}
			if ( this._getColorValue( 'color_text' ) ) {
				style.default += 'color:' + this._getColorValue( 'color_text' ) + '!important;';
			}

			// Colors on focus
			if ( this._getColorValue( 'color_bg_focus' ) ) {
				style.focus += 'background:' + this._getColorValue( 'color_bg_focus' ) + '!important;';
			}
			if ( this._getColorValue( 'color_border_focus' ) ) {
				style.focus += 'border-color:' + this._getColorValue( 'color_border_focus' ) + '!important;';
			}
			if ( this._getColorValue( 'color_text_focus' ) ) {
				style.focus += 'color:' + this._getColorValue( 'color_text_focus' ) + '!important;';
			}

			// Shadow
			if ( this._getColorValue( 'color_shadow' ) != '' ) {
				style.default += 'box-shadow:'
					+ this.group.getValue( 'shadow_offset_h' ) + ' '
					+ this.group.getValue( 'shadow_offset_v' ) + ' '
					+ this.group.getValue( 'shadow_blur' ) + ' '
					+ this.group.getValue( 'shadow_spread' ) + ' '
					+ this._getColorValue( 'color_shadow' ) + ' ';
				if ( $.inArray( '1', this.group.getValue( 'shadow_inset' ) ) !== - 1 ) {
					style.default += 'inset';
				}
				style.default += '!important;';
			}

			// Shadow on focus
			if ( this._getColorValue( 'color_shadow_focus' ) != '' || this._getColorValue( 'color_shadow' ) != '' ) {
				style.focus += 'box-shadow:'
					+ this.group.getValue( 'shadow_focus_offset_h' ) + ' '
					+ this.group.getValue( 'shadow_focus_offset_v' ) + ' '
					+ this.group.getValue( 'shadow_focus_blur' ) + ' '
					+ this.group.getValue( 'shadow_focus_spread' ) + ' ';

				if ( this._getColorValue( 'color_shadow_focus' ) != '' ) {
					style.focus += this._getColorValue( 'color_shadow_focus' ) + ' ';
				} else {
					style.focus += this._getColorValue( 'color_shadow' ) + ' ';
				}
				if ( $.inArray( '1', this.group.getValue( 'shadow_focus_inset' ) ) !== - 1 ) {
					style.focus += 'inset';
				}
				style.focus += '!important;';
			}

			var compiledStyle = className + '{%s}'.replace( '%s', style.default );
			compiledStyle += className + ':focus{%s}'.replace( '%s', style.focus );

			// Add styles for dropdown icon separately
			compiledStyle += '.usof-input-preview-select:after {';
			compiledStyle += 'font-size:' + this.group.getValue( 'font_size' ) + ';';
			compiledStyle += 'margin: 0 ' + this.group.getValue( 'padding' ) + ';';
			compiledStyle += 'color:' + this._getColorValue( 'color_text' ) + ';';
			compiledStyle += '}';

			this.$style.text( compiledStyle );
		},
	};
}( jQuery );
