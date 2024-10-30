jQuery(function ($) {

	$('#profolio-addon-pro-uuid, #profolio-addon-pro-project_uuid').parent().addClass('disabled');

	function show_hide_metaboxes(value) {

		if (value == 'profolio-project-template.php') {
			$('#the-project-list-settings').show();
			$('#the-map-setting').show();
		} else {
			$('#the-project-list-settings').hide();
			$('#the-map-setting').hide();
		}
	}

	show_hide_metaboxes();

	$('body').on('change', '#inspector-select-control-1', function () {
		show_hide_metaboxes(this.value);
	});

	if ( jQuery('#post_ID').length && wp && wp.api ) {
		var post = new wp.api.models.Page({id: jQuery('#post_ID').val()});
		post.fetch().done(function (data) {
			show_hide_metaboxes(data.template);
		});
	}

});
