<?php
get_header();

$pap_top_margin    = carbon_get_theme_option( 'pap_top_margin' );
$pap_bottom_margin = carbon_get_theme_option( 'pap_bottom_margin' );
?>

    <style type="text/css">
        .profolio-container {
            min-height: 400px;
        <?php if ( ! empty( $pap_top_margin ) ) { ?> margin-top: <?php echo esc_attr( $pap_top_margin ); ?>px;
        <?php }

        if ( ! empty( $pap_bottom_margin ) ) { ?> margin-bottom: <?php echo esc_attr( $pap_bottom_margin ); ?>px;
        <?php } ?>
        }

        .profolio-login-button {
            display: inline-block;
            margin: 10px 0;
        }
    </style>
    <div class="profolio-container p-60-0-30">
        <h1 class="profolio-form-title-page"><?php esc_html_e( 'Project Input Form','localseomap-for-elementor'); ?></h1>
		<?php esc_html_e( 'You can\'t see this page','localseomap-for-elementor'); ?><br>
        <a href="<?php echo wp_login_url( get_permalink() ); ?>" class="button profolio-login-button"><?php esc_html_e( 'Please login','localseomap-for-elementor'); ?></a>
    </div>
<?php

get_footer();
