<?php
get_header();
do_action( 'localseomap_before_content' );

$pap_top_margin    = carbon_get_theme_option( 'pap_top_margin' );
$pap_bottom_margin = carbon_get_theme_option( 'pap_bottom_margin' );
$project_id        = sanitize_text_field( get_query_var( 'project_id' ) );


$title = __( 'Project Input Form', 'localseomap-for-elementor' );
if ( ! empty( $project_id ) ) {
	$title = esc_html__( 'Editing: ', 'localseomap-for-elementor' ) . get_the_title( $project_id );
}
?>

    <style type="text/css">
        .profolio-container {
        <?php if ( ! empty( $pap_top_margin ) ) { ?> margin-top: <?php echo esc_attr( $pap_top_margin ); ?>px;
        <?php }

        if ( ! empty( $pap_bottom_margin ) ) { ?> margin-bottom: <?php echo esc_attr( $pap_bottom_margin ); ?>px;
        <?php } ?>
        }
    </style>

    <div class="profolio-container p-60-0-30">
        <h1 class="profolio-form-title-page"><?php echo esc_html( $title ); ?></h1>
		<?php
		include LOCALSEOMAP_PATH . 'data/template/front_ui_form.php';
		?>
    </div>
<?php
do_action( 'localseomap_after_content' );
get_footer();
