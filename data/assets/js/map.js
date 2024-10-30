/**
 *
 * Callback for Google Maps.
 **/

jQuery(function ($) {

	var activeInfoWindow;

	function create_marker(map_core, params, location) {

		var latlng = {lat: Number(location.lat), lng: Number(location.lng)};

		let marker_options = {
			map     : map_core,
			position: latlng,
		};

		if (params.marker) {
			marker_options.icon = params.marker;
		}
		var template = wp.template('profolio-infowindow-template');
		var infowindow = new google.maps.InfoWindow({
			content: template(location),
		});

		var marker = new google.maps.Marker(marker_options);
		marker.addListener('mouseover', function () {
			if (activeInfoWindow) {
				activeInfoWindow.close();
			}
			infowindow.open(map_core, marker);

			activeInfoWindow = infowindow;
		});

		marker.addListener('mouseleave', function () {
			if (activeInfoWindow) {
				activeInfoWindow.close();
			}
		});

		marker.addListener('click', function () {
			if ($(window).width() <= 767) {
				if (activeInfoWindow) {
					activeInfoWindow.close();
				}
				infowindow.open(map_core, marker);

				activeInfoWindow = infowindow;
			} else {
				if (activeInfoWindow) {
					infowindow.close();
				}
			}
		});

		google.maps.event.addListener(map_core, 'click', function (event) {
			infowindow.close();
		});

		google.maps.event.addListener(infowindow, 'domready', function () {
			let $image = $('.profolio-map-pop img');
			let src = $image.data('src');
			let srcset = $image.data('srcset');
			if (src) {
				$('.profolio-map-pop img').attr({
					'src'   : src,
					'srcset': srcset
				});
				$image.removeAttr('data-src');
				$image.removeAttr('data-srcset');
			}
		});

		return marker;

	}

	function randomInteger(min, max) {
		let rand = min + Math.random() * (max - min);
		return parseInt(Math.round(rand));
	}

	function proximate_loc(location) {

		if (window.localseomap_object.type_address == 'general') {

			let rand_meter = window.localseomap_object.rand_meter,
					rand_coef = parseFloat(rand_meter) * 0.0000089;

			location.lat = parseFloat(location.lat) + (rand_coef / Math.cos(parseFloat(location.lng) * 0.018));
			location.lng = parseFloat(location.lng) + rand_coef;

		}

		return location;
	}

	window.profolio_init_map = function (data_object) {
		var map_attr = data_object !== undefined ? data_object : undefined;

		if (typeof window.google === 'undefined') {
			return;
		}

		/* For map */
		var geocoder = new google.maps.Geocoder();

		$('.JS_profolio_map').each(function () {

			if (!window.profolio_addon_pro[this.id]) {
				return;
			}

			let map_params = JSON.parse(atob(window.profolio_addon_pro[this.id]));

			var core_options = {
				zoom: 8,
			};

			if (!map_params.zoom_fit) {
				core_options.center = new google.maps.LatLng(34.052235, -118.243683);
			}

			if (map_params.zoom) {
				core_options.zoom = parseInt(map_params.zoom);
			}

			if (map_params.scrollwheel) {
				core_options.scrollwheel = true;
			}

			// select type map
			if (map_params.select_map_types) {
				core_options.mapTypeId = map_params.select_map_types;
			}

			core_options.mapTypeControl = false;
			core_options.scaleControl = false;
			core_options.fullscreenControl = false;
			core_options.rotateControl = false;
			core_options.zoomControl = false;

			if (map_params.map_type_control) {
				core_options.mapTypeControl = true;
			}

			if (map_params.scale_control) {
				core_options.scaleControl = true;
			}

			if (map_params.fullscreen_control) {
				core_options.fullscreenControl = true;
			}

			if (map_params.rotate_control) {
				core_options.rotateControl = true;
			}

			if (map_params.zoom_control) {
				core_options.zoomControl = true;
			}

			if (!map_params.enable_default_ui) {
				core_options.mapTypeControl = false;
				core_options.scaleControl = false;
				core_options.fullscreenControl = false;
				core_options.rotateControl = false;
				core_options.zoomControl = false;
			}

			core_options.streetViewControl = false;

			var map_core = new google.maps.Map(this, core_options);

			if (map_attr !== undefined && map_attr.lat && map_attr.lng) {
				var cityCircle = new google.maps.Circle({
					strokeColor  : '#FF0000',
					strokeOpacity: 0.6,
					strokeWeight : 2,
					fillColor    : '#FF0000',
					fillOpacity  : 0.25,
					map          : map_core,
					center       : {lat: map_attr.lat, lng: map_attr.lng},
					radius       : map_attr.radius * 1609.344,
				});

				map_params.location = map_attr.location;
			}

			map_core.setTilt(0);
			// 45* imagery enable
			if (map_params.imagery_45) {
				map_core.setTilt(45);
			}

			if (map_params.location && !map_params.zoom_fit) {
				geocoder.geocode({'address': map_params.location}, function (results, status) {
					if (status === 'OK') {
						map_core.setCenter(results[0].geometry.location);
					} else {
						console.error('Geocode was not successful for the following reason: ' + status);
					}
				});
			}

			var bounds = new google.maps.LatLngBounds();

			window.profolio_init_markers = {};
			if ('current_page' === map_params.number_projects) {
				if (window.localseomap_ajax_locations) {
					if (Array.isArray(window.localseomap_ajax_locations)) {
						map_params.locations = window.localseomap_ajax_locations;
					} else {
						map_params.locations = JSON.parse(atob(window.localseomap_ajax_locations));
					}
				}
			}

			console.log(map_params);
			//if ( 'current_page' === map_params.number_projects && window.localseomap_ajax_locations ) {

			/*} else {
				map_params.locations = window.localseomap_ajax_locations !== undefined ? window.localseomap_ajax_locations : map_params.locations;
			}*/

			if (window.localseomap_object.type_address != 'exact') {
				$.each(map_params.locations, function (index, location) {
					location = proximate_loc(location);
					map_params.locations[index].lat = location.lat;
					map_params.locations[index].lng = location.lng;
				});
			}

			$.each(map_params.locations, function (index, location) {

				if (!location.lat || !location.lng) {
					return;
				}

				var marker = create_marker(map_core, map_params, location);
				bounds.extend(marker.position);

				window.profolio_init_markers[location.lat + ',' + location.lng] = marker;

			});

			if (map_params.locations && map_params.zoom_fit) {
				map_core.fitBounds(bounds);
			}

		});

	};

	if (window.elementor) {
		elementor.hooks.addAction('panel/open_editor/widget/profolio_map', function (panel, model, view) {

			view.onRender = function () {
				setTimeout(function () {
					profolio_init_map();
				}, 500);
			};

		});
	}

	// profolio_init_map();
	if (window.elementorFrontend && elementorFrontend.hooks) {

		elementorFrontend.hooks.addAction('frontend/element_ready/global', function (panel, model, view) {
			profolio_init_map();
		});

		elementorFrontend.hooks.addAction('panel/open_editor/widget/map', function (panel, model, view) {

			view.onRender = function () {
				profolio_init_map();
			};

		});

	}
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/map.default', function ($scope, $) {
			//profolio_init_map();
		});

	});

	$(window).on('load', function () {
		profolio_init_map();
	});

	/*==========================
	 Show/Hide info window
	 ==========================*/
	function show_info_window() {
		$('.JS_profolio_project_item').on('mouseenter', function () {
			var $this = $(this),
					latlong = $this.data('latlong');

			if (!latlong.length) {
				return false;
			}

			if (window.profolio_init_markers && latlong) {

				var location = {
					lat: latlong.split(',')[0],
					lng: latlong.split(',')[1],
				};
				location = proximate_loc(location);

				latlong = location.lat.toString() + ',' + location.lng.toString();

				var marker = window.profolio_init_markers[latlong];

				if (marker !== 'undefined' && typeof window.google !== 'undefined') {
					new google.maps.event.trigger(marker, 'mouseover');
				}
			}
		});

		$('.JS_profolio_project_item').on('mouseleave', function () {
			var $this = $(this),
					latlong = $this.data('latlong');

			var location = {
				lat: latlong.split(',')[0],
				lng: latlong.split(',')[1],
			};
			location = proximate_loc(location);

			latlong = location.lat.toString() + ',' + location.lng.toString();

			if (window.profolio_init_markers && latlong) {
				var marker = window.profolio_init_markers[latlong];
				if (marker !== 'undefined' && typeof window.google !== 'undefined') {
					new google.maps.event.trigger(marker, 'mouseleave');
				}
			}
		});
	}

	show_info_window();

	/*==========================
	 Change Placeholder Text
	 ==========================*/
	var locationText = 'Enter a location',
			radiusText = '100 mile(s)';

	function add_filter_value(input, value_text) {
		input.on('focus', function () {
			remove_active_class_category();

			if ($(this).val() === value_text) {
				$(this).val('');
			}
		});

		input.on('blur', function () {
			if ($(this).val() === '') {
				$(this).val(value_text);
			}
		});
	}

	add_filter_value($('.profolio-loc-input'), locationText);
	add_filter_value($('#profolio-radius-input'), radiusText);

	/*====================================
	 Remove Active Class Category
	 ====================================*/
	function remove_active_class_category() {
		var category_frame = $('.profolio-input-frame');

		if (category_frame.find('.profolio-search-dropdown').hasClass('profolio-search-dropdown-active')) {
			category_frame.find('.profolio-search-dropdown').removeClass('profolio-search-dropdown-active');
		}
	}

	/*====================
	 Profolio Filters
	 ====================*/
	function get_projects(data_object) {

		/*var get_map_id = $( '.JS_profolio_map' ).attr( 'id' );

		 if ( get_map_id === undefined ) {
		 return false;
		 }

		 if ( !window.profolio_addon_pro[ get_map_id ] ) {
		 return;
		 }*/

		/*let map_params = JSON.parse( atob( window.profolio_addon_pro[ get_map_id ] ) );

		 if ( 'current_page' === map_params.number_projects ) {
		 data_object.ids = window.profolio_project_ids;
		 }
		 */
		if (!data_object.posts_per_page) {
			data_object.posts_per_page = window.projects_per_page;
		}

		var loaderImg = '<div class="rp-cart-loader"><img src="' + localseomap_object.directoryurl + '/assets/img/ajax-loader.gif" /></div>';
		$('.js_profolio_project').append(loaderImg);

		$.ajax({
			url    : localseomap_object.ajaxurl,
			type   : 'POST',
			data   : data_object,
			success: function (response) {
				console.timeEnd('before_get_projects');

				// remove loader
				$('.rp-cart-loader').remove();

				// add new projects
				$('.js_profolio_project').html(response);


				// reinit map
				profolio_init_map(data_object);
				profolio_map_fixed_map();
				show_info_window();
			},
		});

		return false;
	}

	$('.js-profolio-search-form').on('submit', function (e) {
		e.preventDefault();

		remove_active_class_category();

		var saleIds = [];
		$.each($("input.js-form-sale-check-input:checked"), function () {
			saleIds.push($(this).val());
		});

		var statusIds = [];
		$.each($("input.js-form-status-check-input:checked"), function () {
			statusIds.push($(this).val());
		});

		var $profolio_list = $('.js_profolio_project');

		var inputWrapper = $(this).find('.profolio-search-dropdown'),
				filterItems = inputWrapper.find('.js-form-check-input'),
				location = $('.profolio-loc-input').val(),
				current_id = $('.profolio-current-post-id').val(),
				radius = $('#profolio-radius-input').val(),
				tags_instead_industry = this.tags_instead_industry,
				categoryIds = [],
				data_object = {
					'action'               : 'projects_filter',
					'categoryIds'          : categoryIds,
					'current_id'           : current_id,
					'tags_instead_industry': tags_instead_industry.value,
					'saleIds'              : saleIds,
					'statusIds'            : statusIds,
					'profolio_list_atts'   : $profolio_list.data('atts')
				};

		if (this.pro_project) {
			data_object['pro_project'] = this.pro_project.value;
		}

		if (radius === radiusText) {
			radius = 100;
		}

		if (radius !== '') {
			data_object['radius'] = radius;
		}

		filterItems.each(function () {
			if ($(this).not('.profolio-sellect-all').is(':checked')) {
				categoryIds.push($(this).val());
			}
		});

		if (location !== '' && location !== locationText && radius !== radiusText) {

			var geocoder = new google.maps.Geocoder();

			geocoder.geocode({'address': location}, function (results, status) {
				if (status === 'OK') {
					data_object['lat'] = results[0].geometry.location.lat();
					data_object['lng'] = results[0].geometry.location.lng();
					data_object['location'] = location;

					get_projects(data_object);
				}
			});
		} else {
			get_projects(data_object);
		}
	});

	/*====================
	 Reset projects
	 ====================*/
	$('body').on('click', '.js-reset-projects', function () {
		$('.js-form-check-input').prop('checked', false);
		$('.profolio-loc-input').val('');
		$('#profolio-radius-input').val('');

		get_projects({'action': 'projects_filter', 'reset': 'yes'});
	});

	/*====================
	 Ajax pagination
	 ====================*/
	/*$( 'body' ).on( 'click', '.JS_profolio_pagination .page-numbers', function ( e ) {

	 e.preventDefault();

	 get_projects( { 'action': 'projects_filter', 'projects_per_page': window.projects_per_page } );
	 } );*/

	/*====================
	 Fixed Map
	 ====================*/
	$(window).on('load', function () {

		var map_element = $('.JS_profolio_map');

		if (map_element.length) {
			var is_responsive = false,
					is_fixed,
					is_bottom,
					parent_witdh = map_element.parent().width(),
					column = map_element.closest('.elementor-column, .project-map-column');


			map_element.parent().css({
				'overflow': 'hidden',
			});

			map_element.css({
				'width': parent_witdh,
			});

			if ($(window).width() <= 1024) {
				is_responsive = true;
			} else if ($(window).width() >= 768 && $(window).width() <= 1024) {
				map_element.closest('.elementor-widget-map').css('height', '100%');
				map_element.closest('.elementor-widget-container').css('height', '100%');
				map_element.closest('.profolio-map-wrapper').css('height', '100%');
				map_element.css('height', '100%');
			} else {
				is_responsive = false;
			}
		}

		window.profolio_map_fixed_map = function () {

			var cur_pos_top = $(document).scrollTop();
			var cur_pos_bot = cur_pos_top + $(window).height(),
					parent_witdh = map_element.parent().width(),
					header_height = column.offset().top,
					column_pos_bottom = header_height + column.height();

			if (!is_responsive) {

				is_fixed = false;
				is_bottom = false;

				if (cur_pos_top >= header_height) {
					if (cur_pos_bot <= column_pos_bottom) {
						is_fixed = true;
					} else {
						is_fixed = false;
						is_bottom = true;
					}
				} else {
					is_fixed = false;
				}

				if (is_fixed) {

					map_element.addClass('fixed');

					map_element.css({
						'width': parent_witdh,
					});

					map_element.closest('.elementor-widget-map').css('height', '100%');
					map_element.closest('.elementor-widget-container').css('height', '100%');
					map_element.closest('.profolio-map-wrapper').css('height', '100%');
					map_element.closest('.profolio-map-wrapper').css('height', '100%');
				} else {

					map_element.removeClass('fixed bottom');
					if (is_bottom) {
						map_element.addClass('bottom');
					}

				}

			}
		}

		var get_map_id = $('.JS_profolio_map').attr('id');
		if (get_map_id && window.profolio_addon_pro[get_map_id]) {

			let map_params = JSON.parse(atob(window.profolio_addon_pro[get_map_id]));

			$(document).on('scroll', function () {
				if (map_element.length && (map_params.fullheight === 'yes' || map_params.fullheight === '1')) {
					profolio_map_fixed_map();
				}
			});
		}

		$(window).on('resize', function () {
			if ($(window).width() <= 1024) {
				is_responsive = true;
			} else if ($(window).width() >= 768 && $(window).width() <= 1024) {
				map_element.closest('.elementor-widget-map').css('height', '100%');
				map_element.closest('.elementor-widget-container').css('height', '100%');
				map_element.closest('.profolio-map-wrapper').css('height', '100%');
				map_element.css('height', '100%');
			} else {
				is_responsive = false;
			}
		});
	});

	$('.profolio-default-button.reset-filter').on('click', function () {
		/*$('.js-profolio-search-form').find('input').prop('checked', false);
		$('.js-profolio-search-form').find('input').val('');*/

		$('.js-form-check-input').prop('checked', false);
		$('.profolio-loc-input').val('');
		$('#profolio-radius-input').val('');

		get_projects({'action': 'projects_filter', 'reset': 'yes'});
	});

});
