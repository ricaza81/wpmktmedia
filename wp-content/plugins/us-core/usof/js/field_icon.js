/**
 * USOF Field: Icon
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'icon' ] = {

		init: function( options ) {
			this.$value = this.$row.find( '.us-icon-value' );
			this.$select = this.$row.find( '.us-icon-select' );
			this.$text = this.$row.find( '.us-icon-text' );
			this.$preview = this.$row.find( '.us-icon-preview > i' );
			this.$setLink = this.$row.find( '.us-icon-set-link' );

			this.$select
				.on( 'change', function() {
					var $selectedOption = this.$select.find( ":selected" );
					if ( $selectedOption.length ) {
						this.$setLink.attr( 'href', $selectedOption.data( 'info-url' ) );
					}
					this.setIconValue();
				}.bind( this ) );
			this.$text
				.on( 'change keyup', function( e ) {
					var val = this.$text.val();
					if ( val.toLowerCase().replace( /^\s+/g, '' ) !== val ) {
						this.$text.val( $.trim( val.toLowerCase() ) );
					}
					this.setIconValue( /* quiet */e.type === 'keyup' );
				}.bind( this ) );
			this.$row
				.on( 'click', '.usof-example', this.exampleClick.bind( this ) );
			this.$value
				.on( 'change', function() {
					this.trigger( 'change', this.getValue() );
				}.bind( this ) );
		},
		exampleClick: function( e ) {
			var $target = $( e.target ).closest( '.usof-example' ),
				example = $target.html();
			this.$text.val( example );
			this.setIconValue();
		},
		setIconValue: function( quiet ) {
			var icon_set = this.$select.val(),
				icon_name = $.trim( this.$text.val() ),
				icon_val = '';
			if ( icon_name != '' ) {
				if ( icon_set == 'material' ) {
					icon_name = icon_name.replace( / +/g, '_' );
				}
				icon_val = icon_set + '|' + icon_name;
			}
			this.renderPreview( icon_set, icon_name );
			this.$value
				.val( icon_val );
			if ( ! quiet ) {
				this.$value.trigger( 'change' );
			}
		},
		renderValue: function( value ) {
			var $selectedOption;
			value = value.trim().split( '|' );
			if ( value.length != 2 ) {
				$selectedOption = this.$select.find( 'option:first' );
				this.$text.val( '' );
			} else {
				value[ 0 ] = value[ 0 ].toLowerCase();
				$selectedOption = this.$select.find( 'option[value="' + value[ 0 ] + '"]' );
				this.$text.val( value[ 1 ] );
			}
			if ( $selectedOption.length ) {
				this.$select.find( 'option' ).prop( 'selected', false );
				$selectedOption.prop( 'selected', 'selected' );
			}

			this.renderPreview( value[ 0 ], value[ 1 ] );
		},
		renderPreview: function( icon_set, icon_name ) {
			if ( icon_name != '' ) {
				if ( icon_set == 'material' ) {
					this.$preview.attr( 'class', 'material-icons' ).html( icon_name );
				} else {
					if ( icon_name != undefined ) {
						icon_name = icon_name.replace( /fa-\dx/gi, ' ' );
					}
					this.$preview.attr( 'class', icon_set + ' fa-' + icon_name ).html( '' );
				}
			} else {
				this.$preview.attr( 'class', 'material-icons' ).html( '' );
			}
		},
		setValue: function( value, quiet ) {
			this.renderValue( value );
			this.parentSetValue( value, quiet );
		}
	};
}( jQuery );
