<?php
$image_size = ! empty( $atts['image_size'] ) ? $atts['image_size'] : 'medium';
?>

<div class="profolio-project-list-frame mb30">
    <a href="<?php the_permalink(); ?>" >

		<?php if ( has_post_thumbnail() ) : ?>
            <div class="profolio-p-list-cover">
				<?php the_post_thumbnail( $image_size ); ?>
            </div>
		<?php endif; ?>

        <div class="profolio-project-list-hover">
            <i class="pro_fa pro_fa-search-plus"></i>
            <div class="profolio-header-xs profolio-custom-elementor"><?php the_title(); ?></div>
        </div>
    </a>
</div>
