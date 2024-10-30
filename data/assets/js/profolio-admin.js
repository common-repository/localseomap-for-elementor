jQuery(function ($) {

	let __ = wp.i18n.__;
	let _x = wp.i18n._x;
	let _n = wp.i18n._n;
	let _nx = wp.i18n._nx;

	var profolio_response = '',
			number_projects = 0,
			current_key = 0;

	function import_projects($project, current_key) {

		$.ajax({
			url    : ajaxurl,
			type   : 'POST',
			data   : {
				'action'       : 'profolio_import_projects',
				'project'      : $project,
				'skip_projects': $('#skip_existing_projects').is(':checked')
			},
			success: function (response) {
				let $wrap = $('.import_projects').find('.profolio_import_wrap_count');

				$wrap.find('.current_imported').text(current_key + 1);

				if (current_key === number_projects - 1) {
					$('.import_projects').find('.profolio_import_loader').hide();
					return;
				}

				current_key += 1;
				import_projects(profolio_response[current_key], current_key);

			},
			error  : function (xhr, ajaxOptions, thrownError) {
				let project = profolio_response[current_key];
				let $wrap = $('.import_projects').find('.profolio_import_wrap_count');

				let html_error = '' +
						'<br>' +
						'<span style="color: red">Error:</span><br>' +
						'<span style="color: red">Status +: ' + xhr.status + '</span><br>' +
						'<span style="color: red">Project name: ' + project['name'] + '</span><br>' +
						'<span style="color: red">Project uuid: ' + project['uuid'] + '</span><br>' +
						'-----------<br>';

				$wrap.append(html_error);

				$wrap.find('.current_imported').text(current_key + 1);

				if (current_key == number_projects - 1) {
					$wrap.parent().find('.profolio_import_loader').hide();
					return;
				}
				current_key += 1;
				import_projects(project, current_key);
			},
		});
	}

	$('body').on('click', '.profolio_import_projects', function (e) {
		e.preventDefault();

		let $button = $(this),
				$wrap = $(this).parent();

		$wrap.find('.profolio_import_loader').show();
		$wrap.find('.profolio_import_wrap_count').show();

		$.ajax({
			url     : ajaxurl,
			type    : 'POST',
			dataType: 'json',
			data    : {
				'action': 'profolio_import_projects',
			},
			success : function (response) {

				if (typeof response == 'object') {
					response = Object.entries(response);
					response = response.map(function (el) {
						return el[1] ? el[1] : '';
					});
				}

				if (response.length) {
					number_projects = response.length;

					profolio_response = response;

					$wrap.find('.number_projects').text(number_projects);

					import_projects(response[current_key], current_key);

				}
			},
		});

	});


	$('body').on('click', '.profolio_import_industry', function (e) {
		e.preventDefault();

		let $button = $(this),
				$wrap = $(this).parent();

		$wrap.find('.profolio_import_loader').show();


		$.ajax({
			url     : ajaxurl,
			type    : 'POST',
			context : $wrap.get(0),
			dataType: 'json',
			data    : {
				'action': 'profolio_import_industry',
			},
			success : function (count) {

				if (count === 0) {
					count = __('No categories were added. All data is relevant.', 'localseomap-for-elementor')
				} else {
					count = sprintf(_n('Hurray! %s new category has been added.', 'Hurray! %s new categories have been added.', count, 'localseomap-for-elementor'), count);
				}

				$wrap.find('.current_imported').text(count);
				$wrap.find('.profolio_import_wrap_count').show();
				$wrap.find('.profolio_import_loader').hide();
			},
			error   : function (xhr, ajaxOptions, thrownError) {

				let html_error = '' +
						'<br><br>' +
						'<span style="color: red">Error: ' + xhr.status + '</span><br>' +
						'<span style="color: red">Status: ' + xhr.statusText + '</span><br>' +
						'<br>';

				$(this).append(html_error);

				$(this).find('.profolio_import_loader').hide();

			}
		});

	});


	let profolio_api_use_maps = false;
	window.gm_authFailure = function () {
		$('.profolio_import_loader').hide();
 		$('.profolio_js_api_test').parent().find('.success').hide();
  		$('.profolio_js_api_test').parent().find('.error').show();
	};

	window.profolio_api_test = function () {

		var service = new google.maps.places.AutocompleteService();
		service.getPlacePredictions({
					input: 'Brisbane,Australia',
					types: ['(cities)']
				},
				function (predictions, status) {
					if (status == google.maps.places.PlacesServiceStatus.OK) {
						profolio_api_use_maps = true; /* status is ok so set flag to use Google Maps */
						$('.profolio_import_loader').hide();
 						$('.profolio_js_api_test').parent().find('.error').hide();
 						$('.profolio_js_api_test').parent().find('.success').show();
					}
				});
	}

	$('body').on('click', '.profolio_js_api_test', function (e) {
		e.preventDefault();


		let key = $(this).closest('.cf-field').find('input').val();

		if (!key.length) {
			return;
		}

		$(this).parent().find('.profolio_import_loader').show();

		if ($('#profolio_js_api_map_library').length) {

			$('#profolio_js_api_map_library').attr({
				'src': 'https://maps.googleapis.com/maps/api/js?key=' + key + '&libraries=places,geometry&callback=profolio_api_test'
			});

		} else {

			let $script = $('<script></script>').attr({
				'type': 'text/javascript',
				'id'  : 'profolio_js_api_map_library',
				'src' : 'https://maps.googleapis.com/maps/api/js?key=' + key + '&libraries=places,geometry&callback=profolio_api_test',
			});
			$('head').append($script);

		}


	});


	var current_key = 0;
	$('body').on('click', '.profolio_js_geo_test', function (e) {
		e.preventDefault();

		let key = $(this).closest('.cf-field').find('input').val();

		if (!key.length) {
			return;
		}

		let $error = $('.profolio_js_geo_test').parent().find('.error');
		let $success = $('.profolio_js_geo_test').parent().find('.success');
		let $loader = $('.profolio_js_geo_test').parent().find('.profolio_import_loader');

		$(this).parent().find('.profolio_import_loader').show();

		$.ajax({
			url     : ajaxurl,
			dataType: 'json',
			type    : 'POST',
			data    : {
				'action' : 'localseomap_geo_test',
				'api_key': key,
			},
			success : function (response) {
console.log(response);
				if (response.status === 'OK') {
					$error.hide();
					$success.show();
				} else {
					if (response.error_message) {
						$error.html(response.error_message);
					} else {
						$error.html($error.data('cache'));
					}
					$error.show();
					$success.hide();
				}

				$loader.hide();
			},
			error   : function (xhr, ajaxOptions, thrownError) {

				$loader.hide();
			}
		});

	});


	function add_noindex(current_key, ids, $this) {

		let $error = $this.parent().find('.error');
		let $success = $this.parent().find('.success');
		let $loader = $this.parent().find('.profolio_import_loader');
		let $message_count = $this.parent().find('.profolio_message_count');
		let args = {
			'action': 'localseomap_add_noindex'
		}

		if (ids) {
			if (ids[current_key]) {
				console.log(ids[current_key]);
				args['post_id'] = ids[current_key];
			}
		} else {
			args['get_ids'] = true;
		}

		$.ajax({
			url     : ajaxurl,
			dataType: 'json',
			type    : 'POST',
			data    : args,
			success : function (response) {

				if (response.ids) {
					add_noindex(current_key, response.ids, $this);

					$message_count.html(response.message_label + response.ids.length);
					return;
				}

				current_key++;

				$message_count.html(response.message_label + current_key + '/' + ids.length);

				if (ids && ids.length == current_key) {
					if (response.status === 'OK') {
						$error.hide();
						$success.show();
					} else {
						if (response.error_message) {
							$error.html(response.error_message);
						} else {

						}
					}

					$loader.hide();
					return;
				}


				add_noindex(current_key, ids, $this);

			},
			error   : function (xhr, ajaxOptions, thrownError) {

				$loader.hide();
			}
		});
	}

	$('body').on('click', '.profolio_js_add_noindex', function (e) {
		e.preventDefault();

		let $this = $('.profolio_js_add_noindex');

		$this.parent().find('.profolio_import_loader').show();

		add_noindex(0, null, $this);
	});

});
