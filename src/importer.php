<?php
/**
 * User: localseomap
 * Date: 9.07.2019
 * @package LocalSeoMap/Importer
 */

namespace LocalSeoMap;


if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Importer
 * @since  1.0.0
 */
class Importer extends API {

	public $location;

	/**
	 * Init importer.
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {

		$this->location = array();

		add_action( 'wp', array( &$this, 'init_webhook' ), 0 );
		add_action( 'init', array( &$this, 'init_cron' ), 0 );
		add_action( 'admin_init', array( &$this, 'run_import' ), 0 );


		add_action( 'wp_ajax_profolio_import_projects', array( &$this, 'localseomap_ajax_import' ) );
		add_action( 'wp_ajax_profolio_import_industry', array( &$this, 'localseomap_ajax_import_industry' ) );

		register_activation_hook( LOCALSEOMAP_FILE, array( $this, 'activate' ) );
		add_action( 'admin_init', array( &$this, 'import_after_activated' ), 0 );

	}

	public function activate() {

		add_option( 'localseopam_activated', 'localseomap' );

	}

	public function import_after_activated() {

		if ( is_admin() && get_option( 'localseopam_activated' ) == 'localseomap' ) {

			delete_option( 'localseopam_activated' );

			$types = new Types();
			$types->new_taxonomy();

			$industry = json_decode(
				file_get_contents( LOCALSEOMAP_PATH . 'data/all_industry.json' ),
				true
			);

			$this->import_industries( $industry );

		}
	}

	public function localseomap_ajax_import() {

		if ( ! empty( $_POST['project'] ) ) {

			if ( ! empty( $_POST['project']['uuid'] ) ) {

				$uuid = sanitize_text_field( $_POST['project']['uuid'] );

				if ( ! empty( $_POST['skip_projects'] ) ) {
					$prefix  = $this->get_metabox_prefix();
					$post_id = $this->get_post_id_by_meta_key_and_value( $prefix . 'uuid', $uuid );

					$url = $this->get_value( $_POST['project'], 'field_cover_image', 'url' );

					if ( ! empty( $url ) ) {
						$attachment_id = $this->import_image( $url );
						set_post_thumbnail( $post_id, $attachment_id );
					}

					// Add testimonial cover.
					$testimonial_cover_id = $this->import_image( $this->get_value( $_POST['project'], 'field_story_testimonial_cover', 'url' ) );
					update_post_meta( $post_id, $prefix . 'field_story_testimonial_cover', $testimonial_cover_id );


					if ( ! empty( $post_id ) ) {
						echo wp_send_json( sanitize_text_field( $uuid ) );

						return;
					}
				}

				$this->update_project( $_POST['project'], true );
				echo wp_send_json( $uuid );

			}
			die();
		}

		$projects = $this->get_projects();

		echo wp_send_json( $projects );
		die();
	}

	public function localseomap_ajax_import_industry() {

		echo $this->import_industries( '', true );

		die();
	}

	/**
	 * Init schedule event.
	 * @since  1.0.0
	 * @access public
	 */
	public function init_cron() {

		if ( ! wp_next_scheduled( 'profolio_import_data' ) ) {
			wp_schedule_event( time(), 'daily', 'profolio_import_data' );
		}

		add_action(
			'profolio_import_data',
			function () {
				$this->import_industries();
			}
		);

	}

	/**
	 * Run import.
	 * @since  1.0.0
	 * @access public
	 */
	public function run_import() {
		if ( ! empty( $_GET['profolio_run_import'] ) ) {
			$this->import_projects( $this->get_projects() );
			wp_redirect( home_url( 'wp-admin/admin.php?page=crb_carbon_fields_container_profolio_addon_pro.php' ) );
		}
	}

