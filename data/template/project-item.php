<?php
$post_id = get_the_ID();

$address   = array();
$address[] = get_post_meta( $post_id, $prefix . 'city', true );
$address[] = get_post_meta( $post_id, $prefix . 'province', true );
$address[] = get_post_meta( $post_id, $prefix . 'country', true );
$address   = array_filter( $address );

$image_size = ! empty( $atts['image_size'] ) ? $atts['image_size'] : 'medium_large';
?>

<div class="profolio-project-card" >
	<?php if ( has_post_thumbnail() ) { ?>
        <div class="profolio-card-image-frame">
            <a href="<?php the_permalink(); ?>" class="profolio-card-cover">
				<?php the_post_thumbnail( $image_size ); ?>
            </a>
        </div>
	<?php } ?>
    <div class="profolio-card-descr">
		<?php if ( empty( $atts['hide_title'] ) ) { ?>
			<?php the_title( '<a href="' . get_the_permalink() . '"  class="profolio-header-xs profolio-custom-elementor">', '</a>' ); ?>
		<?php } ?>

		<?php if ( ! empty( $address ) && empty( $atts['hide_location'] ) ) { ?>
            <div class="profolio-text-sm">
                <i class="pro_fa pro_fa-map-marker-alt"></i>
				<?php echo esc_html( implode( ', ', $address ) ) ?>
            </div>
		<?php } ?>
        <div class="profolio-card-descr-bottom">

			<?php if ( empty( $atts['hide_industry'] ) ) { ?>
                <div class="profolio-card-category-frame">
					<?php the_terms( $post_id, 'localseomap_industry', '', ' ' ); ?>
                </div>
			<?php } ?>

            <?php if ( ! empty( $start_date ) ) { ?>
				<?php echo esc_html( $start_date ); ?>
			<?php } ?>
        </div>
    </div>
</div>

