<?php

use LocalSeoMap\Admin;
use LocalSeoMap\Leads;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* Include builder */
include_once plugin_dir_path( dirname( __FILE__ ) ) . 'profolio-builder/core.php';


function localseomap_elementor_widget_categories( $elements_manager ) {

	$elements_manager->add_category(
		'profolio_widgets',
		[
			'title' => 'LocalSEO Widgets',
			'icon'  => 'fa fa-plug',
		]
	);

}

add_action( 'elementor/elements/categories_registered', 'localseomap_elementor_widget_categories' );

function localseomap_elementor_widgets_registered( $widgets_manager ) {


	$directories = glob( plugin_dir_path( dirname( __FILE__ ) ) . 'data/widgets/*', GLOB_ONLYDIR );

	foreach ( $directories as $directory ) {
		include_once $directory . '/elementor.php';

		if ( ! empty( $widgets_manager ) ) {
			$widget = 'Elementor\ProfolioBuilder_' . ucfirst( basename( $directory ) );
			$widgets_manager->register_widget_type( new $widget() );
		}
	}

}

add_action( 'elementor/widgets/widgets_registered', 'localseomap_elementor_widgets_registered' );

function localseomap_elementor_icons() {

	$directories = glob( plugin_dir_path( dirname( __FILE__ ) ) . 'data/widgets/*', GLOB_ONLYDIR );
	//$plugin      = new LocalSeoMap\Admin();

	foreach ( $directories as $directory ) {
		$add_icon = '.profolio-builder-' . str_replace( 'profolio_', '', basename( $directory ) ) . '-icon{
			display: inline-block;
		    width: 35px;
		    height: 35px;
		    background-size: contain; 
		    background-repeat: no-repeat;
		    background-position: center;
			background-image: url("' . plugin_dir_url( $directory ) . basename( $directory ) . '/icon.svg?' . LOCALSEOMAP_VERSION . '") }';


		wp_add_inline_style( 'elementor-common', $add_icon );
	}

}

add_action( 'elementor/editor/before_enqueue_styles', 'localseomap_elementor_icons', 100 );
add_action( 'admin_init', 'localseomap_elementor_icons', 100 );
/**
 *
 */
function localseomap_wpbakery_init() {

	$directories = glob( plugin_dir_path( dirname( __FILE__ ) ) . 'data/widgets/*', GLOB_ONLYDIR );
	foreach ( $directories as $directory ) {

		$widget = 'ProfolioBuilder\\WPBakery_' . ucfirst( basename( $directory ) );

		if ( class_exists( $widget ) ) {
			continue;
		}

		if ( is_readable( $directory . '/wpbakery.php' ) ) {
			include_once $directory . '/wpbakery.php';
		}

		/**
		 * Init class.
		 * @var string $widget $exception
		 */

		try {
			if ( class_exists( $widget ) ) {
				new $widget();
			}
		} catch ( Exception $e ) {
			echo esc_html( $e )->getMessage();

		}
	}
}

add_action( 'admin_init', 'localseomap_wpbakery_init', 100 );
add_action( 'wp', 'localseomap_wpbakery_init' );