	/**
	 * Init Profolio webhook.
	 * @since  1.0.0
	 * @access private
	 */
	public function init_webhook() {

		$rule = sanitize_text_field( get_query_var( 'profolio' ) );

		if ( empty( $rule ) ) {
			return;
		} elseif ( 'webhook' !== $rule ) {
			return;
		}

		header( 'Cache-Control: private, no-cache, must-revalidate, max-age=0' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );

		$data = file_get_contents( 'php://input' );

		if ( empty( $data ) ) {
			$this->profolio_log( 'No data.', true );
			header( 'HTTP/1.1 401' );
			die( esc_html__( 'No data.', 'localseomap-for-elementor' ) );
		}

		$data = json_decode( $data, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			if ( ! empty( $data ) ) {
				$this->profolio_log( 'Incorrect data of the request.', true );
				$this->profolio_log( print_r( $data, true ), true );
				die( esc_html__( 'Incorrect data.', 'localseomap-for-elementor' ) );
			}
		}

		if ( ! empty( $data ) ) {

			$log = '------------' . "\n";
			$log .= 'Status: webhook type: ' . $data['name'] . "\n";
			$log .= 'Time: : ' . date( 'Y-m-d H:i:s' ) . "\n";
			$log .= 'Body: ' . print_r( $data, true ) . "\n";
			$log .= '------------' . "\n";
			$this->profolio_log( $log );
		}


		if ( empty( $data['name'] ) ) {
			header( 'HTTP/1.1 401' );
			$this->profolio_log( 'Required parameter "name" is missing.', true );
			die( esc_html__( 'Required parameter "name" is missing.', 'localseomap-for-elementor' ) );
		}

		if ( empty( $data['data']['entity_uuid'] ) ) {
			$this->profolio_log( 'Required parameter "entity_uuid" is missing.', true );
			die( esc_html__( 'Required parameter "entity_uuid" is missing.', 'localseomap-for-elementor' ) );
		}

		$entity_uuid = reset( $data['data']['entity_uuid'] );

		if ( in_array( $data['name'], array( 'project_update', 'project_insert' ) ) ) {
			$log = '------------' . "\n";
			$log .= 'Status: before update or insert project ' . "\n";
			$log .= 'Uuid: ' . esc_html( $entity_uuid ) . "\n";
			$log .= '------------' . "\n";
			$this->profolio_log( $log );
			$this->update_project( $this->get_project( $entity_uuid ) );
		}

		if ( in_array( $data['name'], array( 'content_insert', 'content_update', 'content_reorder' ) ) ) {
			$this->import_media_by_id( $entity_uuid );
		}

		$delete_projects = carbon_get_theme_option( 'pap_delete_projects' );
		$delete_media    = carbon_get_theme_option( 'pap_delete_media' );

		if ( $data['name'] == 'content_delete' ) {
			if ( $delete_media ) {
				$prefix  = $this->get_metabox_prefix();
				$post_id = $this->get_post_id_by_meta_key_and_value( $prefix . 'uuid', $entity_uuid );
				wp_delete_post( $post_id, true );
				wp_delete_attachment( get_post_thumbnail_id( $post_id ) );
			}

		}

		if ( $data['name'] == 'project_delete' ) {
			if ( $delete_projects ) {
				$prefix  = $this->get_metabox_prefix();
				$post_id = $this->get_post_id_by_meta_key_and_value( $prefix . 'uuid', $entity_uuid );
				wp_delete_post( $post_id, true );
				wp_delete_attachment( get_post_thumbnail_id( $post_id ) );
			}
		}

	}


	/**
	 * Get list projects.
	 * @return bool|array
	 * @since  1.0.0
	 * @access public
	 */
	private function get_projects() {

		return $this->get( 'project' );

	}

	/**
	 * Get single project.
	 *
	 * @param string $id ID of the project.
	 *
	 * @return bool|array
	 * @since  1.0.0
	 * @access public
	 */
	private function get_project( $id ) {

		if ( empty( $id ) ) {
			return false;
		}

		$response = $this->get( 'project/' . $id );

		if ( ! empty( $response ) ) {
			return $response;
		}

		return false;

	}

