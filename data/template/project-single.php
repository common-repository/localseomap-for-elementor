<?php

get_header();
$admin   = new LocalSeoMap\Admin();
$seo     = new LocalSeoMap\Seo();
$seo     = new LocalSeoMap\Seo();
$prefix  = $admin->get_metabox_prefix();
$post_id = get_the_ID();

$media_list = array();


$pap_type_address = get_option( '_pap_type_address' );

$address   = array();
$address[] = get_post_meta( $post_id, $prefix . 'city', true );
$address[] = get_post_meta( $post_id, $prefix . 'province', true );
$address[] = get_post_meta( $post_id, $prefix . 'country', true );

if ( isset( $pap_type_address ) && $pap_type_address == 'exact' ) {
	$address[] = get_post_meta( $post_id, $prefix . 'address', true );
}

$address = array_filter( $address );

/**
 * Project info.
 */

$field_real_estate_price      = get_post_meta( $post_id, $prefix . 'field_real_estate_price', true );
$field_real_estate_sale_type  = get_post_meta( $post_id, $prefix . 'field_real_estate_sale_type', true );
$field_real_estate_status     = get_post_meta( $post_id, $prefix . 'field_real_estate_status', true );
$field_real_estate_mls_id     = get_post_meta( $post_id, $prefix . 'field_real_estate_mls_id', true );
$field_real_estate_home_size  = get_post_meta( $post_id, $prefix . 'field_real_estate_home_size', true );
$field_real_estate_lot_size   = get_post_meta( $post_id, $prefix . 'field_real_estate_lot_size', true );
$field_real_estate_bedrooms   = get_post_meta( $post_id, $prefix . 'field_real_estate_bedrooms', true );
$field_real_estate_bathrooms  = get_post_meta( $post_id, $prefix . 'field_real_estate_bathrooms', true );
$field_real_estate_year_built = get_post_meta( $post_id, $prefix . 'field_real_estate_year_built', true );

/**
 * Project info.
 */

$field_project_value         = get_post_meta( $post_id, $prefix . 'field_project_value', true );
$field_project_status        = get_post_meta( $post_id, $prefix . 'field_project_status', true );
$field_project_pro           = get_post_meta( $post_id, $prefix . 'field_project_pro', true );
$field_project_permit_number = get_post_meta( $post_id, $prefix . 'field_project_permit_number', true );
$field_project_customer_name = get_post_meta( $post_id, $prefix . 'field_project_customer_name', true );

$start_date   = get_post_meta( $post_id, $prefix . 'start_date', true );
$before_photo = get_post_meta( $post_id, $prefix . 'before_photo', true );
$after_photo  = get_post_meta( $post_id, $prefix . 'after_photo', true );

$testimonial_title   = get_post_meta( $post_id, $prefix . 'field_story_testimonial_title', true );
$testimonial_author  = get_post_meta( $post_id, $prefix . 'field_story_testimonial_author', true );
$testimonial_rating  = get_post_meta( $post_id, $prefix . 'field_story_testimonial_rating', true );
$testimonial_body    = get_post_meta( $post_id, $prefix . 'field_story_testimonial_body', true );
$testimonial_video   = get_post_meta( $post_id, $prefix . 'field_story_testimonial_videos', true );
$testimonial_picture = get_post_meta( $post_id, $prefix . 'field_story_testimonial_picture', true );
$testimonial_cover   = get_post_meta( $post_id, $prefix . 'field_story_testimonial_cover', true );

// Image Gallery Options
$pap_enable_gallery = get_option( '_pap_enable_gallery' );
$pap_image_size     = get_option( '_pap_image_size' );
if ( empty( $pap_image_size ) ) {
	$pap_image_size = 'project_thumbnail';
}
$pap_columns  = get_option( '_pap_columns' );
$pap_link     = get_option( '_pap_link' );
$pap_lightbox = get_option( '_pap_lightbox' );
$pap_order_by = get_option( '_pap_order_by' );

/* For media. */
$uuid = get_post_meta( get_the_ID(), $prefix . 'uuid', true );
$args = array(
	'posts_per_page' => - 1,
	'post_type'      => 'localseomap_media',
	'post_status'    => array( 'publish' ),
	'meta_query'     => [
		'relation' => 'AND',
		[
			'key'   => $prefix . 'project_uuid',
			'value' => $uuid,
		],
		[
			'key'   => $prefix . 'state',
			'value' => 'approved',
		],
	],
);

