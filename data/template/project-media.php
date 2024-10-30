<div class="profolio-ftrd-item-frame">
    <a class="profolio-lg-item profolio-slctr" href="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'project_thumbnail' ) ?>">
		<?php the_post_thumbnail( 'medium', array( 'class' => 'profolio-bg-img' ) ); ?>
        <div class="profolio-img-hover">
            <i class="pro_fa pro_fa-search-plus"></i>
            <div class="profolio-header-xxs"><?php the_title(); ?></div>
        </div>
    </a>
</div>
