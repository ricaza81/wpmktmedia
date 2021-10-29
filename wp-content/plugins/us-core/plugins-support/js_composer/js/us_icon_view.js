! function( $ ) {
	window.ViewUsIcon = vc.shortcode_view.extend( {
		// Render method called after element is added( cloned ), and on first initialisation
		render: function() {
			window.ViewUsIcon.__super__.render.call( this );
			this.addedClasses = '';
			return this;
		},
		// Add either FontAwesome of Material icons
		changeIcons: function() {
			var $icon = this.$el.find( '.vc_element-icon' ),
				$button = this.$el.find( 'button' ),
				$addedIcon = '',
				shortcode = this.currentShortcode,
				iconPos = this.model.attributes.params.iconpos,
				matchesFa,
				matchesMa;

			// Catch icon names
			matchesFa = this.model.attributes.params.icon.match( /^(fa\w?)\|(.+)$/ );
			matchesMa = this.model.attributes.params.icon.match( /^(material)\|(.+)$/ );

			// Do only for found elements
			if ( $icon.length || $button.length ) {
				if ( matchesFa || matchesMa ) {
					if ( this.addedClasses.length ) {
						$icon.removeClass( this.addedClasses ).html( '' );
					}
					if ( matchesFa ) {
						if ( shortcode == 'us_iconbox' ) {
							this.addedClasses = matchesFa[ 1 ] + ' fa-' + matchesFa[ 2 ];
							$icon.addClass( this.addedClasses );
						} else if ( shortcode == 'us_btn' ) {
							$addedIcon = '<i class="' + matchesFa[ 1 ] + ' fa-' + matchesFa[ 2 ] + '"></i>';
							if ( iconPos == 'left' ) {
								$( $addedIcon ).prependTo( $button );
							} else {
								// Remove new line from the title to append icon properly
								$button.html( $button.html().trim() );
								$( $addedIcon ).appendTo( $button );
							}
						}
					} else {
						if ( shortcode == 'us_iconbox' ) {
							this.addedClasses = 'material-icons';
							$icon.addClass( this.addedClasses ).html( matchesMa[ 2 ] );
						} else if ( shortcode == 'us_btn' ) {
							$addedIcon = '<i class="material-icons">' + matchesMa[ 2 ] + '</i>';
							if ( iconPos == 'left' ) {
								$( $addedIcon ).prependTo( $button );
							} else {
								// Remove new line from the title to append icon properly
								$button.html( $button.html().trim() );
								$( $addedIcon ).appendTo( $button );
							}
						}
					}
				}
			}
		},
		// Add default title if empty
		checkButtonTitle: function() {
			this.currentShortcode = this.model.attributes.shortcode;
			if ( this.currentShortcode != 'us_btn' ) {
				return;
			}
			if ( this.currentShortcode == 'us_btn' ) {
				if ( ! this.model.attributes.params.label.length && ! this.model.attributes.params.icon.length ) {
					$( this.$el.find( 'button' ) ).html( '<p>' + this.params.label.std + '</p>' );
				}
			}
		},
		ready: function( e ) {
			window.ViewUsIcon.__super__.ready.call( this, e );

			return this;
		},

		// Called every time when params is changed/appended. Also on first initialisation
		changeShortcodeParams: function( model ) {
			window.ViewUsIcon.__super__.changeShortcodeParams.call( this, model );
			this.checkButtonTitle();
			this.changeIcons();
		},
		deleteShortcode: function( e ) {
			this.addedClasses = '';
			window.ViewUsIcon.__super__.deleteShortcode.call( this, e );
		},
		editElement: function( e ) {
			window.ViewUsIcon.__super__.editElement.call( this, e );
		},
		clone: function( e ) {
			window.ViewUsIcon.__super__.clone.call( this, e );
		}
	} );
}( window.jQuery );
