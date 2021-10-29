/**
 * Available spaces
 * _window.$usb - USBuilderPreview class instance
 * _window.parent.$usb - USBuilder class instance
 * _window.parent.$usbcore - Mini library of various methods
 * _window.parent.$usbdata - Data for import into the USBuilder
 * _window.$usbdata - Data for import into the USBuilderPreview
 * _window.$us - UpSolution Theme Core JavaScript Code
 *
 * Note: Double underscore `__funcname` is introduced for functions that are created through `$usbcore.debounce(...)`.
 */
! function( $, undefined ) {

	// Private variables that are used only in the context of this function, it is necessary to optimize the code.
	var _window = window,
		_document = document,
		_undefined = undefined;

	// Check for is set objects
	_window.$usbdata = _window.$usbdata || {};

	var
		// Get parent window
		parent = _window.parent || {},
		// Get $usbcore helpers
		$usbcore = parent.$usbcore || {};

	// If there is no parent window object, we will complete the execute script
	if ( ! parent.$usb ) {
		return;
	}

	/**
	 * @class USBuilderPreview
	 */
	var USBuilderPreview = function() {

		// Variables
		this.fps = 1000 / 200;
		this.mainContainer = parent.$usb.mainContainer || 'container';
		this._preloaders = {}; // All active preloaders
		this._highlights = {}; // All highlights

		/**
		 * Settings for the css compiler
		 * @var {{}}
		 */
		this._designOptions = $.extend(
			{
				// Prefix for class when using design options
				customPrefix: 'us_custom_', // Default prefix
				// Breakpoints of responsive states
				breakpoints: {},
				// List of fonts that have keys, css variables, etc.
				fontVars: {},
				// A general list of settings for option colors, since the definition of a
				// prefixes occurs on a value (not a css variable or key)
				colorVars: {}
			},
			// Get settings from the main object
			this.config( 'designOptions', {} )
		);

		// Elements
		this.$document = $( _document );
		this.$body = $( 'body', this.document );
		this.highlight = $( '.usb-builder-hover', this.$body )[0];
		this.elmMainContainer = this.getElmNode( this.mainContainer );

		// Bondable events.
		this._events = {
			// Track DragAndDrop events
			clickedControlsHoverPanel: this._clickedControlsHoverPanel.bind( this ),
			endDrag: this._endDrag.bind( this ),
			maybeDrag: this._maybeDrag.bind( this ),
			maybeStartDrag: this._maybeStartDrag.bind( this ),

			// Other handlers
			DOMContentLoaded: this._DOMContentLoaded.bind( this ),
			elmAnimationEnd: this._elmAnimationEnd.bind( this ),
			elmAnimationStart: this._elmAnimationStart.bind( this ),
			elmDelete: this._elmDelete.bind( this ),
			elmDuplicate: this._elmDuplicate.bind( this ),
			elmMove: this._elmMove.bind( this ),
			elmSelected: this._elmSelected.bind( this ),
			keydown: parent.$usb._events.keydown, // Extends from the main object
			linkClickHandler: this._linkClickHandler.bind( this ),
			stop: this._stop.bind( this ),

			// Alias for calling on events
			autoSetHighlightsPosition: $usbcore.debounce( this.setHighlightsPosition.bind( this ), this.fps )
		};

		// This event is needed to get various data from the iframe
		_window.onmessage = $usbcore._onMessage.bind( this );

		// When leaving the window with the cursor, hide all the highlights
		_window.onmouseout = $usbcore.debounce( function( e ) {
			e = e || _window.event;
			var elm = e.relatedTarget || e.toElement;
			if ( ! elm || elm.nodeName.toLowerCase() === 'html' ) {
				this._mouseLeavesIframe.call( this, e );
			}
		}.bind( this ), 1 );

		// Highlight position updates on window resize or page scrolling
		_window.onresize = this._events.autoSetHighlightsPosition;
		_document.onscroll = this._events.autoSetHighlightsPosition;

		// Disable Drag and Drop on body
		this.$body.attr( 'draggable', 'false' );

		// Events
		this.$document
			// The event fires when the initial HTML document has been completely loaded and parsed,
			// without waiting for stylesheets, images, and subframes to finish loading.
			.ready( this._events.DOMContentLoaded )
			// Capturing keyboard shortcuts
			.on( 'keydown', this._events.keydown )
			// Disabled dragstart from default
			.on( 'dragstart', function() { return false } )
			// Highlight actions
			.on( 'mousedown', '.usb-builder-hover-panel', this._events.clickedControlsHoverPanel )
			.on( 'mouseup', '.ui-icon_duplicate', this._events.elmDuplicate )
			.on( 'mouseup', '.ui-icon_delete', this._events.elmDelete )
			// Track Drag and Drop events
			.on( 'mousedown', this._events.maybeStartDrag )
			.on( 'mousemove', this._events.maybeDrag )
			.on( 'mouseup', this._events.endDrag )
			// Other events
			.on( 'mouseup', '[data-usbid]', $usbcore.debounce( this._events.elmSelected, 1 ) )
			.on( 'mousemove', $usbcore.debounce( this._events.elmMove, this.fps ) )
			.on( 'mouseleave', $usbcore.debounce( this._events.elmLeave, this.fps ) )
			// Handlers for css animation in elements
			.on( 'animationstart', '[data-usbid]', $usbcore.debounce( this._events.elmAnimationStart, 1 ) )
			.on( 'animationend', '[data-usbid]', $usbcore.debounce( this._events.elmAnimationEnd, 1 ) )
			// When the cursor is within `header` or `footer` then hide all highlights
			.on( 'mouseenter', '.l-header, .l-footer', $usbcore.debounce( this.hideHighlight.bind( this ), 100 ) )
			// Watching content changes (via us scripts)
			.on( 'contentChange', '.l-canvas:first', this._events.autoSetHighlightsPosition );

		this.$body
			// Handler for all link clicks
			.on( 'click', 'a', this._events.linkClickHandler );

		/**
		 * Private events
		 * The events that can come from the main collector window
		 */
		for ( var handler in this._$events ) {
			if ( $.isFunction( this._$events[ handler ] ) ) {
				this.on( handler, this._$events[ handler ].bind( this ) );
			}
		}
	};

	/**
	 * @type {USBuilderPreview}
	 */
	var $usbPreviewPrototype = USBuilderPreview.prototype;

	/**
	 * Transports for send messages between windows or objects
	 */
	$.extend( $usbPreviewPrototype, $usbcore.mixins.events || {}, {
		/**
		 * Send a message to the parent window
		 *
		 * @param {string} eventType A string containing event type
		 * @param {[]} extraParams Additional parameters to pass along to the event handler
		 * @chainable
		 */
		postMessage: function( eventType, extraParams ) {
			parent.postMessage( JSON.stringify( [ /* Namespace */'usb', eventType, extraParams ] ) );
		}
	} );

	/**
	 * Extends the functionality by importing methods from main prototype builder.js
	 * Methods that need to extend the current prototype must be specified explicitly in the list
	 * TODO: Optimize and get rid of this extension.
	 */
	[
		'canBeChildOf',
		'config',
		'escapeRegExp',
		'flushTemp',
		'getAttachmentUrl',
		'getCurrentPreviewOffset',
		'getElmChildren',
		'getElmId',
		'getElmName',
		'getElmParentId',
		'getElmShortcode',
		'getElmTitle',
		'getElmType',
		'getElmValue',
		'getInsertPosition',
		'getNewElmId',
		'getSpareElmId',
		'getTemp',
		'hasSameTypeParent',
		'hideTransit',
		'isElmContainer',
		'isElmTab',
		'isElmTTA',
		'isEmptyTempContent',
		'isFirefox',
		'isHidePanel',
		'isMainContainer',
		'isMode',
		'isParentDragging',
		'isRootElmContainer',
		'isSecondElmContainer',
		'isSetTransit',
		'isValidId',
		'moveElm',
		'restoreTempContent',
		'saveTempContent',
		'setMode',
		'setTemp',
		'setTransitPosition',
		'showTransit'
	].map( function( method ) {
		if ( !! parent.$usb[ method ] && ! this[ method ] ) {
			this[ method ] = parent.$usb[ method ].bind( parent.$usb ) || $.noop;
		}
	}.bind( $usbPreviewPrototype ) );

	/**
	 * Functionality for implementing Drag And Drop
	 * All the necessary methods that are somehow involved in this approach
	 */
	$.extend( $usbPreviewPrototype, {
		// The number of pixels when dragging after which the movement will be initialized
		_dragStartDistance: parent.$usb._dragStartDistance || 5,

		/**
		 * Get all data from the event that is needed for Drag and Drop
		 *
		 * @private
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 * @return {{}} The event data
		 */
		_extractEventData: function( e ) {
			return {
				clientX: e.clientX,
				clientY: e.clientY,
				pageX: e.pageX,
				pageY: e.pageY,
				target: e.target
			};
		},

		/**
		 * Event handler for clicking on any element in the highlight controls on HoverPanel
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_clickedControlsHoverPanel: function( e ) {
			var $highlight = $( e.currentTarget ).closest( '.usb-builder-hover' ),
				elmId = $highlight.data( 'elmid' );
			if ( ! elmId  ) {
				return;
			}
			// The we activate observations to start moving the element
			$( this.getElmNode( elmId ) )
				.trigger( 'mousedown', [ e.pageX, e.pageY ] );
		},

		/**
		 * Handler for checking movement
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_maybeStartDrag: function( e, pageX, pageY ) {
			e.stopPropagation();
			// If there drag in the parent window, then we will exit this method
			if ( this.isParentDragging() ) {
				return;
			}
			// Defining the element to move
			var target = ( ( e.target.className || '' ).indexOf( 'usb-builder-hover-panel-title' ) !== -1 && this.hoveredElm )
				? this.hoveredElm
				: this._getNearestElm( e.target );
			if ( ! target ) {
				return;
			}
			// Clearing all asset and temporary data to move
			this.clearDragAssets();
			// Set temp data
			this.setTemp( 'iframeDrag', {
				isParentTab: false, // Moving in sections in the context of Tabs/Tour
				isDragging: false,
				startDrag: true,
				startX: e.pageX || pageX || 0,
				startY: e.pageY || pageY || 0,
				target: target,
			} );
		},

		/**
		 * Position selection handler for move element
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_maybeDrag: function( e ) {
			var target = e.target,
				currentPreviewOffset = this.getCurrentPreviewOffset(),
				// Get offset for transit
				transit = {
					pageX: Math.ceil( currentPreviewOffset.x + e.pageX - _window.scrollX ), // X axis
					pageY: Math.ceil( currentPreviewOffset.y + e.pageY - _window.scrollY ) // Y axis
				};

			if ( ! this.isFirefox() && this.isParentDragging() ) {
				// Determination of the place where the element can fall
				this._maybeDrop( this._extractEventData( e ) );
				// Set position for transit when adding a new element
				this.setTransitPosition( transit.pageX, transit.pageY );
				return;
			}

			var temp = this.getTemp( 'iframeDrag' );
			if ( $.isEmptyObject( temp ) || ! temp.startDrag || ! temp.target ) {
				return;
			}

			// If the cursor leaves the window, then we end dragging
			if ( target && ( target instanceof HTMLDocument || target.tagName.toLowerCase() === 'html' ) ) {
				this.restoreTempContent(); // Restore the current content that contains the floating element
				this.clearDragAssets();
				this.setMode( 'editor' );
				return;
			}

			// Get ffsets from origin along axis X and Y
			var diffX = Math.abs( temp.startX - e.pageX ),
				diffY = Math.abs( temp.startY - e.pageY );

			// The check the distance of the germinated mouse and if it is more than
			// the specified one, then activate all the necessary methods
			if ( diffX >= this._dragStartDistance || diffY >= this._dragStartDistance ) {
				var // Get target id
					targetId = this.getElmId( temp.target );

				if ( this.isMode( 'editor' ) ) {
					// Selecting mode of moving elements
					this.setMode( 'drag:move' );
					// Add a flag that dragging is activated
					temp.isDragging = true;
					// Moving in sections in the context of tabs
					temp.isParentTab = !! this.isElmTab( this.getElmParentId( targetId ) );
					// Show the transit, default pageX and pageY do not set for correct offset
					this.showTransit( /*type*/this.getElmType( targetId ), /*pageX*/0, /*pageY*/0 );
					// Add helpers classes for visual control
					$usbcore
						.$addClass( temp.target, 'usb_transit' ) // TODO: Move to `this.showTransit()`
						.$addClass( _document.body, 'usb_draging' );
					// Hide tab button
					if ( temp.isParentTab ) {
						$usbcore
							.$addClass( this._getSectionButtonById( targetId ), 'usb_transit' );
					}
					// Hide highlight for editable element
					if ( this.hasEditableHighlight( targetId ) ) {
						temp.editable = true;
						this.hideEditableHighlight( targetId );
					}
				}

				if ( ! this.isMode( 'drag:move' ) ) {
					return;
				}

				// Saving content to a temporary variable and removing the float
				if ( this.isEmptyTempContent() ) {
					// The save content to temp
					this.saveTempContent();
					// Temporarily remove the element to be moved from the content
					parent.$usb.pageData.content = ( '' + parent.$usb.pageData.content )
						.replace( this.getElmShortcode( this.getElmId( temp.target ) ), '' );
				}

				// Determination of the place where the element can fall
				this._maybeDrop( this._extractEventData( e ) );

				// Set position for transit when move element
				this.setTransitPosition( transit.pageX, transit.pageY );
			}
		},

		/**
		 *
		 * Determining the location where the element will be drag
		 * This method is called from both the current window and the parent.window
		 *
		 * @private
		 * @param {{}} data The data from event
		 *
		 * TODO: Develop a linear system of checks that will be taken out of the method!
		 */
		_maybeDrop: function( data ) {
			if (
				! data
				|| ! data.target
				|| ! this.isMode( 'drag:add', 'drag:move' )
			) {
				return;
			}

			var // Get current temp
				temp = this.getTemp( 'iframeDrag' ),
				// This is the ID of the new or moved element
				currentId = this.isMode( 'drag:add' )
					? this.getNewElmId()
					: this.getElmId( temp.target ),
				// Determine if the type or id is in the vc_tta_accordion, vc_tta_tab, vc_tta_tour group or vc_tta_section.
				isCurrentTTA = this.isElmTTA( currentId ),
				// Save a real target since the target can be replaced (Note: Replacement
				// occurs when working with tabs and programmatic element borders).
				realTarget = data.target;

			// Redirects from tab buttons to sections
			if ( temp.isParentTab && ( data.target.className || '' ).indexOf( 'w-tabs-item' ) > -1 ) {
				// The find main element of a button
				if ( $usbcore.$hasClass( data.target.parentNode, 'w-tabs-item' ) ) {
					realTarget = data.target.parentNode;
				}
				// Find the element of the section related to the button.
				var _sectionId = $usbcore.$attr( realTarget, 'data-related-to' );
				if ( _sectionId ) {
					data.target = this.getElmNode( _sectionId );
				}
			}

			var // The found target where the item will be added. All non-root containers must have the target
				// of the root container for example, for vc_column this is vc_row etc.
				targetContainer = this.isSecondElmContainer( currentId )
					? this._getNearestRootElmContainer( data.target )
					: this._getNearestElmContainer( data.target ),
				// Real targets all contain the data of the elements over which the cursor passes
				target = this._getNearestElm( data.target ) || this.elmMainContainer,
				targetId = this.getElmId( target );

			// If the moved element is open for editing then hide the highlight
			if ( !! temp.editable ) {
				this.hideEditableHighlight();
			}

			// Check the target, if it is missing, add the main container
			targetContainer = targetContainer || this.elmMainContainer;

			var // This is the target id that does not change and contains the container
				targetContainerId = this.getElmId( targetContainer ),
				// Get the type of the current id
				currentType = this.getElmType( currentId );

			// If the cursor is on the border, then reload the target to add before or after
			var  borderUnderMouse = this._getBorderUnderMouse( targetContainer, data.clientX, data.clientY );
			if ( borderUnderMouse !== this._DIRECTION.UNKNOWN ) {
				if (
					! this.isElmContainer( currentId )
					|| isCurrentTTA
					|| (
						currentType === 'vc_row_inner'
						&& this.getElmType( targetContainerId ) === 'vc_column_inner'
					)
				) {
					var parentId = this.getElmParentId( targetContainerId ) || this.mainContainer;
					// Reload real target
					if ( borderUnderMouse === this._DIRECTION.TOP && ! this.isSecondElmContainer( parentId ) ) {
						targetId = parentId;
						target = this.getElmNode( parentId );
					}
					// Get next parentId
					if ( ! this.isSecondElmContainer( parentId ) ) {
						parentId = this.getElmParentId( parentId );
					}
					// Reload target
					targetContainerId = parentId || this.mainContainer;
					targetContainer = this.getElmNode( parentId );
				}
			}

			var // The check if the moved element is a tab, accordion, tour or vc_column(_inner), if so, then enable strict mode
				strictMode = ( isCurrentTTA || currentId.indexOf( 'vc_column' ) === 0 );

			// If element and target are `vc_row` then change target to main container
			if ( currentType === 'vc_row' && this.getElmType( targetContainerId ) === 'vc_row' ) {
				targetContainerId = this.mainContainer;
			}

			// Exception when moving vc_column* within one vc_row*, this fixes a blink bug for the css grid
			if ( this.isSecondElmContainer( currentId ) && targetContainerId === targetId ) return;

			// Determine if it is a descendant of itself.
			if ( this.hasSameTypeParent( currentId, targetContainerId ) ) return;

			// Check if the element can be a child of the hover element
			if ( ! this.canBeChildOf( currentId, targetContainerId, strictMode ) ) return;

			// Determine which axis to determine the direction
			var isMouseDirectionX = isCurrentTTA
				// For `vc_tta_tabs` calculate along the X axis for other TTA of along the Y axis.
				? this.getElmType( temp.parentId ) == 'vc_tta_tabs'
				: this.isSecondElmContainer( currentId );

			var // Get the direction of the mouse movement relative to the target along Y or X axis
				// Note: IMPORTANT: To determine the direction, you must use the real node `data.target`,
				// not `target`, as it can be replaced when working from containers.
				mouseDirection = isMouseDirectionX
					? this._getMouseDirectionX( realTarget, data.clientX, data.clientY )  // X axis
					: this._getMouseDirectionY( realTarget, data.clientX, data.clientY ), // Y axis
				// Get a list of all children of the container where mouse movement occurs
				children = this.getElmChildren( targetContainerId ),
				// This is the child ID to search for in the list of children
				targetChildId = ( this.isSecondElmContainer( currentId ) && ! this.isElmContainer( targetId ) )
					? this.getElmId( this._getNearestSecondElmContainer( data.target ) )
					: targetId,
				// This is the index or sequential number of adding an element to the list of nodes
				currentIndex;

			// Get the `currentIndex` to add an element to the document
			if ( ( currentIndex = children.indexOf( targetChildId ) ) === -1 ) {
				currentIndex = 0;
			}
			if ( mouseDirection === this._DIRECTION.BOTTOM || mouseDirection === this._DIRECTION.RIGHT ) {
				currentIndex++;
			}
			if ( ! currentIndex || currentIndex < 0 ) {
				currentIndex = 0;
			}

			// Checking and searching for elements that are near the cursor
			if (
				! isCurrentTTA // Note: Ignore TTA elements as they are hidden.
				&& currentId.indexOf( 'vc_column' ) === -1 // Skip calculate for `vc_column` and `vc_column_inner`.
				&& (
					this.isSecondElmContainer( targetId )
					|| ( // In the mode of adding new element to the main container, allow adding to the end of the list
						this.isMode( 'drag:add' )
						&& this.isMainContainer( targetContainerId )
					)
				)
			) {
				// Get the size of an children elements and its position relative to the viewport
				for ( var elmIndex in children ) {
					var elmId = children[ elmIndex ],
						elm = this.getElmNode( elmId );
					if ( ! elm ) {
						continue;
					}
					var elmRect = $usbcore.$rect( elm ),
						elmX = Math.floor( Math.abs( elmRect.x ) + _window.scrollX ),
						elmY = Math.floor( Math.abs( elmRect.y ) + _window.scrollY );
					// The comparisons where the cursor is in relation to the outer borders of elements
					if ( data.pageY > elmY && data.pageX > elmX ) {
						currentIndex = $usbcore.parseInt( elmIndex ) + 1;
					}
				}
			}

			// Save the last found container
			if ( temp.lastFoundContainer !== targetContainer ) {
				$usbcore
					.$removeClass( temp.lastFoundContainer, 'usb_dropcontainer' )
					.$addClass( targetContainer, 'usb_dropcontainer' );
				temp.lastFoundContainer = targetContainer;
			}

			// Save insert data to a temp variable
			temp.parentId = targetContainerId;
			temp.currentId = currentId;
			temp.currentIndex = currentIndex;

			// Saving data for Firefox since endDrag in the frame window does not work
			if ( this.isFirefox() ) {
				var parentTemp = this.getTemp( 'drag' );
				parentTemp.parentId = temp.parentId;
				parentTemp.currentId = currentId;
				parentTemp.currentIndex = currentIndex;
			}

			// Get insert position
			var insert = this.getInsertPosition( temp.parentId, currentIndex );

			// Additional check for `insert` changes to reduce the number of document calls
			if ( JSON.stringify( insert ) === JSON.stringify( temp.lastInsert ) ) {
				return;
			}
			temp.lastInsert = insert;

			// Create new dropplace element
			$usbcore.$remove( temp.place ); // Remove old dropplace element
			temp.place = _document.createElement( 'div' );
			temp.place.className = 'usb_dropplace';

			// This is where additional settings are added for the vertical line when moving containers
			var isHorizontalWrapper = ( this.getElmName( temp.parentId ) === 'hwrapper' );
			if ( this.isRootElmContainer( temp.parentId ) || isHorizontalWrapper || temp.isParentTab ) {
				temp.place.className += '_container'; // `{usb_dropplace}_container`
				if ( isHorizontalWrapper ) {
					// Add height to the wrapper as elements inside it are not blocks
					temp.place.style.height = $usbcore.$rect( targetContainer ).height + 'px';
				}
			}

			// This is an explicit transfer of the node for the target (Needed to display position by section Tabs/Tour buttons)
			// Note: This is a forced solution since the buttons outside the section are not a shortcode.
			if ( temp.isParentTab ) {
				insert.parent = realTarget;
				if ( $usbcore.isNode( insert.parent ) && [ 'prepend', 'append' ].indexOf( insert.position ) > -1 ) {
					insert.parent = insert.parent.parentNode || insert.parent;
				}
			}

			// Adding a temporary container to the place where the item will be added
			this.trigger( 'insertElm', [ insert.parent, insert.position, temp.place ] );
		},

		/**
		 * End a drag
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_endDrag: function( e ) {
			/**
			 * Kill current event
			 * Note: For FF, we ignore stop since the transmitted object is data, not an event object,
			 * all due to the peculiarities of the FF from work the iframe
			 */
			if ( ! this.isFirefox() ) {
				this._events.stop( e );
			}

			// Get current temp object
			var temp = this.getTemp( 'iframeDrag' );
			// Duplicate the signal in the parent window for correct completion
			if ( this.isParentDragging() ) {
				// Move the data to add a new element
				$.extend( this.getTemp( 'drag' ), {
					parentId: temp.parentId,
					currentId: temp.currentId,
					currentIndex: temp.currentIndex
				} );
				this.postMessage( 'endDrag' );
				return;
			}
			// Reset all data
			this.setMode( 'editor' );

			// If the move is not activated or not started then clear all assets
			if ( ! temp.startDrag || ! temp.isDragging ) {
				// Clearing all asset and temporary data to move
				this.clearDragAssets();
				// Selecting an element for editing
				if (
					! temp.isDragging
					&& ( ( e.target.className || '' ).toLowerCase() ).indexOf( 'usb-builder-hover-panel-name' ) > -1
				) {
					// Hide highlight for editable element
					this.hideEditableHighlight();
					// Running a trigger to initialize shortcode edit mode
					this.hoveredElm = temp.target;
					$( this.hoveredElm ).trigger( 'mouseup' );
				}
				// End execution
				return;
			}

			// Restore the current content that contains the floating element
			this.restoreTempContent();

			// Move the element to a new position
			if ( !! temp.parentId && !! temp.currentId ) {
				this.moveElm( temp.currentId, temp.parentId, temp.currentIndex || 0 );
				// If the element was selected for editing then restore the highlight
				if ( !! temp.editable ) {
					this.showEditableHighlight( temp.currentId );
					// Force highlights position
					this.setHighlightsPosition();
				}
			}

			// Clearing all asset and temporary data to move
			this.clearDragAssets();
		},

		/**
		 * Clearing all asset and temporary data to move
		 */
		clearDragAssets: function() {
			var temp = this.getTemp( 'iframeDrag' );
			if ( $.isEmptyObject( temp ) ) {
				return;
			}
			$usbcore
				// Remove classes
				.$removeClass( temp.target, 'usb_transit' ) // TODO: Move to `this.hideTransit()`
				.$removeClass( temp.lastFoundContainer, 'usb_dropcontainer' )
				.$removeClass( _document.body, 'usb_draging' )
				// Remove dropplace element
				.$remove( temp.place );
			// Show tab button
			if ( temp.isParentTab ) {
				$usbcore.$removeClass( this._getSectionButtonById( this.getElmId( temp.target ) ), 'usb_transit' );
			}
			// Hide the transit
			this.hideTransit();
			// Flush temp data
			this.flushTemp( 'iframeDrag' );
		}
	} );

	/**
	 * Functionality for the implementation of highlights
	 * TODO: Position the highlight in the first container of the element.
	 */
	$.extend( $usbPreviewPrototype, {
		/**
		 * Show the highlight
		 * This method is called many times, so the implementation should be Vanilla JS
		 */
		showHighlight: function() {
			if ( ! this.isMode( 'editor' ) || ! this.isValidId( this.hoveredElmId ) ) {
				return;
			}
			var parentId = this.hoveredElmId,
				iteration = 0;
			while ( parentId !== this.mainContainer && parentId !== null ) {
				if ( iteration++ >= /* max number of iterations */1000/* 1 second */ ) {
					break;
				}
				// Add a clone for the new found element
				this._createHighlight( parentId );

				// Show highlight
				var item = this._highlights[ parentId ];
				item.active = true;
				item.highlight.style.display = 'block';

				/**
				 * Get next parent elm
				 * @var {string|null}
				 */
				parentId = this.getElmParentId( parentId );
			}
			// Set the highlight position
			this.setHighlightsPosition.call( this );
		},

		/**
		 * Hide the highlight
		 * This method is called many times, so the implementation should be Vanilla JS
		 */
		hideHighlight: function() {
			if ( $.isEmptyObject( this._highlights ) ) {
				return;
			}
			for ( var elmId in this._highlights  ) {
				var item = this._highlights[ elmId ];
				item.active = false;
				item.highlight.style.display = 'none';
			}
			this.hoveredElm = null;
			this.hoveredElmId = null;
		},

		/**
		 * Set the highlights position
		 * This method is called many times, so the implementation should be Vanilla JS
		 */
		setHighlightsPosition: function() {
			if ( ! this.isMode( 'editor' ) || $.isEmptyObject( this._highlights ) ) {
				return;
			}
			for ( var elmId in this._highlights ) {
				if ( ! this.isValidId( elmId ) ) {
					continue;
				}
				var item = this._highlights[ elmId ],
					// Receiving at this stage is necessary because the elements can be completely rebooted
					elm = this.getElmNode( elmId );
				if (
					! $usbcore.isNode( elm )
					|| (
						! item.active
						&& ! item.editable
					)
				) {
					continue;
				}
				var elmRect = $usbcore.$rect( elm ),
					cssProps = {
						top: elmRect.top + ( _window.pageYOffset || elm.scrollTop ),
						left: elmRect.left + ( _window.pageXOffset || elm.scrollLeft ),
						width: elmRect.width,
						height: elmRect.height
					};
				// Set css props
				$( item.highlight ).css( cssProps );
			}
		},

		/**
		 * Show highlight for editable element
		 *
		 * @param {string} id Shortcode's usbid, e.g. "us_btn:1"
		 */
		showEditableHighlight: function( id ) {
			if ( ! this.isValidId( id ) ) {
				return;
			}
			// Hide highlight for editable element
			this.hideEditableHighlight();
			// Get highlight object
			var item = this._highlights[ id ];
			// Create new highlight
			if ( ! item ) {
				this.hideHighlight();
				item = this._createHighlight( id );
				if ( item ) {
					item.active = true;
				}
				this.setHighlightsPosition();
			}
			// Show editable mode
			if ( item ) {
				item.editable = true;
				$usbcore.$addClass( item.highlight, 'usb_editable' );
			}
		},

		/**
		 * Hide highlight for editable element
		 *
		 * @param {string} id Shortcode's usbid, e.g. "us_btn:1" (Optional parameter)
		 */
		hideEditableHighlight: function() {
			if ( $.isEmptyObject( this._highlights ) ) {
				return;
			}
			var id = '' + arguments[ 0 ],
				highlights = this._highlights;
			// We update the list where we leave the highlights by the passed id
			if ( !! id && this.hasEditableHighlight( id ) ) {
				highlights = [ highlights[ id ] ];
			}
			for ( var elmId in highlights  ) {
				var item = highlights[ elmId ];
				if ( ! item.editable ) {
					continue;
				}
				// Removing the class that includes the highlighting of the editable element
				$usbcore.$removeClass( item.highlight, 'usb_editable' );
			}
		},

		/**
		 * Determines if editable highlight
		 *
		 * @param {string} id Shortcode's usbid, e.g. "us_btn:1"
		 * @return {boolean} True if editable highlight, False otherwise.
		 */
		hasEditableHighlight: function( id ) {
			return !! ( this._highlights[ id ] || {} ).editable;
		},

		/**
		 * The MutationObserver interface provides the ability to watch for changes being made to the DOM tree.
		 * @see https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver#mutationobserverinit
		 *
		 * @private
		 * @param {string} id Shortcode's usbid, e.g. "us_btn:1"
		 * @return {MutationObserver|undefined}
		 */
		_getMutationObserver: function( id ) {
			var target;
			if (
				! this.isValidId( id )
				|| ! ( target = this.getElmNode( id ) )
			) {
				return;
			}
			var observer = new MutationObserver( $usbcore.debounce( this.setHighlightsPosition.bind( this ), 1 ) );
			observer.observe( target, {
				characterData: true,
				childList: true,
				subtree: true
			} );
			return observer;
		},

		/**
		 * Create new highlight
		 *
		 * @private
		 * @param {string} id Shortcode's usbid, e.g. "us_btn:1"
		 * @return {{}|null} The highlight object
		 */
		_createHighlight: function( id ) {
			if (
				! this.isValidId( id )
				||  this._highlights[ id ]
				|| ! $usbcore.isNode( this.highlight )
			) {
				return null;
			}

			// Clone an element from a template
			var highlightElm = this.highlight.cloneNode( true ),
				elm = this.getElmNode( id ),
				elmEditLink = $usbcore.$attr( elm, 'data-edit_link' );
			// Add a title for highlighting
			highlightElm
				.querySelector( '.usb-builder-hover-panel-name' )
				.innerText = this.getElmTitle( id );
			// Add Edit link if set
			if ( elmEditLink ) {
				$usbcore.$attr(
					highlightElm.querySelector( '.usb-builder-hover-panel-edit' ),
					'href',
					elmEditLink
				);
			}
			// Add all the necessary settings
			$usbcore
				.$attr( highlightElm, 'data-elmid', id )
				.$addClass( highlightElm, 'elm_' + this.getElmType( id ) );
			this.highlight
				.after( highlightElm );

			/**
			 * Definition and purpose of zIndex for highlight only
			 * Note: Necessary for correct display on mobile responsive mode.
			 */
			var zIndex = 9999; // The default zIndex
			if ( this.isSecondElmContainer( id ) ) {
				zIndex -= 1;
			} else if ( this.isRootElmContainer( id ) ) {
				zIndex -= 2;
			}
			highlightElm.style.zIndex = zIndex;

			// Add nodes to a temporary variable
			return this._highlights[ id ] = {
				active: false,
				editable: false,
				highlight: highlightElm,
				MutationObserver: this._getMutationObserver( id )
			};
		},

		/**
		 * Remove a highlights
		 */
		removeHighlights: function() {
			if ( $.isEmptyObject( this._highlights ) ) {
				return;
			}
			for ( var elmId in this._highlights ) {
				if ( ! this.isValidId( elmId ) ) {
					continue;
				}
				if ( null === this.getElmNode( elmId ) ) {
					// Get current highlight data
					var data = this._highlights[ elmId ];
					/**
					 * Disconnect from watching mutations
					 * @see https://developer.mozilla.org/en-US/docs/Web/API/MutationObserver/disconnect
					 */
					if ( data.MutationObserver instanceof MutationObserver ) {
						data.MutationObserver.disconnect();
					}
					// Remove node element
					$usbcore.$remove( data.highlight );
					// Remove data
					delete this._highlights[ elmId ];
				}
			}
		}
	} );

	/**
	 * Functionality for handling events
	 */
	$.extend( $usbPreviewPrototype, {
		/**
		 * Kill current event
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_stop: function( e ) {
			e.preventDefault();
			e.stopPropagation();
		},

		/**
		 * The event fires when the initial HTML document has been completely loaded and parsed,
		 * without waiting for stylesheets, images, and subframes to finish loading.
		 *
		 * @private
		 */
		_DOMContentLoaded: function() {
			// Added class after loading documents so that all scripts have time to be initialized.
			this.$body.addClass( 'usb_content_ready' );
		},

		/**
		 * Link click handler
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_linkClickHandler: function( e ) {
			var $target = $( e.currentTarget ),
				href = ( $target.attr( 'href' ) || '' ).toLowerCase();

			// Anything to exclude from opening in a new window.
			if (
				href.charAt( 0 ) == '#'
				|| href.substr( 0, 'javascript:'.length ) == 'javascript:'
				|| $target.is( '[ref=magnificPopup]' )
				|| $target.hasClass( '.w-tabs-item' ) // Exclude all TTA buttons.
			) {
				return;
			}

			// Kill event
			this._events.stop( e );

			// Opening links in a new window
			_window.open( href, '_blank' );
		},

		/**
		 * Handler for start css animation in element
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_elmAnimationStart: function( e ) {
			if ( ! $usbcore.$attr( e.target, 'data-usbid' ) ) {
				return;
			}
			if (
				this.selectedElmId
				&& this.getElmType( this.selectedElmId ) !== 'us_grid'
			) {
				this.hideEditableHighlight();
			}
		},

		/**
		 * Handler for end css animation in element
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_elmAnimationEnd: function( e ) {
			if ( ! $usbcore.$attr( e.target, 'data-usbid' ) ) {
				return;
			}
			if (
				this.isValidId( this.selectedElmId )
				&& this.getElmType( this.selectedElmId ) !== 'us_grid'
			) {
				this.showEditableHighlight( this.selectedElmId );
				this.setHighlightsPosition();
			}
		},

		/**
		 * The handler is triggered every time the cursor leaves the iframe window
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_mouseLeavesIframe: function( e ) {
			if ( ! this.isHidePanel() ) {
				// The hide all highlights
				this.hideHighlight();
			}
			// Clearing all asset and temporary data to move
			this.clearDragAssets();
		},

		/**
		 * Selected element
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_elmSelected: function( e ) {
			// Check the `editor` mode (Only in edit mode we can select elements to change)
			if ( ! this.isMode('editor') || this.isHidePanel() ) {
				return;
			}
			var elm = this._getNearestElm( e.target );
			if ( elm && elm === this.hoveredElm ) {
				this.selectedElmId = this.getElmId( elm );
				this.postMessage( 'elmSelected', this.selectedElmId );
			}
		},

		/**
		 * Handler when the cursor enters the bounds of an element
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_elmMove: function( e ) {
			if ( this.isHidePanel() ) {
				return;
			}
			var elm = this._getNearestElm( e.target );
			if ( elm && elm !== this.hoveredElm ) {
				this.hideHighlight();
				this.hoveredElm = elm;
				this.hoveredElmId = this.getElmId( elm );
				this.postMessage( 'elmMove', this.hoveredElmId );
				this.showHighlight();
			}
		},

		/**
		 * Handler when the cursor moves out of the bounds of an element
		 *
		 * @private
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_elmLeave: function( e ) {
			if ( this.isHidePanel() ) {
				return;
			}
			var elm = this._getNearestElm( e.target );
			if ( elm ) {
				this.postMessage( 'elmLeave', this.getElmId( elm ) );
				this.hoveredElm = null;
				this.hoveredElmId = null;
			}
		},

		/**
		 * Handler when the duplicate element
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_elmDuplicate: function( e ) {
			var $highlight = $( e.currentTarget ).closest( '.usb-builder-hover' ),
				elmId = $highlight.data( 'elmid' );
			if ( ! elmId  ) {
				return;
			}
			this.postMessage( 'elmDuplicate', elmId );
		},

		/**
		 * Handler when the delete element
		 *
		 * @private
		 * @event handler
		 * @param {Event} e The Event interface represents an event which takes place in the DOM.
		 */
		_elmDelete: function( e ) {
			var $highlight = $( e.currentTarget ).closest( '.usb-builder-hover' ),
				elmId = $highlight.data( 'elmid' );
			if ( ! elmId  ) {
				return;
			}
			$usbcore
				.$remove( this._highlights[ elmId ].highlight || null );
			delete this._highlights[ elmId ];
			this.postMessage( 'elmDelete', elmId );
		}
	});

	/**
	 * Functionality for the implementation of Design options
	 * TODO: All methods that relate to the generation of styles from design settings must be optimized for performance!
	 */
	$.extend( $usbPreviewPrototype, {
		/**
		 * Delayed start of CSS animation
		 * Note: The code is moved to a separate function since `debounced` must be initialized before calling.
		 *
		 * @private
		 * @type debounced
		 */
		__startAnimation: $usbcore.debounce( function( elm ) {
			$usbcore.$addClass( elm, 'start' );
		}, 1 ),

		/**
		 * Add or update custom styles in a document
		 *
		 * @private
		 * @param {string} id Shortcode's usbid, e.g. "us_btn:1"
		 * @param {string} jsoncss The line of design settings from the $usof.field[ 'design_options' ]
		 */
		_addDesignOptions: function( id, jsoncss ) {
			if ( ! id ) {
				return;
			}

			//
			var _styleElms = {};

			jsoncss = '' + jsoncss;
			// Find element of styles for shortcode
			_document.querySelectorAll( 'style[data-for="'+ id +'"]' )
				.forEach( function( elm, i ) {
					if ( i === 0 ) {
						return _styleElms[ id ] = elm;
					}
					// Delete all unnecessary if any
					$usbcore.$remove( elm );
				}.bind( this ));

			/**
			 * Get animated properties in one line
			 *
			 * @param {node} elm
			 * @return {string|undefinded}
			 */
			var getAnimateProps = function( elm ) {
				if ( ! $usbcore.isNode( elm ) ) {
					return;
				}
				var style = _window.getComputedStyle( elm ),
					name = style.getPropertyValue( 'animation-name' ),
					delay = style.getPropertyValue( 'animation-delay' );
				if ( name && name !== 'none' ) {
					return name + delay;
				}
				return;
			};

			// Get shortcode element
			var elm = this.getElmNode( id );

			// Get the first child for buttons
			// Note: Exception for elements that have wrapper that are not main.
			if ( this.getElmType( id ) === 'us_btn' && $usbcore.isNode( elm ) ) {
				elm = elm.firstChild || elm;
			}

			// If there is no style element then create a new one
			if ( ! _styleElms[ id ] ) {
				var // Custom prefix
					customPrefix = this._designOptions[ 'customPrefix' ],
					// Generate unique class name
					className = $usbcore.uniqid( customPrefix );
				// If the element is absent then we will complete the action
				if ( ! $usbcore.isNode( elm ) ) return;
				_styleElms[ id ] = $( '<style data-for="'+ id +'" data-classname="'+ className +'"></style>' )[0];
				// Add a new styling element to the page
				elm.after( _styleElms[ id ] );
				// Removing the old custom class in the absence of a styling element `<style data-for="..." data-classname="..."></style>`
				if ( elm.className.indexOf( customPrefix ) > -1 ) {
					elm.className = elm.className.replace(
						new RegExp( '(' + this.escapeRegExp( customPrefix ) + '\\w+)' ),
						''
					);
				}
				// Add a new class for custom styles
				$usbcore.$addClass( elm, className );
			}

			// Determine the presence of an animation name
			var hasAnimateName = jsoncss.indexOf( 'animation-name' ) > -1,
				oldAnimateProp;

			// Compile and add styles to document
			if ( _styleElms[ id ] ) {
				var _className = $usbcore.$attr( _styleElms[ id ], 'data-className' );
				// If there are animation settings, keep the old value
				if ( hasAnimateName ) {
					oldAnimateProp = getAnimateProps( elm );
				}
				_styleElms[ id ].innerText = this._compileDesignOptions( _className, jsoncss );
			}

			// Checking classes and restarting animation
			if ( hasAnimateName ) {
				var currentAnimateProps = getAnimateProps( elm );
				if ( currentAnimateProps && currentAnimateProps !== oldAnimateProp ) {
					// Adjusting classes for normal animation work
					$usbcore.$addClass( elm, 'us_animate_this' );
					$usbcore.$removeClass( elm, 'start' );
					// Delayed start of CSS animation
					this.__startAnimation( elm );
				}
			} else if ( ( '' + elm.className ).indexOf( 'us_animate_this' ) > -1 ) {
				$usbcore.$removeClass( elm, 'us_animate_this start' );
			}
		},

		/**
		 * Removing design styles for elements that do not exist
		 *
		 * @private
		 */
		_removeDesignForElmsNotExist: function() {
			_document.querySelectorAll( 'style[data-for]' )
				.forEach( function( style ) {
					var id = $usbcore.$attr( style, 'data-for' );
					if ( id && null === this.getElmNode( id ) ) {
						$usbcore.$remove( style );
					}
				}.bind( this ) );
		},

		/**
		 * Remove style tag by element ID
		 *
		 * @private
		 * @param {string} id Shortcode's usbid, e.g. "us_btn:1"
		 */
		_removeDesignById: function( id ) {
			_document.querySelectorAll( 'style[data-for="'+ id +'"]' )
				.forEach( function( style ) {
					$usbcore.$remove( style );
				} );
		},

		/**
		 * Compile and add styles to document
		 * Note: The method can be called many times, especially when choosing a color, it should be as efficient and
		 * fast as possible
		 *
		 * @private
		 * @param {string} className The unique name of the class attached to the element
		 * @param {string} jsoncss Design settings as a unescape string
		 * @return {string} Compiled css string
		 */
		_compileDesignOptions: function( className, jsoncss ) {
			var collections = {};
			// Get object jsoncss
			try {
				jsoncss = JSON.parse( unescape( '' + jsoncss ) || '{}' );
			} catch ( e ) {
				jsoncss = {};
			}
			// If there are no jsoncss options, return an empty string
			if ( $.isEmptyObject( jsoncss ) ) {
				return '';
			}

			// Create of collections for different responsive states
			this.config( 'responsiveStates', [] ).map( function( responsiveState ) {
				if ( !! jsoncss[ responsiveState ] ) {
					collections[ responsiveState ] = this._normalizeJsoncss( jsoncss[ responsiveState ] );
				}
			}.bind( this ) );
			var // Result string, these are the compiled styles
				result = '';
			// The formation of styles for different responsive states
			for ( var responsiveState in collections ) {
				if ( $.isEmptyObject( collections[ responsiveState ] ) ) {
					continue;
				}
				var // Final css code
					cssCode = '',
					// Get the current collection ( Apply
					// masks to css properties )
					collection = this._applyMaskToBackgroundCss( collections[ responsiveState ] ),
					// Get breakpoint sizes
					breakpoint = ( this._designOptions.breakpoints || {} )[ responsiveState ] || '';
				// Collection to string options
				for( var prop in collection ) {
					if ( ! prop || ! collection[ prop ] ) {
						continue;
					}
					cssCode += prop + ':' + collection[ prop ] + '!important;';
				}
				// Add class to styles
				if ( cssCode ) {
					cssCode = '.' + className + '{'+ cssCode +'}';
				}
				// Add styles to the result
				result += ( breakpoint )
					? '@media '+ breakpoint +' {'+ cssCode +'}'
					: cssCode;
			}
			return result;
		},

		/**
		 * This helper method is for normalizing css options ( jsoncss option -> css option )
		 * TODO: Minimal functionality providing only styling applications without optimizations
		 *
		 * @private
		 * @param {{}} cssOptions The css options
		 * @return {{}}
		 */
		_normalizeJsoncss: function( options ) {
			if ( $.isEmptyObject( options ) ) {
				return options;
			}

			// For background-image get an image URL by attachment ID (Preliminary check)
			if ( !! options[ 'background-image' ] ) {
				var url = this.getAttachmentUrl( options[ 'background-image' ] );
				if ( !! url ) {
					options[ 'background-image' ] = 'url('+ url +')';
				}
			}

			// Normalization of css parameters
			for ( var prop in options ) {
				if ( ! prop || ! options[ prop ] ) {
					continue;
				}
				var value = options[ prop ];

				/**
				 * If the name contains the text color and the values start from the underscore,
				 * try to get the css variable
				 *
				 * Example: color, background-color, border-color, box-shadow-color etc.
				 */
				if ( /(^color|-color$)/.test( prop ) && ( '' + value ).charAt( 0 ) === '_' ) {
					value = this.getColorValue( value );
					options[ prop ] = value;
				}

				// Generate correct font-family value
				if ( prop === 'font-family' ) {
					options[ prop ] = ( this._designOptions.fontVars || {} )[ value ] || value;
				}
				// border-style to border-{position}-style provided that there is a width of this border
				if ( prop === 'border-style' ) {
					[ 'left', 'top', 'right', 'bottom' ] // List of possible positions
						.map( function( position ) {
							var borderWidth = options[ 'border-'+ position +'-width' ];
							if ( ! $usbcore.isUndefined( borderWidth ) && borderWidth !== '' ) {
								options[ 'border-'+ position +'-style' ] = '' + value;
							}
						}.bind( this ) );
					delete options[ prop ];
				}
				// Check for line spacing
				if ( prop === 'font-height' ) {
					if ( !! value ) {
						options[ 'line-height' ] = value;
					}
					delete options[ prop ];
				}
			}
			// Forming `box-shadow` from the list of parameters
			if ( !! options && !! options[ 'box-shadow-color' ] ) {
				var _boxShadow = [];
				// Value map for `box-shadow` this map is needed to turn the list into a string,
				// the order is also very important here!
				[ 'h-offset', 'v-offset', 'blur', 'spread', 'color' ].map( function( key ) {
					var value = options[ 'box-shadow-' + key ];
					if ( $usbcore.isUndefined( value ) ) {
						value = ( key === 'color' )
							? 'transparent' // The default color
							: '0';
					}
					_boxShadow.push( value );
					delete options[ 'box-shadow-' + key ];
				}.bind( this ) );
				if ( _boxShadow.length ) {
					options[ 'box-shadow' ] = _boxShadow.join( ' ' );
				}
			}

			return options;
		},

		/**
		 * Apply masks to css properties
		 *
		 * @private
		 * @param {{}} collection The collection
		 * @return {{}}
		 */
		_applyMaskToBackgroundCss: function( collection ) {
			collection = $.extend( {}, collection || {} );
			/**
			 * Masks for optimizing and combining styles
			 * NOTE: The order of all values must match the specification of the css
			 * @type {{}}
			 */
			var propNames = 'color image repeat attachment position size'.split( ' ' ) || [], // Get an array of
																							  // properties
				assignedProps = {},
				backgroupdPropValue = '';
			// If there are masks, then check and remove from the main collection
			for ( var i in propNames ) {
				var name = propNames[ i ],
					cssName = 'background-' + name;

				if ( !! collection[ cssName ] ) {
					assignedProps[ name ] = collection[ cssName ];
					delete collection[ cssName ];
				}
			}
			/**
			 * Adjust background options before merging
			 * @link https://www.w3schools.com/cssref/css3_pr_background.asp
			 */
			var _gradient = '';
			if ( !! assignedProps[ 'image' ] && this._isCssGradient( assignedProps[ 'color' ] ) ) {
				_gradient = assignedProps[ 'color' ];
				delete assignedProps[ 'color' ];
			}
			if ( !! assignedProps[ 'size' ] ) {
				// If size is set, position should have a value, setting default value for position if it is not set
				if ( ! assignedProps[ 'position' ] ) {
					assignedProps[ 'position' ] = 'left top';
				}
				assignedProps[ 'size' ] = '/ ' + assignedProps[ 'size' ];
			}

			for ( var i in propNames ) {
				var name = propNames[ i ];
				if ( !! assignedProps[ name ] ) {
					backgroupdPropValue += ' ' + assignedProps[ name ];
				}
			}
			// If there is a gradient then add to the end
			if ( _gradient ) {
				backgroupdPropValue += ', ' + _gradient;
			}
			// Add a property created by the mask
			collection[ 'background' ] = backgroupdPropValue.trim();

			return collection;
		},

		/**
		 * Determines whether the specified value is css gradient.
		 *
		 * @private
		 * @param {string} value The css value
		 * @return {string} True if the specified value is css gradient, False otherwise.
		 */
		_isCssGradient: function( value ) {
			value += ''; // To string
			return value.indexOf( 'gradient' ) > -1 || /\s?var\(.*-grad\s?\)$/.test( value ); // The support css var(*-grad);
		}

	} );

	/**
	 * Functionality for the implementation of Main API
	 */
	$.extend( $usbPreviewPrototype, {

		/**
		 * Direction constants
		 * @var {{}}
		 */
		_DIRECTION: {
			BOTTOM: 'bottom',
			LEFT: 'left',
			RIGHT: 'right',
			TOP: 'top',
			UNKNOWN: 'unknown'
		},

		/**
		 * Get the mouse movement angle
		 *
		 * @private
		 * @param {node} target The target node
		 * @param {number} clientX The coordinates along the X axis
		 * @param {number} clientY The coordinates along the Y axis
		 * @return {number} Return the angle of mouse movement
		 *
		 * Visual example of a map in 360°:
		 * +--------------------+--------------------+
		 * | -165              -90               -15 |
		 * |                    |                    |
		 * | -180               |                 -1 |
		 * +------------------- 0 -------------------+
		 * |  180               |                  1 |
		 * |                    |                    |
		 * |  165               90                15 |
		 * +--------------------+--------------------+
		 */
		_getMouseAngle: function( target, clientX, clientY ) {
			// Check if the target is a node
			if ( ! $usbcore.isNode( target ) ) {
				return 0;
			}
			var // Radius to Degree
				RAD_TO_DEG = 180 / Math.PI,
				// Get the size of the container and its position relative to the viewport
				rect = $usbcore.$rect( target ),
				// Get the center of the container
				center = {
					x: rect.width / 2 + rect.left,
					y: rect.height / 2 + rect.top
				},
				// Get a vector relative to the target (container)
				vector = {
					x: clientX - center.x,
					y: clientY - center.y
				},
				// Get a vector length
				vectorLength = Math.sqrt( vector.x * vector.x + vector.y * vector.y ),
				// Get a directions
				direction = {
					x: vector.x / vectorLength,
					y: vector.y / vectorLength
				};
			// Return the angle of mouse movement
			return Math.atan2( direction.y, direction.x ) * RAD_TO_DEG;
		},

		/**
		 * Get the direction of the mouse movement relative to the target along Y axis
		 *
		 * @private
		 * @param {node} target The target node
		 * @param {number} clientX The coordinates along the X axis
		 * @param {number} clientY The coordinates along the Y axis
		 * @return {string}
		 */
		_getMouseDirectionY: function( target, clientX, clientY ) {
			// Check if the target is a node
			if ( ! $usbcore.isNode( target ) ) {
				return this._DIRECTION.UNKNOWN;
			}
			// Get the mouse movement angle
			return ( this._getMouseAngle( target, clientX, clientY ) < 0 )
				? this._DIRECTION.TOP
				: this._DIRECTION.BOTTOM;
		},

		/**
		 * Get the direction of the mouse movement relative to the target along X axis
		 *
		 * @private
		 * @param {node} target The target node
		 * @param {number} clientX The coordinates along the X axis
		 * @param {number} clientY The coordinates along the Y axis
		 * @return {string}
		 */
		_getMouseDirectionX: function( target, clientX, clientY ) {
			// Check if the target is a node
			if ( ! $usbcore.isNode( target ) ) {
				return this._DIRECTION.UNKNOWN;
			}
			// Get the mouse movement angle
			var angle = this._getMouseAngle( target, clientX, clientY );
			return ( angle > -180 && angle <= -130 || angle <= 180 && angle > 130 )
				? this._DIRECTION.LEFT
				: this._DIRECTION.RIGHT;
		},

		/**
		 * Get directions of mouse movement relative to target
		 * Note: The code is not used.
		 *
		 * @private
		 * @param {node} target The target node
		 * @param {number} clientX The coordinates along the X axis
		 * @param {number} clientY The coordinates along the Y axis
		 * @return {string}
		 */
		// TODO: looks like unused, possibly delete this method
		_getMouseDirection: function( target, clientX, clientY ) {
			// Check if the target is a node
			if ( ! $usbcore.isNode( target ) ) {
				return this._DIRECTION.UNKNOWN;
			}
			// Get the mouse movement angle
			var angle = this._getMouseAngle( target, clientX, clientY );
			// Determine the direction depending on the angle of movement
			if ( angle <= -45 && angle > -130 ) {
				return this._DIRECTION.TOP;
			} else if ( angle > -180 && angle <= -130 || angle <= 180 && angle > 130 ) {
				return this._DIRECTION.LEFT;
			} else if ( angle > 45 && angle <= 130 ) {
				return this._DIRECTION.BOTTOM;
			} else if ( angle <= 45 && angle > -45 ) {
				return this._DIRECTION.RIGHT;
			}
			return this._DIRECTION.UNKNOWN;
		},

		/**
		 * Get the border under mouse.
		 *
		 * @private
		 * @param {node} target The target node
		 * @param {number} clientX The coordinates along the X axis
		 * @param {number} clientY The coordinates along the Y axis
		 * @return {boolean} True if mouse on container border, False otherwise.
		 */
		_getBorderUnderMouse: function( target, clientX, clientY ) {
			if (
				! $usbcore.isNode( target )
				|| target === this.elmMainContainer
				|| ! $.isNumeric( clientX )
				|| ! $.isNumeric( clientY )
			) {
				return this._DIRECTION.UNKNOWN;
			}

			// Scrolling corrections
			clientX += _window.scrollX;
			clientY += _window.scrollY;

			// Get sizes
			var elmRect = $usbcore.$rect( target ),
				elmX = Math.floor( Math.abs( elmRect.x ) + _window.scrollX ),
				elmY = Math.floor( Math.abs( elmRect.y ) + _window.scrollY ),
				elmBottom = Math.floor( elmY + elmRect.height ),
				elmRight = Math.floor( elmX + elmRect.width ),
				borderAround = 10; // This is the size of the border around the perimeter of the container.

			// Top border
			if ( clientY > elmY && clientY <= ( elmY + borderAround ) ) {
				return this._DIRECTION.TOP;
			}
			// Bottom border
			else if ( clientY < elmBottom && clientY >= ( elmBottom - borderAround ) ) {
				return this._DIRECTION.BOTTOM;
			}
			// Left border
			else if ( clientX > elmX && clientX <= ( elmX + borderAround ) ) {
				return this._DIRECTION.LEFT;
			}
			// Rigth border
			else if ( clientX < elmRight && clientX >= ( elmRight - borderAround ) ) {
				return this._DIRECTION.RIGHT;
			}

			return this._DIRECTION.UNKNOWN;
		},

		/**
		 * Determines if hoverable element
		 *
		 * @private
		 * @param {node} elm The elm
		 * @param {string} filterName
		 * @return {boolean} True if hoverable element, False otherwise.
		 */
		_isHoverableNode: function( elm, filterName ) {
			if ( ! elm ) {
				return false;
			}
			var elmId = this.getElmId( elm );
			switch ( filterName ) {
				case 'elmContainer':
					return this.isElmContainer( elmId );
					break;
				case 'secondContainer':
					return this.isSecondElmContainer( elmId );
					break;
				case 'rootContainer':
					return this.isRootElmContainer( elmId );
					break;
				default:
					return !! elmId;
					break;
			}
		},

		/**
		 * Get the nearest node
		 *
		 * TODO: It is necessary to optimize in the DOM to find only the first element,
		 * everything else is based on the getElmParentId method as it works from $usb.pageData.content
		 *
		 * @private
		 * @param {node} elm The elm
		 * @param {string} filterName Filters when checking the found node
		 * @return {mixed}
		 */
		_getNearestNode: function( elm, filterName ) {
			var found;
			while ( ! ( found = this._isHoverableNode( elm, filterName ) ) ) {
				if ( ! elm.parentNode ) {
					return null;
				}
				elm = elm.parentNode;
			}
			return ( found ) ? elm : null;
		},

		/**
		 * Get the nearest elment
		 *
		 * @private
		 * @param {node} elm The elm
		 * @return {mixed}
		 */
		_getNearestElm: function( elm ) {
			return this._getNearestNode( elm );
		},

		/**
		 * Get the nearest elment container
		 *
		 * @private
		 * @param {node} elm The elm
		 * @return {mixed}
		 */
		_getNearestElmContainer: function( elm ) {
			return this._getNearestNode( elm, 'elmContainer' );
		},

		/**
		 * Get the nearest second elment container
		 *
		 * @private
		 * @param {node} elm The elm
		 * @return {mixed}
		 */
		_getNearestSecondElmContainer: function( elm ) {
			return this._getNearestNode( elm, 'secondContainer' ); // TODO: ( secondContainer | elmContainer ) ???
		},

		/**
		 * Get the nearest root elment container
		 *
		 * @private
		 * @param {node} elm The elm
		 * @return {mixed}
		 */
		_getNearestRootElmContainer: function( elm ) {
			return this._getNearestNode( elm, 'rootContainer' );
		},

		/**
		 * Get the color value
		 * Note: The color result can include variable css
		 *
		 * @param {string} value The value
		 * @return {string} The color value
		 */
		getColorValue: function( value ) {
			if ( ( '' + value ).indexOf( '_' ) > -1 ) {
				return this._designOptions.colorVars[ value ] || value;
			}
			return value;
		},

		/**
		 * Get the target element
		 *
		 * @private
		 * @param {string} targetId Shortcode's usbid, e.g. "us_btn:1" or `container`
		 * @param {string} position The position
		 * @return {mixed}
		 */
		_getTargetElm: function( targetId, position ) {
			// Check the correctness of the data in the variables
			if (
				! targetId
				|| ! parent.$usb
				|| ['before', 'prepend', 'append', 'after'].indexOf( position ) === - 1
			) {
				return;
			}
			var isMainContainer = this.isMainContainer( targetId ),
				// Find parent element
				// TODO:Optimize and implement without jQuery
				$parentElm = $( this.getElmNode( isMainContainer ? this.mainContainer : targetId ) );
			// When positioned before or after, return the $parentElm unchanged
			if ( ['before', 'after'].indexOf( position ) !== - 1 ) {
				return $parentElm;
			}
			/**
			 * Parent adjustment for different shortcodes
			 *
			 * Note: All searches for the location of the root element are strictly tied to
			 * the structure and classes, see the switch construction!
			 */
			if ( ! isMainContainer && $parentElm.length ) {
				switch ( parent.$usb.getElmType( targetId ) ) {
					case 'vc_row':
						$parentElm = $( '> .l-section-h > .g-cols', $parentElm );
						break;
					case 'vc_tta_accordion':
					case 'vc_tta_tabs':
					case 'vc_tta_tour':
						$parentElm = $( '.w-tabs-sections:first', $parentElm );
						break;
					case 'vc_tta_section':
						$parentElm = $( '.w-tabs-section-content-h:first', $parentElm );
						break;
					case 'vc_row_inner':
						// Without changes!
						break;
					default:
						var $columnWrapper = $( '.vc_column-inner:first', $parentElm ),
							$legacyColumnWrapper = $( '.vc_column-inner:first > .wpb_wrapper', $parentElm );
						if ( $legacyColumnWrapper.length ) {
							$parentElm = $legacyColumnWrapper;
						} else if ( $columnWrapper.length ) {
							$parentElm = $columnWrapper;
						}
						break;
				}
			}
			return $parentElm;
		},

		/**
		 * Get an node or nodes by ID
		 *
		 * @param {string|[]} id Shortcode's usbid, e.g. "us_btn:1"
		 * @return {null|node|[node...]}
		 */
		getElmNode: function( id ) {
			if ( ! id ) {
				return;
			}
			var ids = id;

			// The convert to a single type to data
			if ( ! $.isArray( ids ) ) {
				ids = [ ids ];
			}

			// Checking if the ID's is correct
			ids = ids.filter( function( id ) {
				// We will leave everything that passes the validation, and delete the rest.
				return this.isValidId( id ) || this.isMainContainer( id );
			}.bind( this ) );

			// Convert ID's to selectors
			ids = ids.map(function( id ) {
				return '[data-usbid="'+ id +'"]';
			});

			// The get one node
			if ( typeof id === 'string' && ids.length === 1 ) {
				return _document.querySelector( ids[0] );

			}
			// The get an array of nodes
			if ( $.isArray( id ) && ids.length ) {
				var nodes =_document.querySelectorAll( ids.join( ',' ) );
				return [].slice.call( nodes );
			}

			// If there is nothing, return `null`
			return null;
		},

		/**
		 * Get all html for a node including styles
		 *
		 * @param {string|[]} id Shortcode's usbid, e.g. "us_btn:1"
		 * @return {string}
		 */
		getElmOuterHtml: function( id ) {
			var node = this.getElmNode( id );
			if ( $usbcore.isNode( node ) ) {
				return node.outerHTML + ( _document.querySelector( 'style[data-for="'+ id +'"]' ) || {} ).outerHTML || '';
			}
			return '';
		},

		/**
		 * Gets the section button by id
		 *
		 * @private
		 * @param {string} sectionId Shortcode's usbid, e.g. "vc_tta_section:1"
		 * @return {node|null}.
		 */
		_getSectionButtonById: function( sectionId ) {
			if (
				! this.isValidId( sectionId )
				|| ! this.isElmTTA( sectionId )
			) {
				return null;
			}
			return _document.querySelector( '[data-related-to="'+ sectionId +'"]' );
		},

		/**
		 * Set the highlights position
		 * Note: The code is moved to a separate function since `debounced` must be initialized before calling.
		 *
		 * @private
		 * @type debounced
		 */
		__setHighlightsPosition: $usbcore.debounce( function() {
			this.setHighlightsPosition();
		}, 10 ),

		/**
		 * Handlers for private events
		 * @private
		 */
		_$events: {

			/**
			 * Called every time an element is duplicate
			 *
			 * @private
			 * @event handler
			 * @param {string} id Shortcode's usbid, e.g. "us_btn:1"
			 */
			duplicateElmId: function( id ) {
				if ( ! this.isValidId( id ) ) {
					return;
				}
				this.selectedElmId = id;
				this.showEditableHighlight( id );
			},

			/**
			 * The handler is called every time the panel display changes
			 *
			 * @private
			 * @event handler
			 */
			changeSwitchPanel: function() {
				this.$body.toggleClass( 'usb_preview', ! this.isHidePanel() );
			},

			/**
			 * Show the loading
			 *
			 * @private
			 * @event handler
			 * @param {string} targetId Shortcode's usbid, e.g. "us_btn:1"
			 * @param {string} position The position ( possible values: before, prepend, append, after )
			 * @param {boolean} isContainer If these values are true, then a container class will be added for customization
			 * @param {string} id The unique id for preloader
			 */
			showPreloader: function( targetId, position, isContainer, id ) {
				// The replace element
				if ( $usbcore.isUndefined( position ) ) {
					$( this.getElmNode( targetId ) )
						.addClass( this.config( 'className.elmLoading', '' ) );
					return;
				}
				// Creating a new preloader
				var $preloader = $( '<div class="g-preloader type_1 for_usbuilder"></div>' )
					// If a container is added to the tucked place, then we add a class to be able to customize the display
					.toggleClass( 'usb-loading-container', !! isContainer );

				// Add to the list of active preloaders
				this._preloaders[ id || targetId ] = $preloader.get( 0 );

				// The insert element
				this.trigger( 'insertElm', [ targetId, position, $preloader ] );
			},

			/**
			 * Hide the loading
			 *
			 * @private
			 * @event handler
			 * @param {string} id Shortcode's usbid, e.g. "us_btn:1"
			 */
			hidePreloader: function( id ) {
				if ( !! id && this._preloaders[ id ] ) {
					$usbcore
						.$remove( this._preloaders[ id ] );
					delete this._preloaders[ id ];
				}
			},

			/**
			 * Remove an element from a document by its ID
			 *
			 * @event handler
			 * @param {string|[]} id The element that is being removed, e.g. "us_btn:1"
			 */
			removeHtmlById: function ( removeId ) {
				if ( ! removeId ) {
					return;
				}
				if ( ! $.isArray( removeId ) ) {
					removeId = [ removeId ];
				}
				// Get all nodes to remove
				var nodes = this.getElmNode( removeId ) || [];
				if ( ! nodes.length ) {
					return;
				}

				// Remove all nodes
				nodes.map( function( node ) {
					if ( ! $usbcore.isNode( node ) ) {
						return;
					}
					var $node = $( node ),
						$tabs = $node.closest( '.w-tabs' );

					// Removing a button and opening a free section
					if ( this.getElmType( node ) === 'vc_tta_section' ) {
						$( '[aria-controls="content-'+ $node.attr( 'id' ) +'"]:first', $tabs )
							.remove();
						// The opening the first section
						$tabs
							.find( '.w-tabs-list a:first, .w-tabs-section-title:first' )
							.trigger('click')
					}
					// Remove node
					$node
						// Trigger events about the remove of an element
						// to track changes in the elements.
						.trigger( 'usb.removeHtml' )
						// Remove a element
						.remove();
					// Remove highlights ( TODO:Remove after merge # 2313 )
					this.removeHighlights();
					// Removing design styles for elements that do not exist ( TODO:Remove after merge # 2313 )
					this._removeDesignForElmsNotExist();
				}.bind( this ) );
			},

			/**
			 * Add new item to document
			 *
			 * @event handler
			 * @param {string|node} parent Shortcode's usbid, e.g. "us_btn:1" or `container`
			 * @param {string} position The position ( possible values: before, prepend, append, after )
			 * @param {string} html The html
			 * @param {boolean} scrollIntoView If the True are set, then after adding the scroll   to the new node.
			 */
			insertElm: function( parent, position, html, scrollIntoView ) {
				// Definition based on `usbid` and position
				var $parentElm = ! $usbcore.isNode( parent )
					? this._getTargetElm( parent, position )
					: $( parent ); // If explicitly passed node to `parent`
				// TODO: This code is often called when moving or adding a new item, so you need to implement in VanillaJS
				if ( $parentElm instanceof $ ) {
					var $html = $( html );
					$parentElm[ position ]( $html );
					// Init its JS if needed
					$( '[data-usbid]', $html ).each( function( _, item ) {
						this.trigger( 'maybeInitElmJS', [ $usbcore.$attr( item, 'data-usbid' ) ] );
					}.bind( this ));
					// Scrolls the current container of the parent of the element so that the new element is visible to the user.
					if ( scrollIntoView ) {
						$html[0].scrollIntoView();
						// The animation start control.
						$( ( '[class*="us_animate_"]:not(.start)' ), $html )
							.addClass( 'start' );
					}
				}
			},

			/**
			 * Move element on preview page
			 *
			 * @event handler
			 * @param {string} parent Shortcode's usbid, e.g. "us_btn:1" or `container`
			 * @param {string} position The position ( possible values: before, prepend, append, after )
			 * @param {string} elmId Shortcode's usbid, e.g. "us_btn:1"
			 */
			moveElm: function( parent, position, elmId ) {
				var $parentElm = this._getTargetElm( parent, position ),
					$elm = $( this.getElmNode( elmId ) );
				if ( $parentElm instanceof $ && $elm.length ) {
					$parentElm[ position ]( $elm );
					// Since we always have custom styles after the elements, when we
					// move the element, we will move the styles if any.
					var $style = $( 'style[data-for="' + elmId + '"]:first', this.$body );
					if ( $style.length ) {
						$elm.after( $style );
					}
					// When moving sections of tabs, move the buttons accordingly
					var parentId = this.getElmParentId( elmId );
					if ( parentId && !! this.isElmTab( parentId ) ) {
						var children = ( this.getElmChildren( parentId ) || [] ).reverse();
						children.map( function( sectionId ) {
							var tabButton = this._getSectionButtonById( sectionId );
							$( tabButton.parentNode ).prepend( tabButton );
						}.bind( this ) );
					}
				}
			},

			/**
			 * Updates the selected element on the page
			 *
			 * @event handler
			 * @param {string} id Shortcode's usbid, e.g. "us_btn:1"
			 * @param {string} html This is the html code of the element and additionally,
			 *						if necessary, the styles in a separate tag after the element.
			 */
			updateSelectedElm: function( id, html ) {
				if ( ! id ) {
					return;
				}
				var node = this.getElmNode( id );
				if ( ! $usbcore.isNode( node ) ) {
					return;
				}

				// Remove style tag by element ID
				this._removeDesignById( id );
				node.outerHTML = '' + html; // Refresh entire node

				// Init its JS if needed
				this.trigger( 'maybeInitElmJS', [ id ] );
				// Update highlight for the element
				this.__setHighlightsPosition();
			},

			/**
			 * Update custom css on the preview page
			 *
			 * @see
			 * @param {string} css The css
			 */
			updatePageCustomCss: function( css ) {
				// Meta key for post custom css
				var keyCustomCss = this.config( 'settings.keyCustomCss', /* Default */'usb_post_custom_css' );

				// Note: Since this is outputed inside the WPBakery Page Builder, we can correct it here.
				var $style = $( 'style[data-type="'+ keyCustomCss +'"]', this.$document );
				if ( ! $style.length )  {
					$style = $( '<style data-type="'+ keyCustomCss +'">' );
					$( 'head', this.$document )
						.append( $style );
				}
				$style.text( css || '' );
			},

			/**
			 * Update element content
			 * Note: This method is only for updating content.
			 *
			 * @param {string|node} selector The selector to find nodes
			 * @param {string} content Text or HTML content to be installed
			 * @param {string} method  Method to be used
			 */
			updateElmContent: function( selector, content, method ) {
				if ( [ 'text', 'html' ].indexOf( method ) === -1 ) {
					method = 'text';
				}
				$( selector, this.$document )[ method ]( '' + content );
			},

			/**
			 * Init its JS if needed
			 *
			 * @param {string} targetId Shortcode's usbid, e.g. "vc_row:1"
			 */
			maybeInitElmJS: function( targetId ) {
				var initMethods = $.isPlainObject( _window.$usbdata.elmsInitJSMethods )
						? _window.$usbdata.elmsInitJSMethods
						: {},
					elmType = this.getElmType( targetId );
				if (
					! $usbcore.isUndefined( initMethods[ elmType ] )
					&& $.isFunction( initMethods[ elmType ] )
				) {
					initMethods[ elmType ]( $( this.getElmNode( targetId ) ) );
				}
			},

			/**
			 * Apply changes to the element
			 *
			 * instruction: `
			 * {
			 * 		'attr': 'html|text|tag|attribute(style|class|...)',
			 * 		'css': '{selectors}',
			 * 		'elm': '{selectors}',
			 * 		'mod': '{name}',
			 * 		'toggle_atts': {
			 * 			'attribute': '{value}',
			 * 			'attribute2': '{value2}',
			 * 		},
			 * 		'toggle_class': '{class name}',
			 * 		'toggle_class_inverse': '{class name}',
			 * 		'design_options': true,
			 * }`
			 * or array instructions: `
			 * [
			 *        {...},
			 *        {...}
			 * ]`
			 *
			 * @event handler
			 * @param {string} targetId Shortcode's usbid, e.g. "us_btn:1"
			 * @param {{}} instructions The are instructions for updating elements
			 * @param {mixed} value The value
			 * @param {string} fieldType Field type
			 */
			onPreviewParamChange: function( targetId, instructions, value, fieldType ) {
				var $target = $( this.getElmNode( targetId ) );
				if ( ! $target.length ) {
					return;
				}
				if ( $usbcore.isUndefined( instructions[ 0 ] ) ) {
					instructions = [ instructions || {} ];
				}

				// If the field type is color and the value has a key, then we get css color variable
				if ( fieldType === 'color' && ( '' + value ).charAt( 0 ) === '_' ) {
					value = this.getColorValue( value );
				}

				for ( var i in instructions ) {
					var instruction = instructions[ i ],
						// Define the element to change
						$elm = ! $usbcore.isUndefined( instruction[ 'elm' ] )
							? $target.find( instruction[ 'elm' ] )
							: $target;

					if ( ! $elm.length ) {
						continue;
					}

					// Changing the class modifier of an element
					if ( ! $usbcore.isUndefined( instruction[ 'mod' ] ) ) {
						var mod = '' + instruction[ 'mod' ],
							pcre = new RegExp( '((^| )'+ this.escapeRegExp( mod ) + '[a-zA-Z0-9\_\-]+)', 'g' );
						// Remove all classes from modifier
						$elm.each( function( _, elm ) {
							elm.className = elm.className.replace( pcre, '' );
						} );
						// Add classes modifiers
						( $.isArray( value ) ? value : [ value ] ).map( function( value ) {
							if ( !! value ) {
								$elm.addClass( mod + '_' + value );
							}
						} );

						// Changing the inline parameter
					} else if ( ! $usbcore.isUndefined( instruction[ 'css' ] ) ) {
						// For the font-family property, check for the presence of global keys `body`, 'h1`, `h2` etc.
						if ( instruction[ 'css' ] === 'font-family' ) {
							// Get the font family from the design options
							value = ( this._designOptions.fontVars || {} )[ value ] || value;
						}
						$elm.css( instruction[ 'css' ], value );

						/*
						 * Ugly hack for Safari compatibility:
						 * since it would not re-render element after changing grid-gap CSS property,
						 * force re-render by changing opacity property
						 */
						if (
							/^((?!chrome|android).)*safari/i.test(navigator.userAgent) // safari detection
							&& instruction[ 'css' ] === 'grid-gap'
						) {
							$elm.css( 'opacity', '0.99' );
							setTimeout( function() {
								$elm.css( 'opacity', '' );
							}, 50 );
						}

						// Changing some attribute (or embedded text, html)
					} else if ( ! $usbcore.isUndefined( instruction[ 'attr' ] ) ) {
						var attr_name = '' + instruction[ 'attr' ];

						switch ( attr_name ) {
							case 'html': // Set html to $elm
								$elm.html( value );
								break;
							case 'text': // Set text to $elm
								$elm.text( value );
								break;
							case 'tag': // Replace tag name in $elm
								$elm.replaceWith( function() {
									var $tag = $( '<' + value + '>' ).html( $( this ).html() );
									for ( var i = this.attributes.length - 1; i >= 0; -- i ) {
										var item = this.attributes[ i ];
										$tag.attr( item.name, item.value );
									}
									return $tag;
								} );
								break;
							case 'class': // Adding a custom class
								$elm
									.removeClass( $elm.data( 'last-classname' ) || '' )
									.addClass( value )
									.data( 'last-classname', value );
								break;
							case 'onclick': // Adding error protection for event values.
								// If there are errors in custom JS, an error message will be displayed
								// in the console, and this will not break the work of the site.
								if ( value ) {
									value = 'try{' + value + '}catch(e){console.error(e)}';
								}
								// Note: no break; here, so default: code is executed too
							default: // Update other attributes
								$elm.attr( attr_name, value );
						}

						// Attribute toggles
					} else if ( ! $usbcore.isUndefined( instruction[ 'toggle_atts' ] ) ) {
						for ( var k in instruction[ 'toggle_atts' ] ) {
							if ( value == true ) {
								// Set attribute
								$elm.attr( k, instruction[ 'toggle_atts' ][ k ] );
							} else {
								// Remove attribute
								$elm.removeAttr( k );
							}
						}

						// Turn on/off css class
					} else if ( ! $usbcore.isUndefined( instruction[ 'toggle_class' ] ) ) {
						$elm.toggleClass( instruction[ 'toggle_class' ], !! value );

						// Turn on/off css class (inverse)
					} else if ( ! $usbcore.isUndefined( instruction[ 'toggle_class_inverse' ] ) ) {
						$elm.toggleClass( instruction[ 'toggle_class_inverse' ], ! value );

						// Compiling and updating design styles
					} else if ( ! $usbcore.isUndefined( instruction[ 'design_options' ] ) ) {
						this._addDesignOptions( targetId, /* jsoncss string */value );

						// The error message
					} else {
						console.log( 'Unknown instruction:', { instruction: instruction, value: value } );
					}
				}

				// Set the highlight position
				this.setHighlightsPosition();
			},

			/**
			 * Called when a new element is added and gets the coordinates of the mouse
			 *
			 * @event handler
			 * @param {string} method The event name
			 * @param {{}} data The mouse event data
			 */
			onParentEventData: function( method, data ) {
				if ( ! method ) {
					return;
				}
				// Determination of the element that is under the coordinates, and obtaining all additional data
				data = $.extend( /* Default */{ eventX: 0, eventY: 0, clientX: 0, clientY: 0, pageX: 0, pageY: 0 }, data || {} );
				data.target = _document.elementFromPoint( data.eventX, data.eventY );
				this.trigger( 'doAction', [ method, data ] );
			},

			/**
			 * This method calls another method that is specified in
			 * the parameters and, if necessary, passes arguments
			 *
			 * @event handler
			 * @param {string} name Method name to run
			 * @param {{}} args Arguments to be passed to the method
			 */
			doAction: function( name, args ) {
				if ( ! name || ! $.isFunction( this[ name ] ) ) {
					return;
				}
				args = args || [];
				this[ name ].apply( this, $.isArray( args ) ? args : [ args ] );
			},

			/**
			 * This handler is called every time the column/column_inner in change
			 * Note: At the moment, the same distribution of space between the columns is implemented
			 *
			 * @event handler
			 * @param {string} rootContainerId Shortcode's usbid, e.g. "vc_row:1", "vc_row_inner:1"
			 */
			vcColumnChange: function( rootContainerId ) {
				var _this = this;
				if ( ! rootContainerId || ! _this.isValidId( rootContainerId ) ) {
					return;
				}
				var columns = _this.getElmChildren( rootContainerId );
				$( columns.map( function( usbid ) { return '[data-usbid="'+ usbid +'"]' } ).join(','), _this.$body )
					.each( function( i, column ) {
						// Get width depending on mesh type Grid/Flex
						var width = '' + _this.getElmValue( columns[i], 'width' );
						if ( _this.config( 'isGridColumnsLayout', /* default */false ) && /(\d+)\/(\d+)/.test( width ) ) {
							var parts = width.split( '/' );
							width = Math.ceil( parts[ /* x */0 ] / parts [ /* y */1 ] * 12 );
						}
						if ( ! width ) {
							return;
						}
						for ( var i = 3; i > -1; i-- ) {
							var prefix = [ 'xs', 'sm', 'md', 'lg' ][ i ],
								matches = ( new RegExp( '(vc_col)-('+ prefix +')-[0-9\\/]+' ) ).exec( column.className );
							if ( ! matches ) {
								continue;
							}
							// TODO: Change the algorithm to calculate the width without changing the already existing columns
							column.className = column.className.replace( matches[0], matches[1] + '-' + prefix + '-' + width );
						}
					}.bind( _this ) );
			}
		}
	} );

	$( function() {
		_window.$usb = new USBuilderPreview;
	} );
}( window.jQuery );
