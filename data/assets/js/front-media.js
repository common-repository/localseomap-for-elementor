/**
 * @Script: WordPress Multiple Image Selection in jQuery
 * @Version: 0.1
 * @Author: CK MacLeod
 * @Author URI: http://ckmacleod.com
 * @License: GPL3
 */

jQuery(function ($) {

	let localseomap_media_upload;

	let ids = [];

	function toggleButton($el, ids) {

		if (ids.length) {
			$el.parent().find('.profolio-main-img-btn').addClass('hidden');
		} else {
			$el.parent().find('.profolio-main-img-btn').removeClass('hidden');
		}
	}

	$('.profolio_add_images').on('click', function (e) {

		e.preventDefault();

		// If the uploader object has already been created, reopen the dialog
		let $button = $(this),
				multiple = $button.data('multiple'),
				field_name = $button.data('name');

		$button.closest('.media-field').addClass('active');

		// Extend the wp.media object
		localseomap_media_upload = wp.media.frames.file_frame = wp.media({

			//button_text set by wp_localize_script()
			title   : profolio_front_media_button.title,
			button  : {text: profolio_front_media_button.button},
			multiple: multiple, //allowing for multiple image selection
		});

		localseomap_media_upload.on('open', function () {

			var selection = localseomap_media_upload.state().get('selection'),
					attachment;

			if (!ids.length) {
				ids = $('#profolio_project_gallery_ids_tmp').val().split(',');
			}

			ids.forEach(function (id) {
				attachment = wp.media.attachment(id);
				attachment.fetch();
				selection.add(attachment ? [attachment] : ['']);
			});

			if (!multiple) {
				/* show/hide button*/
				toggleButton($button, ids);
			}


		});

		/**
		 *When multiple images are selected, get the multiple attachment objects
		 *and convert them into a usable array of attachments
		 */
		localseomap_media_upload.on('select', function () {

			var attachments = localseomap_media_upload.state().get('selection').map(
					function (attachment) {

						attachment.toJSON();
						return attachment;

					});

			// remove all preview
			$('.media-field.active').find('.profolio-media-preview').html('');


			if (!attachments.length) {
				return;
			}


			//loop through the array and do things with each attachment
			for (let i = 0; i < attachments.length; ++i) {

				let image_id = attachments[i].id;

				if (attachments[i].attributes.sizes) {
					image_url = attachments[i].attributes.sizes.thumbnail.url;
				} else {
					image_url = attachments[i].attributes.thumb.src;
				}

				let title = '';
				if (attachments[i].attributes.title) {
					title = attachments[i].attributes.title;
				}

				let gallery_item = {
					image_id  : image_id,
					image_url : image_url,
					title     : title,
					field_name: field_name
				}

				let template = wp.template('localseomap-gallery-item');
				//sample function 1: add image preview
				$('.media-field.active').find('.profolio-media-preview').append(template(gallery_item));

				ids.push(image_id);

				if (!multiple) {
					break;
				}

			}

			if (!multiple) {
				/* show/hide button*/
				toggleButton($button, ids);
			}

			$('.media-field').removeClass('active');

		});

		localseomap_media_upload.open();

	});

	/**
	 * Delete the image.
	 * */
	$('.media-field').on('click', '.profolio-close-img', function (e) {

		e.preventDefault();

		let $img = $(this),
				$wrap = $img.parent('.profolio-pre-img');

		for (var i = 0; i < ids.length; i++) {
			if ($wrap.find('input').val() == ids[i]) {
				ids.splice(i, 1);
			}
		}
		let $button = $img.closest('.media-field').find('.profolio-main-img-btn');
		toggleButton($button, []);


		$wrap.remove();

	});

});
