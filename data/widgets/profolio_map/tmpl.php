<?php
/*
 * $atts - the widget params
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( empty( $atts ) ) {
	global $post;
	if ( ! empty( $post->ID ) ) {

		$atts = get_post_meta( $post->ID );

		if ( ! empty( $atts ) && is_array( $atts ) ) {
			$atts = array_map( function ( $param ) {
				return ! empty( $param[0] ) ? $param[0] : '';
			}, $atts );
		}
	}
}

$plugin = new LocalSeoMap\Admin();
$prefix = $plugin->get_metabox_prefix();

if ( ! get_option( '_pap_google_maps_api_key' ) ) {
	echo esc_html__( 'Please insert your API KEY','localseomap-for-elementor');

	return;
}

if ( empty( $atts['number_projects'] ) ) {
	$atts['number_projects'] = 'all_pages';
}


if ( ! empty( $atts['number_projects'] ) && 'all_pages' == $atts['number_projects'] ) {
	$atts['locations'] = $plugin->get_map_locations();
}


if ( empty( $atts['location'] ) ) {
	$atts['location'] = '';
}

if ( empty( $atts['zoom'] ) ) {
	$atts['zoom'] = '8';
}

$atts['zoom'] = intval( $atts['zoom'] );

if ( ! empty( $atts['marker'] ) ) {
	if ( is_numeric( $atts['marker'] ) ) {
		$atts['marker'] = wp_get_attachment_url( $atts['marker'] );
	} elseif ( is_array( $atts['marker'] ) && ! empty( $atts['marker']['url'] ) ) {
		$atts['marker'] = $atts['marker']['url'];
	} else {
		unset( $atts['marker'] );
	}
} else {
	unset( $atts['marker'] );
}


$unique_id = uniqid();
$rand      = rand( 1, 50 );
$salt      = substr( md5( $rand * 200 ), 0, 10 );
$salt2     = substr( md5( $rand * 100 ), 0, 10 );
?>
<style>
	.profolio_widget_map {
		height: <?php echo !empty($atts['height']) ? $atts['height'] . 'px;' : '300px;' ?>;
	}

	.profolio_widget_map.bottom {
		position: absolute !important;
		bottom: 0;
	}

	<?php if ( isset( $atts['fullheight'] ) && ($atts['fullheight'] === 'yes'|| $atts['fullheight'] === '1') ) : ?>
	.profolio_widget_map {
		height: 100vh;
	}

	.profolio_widget_map.fixed {
		position: fixed !important;
		top: 0;
	}

	<?php endif; ?>

	<?php if ( ! empty($atts['infowindow_color_bg']) ){ ?>
	.profolio_widget_map .gm-style .gm-style-iw-c {
		background-color: <?php echo esc_attr($atts['infowindow_color_bg']); ?>;
	}

	<?php } ?>

	.profolio_widget_map .gm-style .gm-style-iw-d {
		/*overflow: auto !important;*/
		padding-bottom: 15px;
	}

	<?php if ( ! empty($atts['infowindow_color_title']) ){ ?>
	.profolio_widget_map .profolio-header-xxs {
		color: <?php echo esc_attr($atts['infowindow_color_title']); ?>;
	}

	<?php } ?>

	<?php if ( ! empty($atts['infowindow_color_location']) ){ ?>
	.profolio_widget_map .profolio-text-xs {
		color: <?php echo esc_attr($atts['infowindow_color_location']); ?>;
	}

	<?php } ?>
</style>

<style>

	#profolio_widget_map_<?php echo esc_attr( $salt ); ?> {
		background-image: url("<?php echo LOCALSEOMAP_URL; ?>data/assets/img/local-seo-map-logo.png") !important;
		position: absolute;
		top: 0;
		right: 0;
		z-index: 1000;
		width: 81px;
		height: 20px;
		background-size: contain;
		background-repeat: no-repeat;
		margin: 10px;
	}

	#profolio_widget_map_<?php echo esc_attr( $salt ); ?> a {
		display: block;
		position: absolute;
		width: 100%;
		height: 100%;
	}
</style>


<div class="profolio-map-wrapper">



	<div id="profolio_widget_map_<?php echo esc_attr( $salt2 ); ?>">
		<div id="profolio_widget_map_<?php echo esc_attr( $unique_id ); ?>"
				class="profolio_widget_map JS_profolio_map"></div>
	</div>

</div>

<script>
	window.profolio_addon_pro = {};
	profolio_addon_pro[ 'profolio_widget_map_<?php echo esc_attr( $unique_id ); ?>' ] = '<?php echo base64_encode( json_encode( $atts ) ); ?>';
</script>

<script type="text/html" id="tmpl-profolio-infowindow-template">
	<div class="profolio-map-pop">
		<div class="profolio-card-image-frame">
			<a href="{{{data.link}}}" class="profolio-card-cover-sm">
				{{{data.image}}}
			</a>
		</div>
		<div class="profolio-card-descr">
			<?php if ( empty( $atts['infowindow_hide_title'] ) ) : ?>
				<a href="{{{data.link}}}" class="profolio-header-xxs">{{{data.title}}}</a>
			<?php endif; ?>
			<# if (data && data.address) { #>
			<div class="profolio-text-xs"><i class="pro_fa pro_fa-map-marker-alt"></i>{{{data.address}}}</div>
			<# } #>
			<div class="profolio-card-category-frame">
				<# _.each(data.terms, function(term) { #>
				<a href="{{{ term.link }}}">{{{ term.name }}}</a>
				<# }); #>
			</div>
		</div>
	</div>
</script>