function localseomap_wp_enqueue_scripts() {
	wp_enqueue_style( 'localseomap-bootstrap', plugin_dir_url( __FILE__ ) . '/assets/css/bootstrap.min.css', '', '4.3.1' );

	//if ( ! carbon_get_theme_option( 'disable_fontawesome' ) ) {
	wp_enqueue_style( 'localseomap-icons', plugin_dir_url( __FILE__ ) . '/assets/css/all.css', '', LOCALSEOMAP_VERSION );
	//}
	wp_enqueue_style( 'localseomap-popup', plugin_dir_url( __FILE__ ) . '/assets/css/magnific-popup.css', '', LOCALSEOMAP_VERSION );

	wp_enqueue_style( 'localseomap-lightgallery', plugin_dir_url( __FILE__ ) . '/assets/css/lightgallery.min.css', '', LOCALSEOMAP_VERSION );
	if ( is_single() && 'localseomap_projects' === get_post_type() ) {
		wp_enqueue_style( 'swiper', plugin_dir_url( __FILE__ ) . 'assets/css/swiper.min.css' );
		wp_enqueue_style( 'localseomap-before-after', plugin_dir_url( __FILE__ ) . 'assets/css/before-after.min.css' );
	}

	if ( carbon_get_theme_option( 'pap_single_project_template' ) === 'modern' ) {
		wp_enqueue_style( 'localseomap_css', plugin_dir_url( __FILE__ ) . '/assets/css/style-modern.css', '', LOCALSEOMAP_VERSION );
	} else {
		wp_enqueue_style( 'localseomap_css', plugin_dir_url( __FILE__ ) . '/assets/css/style.css', '', LOCALSEOMAP_VERSION );
	}

	$language = carbon_get_theme_option( 'pap_language' );

	if ( ! empty( $language ) ) {
		$language = '&language=' . $language;
	}

	wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . get_option( '_pap_google_maps_api_key' ) . '&libraries=places,geometry' . $language, '', '' );
	wp_enqueue_script( 'wp-util' );

	if ( is_single() && 'localseomap_projects' === get_post_type() ) {
		wp_enqueue_script( 'swiper', plugin_dir_url( __FILE__ ) . 'assets/js/swiper.min.js', array( 'jquery' ), LOCALSEOMAP_VERSION, true );
		wp_enqueue_script( 'localseomap-before-after', plugin_dir_url( __FILE__ ) . 'assets/js/before-after.min.js', array( 'jquery' ), LOCALSEOMAP_VERSION, true );
	}

	wp_enqueue_script( 'lightgallery', plugin_dir_url( __FILE__ ) . 'assets/js/lightgallery-all.min.js', array( 'jquery' ), LOCALSEOMAP_VERSION, true );

	wp_enqueue_script( 'magnific-popup', plugin_dir_url( __FILE__ ) . 'assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), LOCALSEOMAP_VERSION, true );

	// localseomap
	wp_enqueue_script( 'localseomap-main-script', plugin_dir_url( __FILE__ ) . 'assets/js/data.js', array(
		'wp-api',
		'jquery'
	), LOCALSEOMAP_VERSION, true );

	wp_enqueue_script(
		'localseomap-map',
		plugin_dir_url( __FILE__ ) . 'assets/js/map.js',
		array(
			'google-maps',
			'jquery',
		),
		LOCALSEOMAP_VERSION,
		true
	);

	global $wp_query;
	$total            = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
	$paged            = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : 1;
	$pap_type_address = carbon_get_theme_option( 'pap_type_address' );
	$max_radius       = carbon_get_theme_option( 'pap_app_max_radius' );
	if ( empty( $max_radius ) ) {
		$max_radius = 1609;
	}

	$rand_meter = rand( 0, $max_radius );

	wp_localize_script(
		'localseomap-main-script',
		'localseomap_object',
		array(
			'ajaxurl'      => admin_url( 'admin-ajax.php' ),
			'directoryurl' => plugin_dir_url( __FILE__ ),
			'data'         => array(
				'action' => 'localseomap_get_products',
			),
			'type_address' => $pap_type_address,
			'max_radius'   => $max_radius,
			'rand_meter'   => $rand_meter,
			'startPage'    => $paged,
			'maxPage'      => $total,
			'nextLink'     => next_posts( $total, false ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'localseomap_wp_enqueue_scripts', 100 );
add_action( 'elementor/frontend/after_register_scripts', 'localseomap_wp_enqueue_scripts', 100 );

/**
 * Admin enqueue scripts.
 */
function localseomap_admin_enqueue_scripts() {
	wp_enqueue_style( 'admin-style', plugin_dir_url( __FILE__ ) . '/assets/admin/css/main.css', '', LOCALSEOMAP_VERSION );
	wp_enqueue_script( 'admin-script', plugin_dir_url( __FILE__ ) . 'assets/admin/js/admin.js', array(
		'jquery',
		'wp-api'
	), LOCALSEOMAP_VERSION, true );
}

add_action( 'admin_enqueue_scripts', 'localseomap_admin_enqueue_scripts' );

/**
 * Add archive pages.
 *
 * @param $template
 *
 * @return string
 */
function localseomap_include_archive_pages( $template ) {
	if ( is_tax( 'localseomap_industry' ) || is_tax( 'localseomap_project_tag' ) || is_tax( 'localseomap_area_tags' ) ) {
		$template = plugin_dir_path( dirname( __FILE__ ) ) . 'data/archives/taxonomy-industry.php';
	}

	if ( is_post_type_archive( 'localseomap_projects' ) ) {
		$template = plugin_dir_path( dirname( __FILE__ ) ) . 'data/archives/archive-profolio_projects.php';
	}

	return $template;
}

add_filter( 'template_include', 'localseomap_include_archive_pages' );

/**
 * Filter projects by category.
 */
function localseomap_filter_by_category() {
	$taxonomy = 'localseomap_industry';

	$column_size = carbon_get_theme_option( 'pap_column_size' );
	//$posts_per_page = get_option( 'posts_per_page' );

	// limit of posts
	$posts_per_page = 100;

	$args = array(
		'post_type'      => 'localseomap_projects',
		'posts_per_page' => ! empty( $posts_per_page ) ? $posts_per_page : 26,
		'post_status'    => array( 'publish' ),
		'orderby'        => 'date',
	);

	if ( ! empty( $_POST['tags_instead_industry'] ) && $_POST['tags_instead_industry'] == 'localseomap_project_tag' ) {
		$taxonomy = sanitize_text_field( $_POST['tags_instead_industry'] );
	}

	if ( ! empty( $_POST['categoryIds'] ) ) {

		$categoryIds = $_POST['categoryIds'];
		if ( is_array( $categoryIds ) ) {
			$categoryIds = filter_var_array( $categoryIds, FILTER_SANITIZE_STRING );
		} else {
			$categoryIds = sanitize_text_field( $categoryIds );
		}

		$args['tax_query'] = array(
			array(
				'taxonomy' => $taxonomy,
				'field'    => 'id',
				'terms'    => $categoryIds
			)
		);
	}

	$projects_query = new WP_Query( $args );

	if ( $projects_query->have_posts() ) {
		while ( $projects_query->have_posts() ) : $projects_query->the_post(); ?>
            <div class="profolio-col-12 profolio-col-sm-6 profolio-col-md-<?php echo esc_attr( $column_size ); ?>">
				<?php
				$pap_single_project_template = carbon_get_theme_option( 'pap_single_project_template' );
				if ( ! empty( $pap_single_project_template ) && 'modern' == carbon_get_theme_option( 'pap_single_project_template' ) ) {
					include LOCALSEOMAP_PATH . 'data/template/project-archive-modern.php';
				} else {
					include LOCALSEOMAP_PATH . 'data/template/project-archive-default.php';
				} ?>
            </div>
		<?php endwhile;
	} else { ?>
        <div class="profolio-container text-center">
            <div class="profolio-no-result">
                <span><?php esc_html_e( 'No projects found here. Please try another search.', 'localseomap-for-elementor' ); ?></span>
                <button class="profolio-default-button js-reset-filter"><i
                            class="pro_fa pro_fa-chevron-left"></i> <?php esc_html_e( 'Back', 'localseomap-for-elementor' ); ?>
                </button>
            </div>
        </div>
	<?php }

	wp_die();
}

add_action( 'wp_ajax_filter_by_category', 'localseomap_filter_by_category' );
add_action( 'wp_ajax_nopriv_filter_by_category', 'localseomap_filter_by_category' );

/**
 * Projects filter.
 */
if ( ! function_exists( 'localseomap_projects_filter' ) ) {
	function localseomap_projects_filter() {

		$plugin = new LocalSeoMap\Admin();
		$prefix = $plugin->get_metabox_prefix();

		$taxonomy = 'localseomap_industry';

		if ( ! empty( $_POST['tags_instead_industry'] ) ) {
			$taxonomy = sanitize_text_field( $_POST['tags_instead_industry'] );
		}

		//$posts_per_page = isset( $_POST['posts_per_page'] ) ? sanitize_text_field( $_POST['posts_per_page'] ) : 26;

		// limit of posts
		$posts_per_page = 100;
		$args           = array(
			'post_type'      => 'localseomap_projects',
			'post_status'    => array( 'publish' ),
			'posts_per_page' => ! empty( $posts_per_page ) ? $posts_per_page : 26,
		);


		if ( isset( $_POST['categoryIds'] ) ) {

			$categoryIds = $_POST['categoryIds'];
			if ( is_array( $categoryIds ) ) {
				$categoryIds = filter_var_array( $categoryIds, FILTER_SANITIZE_STRING );
			} else {
				$categoryIds = array( sanitize_text_field( $categoryIds ) );
			}

			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $categoryIds
				)
			);
		}

		$salearr = array();
		if ( isset( $_POST['saleIds'] ) ) {

			$saleIds = $_POST['saleIds'];
			if ( is_array( $saleIds ) ) {
				$saleIds = filter_var_array( $saleIds, FILTER_SANITIZE_STRING );
			} else {
				$saleIds = array( sanitize_text_field( $saleIds ) );
			}
			$salearr = array(
				'key'     => $prefix . 'field_real_estate_sale_type',
				'compare' => 'IN',
				'value'   => $saleIds
			);

		}

		$statusarr = array();
		if ( isset( $_POST['statusIds'] ) ) {

			$statusIds = $_POST['statusIds'];
			if ( is_array( $statusIds ) ) {
				$statusIds = filter_var_array( $statusIds, FILTER_SANITIZE_STRING );
			} else {
				$statusIds = array( sanitize_text_field( $statusIds ) );
			}
			$statusarr = array(
				'key'     => $prefix . 'field_real_estate_status',
				'compare' => 'IN',
				'value'   => $statusIds
			);
		}
		// Filter by location
		if ( ! empty( $_POST['lat'] ) && ! empty( $_POST['lng'] ) ) {
			global $wpdb;

			$lat = sanitize_text_field( $_POST['lat'] );
			$lng = sanitize_text_field( $_POST['lng'] );

			$radius = isset( $_POST['radius'] ) ? sanitize_text_field( intval( $_POST['radius'] ) ) : 100;

			$cache_key = 'localseomap_filter_ids_' . md5( $lat . $lng . $radius );

			$posts_in = wp_cache_get( $cache_key );

			if ( false === $posts_in ) {

				$sql = $wpdb->prepare( "
                    SELECT pm1.post_id, (3959 *acos(cos(radians('%s'))*cos(radians(pm1.meta_value))*cos(radians(pm2.meta_value)-radians('%s'))+sin(radians('%s'))*sin(radians(pm1.meta_value)))) AS distance
                    FROM {$wpdb->postmeta} AS pm1
                    INNER JOIN {$wpdb->postmeta} AS pm2 ON pm1.post_id = pm2.post_id
                    WHERE pm1.meta_key = %s AND pm2.meta_key = %s
                    HAVING distance < %d
                    ORDER BY distance
                    LIMIT 0, 1000;
                ", $lat, $lng, $lat, 'localseomap-latitude', 'localseomap-longitude', $radius );

				$posts_in = $wpdb->get_col( $sql ) ? $wpdb->get_col( $sql ) : array( 0 );
				wp_cache_set( $cache_key, $posts_in );
			}
		}

		// Filter by current page project ids
		$ids = array();

		if ( ! empty( $posts_in ) ) {
			$args['post__in'] = $posts_in;
		}

		if ( ! empty( $_POST['pro_project'] ) ) {
			$args['meta_query'] = array(
				array(
					'key'   => $prefix . 'field_project_pro',
					'value' => '1'
				)
			);
		}

		$args['orderby'] = array(
			$prefix . 'start_date' => 'DESC'
		);

		$args['meta_query'] = array(
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
			),
			$salearr,
			$statusarr


		);


		// Filter by category
		if ( ! isset( $_POST['reset'] ) && ! empty( $ids ) ) {


			$category_ids = isset( $_POST['categoryIds'] ) ? sanitize_text_field( $_POST['categoryIds'] ) : localseomap_get_ids_terms( $taxonomy );

			if ( is_array( $category_ids ) ) {
				$category_ids = filter_var_array( $category_ids, FILTER_SANITIZE_STRING );
			} else {
				$category_ids = sanitize_text_field( $category_ids );
			}

			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'id',
					'terms'    => $category_ids
				)
			);

		}
		echo '<script>';
		echo 'console.log(' . json_encode( $args ) . ' )';
		echo '</script>';
		$projects_query = new WP_Query( $args );

		$col        = '';
		$style_list = '';
		if ( ! empty( $_POST['profolio_list_atts'] ) && is_array( $_POST['profolio_list_atts'] ) ) {
			$profolio_list_atts = $_POST['profolio_list_atts'];
			$col                = ! empty( $profolio_list_atts['number_columns'] ) ? $profolio_list_atts['number_columns'] : 'profolio-col-lg-6';

			if ( ! empty( $profolio_list_atts['style'] ) ) {
				$style_list = $profolio_list_atts['style'];
			}
		}


		$locations = array();

		if ( $projects_query->have_posts() ) {
			?>
            <div class="row profolio-cards-row">
				<?php
				while ( $projects_query->have_posts() ) : $projects_query->the_post();
					$longitude = get_post_meta( get_the_ID(), $prefix . 'longitude', true );
					$latitude  = get_post_meta( get_the_ID(), $prefix . 'latitude', true );

					$address   = array();
					$address[] = get_post_meta( get_the_ID(), $prefix . 'city', true );
					$address[] = get_post_meta( get_the_ID(), $prefix . 'province', true );
					$address[] = get_post_meta( get_the_ID(), $prefix . 'country', true );
					$address   = array_filter( $address );

					$terms = wp_get_post_terms( get_the_ID(), $taxonomy );

					foreach ( $terms as $key => $term ) {
						$terms[ $key ]->link = get_term_link( $term, $taxonomy );
					}

					$max_radius = carbon_get_theme_option( 'pap_app_max_radius' );
					if ( ! is_numeric( $max_radius ) ) {
						$max_radius = 1609;
					}

					$pap_type_address = carbon_get_theme_option( 'pap_type_address' );

					$rand_meter = rand( 0, $max_radius );
					$rand_coef  = $rand_meter * 0.0000089;
					if ( ( ! empty( $longitude ) && ! empty( $latitude ) ) && isset( $pap_type_address ) && $pap_type_address !== 'exact' ) {
						$longitude += $rand_coef / cos( $latitude * 0.018 );
						$latitude  += $rand_coef;
					}

					$locations[] = array(
						'link'    => get_the_permalink( get_the_ID() ),
						'lat'     => esc_attr( $latitude ),
						'lng'     => esc_attr( $longitude ),
						'title'   => esc_html( apply_filters( 'the_title', get_the_title() ) ),
						'image'   => get_the_post_thumbnail( get_the_ID(), 'medium_large' ),
						'desc'    => apply_filters( 'the_content', get_the_content() ),
						'address' => esc_html( implode( ', ', $address ) ),
						'terms'   => $terms,
					); ?>
                    <div class="profolio-col-xs-12 <?php echo esc_attr( $col ); ?> JS_profolio_project_item"
                         data-latlong="<?php echo esc_attr( $latitude . ',' . $longitude ); ?>">
						<?php
						if ( 'project_list' === $style_list ) {
							include LOCALSEOMAP_PATH . 'data/template/project-item-list.php';
						} else {
							include LOCALSEOMAP_PATH . 'data/template/project-item.php';
						} ?>
                    </div>
				<?php endwhile; ?>
            </div>
            <!-- <div class="row">
                <div class="profolio-col-12 text-center profolio-pagination JS_profolio_pagination">
                    <nav>
						<?php
			/*						/*echo paginate_links( array(
										'base'         => home_url( '/%_%' ),
										//'format' => 'page/%#%/',
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
									) );*/
			?>
                    </nav>
                </div>
            </div>-->
            <script>
							window.localseomap_ajax_locations = <?php echo json_encode( $locations ) ?>;
            </script>
		<?php } else { ?>
            <div class="profolio-container text-center">
                <div class="profolio-no-result">
                    <span><?php esc_html_e( 'No projects found here. Please try another search.', 'localseomap-for-elementor' ); ?></span>
                    <button class="profolio-default-button js-reset-projects"><i
                                class="pro_fa pro_fa-chevron-left"></i> <?php esc_html_e( 'Reset', 'localseomap-for-elementor' ); ?>
                    </button>
                </div>
            </div>
		<?php } ?>

		<?php


		wp_die();
	}
}

