<?php

get_header();

$plugin = new LocalSeoMap\Admin();
$prefix = $plugin->get_metabox_prefix();

$terms = get_terms( 'localseomap_industry', [
	'hide_empty' => false,
] );

$column_size = carbon_get_theme_option( 'pap_column_size' );

$args = array(
	'numberposts'    => - 1,
	'post_type'      => 'localseomap_projects',
	'posts_per_page' => get_option( 'posts_per_page' ) ? get_option( 'posts_per_page' ) : 26,
	'post_status'    => array( 'publish' ),
	'orderby'        => 'date'
);

$start_date = get_post_meta( '', $prefix . 'start_date', true );

$projects_query = new WP_Query( $args );
?>
    <div class="container p-90-0-60">
		<?php
		do_action( 'localseomap_before_content' );
		if ( $projects_query->have_posts() ) { ?>

            <div class="profolio-section-title mb15"><b><?php esc_html_e( 'Project List','localseomap-for-elementor'); ?></b>
            </div>
			<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>

            <div class="profolio-underline mb30"></div>

			<?php if ( ! empty( $terms ) ) : ?>
                <div class="profolio-row no-gutters">
                    <div class="profolio-col-6 profolio-col-md-3">
                        <div class="profolio-input-frame profolio-pr-15">
                            <div class="profolio-dropdown-frame mb30">
                                <div class="input-group profolio-open-dropdown">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="pro_fa pro_fa-th-list"></i></span>
                                    </div>
                                    <span class="profolio-dropdown-fake-button form-control profolio-default-input"><?php esc_html_e( 'Category','localseomap-for-elementor'); ?></span>
                                </div>
                                <ul class="profolio-search-dropdown js-category-filter">
                                    <li>
                                        <input type="checkbox" class="form-check-input js-form-check-input profolio-sellect-all" id="exampleCheck0">
                                        <label class="form-check-label" for="exampleCheck0"><?php esc_html_e( 'Select','localseomap-for-elementor'); ?>
                                            <span class="profolio-d-all profolio-d-true"><?php esc_html_e( 'All','localseomap-for-elementor'); ?></span><span class="profolio-d-none"><?php esc_html_e( 'None','localseomap-for-elementor'); ?></span></label>
                                    </li>
									<?php foreach ( $terms as $key => $term ) : $key ++; ?>
                                        <li>
                                            <input type="checkbox" class="form-check-input js-form-check-input" id="exampleCheck<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $term->term_id ); ?>">
                                            <label class="form-check-label" for="exampleCheck<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $term->name ); ?></label>
                                        </li>
									<?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
			<?php endif; ?>

            <div class="profolio-row js-projects-wrapper">
				<?php while ( $projects_query->have_posts() ) : $projects_query->the_post(); ?>
                    <div class="profolio-col-12 profolio-col-sm-6 profolio-col-md-<?php echo esc_attr( $column_size ); ?>">
						<?php
						$pap_category_template = carbon_get_theme_option( 'pap_category_template' );
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

		do_action( 'localseomap_after_content' );
		?>
    </div>
<?php
get_footer();
