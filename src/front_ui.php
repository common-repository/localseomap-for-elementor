<?php

namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}


class FRONT_UI extends Admin {

	/**
	 * API constructor.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Init importer.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {

		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 0 );

		add_filter( 'document_title_parts', array( &$this, 'replace_wp_title' ), 10 );

		add_filter( 'template_include', array( &$this, 'include_page_template' ), 0 );

		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 100 );

		add_filter( 'attachment_fields_to_edit', array( $this, 'add_fields_to_media' ), 0, 2 );

		add_action( 'wp_ajax_profolio_send_form', array( $this, 'send_profolio' ) );

		add_action( 'wp_ajax_nopriv_profolio_send_form', array( $this, 'send_profolio' ) );

		add_action( 'admin_bar_menu', array( $this, 'add_menu_in_admin_bar' ), 200 );


	}

	/**
	 * Clear db request when the page is "add-input-form".
	 *
	 * @param object $query wp query.
	 */
	public function pre_get_posts( $query ) {
		if ( ! empty( $query->query_vars['add-input-form'] ) ) {
			$query->set( 'post_type', array( 'page' ) );
			$query->set( 'posts_per_page', 1 );
		}
	}

	public function replace_wp_title( $title_parts ) {
		$show_input_form = sanitize_text_field( get_query_var( 'add-input-form' ) );
		$project_id      = sanitize_text_field( get_query_var( 'project_id' ) );
		if ( ! empty( $show_input_form ) ) {
			$title_parts['title'] = esc_html__( 'Project Input Form', 'localseomap-for-elementor' );

			if ( ! empty( $project_id ) ) {
				$title_parts['title'] = esc_html__( 'Editing: ', 'localseomap-for-elementor' ) . get_the_title( $project_id );
			}
		}

		return $title_parts;
	}


	public function include_page_template( $template ) {

		if ( wp_doing_ajax() ) {
			return $template;
		}

		$show_input_form = sanitize_text_field( get_query_var( 'add-input-form' ) );
		$allow_roles     = $this->get_allow_roles();

		if ( ! empty( $show_input_form ) ) {
			

			remove_all_filters( 'the_title' );
			remove_all_filters( 'the_content' );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'wp_head', 'wlwmanifest_link' );
			remove_action( 'wp_head', 'index_rel_link' );
			remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
			remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
			remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
			remove_action( 'wp_head', 'wp_generator' );

			if ( current_user_can( 'administrator' ) || ! empty( $allow_roles ) ) {

				$template = get_option( '_pap_input_form_template' );

				ob_start();
				do_action( 'localseomap_before_content' );

				include LOCALSEOMAP_PATH . 'data/template/front_ui_form.php';

				do_action( 'localseomap_after_content' );
				$content = ob_get_clean();

				global $post;


				$post->post_title   = esc_html__( 'Project Input Form', 'localseomap-for-elementor' );
				$post->post_content = $content;

				if ( $template == 'page' && file_exists( get_template_directory() . '/page.php' ) ) {
					return get_template_directory() . '/page.php';
				} else {
					return LOCALSEOMAP_PATH . 'data/template/front_ui_default.php';
				}

			} else {
				return LOCALSEOMAP_PATH . 'data/template/front_ui_none.php';
			}

		}