if ( isset( $pap_order_by ) && $pap_order_by == 'random' ) {
	$args['orderby'] = 'rand';
}

$media_query = new WP_Query( $args );

$pap_show_share_links = get_option( '_pap_show_share_links' );

$pap_title_font_size     = get_option( '_pap_title_font_size' );
$pap_title_color         = get_option( '_pap_title_color' );
$pap_gallery_title_fs    = get_option( '_pap_gallery_title_font_size' );
$pap_gallery_title_color = get_option( '_pap_gallery_title_color' );
$pap_seperator_color     = get_option( '_pap_seperator_color' );
$pap_button_bg_color     = get_option( '_pap_button_bg_color' );
$pap_show_preview        = get_option( '_pap_show_preview' );
$max_radius              = get_option( '_pap_app_max_radius' );
if ( ! is_numeric( $max_radius ) ) {
	$max_radius = 1609;
}

$longitude  = get_post_meta( $post_id, $prefix . 'longitude', true );
$latitude   = get_post_meta( $post_id, $prefix . 'latitude', true );
$rand_meter = rand( 0, $max_radius );
$rand_coef  = $rand_meter * 0.0000089;
if ( ( ! empty( $longitude ) && ! empty( $latitude ) ) && isset( $pap_type_address ) && $pap_type_address !== 'exact' ) {
	$longitude += $rand_coef / cos( $latitude * 0.018 );
	$latitude  += $rand_coef;
}