add_action( 'wp_ajax_projects_filter', 'localseomap_projects_filter' );
add_action( 'wp_ajax_nopriv_projects_filter', 'localseomap_projects_filter' );

if ( ! function_exists( 'localseomap_get_status' ) ) {
	function localseomap_get_status( $key = '', $type = '' ) {

		if ( $key == '-1' ) {
			$key = '';
		}

		if ( $type == 'project' ) {

			$status = array(
				0 => esc_html__( 'Active', 'localseomap-for-elementor' ),
				1 => esc_html__( 'Completed', 'localseomap-for-elementor' ),
				2 => esc_html__( 'On Hold', 'localseomap-for-elementor' ),
				3 => esc_html__( 'Dropped', 'localseomap-for-elementor' ),
			);

		} elseif ( $type == 'property' ) {

			$status = array(
				0 => esc_html__( 'active', 'localseomap-for-elementor' ),
				1 => esc_html__( 'sold', 'localseomap-for-elementor' ),
				2 => esc_html__( 'inactive', 'localseomap-for-elementor' ),
			);
		}

		return ! empty( $key ) || $key === 0 ? $status[ $key ] : '';
	}
}


if ( ! function_exists( 'localseomap_get_sale_type' ) ) {
	function localseomap_get_sale_type( $key = '' ) {

		if ( $key == '-1' ) {
			$key = '';
		}

		$sale_type = array(
			0 => esc_html__( 'for sale', 'localseomap-for-elementor' ),
			1 => esc_html__( 'for rent', 'localseomap-for-elementor' ),
		);

		return ! empty( $key ) || $key === 0 ? $sale_type[ $key ] : '';
	}
}

