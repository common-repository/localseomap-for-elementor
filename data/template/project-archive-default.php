<?php
$post_id = get_the_ID();

$start_date = get_post_meta( $post_id, $prefix . 'start_date', true );
$address    = array();
$address[]  = get_post_meta( $post_id, $prefix . 'city', true );
$address[]  = get_post_meta( $post_id, $prefix . 'province', true );
$address[]  = get_post_meta( $post_id, $prefix . 'country', true );
$address    = array_filter( $address );
?>

<div class="profolio-project-card">
	<?php if ( has_post_thumbnail() ) { ?>
        <div class="profolio-card-image-frame">
            <a href="<?php the_permalink(); ?>" class="profolio-card-cover">
				<?php the_post_thumbnail( 'medium_large' ); ?>
            </a>
        </div>
	<?php } ?>
    <div class="profolio-card-descr">
		<?php if ( empty( $atts['hide_title'] ) ) { ?>
			<?php the_title( '<a href="' . get_the_permalink() . '"  class="profolio-header-xs">', '</a>' ); ?>
		<?php } ?>

		<?php if ( ! empty( $address ) && empty( $atts['hide_location'] ) ) { ?>
            <div class="profolio-text-sm">
                <i class="pro_fa pro_fa-map-marker-alt"></i>
				<?php echo esc_html( implode( ', ', $address ) ) ?>
            </div>
		<?php } ?>
        <div class="profolio-card-descr-bottom">

			<?php
			$remove_tag_buttons = carbon_get_theme_option( 'pap_remove_tag_buttons' );
			if ( ( ! empty( $atts ) && empty( $atts['hide_industry'] ) ) || empty( $remove_tag_buttons ) ) { ?>
                <div class="profolio-card-category-frame">
					<?php the_terms( $post_id, 'localseomap_industry', '', ' ' ); ?>
                </div>
			<?php } ?>

        </div>
    </div>
</div>
