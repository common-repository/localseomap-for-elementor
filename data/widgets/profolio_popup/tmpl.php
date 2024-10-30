<?php
/*
 * $atts - the widget params
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$pap_show_leads_button = carbon_get_theme_option( 'pap_show_leads_button' );
$pap_color_btn = carbon_get_theme_option( 'pap_color_btn' );
$pap_bg_color_btn = carbon_get_theme_option( 'pap_bg_color_btn' );
$pap_font_size_btn = carbon_get_theme_option( 'pap_font_size_btn' );
$field_external_services_id = carbon_get_theme_option( 'field_external_services_id' );

if ( isset( $pap_show_leads_button ) && $pap_show_leads_button && localseomap()->is_plan( 'starter' )  ) : ?>

    <style type="text/css">
        .profolio-leads-btn {
            <?php if ( ! empty( $pap_font_size_btn ) ) : ?>
                font-size: <?php echo esc_attr($pap_font_size_btn); ?>px;
            <?php endif; ?>

            <?php if ( ! empty( $pap_color_btn ) ) : ?>
                color: <?php echo esc_attr($pap_color_btn); ?>;
            <?php endif; ?>

            <?php if ( ! empty( $pap_bg_color_btn ) ) : ?>
                background-color: <?php echo esc_attr($pap_bg_color_btn); ?>;
                border-color: <?php echo esc_attr($pap_bg_color_btn); ?>;
            <?php endif; ?>
        }

        .profolio-leads-btn:focus {
            <?php if ( ! empty( $pap_color_btn ) ) : ?>
                color: <?php echo esc_attr($pap_color_btn); ?>;
            <?php endif; ?>
        }

        .profolio-leads-btn:hover {
            <?php if ( ! empty( $pap_bg_color_btn ) ) : ?>
                background-color: <?php echo esc_attr($pap_bg_color_btn); ?>;
                border-color: <?php echo esc_attr($pap_bg_color_btn); ?>;
            <?php endif; ?>
        }
    </style>

<?php

    localseomap_leads_popup();

endif;
