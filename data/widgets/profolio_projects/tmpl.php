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


$type_terms_filter = ! empty( $atts['tags_instead_industry'] ) ? 'filter_tags' : 'filter_terms';
$type_terms        = ! empty( $atts['tags_instead_industry'] ) ? 'localseomap_project_tag' : 'localseomap_industry';

if ( ! empty( $atts[ $type_terms_filter ] ) ) {

	$terms = $atts[ $type_terms_filter ];
}


$term_ids = array();
if ( ! empty( $terms ) ) {
	foreach ( $terms as $term ) {
		$term_id    = is_numeric( $term ) ? $term : $term->term_id;
		$term_ids[] = $term_id;
	}
}


$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

if ( empty( $atts['posts_per_page'] ) ) {
	$atts['posts_per_page'] = 26;
}

$args = array(
	'post_type'      => 'localseomap_projects',
	'posts_per_page' => $atts['posts_per_page'],
	'post_status'    => array( 'publish' ),
	'orderby'        => 'date',
	'order'          => 'DESC',
	'paged'          => $paged
);

if ( ! empty( $term_ids ) ) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => $type_terms,
			'field'    => 'term_id',
			'terms'    => $term_ids
		)
	);
}

if ( ! empty( $atts['orderby'] ) ) {

	if ( 'ID' === $atts['orderby'] ) {
		$args['orderby']  = 'meta_value';
		$args['meta_key'] = $prefix . 'uuid';
	}


	if ( 'start_date' === $atts['orderby'] ) {

		$args['orderby'] = array(
			$prefix . 'start_date' => 'DESC'
		);
	}

}

/*$args['meta_query'] = array(
	'relation' => 'AND',
	array(
		'relation' => 'OR',
		array(
			'key'     => $prefix . 'start_date',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => $prefix . 'start_date',
			'compare' => 'NOT EXISTS',
		)
	)
);*/

if ( ! empty( $atts['pro_project'] ) ) {
	$args['meta_query'][] = array(
		'key'   => $prefix . 'field_project_pro',
		'value' => '1'
	);
}

$start_date = get_post_meta( '', $prefix . 'start_date', true );

$col = ! empty( $atts['number_columns'] ) ? $atts['number_columns'] : 'profolio-col-lg-6';

$projects_query = new WP_Query( $args );

$wrap_class  = 'js_profolio_project';
$wrap2_class = 'profolio-cards-row';
if ( empty( $atts['style'] ) ) {
	$wrap_class  = 'js_profolio_project profolio-project-cards';
	$wrap2_class = 'profolio-cards-row';
}


$ids = array(); ?>

    <style>
        <?php if ( ! empty( $atts['category_background_shape'] ) ) : ?>
        .profolio-card-category-frame a {
            background-color: <?php echo esc_attr($atts['category_background_shape']); ?>;
        }

        <?php endif; ?>

        <?php if ( ! empty( $atts['category_color_shape'] ) ) : ?>
        .profolio-card-category-frame a {
            color: <?php echo esc_attr($atts['category_color_shape']); ?>;
        }

        <?php endif; ?>

        <?php if ( ! empty( $atts['title_color'] ) ) : ?>
        .profolio-header-xs {
            color: <?php echo esc_attr($atts['title_color']); ?> !important;
        }

        <?php endif; ?>

        <?php if ( ! empty( $atts['remove_bg'] ) ) : ?>
        .profolio-project-cards {
            background: none;
        }

        <?php endif; ?>

        .rp-cart-loader {
            background-color: rgba(255, 255, 255, .5);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .rp-cart-loader img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 85px;
        }

        .profolio-pagination li a,
        .profolio-pagination li span {
        <?php
		if ( ! empty( $atts['pagination_text_color'] ) ) {
			echo 'color:' . $atts['pagination_text_color'].';';
		}

		if ( ! empty( $atts['pagination_background_color'] ) ) {
			echo 'background-color:' . $atts['pagination_background_color'].';';
		}

		?>
        }

        .profolio-pagination .current {
        <?php
			if ( ! empty( $atts['pagination_active_text_color'] ) ) {
				echo 'color:' . $atts['pagination_active_text_color'].';';
			}
			if ( ! empty( $atts['pagination_active_background_color'] ) ) {
				echo 'background-color:' . $atts['pagination_active_background_color'].';';
			}
			 ?>
        }

        .profolio-pagination li a:hover {
        <?php

		if ( ! empty( $atts['pagination_hover_background_color'] ) ) {
			echo 'background-color:' . $atts['pagination_hover_background_color'].';';
		}

		?>
        }

        }
    </style>

<?php if ( $projects_query->have_posts() ) { ?>
    <div class="<?php echo esc_attr( $wrap_class ); ?>" data-atts='<?php echo esc_attr(json_encode( $atts )); ?>'>
        <div class="profolio-row <?php echo esc_attr( $wrap2_class ); ?>">
			<?php while ( $projects_query->have_posts() ) :
				$projects_query->the_post();

				$ids[] = get_the_ID();

				$longitude = get_post_meta( get_the_ID(), $prefix . 'longitude', true );
				$latitude  = get_post_meta( get_the_ID(), $prefix . 'latitude', true );

				$modal = '';
				if ( carbon_get_theme_option( 'pap_show_project_in_modal' ) ) {
					$modal = 'enable_modal';
				}
				?>
                <div class="profolio-col-xs-12 <?php echo esc_attr( $col ); ?> JS_profolio_project_item <?php echo esc_attr( $modal ); ?>"
                     data-latlong="<?php echo esc_attr( $latitude . ',' . $longitude ); ?>"
                     data-post-id="<?php the_ID(); ?>">
					<?php
					if ( ! empty( $atts['style'] ) && 'project_list' == $atts['style'] ) {
						include LOCALSEOMAP_PATH . 'data/template/project-item-list.php';
					} else {
						include LOCALSEOMAP_PATH . 'data/template/project-item.php';
					} ?>
                </div>
			<?php endwhile; ?>
        </div>
		<?php if ( localseomap()->is_plan( 'starter' ) && ! empty( $atts['show_pagination'] ) ) { ?>
            <div class="profolio-row">
                <div class="profolio-col-12 text-center profolio-pagination">
                    <nav>
						<?php
						echo paginate_links( array(
							'total'        => $projects_query->max_num_pages,
							'current'      => max( 1, get_query_var( 'paged' ) ),
							'show_all'     => false,
							'type'         => 'list',
							'end_size'     => 2,
							'mid_size'     => 1,
							'prev_next'    => true,
							'prev_text'    => esc_html__( 'Previous', 'localseomap-for-elementor' ),
							'next_text'    => esc_html__( 'Next', 'localseomap-for-elementor' ),
							'add_args'     => false,
							'add_fragment' => '',
						) );
						?>
                    </nav>
                </div>
            </div>
		<?php } ?>
    </div>
	<?php

	?>
    <script>
			window.projects_per_page = <?php echo json_encode( $atts['posts_per_page'] ); ?>;

			window.profolio_project_ids = <?php echo json_encode( $ids ) ?>;

			window.localseomap_ajax_locations = '<?php echo base64_encode( json_encode( $plugin->get_map_locations( $ids ) ) ); ?>';
    </script>

	<?php

} else {
	?>
	<?php esc_html__( 'No projects found here. Please try another search.', 'localseomap-for-elementor' ); ?>
	<?php
}
wp_reset_postdata();
