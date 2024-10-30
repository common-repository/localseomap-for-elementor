<?php

get_header();

$tax_object = get_queried_object();

$plugin = new LocalSeoMap\Admin();
$prefix = $plugin->get_metabox_prefix();

$column_size = carbon_get_theme_option( 'pap_column_size' );

$taxonomy = 'localseomap_industry';

if ( is_tax( 'localseomap_project_tag' ) ) {
	$taxonomy = 'localseomap_project_tag';
}

if ( is_tax( 'localseomap_area_tags' ) ) {
	$taxonomy = 'localseomap_area_tags';
}

$args = array(
	'post_type'      => 'localseomap_projects',
	'posts_per_page' => get_option( 'posts_per_page' ) ? get_option( 'posts_per_page' ) : 26,
	'post_status'    => array( 'publish' ),
	'orderby'        => 'date',
	'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
	'tax_query'      => array(
		array(
			'taxonomy' => $taxonomy,
			'field'    => 'id',
			'terms'    => $tax_object->term_id
		)
	)
);

$start_date = get_post_meta( '', $prefix . 'start_date', true );

$projects_query = new WP_Query( $args );

$pap_top_margin    = carbon_get_theme_option( 'pap_top_margin' );
$pap_bottom_margin = carbon_get_theme_option( 'pap_bottom_margin' );

$seperator_color       = carbon_get_theme_option( 'pap_seperator_color' );
$pap_tag_buttons_color = carbon_get_theme_option( 'pap_tag_buttons_color' );
?>

    <style type="text/css">
        <?php if ( ! empty( $seperator_color ) ) : ?>
        .profolio-underline {
            background-color: <?php echo esc_attr($seperator_color); ?>;
        }
        <?php endif; ?>

        <?php if ( ! empty( $pap_tag_buttons_color ) ) : ?>
        .profolio-card-category-frame a {
            background-color: <?php echo esc_attr($pap_tag_buttons_color); ?>;
        }
        <?php endif; ?>

        .profolio-container {
            <?php if ( ! empty( $pap_top_margin ) ) { ?>
                margin-top: <?php echo esc_attr( $pap_top_margin ); ?>px;
            <?php }

            if ( ! empty( $pap_bottom_margin ) ) { ?>
                margin-bottom: <?php echo esc_attr( $pap_bottom_margin ); ?>px;
            <?php } ?>
        }
    </style>
<?php do_action( 'localseomap_before_content' ); ?>
    <div class="profolio-container p-60-0-30">


		<?php if ( $projects_query->have_posts() ) { ?>
            <div class="profolio-section-title mb15">
                <h3><?php echo esc_html( $tax_object->name ); ?></h3>
            </div>
			<?php
			$page = get_query_var( 'paged' );
			if ( empty( $page ) ): ?>
				<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
			<?php endif; ?>
            <div class="profolio-underline mb30"></div>

            <div class="profolio-row js-projects-wrapper">
				<?php while ( $projects_query->have_posts() ) : $projects_query->the_post(); ?>
                    <div class="profolio-col-12 profolio-col-sm-6 profolio-col-md-<?php echo esc_attr( $column_size ); ?>">
						<?php
						$pap_category_template = carbon_get_theme_option( 'pap_category_template');
                        if ( ! empty( $pap_category_template ) && 'modern' == $pap_category_template ) {
							include LOCALSEOMAP_PATH . 'data/template/project-archive-modern.php';
						} else {
							include LOCALSEOMAP_PATH . 'data/template/project-archive-default.php';
						} ?>
                    </div>
				<?php endwhile; ?>
            </div>

			<?php

		} else {
			?>
			<?php esc_html__( 'No projects found here. Please try another search.','localseomap-for-elementor'); ?>
			<?php
		}
		wp_reset_postdata();


		?>
        <div class="profolio-row">
            <div class="profolio-col-12 text-center profolio-pagination">
                <nav>
					<?php
					echo paginate_links( array(
						'total'        => $projects_query->max_num_pages,
						'current'      => max( 1, get_query_var( 'paged' ) ),
						'show_all'     => false,
						'type'         => 'list',
						'end_size'     => 2,
						'mid_size'     => 1,
						'prev_next'    => true,
						'prev_text'    => esc_html__( 'Previous','localseomap-for-elementor'),
						'next_text'    => esc_html__( 'Next','localseomap-for-elementor'),
						'add_args'     => false,
						'add_fragment' => '',
					) );
					?>
                </nav>
            </div>
        </div>
    </div>
<?php
do_action( 'localseomap_after_content' );

get_footer();
