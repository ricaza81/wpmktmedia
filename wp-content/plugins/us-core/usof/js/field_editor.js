/**
 * USOF Field: Editor
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'editor' ] = {
		/**
		 * Initializes the object.
		 */
		init: function() {
			// Elements
			this.$container = $( '.usof-editor', this.$row );

			// Delete template
			this.$container.find( 'script.usof-editor-template' ).remove();

			// Get textarea
			this.$textarea = $( 'textarea', this.$container );

			// Variables
			this.originalEditorId = this.$textarea.data( 'editor-id' ) || 'usof_editor';
			this.originalEditorSettings = _window.tinyMCEPreInit.mceInit[ this.originalEditorId ] || {};
			this.editorSettings = this.$row.find( '.usof-editor-settings' )[ 0 ].onclick() || {};

			// Since there could be several instances of the field with same original ID, ...
			// ... adding random part to the ID
			this.editorId = this.originalEditorId + $usof.uniqid();

			this.$textarea.attr( 'id', this.editorId );

			// Event handlers
			this._events = {
				changeField: this._changeField.bind( this ),
				changeTinymceContent: this._changeTinymceContent.bind( this )
			};

			// Events
			this.$container
				.on( ( this.isUSBuilder() ? 'input' : 'change' ), 'textarea', this._events.changeField );

			this.initEditor();
		},

		/**
		 * Init WP Editor for USOF.
		 *
		 * @docs https://www.tiny.cloud/docs/api/
		 */
		initEditor: function() {
			if ( ! _window.wp || ! _window.wp.editor /*|| ! _window.tinymce */) return;
			// At initialization, add monitoring for content changes
			_window.tinymce.on( 'AddEditor', function( e ) {
				if ( e.editor.id !== this.editorId ) return;
				// Event name: `input` or `change`
				var eventName = this.isUSBuilder()
					? 'input NodeChange'
					: 'change NodeChange';
				e.editor.off( eventName ).on( eventName, this._events.changeTinymceContent );

			}.bind( this ), true );
			// Init editors
			var pid = setTimeout( function() {
				var editorSettings = {
						quicktags: true,
						tinymce: {},
						mediaButtons: ( this.editorSettings.media_buttons !== undefined ) ? this.editorSettings.media_buttons : true
					},
					qtSettings = {
						id: this.editorId,
						buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,more,close"
					},
					settingsFields = [
						'content_css',
						'toolbar1',
						'toolbar2',
						'toolbar3',
						'toolbar4',
						'theme',
						'skin',
						'language',
						'formats',
						'relative_urls',
						'remove_script_host',
						'convert_urls',
						'browser_spellcheck',
						'fix_list_elements',
						'entities',
						'entity_encoding',
						'keep_styles',
						'resize',
						'menubar',
						'branding',
						'preview_styles',
						'end_container_on_empty_block',
						'wpeditimage_html5_captions',
						'wp_lang_attr',
						'wp_keep_scroll_position',
						'wp_shortcut_labels',
						'plugins',
						'wpautop',
						'indent',
						'tabfocus_elements',
						'textcolor_map',
						'textcolor_rows',
					];

				settingsFields.forEach( function( setting, index ) {
					if ( this.originalEditorSettings[setting] !== undefined ) {
						editorSettings.tinymce[setting] = this.originalEditorSettings[setting];
					}
				}.bind( this ) );

				// TODO check if we shoud and can remove all other editors in builder mode
				_window.wp.editor.initialize( this.editorId, editorSettings );
				_window.quicktags( qtSettings );
				// Open tinymce by default
				this.switchEditors( 'tinymce' );
				clearTimeout( pid );
			}.bind( this ), 1 );
		},
		/**
		 * Switcher editors.
		 *
		 * @param {string} modeThe mode
		 */
		switchEditors: function( mode ) {
			mode = ( '' + mode ).toLowerCase();
			$( '#' + this.editorId + '-' + ( mode === 'tinymce' ? 'tmce' : 'html' ), this.$container )
				.trigger( 'click' );
		},

		/**
		 * Field change event.
		 *
		 * @private
		 * @param {Event} e
		 */
		_changeField: function( e ) {
			this.trigger( 'change', e.currentTarget.value );
		},
		/**
		 * Content change handler in TinyMCE.
		 *
		 * @private
		 * @param {Event} e TinyMCE Event
		 */
		_changeTinymceContent: function( e ) {
			var value = this.getValue().trim(),
				editorValue = '' + _window.tinymce.get( this.editorId ).getContent(),
				// Remove html from start and end content
				cleanValue = editorValue.replace( /(<([^>]+)>)/ig, '' );
			if ( editorValue == value || cleanValue == value ) {
				return;
			}
			this.$textarea.val( editorValue );
			this.trigger( 'change', editorValue );
		},

		/**
		 * Sets the value.
		 *
		 * @param {string} value The value
		 * @param {boolean} quiet The quiet
		 */
		setValue: function( value, quiet ) {
			// Set value to tinyMCE
			if ( !! _window.tinyMCE && !! _window.tinyMCE.get( this.editorId ) ) {
				_window.tinyMCE.get( this.editorId ).setContent( value );
			} else {
				this.$textarea.val( value );
			}
			if ( quiet ) {
				this.trigger( 'change', value );
			}
		},

		/**
		 * Gets the value.
		 *
		 * @return {string} The value.
		 */
		getValue: function() {
			var $input = ( this.$textarea ) ? this.$textarea : this.$input; // TODO: replace this.$textarea with this.$input after tests
			return $input.val() || '';
		}
	};
}( jQuery );