if ( ! function_exists( 'localseomap_format_price' ) ) {
	function localseomap_format_number( $number, $before = '', $after = '' ) {
		return $before . number_format_i18n( $number, ',' ) . $after;
	}
}

/**
 * Register sidebar.
 */
if ( ! function_exists( 'localseomap_register_widgets' ) ) {
	function localseomap_register_widgets() {
		register_sidebar(
			array(
				'id'            => 'profolio-detail-sidebar',
				'name'          => esc_html__( 'Profolio detail sidebar', 'localseomap-for-elementor' ),
				'before_widget' => '<div class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
				'description'   => esc_html__( 'Drag the widgets for profolio detail sidebar.', 'localseomap-for-elementor' )
			)
		);
	}
}

add_action( 'widgets_init', 'localseomap_register_widgets', 100);

/**
 * Get filter industry.
 * @return array
 */
if ( ! function_exists( 'localseomap_get_filter_industry' ) ) {
	function localseomap_get_filter_industry() {

		$terms = get_terms( 'localseomap_industry', [
			'hide_empty' => true,
		] );

		$terms_list = array();

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$terms_list[ $term->term_id ] = $term->name;
			}
		}

		return $terms_list;
	}
}


/**
 * Get filter tags.
 * @return array
 */
if ( ! function_exists( 'localseomap_get_filter_tags' ) ) {
	function localseomap_get_filter_tags() {

		$terms = get_terms( 'localseomap_project_tag', [
			'hide_empty' => true,
		] );

		$terms_list = array();

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$terms_list[ $term->term_id ] = $term->name;
			}
		}

		return $terms_list;
	}
}

