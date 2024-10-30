<?php
/* General data */
$admin  = new LocalSeoMap\Admin();
$prefix = $admin->get_metabox_prefix();

/* The current post data */
$post_id = get_the_ID();

/* Get parent project */
$parent_project_uuid = get_post_meta( $post_id, $prefix . 'project_uuid', true );
$project_id          = $admin->get_post_id_by_meta_key_and_value( $prefix . 'uuid', $parent_project_uuid );

$video_url       = get_post_meta( $post_id, $prefix . 'field_video', true );
$author_name     = get_post_meta( $post_id, $prefix . 'field_ps_author_name', true );
$author_page_url = get_post_meta( $post_id, $prefix . 'field_ps_author_page_url', true );

$field_project_status = get_post_meta( $project_id, $prefix . 'field_project_status', true );
$state                = get_post_meta( $project_id, $prefix . 'pin_pf_state', true );

$pap_type_address = carbon_get_theme_option( 'pap_type_address' );

// Styling content
$pap_title_font_size     = get_option( '_pap_title_font_size' );
$pap_title_color         = get_option( '_pap_title_color' );
$pap_gallery_title_fs    = get_option( '_pap_gallery_title_font_size' );
$pap_gallery_title_color = get_option( '_pap_gallery_title_color' );
$pap_seperator_color     = get_option( '_pap_seperator_color' );
$pap_icon_bg_color       = get_option( '_pap_icon_bg_color' );
$pap_map_border_color    = get_option( '_pap_map_border_color' );
$pap_button_bg_color     = get_option( '_pap_button_bg_color' );
$pap_type_location       = get_option( '_pap_type_location' );
$pap_top_margin          = get_option( '_pap_top_margin' );
$pap_bottom_margin          = get_option( '_pap_bottom_margin' );


$address = array();
if ( $pap_type_location == 'media' ) {
	$media_data = get_post_meta( $post_id, $prefix . 'media_data', true );

	$address[] = $media_data['geoip_location']['city'];
	$address[] = $media_data['geoip_location']['region'];
	$address[] = $media_data['geoip_location']['country'];

	if ( isset( $pap_type_address ) && $pap_type_address == 'exact' ) {
		$address[] = get_post_meta( $post_id, $prefix . 'address', true );
	}

} else {

	$address[] = get_post_meta( $project_id, $prefix . 'city', true );
	$address[] = get_post_meta( $project_id, $prefix . 'province', true );
	$address[] = get_post_meta( $project_id, $prefix . 'country', true );

	if ( isset( $pap_type_address ) && $pap_type_address == 'exact' ) {
		$address[] = get_post_meta( $project_id, $prefix . 'address', true );
	}
}

$start_date   = get_post_meta( $project_id, $prefix . 'start_date', true );


if ( ! empty( $address ) ) {
	$address = array_filter( $address );
}

$pap_map_zoom = get_option( '_pap_map_zoom' );
if ( empty( $pap_map_zoom ) ) {
	$pap_map_zoom = '18';
}

get_header();
