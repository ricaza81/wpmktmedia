/**
 * USOF Field: Css / Html
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'css' ] = $usof.field[ 'html' ] = {

		init: function() {
			// Variables
			this._params = {};
			this.editor = null;
			this.editorDoc = null;
			// Handlers
			this._events = {
				/**
				 * Editor change.
				 *
				 * @param object doc CodeMirror
				 */
				editorChange: function( doc ) {
					this.parentSetValue( this.getValue() );
				}
			};

			// Init CodeEditor
			if ( wp.hasOwnProperty( 'codeEditor' ) ) {
				this._params = this.$row.find( '.usof-form-row-control-params' )[ 0 ].onclick() || {};
				this.$row.find( '.usof-form-row-control-params' ).removeAttr( 'onclick' );
				if ( this._params.editor !== false ) {
					this.editor = wp.codeEditor.initialize( this.$input[ 0 ], this._params.editor || {} );
					this.editorDoc = this.editor.codemirror.getDoc();
					this.setValue( this.$input.val() );
				}
			} else {
				this.$input.on( 'keyup', function() {
					this.parentSetValue( this.getValue() );
					this.setValue( this.$input.val() );
				}.bind( this ) );
			}
		},
		setValue: function( value ) {
			if ( !! this._params && this._params.hasOwnProperty( 'encoded' ) && this._params.encoded ) {
				value = usof_rawurldecode( usof_base64_decode( value ) );
			}
			if ( this.editor !== undefined && wp.hasOwnProperty( 'codeEditor' ) ) {
				this.editorDoc.off( 'change', this._events.editorChange.bind( this ) );
				if ( !! this.pid ) {
					clearTimeout( this.pid );
				}
				this.pid = setTimeout( function() {
					this.editorDoc.cm.refresh();
					this.editorDoc.setValue( value );
					this.editorDoc.on( 'change', this._events.editorChange.bind( this ) );
					clearTimeout( this.pid );
				}.bind( this ), 1 );
			}
		},
		getValue: function() {
			var value = this.editor !== undefined && wp.hasOwnProperty( 'codeEditor' )
				? this.editorDoc.getValue()
				: this.$input.val();
			if ( this._params !== undefined && this._params.encoded ) {
				value = usof_base64_encode( usof_rawurlencode( value ) );
			}
			return value;
		}
	};
}( jQuery );
