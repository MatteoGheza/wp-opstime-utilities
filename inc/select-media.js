/**
 * Load media uploader on pages with our custom metabox
 */
 jQuery(document).ready(function($){

	'use strict';

	// Instantiates the variable that holds the media library frame.
	var metaImageFrame;

	// Runs when the media button is clicked.
	$( 'body' ).click(function(e) {

		// Get the btn
		var btn = e.target;

		// Check if it's the upload button
		if ( !btn || !$( btn ).attr( 'data-media-uploader-target' ) ) return;

		// Get the field target
		var field = $( btn ).data( 'media-uploader-target' );

		var uploadNotice = $( btn ).data( 'media-upload-notice' );
		var filenameLabel = $( btn ).data( 'media-filename-label' );
		var mediaUrl = $( btn ).data( 'media-url' );

		// Prevents the default action from occuring.
		e.preventDefault();

		// Sets up the media library frame
		metaImageFrame = wp.media.frames.metaImageFrame = wp.media({
			title: 'Seleziona un documento da caricare',
			button: { text: 'Usa questo file' },
			multiple: false,
			library: {
				type: 'application/pdf'
			}
		});

		// Runs when an image is selected.
		metaImageFrame.on('select', function() {

			console.log(metaImageFrame, metaImageFrame.state().get('selection').first().toJSON());

			// Grabs the attachment selection and creates a JSON representation of the model.
			var media_attachment = metaImageFrame.state().get('selection').first().toJSON();

			// Sends the attachment URL to our custom image input field.
			$( field ).val(media_attachment.url);

			$( uploadNotice ).hide();
			$( filenameLabel ).text(`Nome file: ${media_attachment.title}`);
			$( mediaUrl ).text("URL Documento caricato (premi per aprire in nuova scheda)");
			$( mediaUrl ).attr("href", media_attachment.url);

		});

		// Opens the media library frame.
		metaImageFrame.open();

	});

});