$pap_map_zoom = get_option( '_pap_map_zoom' );
if ( empty( $pap_map_zoom ) ) {
	$pap_map_zoom = '18';
}
?>
    <div class="progolio-project-wrap">
        <style>
            .profolio-details-map-frame img {
                width: 100%;
                height: 100%;
                border: none;
                border-radius: 50%;
                object-fit: cover;
            }

            <?php if ( ! empty( $pap_title_font_size ) ) : ?>
            .profolio-title-page {
                font-size: <?php echo esc_attr($pap_title_font_size); ?>px;
            }

            <?php endif; ?>

            <?php if ( ! empty( $pap_title_color ) ) : ?>
            .profolio-title-page {
                color: <?php echo esc_attr($pap_title_color); ?>;
            }

            <?php endif; ?>

            <?php if ( ! empty( $pap_gallery_title_fs ) ) : ?>
            .profolio-gallery-title {
                font-size: <?php echo esc_attr($pap_gallery_title_fs); ?>px;
            }

            <?php endif; ?>

            <?php if ( ! empty( $pap_gallery_title_color ) ) : ?>
            .profolio-gallery-title {
                color: <?php echo esc_attr($pap_gallery_title_color); ?>;
            }

            <?php endif; ?>

            <?php if ( ! empty( $pap_seperator_color ) ) : ?>
            h1:not(.site-title):before, h2:before,
            .swiper-pagination-bullet-active {
                background-color: <?php echo esc_attr($pap_seperator_color); ?>;
            }

            <?php endif; ?>

            <?php if ( ! empty( $pap_button_bg_color ) ) : ?>
            .profolio-default-button,
            .profolio-card-category-frame a {
                background-color: <?php echo esc_attr($pap_button_bg_color); ?>;
            }


            <?php endif; ?>

            <?php
			$pap_top_margin    = get_option( '_pap_top_margin' );
			$pap_bottom_margin = get_option( '_pap_bottom_margin' );
			?>
            .profolio-single-wrap {
            <?php
			if ( ! empty($pap_top_margin) ) { ?> margin-top: <?php echo esc_attr($pap_top_margin); ?>px;
            <?php }
			if ( ! empty($pap_bottom_margin) ) { ?> margin-bottom: <?php echo esc_attr($pap_bottom_margin); ?>px;
            <?php } ?>
            }
        </style>

		<?php do_action( 'localseomap_before_content' ); ?>

        <div class="profolio-container profolio-single-wrap p-60-0-30">
            <div class="profolio-row">
                <div class="profolio-col-lg-9">
                    <div class="profolio-project-frame">

						<?php
						$hide_project_title = get_option( '_pap_hide_project_title' );

						if ( empty( $hide_project_title ) ):
							the_title( '<h1 class="profolio-header-sm profolio-title-page mb30">', '</h1>' );
						endif; ?>

						<?php if ( ! empty( $before_photo ) && ! empty( $after_photo ) ) : ?>
                            <div class="ba-slider">
                                <img src="<?php echo wp_get_attachment_image_url( $before_photo, 'project_thumbnail' ); ?>">
                                <div class="resize">
                                    <img src="<?php echo wp_get_attachment_image_url( $after_photo, 'project_thumbnail' ); ?>">
                                    <meta property="og:image"
                                          content="<?php echo wp_get_attachment_image_url( $after_photo, 'project_thumbnail' ); ?>" />
                                </div>
                                <span class="handle"></span>
                            </div>
						<?php else : ?>
							<?php if ( has_post_thumbnail() ) : ?>
                                <div class="profolio-project-img">
									<?php the_post_thumbnail( 'project_thumbnail', array( 'class' => 'profolio-bg-img' ) ); ?>
                                </div>
							<?php endif; ?>
						<?php endif; ?>

						<?php
						/**
						 * Leads button.
						 */

						localseomap_render_leads_button( 'mobile' );
						?>

						<?php while ( have_posts() ) : the_post(); ?>
                            <div class="profolio-project-desc">
								<?php the_content(); ?>
                            </div>
						<?php endwhile; ?>

                        <div class="profolio-next-prev">
							<?php
							$prev_post = get_previous_post();
							$next_post = get_next_post();
							?>
							<?php if ( ! empty( $prev_post ) ) { ?>
                                <a href="<?php echo get_the_permalink( $prev_post ); ?>"
                                   class="profolio-link profolio-r-link mb30">
                                    <b>
										<?php esc_html_e( 'Previous Project', 'localseomap-for-elementor' ); ?>
                                    </b>
                                    <i class="pro_fa pro_fa-chevron-left"></i>
                                </a>
							<?php } ?>

							<?php if ( ! empty( $next_post ) ) { ?>
                                <a href="<?php echo get_the_permalink( $next_post ); ?>"
                                   class="profolio-link profolio-l-link mb30"><i
                                            class="pro_fa pro_fa-chevron-right"></i><b><?php esc_html_e( 'Next Project', 'localseomap-for-elementor' ); ?></b>
                                </a>
							<?php } ?>

                        </div>

						<?php if ( $media_query->have_posts() ) : ?>
                            <div class="profolio-row justify-content-between mb30">
								<?php if ( isset( $pap_enable_gallery ) && ! $pap_enable_gallery ) : ?>
                                    <div class="profolio-col-12 profolio-col-md-3">
                                        <div class="profolio-section-title-sm">
                                            <b><?php esc_html_e( 'Look inside', 'localseomap-for-elementor' ); ?></b>
                                        </div>
                                    </div>
								<?php endif; ?>

                                <div class="profolio-col-12 profolio-col-md-<?php echo isset( $pap_enable_gallery ) && $pap_enable_gallery ? '12' : '6'; ?> align-self-center">
                                    <div class="profolio-filter-frame js-profolio-filter-frame">
                                        <div data-filter="*" class="profolio-filter-current">
                                            <span><?php esc_html_e( 'All', 'localseomap-for-elementor' ); ?></span>
                                        </div>
                                        <div data-filter="before">
                                            <span><?php esc_html_e( 'Before', 'localseomap-for-elementor' ); ?></span>
                                        </div>
                                        <div data-filter="after">
                                            <span><?php esc_html_e( 'After', 'localseomap-for-elementor' ); ?></span>
                                        </div>
                                        <div data-filter="during">
                                            <span><?php esc_html_e( 'During', 'localseomap-for-elementor' ); ?></span>
                                        </div>
                                    </div>
                                </div>

								<?php if ( isset( $pap_enable_gallery ) && ! $pap_enable_gallery ) : ?>
									<?php if ( is_array( $media_query->posts ) && count( $media_query->posts ) >= 5 ) : ?>
                                        <div class="profolio-col-12 profolio-col-md-3 align-self-center">
                                            <div class="profolio-arrows-wraper">
                                                <div class="profolio-button-prev">
                                                    <i class="pro_fa pro_fa-chevron-left"></i>
                                                </div>
                                                <div class="profolio-button-next">
                                                    <i class="pro_fa pro_fa-chevron-right"></i>
                                                </div>
                                            </div>
                                        </div>
									<?php endif; ?>
								<?php endif; ?>
                            </div>
						<?php endif; ?>
                    </div>

					<?php if ( $media_query->have_posts() ) :
						if ( isset( $pap_enable_gallery ) && $pap_enable_gallery ) :
							$count = 0;
							$gallery_class = isset( $pap_columns ) ? ' profolio-col-lg-' . $pap_columns : 'profolio-col-lg-12';
							?>
                            <div class="profolio-lightgallery profolio-simple-gallery profolio-row js-profolio-filter-wrapper">
								<?php while ( $media_query->have_posts() ) : $media_query->the_post();
									$state       = get_post_meta( get_the_ID(), $prefix . 'pin_pf_state', true );
									$field_video = get_post_meta( get_the_ID(), $prefix . 'field_video', true );;
									echo $seo->the_schema_video( get_the_ID() );
									?>
                                    <div class="profolio-col-12 profolio-col-sm-6 profolio-col-md-6 js-profolio-filter-item <?php echo esc_attr( $gallery_class ); ?>"
                                         data-category="<?php echo esc_attr( $state ); ?>">
                                        <a download class="profolio-slctr" download href="<?php the_post_thumbnail_url( 'project_thumbnail' ) ?>"
                                           data-id="<?php echo esc_attr( $count ); ?>">
                                            <div class="profolio-gal-frame">
												<?php the_post_thumbnail( $pap_image_size, array( 'class' => 'profolio-bg-img' ) ); ?>
												<?php if ( ! empty( $field_video ) ):
													$media_list[] = array(
														'html'  => '#media_video_' . $count,
														'thumb' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
													); ?>
                                                    <div class="profolio-img-hover-video"
                                                         data-html="#media_video_<?php echo esc_attr( $count ); ?>">
                                                        <i class="pro_far pro_fa-play-circle"></i>
                                                    </div>
                                                    <div style="display:none;"
                                                         id="media_video_<?php echo esc_attr( $count ); ?>">
                                                        <video class="lg-video-object lg-html5" controls preload="metadata">
                                                            <source src="<?php echo esc_url( $field_video ); ?>"
                                                                    type="video/mp4">
															<?php esc_html_e( 'Your browser does not support HTML5 video.', 'localseomap-for-elementor' ); ?>
                                                        </video>
                                                    </div>
												<?php else :
													$media_list[] = array(
														'src'   => get_the_post_thumbnail_url( get_the_ID(), 'project_thumbnail' ),
														'thumb' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
													); ?>
                                                    <div class="profolio-img-hover">
                                                        <i class="pro_fa pro_fa-search-plus"></i>
                                                    </div>
												<?php endif; ?>
                                            </div>
                                        </a>
										<?php the_title( '<div class="profolio-media-more"><a href="' . esc_url( get_the_permalink() ) . '">', '</a></div>' ); ?>
                                    </div>

									<?php $count ++; endwhile; ?>
                            </div>
						<?php else : ?>
                            <div class="swiper-container profolio-swiper-details">
                                <div class="profolio-lightgallery swiper-wrapper js-profolio-filter-wrapper">
									<?php $count = 0;
									while ( $media_query->have_posts() ) : $media_query->the_post();

										setup_postdata( $media_query );

										$field_video = get_post_meta( get_the_ID(), $prefix . 'field_video', true );
										echo $seo->the_schema_video( get_the_ID() );
										$state = get_post_meta( get_the_ID(), $prefix . 'pin_pf_state', true );
										?>
                                        <div class="swiper-slide profolio-isotope__item js-profolio-filter-item"
                                             data-category="<?php echo esc_attr( $state ); ?>">

											<?php if ( ! empty( $field_video ) ) :
												$media_list[] = array(
													'html'  => '#media_video_' . $count,
													'thumb' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
												);
												?>
                                                <div style="display:none;"
                                                     id="media_video_<?php echo esc_attr( $count ); ?>">
                                                    <video class="lg-video-object lg-html5" controls
                                                           preload="none">
                                                        <source src="<?php echo esc_url( $field_video ); ?>"
                                                                type="video/mp4">
														<?php esc_html_e( 'Your browser does not support HTML5 video.', 'localseomap-for-elementor' ); ?>
                                                    </video>
                                                </div>

                                                <a class="profolio-slctr" download
                                                   href="<?php the_post_thumbnail_url( 'project_thumbnail' ) ?>"
                                                   data-id="<?php echo esc_attr( $count ); ?>">
													<?php if ( has_post_thumbnail() ): ?>
                                                        <div class="profolio-video-preview"
                                                             data-html="#media_video_<?php echo esc_attr( $count ); ?>">
															<?php the_post_thumbnail( 'project_thumbnail', array( 'class' => 'profolio-bg-img' ) ); ?>
                                                            <i class="pro_far pro_fa-play-circle"></i>
                                                        </div>
													<?php else: ?>
                                                        <div class="profolio-video-preview"
                                                             data-html="#media_video_<?php echo esc_attr( $count ); ?>">
                                                            <video src="<?php echo esc_url( $field_video ); ?>" preload="metadata"></video>
                                                            <i class="pro_far pro_fa-play-circle"></i>
                                                        </div>
													<?php endif; ?>
                                                </a>
											<?php else :
												$media_list[] = array(
													'src'   => get_the_post_thumbnail_url( get_the_ID(), 'project_thumbnail' ),
													'thumb' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
												);
												?>
                                                <a class="profolio-slctr" download
                                                   href="<?php the_post_thumbnail_url( 'project_thumbnail' ) ?>"
                                                   data-id="<?php echo esc_attr( $count ); ?>">
                                                    <div class="profolio-gal-frame">
														<?php the_post_thumbnail( $pap_image_size, array( 'class' => 'profolio-bg-img' ) ); ?>
                                                        <div class="profolio-img-hover"><i
                                                                    class="pro_fa pro_fa-search-plus"></i>
                                                        </div>
                                                    </div>
                                                </a>
											<?php endif; ?>

                                        </div>
										<?php $count ++; endwhile; ?>
                                </div>
                                <!-- Add Pagination -->
                                <div class="swiper-pagination"></div>
                            </div>
						<?php endif; ?>
					<?php endif;
					wp_reset_postdata(); ?>

                </div>

                <div class="profolio-col-lg-3">
                    <div class="profolio-right-side">

						<?php if ( ! empty( $address ) ) : ?>
                            <div class="profolio-header-xs"><?php echo esc_html__( 'Location', 'localseomap-for-elementor' ); ?></div>
                            <hr>
                            <div class="profolio-text-sm mb30">
                                <i class="pro_fa pro_fa-map-marker-alt"></i>
								<?php echo implode( ', ', $address ); ?>
                            </div>
						<?php endif;

						$location = $admin->get_lat_long( $post_id );

						$language = get_option( '_pap_language' );

						if ( ! empty( $language ) ) {
							$language = '&language=' . $language;
						}

						if ( ! empty( $location['longitude'] ) && ! empty( $location['latitude'] ) && get_option( '_pap_google_maps_api_key' ) ) : ?>
                            <div>
                                <img src="https://maps.googleapis.com/maps/api/staticmap?zoom=<?php echo esc_attr( $pap_map_zoom ); ?>&size=600x300&maptype=roadmap&markers=<?php echo esc_attr( $location['latitude'] ); ?>,<?php echo esc_attr( $location['longitude'] ); ?>&key=<?php echo get_option( '_pap_google_maps_api_key' ) . $language; ?>"
                                     class="profolio-right-side-map" alt="">
                            </div>
						<?php endif; ?>

						<?php
						/**
						 * Leads button.
						 */

						localseomap_render_leads_button();
						?>

                        <div class="profolio-review mb60">

							<?php if ( ! empty( $start_date ) || ! empty( $field_project_value ) || ( isset( $field_project_status ) && $field_project_status !== '' ) || ! empty( $field_project_permit_number ) || ! empty( $field_project_customer_name ) ) : ?>
                                <hr>
                                <div class="profolio-header-xs"><?php echo esc_html__( 'Project Details', 'localseomap-for-elementor' ); ?></div>

								<?php if ( ! empty( $start_date ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php echo esc_html__( 'Start date:', 'localseomap-for-elementor' ); ?></span>
										<?php echo date( 'F d, Y ', strtotime( $start_date ) ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_project_value ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Project Value: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( localseomap_format_number( $field_project_value, '$' ) ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_project_status ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Project Status: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( localseomap_get_status( $field_project_status, 'project' ) ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_project_permit_number ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Permit Number: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( localseomap_format_number( $field_project_permit_number ) ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_project_customer_name ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Customer Name: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( $field_project_customer_name ); ?>
                                    </div>
								<?php endif; ?>

							<?php endif; ?>

							<?php
							$terms = get_the_terms( $post_id, 'localseomap_area_tags' );
							if ( ! empty( $terms ) ) : ?>
                                <hr>
                                <div class="profolio-text-sm">
                                    <i class="pro_fa pro_fa-list"></i>
									<?php echo esc_html__( 'Local Services:', 'localseomap-for-elementor' ); ?>
                                </div>

								<?php the_terms( $post_id, 'localseomap_area_tags', '<div class="profolio-card-area-frame mb30">', '', '</div> ' ); ?>
							<?php endif; ?>

							<?php
							$industry = get_the_terms( $post_id, 'localseomap_industry' );
							if ( ! empty( $industry ) ) : ?>
                                <hr>
                                <div class="profolio-text-sm">
                                    <i class="pro_fa pro_fa-list"></i>
									<?php echo esc_html__( 'Category:', 'localseomap-for-elementor' ); ?>
                                </div>

								<?php the_terms( $post_id, 'localseomap_industry', '<div class="profolio-card-category-frame mb30">', '', '</div> ' ); ?>
							<?php endif; ?>

							<?php
							$project_tag = get_the_terms( $post_id, 'localseomap_project_tag' );
							if ( ! empty( $project_tag ) ) : ?>
                                <hr>
                                <div class="profolio-text-sm">
                                    <i class="pro_fa pro_fa-list"></i>
									<?php echo esc_html__( 'Tags:', 'localseomap-for-elementor' ); ?>
                                </div>

								<?php the_terms( $post_id, 'localseomap_project_tag', '<div class="profolio-card-category-frame mb30">', '', '</div> ' ); ?>
							<?php endif; ?>

							<?php if ( ! empty( $testimonial_title ) && ! empty( $testimonial_author ) ) : ?>

								<?php
								global $post;
								$gavar_url = get_avatar_url( $post->post_author, array(
									'default' => '404'
								) );
								if ( get_avatar_url( $post->post_author ) ) {
									if ( strpos( $gavar_url, '404' ) == false ) {
										?>
                                        <div class="profolio-reviewer-photo mb15">
                                            <img src="<?php echo esc_url( $gavar_url ); ?>" alt=""
                                                 class="profolio-bg-img">
                                        </div>
										<?php
									}
								} ?>

                                <hr>
								<?php if ( ! empty( $testimonial_author ) ) : ?>
                                    <div class="profolio-elm-title profolio-reviewer-author profolio-card-category-frame">
                                        <b><?php echo esc_html( $testimonial_author ); ?></b>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $testimonial_title ) ) : ?>
                                    <h2 class="profolio-reviewer-post"><?php echo esc_html( $testimonial_title ); ?></h2>
								<?php endif; ?>

                                <ul class="profolio-card-rating">
									<?php if ( ! empty( $testimonial_rating ) ) { ?>
										<?php for ( $i = 1; $i <= 5; $i ++ ) {
											if ( $testimonial_rating >= 1 ) : ?>
                                                <li><i class="pro_fa pro_fa-star"></i></li>
												<?php $testimonial_rating --;
											else : ?>
                                                <li><i class="pro_far pro_fa-star"></i></li>
											<?php endif;
										} ?>
									<?php } ?>
                                </ul>

								<?php if ( ! empty( $testimonial_body ) ) : ?>
                                    <div class="profolio-dflt-text mb30">
										<?php echo esc_html( $testimonial_body ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $testimonial_video ) ) : ?>
                                    <div style="display:none;" id="testimonial_video">
                                        <video class="lg-video-object lg-html5" autoplay controls preload="none">
                                            <source src="<?php echo esc_url( $testimonial_video ); ?>" type="video/mp4">
											<?php esc_html_e( 'Your browser does not support HTML5 video.', 'localseomap-for-elementor' ); ?>
                                        </video>
                                    </div>

                                    <div class="video-gallery">
										<?php if ( ! empty( $pap_show_preview ) && ! empty( $testimonial_cover ) ): ?>
                                            <div class="profolio-video-preview s-back-switch"
                                                 data-html="#testimonial_video"
                                                 data-sub-html="<?php echo esc_html( $testimonial_title ); ?>">
												<?php echo wp_get_attachment_image( $testimonial_cover, 'medium', '', array( 'class' => 'profolio-bg-img' ) ); ?>
                                                <i class="pro_far pro_fa-play-circle"></i>
                                            </div>
										<?php else: ?>
                                            <div data-html="#testimonial_video"
                                                 data-sub-html="<?php echo esc_html( $testimonial_title ); ?>"
                                                 class="profolio-default-button mb30">
                                                <i class="pro_fab pro_fa-youtube"></i>
                                                <span><?php esc_html_e( 'Watch Video', 'localseomap-for-elementor' ); ?></span></a>
                                            </div>
										<?php endif; ?>
                                    </div>
								<?php endif; ?>


							<?php endif; ?>

							<?php if ( ! empty( $field_real_estate_price ) || localseomap_get_sale_type( $field_real_estate_sale_type ) || localseomap_get_status( $field_real_estate_status, 'property' ) || ! empty( $field_real_estate_mls_id ) || ! empty( $field_real_estate_home_size ) || ! empty( $field_real_estate_year_built ) || ! empty( $field_real_estate_bathrooms ) || ! empty( $field_real_estate_lot_size ) || ! empty( $field_real_estate_bedrooms ) ) : ?>
                                <hr>

                                <div class="profolio-header-xs"><?php echo esc_html__( 'Property Details', 'localseomap-for-elementor' ); ?></div>
								<?php if ( ! empty( $field_real_estate_price ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Price: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( localseomap_format_number( $field_real_estate_price, '$' ) ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( localseomap_get_sale_type( $field_real_estate_sale_type ) ): ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Sale type: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( localseomap_get_sale_type( $field_real_estate_sale_type ) ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( localseomap_get_status( $field_real_estate_status ) ): ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Status: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( localseomap_get_status( $field_real_estate_status ) ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_real_estate_mls_id ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'MLS ID: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( $field_real_estate_mls_id ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_real_estate_home_size ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Home size: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( localseomap_format_number( $field_real_estate_home_size ) ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_real_estate_lot_size ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Lot size: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( localseomap_format_number( $field_real_estate_lot_size ) ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_real_estate_bedrooms ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Bedrooms: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( $field_real_estate_bedrooms ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_real_estate_bathrooms ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Bathrooms: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( $field_real_estate_bathrooms ); ?>
                                    </div>
								<?php endif; ?>

								<?php if ( ! empty( $field_real_estate_year_built ) ) : ?>
                                    <div class="profolio-text-sm">
                                        <span><?php esc_html_e( 'Year built: ', 'localseomap-for-elementor' ); ?></span><?php echo esc_html( $field_real_estate_year_built ); ?>
                                    </div>
								<?php endif; ?>

							<?php endif; ?>

                        </div>


						<?php if ( isset( $pap_show_share_links ) && $pap_show_share_links ) :
							$url = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
							?>
                            <ul class="profolio-details-social">
                                <li><a href="#"
                                       data-share="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>&amp;t=<?php the_title(); ?>"><i
                                                class="pro_fab pro_fa-facebook-f"></i></a href="#"></li>
                                <li><a href="#"
                                       data-share="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php echo esc_attr( $url ); ?>">
                                        <i class="pro_fab pro_fa-pinterest-p"></i></a>
                                </li>
                                <li><a href="#"
                                       data-share="http://twitter.com/home/?status=<?php the_title(); ?> - <?php the_permalink(); ?>"><i
                                                class="pro_fab pro_fa-twitter"></i></a></li>
                            </ul>
						<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

		<?php
		$seo = new LocalSeoMap\Seo();
		echo $seo->the_schema_video_testimonial( get_the_ID() );
		echo $seo->the_schema_review( get_the_ID() );
		echo $seo->the_schema_aggregate_rating( get_the_ID() );
		?>

		<?php do_action( 'localseomap_after_content' ); ?>
    </div>
<?php

wp_localize_script( 'localseomap-main-script', 'media_data', array(
	'media_list' => json_encode( $media_list )
) );

get_footer();
