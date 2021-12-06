/**
 * USOF Field: Design Options
 */
! function( $, undefined ) {
	var _window = window,
		_document = document,
		_undefined = undefined;

	if ( _window.$usof === _undefined ) {
		return;
	}

	$usof.field[ 'design_options' ] = {
		init: function() {
			// Variables
			this.defaultGroupValues = {}; // Default parameter values by groups
			this.defaultValues = {}; // Default inline values
			this.groupParams = {};
			this._lastSelectedResponsiveState = 'default';

			// Elements
			this.$document = $( _document );
			this.$container = this.$row.find( '.usof-design-options' );
			this.$input = $( 'textarea.usof_design_value', this.$container );

			// Elements import
			this.$import = $( '.usof-design-options-import', this.$container );
			this.$importHeader = $( '.usof-design-options-import-header', this.$import );
			this.$importFooter = $( '.usof-design-options-import-footer', this.$import );
			this.$importBtnCopy = $( 'button[data-action="copy"]', this.$importHeader );

			// Get responsive states
			this.states = this.$container[ 0 ].onclick() || ['default'];
			this.extStates = this.states.slice( 1 );
			this.$container.removeAttr( 'onclick' );

			// Fix live click for WPBakery Page Builder
			this.isWPBakery = this.$input.hasClass( 'wpb_vc_param_value' );

			// The value is a string otherwise it will be an object
			this.hasStringValue = ( this.isWPBakery || this.isUSBuilder() );

			if ( this.isWPBakery ) {
				this.$container
					.closest( '.edit_form_line' )
					.addClass( 'usof-not-live' );
			}

			/**
			 * Check for changes in the parameter group
			 * Note: The code is moved to a separate function since `debounced` must be initialized before calling.
			 *
			 * @private
			 * @type debounced
			 */
			this.__checkChangeValues = usof_debounce( this.checkChangeValues.bind( this ), 1/* 1ms */ );

			// Creates copy settings for different screen sizes
			if ( this.extStates.length ) {
				$( '[data-responsive-state-content="default"]', this.$container )
					.each( function( _, content ) {
						var $content = $( content );
						this.extStates.map( function( responsiveState ) {
							var $cloneContent = $content
								.clone()
								.attr( 'data-responsive-state-content', responsiveState )
								.addClass( 'hidden' );
							$content
								.after( $cloneContent );
						}.bind( this ) );
					}.bind( this ) );
			}

			// State grouping
			this.states.map( function( responsiveState ) {
				this.groupParams[ responsiveState ] = new $usof.GroupParams(
					$( '[data-responsive-state-content="' + responsiveState + '"]', this.$container )
				);
			}.bind( this ) );

			$.each( this.groupParams, function( responsiveState, groupParams ) {
				// Group start parameters
				$.each( groupParams.fields, function( fieldName, field ) {
					var $group = field.$row.closest( '[data-accordion-content]' ),
						value = field.getValue();
					if ( $group.length ) {
						var groupKey = $group.data( 'accordion-content' );

						// Save groups
						if ( ! this.defaultGroupValues.hasOwnProperty( groupKey ) ) {
							this.defaultGroupValues[ groupKey ] = {};
						}
						if ( ! this.defaultGroupValues[ groupKey ].hasOwnProperty( responsiveState ) ) {
							this.defaultGroupValues[ groupKey ][ responsiveState ] = {};
						}
						this.defaultGroupValues[ groupKey ][ responsiveState ][ fieldName ] = value;

						// Save default value
						if ( ! this.defaultValues.hasOwnProperty( responsiveState ) ) {
							this.defaultValues[ responsiveState ] = {};
						}
						this.defaultValues[ responsiveState ][ fieldName ] = value;

						// Add devive type to group and field
						$group.data( 'responsive-state', responsiveState )
						field.responsiveState = responsiveState;
					}
				}.bind( this ) );
				// Initializing control over parameter associations
				$.each( groupParams.fields, function( _, field ) {
					var $row = field.$row;
					if ( $row.attr( 'onclick' ) ) {
						field._data = $row[ 0 ].onclick() || '';
						$row.removeAttr( 'onclick' );
						if ( field._data.hasOwnProperty( 'relations' ) ) {
							$row.append( '<i class="fas fa-unlink"></i>' )
								.on( 'click', 'i.fas', this._events.watchAttrLink.bind( this, field ) );
						}
					}
					// Watch events
					field
						.trigger( 'beforeShow' )
						.on( 'change', usof_debounce( this._events.changeValue.bind( this ), 1 ) );
				}.bind( this ) );
			}.bind( this ) );

			// Initializing parameters for shortcodes
			var pid = setTimeout( function() {
				if ( ! this.inited ) {
					this.setValue( this.$input.val() );
					// Check for changes in the parameter group
					this.checkChangeValues.call( this );
				}
				// Controlling the display of the button for copying
				this.$importBtnCopy.prop( 'disabled', ! this.getValue() );
				clearTimeout( pid );
			}.bind( this ), 1 );

			// Hide/Show states panel
			this.$container
				.find( '.us-builder-states' )
				.toggleClass( 'hidden', ! this.extStates.length );

			// Watch events
			this.$container
				.on( 'click', '[data-accordion-id]', this._events.toggleAccordion.bind( this ) )
				.on( 'click', '.usof-design-options-reset', this._events.resetValues.bind( this ) )
				.on( 'click', '.usof-design-options-responsive', this._events.toggleResponsive.bind( this ) )
				.on( 'click', '[data-responsive-state]', this._events.changeResponsiveStates.bind( this ) );

			// Forwarding events through document
			this.$document
				.on( 'usb.setResponsiveState', this._events.usbSetResponsiveState.bind( this ) );

			// Watch import events
			this.$import
				.on( 'click', 'button[data-action]', this._events.importActions.bind( this ) );
		},

		// Event handlers
		_events: {
			/**
			 * Collects parameters into a string when changing any parameter.
			 *
			 * @param {$usof.field} field USOF Field
			 */
			changeValue: function( field ) {
				var resultValue = {},
					valueStateChanged = {},
					enabledResponsives = {};
				$.each( this.groupParams, function( responsiveState, groupParams ) {
					// Definition for whom responsive is enabled
					if ( 'default' === responsiveState ) {
						for ( var k in groupParams.fields ) {
							enabledResponsives[ k ] = !! $( groupParams.fields[ k ].$row )
								.closest( '[data-accordion-content]' )
								.prev( '[data-accordion-id].responsive' )
								.length;
						}
					}
					// Get group values
					var groupValues = groupParams.getValues();
					// Check the parameters, if the value is not default then add the setting to the result value
					$.each( groupValues, function( param, value ) {
						var defaultValue = this.defaultValues[ responsiveState ][ param ];
						// For the `position`, `text-align`, `text-align` and `animation-name` property, we additionally check for changes to
						// the value, this is necessary because the default value can be overriding the previous value
						if ( [ 'position', 'border-style', 'text-align', 'animation-name' ].indexOf( param ) > -1 ) {
							// Determine if there are changes in responsive states
							valueStateChanged[ param ] = ( enabledResponsives[ param ] )
								? ( value !== defaultValue || valueStateChanged[ param ] )
								: valueStateChanged[ param ] || _undefined;

							if ( valueStateChanged[ param ] ) {
								// The value is set here intentionally, which cannot be,
								// this is necessary to bypass the check for a default value
								defaultValue = null;
							}
						}
						if ( value !== defaultValue ) {
							if ( ! resultValue.hasOwnProperty( responsiveState ) ) {
								resultValue[ responsiveState ] = {};
							}
							// Image URL support
							if ( param === 'background-image' && /http/.test( value ) ) {
								value = 'url(' + value + ')';
							}
							resultValue[ responsiveState ][ param ] = value;
						}

					}.bind( this ) );
				}.bind( this ) );

				resultValue = ( JSON.stringify( resultValue ) !== '{}' )
					// Due to the nature of WPBakery Page Builder, we convert special characters
					// standard escape function
					? usof_rawurlencode( JSON.stringify( resultValue ) )
					: '';

				// Set result value
				this.$input.val( resultValue );

				// Only when the result changes, then fire the change event.
				if ( ! this._lastResultValue || this._lastResultValue !== resultValue ) {
					this._lastResultValue = resultValue;
					this.trigger( 'change', resultValue );
				}

				// Check for changes in the parameter group
				this.__checkChangeValues();

				// Controlling the display of the button for copying
				this.$importBtnCopy.prop( 'disabled', ! resultValue );

				this // Send a signal about a field changed ( this event is used in USBuilder )
					.trigger( 'changeDesignField', field );
			},

			/**
			 * Resets all group settings to default.
			 *
			 * @event handler
			 * @param {Event} e The Event interface represents an event which takes place in the DOM.
			 */
			resetValues: function( e ) {
				e.stopPropagation();
				var $target = $( e.currentTarget ),
					$groupHeader = $target.closest( '[data-accordion-id]' ),
					groupName = $groupHeader.data( 'accordion-id' );

				// Hide responsive options
				if ( $groupHeader.hasClass( 'responsive' ) ) {
					this._events.toggleResponsive.call( this, e );
				}
				if ( this.defaultGroupValues.hasOwnProperty( groupName ) ) {
					$.each( this.defaultGroupValues[ groupName ], function( responsiveState, defaultValues ) {
						var groupParams = this.groupParams[ responsiveState ];
						/**
						 * Note: Setting the default values is done by combining from the
						 * current ones because of the way usof works.
						 */
						groupParams.setValues( $.extend( groupParams.getValues(), defaultValues ) );
						// Didable fields link
						$.each( defaultValues, function( groupParams, name ) {
							var fields = groupParams.fields;
							if (
								fields.hasOwnProperty( name )
								&& fields[ name ].hasOwnProperty( '_data' )
								&& fields[ name ]._data.hasOwnProperty( 'relations' )
							) {
								var $link = $( 'i.fas', groupParams.$fields[ name ] );
								if ( $link.length && $link.hasClass( 'fa-link' ) ) {
									$link.trigger( 'click' );
								}
							}
						}.bind( this, groupParams ) );
					}.bind( this ) );
				}
				var pid = setTimeout( function() {
					$groupHeader.removeClass( 'changed' );
					clearTimeout( pid );
				}, 1000 * 0.5 );
			},

			/**
			 * Enable or disable duplication.
			 *
			 * @param {$usof.field} field USOF Field
			 * @param {Event} e The Event interface represents an event which takes place in the DOM.
			 * @param {boolean|undefined} state
			 */
			watchAttrLink: function( field, e, state ) {
				var $target = $( e.currentTarget ),
					isUnlink = $target.hasClass( 'fa-unlink' ),
					relations = [];
				if ( state !== _undefined ) {
					isUnlink = state;
				}
				if ( field.hasOwnProperty( '_data' ) && field.hasOwnProperty( 'responsiveState' ) ) {
					$.each( this.groupParams[ field.responsiveState ].fields, function( _name, item ) {
						if ( $.inArray( item.name, field._data.relations || [] ) !== - 1 ) {
							relations.push( item );
						}
					} );
				}
				$target
					.toggleClass( 'fa-link', isUnlink )
					.toggleClass( 'fa-unlink', ! isUnlink );
				if ( relations.length ) {
					relations.map( function( item ) {
						item.$input.prop( 'disabled', isUnlink );
					} );
					field.watchValue = isUnlink;
					if ( isUnlink ) {
						field.$input
							.focus()
							.on( 'input', this._events.changeRelationsValue.bind( this, relations ) )
							.trigger( 'input' );
					} else {
						field.$input.off( 'input' );
					}
				}
			},

			/**
			 * Duplicates settings to related fields.
			 *
			 * @param {{}} fields
			 * @param {Event} e The Event interface represents an event which takes place in the DOM.
			 */
			changeRelationsValue: function( fields, e ) {
				var $this = $( e.currentTarget ),
					value = $this.val();
				fields.map( function( item ) {
					if ( item instanceof $usof.field ) {
						item.setValue( value );
					}
				} );
			},

			/**
			 * Accordion Switch.
			 *
			 * @event handler
			 * @param {Event} e The Event interface represents an event which takes place in the DOM.
			 */
			toggleAccordion: function( e ) {
				var $target = $( e.currentTarget ),
					$content = $( '[data-accordion-content="' + $target.data( 'accordion-id' ) + '"]' );

				if ( $target.hasClass( 'active' ) ) {
					$target.removeClass( 'active' );
					$content.removeClass( 'active' );
				} else {
					$target.siblings().removeClass( 'active' );
					$content.siblings().removeClass( 'active' );
					$target.addClass( 'active' );
					$content.addClass( 'active' );
				}
			},

			/**
			 * ON/OFF Responsive options.
			 *
			 * @event handler
			 * @param {Event} e The Event interface represents an event which takes place in the DOM.
			 */
			toggleResponsive: function( e ) {
				e.preventDefault();
				e.stopPropagation();

				var $target = $( e.currentTarget ),
					$header = $target.closest( '[data-accordion-id]' ),
					groupKey = $header.data( 'accordion-id' ),
					isEnabled = $header.hasClass( 'responsive' ),
					// Determine the first responsive settings or not
					isFirstResponsive = ! isEnabled
						? ! $( '.usof-design-options-header.responsive:first', this.$container ).length
						: false;

				$header.toggleClass( 'responsive', ! isEnabled );

				if ( ! isEnabled ) {
					// If the first setting will send a change event
					if ( isFirstResponsive ) {
						this.trigger( 'changeResponsiveState', this._lastSelectedResponsiveState );
					}
					this.switchResponsiveState( this._lastSelectedResponsiveState );
				} else {
					this.switchResponsiveState( 'default', /* hidden */true );
				}

				if ( this.defaultGroupValues.hasOwnProperty( groupKey ) ) {
					this.extStates.map( function( responsiveState ) {
						// Reset values for a group whose responsive support is enabled
						var values = $.extend( {}, this.defaultGroupValues[ groupKey ][ responsiveState ] || {} );
						if ( ! isEnabled ) {
							// Set default values for current responsiveState
							$.each( values, function( prop ) {
								if ( this.groupParams[ 'default' ].fields.hasOwnProperty( prop ) ) {
									values[ prop ] = this.groupParams[ 'default' ].fields[ prop ].getValue();
								}
							}.bind( this ) );
						}
						if (
							this.groupParams.hasOwnProperty( responsiveState )
							&& this.groupParams[ responsiveState ] instanceof $usof.GroupParams
						) {
							// Get current values to support already set values
							values = $.extend( this.groupParams[ responsiveState ].getValues(), values );
							this.groupParams[ responsiveState ].setValues( values, /* quiet mode */ true );
						}
						// Checking and duplicating wiretap related fields
						if ( ! isEnabled && this.groupParams.hasOwnProperty( responsiveState ) ) {
							$.each( this.groupParams[ 'default' ].fields, function( _, field ) {
								if ( field.hasOwnProperty( 'watchValue' ) ) {
									$( '.fas', this.groupParams[ responsiveState ].fields[ field.name ].$row )
										.trigger( 'click', field.watchValue );
								}
							}.bind( this ) );
						}
					}.bind( this ) );
				}
			},

			/**
			 * The action handler for import.
			 *
			 * @event handler
			 * @param {Event} e The Event interface represents an event which takes place in the DOM.
			 */
			importActions: function( e ) {
				var $target = $( e.target ),
					action = '' + $target.data( 'action' );
				// Copy design options to clipboard
				if ( action == 'copy' ) {
					this._copyValueToClipboard();

					// Show the field for entering design options
				} else if ( action == 'paste' ) {
					this.$import.addClass( 'show_input' );
					this.$input[0].select();

					// Close field without changes
				} else if ( action == 'cancel' ) {
					this.$import
						.removeClass( 'show_input show_novalid' );

					// Apply design options
				} else if ( action == 'apply' ) {
					var value = this.$input.val();
					if ( ! value ) return;
					// Is valid value
					var isValidValue = this._isValidValue( value );
					this.$import
						.toggleClass( 'show_novalid', ! isValidValue );
					if ( ! isValidValue ) {
						this.$input.val( '' );
						return;
					}
					// Reset values to default
					$.each( this.groupParams, function( responsiveState, groupParams ) {
						groupParams.setValues( this.defaultValues[ responsiveState ] || {}, true );
					}.bind( this ));
					// Set new values
					this.setValue( value );
					// Reset css classes and atts
					this.$import.removeClass( 'show_input' );
					this.$importBtnCopy.prop( 'disabled', ! value );
				}
			},

			/**
			 * Choosing a group of settings for devices.
			 *
			 * @event handler
			 * @param {Event} e The Event interface represents an event which takes place in the DOM.
			 */
			changeResponsiveStates: function( e ) {
				var responsiveState = $( e.currentTarget ).data( 'responsive-state' );
				this.switchResponsiveState( responsiveState );
				this.trigger( 'changeResponsiveState', responsiveState );
				this._lastSelectedResponsiveState = responsiveState;
			},

			/**
			 * This is the install handler `responsiveState` of USBuilder
			 *
			 * @param {Event} _
			 * @param {string} responsiveState The device type
			 */
			usbSetResponsiveState: function( _, responsiveState ) {
				this._lastSelectedResponsiveState = responsiveState || 'default';
				this.switchResponsiveState( this._lastSelectedResponsiveState );
			}
		},

		/**
		 * Switch device type.
		 *
		 * @param {string} responsiveState
		 * @param {boolean} hidden
		 */
		switchResponsiveState:function( responsiveState, hidden ) {
			if ( ! responsiveState ) return;
			// Switch between hidden/shown targets
			var hasResponsiveClass = ! hidden
				? '.responsive'
				: ':not(.responsive)'

			// Get active responsive blocks
			var $target = $( '[data-accordion-id]' + hasResponsiveClass, this.$container )
					.next( '[data-accordion-content]' )
					.find( '[data-responsive-state="'+ responsiveState +'"]' );
			// Remove active class from siblings
			$target
				.siblings()
				.removeClass( 'active' );
			// Show the required content by device type
			$target
				.addClass( 'active' )
				.closest( '.usof-design-options-content' )
				.find( '> [data-responsive-state-content]' )
				.addClass( 'hidden' )
				.filter( '[data-responsive-state-content="' + responsiveState + '"]' )
				.removeClass( 'hidden' );
		},

		/**
		 * Determines whether the specified value is valid value.
		 *
		 * @private
		 * @param {string} value The value
		 * @return {boolean} True if the specified value is valid value, False otherwise.
		 */
		_isValidValue: function( value ) {
			try {
				value = JSON.parse( usof_rawurldecode( ( '' + value ).trim() ) || '{}' );
				for ( var i in this.states ) {
					var state = this.states[ i ];
					if ( state && !! value[ state ] ) {
						return true;
					}
				}
			} catch ( err ) {}
			return false;
		},

		/**
		 * Check for changes in the parameter group
		 */
		checkChangeValues: function() {
			// Get current values
			var currentGroupValues = {};
			$.each( this.groupParams, function( responsiveState, groupParams ) {
				$.each( groupParams.fields, function( _, field ) {
					var groupName = field.$row
						.closest( '[data-accordion-content]' )
						.data( 'accordion-content' );
					if ( ! currentGroupValues.hasOwnProperty( groupName ) ) {
						currentGroupValues[ groupName ] = {};
					}
					if ( ! currentGroupValues[ groupName ].hasOwnProperty( responsiveState ) ) {
						currentGroupValues[ groupName ][ responsiveState ] = {};
					}
					currentGroupValues[ groupName ][ responsiveState ][ field.name ] = field.getValue();
				} );
			} );
			$.each( this.defaultGroupValues, function( groupName, devices ) {
				var change = false;
				$.each( devices, function( responsiveState, values ) {
					if ( ! currentGroupValues.hasOwnProperty( groupName ) || ! currentGroupValues[ groupName ].hasOwnProperty( responsiveState ) ) {
						return;
					}
					change = ( change || JSON.stringify( values ) !== JSON.stringify( currentGroupValues[ groupName ][ responsiveState ] ) );
				}.bind( this ) );
				this.$container
					.find( '[data-accordion-id=' + groupName + ']' )
					.toggleClass( 'changed', change );
			}.bind( this ) );
		},

		/**
		 * Get the value
		 *
		 * @return {string}
		 */
		getValue: function() {
			var value = $.trim( this.$input.val() );
			if ( ! this.hasStringValue && value && typeof value === 'string' ) {
				value = JSON.parse( usof_rawurldecode( value ) || '{}' );
			}
			return value;
		},

		/**
		 * Set the value.
		 *
		 * @param {string} value
		 * @param {boolean} quiet The quiet
		 */
		setValue: function( value, quiet ) {
			// Get saved parameter values
			var savedValues = {};
			if ( typeof value === 'string' ) {
				try {
					savedValues = JSON.parse( usof_rawurldecode( value ) || '{}' );
				} catch ( err ) {
					console.error( value, err );
					savedValues = {};
				}
			} else if ( $.isPlainObject( value ) ) {
				savedValues = value;
			}
			var pid = setTimeout( function() {
				// Set values and check link
				$.each( this.groupParams, function( responsiveState, groupParams ) {
					// Reset values
					if ( ! this.hasStringValue ) {
						groupParams.setValues( this.defaultValues[ responsiveState ] || {}, true );
					}
					var values = savedValues[ responsiveState ] || {};
						propName = 'background-image';
					// Image URL support
					if ( values.hasOwnProperty( propName ) && /url\(/.test( values[ propName ] || '' ) ) {
						values[ propName ] = values[ propName ]
							.replace( /\s?url\("?(.*?)"?\)/gi, '$1' );
					}
					// Border style support.
					for ( var k in values ) {
						if ( ! /border-(\w+)-style/.test( k ) ) continue;
						values[ 'border-style' ] = values[ k ];
						delete values[ k ];
					}
					// Set values
					groupParams.setValues( values, true );
					// Check relations link
					$.each( groupParams.fields, function( _, field ) {
						if ( field.hasOwnProperty( '_data' ) && field._data.hasOwnProperty( 'relations' ) ) {
							var $row = field.$row,
								value = $.trim( field.getValue() ),
								isLink = [];
							// Matching all related parameters, and if necessary enable communication.
							( field._data.relations || [] ).map( function( name ) {
								if ( value && this.groupParams[ field.responsiveState ].fields.hasOwnProperty( name ) ) {
									isLink.push( value === $.trim( this.groupParams[ field.responsiveState ].fields[ name ].getValue() ) );
								}
							}.bind( this ) );
							if ( isLink.length ) {
								isLink = isLink.filter( function( v ) {
									return v == true
								} );
								if ( isLink.length === 3 ) {
									var pid = setTimeout( function() {
										$row.find( 'i.fas' ).trigger( 'click' );
										clearTimeout( pid );
									}, 1 );
								}
							}
						}
					}.bind( this ) );
				}.bind( this ) );

				// Check options for devices
				var responsiveGroups = {};
				this.extStates.map( function( responsiveState ) {
					var values = savedValues[ responsiveState ] || {};
					$.each( this.defaultGroupValues, function( groupKey, devices ) {
						var isEnable = false;
						$.each( devices[ responsiveState ], function( prop ) {
							if ( ! responsiveGroups[ groupKey ] ) {
								responsiveGroups[ groupKey ] = values.hasOwnProperty( prop );
							}
						} );
					}.bind( this ) );
				}.bind( this ) );

				$.each( responsiveGroups, function( groupKey, isEnable ) {
					$( '[data-accordion-id="' + groupKey + '"]', this.$container )
						.toggleClass( 'responsive', isEnable );
				}.bind( this ) );

				// Check for changes in the parameter group
				this.checkChangeValues.call( this );

				// Default tab selection
				this.switchResponsiveState( 'default', /* hidden */true );

				clearTimeout( pid );
			}.bind( this ), 1 );

			// Set value
			if ( value ) {
				value = this.hasStringValue ? value : usof_rawurlencode( JSON.stringify( value ) );
			}
			this.$input.val( '' + value );

			if ( ! quiet ) {
				this.trigger( 'change', [ value ] );
			}

			// Hide all sections of the accordion
			if ( ! this.$input.hasClass( 'wpb_vc_param_value' ) ) {
				this.$container.find( '> div' ).removeClass( 'active' );
			}
		},

		/**
		 * Сopy value to clipboard.
		 *
		 * @private
		 */
		_copyValueToClipboard: function() {
			if ( ! this.$input.val() ) return;
			this.$input[ 0 ].select();
			_document.execCommand( 'copy' );
			// The unselect data
			if ( _window.getSelection ) {
				_window.getSelection().removeAllRanges();
			} else if ( _document.selection ) {
				_document.selection.empty();
			}
		},

		/**
		 * Force value for WPBakery
		 */
		forceWPBValue: function() {
			if ( this.hasStringValue ) {
				this.setValue( this.getValue() );
			}
		}
	};
}( jQuery );
