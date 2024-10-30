jQuery(function ($) {

	if (typeof window.google === 'undefined') {
		return;
	}

	/*====================
	 Autocomplete init
	 ====================*/
	function profolio_init_autocomplete() {
		if ($('.profolio-loc-input').length) {
			$('.profolio-loc-input').each(function () {
				var autocomplete = new google.maps.places.Autocomplete(this);
			});

		}
	}

	profolio_init_autocomplete();

	$('.swiper-thumb').on('click', function () {
		$('.profolio-slider-frame').addClass('profolio-show-slider');
	});


	/*====================
	 Swiper Thumb Sliders
	 ====================*/
	/*function profolio_init_gallery_thumbs() {

	 if ( $( '.swiper-thumb' ).length ) {
	 var galleryThumbs = new Swiper( '.swiper-thumb', {
	 spaceBetween: 10,
	 slidesPerView: 4,
	 watchSlidesVisibility: true,
	 watchSlidesProgress: true,
	 breakpoints: {
	 992: {
	 slidesPerView: '3',
	 },
	 767: {
	 slidesPerView: '2',
	 },
	 },
	 } );
	 }
	 }

	 profolio_init_gallery_thumbs();*/

	/*/!*====================
	 Swiper Thumb Sliders
	 ====================*!/
	 function profolio_init_gallery_zoom() {
	 if ( $( '.swiper-zoom' ).length ) {
	 var galleryZoom = new Swiper( '.swiper-zoom', {
	 spaceBetween: 10,
	 effect: 'fade',
	 navigation: {
	 nextEl: '.swiper-button-next',
	 prevEl: '.swiper-button-prev',
	 },
	 thumbs: {
	 swiper: galleryThumbs,
	 },
	 } );
	 }
	 }

	 profolio_init_gallery_zoom();*/

	/*====================
	 Swiper Project details
	 ====================*/
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
	if ($('.profolio-lightgallery').length && $('.profolio-lightgallery').data('lightGallery')) {
		$('.profolio-lightgallery').lightGallery({
			download: false,
			selector: '.profolio-slctr',
			loop    : false,
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
			let src = _this.data('src') ? _this.data('src') : this.src;
			$parent.css('background-image', 'url(' + src + ')').addClass('s-back-switch');
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

	function play_lightbox_video() {
		let $video = $('.lg-current video');
		if ($video.length) {
			$video.get(0).play();
		}
	}

	if (window.media_data !== undefined && $('.profolio-lightgallery').length) {
		$('.profolio-slctr').on('click', function (e) {
			e.preventDefault();

			let item_id = $(this).data('id');

			let $lg = $(this).lightGallery({
				download : false,
				loop     : false,
				index    : parseInt(item_id),
				dynamic  : true,
				dynamicEl: JSON.parse(window.media_data.media_list),
			});

			$lg.on('onAfterSlide.lg', function () {
				play_lightbox_video();
			});

			$lg.on('onAfterOpen.lg', function () {
				play_lightbox_video();
			});
		});

	}

	var params = location.hash.split("&").map(function (v) {
		return v.split("=");
	}).reduce(function (pre, _ref) {
		var _ref2;

		var key = _ref[0],
				value = _ref[1];
		return _ref2 = {}, _ref2[key] = value, _ref2;
	}, {});

	if ($('.profolio-slctr').length && params.slide) {

		let $lg = $('.profolio-slctr').lightGallery({
			download : false,
			loop     : false,
			index    : parseInt(params.slide),
			dynamic  : true,
			dynamicEl: JSON.parse(window.media_data.media_list),
		});
	}

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

	/*======================
	 Profolio Map on the Detail Page
	 ======================*/
	function profolio_display_map() {
		$('.profolio-details-map-frame img').on('load', function () {
			$(this).parent().show();
		});
	}

	profolio_display_map();

	/*======================
	 Profolio Filter Slider
	 ======================*/
	function profolio_filter_init() {

		if ($('.js-profolio-filter-wrapper').length) {

			$('.js-profolio-filter-frame').find('> div').hide();

			$('.js-profolio-filter-wrapper').each(function () {

				var $container = $(this),
						data_items = $container.find('.js-profolio-filter-item'),
						$filters = $('.js-profolio-filter-frame').find('div');

				var $slide = $container.find('> div');

				var is_category = false;
				$slide.each(function () {
					var filterValue = $(this).attr('data-category');

					if (filterValue != '') {
						//filterValue = '*';
						is_category = true;
						$('.js-profolio-filter-frame').find('[data-filter="' + filterValue + '"]').show();
					}
				});

				if (is_category) {
					$('.js-profolio-filter-frame').find('[data-filter="*"]').show();
				}

				// Filter
				$filters.on('click', function () {

					// Show/Hide active class
					$filters.removeClass('profolio-filter-current');
					$(this).addClass('profolio-filter-current');

					var filterValue = $(this).attr('data-filter');

					// Show/Hide brands item
					if (filterValue === '*') {
						$('.js-profolio-filter-wrapper').find('.js-profolio-filter-item').show();
						$('.profolio-arrows-wraper').show();
					} else {
						var filteredItems = data_items.filter('[data-category = "' + filterValue + '"]');

						$('.js-profolio-filter-wrapper').find('.js-profolio-filter-item').hide();
						filteredItems.show();

						if ($('.js-profolio-filter-wrapper').find('[data-category = "' + filterValue + '"]').length <= 4) {
							$('.profolio-arrows-wraper').hide();
						} else {
							$('.profolio-arrows-wraper').show();
						}
					}

					swiper_project_details();

					return false;
				});
			});
		}
	}

	profolio_filter_init();

	/*====================
	 Before/After block
	 ====================*/
	function profolio_before_after_init() {

		if ($('.ba-slider').length) {
			$('.ba-slider').beforeAfter();
		}
	}

	profolio_before_after_init();

	/*====================
	 Project modal init
	 ====================*/
	let cache = {},
			current_path = window.location.pathname;

	function set_to_template(data, id) {
		var template = wp.template(id);
		return template(data);
	}

	$('.JS_profolio_project_item.enable_modal a').on('click', function (e) {
		e.preventDefault();

		let project_path = this.href.replace(/^.*\/\/[^\/]+/, ''),
				$wrap = $(this).closest('.enable_modal'),
				post_id = $wrap.data('post-id');

		$.magnificPopup.open({
			items    : {
				src : '<div class="profolio-project-modal"></div>', // can
				// be
				// a
				// HTML
				// string,
				// selector
				type: 'inline',
			},
			callbacks: {

				open: function () {
					$('html').css('overflow-y', 'hidden');
				}, // over-write URL when popup is opened

				close: function () {
					$('html').css('overflow-y', 'auto');
					history.pushState('', document.title, current_path);
				}, // over-write URL when popup is closed
			},
		});

		if (cache[post_id]) {
			let project = cache[post_id];
			let project_path = project.link.replace(/^.*\/\/[^\/]+/, '');
			history.pushState('', project.link, project_path);
			let html = set_to_template(cache[post_id], 'profolio-modal-detail');
			$('.profolio-project-modal').html(html);
			return;
		}

		$.get(wpApiSettings.root + wpApiSettings.versionString + 'profolio_projects/' + post_id,
				function (project) {
					let project_path = project.link.replace(/^.*\/\/[^\/]+/, '');
					history.pushState('', project.link, project_path);
					let html = set_to_template(project, 'profolio-modal-detail');
					$('.profolio-project-modal').html(html);
					cache[post_id] = project;

				},
		);

		/*$.get( this.href, function ( response ) {

		 let $project = $( response ).find( '.progolio-project-wrap' );

		 $project.find( '.container' ).removeClass( 'p-90-0-60' );

		 cache[ project_path ] = $project.html();

		 $( '.profolio-project-modal' ).html( cache[ project_path ] );

		 // reinit all functions
		 wpc_add_img_bg( '.profolio-bg-img' );
		 profolio_display_map();
		 profolio_filter_init();
		 profolio_before_after_init();
		 swiper_project_details();
		 } );*/

	});

	window.onpopstate = function (event) {
		if (event.target.location.pathname == current_path) {
			$.magnificPopup.close();
		}
	};

	/*====================
          Leads form
	 ====================*/
	if ($('.js-show-leads-form').length) {
		$('.js-show-leads-form').magnificPopup({
			type     : 'inline',
			preloader: false
		});
	}

	if ($('.elementor-form').length) {
		$('.elementor-form').each(function () {
			$(this).append('<input type="hidden" name="form_fields[hidden_referrer_page_url]" value="' + document.referrer + '" />');
		});
	}

	$('.profolio-leads-form').on('submit', function (e) {
		e.preventDefault();

		var _this = $(this);

		$.ajax({
			type      : 'POST',
			url       : localseomap_object.ajaxurl,
			data      : {
				action   : 'submit_leads_form',
				'request': $(this).serialize()
			},
			beforeSend: function () {
				var loaderImg = '<div class="rp-cart-loader"><img src="' + localseomap_object.directoryurl + '/assets/img/ajax-loader.gif" /></div>';
				$('.profolio-leads-popup').append(loaderImg);
			},
			success   : function (response) {
				var response_data = JSON.parse(response);

				$('.rp-cart-loader').remove();

				if (response_data.status === 200) {
					_this.find('.form-control').val('');
				}

				$('.profolio-leads-message').html(response_data.message);
			},
			error     : function () {
				$('.profolio-leads-message').html('Something went wrong, try again!');
			}
		});

		return false;
	});

});




