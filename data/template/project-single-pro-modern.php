<?php get_header();

$admin   = new ProfolioAddonPro\Admin();
$prefix  = $admin->get_metabox_prefix();
$post_id = get_the_ID();

$media_list = array();

$pap_type_address = carbon_get_theme_option( 'pap_type_address' );

$address   = array();
$address[] = get_post_meta( $post_id, $prefix . 'city', true );
$address[] = get_post_meta( $post_id, $prefix . 'province', true );
$address[] = get_post_meta( $post_id, $prefix . 'country', true );

if ( isset( $pap_type_address ) && $pap_type_address == 'exact' ) {
	$address[] = get_post_meta( $post_id, $prefix . 'address', true );
}

$address = array_filter( $address );

// Image Gallery Options
$pap_enable_gallery = carbon_get_theme_option( 'pap_enable_gallery' );
$pap_image_size     = carbon_get_theme_option( 'pap_image_size' );
$pap_columns        = carbon_get_theme_option( 'pap_columns' );
$pap_link           = carbon_get_theme_option( 'pap_link' );
$pap_lightbox       = carbon_get_theme_option( 'pap_lightbox' );
$pap_order_by       = carbon_get_theme_option( 'pap_order_by' );

/* For media. */
$uuid = get_post_meta( $post_id, $prefix . 'uuid' );
$args = array(
	'posts_per_page' => - 1,
	'post_type'      => 'profolio_media',
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

$start_date = get_post_meta( $post_id, $prefix . 'start_date', true );

$pap_show_share_links = carbon_get_theme_option( 'pap_show_share_links' );

$pap_title_font_size     = carbon_get_theme_option( 'pap_title_font_size' );
$pap_title_color         = carbon_get_theme_option( 'pap_title_color' );
$pap_gallery_title_fs    = carbon_get_theme_option( 'pap_gallery_title_font_size' );
$pap_gallery_title_color = carbon_get_theme_option( 'pap_gallery_title_color' );
$pap_seperator_color     = carbon_get_theme_option( 'pap_seperator_color' );
$pap_icon_bg_color       = carbon_get_theme_option( 'pap_icon_bg_color' );
$pap_map_border_color    = carbon_get_theme_option( 'pap_map_border_color' );
$pap_button_bg_color     = carbon_get_theme_option( 'pap_button_bg_color' );
$pap_show_preview        = carbon_get_theme_option( 'pap_show_preview' );

$longitude  = get_post_meta( $post_id, $prefix . 'longitude', true );
$latitude   = get_post_meta( $post_id, $prefix . 'latitude', true );
$max_radius = carbon_get_theme_option( 'pap_app_max_radius' );
if ( ! is_numeric( $max_radius ) ) {
	$max_radius = 1609;
}

$rand_meter = rand( 0, $max_radius );
$rand_coef  = $rand_meter * 0.0000089;
if ( ( ! empty( $longitude ) && ! empty( $latitude ) ) && isset( $pap_type_address ) && $pap_type_address !== 'exact' ) {
	$longitude += $rand_coef / cos( $latitude * 0.018 );
	$latitude  += $rand_coef;
}

$pap_map_zoom = carbon_get_theme_option( 'pap_map_zoom' );
if ( empty( $pap_map_zoom ) ) {
	$pap_map_zoom = '18';
}

?>
	<style>
		.profolio-details-map-frame img {
			width: 100%;
			height: 100%;
			border: none;
			border-radius: 50%;
			object-fit: cover;
		}

		<?php if ( ! empty( $pap_title_font_size ) ) : ?>
		.profolio-section-title {
			font-size: <?php echo $pap_title_font_size; ?>px;
		}

		<?php endif; ?>

		<?php if ( ! empty( $pap_title_color ) ) : ?>
		.profolio-section-title {
			color: <?php echo $pap_title_color; ?>;
		}

		<?php endif; ?>

		<?php if ( ! empty( $pap_gallery_title_fs ) ) : ?>
		.profolio-gallery-title {
			font-size: <?php echo $pap_gallery_title_fs; ?>px;
		}

		<?php endif; ?>

		<?php if ( ! empty( $pap_gallery_title_color ) ) : ?>
		.profolio-gallery-title {
			color: <?php echo $pap_gallery_title_color; ?>;
		}

		<?php endif; ?>

		<?php if ( ! empty( $pap_seperator_color ) ) : ?>
		.profolio-underline,
		.swiper-pagination-bullet-active {
			background-color: <?php echo $pap_seperator_color; ?>;
		}

		<?php endif; ?>

		<?php if ( ! empty( $pap_icon_bg_color ) ) : ?>
		.profolio-elm-title span {
			background-color: <?php echo $pap_icon_bg_color; ?>;
		}

		<?php endif; ?>

		<?php if ( ! empty( $pap_map_border_color ) ) : ?>
		.profolio-details-map-frame {
			border-color: <?php echo $pap_map_border_color; ?>;
		}

		<?php endif; ?>

		<?php if ( ! empty( $pap_button_bg_color ) ) : ?>
		.profolio-default-button {
			background-color: <?php echo $pap_button_bg_color; ?>;
		}

		<?php endif; ?>

		<?php
		$pap_top_margin = carbon_get_theme_option( 'pap_top_margin' );
		if ( ! empty($pap_top_margin) ) : ?>
		.container {
			margin-top: <?php echo esc_attr($pap_top_margin); ?>px;
		}
		<?php endif; ?>
	</style>

    <?php do_action( 'profolio_addon_pro_before_content' ); ?>

	<div class="container p-90-0-60">
		<?php the_title( '<h1 class="profolio-section-title mb15"><b>', '</b></h1>' ); ?>
		<div class="profolio-underline mb30"></div>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="profolio-prjct-cover mb60">
				<?php the_post_thumbnail( 'project_thumbnail', array( 'class' => 'profolio-bg-img' ) );

				$location = $admin->get_lat_long( $post_id );

				if ( ! empty( $location['longitude'] ) && ! empty( $location['latitude'] ) ) : ?>
					<div class="profolio-details-map-frame">
						<img src="https://maps.googleapis.com/maps/api/staticmap?zoom=<?php echo esc_attr( $pap_map_zoom ); ?>&size=600x300&maptype=roadmap&markers=<?php echo esc_attr( $location['latitude'] ); ?>,<?php echo esc_attr( $location['longitude'] ); ?>&key=<?php echo carbon_get_theme_option( 'pap_google_maps_api_key' ); ?>"
								alt="">
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="row mb30">
			<?php if ( ! empty( get_the_terms( $post_id, 'project_tag' ) ) || ! empty( $start_date ) || ! empty( $address ) ) : ?>
				<div class="col-lg-12 mb30">
					<div class="profolio-elm-title mb15">
						<span><i class="fas fa-info-circle"></i></span>
						<b><?php echo esc_html__( 'Story Details', 'profolio-addon-pro' ); ?></b>
					</div>

					<b><?php echo esc_html__( 'Date', 'profolio-addon-pro' ); ?>:</b>
					<?php echo date( 'F d, Y ', strtotime( $start_date ) ); ?>
					<br>

					<b><?php echo esc_html__( 'Location', 'profolio-addon-pro' ); ?>:</b>
					<?php echo implode( ', ', $address ); ?>
					<br>

					<?php if ( ! empty( get_the_terms( $post_id, 'project_tag' ) ) ) : ?>
						<b><?php echo esc_html__( 'Tags', 'profolio-addon-pro' ); ?>:</b>
						<?php echo wp_strip_all_tags( get_the_term_list( $post_id, 'project_tag', '', ', ', '' ) ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

        <?php while ( have_posts() ) : the_post(); ?>
            <div class="profolio-dflt-text">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>

		<?php if ( $media_query->have_posts() ) :
			$count = 0;
			$gallery_class = isset( $pap_columns ) ? ' col-lg-' . $pap_columns : 'col-lg-12';
			?>
			<div class="row justify-content-between mb30">
				<div class="col-12 col-md-12 align-self-center">
					<div class="profolio-filter-frame js-profolio-filter-frame">
						<div data-filter="*" class="profolio-filter-current">
							<span><?php esc_html_e( 'All', 'profolio-addon-pro' ); ?></span>
						</div>
						<div data-filter="before">
							<span><?php esc_html_e( 'Before', 'profolio-addon-pro' ); ?></span>
						</div>
						<div data-filter="after">
							<span><?php esc_html_e( 'After', 'profolio-addon-pro' ); ?></span>
						</div>
						<div data-filter="during">
							<span><?php esc_html_e( 'During', 'profolio-addon-pro' ); ?></span>
						</div>
					</div>
				</div>
			</div>

			<div class="profolio-lightgallery profolio-simple-gallery row js-profolio-filter-wrapper">
				<?php while ( $media_query->have_posts() ) : $media_query->the_post();
					$state       = get_post_meta( get_the_ID(), $prefix . 'pin_pf_state', true );
					$field_video = get_post_meta( get_the_ID(), $prefix . 'field_video', true );
					?>
					<div class="col-12 col-sm-6 col-md-6 js-profolio-filter-item <?php echo esc_attr( $gallery_class ); ?>"
							data-category="<?php echo esc_attr( $state ); ?>">
						<a class="profolio-slctr" href="<?php the_post_thumbnail_url( 'full' ) ?>"
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
										<i class="far fa-play-circle"></i>
									</div>
									<div style="display:none;" id="media_video_<?php echo esc_attr( $count ); ?>">
										<video class="lg-video-object lg-html5" controls preload="none">
											<source src="<?php echo esc_url( $field_video ); ?>" type="video/mp4">
											<?php esc_html_e( 'Your browser does not support HTML5 video.', 'profolio-addon-pro' ); ?>
										</video>
									</div>
								<?php else :
									$media_list[] = array(
										'src'   => get_the_post_thumbnail_url( get_the_ID(), 'full' ),
										'thumb' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
									); ?>
									<div class="profolio-img-hover"><i class="fas fa-search-plus"></i></div>
								<?php endif; ?>
							</div>
						</a>
						<?php the_title( '<div class="profolio-media-more"><a href="' . get_the_permalink() . '">', '</a></div>' ); ?>
					</div>
					<?php $count ++; endwhile; ?>
			</div>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

		<div class="profolio-next-prev">
			<?php
			$prev_post = get_previous_post();
			$next_post = get_next_post();
			?>

			<?php if ( ! empty( $prev_post ) ) : ?>
				<a href="<?php echo get_the_permalink( $prev_post ); ?>" class="profolio-link profolio-r-link mb30">
					<span><?php esc_html_e( 'Previous story', 'profolio-addon-pro' ); ?></span>
					<i class="fas fa-chevron-left"></i>
				</a>
			<?php endif; ?>

			<?php if ( ! empty( $next_post ) ) : ?>
				<a href="<?php echo get_the_permalink( $next_post ); ?>"
						class="profolio-link profolio-l-link mb30">
					<i class="fas fa-chevron-right"></i>
					<span><?php esc_html_e( 'Next story', 'profolio-addon-pro' ); ?></span>
				</a>
			<?php endif; ?>

		</div>
	</div>

<?php do_action( 'profolio_addon_pro_after_content' ); ?>

<?php
wp_localize_script( 'main-script', 'media_data', array(
	'media_list' => json_encode( $media_list )
) );

get_footer();
