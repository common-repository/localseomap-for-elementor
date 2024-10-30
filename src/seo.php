<?php

namespace LocalSeoMap;

use WP_Query;

if ( ! defined( 'WPINC' ) ) {
	die;
}


class Seo extends Admin {

	/**
	 * API constructor.
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Init importer.
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {
		ob_start();
		add_action( 'wp_head', array( &$this, 'start_wp_head_buffer_end' ), 999999 );

		add_action( 'wp_head', array( &$this, 'the_schema_company' ) );
		add_action( 'wp_head', array( &$this, 'add_canonical_url_term' ), 10 );
		add_action( 'wp_head', array( &$this, 'add_og_schema' ), 10 );
		add_action( 'wp_head', array( &$this, 'add_geotags' ), 10 );
		add_filter( 'get_canonical_url', array( &$this, 'remove_comment_page_canonical_url' ), 10, 2 );
		add_action( 'save_post', array( &$this, 'update_noindex' ), 10, 3 );
		add_action( 'edited_term', array( &$this, 'update_noindex_term' ), 10, 3 );


		add_action( 'wp_head', array( &$this, 'add_metadata_noindex' ), 0 );

		add_action( 'wp_ajax_localseomap_add_noindex', array( &$this, 'localseomap_add_noindex' ), 0 );
	}

	public function start_wp_head_buffer_end() {
		$remove_other_robots = carbon_get_theme_option( 'pap_remove_other_robots' );

		$regex = '/\<meta\s+?name=[\'|"][Robots|robots]+[\'|"]\s+?content=[\'|"].+?[\'|"].*?\>/i';

		if ( ! empty( $remove_other_robots ) ) {

			if ( function_exists( 'get_queried_object' ) && ! empty( get_queried_object()->taxonomy ) ) {
				$taxonomy = get_queried_object()->taxonomy;
				if (
					'localseomap_industry' === $taxonomy ||
					'localseomap_project_tag' === $taxonomy ||
					'localseomap_area_tags' === $taxonomy

				) {

					$term_id = get_queried_object()->term_id;
					$noindex = get_term_meta( $term_id, 'localseomap_noindex', true );
					if ( ! empty( $noindex ) ) {
						$content_head = ob_get_clean();
						$content_head = preg_replace( $regex, '', $content_head );
						echo $content_head;
					}
				}
			} else {
				global $post;
				$localseomap_noindex = get_post_meta( $post->ID, 'localseomap_noindex', true );

				if ( ! empty( $localseomap_noindex ) ) {

					$content_head = ob_get_clean();
					$content_head = preg_replace( $regex, '', $content_head );
					echo $content_head;
				}
			}

		} else {
			echo ob_get_clean();
		}


	}

	public function add_canonical_url_term() {
		$queried_object = get_queried_object();
		if ( is_wp_error( $queried_object ) ) {
			return;
		}
		if ( empty( $queried_object->term_id ) || empty( $queried_object->taxonomy ) ) {
			return;
		}
		$term_link = get_term_link( $queried_object->term_id, $queried_object->taxonomy );

		$page = get_query_var( 'paged' );

		$structure = get_option( 'permalink_structure' );

		if ( ! empty( $term_link ) ) {
			if ( ! empty( $page ) ) {
				if ( $structure != '' ) {
					$term_link = trailingslashit( $term_link ) . 'page/' . $page;
				} else {
					$term_link = add_query_arg( 'page', $page, $term_link );
				}
			}

			?>
            <link rel="canonical" href="<?php echo esc_url( $term_link ); ?>" />
			<?php
		}
	}

	public function remove_comment_page_canonical_url( $canonical_url, $post ) {

		if ( 'localseomap_projects' !== $post->post_type ) {
			return $canonical_url;
		}

		// for comment page of current post only
		if ( get_query_var( 'page', 0 ) && $post->ID === get_queried_object_id() ) {
			$canonical_url = get_permalink( $post );
		}

		return $canonical_url;
	}

	public function the_schema_review( $post_id = '' ) {

		$pap_use_scheme = \carbon_get_theme_option( 'pap_use_scheme_aggregate_rating' );
		if ( localseomap()->is_plan( 'starter' ) && $pap_use_scheme ) {

			$pap_schema_telephone = carbon_get_theme_option( 'pap_schema_telephone' );
			$pap_schema_address   = carbon_get_theme_option( 'pap_schema_address' );

			$pap_schema_city         = carbon_get_theme_option( 'pap_schema_city' );
			$pap_schema_state_region = carbon_get_theme_option( 'pap_schema_state_region' );
			$pap_schema_zip          = carbon_get_theme_option( 'pap_schema_zip' );
			$pap_schema_country      = carbon_get_theme_option( 'pap_schema_country' );

			$prefix = $this->get_metabox_prefix();

			$number_testimonials = $this->get_number_of_testimonials();

			$testimonial_rating  = get_post_meta( $post_id, $prefix . 'field_story_testimonial_rating', true );
			$testimonial_author  = get_post_meta( $post_id, $prefix . 'field_story_testimonial_author', true );
			$testimonial_body    = get_post_meta( $post_id, $prefix . 'field_story_testimonial_body', true );
			$testimonial_picture = get_post_meta( $post_id, $prefix . 'field_story_testimonial_picture', true );

			if ( ! empty( $testimonial_body ) && ! empty( $testimonial_author ) && ! empty( $testimonial_rating ) ) {

				$schema = array(
					'@context'      => 'https://schema.org',
					'@type'         => 'Review',
					'author'        => esc_html( $testimonial_author ),
					'datePublished' => esc_html( get_the_date( 'c', $post_id ) ),
					'reviewBody'    => esc_html( $testimonial_body ),
					'itemReviewed'  =>
						array(
							'@type'           => 'LocalBusiness',
							'image'           => get_the_post_thumbnail_url( $post_id, 'full' ),
							'name'            => esc_html( apply_filters( 'the_title', get_the_title( $post_id ) ) ),
							'telephone'       => esc_html( $pap_schema_telephone ),
							'address'         =>
								array(
									'@type'           => 'PostalAddress',
									'streetAddress'   => esc_html( $pap_schema_address ),
									'addressLocality' => esc_html( $pap_schema_city ),
									'postalCode'      => esc_html( $pap_schema_zip ),
									'addressCountry'  => esc_html( $pap_schema_country ),
									'addressRegion'   => esc_html( $pap_schema_state_region ),
								),
							'aggregateRating' =>
								array(
									'@type'       => 'AggregateRating',
									'ratingValue' => esc_html( $testimonial_rating ),
									'bestRating'  => '5',
									'ratingCount' => esc_html( $number_testimonials ),
								),
						),
					'reviewRating'  =>
						array(
							'@type'       => 'Rating',
							'bestRating'  => '5',
							'ratingValue' => esc_html( $testimonial_rating )
						)
				);

				return $this->add_ld_json_wrap( $schema );
			}


		}

	}

	public function the_schema_aggregate_rating( $post_id = '' ) {

		$pap_use_scheme_aggregate_rating = \carbon_get_theme_option( '_pap_use_scheme' );
		if ( localseomap()->is_plan( 'starter' ) && $pap_use_scheme_aggregate_rating ) {

			if ( empty( $post_id ) ) {
				global $post;
				$post_id = $post->ID;
			}


			$schema = '';

			$number_testimonials = $this->get_number_of_testimonials();

			$prefix = $this->get_metabox_prefix();

			$pap_schema_telephone    = carbon_get_theme_option( 'pap_schema_telephone' );
			$pap_schema_address      = carbon_get_theme_option( 'pap_schema_address' );
			$pap_schema_city         = carbon_get_theme_option( 'pap_schema_city' );
			$pap_schema_state_region = carbon_get_theme_option( 'pap_schema_state_region' );
			$pap_schema_zip          = carbon_get_theme_option( 'pap_schema_zip' );
			$pap_schema_country      = carbon_get_theme_option( 'pap_schema_country' );

			$testimonial_rating = get_post_meta( $post_id, $prefix . 'field_story_testimonial_rating', true );
			$testimonial_author = get_post_meta( $post_id, $prefix . 'field_story_testimonial_author', true );
			$testimonial_body   = get_post_meta( $post_id, $prefix . 'field_story_testimonial_body', true );

			if ( ! empty( $testimonial_body ) && ! empty( $testimonial_rating ) && ! empty( $number_testimonials ) ) {

				$schema = array(
					'@context'        => 'https://schema.org/',
					'@type'           => 'LocalBusiness',
					'name'            => esc_html( apply_filters( 'the_title', get_the_title( $post_id ) ) ),
					'image'           => get_the_post_thumbnail_url( $post_id, 'full' ),
					'description'     => esc_html( $testimonial_body ),
					'telephone'       => esc_html( $pap_schema_telephone ),
					'aggregateRating' =>
						array(
							'@type'       => 'AggregateRating',
							'ratingValue' => esc_html( $testimonial_rating ),
							'bestRating'  => '5',
							'ratingCount' => esc_html( $number_testimonials ),
						),
					'address'         =>
						array(
							'@type'           => 'PostalAddress',
							'streetAddress'   => esc_html( $pap_schema_address ),
							'addressLocality' => esc_html( $pap_schema_city ),
							'postalCode'      => esc_html( $pap_schema_zip ),
							'addressCountry'  => esc_html( $pap_schema_country ),
							'addressRegion'   => esc_html( $pap_schema_state_region ),
						),
				);

			}

			return $this->add_ld_json_wrap( $schema );
		}
	}

	public function the_schema_company() {

		$pap_use_scheme = \carbon_get_theme_option( 'pap_use_scheme' );
		if ( localseomap()->is_plan( 'starter' ) && $pap_use_scheme ) {

			$pap_use_scheme          = carbon_get_theme_option( 'pap_use_scheme' );
			$pap_schema_type         = carbon_get_theme_option( 'pap_schema_type' );
			$pap_schema_type_custom  = carbon_get_theme_option( 'pap_schema_type_custom' );
			$pap_schema_company_name = carbon_get_theme_option( 'pap_schema_company_name' );
			$pap_schema_company_logo = carbon_get_theme_option( 'pap_schema_company_logo' );
			$pap_schema_description  = carbon_get_theme_option( 'pap_schema_description' );
			$pap_schema_address      = carbon_get_theme_option( 'pap_schema_address' );
			$pap_schema_city         = carbon_get_theme_option( 'pap_schema_city' );
			$pap_schema_state_region = carbon_get_theme_option( 'pap_schema_state_region' );
			$pap_schema_zip          = carbon_get_theme_option( 'pap_schema_zip' );
			$pap_schema_country      = carbon_get_theme_option( 'pap_schema_country' );
			$pap_schema_latitude     = carbon_get_theme_option( 'pap_schema_latitude' );
			$pap_schema_longitude    = carbon_get_theme_option( 'pap_schema_longitude' );
			$pap_schema_price_range  = carbon_get_theme_option( 'pap_schema_price_range' );
			$pap_schema_telephone    = carbon_get_theme_option( 'pap_schema_telephone' );
			$pap_schema_image        = carbon_get_theme_option( 'pap_schema_image' );
			$pap_schema_url          = carbon_get_theme_option( 'pap_schema_url' );

			if ( empty( $pap_use_scheme ) || empty( $pap_schema_type ) ) {
				return;
			}

			if ( $pap_schema_type == 'Manual' && ! empty( $pap_schema_type_custom ) ) {
				$pap_schema_type = $pap_schema_type_custom;
			}
			$schema = array(
				'@context'    => 'http://www.schema.org',
				'@type'       => esc_html( $pap_schema_type ),
				'name'        => esc_html( $pap_schema_company_name ),
				'priceRange'  => esc_html( $pap_schema_price_range ),
				'image'       => esc_html( $pap_schema_image ),
				'logo'        => esc_html( $pap_schema_company_logo ),
				'description' => esc_html( $pap_schema_description ),
				'address'     =>
					array(
						'@type'           => 'PostalAddress',
						'streetAddress'   => esc_html( $pap_schema_address ),
						'addressLocality' => esc_html( $pap_schema_city ),
						'postalCode'      => esc_html( $pap_schema_zip ),
						'addressCountry'  => esc_html( $pap_schema_country ),
						'addressRegion'   => esc_html( $pap_schema_state_region ),
					),
				'geo'         =>
					array(
						'@type'     => 'GeoCoordinates',
						'latitude'  => esc_html( $pap_schema_latitude ),
						'longitude' => esc_html( $pap_schema_longitude ),
					),
				'telephone'   => esc_html( $pap_schema_telephone ),
				'url'         => esc_html( $pap_schema_url ),
			);

			echo $this->add_ld_json_wrap( $schema );

		}

	}

	public function the_schema_video( $post_id = '' ) {

		$pap_use_scheme_video = carbon_get_theme_option( 'pap_use_scheme_video' );
		if ( localseomap()->is_plan( 'starter' ) && $pap_use_scheme_video ) {
			$prefix = $this->get_metabox_prefix();

			$excerpt = get_the_excerpt( $post_id );

			if ( empty( $excerpt ) ) {
				$excerpt = get_the_title( $post_id );
			}

			$field_video = get_post_meta( $post_id, $prefix . 'field_video', true );

			$schema = array();
			if ( ! empty( $field_video ) ) {

				$schema = array(
					'@context'     => 'http://schema.org',
					'@type'        => 'VideoObject',
					'name'         => esc_html( apply_filters( 'the_title', get_the_title( $post_id ) ) ),
					'description'  => esc_html( $excerpt ),
					'thumbnailUrl' => get_the_post_thumbnail_url( $post_id, 'full' ),
					'uploadDate'   => get_the_date( 'c', $post_id ),//'2018-04-16T08:01:27.000Z',
					//'duration'         => 'PT4M43S',
					'contentUrl'   => esc_url( $field_video ),
					//'interactionCount' => '130',
				);
			}

			return $this->add_ld_json_wrap( $schema );
		}

	}

	public function the_schema_video_testimonial( $post_id = '' ) {

		if ( empty( $post_id ) ) {
			global $post;
			$post_id = $post->ID;
		}

		$pap_use_scheme_video = \carbon_get_theme_option( 'pap_use_scheme_video' );
		if ( is_single( $post_id ) && localseomap()->is_plan( 'starter' ) && $pap_use_scheme_video ) {
			$prefix              = $this->get_metabox_prefix();
			$testimonial_title   = get_post_meta( $post_id, $prefix . 'field_story_testimonial_title', true );
			$testimonial_video   = get_post_meta( $post_id, $prefix . 'field_story_testimonial_videos', true );
			$testimonial_picture = get_post_meta( $post_id, $prefix . 'field_story_testimonial_picture', true );
			$testimonial_body    = get_post_meta( $post_id, $prefix . 'field_story_testimonial_body', true );

			if ( ! empty( $testimonial_title ) && ! empty( $testimonial_body ) && ! empty( $testimonial_picture ) && ! empty( $testimonial_video ) ) {

				$schema = array(
					'@context'     => 'http://schema.org',
					'@type'        => 'VideoObject',
					'name'         => $testimonial_title,
					'description'  => $testimonial_body,
					'thumbnailUrl' => $testimonial_picture,
					'uploadDate'   => get_the_date( 'c', $post_id ),//'2018-04-16T08:01:27.000Z',
					//'duration'         => 'PT4M43S',
					'contentUrl'   => esc_url( $testimonial_video ),
					//'interactionCount' => '130',
				);

				return $this->add_ld_json_wrap( $schema );
			}

		}
	}

	private function add_ld_json_wrap( $schema ) {

		if ( empty( $schema ) || ! is_array( $schema ) ) {
			return '';
		}

		$schema = json_encode( $schema );

		if ( empty( $schema ) ) {
			return $schema;
		}

		$schema = str_replace( '\/', '/', $schema );

		return '<script type="application/ld+json">' . $schema . '</script>';
	}

	public function add_og_schema() {

		global $post;
		if ( is_single() ) {

			if ( get_post_type( $post ) == 'localseomap_projects' || get_post_type( $post ) == 'localseomap_media' ) {

				if ( $excerpt = $post->post_excerpt ) {
					$excerpt = strip_tags( $post->post_excerpt );
					$excerpt = str_replace( "", "'", $excerpt );
				}

				?>
                <meta property="og:title" content="<?php the_title(); ?>" />
                <meta property="og:description" content="<?php echo esc_attr( $excerpt ); ?>" />
                <meta property="og:type" content="article" />
                <meta property="og:url" content="<?php the_permalink(); ?>" />
                <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>" />

				<?php if ( has_post_thumbnail( $post->ID ) ) { ?>
                    <meta property="og:image" content="<?php the_post_thumbnail_url( $post->ID, 'medium' ); ?>" />
				<?php } ?>

				<?php
			}
		}
	}

	public function add_geotags() {
		global $post;
		if ( is_single() ) {

			if ( get_post_type( $post ) == 'localseomap_projects' || get_post_type( $post ) == 'localseomap_media' ) {

				$admin    = new Admin();
				$post_id  = $post->ID;
				$location = $admin->get_lat_long( $post_id );

				if ( ! empty( $location['longitude'] ) && ! empty( $location['latitude'] ) ) {

					$prefix   = $admin->get_metabox_prefix();
					$province = get_post_meta( $post_id, $prefix . 'province', true );
					$country  = get_post_meta( $post_id, $prefix . 'country', true );

					$address   = array();
					$address[] = get_post_meta( $post_id, $prefix . 'city', true );
					$address[] = $province;
					$address[] = $country;

					$pap_type_address = carbon_get_theme_option( 'pap_type_address' );
					if ( isset( $pap_type_address ) && $pap_type_address == 'exact' ) {
						$address[] = get_post_meta( $post_id, $prefix . 'address', true );
					}

					$address = array_filter( $address );
					?>
                    <meta name="geo.position" content="<?php echo esc_attr( $location['latitude'] ); ?>;<?php echo esc_attr( $location['longitude'] ); ?>" />
                    <meta name="geo.placename" content="<?php echo esc_attr( implode( ', ', $address ) ); ?>" />
                    <meta name="geo.region" content="<?php echo esc_attr( $country ); ?>-<?php echo esc_attr( $province ); ?>" />
					<?php
				}
			}
		}
	}

	public function update_noindex( $post_id, $post, $update ) {

		$post_type = $post->post_type;

		if ( 'localseomap_projects' == $post_type || 'localseomap_media' == $post_type ) {

			$char_count = carbon_get_theme_option( 'pap_char_count_' . $post_type );

			$count_content = strlen( wp_strip_all_tags( $post->post_content, 0 ) );

			$noindex = '';
			if ( $count_content < $char_count ) {
				$noindex = 'true';
			}

			update_post_meta( $post_id, 'localseomap_noindex', $noindex );

		}

	}

	public function update_noindex_term( $term_id, $tt_id, $taxonomy ) {

		if (
			'localseomap_industry' === $taxonomy ||
			'localseomap_project_tag' === $taxonomy ||
			'localseomap_area_tags' === $taxonomy

		) {

			$industry_char_count = carbon_get_theme_option( 'pap_industry_char_count' );
			$area_tag_char_count = carbon_get_theme_option( 'pap_area_tag_char_count' );
			$tag_char_count      = carbon_get_theme_option( 'pap_tag_char_count' );

			$count_content = strlen( wp_strip_all_tags( term_description( $term_id, $taxonomy ) ) );

			$noindex = '';

			if ( 'localseomap_industry' === $taxonomy && $count_content < $industry_char_count ) {
				$noindex = 'true';
			}

			if ( 'localseomap_project_tag' === $taxonomy && $count_content < $tag_char_count ) {
				$noindex = 'true';
			}

			if ( 'localseomap_area_tags' === $taxonomy && $count_content < $area_tag_char_count ) {

				$noindex = 'true';
			}

			update_term_meta( $term_id, 'localseomap_noindex', $noindex );


		}

	}

	/**
	 *
	 */
	public function add_metadata_noindex() {

		$remove_other_robots = carbon_get_theme_option( 'pap_remove_other_robots' );

		if ( ! empty( $remove_other_robots ) ) {

			if ( function_exists( 'get_queried_object' ) && get_queried_object()->term_id ) {

				$term_id             = get_queried_object()->term_id;
				$localseomap_noindex = get_term_meta( $term_id, 'localseomap_noindex', true );

				if ( ! empty( $localseomap_noindex ) ) {
					?>
                    <meta name="Robots" id="localseomap" content="noindex, nofollow">
					<?php
				}
			} else {
				global $post;
				if ( ! empty( $post->post_type ) ) {
					if ( 'localseomap_projects' == $post->post_type || 'localseomap_media' == $post->post_type ) {

						$localseomap_noindex = get_post_meta( $post->ID, 'localseomap_noindex', true );
						if ( ! empty( $localseomap_noindex ) ) {
							?>
                            <meta name="Robots" id="localseomap" content="noindex, nofollow">
							<?php
						}

					}
				}
			}

		}
	}