/**
 * Get filter terms.
 * @return array
 */
if ( ! function_exists( 'localseomap_get_ids_terms' ) ) {
	function localseomap_get_ids_terms( $taxonomy ) {

		$terms = get_terms( $taxonomy, [
			'hide_empty' => false,
		] );

		$terms_ids = array();

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$terms_ids[] = $term->term_id;
			}
		}

		return $terms_ids;
	}
}

/**
 * Get projects list.
 * @return array
 */
if ( ! function_exists( 'localseomap_get_projects_list' ) ) {
	function localseomap_get_projects_list() {

		if ( ! empty( $_GET['post'] ) && is_numeric( $_GET['post'] ) ) {
			$post_type = get_post_type( $_GET['post'] );
			if ( $post_type == 'localseomap_media' ) {


				$projects = get_posts( array( 'post_type' => 'localseomap_projects', 'numberposts' => - 1 ) );

				$projects_list = array();

				if ( ! empty( $projects ) ) {
					foreach ( $projects as $project ) {
						$projects_list[ $project->ID ] = $project->post_title;
					}
				}

				return $projects_list;
			}
		}

		return array();
	}
}

if ( ! function_exists( 'localseomap_add_new_size_thumbnail' ) ) {
	function localseomap_add_new_size_thumbnail() {
		add_image_size( 'localseomap_map_info_thumbnail', 380, 9999 );
		add_image_size( 'localseomap_project_thumbnail', 1920, 800, true );
	}
}