		return $template;

	}


	public function wp_enqueue_scripts() {
		wp_enqueue_style( 'select2', plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/css/select2.min.css' );
		wp_enqueue_style( 'datepicker', plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/css/datepicker.min.css' );
		wp_enqueue_style( 'localseomap-front-ui', plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/css/front-ui.css', '', LOCALSEOMAP_VERSION );

		wp_enqueue_editor();
		wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . get_option( '_pap_google_maps_api_key' ) . '&libraries=places,geometry', '', '' );
		wp_enqueue_script( 'select2', plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/js/select2.full.min.js' );
		wp_enqueue_script( 'datepicker', plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/js/datepicker.min.js' );

		wp_enqueue_media();
		wp_enqueue_script( 'localseomap-front-media', plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/js/front-media.js', '', LOCALSEOMAP_VERSION );
		wp_localize_script( 'localseomap-front-media', 'profolio_front_media_button',
			array(
				'text'   => '',
				'button' => esc_html__( 'Add media', 'localseomap-for-elementor' ),
			)
		);
		wp_enqueue_script( 'localseomap-front-ui', plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/js/front-ui.js', '', LOCALSEOMAP_VERSION );
	}

	public function add_fields_to_media( $form_fields, $post ) {

		$form_fields['profolio-media-title'] = array(
			'label' => esc_html__( 'Media title', 'localseomap-for-elementor' ),
			'input' => 'text',
			'value' => get_post_meta( $post->ID, 'profolio-media-title', true ),
			'helps' => esc_html__( 'Please add the profolio media title here', 'localseomap-for-elementor' ),
		);

		$form_fields['profolio-media-desc'] = array(
			'label' => esc_html__( 'Media title', 'localseomap-for-elementor' ),
			'input' => 'textarea',
			'value' => get_post_meta( $post->ID, 'profolio-media-desc', true ),
			'helps' => esc_html__( 'Please add the profolio media title here', 'localseomap-for-elementor' ),
		);

		return $form_fields;
	}

	public function send_profolio() {
		echo $this->add_project();
	}


	public function add_project() {
		if ( ! empty( $_POST ) && wp_verify_nonce( $_POST['profolio_action_nonce_field'], 'profolio_action_nonce' ) ) {

			$project = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

			$prefix = $this->get_metabox_prefix();

			if ( empty( $project['name'] ) ) {
				$response = array(
					'status'  => 'error',
					'message' => esc_html__( 'Please add the project name', 'localseomap-for-elementor' ),
				);
				wp_send_json( $response, '200' );
			}

			$project_data = [
				'post_type'    => 'localseomap_projects',
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
				'post_title'   => ! empty( $project['name'] ) ? $project['name'] : '',
				'post_content' => ! empty( $project['profolio_description'] ) ? $project['profolio_description'] : '',
			];

			if ( empty( $project['project_id'] ) ) {
				$project_data['ID'] = wp_insert_post( wp_slash( $project_data ) );

				/* add meta if it's a manual project */
				update_post_meta( $project_data['ID'], $prefix . '_manual_project', 'true' );
				update_post_meta( $project_data['ID'], $prefix . 'post_method', 'manual' );
			} else {
				$project_data['ID'] = $project['project_id'];
				wp_update_post( wp_slash( $project_data ) );
			}


            add_post_meta( $project_data['ID'], $prefix . 'uuid', 'manual_project_' . $project_data['ID'] );

			update_post_meta( $project_data['ID'], $prefix . 'start_date', $project['start_date'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_project_pro', $project['field_project_pro'] );


			update_post_meta( $project_data['ID'], $prefix . 'country', $project['profolio_country'] );
			update_post_meta( $project_data['ID'], $prefix . 'province', $project['profolio_province'] );
			update_post_meta( $project_data['ID'], $prefix . 'county', $project['profolio_county'] );
			update_post_meta( $project_data['ID'], $prefix . 'city', $project['profolio_city'] );
			update_post_meta( $project_data['ID'], $prefix . 'address', $project['profolio_location'] );


			update_post_meta( $project_data['ID'], $prefix . 'latitude', $project['profolio_location_lat'] );
			update_post_meta( $project_data['ID'], $prefix . 'longitude', $project['profolio_location_lng'] );


			update_post_meta( $project_data['ID'], $prefix . 'field_real_estate_price', $project['field_real_estate_price'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_real_estate_sale_type', $project['profolio_type'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_real_estate_status', $project['profolio_status'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_real_estate_year_built', $project['year_built'] );


			update_post_meta( $project_data['ID'], $prefix . 'field_story_testimonial_title', $project['testimonial_title'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_story_testimonial_author', $project['testimonial_author'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_story_testimonial_rating', $project['testimonial_rating'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_story_testimonial_body', $project['testimonial_body'] );

			update_post_meta( $project_data['ID'], $prefix . 'field_real_estate_mls_id', $project['estate_mls_id'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_real_estate_home_size', $project['profolio_home_size'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_real_estate_lot_size', $project['profolio_lot_size'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_real_estate_bathrooms', $project['profolio_bathrooms'] );
			update_post_meta( $project_data['ID'], $prefix . 'field_real_estate_bedrooms', $project['profolio_bedrooms'] );


			/*
			 * Add industry
			 * */
			$project_industry = array();
			if ( ! empty( $project['project_industry'] ) && is_array( $project['project_industry'] ) ) {
				$project_industry = $project['project_industry'];
			}


			if ( ! empty( $project['project_custom_industry'] ) ) {

				$project_industry = array_filter( array_merge( $project_industry, $project['project_custom_industry'] ) );
			}


			if ( ! empty( $project_industry ) ) {
				$ids = array();
				foreach ( $project_industry as $term_name ) {

					if ( is_numeric( $term_name ) ) {
						$ids[] = $term_name;
						continue;
					}


					$term = get_term_by( 'name', $term_name, 'localseomap_industry', ARRAY_A );

					if ( empty( $term ) || is_wp_error( $term ) ) {
						$slug = 'manual_' . sanitize_title( $term_name );
						$term = get_term_by( 'slug', $slug, 'localseomap_industry', ARRAY_A );
						if ( empty( $term ) || is_wp_error( $term ) ) {
							$term = wp_insert_term(
								$term_name,
								'localseomap_industry',
								array(
									'description' => '',
									'slug'        => $slug,
								)
							);
						}

					}
					if ( ! empty( $term['term_id'] ) ) {
						$ids[] = (int) $term['term_id'];
					}
				}


				/*
				* Add post to industry.
				*/
				if ( ! empty( $ids ) ) {
					wp_set_post_terms( $project_data['ID'], $ids, 'localseomap_industry' );
				}
			}

			/*
			 * Add tags
			 * */
			$project_tag = array();
			if ( ! empty( $project['project_tag'] ) && is_array( $project['project_tag'] ) ) {
				$project_tag = $project['project_tag'];
			}

			if ( ! empty( $project['profolio_custom_tag'] ) ) {
				$project_tag = array_merge( $project_tag, $project['profolio_custom_tag'] );
			}

			if ( ! empty( $project_tag ) ) {
				$ids = array();
				foreach ( $project_tag as $slug ) {
					if ( empty( $slug ) ) {
						continue;
					}
					$term = get_term_by( 'slug', sanitize_title( $slug ), 'localseomap_project_tag', ARRAY_A );

					if ( empty( $term ) || is_wp_error( $term ) ) {

						$term = wp_insert_term(
							$slug,
							'localseomap_project_tag'
						);

					}

					if ( ! empty( $term['term_id'] ) ) {
						$ids[] = $term['term_id'];
					}
				}

				/*
				* Add post to industry.
				*/
				if ( ! empty( $ids ) ) {
					wp_set_post_terms( $project_data['ID'], $ids, 'localseomap_project_tag' );
				}
			}

			/*
			 * Add main media
			 * */
			if ( ! empty( $project['profolio_project_main_image'] ) ) {
				set_post_thumbnail( $project_data['ID'], $project['profolio_project_main_image'] );
				$this->add_media( $project['profolio_project_main_image'] );
			}

			/*
			 * Add media
			 * */
			if ( ! empty( $project['profolio_project_media'] ) && is_array( $project['profolio_project_media'] ) ) {
				foreach ( $project['profolio_project_media'] as $attachment_id ) {
					$media_id = $this->add_media( $attachment_id );

                    add_post_meta( $media_id, $prefix . 'project_uuid', 'manual_project_' . $project_data['ID'] );
				}
				update_post_meta( $project_data['ID'], $prefix . 'media_list', $project['profolio_project_media'] );
			}

			$importer = new Importer();

			/*
			 * Convert location
			 * */

			$importer->location = $importer->reverse_geocoding( $project['profolio_location_lat'], $project['profolio_location_lng'] );
			if ( ! empty( $importer->location ) && is_array( $importer->location ) ) {
				foreach ( $importer->location as $key => $value ) {
					update_post_meta( $project_data['ID'], $prefix . $key, $value );
				}
			}

			$importer->add_new_area_save_post( $project_data['ID'] );

			$response = array(
				'status'   => 'success',
				'post_id'  => $project_data['ID'],
				'post_url' => get_permalink( $project_data['ID'] ),
				'edit_url' => home_url() . '/add-input-form/' . $project_data['ID'],
			);

		} else {
			$response = array(
				'status'  => 'error',
				'message' => esc_html__( 'No data', 'localseomap-for-elementor' ),
			);
		}

		wp_send_json( $response, '200' );
	}

	public function add_media( $attachment_id ) {

		$title = get_post_meta( $attachment_id, 'profolio-media-title', true );
		$desc  = get_post_meta( $attachment_id, 'profolio-media-desc', true );

		$prefix = $this->get_metabox_prefix();

		$media_data = [
			'post_type'    => 'localseomap_media',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_title'   => ! empty( $title ) ? $title : get_the_title( $attachment_id ),
			'post_content' => ! empty( $desc ) ? $desc : '',
		];

		$media = get_page_by_title( $media_data['post_title'], OBJECT, 'localseomap_media' );
		if ( ! empty( $media ) ) {
			$media_id = $media->ID;
		} else {
			$media_id = wp_insert_post( wp_slash( $media_data ) );
		}

		update_post_meta( $media_id, $prefix . 'state', 'approved' );

		if ( wp_attachment_is( 'video', $attachment_id ) && wp_get_attachment_url( $attachment_id ) ) {
			update_post_meta( $media_id, $prefix . 'field_video', wp_get_attachment_url( $attachment_id ) );
		}

		if ( wp_attachment_is( 'image', $attachment_id ) && $attachment_id ) {
			set_post_thumbnail( $media_id, $attachment_id );
		}

		return $media_id;


	}

	public function add_menu_in_admin_bar( \WP_Admin_Bar $wp_admin_bar ) {

		$prefix = $this->get_metabox_prefix();

		$allow_roles = $this->get_allow_roles();

		if ( current_user_can( 'administrator' ) || ! empty( $allow_roles ) ) {

			$project_id = get_the_ID();
			if ( is_single() && 'localseomap_projects' == get_post_type( $project_id ) ) {

				$uuid   = get_post_meta( $project_id, $prefix . 'uuid', true );
				$method = get_post_meta( $project_id, $prefix . 'post_method', true );

				if ( 'manual' === $method || strpos( $uuid, 'manual_project_' ) !== false ) {
					$wp_admin_bar->add_menu( [
						'id'    => 'localseomap_project_edit_link',
						'title' => esc_html__( 'Edit in LocalSEOMap', 'localseomap-for-elementor' ),
						'href'  => home_url() . '/add-input-form/' . get_the_ID(),
					] );
				}

				if ( 'profolio_api' === $method || strpos( $uuid, 'manual_project_' ) === false ) {
					$wp_admin_bar->add_menu( [
						'id'    => 'localseomap_project_edit_link_profolio',
						'title' => esc_html__( 'Edit in Profolio', 'localseomap-for-elementor' ),
						'href'  => get_post_meta( get_the_ID(), $prefix . 'workspace_url', true ),
					] );
				}
			}
		}
	}

}
