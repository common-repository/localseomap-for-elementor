<?php
/**
 * User: localseomap
 * Date: 9.07.2019
 * @package LocalSeoMap/Admin
 */

namespace LocalSeoMap;

use ElementorPro;
use WP_Query;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Admin
 *
 * @since  1.0.0
 */
class Admin {


	protected $template = 'profolio-project-template.php';

	/**
	 * Leads constructor.
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Get the prefix of the metabox.
	 * @since  1.0.0
	 * @access private
	 */
	public static function get_metabox_prefix() {
		return 'localseomap-';
	}

	/**
	 * Init all hooks of the plugin.
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {

		add_action( 'after_setup_theme', array( &$this, 'init_carbon_fields' ) );

		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );

		add_action( 'admin_init', array( &$this, 'localseomap_clear_logs' ) );

		add_action( 'admin_menu', array( &$this, 'add_submenu_link' ), 1 );

		add_action( 'pre_get_posts', array( &$this, 'offset_projects' ), 10000 );

		//add_action( 'rest_after_insert_localseomap_projects', array( &$this, 'change_slug_project' ), 10, 3 );
		//add_action( 'save_post', array( &$this, 'change_slug_project' ), 10, 3 );

		add_filter( 'theme_page_templates', function ( $posts_templates ) {

			$posts_templates = array_merge( $posts_templates, array(
				$this->template => esc_html__( 'Projects template', 'localseomap-for-elementor' ),
			) );

			return $posts_templates;
		} );

		add_action( 'carbon_fields_container_localseomap_for_elementor_before_fields', array(
			&$this,
			'remove_autocomplete'
		) );

		add_action( 'admin_init', array(
			&$this,
			'allow_users_upload'
		) );

		$pap_show_leads_button = get_option( '_pap_show_leads_button' );

		if ( isset( $pap_show_leads_button ) && $pap_show_leads_button ) {
			add_action( 'elementor_pro/init', array( &$this, 'elementor_pro_init' ) );
		}

		add_action( 'wp_ajax_localseomap_geo_test', array( &$this, 'localseomap_geo_test' ), 0 );
		add_action( 'wp_ajax_nopriv_localseomap_geo_test', array( &$this, 'localseomap_geo_test' ), 0 );


	}

	public function elementor_pro_init() {

		// Here its safe to include our action class file
		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'src/profolio_actions.php';

		// Instantiate the action class
		$action = new \Profolio_Action();

		// Register the action with form widget
		ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $action->get_name(), $action );

	}

	public function admin_enqueue_scripts() {

		wp_enqueue_script( 'localseomap-admin', plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/js/profolio-admin.js', array( 'wp-i18n' ), LOCALSEOMAP_VERSION, true );
	}


	public function add_submenu_link() {
		global $submenu;

		$submenu['crb_carbon_fields_container_localseomap_for_elementor.php'][] = array(
			esc_html__( 'Settings', 'localseomap-for-elementor' ),
			'manage_options',
			home_url( 'wp-admin/admin.php?page=crb_carbon_fields_container_localseomap_for_elementor.php' )
		);


		if ( '' != get_option( 'permalink_structure' ) ) {
			// using pretty permalinks, append to url
			$form_link = user_trailingslashit( 'add-input-form' );
		} else {
			$form_link = $this->clear_permalinks() . '/' . add_query_arg( 'add-input-form', 'true', '' );
		}

		if ( current_user_can( 'administrator' ) ) {
			$submenu['crb_carbon_fields_container_localseomap_for_elementor.php'][] = array(
				esc_html__( 'Input form', 'localseomap-for-elementor' ),
				'manage_options',
				home_url( $form_link )
			);
		} else {

			$roles = carbon_get_theme_option( 'pap_capabilities' );

			$allow_roles = array();
			$user        = wp_get_current_user();
			if ( ! empty( $roles ) && ! empty( $user->roles ) ) {
				$allow_roles = array_intersect( $user->roles, $roles );
			}

			if ( ! empty( $allow_roles ) ) {
				add_menu_page( esc_html__( 'Input form', 'localseomap-for-elementor' ), esc_html__( 'Input form', 'localseomap-for-elementor' ), 'read', home_url( $form_link ), '', plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/img/icon-m.png', 50 );
			}
		}


		$args  = array(
			'post_type'  => 'page',
			'meta_key'   => '_wp_page_template',
			'meta_value' => $this->template
		);
		$pages = get_posts( $args );
		if ( ! empty( $pages ) ) {
			$submenu['crb_carbon_fields_container_localseomap_for_elementor.php'][] = array(
				$pages[0]->post_title,
				'manage_options',
				get_the_permalink( $pages[0]->ID )
			);
		}

	}


	/**
	 * Get post id from meta key and value
	 *
	 * @param string $key  Meta key.
	 * @param mixed $value Meta value.
	 *
	 * @return int|bool
	 */
	public function get_post_id_by_meta_key_and_value( $meta_key, $meta_value ) {
		global $wpdb;

		$post_id = wp_cache_get( md5( $meta_key . $meta_value ) );
		if ( $post_id === false ) {

			$post_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s",
					$meta_key,
					$meta_value
				)
			);
			wp_cache_set( md5( $meta_key . $meta_value ), $post_id );
		}

		if ( ! empty( $post_id ) ) {
			return $post_id;
		} else {
			return false;
		}
	}

	function get_map_locations( $ids = array(), $args = array() ) {

		$prefix = $this->get_metabox_prefix();


		$args = array(
			'fields'         => 'ids',
			'posts_per_page' => - 1,
			'post_type'      => 'localseomap_projects',
		);

		if ( ! empty( $ids ) && is_array( $ids ) ) {
			$args['post__in'] = $ids;
		}

		$projects = new WP_Query( $args );

		$locations = array();
		if ( $projects->have_posts() ) {
			while ( $projects->have_posts() ) {
				$projects->the_post();
				$project = get_post( get_the_ID() );

				$longitude = get_post_meta( get_the_ID(), $prefix . 'longitude', true );
				$latitude  = get_post_meta( get_the_ID(), $prefix . 'latitude', true );

				$address   = array();
				$address[] = get_post_meta( get_the_ID(), $prefix . 'city', true );
				$address[] = get_post_meta( get_the_ID(), $prefix . 'province', true );
				$address[] = get_post_meta( get_the_ID(), $prefix . 'country', true );
				$address   = array_filter( $address );

				/* $terms = get_the_terms( $project->ID, 'localseomap_industry' );

				foreach ( $terms as $key => $term ) {
					$terms[ $key ]->link = get_term_link( $term );
				}*/

				$image = get_the_post_thumbnail( $project->ID, 'medium_large', array( 'class' => 'profolio-bg-img' ) );
				$image = str_replace( 'src=', 'data-src=', $image );
				$image = str_replace( 'srcset=', 'data-srcset=', $image );

				$locations[] = array(
					'link'    => get_the_permalink( $project ),
					'lat'     => $latitude,
					'lng'     => $longitude,
					'title'   => $project->post_title,
					'image'   => $image,
					'desc'    => $project->post_content,
					'address' => implode( ', ', $address ),
					//'terms' => $terms,
				);
			}
		}

		wp_reset_postdata();


		return $locations;
	}

	/**
	 * Init Carbon Fields.
	 * @since  1.0.0
	 * @access public
	 */
	public function init_carbon_fields() {
		\Carbon_Fields\Carbon_Fields::boot();
	}

	/**
	 * @param $msg
	 * Error log function.
	 */
	public static function profolio_log( $msg, $error = false ) {


		if ( defined( "LOCALSEOMAP_ENABLE_LOGS" ) && LOCALSEOMAP_ENABLE_LOGS == true ) {

			if ( $error ) {
				$error_logs = get_option( 'localseomap_error_logs' );
				update_option( 'localseomap_error_logs', $error_logs . $msg );
			} else {
				$success_logs = get_option( 'localseomap_success_logs' );
				update_option( 'localseomap_success_logs', $success_logs . $msg );
			}

		}
	}

	/**
	 * @param $msg
	 * Clear logs
	 */
	public function localseomap_clear_logs() {
		if ( ! empty( $_GET['localseomap_clear_logs'] ) ) {

			update_option( 'localseomap_error_logs', '' );
			update_option( 'localseomap_success_logs', '' );
			wp_redirect( home_url( 'wp-admin/admin.php?page=crb_carbon_fields_container_localseomap_for_elementor.php' ) );

		}

	}

	public function get_lat_long( $post_id ) {

		$google_maps_api_key = get_option( '_pap_google_maps_api_key' );
		if ( empty( $google_maps_api_key ) ) {
			return null;
		}

		$prefix           = $this->get_metabox_prefix();
		$pap_type_address = carbon_get_theme_option( 'pap_type_address' );

		$lat_long              = array();
		$lat_long['longitude'] = get_post_meta( $post_id, $prefix . 'longitude', true );
		$lat_long['latitude']  = get_post_meta( $post_id, $prefix . 'latitude', true );

		if ( carbon_get_theme_option( 'pap_type_location' ) == 'media' ) {

			$media_data = get_post_meta( $post_id, $prefix . 'media_data', true );

			if ( empty( $lat_long['longitude'] ) && ! empty( $media_data['geoip_location'] ) ) {
				$lat_long['longitude'] = $media_data['geoip_location']['longitude'];
			}
			if ( empty( $lat_long['latitude'] ) && ! empty( $media_data['geoip_location'] ) ) {
				$lat_long['latitude'] = $media_data['geoip_location']['latitude'];
			}
		}

		$max_radius = carbon_get_theme_option( 'pap_app_max_radius' );
		if ( empty( $max_radius ) ) {
			$max_radius = 1609;
		}

		if ( ( ! empty( $lat_long['longitude'] ) && ! empty( $lat_long['latitude'] ) ) && $pap_type_address == 'general' ) {
			$rand_meter            = rand( 0, $max_radius );
			$rand_coef             = $rand_meter * 0.0000089;
			$lat_long['longitude'] += $rand_coef / cos( $lat_long['latitude'] * 0.018 );
			$lat_long['latitude']  += $rand_coef;
		}

		return $lat_long;

	}


	/**
	 * @param $query
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function offset_projects( $query ) {

		if ( empty( $query->query_vars['post_type'] ) || $query->query_vars['post_type'] != 'localseomap_projects' ) {
			return;
		}

		$offset = localseomap_get_offset( $query );

		if ( ! empty( $offset ) ) {
			$query->set( 'posts_per_page', $offset );
		}


	}

	public function change_slug_project( $post_ID, $post, $update ) {

		// allow 'publish', 'draft', 'future'
		if ( $post->post_type != 'localseomap_projects' || $post->post_status == 'auto-draft' ) {
			return;
		}

		// use title, since $post->post_name might have unique numbers added
		$new_slug = sanitize_title( $post->post_title, $post_ID );

		if ( $new_slug == $post->post_name ) {
			return;
		} // already set

		// unhook this function to prevent infinite looping
		remove_action( 'save_post', 'slug_save_post_callback', 10, 3 );

		// update the post slug (WP handles unique post slug)
		wp_update_post( array(
			'ID'        => $post_ID,
			'post_name' => $new_slug
		) );
		// re-hook this function
		add_action( 'save_post', 'slug_save_post_callback', 10, 3 );
	}

	public function remove_autocomplete() {
		?>
        <input style="display:none">
        <input type="password" style="display:none">
		<?php
	}

	protected function get_number_of_testimonials() {

		$profolio_number_of_testimonials = get_transient( 'profolio_number_of_testimonials' );

		if ( false !== $profolio_number_of_testimonials ) {
			return $profolio_number_of_testimonials;
		}

		$args = array(
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'post_type'      => 'localseomap_projects',
			'post_status'    => 'publish',
			'meta_key'       => $this->get_metabox_prefix() . 'field_story_testimonial_rating',
			'meta_value'     => '',
			'meta_compare'   => '!=',
		);

		$query = new \WP_Query( $args );
		if ( ! empty( $query->found_posts ) ) {
			set_transient( 'profolio_number_of_testimonials', $query->found_posts, DAY_IN_SECONDS );

			return $query->found_posts;
		}

		return 0;
	}

	private function clear_permalinks() {
		$rewritecode    = array(
			'/%year%',
			'/%monthnum%',
			'/%day%',
			'/%hour%',
			'/%minute%',
			'/%second%',
			'/%postname%',
			'/%post_id%',
			'/%category%',
			'/%author%',
			'/%pagename%',
		);
		$rewritereplace =
			array(
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
				'',
			);
		$permalink      = get_option( 'permalink_structure' );

		return trim( str_replace( $rewritecode, $rewritereplace, $permalink ), '/' );
	}

	public function reverse_geocoding( $lat, $lng ) {

		if ( empty( $lat ) ) {
			return null;
		}

		if ( empty( $lng ) ) {
			return null;
		}

		$key = carbon_get_theme_option( 'pap_google_maps_api_server_key' );

		if ( empty( $key ) ) {
			$log = '------------' . "\n";
			$log .= 'Please add Google Maps server key' . "\n";
			$log .= '------------' . "\n";
			$this->profolio_log( $log );

			return null;
		}

		$language = carbon_get_theme_option( 'pap_language' );

		if ( ! empty( $language ) ) {
			$language = '&language=' . $language;
		}

		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim( $lat ) . ',' . trim( $lng ) . '&sensor=false&key=' . $key . $language;

		$json = @file_get_contents( $url );

		$data = json_decode( $json, true );


		$status   = $data['status'];
		$response = array();

		$city_param = carbon_get_theme_option( 'pap_select_city_param' );
		if ( empty( $city_param ) ) {
			$city_param = array( 'neighborhood' );
		}


		if ( $status == "OK" ) {
			//Get address from json data
			foreach ( $data['results'][0]['address_components'] as $key => $addressComponent ) {

				if ( ! empty( $city_param[3] ) && in_array( $city_param[3], $addressComponent['types'] ) ) {
					$response['city'] = $addressComponent['long_name'];
					continue;

				}
				if ( ! empty( $city_param[2] ) && in_array( $city_param[2], $addressComponent['types'] ) ) {
					$response['city'] = $addressComponent['long_name'];
					break;

				}
				if ( ! empty( $city_param[1] ) && in_array( $city_param[1], $addressComponent['types'] ) ) {
					$response['city'] = $addressComponent['long_name'];
					break;

				}
				if ( in_array( $city_param[0], $addressComponent['types'] ) ) {
					$response['city'] = $addressComponent['long_name'];
					break;

				}

			}

			foreach ( $data['results'][0]['address_components'] as $key => $addressComponent ) {

				if ( in_array( 'administrative_area_level_2', $addressComponent['types'] ) ) {
					$response['county'] = $addressComponent['short_name'];
				}
				if ( in_array( 'administrative_area_level_1', $addressComponent['types'] ) ) {
					$response['province'] = $addressComponent['short_name'];
				}
				if ( in_array( 'country', $addressComponent['types'] ) ) {
					$response['country'] = $addressComponent['short_name'];
				}

			}

			$log = '------------' . "\n";
			$log .= 'Status Google Maps' . "\n";
			$log .= print_r( $response, true ) . "\n";
			$log .= '------------' . "\n";
			$this->profolio_log( $log );

			return $response;

		} elseif ( $status == 'REQUEST_DENIED' ) {
			if ( ! empty( $data->error_message ) ) {
				$log = '------------' . "\n";
				$log .= 'Status Google Maps' . "\n";
				$log .= $data->error_message . "\n";
				$log .= '------------' . "\n";
				$this->profolio_log( $log, true );

				return null;
			} else {
				$log = '------------' . "\n";
				$log .= 'Status Google Maps' . "\n";
				$log .= 'Location Not Found' . "\n";
				$log .= $json . "\n";
				$log .= '------------' . "\n";
				$this->profolio_log( $log, true );

				return null;
			}
		} else {
			$log = '------------' . "\n";
			$log .= 'Status Google Maps' . "\n";
			$log .= 'Location Not Found' . "\n";
			$log .= $json . "\n";
			$log .= '------------' . "\n";
			$this->profolio_log( $log, true );

			return null;
		}

	}

	/**
	 * Get the term by the meta key and the meta value.
	 *
	 * @param string $taxonomy Taxonomy.
	 * @param string $key      The meta key.
	 * @param string $value    The meta value.
	 *
	 * @return string
	 */
	protected function get_term_by_meta_value( $taxonomy, $key, $value ) {

		if ( empty( $key ) || empty( $value ) ) {
			return '';
		}

		$args  = array(
			'hide_empty' => false, // also retrieve terms which are not used yet.
			'meta_query' => array(
				array(
					'key'     => $key,
					'value'   => $value,
					'compare' => 'LIKE',
				),
			),
			'taxonomy'   => $taxonomy,
		);
		$terms = get_terms( $args );

		return ! is_wp_error( $terms ) && ! empty( $terms[0] ) ? $terms[0] : '';
	}

	protected function get_terms_sql( $taxonomy, $fields = 'name', $limit = '' ) {
		global $wpdb;

		$terms = wp_cache_get( md5( 'profolio_terms_cache' ) );
		if ( $terms === false ) {
			$terms = $wpdb->get_results( $wpdb->prepare(
				"SELECT " . sanitize_text_field( $fields ) . " FROM $wpdb->terms  LEFT JOIN $wpdb->term_taxonomy ON  $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id WHERE $wpdb->term_taxonomy.taxonomy = %s " . sanitize_text_field( $limit ),
				sanitize_text_field( $taxonomy )
			) );
			wp_cache_add( md5( 'profolio_terms_cache' ), $terms );
		}

		return $terms;
	}

	public function allow_users_upload() {

		$pap_capabilities = carbon_get_theme_option( 'pap_capabilities' );
		if ( ! empty( $pap_capabilities ) && is_array( $pap_capabilities ) ) {
			$pap_allow_upload = carbon_get_theme_option( 'pap_allow_uploads' );
			if ( ! empty( $pap_allow_upload ) ) {
				foreach ( $pap_capabilities as $pap_capability ) {
					$role = get_role( $pap_capability );
					$role->add_cap( 'upload_files' );
				}
			}
		}


	}

	protected function get_allow_roles() {
		global $wpdb;
		$roles = $wpdb->get_results( $wpdb->prepare(
			"SELECT option_value FROM wp_options WHERE option_name LIKE %s", "%pap_capabilities%"
		) );

		if ( ! empty( $roles ) && is_array( $roles ) ) {
			$roles = array_map( function ( $item ) {
				return $item->option_value;
			}, $roles );
		}

		$allow_roles = array();
		$user        = wp_get_current_user();
		if ( ! empty( $roles ) && ! empty( $user->roles ) ) {
			$allow_roles = array_intersect( $user->roles, $roles );
		}

		return $allow_roles;
	}


	/**
	 * Ajax test Geocoding API key
	 *
	 * @return string
	 */
	public function localseomap_geo_test() {

		if ( empty( $_POST['api_key'] ) ) {
			echo 'false';
		}

		$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=34.0462591,-118.2516532&sensor=false&key=' . $_POST['api_key'];
		$response = wp_remote_get( $url );
		$body = wp_remote_retrieve_body( $response );
	
 		echo $body;
		die();
	}
}