add_action( 'after_setup_theme', 'localseomap_add_new_size_thumbnail' );

/**
 * Get thumbnail sizes.
 * @return array
 */
if ( ! function_exists( 'localseomap_get_thumbnail_sizes' ) ) {
	function localseomap_get_thumbnail_sizes( $type = 'left' ) {
		global $_wp_additional_image_sizes;

		$default_image_sizes = get_intermediate_image_sizes();

		foreach ( $default_image_sizes as $size ) {
			$image_sizes[ $size ] = ucfirst( str_replace( array(
					'_',
					'-'
				), ' ', $size ) ) . ' - ' . intval( get_option( "{$size}_size_w" ) ) . ' x ' . intval( get_option( "{$size}_size_h" ) );
		}

		if ( is_array( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
			foreach ( $_wp_additional_image_sizes as $key => $size ) {
				$additional_image_sizes[ $key ] = ucfirst( str_replace( array(
						'_',
						'-'
					), ' ', $key ) ) . ' - ' . intval( $size['width'] ) . ' x ' . intval( $size['height'] );
			}

			$image_sizes = array_merge( $image_sizes, $additional_image_sizes );
		}

		if ( $type === 'right' ) {
			$image_sizes = array_flip( $image_sizes );
		}

		return $image_sizes;
	}
}

if ( function_exists( 'register_rest_field' ) ) {
	register_rest_field( 'localseomap_projects', 'metadata', array(
		'get_callback' => function ( $data ) {
			return get_post_meta( $data['id'], '', '' );
		},
	) );
}

if ( ! function_exists( 'localseomap_get_offset' ) ) {
	function localseomap_get_offset( $query ) {

		if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
			return '';
		}

		$offset = 10;

		if ( localseomap()->is_plan( 'starter' ) ) {
			$offset = 30;
		}
		if ( localseomap()->is_plan( 'professional' ) ) {
			$offset = 100;
		}
		if ( localseomap()->is_plan( 'business' ) ) {
			$offset = '';
		}

		return $offset;

	}
}

