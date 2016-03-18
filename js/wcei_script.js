jQuery(document).ready(function () {

	// eBay Image Gallery/Uploads 

	var ebay_gallery_frame;
	var $ebay_image_gallery_ids = jQuery( '#ebay_image_gallery' );
	var $ebay_images    = jQuery( '#ebay_images_container' ).find( 'ul.ebay_images' );

	jQuery( '.add_ebay_images' ).on( 'click', 'a', function( event ) {
		var $el = jQuery( this );

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( ebay_gallery_frame ) {
			ebay_gallery_frame.open();
			return;
		}

		// Create the media frame.
		ebay_gallery_frame = wp.media.frames.ebay_gallery = wp.media({
			// Set the title of the modal.
			title: $el.data( 'choose' ),
			button: {
				text: $el.data( 'update' )
			},
			states: [
				new wp.media.controller.Library({
					title: $el.data( 'choose' ),
					filterable: 'all',
					multiple: true
				})
			]
		});

		// When an image is selected, run a callback.
		ebay_gallery_frame.on( 'select', function() {
		var selection = ebay_gallery_frame.state().get( 'selection' );
		var attachment_ids = $ebay_image_gallery_ids.val();

		selection.map( function( attachment ) {
			attachment = attachment.toJSON();

			if ( attachment.id ) {
				attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
				var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

				$ebay_images.append( '<li class="ebay_image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete tips" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
			}
		});

		$ebay_image_gallery_ids.val( attachment_ids );
		});

		// Finally, open the modal.
		ebay_gallery_frame.open();
	});
	
	// Remove images
	jQuery( '#ebay_images_container' ).on( 'click', 'a.delete', function() {
		jQuery( this ).closest( 'li.ebay_image' ).remove();

		var attachment_ids = '';

		jQuery( '#ebay_images_container' ).find( 'ul li.ebay_image' ).css( 'cursor', 'default' ).each( function() {
			var attachment_id = jQuery( this ).attr( 'data-attachment_id' );
			attachment_ids = attachment_ids + attachment_id + ',';
		});

		$ebay_image_gallery_ids.val( attachment_ids );

		// remove any lingering tooltips
		jQuery( '#tiptip_holder' ).removeAttr( 'style' );
		jQuery( '#tiptip_arrow' ).removeAttr( 'style' );

		return false;
	});

	// Image ordering
	$ebay_images.sortable({
		items: 'li.ebay_image',
		cursor: 'move',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		forceHelperSize: false,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
		},
		update: function() {
			var attachment_ids = '';

			jQuery( '#ebay_images_container' ).find( 'ul li.ebay_image' ).css( 'cursor', 'default' ).each( function() {
				var attachment_id = jQuery( this ).attr( 'data-attachment_id' );
				attachment_ids = attachment_ids + attachment_id + ',';
			});

			$ebay_image_gallery_ids.val( attachment_ids );
		}
	});
})