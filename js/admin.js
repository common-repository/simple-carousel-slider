jQuery(document).on("click", ".custom_media_upload", function(e) {
	e.preventDefault();
	var custom_uploader = wp.media({
		title: 'Select Image to Use',
		button: {
			text: 'Use Selected Image',
		},
		multiple: false  // Set this to true to allow multiple files to be selected
	})
	.on('select', function() {
		var attachment = custom_uploader.state().get('selection').first().toJSON();
		jQuery('.custom_media_image').attr('src', attachment.url);
		jQuery('.custom_media_url').val(attachment.id);
	})
	.open();
});	