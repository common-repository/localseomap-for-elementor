<?php include 'media-header.php';

$pap_show_leads_button = carbon_get_theme_option( 'pap_show_leads_button' );
$pap_override_btn_redirect = carbon_get_theme_option( 'pap_override_btn_redirect' );
$pap_redirect_url_btn = carbon_get_theme_option( 'pap_redirect_url_btn' );
$pap_leads_button_label = carbon_get_theme_option( 'pap_leads_button_label' ) ? carbon_get_theme_option( 'pap_leads_button_label' ) : esc_html__('Contact Us','localseomap-for-elementor');
?>

<style>
    <?php if ( ! empty( $pap_title_font_size ) ) : ?>
    .profolio-section-title {
        font-size: <?php echo esc_attr($pap_title_font_size); ?>px;
    }

    <?php endif; ?>

    <?php if ( ! empty( $pap_title_color ) ) : ?>
    .profolio-section-title {
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
    .profolio-underline,
    .swiper-pagination-bullet-active {
        background-color: <?php echo esc_attr($pap_seperator_color); ?>;
    }

    <?php endif; ?>

    <?php if ( ! empty( $pap_icon_bg_color ) ) : ?>
    .profolio-elm-title span {
        background-color: <?php echo esc_attr($pap_icon_bg_color); ?>;
    }

    <?php endif; ?>

    <?php if ( ! empty( $pap_map_border_color ) ) : ?>
    .profolio-details-map-frame {
        border-color: <?php echo esc_attr($pap_map_border_color); ?>;
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
	<?php
	$hide_project_title = carbon_get_theme_option( 'pap_hide_project_title' );
	if ( empty( $hide_project_title ) ):
		the_title( '<h1 class="profolio-section-title mb15"><b>', '</b></h1>' );
	endif; ?>
    <div class="profolio-underline mb30"></div>

	<?php if ( ! empty( $video_url ) ) : ?>
        <div style="display:none;" id="testimonial_video">
            <video class="lg-video-object lg-html5" autoplay controls preload="none">
                <source src="<?php echo esc_url( $video_url ); ?>" type="video/mp4">
				<?php esc_html_e( 'Your browser does not support HTML5 video.','localseomap-for-elementor'); ?>
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
            <div class="profolio-prjct-cover mb60">
				<?php the_post_thumbnail( 'project_thumbnail', array( 'class' => 'profolio-bg-img' ) );

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
                    <div class="profolio-details-map-frame" style="background-position: center;">
                        <img src="https://maps.googleapis.com/maps/api/staticmap?zoom=<?php echo esc_attr( $pap_map_zoom ); ?>&size=600x300&maptype=roadmap&markers=<?php echo esc_attr( $location['latitude'] ); ?>,<?php echo esc_attr( $location['longitude'] ); ?>&key=<?php echo get_option( '_pap_google_maps_api_key' ) . $language; ?>"
                             class="profolio-bg-img" alt="">
                    </div>
				<?php endif; ?>
            </div>
		<?php endif;
	endif; ?>

	<?php
	/**
	 * Leads button.
	 */

	localseomap_render_leads_button( 'mobile' );
	?>

    <div class="profolio-row mb30">
		<?php if ( ! empty( $project_id ) ) : ?>
            <div class="profolio-col-lg-3 mb30">
                <div class="profolio-elm-title mb15"><span><i
                                class="pro_fa pro_fa-clipboard-list"></i></span><b><?php esc_html_e( 'Project Details','localseomap-for-elementor'); ?></b>
                </div>
                <div class="profolio-similar-projects">
                    <a href="<?php echo get_the_permalink( $project_id ); ?>"><?php echo get_the_title( $project_id ); ?></a>
                </div>

				<?php if ( localseomap_get_status( $field_project_status, 'project' ) ) : ?>
                    <div class="profolio-dflt-text">
                        <span><?php esc_html_e( 'Status: ','localseomap-for-elementor'); ?></span> <?php echo esc_html( localseomap_get_status( $field_project_status, 'project' ) ); ?>
                    </div>
				<?php endif; ?>

				<?php if ( ! empty( $state ) ) : ?>
                    <div class="profolio-dflt-text">
                        <span><?php esc_html_e( 'Project Flow State: ','localseomap-for-elementor'); ?></span> <?php echo esc_html($state); ?>
                    </div>
				<?php endif; ?>

				<?php if ( ! empty( $author_name ) ) : ?>
                    <div class="profolio-dflt-text">
                        <span><?php esc_html_e( 'Author Name: ','localseomap-for-elementor'); ?></span> <?php echo esc_html($author_name); ?>
                    </div>
				<?php endif; ?>

				<?php if ( ! empty( $author_page_url ) ) : ?>
                    <div class="profolio-dflt-text">
                        <span><?php esc_html_e( 'Author Page URL: ','localseomap-for-elementor'); ?></span> <?php echo esc_html($author_page_url); ?>
                    </div>
				<?php endif; ?>

				<?php if ( ! empty( $start_date ) ) : ?>
                    <div class="profolio-dflt-text">
                        <span><?php esc_html_e( 'Start Date: ','localseomap-for-elementor'); ?></span> <?php echo date( 'F d, Y ', strtotime( $start_date ) ); ?>
                    </div>
				<?php endif; ?>
            </div>
		<?php endif; ?>

        <div class="profolio-col-lg-3 mb30">
            <div class="profolio-elm-title mb15"><span><i
                            class="pro_fa pro_fa-info-circle"></i></span><b><?php esc_html_e( 'Media Details: ','localseomap-for-elementor'); ?></b>
            </div>
            <div class="profolio-text-sm mb30"><i
                        class="pro_far pro_fa-calendar-alt"></i><?php esc_html_e( 'Submitted: ','localseomap-for-elementor'); ?><?php echo get_the_date( 'F d, Y ' ); ?>
            </div>
        </div>

		<?php if ( ! empty( $address ) ) : ?>
            <div class="profolio-col-lg-6 mb30">
                <div class="profolio-elm-title mb15"><span><i
                                class="pro_fa pro_fa-map-marker-alt"></i></span><b><?php esc_html_e( 'Location: ','localseomap-for-elementor'); ?></b>
                </div>
                <div class="profolio-dflt-text"><?php echo implode( ', ', $address ); ?></div>
            </div>
		<?php endif; ?>

	    <?php
	    /**
	     * Leads button.
	     */

	    localseomap_render_leads_button();
	    ?>
    </div>

	<?php
	$seo = new LocalSeoMap\Seo();
	echo $seo->the_schema_video( get_the_ID() );
	echo $seo->the_schema_video_testimonial( get_the_ID() );

	echo $seo->the_schema_review( get_the_ID() );
	?>

	<?php while ( have_posts() ) : the_post(); ?>
        <div class="profolio-project-desc">
			<?php the_content(); ?>
        </div>
	<?php endwhile; ?>
</div>

<?php do_action( 'localseomap_after_content' ); ?>

<?php get_footer(); ?>
