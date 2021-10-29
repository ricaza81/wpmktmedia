window.wp = window.wp || {};

( function( $ ) {

	var media = wp.media,
		curAttachmentsBrowser = media.view.AttachmentsBrowser;

	media.view.AttachmentFilters.Taxonomy = media.view.AttachmentFilters.extend( {
		tagName: 'select',
		createFilters: function() {
			var filters = {},
				that = this;

			_.each( that.options.termList || {}, function( term, key ) {
				var term_id = term[ 'term_id' ],
					term_name = $( '<div/>' ).html( term[ 'term_name' ] ).text();

				filters[ term_id ] = {
					text: term_name,
					priority: key + 2
				};

				filters[ term_id ][ 'props' ] = {};
				filters[ term_id ][ 'props' ][ that.options.taxonomy ] = term_id;
			} );

			filters.all = {
				text: that.options.termListTitle,
				priority: 1
			};

			filters[ 'all' ][ 'props' ] = {};
			filters[ 'all' ][ 'props' ][ that.options.taxonomy ] = null;

			this.filters = filters;
		}
	} );

	media.view.AttachmentsBrowser = media.view.AttachmentsBrowser.extend( {
		createToolbar: function() {
			var that = this,
				i = 1,
				filters = that.options.filters;

			curAttachmentsBrowser.prototype.createToolbar.apply( this, arguments );

			$.each( us_media_categories_taxonomies, function( taxonomy, values ) {
				if ( values.term_list && filters ) {
					that.toolbar.set( taxonomy + '-filter', new media.view.AttachmentFilters.Taxonomy( {
						controller: that.controller,
						model: that.collection.props,
						priority: - 80 + 10 * i ++,
						taxonomy: taxonomy,
						termList: values.term_list,
						termListTitle: values.list_title,
						className: 'attachment-filters for_us_media'
					} ).render() );
				}
			} );
		}
	} );

	$.extend( wp.Uploader.prototype, {
		success: function( file_attachment ) {
			var category = $( ".attachment-filters.for_us_media" ).val();
			var data = {
				action: 'us_ajax_set_category_on_upload',
				post_id: file_attachment.attributes.id,
				category: category,
			};

			jQuery.post( ajaxurl, data, function( response ) {

			} );

		}
	} );

} )( jQuery );
