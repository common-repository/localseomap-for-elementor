<?php
/*
 * $atts - the widget params
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$metaboxes = new LocalSeoMap\Metaboxes();
$prefix    = $metaboxes->get_metabox_prefix();

$media_list = array();

$media_create_datetime = carbon_get_theme_option( 'pap_sort_by_media_create_datetime' );

$args = array(
	'numberposts'    => - 1,
	'post_type'      => 'localseomap_media',
	'posts_per_page' => ! empty( $atts['posts_per_page'] ) ? $atts['posts_per_page'] : 26,
	'post_status'    => array( 'publish' ),
	'orderby'        => 'date',
	'order'          => 'DESC',
);

if ( ! empty( $atts['orderby'] ) ) {

	if ( 'ID' === $atts['orderby'] ) {
		$args['orderby']  = 'meta_value';
		$args['meta_key'] = $prefix . 'uuid';
	}

	if ( 'start_date' === $atts['orderby'] ) {
		$args['orderby']  = 'meta_value';
		$args['meta_key'] = $prefix . 'start_date';
	}
}

if ( ! empty( $media_create_datetime ) ) {
	$args['meta_key'] = 'localseomap-media_create_datetime';
	$args['orderby']  = 'meta_value_num';
	$args['order']    = 'DESC';
}

$start_date = get_post_meta( '', $prefix . 'start_date', true );

$col = ! empty( $atts['number_columns'] ) ? $atts['number_columns'] : 'profolio-col-lg-6';

$projects_query = new WP_Query( $args );
?>
<div class="container">
	<div class="profolio-row no-gutters">
		<div class="profolio-col-6 profolio-col-md-3">
			<div class="profolio-input-frame profolio-pr-15">
				<div class="profolio-dropdown-frame mb30">
					<div class="input-group profolio-open-dropdown">
						<div class="input-group-prepend">
							<span class="input-group-text" id="basic-addon1"><i class="pro_fa pro_fa-th-list"></i></span>
						</div>
						<a class="profolio-dropdown-fake-button form-control profolio-default-input"
								href="#">Category</a>
					</div>
					<ul class="profolio-search-dropdown profolio-filter-category">
						<li>
							<input type="checkbox" class="form-check-input profolio-sellect-all" id="exampleCheck0">
							<label class="form-check-label" for="exampleCheck0">Select <span
										class="profolio-d-all profolio-d-true">All</span><span
										class="profolio-d-none">None</span></label>
						</li>
						<li>
							<input type="checkbox" class="form-check-input" id="exampleCheck1">
							<label class="form-check-label" for="exampleCheck1">Bathroom Remodel</label>
						</li>
						<li>
							<input type="checkbox" class="form-check-input" id="exampleCheck2">
							<label class="form-check-label" for="exampleCheck2">Bathroom Tiling</label>
						</li>
						<li>
							<input type="checkbox" class="form-check-input" id="exampleCheck3">
							<label class="form-check-label" for="exampleCheck3">Concrete Driveway
								Installation</label>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php if ( $projects_query->have_posts() ) { ?>
		<div class="profolio-row profolio-lightgallery lightgallery-ftrd">
			<?php while ( $projects_query->have_posts() ) {
				$projects_query->the_post();

                $media_list[] = array(
                    'src'   => get_the_post_thumbnail_url( get_the_ID(), 'full' ),
                    'thumb' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
                ); ?>
				<div class="<?php echo esc_attr( $col ); ?> mb30">
					<?php include LOCALSEOMAP_PATH . 'data/template/project-media.php'; ?>
				</div>
			<?php } ?>
		</div>
	<?php } else { ?>

		<div class="text-center">
			<div class="profolio-no-result">
				<span><?php esc_html_e( 'No media found here. Please try another search.','localseomap-for-elementor'); ?></span>
				<button class="profolio-default-button"><i class="pro_fa pro_fa-chevron-left"></i> Back</button>
			</div>
		</div>
		<?php
	}
	wp_reset_postdata();
	?>
</div>

<?php
wp_localize_script( 'localseomap-main-script', 'media_data', array(
    'media_list' => json_encode( $media_list )
) );