/**
 * Display Leads popup.
 */

if ( ! function_exists( 'localseomap_leads_popup' ) ) {
	function localseomap_leads_popup() {
		$leads = new Leads();
		$leads->leads_popup();
	}
}

if ( ! function_exists( 'localseomap_render_leads_button' ) ) {
	function localseomap_render_leads_button( $type = 'desktop' ) {

		$pap_leads_button_label = carbon_get_theme_option( 'pap_leads_button_label' );
		if ( empty( $pap_leads_button_label ) ) {
			$pap_leads_button_label = esc_html__( 'Contact Us', 'localseomap-for-elementor' );
		}
		$pap_show_leads_button     = carbon_get_theme_option( 'pap_show_leads_button' );
		$pap_override_btn_redirect = carbon_get_theme_option( 'pap_override_btn_redirect' );
		$pap_redirect_url_btn      = carbon_get_theme_option( 'pap_redirect_url_btn' );

		/**
		 * Leads button.
		 */
		if ( ! empty( $pap_show_leads_button ) && localseomap()->is_plan( 'business' ) ) :
			$show_class_popup = ! empty( $pap_override_btn_redirect ) ? 'js-show-leads-form' : '';
			$show_redirect_url     = '#profolio-leads-popup';
			if ( empty( $pap_override_btn_redirect ) ) {
				$show_redirect_url = ! empty( $pap_redirect_url_btn ) ? $pap_redirect_url_btn : '';
			}


			?>
            <a href="<?php echo esc_url( $show_redirect_url ); ?>" class="<?php echo esc_attr( $show_class_popup ); ?> profolio-leads-btn profolio-leads-btn--<?php echo esc_attr( $type ); ?> btn btn-primary"><?php echo esc_html( $pap_leads_button_label ); ?></a>
			<?php localseomap_leads_popup();
		endif;
	}
}

/**
 * Get project data
 * @return array
 */
if ( ! function_exists( 'localseomap_project_data_attr' ) ) {
	function localseomap_project_data_attr( $key = '' ) {

		$plugin = new LocalSeoMap\Admin();
		$prefix = $plugin->get_metabox_prefix();

		$show_input_form = sanitize_text_field( get_query_var( 'add-input-form' ) );
		$project_id      = sanitize_text_field( get_query_var( 'project_id' ) );

		if ( empty( $show_input_form ) ) {
			return '';
		}

		if ( empty( $project_id ) ) {
			return '';
		}

		$project_data = wp_cache_get( md5( 'localseomap_project_data_' . $project_id ) );

		if ( $project_data === false ) {

			$project      = (array) get_post( $project_id );
			$project_meta = array_map( function ( $el ) {
				return ! empty( $el[0] ) ? $el[0] : '';
			}, get_post_meta( $project_id ) );

			$project_data = array_merge( $project, $project_meta );

			$project_data['thumbnail_id']  = get_post_thumbnail_id( $project_id );
			$project_data['thumbnail_url'] = get_the_post_thumbnail_url( $project_id, 'thumbnail' );

			wp_cache_add( md5( 'localseomap_project_data_' . $project_id ), $project_data );
		}

		if ( ! empty( $project_data[ $key ] ) ) {
			return esc_html( $project_data[ $key ] );
		}

		if ( empty( $project_data[ $prefix . $key ] ) ) {
			return '';
		}

		return esc_html( $project_data[ $prefix . $key ] );

	}
}

/**
 * Output project thumbnail
 */
