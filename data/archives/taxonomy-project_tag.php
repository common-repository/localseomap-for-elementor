<?php

get_header();

$tax_object = get_queried_object();

$plugin = new LocalSeoMap\Admin();
$prefix = $plugin->get_metabox_prefix();

$column_size = carbon_get_theme_option( 'pap_column_size');

$args = array(
    'post_type'      => 'localseomap_projects',
    'posts_per_page' => get_option( 'posts_per_page' ) ? get_option( 'posts_per_page' ) : 26,
    'post_status'    => array( 'publish' ),
    'orderby'        => 'date',
    'tax_query'      => array(
        array(
            'taxonomy' => 'localseomap_project_tag',
            'field'    => 'id',
            'terms'    => $tax_object->term_id
        )
    )
);

$start_date = get_post_meta( '', $prefix . 'start_date', true );

$projects_query = new WP_Query( $args );

$seperator_color = carbon_get_theme_option( 'pap_seperator_color');
?>

<style type="text/css">
    <?php if ( ! empty( $seperator_color ) ) : ?>
        .profolio-underline {
            background-color: <?php echo esc_attr($seperator_color); ?>;
        }
    <?php endif; ?>
</style>

<?php do_action( 'localseomap_before_content' ); ?>

<?php if ( $projects_query->have_posts() ) { ?>
    <div class="container p-90-0-60">
        <div class="profolio-section-title mb15"><b><?php echo esc_html( $tax_object->name ); ?></b></div>
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
    </div>

    <?php

} else {
    ?>
    <?php esc_html__( 'No projects found here. Please try another search.','localseomap-for-elementor'); ?>
    <?php
}
wp_reset_postdata();

do_action( 'localseomap_before_content' );

get_footer();