	/**
	 * Import single project to WordPress.
	 *
	 * @param string $response Respondde data.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function update_project( $project, $run_import = false ) {

		if ( empty( $project['uuid'] ) ) {
			return '';
		}

		$prefix = $this->get_metabox_prefix();

		$post_id = $this->get_post_id_by_meta_key_and_value( $prefix . 'uuid', sanitize_text_field( $project['uuid'] ) );

		$project_data = [
			'post_type'    => 'localseomap_projects',
			'post_status'  => 'publish',
			'post_author'  => '1',
			'post_title'   => ! empty( $project['name'] ) ? esc_html( $project['name'] ) : '',
			'post_content' => ! empty( $project['description'] ) && is_string( $project['description'] ) ? wp_kses_post( $project['description'] ) : '',
		];

		$body = $this->get_value( $project, 'body', 'value' );
		if ( ! empty( $body ) && is_string( $body ) ) {
			$project_data['post_content'] = wp_kses_post( $body );
		}

		$visibility_type = $this->get_value( $project, 'field_folder_visibility_type', 'value' );
		if ( 'private' === $visibility_type ) {
			$project_data['post_status'] = 'private';
		}
		if ( 'unlisted' === $visibility_type ) {
			$project_data['post_status'] = 'draft';
		}

		if ( empty( $post_id ) || is_wp_error( $post_id ) ) {
			$post_id = wp_insert_post( wp_slash( $project_data ) );
			$log     = '------------' . "\n";
			$log     .= 'Status: insert new project ' . "\n";
			$log     .= 'Uuid2: ' . esc_html( $project['uuid'] ) . "\n";
			$log     .= 'Post id: ' . esc_html( $post_id ) . "\n";
			$log     .= '------------' . "\n";
			$this->profolio_log( $log );
			update_post_meta( $post_id, $prefix . 'uuid', $project['uuid'] );
		} else {
			$project_data['ID'] = $post_id;

			$changed = get_post_meta( $post_id, $prefix . 'changed' );

			/* Dont' update if the update time is the same. */
			if ( ! empty( $changed ) && $changed === $project['changed'] && ! $run_import ) {
				return false;
			}

			/* Update post. */
			wp_update_post( wp_slash( $project_data ) );
			update_post_meta( $post_id, $prefix . 'uuid', sanitize_text_field( $project['uuid'] ) );