if ( ! function_exists( 'localseomap_the_project_thumbnail' ) ) {
	function localseomap_the_project_thumbnail( $type = 'thumbnail', $hidden = false ) {
		$thumbnail_id  = localseomap_project_data_attr( 'thumbnail_id' );
		$thumbnail_url = localseomap_project_data_attr( 'thumbnail_url' );
		if ( 'thumbnail' === $type && ( ! empty( $thumbnail_id ) || $hidden ) ):
			?>
            <div class="profolio-pre-img" id="profolio-thumbnail-<?php echo esc_attr( $thumbnail_id ); ?>">
                <img src="<?php echo esc_url( $thumbnail_url ); ?>">
                <i class="pro_fa pro_fa-times-circle profolio-close-img"></i>
                <input type="hidden" name="profolio_project_main_image" value="<?php echo esc_attr( $thumbnail_id ); ?>">
            </div>
		<?php
		endif;

		if ( 'gallery' === $type ):
			$plugin = new LocalSeoMap\Admin();
			$prefix    = $plugin->get_metabox_prefix();

			$meta_key     = $prefix . 'project_uuid';
			$project_uuid = localseomap_project_data_attr( $prefix . 'uuid' );

			$args                 = array(
				'post_type' => 'localseomap_media',
				'fields'    => 'ids'
			);
			$media                = localseomap_get_posts( $meta_key, $project_uuid, $args );
			if ( ! empty( $media ) && is_array( $media ) ) :
				$ids = array();
				foreach ( $media as $id ) :
					$thumbnail_url = get_the_post_thumbnail_url( $id );
					$thumbnail_id = get_post_thumbnail_id( $id );
					$ids[]        = $thumbnail_id;
					?>
                    <div class="profolio-pre-img" id="profolio-thumbnail-<?php echo esc_attr( $thumbnail_id ); ?>">
                        <img src="<?php echo esc_url( $thumbnail_url ); ?>">
                        <i class="pro_fa pro_fa-times-circle profolio-close-img"></i>
                        <input type="hidden" name="profolio_project_main_image" value="<?php echo esc_attr( $thumbnail_id ); ?>">
                    </div>
				<?php endforeach; ?>
                <input type="hidden" id="profolio_project_gallery_ids_tmp" value="<?php echo esc_attr( implode( ',', $ids ) ); ?>">
			<?php


			endif;

		endif;

	}
}

/**
 * Get posts by meta
 */
if ( ! function_exists( 'localseomap_get_posts' ) ) {
	function localseomap_get_posts( $key, $value, $custom_args = array() ) {

		$args = array(

			//'author'     => get_current_user_id(),
			'meta_query' => array(
				array(
					'key'     => $key,
					'value'   => $value,
					'compare' => '=',
				)
			)
		);

		$args = array_merge( $args, $custom_args );

		$posts = get_posts( $args );

		return $posts;

	}
}


/**
 * Get categories of the project
 */
if ( ! function_exists( 'localseomap_get_terms_ids' ) ) {
	function localseomap_get_terms_ids( $taxonomy = 'localseomap_industry' ) {

		$project_id = sanitize_text_field( get_query_var( 'project_id' ) );

		if ( empty( $project_id ) ) {
			return '';
		}

		if ( $terms = get_the_terms( $project_id, $taxonomy ) ) {
			$term_ids = wp_list_pluck( $terms, 'term_id' );
		}

		if ( empty( $term_ids ) ) {
			return '';
		}

		return $term_ids;


	}
}

/**
 * Get categories of the project
 */
if ( ! function_exists( 'localseomap_test_api_button' ) ) {
	function localseomap_test_api_button( $label, $class = '', $success_mess = '', $error_mass = '' ) {

		if ( empty( $success_mess ) ) {
			$success_mess = esc_html__( 'Api key is valid', 'localseomap-for-elementor' );
		}

		if ( empty( $error_mass ) ) {
			$error_mass = esc_html__( 'Api key is not valid', 'localseomap-for-elementor' );
		}


		ob_start(); ?>
        <p class="test_api_button_wrap">
            <a href="#" class="button button-primary <?php echo esc_attr( $class ); ?>">
				<?php echo esc_html( $label ); ?>
            </a>
            <img class="profolio_import_loader" style="display: none;vertical-align: bottom;" width="30" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>/data/assets/img/ajax-loader.gif" alt="">
            <span class="success" style="display: none;"><?php echo esc_html( $success_mess ); ?></span>
            <span class="profolio_message_count"></span>
            <span class="error" style="display: none;" data-cache="<?php echo esc_html( $error_mass ); ?>"><?php echo esc_html( $error_mass ); ?></span>
        </p>
		<?php
		return ob_get_clean();
	}
}

