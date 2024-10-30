<?php include 'media-header.php'; ?>

<style>
    <?php if ( ! empty( $pap_title_font_size ) ) : ?>
    .profolio-header-sm {
        font-size: <?php echo esc_attr($pap_title_font_size); ?>px;
    }

    <?php endif; ?>

    <?php if ( ! empty( $pap_title_color ) ) : ?>
    .profolio-header-sm {
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
    .profolio-default-button {
        background-color: <?php echo esc_attr($pap_button_bg_color); ?>;
    }

    <?php endif; ?>


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
				$hide_project_title = carbon_get_theme_option( 'pap_hide_project_title' );
				if ( empty( $hide_project_title ) ):
					the_title( '<h1 class="profolio-header-sm mb30">', '</h1>' );
				endif; ?>

				<?php if ( ! empty( $video_url ) ) :
					?>
                    <div style="display:none;" id="testimonial_video">
                        <video class="lg-video-object lg-html5" autoplay controls preload="none">
                            <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
							<?php esc_html_e( 'Your browser does not support HTML5 video.', 'localseomap-for-elementor' ); ?>
                        </video>
                    </div>
					<?php
					if ( ! empty( $video_url ) ) {
						$seo = new LocalSeoMap\Seo();
						echo $seo->the_schema_video( get_the_ID() );
					}
					?>

                    <div class="video-gallery">
						<?php if ( has_post_thumbnail() ): ?>
                            <div class="profolio-video-preview s-back-switch" data-html="#media_video">
								<?php the_post_thumbnail( 'medium', array( 'class' => 'profolio-bg-img' ) ); ?>
                                <i class="pro_far pro_fa-play-circle"></i>
                            </div>
						<?php else : ?>
                            <div class="profolio-video-preview  s-back-switch" data-html="#media_video">
                                <video src="<?php echo esc_url( $video_url ); ?>" preload="metadata"></video>
                                <i class="pro_far pro_fa-play-circle"></i>
                            </div>
						<?php endif; ?>
                    </div>
				<?php else :
					if ( has_post_thumbnail() ) : ?>
                        <div class="profolio-project-img">
							<?php the_post_thumbnail( 'project_thumbnail', array( 'class' => 'profolio-bg-img' ) ); ?>
                        </div>
					<?php endif;
				endif; ?>

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
            </div>
        </div>

		<?php
		/**
		 * Sidebar.
		 */
		?>
        <div class="profolio-col-lg-3">
            <div class="profolio-right-side">
                <div class="profolio-header-xs"><?php esc_html_e( 'Location', 'localseomap-for-elementor' ); ?></div>
                <hr>
				<?php if ( ! empty( $address ) ) : ?>
                    <div class="profolio-text-sm mb30"><i
                                class="pro_fa pro_fa-map-marker-alt"></i><?php echo implode( ', ', $address ); ?></div>
				<?php endif; ?>

				<?php
				if ( $pap_type_location == 'media' ) {
					$location = $admin->get_lat_long( $post_id );
				} else {
					$location = $admin->get_lat_long( $project_id );
				}

				$language = carbon_get_theme_option( 'pap_language' );

				if ( ! empty( $language ) ) {
					$language = '&language=' . $language;
				}

				if ( ! empty( $location['longitude'] ) && ! empty( $location['latitude'] ) ) : ?>
                    <img src="https://maps.googleapis.com/maps/api/staticmap?zoom=<?php echo esc_attr( $pap_map_zoom ); ?>&size=600x300&maptype=roadmap&markers=<?php echo esc_attr( $location['latitude'] ); ?>,<?php echo esc_attr( $location['longitude'] ); ?>&key=<?php echo get_option( '_pap_google_maps_api_key' ) . $language; ?>"
                         class="profolio-right-side-map" alt="">
				<?php endif; ?>

				<?php
				/**
				 * Leads button.
				 */

				localseomap_render_leads_button();
				?>

                <div class="profolio-header-xs"><?php esc_html_e( 'Project Details', 'localseomap-for-elementor' ); ?></div>

				<?php if ( ! empty( $project_id ) ) : ?>
                    <hr>
                    <div class="profolio-text-sm">
                        <i class="pro_fa pro_fa-list"></i>
						<?php echo esc_html__( 'Project:', 'localseomap-for-elementor' ); ?>
                    </div>

                    <a href="<?php echo get_the_permalink( $project_id ); ?>"
                       class="profolio-similar-projects"><?php echo get_the_title( $project_id ); ?></a>
                    <hr>
				<?php endif; ?>

				<?php if ( localseomap_get_status( $field_project_status, 'project' ) ) : ?>
                    <div class="profolio-dflt-text">
                        <span><?php esc_html_e( 'Status: ', 'localseomap-for-elementor' ); ?></span> <?php echo esc_html( localseomap_get_status( $field_project_status, 'project' ) ); ?>
                    </div>
				<?php endif; ?>

				<?php if ( ! empty( $state ) ) : ?>
                    <div class="profolio-dflt-text">
                        <span><?php esc_html_e( 'Project Flow State: ', 'localseomap-for-elementor' ); ?></span> <?php echo esc_html( $state ); ?>
                    </div>
				<?php endif; ?>

				<?php if ( ! empty( $author_name ) ) : ?>
                    <div class="profolio-dflt-text">
                        <span><?php esc_html_e( 'Author Name: ', 'localseomap-for-elementor' ); ?></span> <?php echo esc_html( $author_name ); ?>
                    </div>
				<?php endif; ?>

				<?php if ( ! empty( $author_page_url ) ) : ?>
                    <div class="profolio-dflt-text">
                        <span><?php esc_html_e( 'Author Page URL: ', 'localseomap-for-elementor' ); ?></span> <?php echo esc_html( $author_page_url ); ?>
                    </div>
				<?php endif; ?>

                <div class="profolio-text-sm mb30"><i
                            class="pro_far pro_fa-calendar-alt"></i><?php esc_html_e( 'Submitted: ', 'localseomap-for-elementor' ); ?><?php echo get_the_date( 'F d, Y ' ); ?>
                </div>

                <div class="profolio-header-xs"><?php esc_html_e( 'Share', 'localseomap-for-elementor' ); ?></div>
                <hr>
                <ul class="profolio-details-social">
                    <li><a href="#"
                           data-share="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>&amp;t=<?php the_title(); ?>"><i
                                    class="pro_fab pro_fa-facebook-f"></i></a href="#"></li>
                    <li><a href="#"
                           data-share="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php $url = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
					       echo esc_attr( $url ); ?>"><i class="pro_fab pro_fa-pinterest-p"></i></a></li>
                    <li><a href="#"
                           data-share="http://twitter.com/home/?status=<?php the_title(); ?> - <?php the_permalink(); ?>"><i
                                    class="pro_fab pro_fa-twitter"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php do_action( 'localseomap_after_content' ); ?>

<?php get_footer(); ?>