			$log = '------------' . "\n";
			$log .= 'Status: Update project ' . "\n";
			$log .= 'Uuid: ' . print_r( $project, true ) . "\n";
			$log .= 'Post data: ' . print_r( wp_slash( $project_data ), true ) . "\n";
			$log .= '------------' . "\n";
			$this->profolio_log( $log );
		}


		update_post_meta( $post_id, $prefix . 'changed', sanitize_text_field( $project['changed'] ) );


		$url = $this->get_value( $project, 'field_cover_image', 'url' );

		if ( ! empty( $url ) ) {
			$attachment_id = $this->import_image( $url );
			set_post_thumbnail( $post_id, $attachment_id );
		}

		update_post_meta( $post_id, $prefix . 'post_method', 'profolio_api' );

		update_post_meta( $post_id, $prefix . 'cover_title', $project['cover_title'] );
		update_post_meta( $post_id, $prefix . 'cover_url', $project['cover_url'] );
		update_post_meta( $post_id, $prefix . 'owner_uid', $project['owner_uid'] );
		update_post_meta( $post_id, $prefix . 'owner_name', $project['owner_name'] );
		update_post_meta( $post_id, $prefix . 'url', $project['url'] );
		update_post_meta( $post_id, $prefix . 'workspace_url', $project['workspace_url'] );
		update_post_meta( $post_id, $prefix . 'created', $project['created'] );


		update_post_meta( $post_id, $prefix . 'start_date', $this->get_value( $project, 'field_project_start_date', 'value' ) );
		update_post_meta( $post_id, $prefix . 'timezone', $this->get_value( $project, 'field_project_start_date', 'timezone' ) );
		update_post_meta( $post_id, $prefix . 'field_project_verified', $this->get_value( $project, 'field_project_verified', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_folder_visibility_type', $visibility_type );

		update_post_meta( $post_id, $prefix . 'field_project_status', $this->get_value( $project, 'field_project_status', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_project_value', $this->get_value( $project, 'field_project_value', 'amount' ) );
		update_post_meta( $post_id, $prefix . 'field_project_currency_code', $this->get_value( $project, 'field_project_value', 'currency_code' ) );

		$field_project_pro = $this->get_value( $project, 'field_project_pro', 'value' );
		if ( empty( $project['field_project_pro'] ) ) { // it's a story if is story
			$project['field_project_pro'] = '2';
		}
		update_post_meta( $post_id, $prefix . 'field_project_pro', $field_project_pro );
		update_post_meta( $post_id, $prefix . 'field_project_permit_number', $this->get_value( $project, 'field_project_permit_number', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_project_customer_name', $this->get_value( $project, 'field_project_customer_name', 'value' ) );


		// Testimonials.
		update_post_meta( $post_id, $prefix . 'field_story_testimonial_title', $this->get_value( $project, 'field_story_testimonial_title', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_story_testimonial_author', $this->get_value( $project, 'field_story_testimonial_author', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_story_testimonial_rating', $this->get_value( $project, 'field_story_testimonial_rating', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_story_testimonial_body', $this->get_value( $project, 'field_story_testimonial_body', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_story_testimonial_videos', $this->get_value( $project, 'field_story_testimonial_videos', 'url' ) );


		// Add testimonial cover.
		$testimonial_cover_id = $this->import_image( $this->get_value( $project, 'field_story_testimonial_cover', 'url' ) );
		update_post_meta( $post_id, $prefix . 'field_story_testimonial_cover', $testimonial_cover_id );


		// Get the owner picture.
		if ( ! empty( $project['owner_uid'] ) ) {
			$user_data = $this->get( 'user/' . $project['owner_uid'] );
			update_post_meta( $post_id, $prefix . 'field_story_testimonial_picture', $user_data['picture']['url'] );
		}

		// save temp data
		update_post_meta( $post_id, $prefix . 'tmp_data', json_encode( $project ) );

		update_post_meta( $post_id, $prefix . 'field_real_estate_price', $this->get_value( $project, 'field_real_estate_price', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_real_estate_sale_type', $this->get_value( $project, 'field_real_estate_sale_type', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_real_estate_status', $this->get_value( $project, 'field_real_estate_status', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_real_estate_mls_id', $this->get_value( $project, 'field_real_estate_mls_id', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_real_estate_home_size', $this->get_value( $project, 'field_real_estate_home_size', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_real_estate_lot_size', $this->get_value( $project, 'field_real_estate_lot_size', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_real_estate_bedrooms', $this->get_value( $project, 'field_real_estate_bedrooms', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_real_estate_bathrooms', $this->get_value( $project, 'field_real_estate_bathrooms', 'value' ) );
		update_post_meta( $post_id, $prefix . 'field_real_estate_year_built', $this->get_value( $project, 'field_real_estate_year_built', 'value' ) );


		// Add Before photo.
		$entity_uuid = $this->get_value( $project, 'field_folder_before_photo', 'target_uuid' );
		$before_id   = $this->import_media_by_id( $entity_uuid );
		if ( ! empty( $before_id ) ) {
			$before_id = get_post_thumbnail_id( $before_id );
			update_post_meta( $post_id, $prefix . 'before_photo', $before_id );

		}
		// Add After photo.
		$entity_uuid = $this->get_value( $project, 'field_folder_after_photo', 'target_uuid' );
		$after_id    = $this->import_media_by_id( $entity_uuid );

		if ( ! empty( $after_id ) ) {
			$after_id = get_post_thumbnail_id( $after_id );
			update_post_meta( $post_id, $prefix . 'after_photo', $after_id );

		}

		$industry_term_ids = $this->get_value( $project, 'field_project_industry_category' );

		$ids = array();
		if ( is_array( $industry_term_ids ) ) {
			foreach ( $industry_term_ids as $industry_term_id ) {

				// get term by the profolio unique_id
				$term = $this->get_term_by_meta_value( 'localseomap_industry', 'unique_id', $industry_term_id['target_id'] );
				if ( ! empty( $term->term_id ) ) {
					$ids[] = $term->term_id;
				}
			}
		}

		/* Import all media and add them to industries */
		$media_ids = $this->import_media_list( $project['uuid'], $ids );
		update_post_meta( $post_id, $prefix . 'media_list', $media_ids );

		/*
		* Add post to industry.
		*/
		if ( ! empty( $ids ) ) {
			wp_set_post_terms( $post_id, $ids, 'localseomap_industry' );
		}

		/*
		* Add post to project_tag.
		*/
		if ( ! empty( $project['field_tags_text'] ) && is_array( $project['field_tags_text'] ) ) {

			$ids = array();
			foreach ( $project['field_tags_text'] as $tag ) {

				$slug = sanitize_title( $tag );

				$term = get_term_by( 'slug', $slug, 'localseomap_project_tag', ARRAY_A );

				if ( empty( $term ) || is_wp_error( $term ) ) {

					$term = wp_insert_term(
						$tag,
						'localseomap_project_tag',
						array(
							'description' => '',
							'slug'        => $slug,
						)
					);
				}

				$ids[] = $term['term_id'];


			}
			wp_set_post_terms( $post_id, $ids, 'localseomap_project_tag' );

		}

		/*
		* Add the location info.
		*/
		$location = $this->get_value( $project, 'field_project_location', '', true );

		if ( is_array( $location ) ) {
			foreach ( $location as $key => $value ) {
				update_post_meta( $post_id, $prefix . $key, $value );
			}
		}

		/*
		 * Convert location
		 * */
		$this->location = $this->reverse_geocoding( $location['latitude'], $location['longitude'] );
		if ( ! empty( $this->location ) && is_array( $this->location ) ) {
			foreach ( $this->location as $key => $value ) {
				update_post_meta( $post_id, $prefix . $key, $value );
			}
		}

		$this->add_new_area_save_post( $post_id );
	}

	/**
	 * Import projects to WordPress.
	 *
	 * @param string $response Respondde data.
	 * @param string $limit    How many items to import.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	private function import_media_by_id( $entity_uuid ) {
		if ( empty( $entity_uuid ) ) {
			return false;
		}
		$prefix   = $this->get_metabox_prefix();
		$media_id = $this->get_post_id_by_meta_key_and_value( $prefix . 'uuid', $entity_uuid );
		if ( ! empty( $media_id ) ) {
			return $this->import_media( $this->get( 'media/' . $entity_uuid ), $media_id );
		} else {
			return $this->import_media( $this->get( 'media/' . $entity_uuid ) );
		}
	}


	/**
	 * Import projects to WordPress.
	 *
	 * @param string $response Respondde data.
	 * @param string $limit    How many items to import.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function import_projects( $response = '', $limit = '' ) {

		if ( empty( $response ) || ! is_array( $response ) ) {
			return;
		}

		$i = 0;
		foreach ( $response as $project ) {

			$this->update_project( $project, true );

			$i ++;

			if ( ! empty( $limit ) && $limit === $i ) {
				break;
			}
		}


	}

	/**
	 * Import single media.
	 *
	 * @param string $media_uuid Unique id.
	 */
	public function import_media( $media = '', $media_id = '' ) {

		if ( empty( $media ) || ! is_array( $media ) ) {
			return;
		}

		$prefix = $this->get_metabox_prefix();

		$media_data = [
			'post_type'    => 'localseomap_media',
			'post_status'  => 'publish',
			'post_author'  => '1',
			'post_title'   => ! empty( $media['title'] ) ? $media['title'] : '',
			'post_content' => ! empty( $media['description'] ) ? $media['description'] : '',
		];

		$body = $this->get_value( $media, 'body', 'value' );
		if ( ! empty( $body ) ) {
			$media_data['post_content'] = $body;
		}

		if ( empty( $media_id ) ) {
			$media_id = wp_insert_post( wp_slash( $media_data ) );
			update_post_meta( $media_id, $prefix . 'uuid', $media['uuid'] );
		} else {
			$media_data['ID'] = $media_id;

			/* Update media post. */
			wp_update_post( wp_slash( $media_data ) );
		}

		// save temp data
		update_post_meta( $media_id, $prefix . 'tmp_data', json_encode( $media ) );

		$meta_keys = array(
			'created',
			'vuuid',
			'nid',
			'comment',
		);

		foreach ( $meta_keys as $meta_key ) {
			update_post_meta( $media_id, $prefix . $meta_key, $media[ $meta_key ] );
		}

		update_post_meta( $media_id, $prefix . 'media_create_datetime', $this->get_value( $media, 'field_ifd0_datetime', 'value' ) );
		update_post_meta( $media_id, $prefix . 'media_long', $this->get_value( $media, 'field_gps_gpslatitude', 'value' ) );
		update_post_meta( $media_id, $prefix . 'media_lat', $this->get_value( $media, 'field_gps_gpslongitude', 'value' ) );
		update_post_meta( $media_id, $prefix . 'project_uuid', $this->get_value( $media, 'field_personal_tag', 'uuid' ) );

		// add flow state
		update_post_meta( $media_id, $prefix . 'pin_pf_state', $this->get_value( $media, 'field_pin_pf_state', 'value' ) );

		if ( empty( $media['state'] ) ) {
			$media['state'] = 'approved';
		}

		if ( ! empty( $media['state'] ) ) {
			update_post_meta( $media_id, $prefix . 'state', $media['state'] );
		}

		if ( ! empty( $media['weight'] ) ) {
			update_post_meta( $media_id, $prefix . 'sort', $media['weight'] );
		}

		$longitude = $this->get_value( $media, 'field_gps_gpslongitude', 'value' );
		$latitude  = $this->get_value( $media, 'field_gps_gpslatitude', 'value' );

		if ( ! empty( $media['data'] ) ) {
			$media_data = unserialize( $media['data'] );
			if ( empty( $longitude ) ) {
				$longitude = $media_data['geoip_location']['latitude'];
			}
			if ( empty( $latitude ) ) {
				$latitude = $media_data['geoip_location']['longitude'];
			}
		}

		update_post_meta( $media_id, $prefix . 'media_long', $longitude );
		update_post_meta( $media_id, $prefix . 'media_lat', $latitude );

		update_post_meta( $media_id, $prefix . 'media_data', $media_data );

		// import video
		$video = $this->get_value( $media, 'field_video', 'playablefiles' );
		if ( ! empty( $video[0]['url'] ) ) {
			update_post_meta( $media_id, $prefix . 'field_video', $video[0]['url'] );
		}

		$url = $this->get_value( $media, 'field_image', 'url' );
		if ( ! empty( $url ) ) {
			$attachment_id = $this->import_image( $url );
			set_post_thumbnail( $media_id, $attachment_id );
		}

		return $media_id;

	}


	/**
	 * Import media from the project.
	 *
	 * @param string $project_uuid Unique id.
	 */
	public function import_media_list( $project_uuid = '', $term_ids = '' ) {

		$prefix     = $this->get_metabox_prefix();
		$media_list = $this->get( 'media?project_uuid=' . $project_uuid );

		$media_ids = array();
		if ( ! empty( $media_list ) && is_array( $media_list ) ) {
			foreach ( $media_list as $media ) {

				$media_id = $this->import_media_by_id( $media['uuid'] );
				if ( empty( $media_id ) ) {
					continue;
				}
				update_post_meta( $media_id, $prefix . 'project_uuid', $project_uuid );

				$media_ids[] = $media_id;

				/*
				* Add post to industry.
				*/
				if ( ! empty( $term_ids ) ) {
					wp_set_post_terms( $media_id, $term_ids, 'localseomap_industry' );
				}

			}
		}

		return $media_ids;
	}

	/**
	 * Import industries.
	 *
	 * @param string $suf Suffix.
	 */
	public function import_industries( $data = array(), $return_count = false ) {

		if ( empty( $data ) ) {
			$data = $this->get( 'industry/full_vocabulary/' );
		}


		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}

		$counter = 0;


		foreach ( $data as $industry ) {

			if ( empty( $industry['tid'] ) || empty( $industry['name'] ) ) {
				continue;
			}

			$term = $this->get_term_by_meta_value( 'localseomap_industry', 'unique_id', $industry['tid'] );

			if ( is_wp_error( $term ) || empty( $term ) ) {
				$term = get_term_by( 'name', $industry['name'], 'localseomap_industry' );
			}


			$industry_original_name = $industry['name'];
			$industry['name']       = Localize::translate( $industry_original_name, '_pap_translation_industry' );
			$slug                   = sanitize_title( $industry['name'] );

			if ( ( is_wp_error( $term ) || empty( $term ) ) && ! empty( $industry['name'] ) ) {

				$term = (object) wp_insert_term(
					$industry['name'],
					'localseomap_industry',
					array(
						'description' => $industry['description'],
						'slug'        => $slug,
					)
				);

				if ( ! is_wp_error( $term ) ) {
					$counter ++;
				}
			}


			if ( ! is_wp_error( $term ) && ! empty( $term->term_id ) ) {

				add_term_meta( $term->term_id, 'original_name', $industry_original_name, true );
				add_term_meta( $term->term_id, 'unique_id', $industry['tid'], true );
				add_term_meta( $term->term_id, 'profolio_uuid', $industry['uuid'], true );

				if ( ! empty( $industry['categories'] ) && is_array( $industry['categories'] ) ) {

					$sub_terms = array();
					foreach ( $industry['categories'] as $key => $category ) {
						$sub_terms = array_merge( $sub_terms, $category['items'] );
					}

					if ( ! empty( $sub_terms ) ) {
						foreach ( $sub_terms as $key => $category ) {


							$sub_industry_original_name = $category['name'];
							$sub_term                   = $this->get_term_by_meta_value( 'localseomap_industry', 'unique_id', $category['tid'] );


							if ( is_wp_error( $sub_term ) || empty( $sub_term ) ) {
								$sub_term = get_term_by( 'name', $category['name'], 'localseomap_industry' );
							}

							$category['name'] = Localize::translate( $sub_industry_original_name, '_pap_translation_industry' );
							$slug             = sanitize_title( $category['name'] );

							if ( ( empty( $sub_term ) || is_wp_error( $sub_term ) ) && ! empty( $industry['name'] ) ) {
								$sub_term = wp_insert_term(
									$category['name'], // the term.
									'localseomap_industry', // the taxonomy.
									array(
										// 'description'=> 'Some description.',
										'slug'   => $slug,
										'parent' => $term->term_id,
									)
								);
								if ( ! is_wp_error( $sub_term ) ) {
									$counter ++;
								}
							}


							if ( ! empty( $sub_term->term_id ) && ! is_wp_error( $sub_term ) ) {
								add_term_meta( $sub_term->term_id, 'original_name', $sub_industry_original_name, true );
								add_term_meta( $sub_term->term_id, 'unique_id', $category['tid'], true );
								add_term_meta( $sub_term->term_id, 'profolio_uuid', $category['uuid'], true );
							}
						}
					}
				}
			}

		}

		if ( $return_count ) {
			return $counter;
		}

		/*error_log( print_r( $data, true ), 0 );
		die();*/

	}

	/**
	 * Import image to WordPress.
	 *
	 * @param string $url The image url.
	 *
	 * @return bool|int|string
	 * @since  1.0.0
	 * @access private
	 */
	private function import_image( $url = '' ) {

		if ( empty( $url ) ) {
			return '';
		}

		try {

			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );

			return media_sideload_image( $url, 0, '', 'id' );


		} catch ( Exception $e ) {

			$log = '------------' . "\n";
			$log .= 'Error upload image ' . "\n";
			$log .= '' . $e->getMessage() . "\n";
			$log .= '------------' . "\n";
			$this->profolio_log( $log );

		}


	}

	/**
	 * Get attachment ID by attachment title.
	 *
	 * @param string $post_title Post title.
	 *
	 * @return mixed
	 */
	function get_attachment_id( $post_title ) {
		global $wpdb;
		$attachment = $wpdb->get_col(
			$wpdb->prepare(
				'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_name=\'%s\';',
				sanitize_title( $post_title )
			)
		);

		if ( empty( $attachment ) ) {
			return null;
		}

		return $attachment[0];
	}

	/**
	 * Get value.
	 *
	 * @param string $project Project.
	 * @param string $option  The project option.
	 * @param string $key     The optopn key.
	 * @param bool $first     Get first item.
	 *
	 * @return string
	 */
	private function get_value( $project, $option, $key = '', $first = false ) {

		if ( ! empty( $key ) ) {

			if ( ! empty( $project[ $option ]['und'][0][ $key ] ) ) {

				return $project[ $option ]['und'][0][ $key ];

			}

		} elseif ( ! empty( $project[ $option ]['und'] ) ) {

			if ( is_array( $project[ $option ]['und'] ) && $first ) {
				return reset( $project[ $option ]['und'] );
			}

			return $project[ $option ]['und'];

		} elseif ( ! empty( $project[ $option ][0] ) ) {

			return $project[ $option ][0];

		} elseif ( ! empty( $project[ $option ] ) ) {

			return $project[ $option ];

		}

		return '';

	}


	/**
	 * @param $term_name
	 * @param $location
	 * @param $area_level
	 *
	 * @return array
	 */
	private function set_new_area_term( $term_name, $location, $area_level ) {

		$area = $location[ $area_level ];

		$name_area_tag = trim( $term_name ) . ' ' . trim( $area );
		$slug_area_tag = sanitize_title( $name_area_tag );

		$term = get_term_by( 'slug', $slug_area_tag, 'localseomap_area_tags', ARRAY_A );

		$id = '';
		if ( empty( $term ) || is_wp_error( $term ) ) {

			$term = wp_insert_term(
				$name_area_tag,
				'localseomap_area_tags',
				array(
					'description' => '',
					'slug'        => $slug_area_tag,
				)
			);


			if ( ! empty( $term['term_id'] ) ) {
				$id = (int) $term['term_id'];

				add_term_meta( $term['term_id'], 'original_name', $name_area_tag, true );

				if ( ! empty( $location ) && is_array( $location ) ) {
					foreach ( $location as $area_key => $value ) {
						add_term_meta( $term['term_id'], $area_key, trim( $value ), true );
					}
				}

			}
		} else {
			return $term['term_id'];
		}

		return $id;
	}

	/**
	 * @param $post_id
	 * @param $industries
	 * @param $tt_ids
	 * @param $taxonomy
	 *
	 * @return string
	 */
	public function add_new_area_terms( $post_id, $industries, $tt_ids, $taxonomy ) {

		if ( $taxonomy !== 'localseomap_industry' ) {
			return '';
		}

		$area_levels        = carbon_get_theme_option( 'pap_select_area_levels' );
		$disable__area_tags = carbon_get_theme_option( 'pap_disable_parent_area_tags' );

		if ( empty( $area_levels ) || ! is_array( $area_levels ) ) {
			$area_levels = array( 'city', 'province' );
		}

		if ( ! is_wp_error( $industries ) && is_array( $industries ) ) {

			$ids = array();
			foreach ( $industries as $industry ) {

				if ( ! empty( $disable__area_tags ) && $industry->parent == '0' ) {

					if ( strpos( $industry->slug, 'manual_' ) === false ) {
						continue;
					}
				}

				foreach ( $area_levels as $area_level ) {
					/* get location */
					if ( ! empty( $this->location[ $area_level ] ) ) {
						$id = $this->set_new_area_term( $industry->name, $this->location, $area_level );
						if ( ! empty( $id ) && is_numeric( $id ) ) {
							$ids[] = $id;
						}
					}
				}

			}

			/*
			* Add post to project_area_tags.
			*/
			if ( ! empty( $ids ) ) {
				wp_set_post_terms( $post_id, $ids, 'localseomap_area_tags' );
			}

		}

	}

	public function add_new_area_save_post( $post_id ) {

		$post = get_post( $post_id );

		// allow 'publish', 'draft', 'future'
		if ( $post->post_type != 'localseomap_projects' || $post->post_status == 'auto-draft' ) {
			return;
		}

		$taxonomy = 'localseomap_industry';

		$industry = wp_get_post_terms(
			$post_id,
			$taxonomy
		);

		if ( ! empty( $industry ) && is_array( $industry ) ) {
			$this->add_new_area_terms( $post_id, $industry, '', $taxonomy );
		}

	}


}