	public function localseomap_add_noindex() {

		$args = array(
			'posts_per_page' => - 1,
			'post_type'      => array( 'localseomap_projects', 'localseomap_media' ),
		);

		$response = array();

		if ( ! empty( $_POST['get_ids'] ) ) {
			$args['fields'] = 'ids';
			$ids            = get_posts( $args );
			$response       = array(
				'status'        => 'OK',
				'ids'           => $ids,
				'message_label' => 'Number of items: ',
			);
			echo wp_json_encode( $response );
			die();
		}

		if ( ! empty( $_POST['post_id'] ) && is_numeric( $_POST['post_id'] ) ) {

			$post = get_post( $_POST['post_id'] );

			$count_content = strlen( wp_strip_all_tags( $post->post_content, 0 ) );

			$noindex    = '';
			$char_count = carbon_get_theme_option( 'pap_char_count_' . $post->post_type );
			if ( $count_content < $char_count ) {
				$noindex = 'true';
			}
			update_post_meta( $post->ID, 'localseomap_noindex', $noindex );

			$response = array(
				'status'        => 'OK',
				'message_label' => 'Updated items: ',
				'post_id'       => $post->ID,
			);

			$taxonomy_names = get_object_taxonomies( $post );
			$terms          = get_terms( $taxonomy_names, array( 'hide_empty' => true ) );


			if ( ! empty( $terms ) && ! is_wp_error( $terms ) && is_array( $terms ) ) {

				$industry_char_count = carbon_get_theme_option( 'pap_industry_char_count' );
				$area_tag_char_count = carbon_get_theme_option( 'pap_area_tag_char_count' );
				$tag_char_count      = carbon_get_theme_option( 'pap_tag_char_count' );

				foreach ( $terms as $cur_term ) {

					$term_id       = $cur_term->term_id;
					$taxonomy      = $cur_term->taxonomy;
					$count_content = strlen( wp_strip_all_tags( term_description( $term_id, $taxonomy ) ) );

					$noindex = '';

					if ( 'localseomap_industry' === $taxonomy && $count_content < $industry_char_count ) {
						$noindex = 'true';
					}

					if ( 'localseomap_project_tag' === $taxonomy && $count_content < $tag_char_count ) {
						$noindex = 'true';
					}

					if ( 'localseomap_area_tags' === $taxonomy && $count_content < $area_tag_char_count ) {
						$noindex = 'true';
					}
					update_term_meta( $term_id, 'localseomap_noindex', $noindex );

				}


			}


		}


		echo wp_json_encode( $response );
		die();

	}


}
