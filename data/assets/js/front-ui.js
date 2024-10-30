jQuery(function ($) {

	if (typeof window.google === 'undefined') {
		return;
	}

	$(".profolio-input-more").on('click', function (e) {

		e.preventDefault();

		let $this = $(this),
				$parent = $this.parent();

		//$this.toggleClass('active');
		$parent.find('.profolio-more-popup').toggleClass('active');


		/*$(".elementor-element-b313469 #profolio-input-more").toggleClass('active');
		$('.elementor-element-b313469 #more-js').toggleClass('click');*/

	});

	/*====================
	Dropdown of the filter init
	====================*/
	$('html').on('click', function (e) {
		var div = $('.more-button-container');


		if (!div.is(e.target) && div.has(e.target).length === 0 && div.find('.more-button-container')) {

			$('.profolio-more-popup').removeClass('active');
			//$('.profolio-input-more').removeClass('active');

		}
	});


	$('.swiper-thumb').on('click', function () {
		$('.profolio-slider-frame').addClass('profolio-show-slider');
	});

	$('.profolio-close-btn').on('click', function () {
		$('.profolio-slider-frame').removeClass('profolio-show-slider');
	});

	$('.profolio-sellect-all').change(function () {
		var checkboxes = $(this).closest('.profolio-search-dropdown').find(':checkbox');
		checkboxes.prop('checked', $(this).is(':checked'));
		$('.profolio-d-all').toggleClass('profolio-d-true');
		$('.profolio-d-none').toggleClass('profolio-d-true');
	});

	$('.profolio-open-dropdown').on('click', function () {
		var parent = $(this).closest('.profolio-input-frame');

		var isActive = parent.find('.profolio-search-dropdown').hasClass('profolio-search-dropdown-active') ? true : false;
		console.log(isActive)

		$('.profolio-search-dropdown').removeClass('profolio-search-dropdown-active');


		if(!isActive) {
			parent.find('.profolio-search-dropdown').toggleClass('profolio-search-dropdown-active')
		}

	});


	function profolio_initialize_form_map() {

		var input = $('.profolio-loc-input').get(0);
		if (input) {

			var autocomplete = new google.maps.places.Autocomplete(input);

			google.maps.event.addListener(autocomplete, 'place_changed', function () {
				var place = autocomplete.getPlace();
				var components = place.address_components;

				var city = null;
				var county = null;
				var province = null;
				var country = null;
				for (var i = 0, component; component = components[i]; i++) {

					if (component.types[0] == 'neighborhood') {
						city = component['long_name'];
					}

					if (component.types[0] == 'administrative_area_level_2') {
						county = component['long_name'];
					}
					if (component.types[0] == 'administrative_area_level_1') {
						province = component['short_name'];
					}
					if (component.types[0] == 'country') {
						country = component['short_name'];
					}
				}

				if (city) {
					$('#profolio-loc-city').val(city);
				}

				if (county) {
					$('#profolio-loc-county').val(county);
				}

				if (province) {
					$('#profolio-loc-province').val(province);
				}

				if (country) {
					$('#profolio-loc-country').val(country);
				}

				$('#profolio-loc-lat').val(place.geometry.location.lat());
				$('#profolio-loc-lng').val(place.geometry.location.lng());
			});
		}
	}

	google.maps.event.addDomListener(window, 'load', profolio_initialize_form_map);

	/*====================
	 Swiper Sliders
	 ====================*/
	if ($('.swiper-thumb').length) {
		var galleryThumbs = new Swiper('.swiper-thumb', {
			spaceBetween         : 10,
			slidesPerView        : 4,
			watchSlidesVisibility: true,
			watchSlidesProgress  : true,
			breakpoints          : {
				992: {
					slidesPerView: '3',
				},
				767: {
					slidesPerView: '2',
				},
			},
		});
	}

	if ($('.swiper-zoom').length) {
		var galleryZoom = new Swiper('.swiper-zoom', {
			spaceBetween: 10,
			effect      : 'fade',
			navigation  : {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			thumbs      : {
				swiper: galleryThumbs,
			},
		});
	}

	function swiper_project_details() {
		if ($('.profolio-swiper-details').length) {

			var args_slider = {
				slidesPerView: 4,
				spaceBetween : 15,
				breakpoints  : {
					992: {
						slidesPerView: '3',
					},
					767: {
						slidesPerView: '2',
					},
					500: {
						slidesPerView: '1',
					},
				},
				navigation   : {
					nextEl: '.profolio-button-next',
					prevEl: '.profolio-button-prev',
				},

			};

			if ($('.profolio-swiper-details').find('.swiper-slide').length >= 5) {
				args_slider.pagination = {
					el       : '.swiper-pagination',
					clickable: true,
				};
			}

			var swiper = new Swiper('.profolio-swiper-details', args_slider);
		}
	}

	swiper_project_details();

	if ($('.profolio-prjct-details').length) {
		var swiper = new Swiper('.profolio-prjct-details', {
			slidesPerView: 4,
			spaceBetween : 15,
			pagination   : {
				el       : '.swiper-pagination',
				clickable: true,
			},
			breakpoints  : {
				992: {
					slidesPerView: '3',
				},
				767: {
					slidesPerView: '2',
				},
			},
			navigation   : {
				nextEl: '.profolio-button-next',
				prevEl: '.profolio-button-prev',
			},
		});
	}

	/*====================
	 Video Gallery
	 ====================*/
	if ($('.video-gallery').length) {
		$('.video-gallery').lightGallery({
			download: false,
		});
	}

	/*====================
	 Image Bg
	 ====================*/
	window.wpc_add_img_bg = function (img_sel, parent_sel) {
		if (!img_sel) {
			console.info('no img selector');
			return false;
		}
		var $parent, $imgDataHidden, _this;
		$(img_sel).each(function () {
			_this = $(this);
			$imgDataHidden = _this.data('s-hidden');
			$parent = _this.closest(parent_sel);
			$parent = $parent.length ? $parent : _this.parent();
			$parent.css('background-image', 'url(' + this.src + ')').addClass('s-back-switch');
			if ($imgDataHidden) {
				_this.css('visibility', 'hidden');
			} else {
				_this.hide();
			}
		});
	};

	wpc_add_img_bg('.profolio-bg-img');

	/*====================
	 Share links
	 ====================*/
	$('[data-share]').on('click', function (e) {
		e.preventDefault();

		var w = window,
				url = $(this).attr('data-share'),
				title = '',
				w_pop = 600,
				h_pop = 600,
				scren_left = w.screenLeft != undefined ? w.screenLeft : screen.left,
				scren_top = w.screenTop != undefined ? w.screenTop : screen.top,
				width = w.innerWidth,
				height = w.innerHeight,
				left = ((width / 2) - (w_pop / 2)) + scren_left,
				top = ((height / 2) - (h_pop / 2)) + scren_top,
				newWindow = w.open(url, title, 'scrollbars=yes, width=' + w_pop + ', height=' + h_pop + ', top=' + top + ', left=' + left);

		if (w.focus) {
			newWindow.focus();
		}

		return false;
	});

	/*====================
	 Light gallery
	 ====================*/
	if ($('.profolio-lightgallery').length && $('.profolio-lightgallery').data('lightGallery')) {
		$('.profolio-lightgallery').lightGallery({
			download: false,
			selector: '.profolio-slctr',
			loop    : false,
		});
	}

	$('.profolio-filter-category input').on('change', function () {
		console.log('change');
	});

	/*====================
	 Category Filter
	 ====================*/
	function project_filters(input_field) {
		var inputWrapper = input_field ? input_field.closest('.js-category-filter') : $('.js-category-filter'),
				filterItems = inputWrapper.find('.js-form-check-input'),
				categoryIds = [];

		filterItems.each(function () {
			if ($(this).not('.profolio-sellect-all').is(':checked')) {
				categoryIds.push($(this).val());
			}
		});

		$.ajax({
			url    : localseomap_object.ajaxurl,
			type   : 'POST',
			data   : {
				'action'     : 'filter_by_category',
				'categoryIds': categoryIds,
			},
			success: function (response) {
				$('.js-projects-wrapper').html(response);
				wpc_add_img_bg('.profolio-bg-img');
			},
		});

		return false;
	}

	$('.js-form-check-input').on('change', function () {
		project_filters($(this));
	});

	$('body').on('click', '.js-reset-filter', function () {
		$('.js-form-check-input').prop('checked', false);
		project_filters();
	});

	$('.profolio-details-map-frame img').on('load', function () {
		$(this).parent().show();
	});

	/*======================
	 Profolio Filter Slider
	 ======================*/
	if ($('.js-profolio-filter-wrapper').length) {
		$('.js-profolio-filter-wrapper').each(function () {

			var $container = $(this),
					data_items = $container.find('.js-profolio-filter-item'),
					$filters = $('.js-profolio-filter-frame').find('div');

			// Filter
			$filters.on('click', function () {

				// Show/Hide active class
				$filters.removeClass('profolio-filter-current');
				$(this).addClass('profolio-filter-current');

				var filterValue = $(this).attr('data-filter');

				// Show/Hide brands item
				if (filterValue === '*') {
					$('.js-profolio-filter-wrapper').find('.js-profolio-filter-item').show();
				} else {
					var filteredItems = data_items.filter('[data-category = "' + filterValue + '"]');

					$('.js-profolio-filter-wrapper').find('.js-profolio-filter-item').hide();
					filteredItems.show();
				}

				swiper_project_details();

				return false;
			});
		});
	}

	/*====================
	 Before/After block
	 ====================*/
	if ($('.ba-slider').length) {
		$('.ba-slider').beforeAfter();
	}

	$('.js-example-basic-multiple').select2().on('select2:open', function (element) {
		$('.select2-container--open').addClass('profolio-select2-result');

	});

	$('.profolio-add-form-field').click(function (e) {
		e.preventDefault();

		$(this).prev().append($(this).next('.profolio-inpt').clone().css({
			'display': 'flex',
		})); //add input box
	});

	$('.profolio-ad-inp-frame').on('click', '.profolio-delete-inpt', function (e) {
		e.preventDefault();
		$(this).parent('.profolio-inpt').remove();
	});


	$('.profolio-add-file-form').on('submit', function (e) {

		$form = $(this);
		$loader = $form.parent().find('.profolio-ajax-form-loader');

		$loader.addClass('active');

		jQuery.ajax({
			method : $form.attr('method'),
			type   : 'POST',
			url    : localseomap_object.ajaxurl,
			data   : $form.serialize(),
			success: function (response) {

				if ('success' === response.status) {
					let template = wp.template('localseomap-form-success-template');
					$form.parent().find('.profolio-form-success').addClass('active').html(template(response));

					setTimeout(function () {
						$('html, body').animate({
							scrollTop: $('.profolio-form-success').offset().top - 150
						}, 1000);
					}, 1000);
				}

				if ('error' === response.status) {
					$form.parent().find('.profolio-form-error').addClass('active').html(response.message);
				}

				$loader.removeClass('active');
				$form.hide();
				$form.parent().find('.profolio-add-new-project').show();
			},
		});

		return false;

	});

	$('.profolio-add-new-project').on('click', function (e) {

		location.href = location.href;


	});

	if ($('.profolio-datepick').length && $.fn.datepicker.language != undefined) {
		$.fn.datepicker.language['en'] = {
			days       : ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
			daysShort  : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
			daysMin    : ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
			months     : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
			monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			today      : 'Today',
			clear      : 'Clear',
			dateFormat : 'yy-mm-dd',
			timeFormat : 'hh:ii',
			firstDay   : 0,
		};

		$('.profolio-datepick').datepicker({
			language  : 'en',
			timepicker: true,
		});
	}

	$('.profolio-hidden-part-btn').on('click', function () {

		$(this).find('i').toggleClass('profolio-rotate');
		$(this).next('.profolio-hidden-part').toggleClass('profolio-hidden-part-show');

	});

});
