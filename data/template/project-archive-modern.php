<div class="profolio-project-list-frame mb30">
    <a href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
            <div class="profolio-p-list-cover">
				<?php the_post_thumbnail( 'medium' ); ?>
            </div>
		<?php endif; ?>

        <div class="profolio-project-list-hover">
            <i class="pro_fa pro_fa-search-plus"></i>
			<?php the_title('<div class="profolio-elm-title"><b>','</b></div>'); ?>
        </div>
    </a>
</div>
