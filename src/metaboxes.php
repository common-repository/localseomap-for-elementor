<?php
/**
 * User: localseomap
 * Date: 9.07.2019
 * @package LocalSeoMap/Metaboxes
 */

namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Metaboxes
 * @since  1.0.0
 */
class Metaboxes extends Admin {

	/**
	 * Init class.
	 */
	public function init() {

		if ( is_admin() ) {
			add_filter( 'rwmb_meta_boxes', array( $this, 'get_meta_boxes' ) );
		}

	}

	/**
	 * Create metaboxes for projects and media custom post type.
	 * @return array
	 */
	public function get_meta_boxes( $meta_boxes ) {

		$prefix = $this->get_metabox_prefix();

		$map     = null;
		$api_key = get_option( '_pap_google_maps_api_key' );
		if ( ! empty( $api_key ) ) {
			$map = array(
				'id'            => $prefix . 'map',
				'name'          => esc_html__( 'Location', 'localseomap-for-elementor' ),
				'type'          => 'map',

				// Address field ID
				'address_field' => $prefix . 'address',

				// Google API key
				'api_key'       => $api_key,
			);
		}

		$meta_boxes[] = array(
			'id'         => $prefix . 'project_details',
			'title'      => esc_html__( 'Project Details', 'localseomap-for-elementor' ),
			'post_types' => array( 'localseomap_projects' ),
			'context'    => 'normal',
			'priority'   => 'default',
			'autosave'   => 'false',
			'fields'     => array(
				array(
					'id'         => $prefix . 'uuid',
					'type'       => 'text',
					'name'       => esc_html__( 'Unique ID', 'localseomap-for-elementor' ),
					'attributes' => array(//						'disabled' => 'disabled',
					),
				),
				array(
					'id'          => $prefix . 'start_date',
					'type'        => 'datetime',
					'name'        => esc_html__( 'Start Date', 'localseomap-for-elementor' ),
					'save_format' => 'Y-m-d H:i:s',
				),
				array(
					'id'   => $prefix . 'timezone',
					'type' => 'text',
					'name' => esc_html__( 'Timezone', 'localseomap-for-elementor' ),
				),
				array(
					'id'           => $prefix . 'city',
					'type'         => 'text',
					'name'         => esc_html__( 'City', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'county',
					'type'         => 'text',
					'name'         => esc_html__( 'County', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'province',
					'type'         => 'text',
					'name'         => esc_html__( 'Province', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'country',
					'type'         => 'text',
					'name'         => esc_html__( 'Country', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'address',
					'type'         => 'text',
					'name'         => esc_html__( 'Address', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				//$map,
				array(
					'id'           => $prefix . 'latitude',
					'type'         => 'text',
					'name'         => esc_html__( 'Latitude', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'longitude',
					'type'         => 'text',
					'name'         => esc_html__( 'Longitude', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				/*array(
					'id'           => $prefix . 'name',
					'type'         => 'text',
					'name'         => esc_html__( 'Name','localseomap-for-elementor'),
					'force_delete' => 'false',
				),*/
				array(
					'id'           => $prefix . 'before_photo',
					'type'         => 'image',
					'name'         => esc_html__( 'Before photo', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'after_photo',
					'type'         => 'image',
					'name'         => esc_html__( 'After photo', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'   => $prefix . 'field_project_value',
					'type' => 'text',
					'name' => esc_html__( 'Project Value', 'localseomap-for-elementor' ),
				),
				array(
					'id'           => $prefix . 'field_project_status',
					'type'         => 'select',
					'options'      => array(
						''  => 'None',
						'1' => 'Completed',
						'2' => 'On Hold',
						'3' => 'Dropped',
					),
					'name'         => esc_html__( 'Project Status', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'      => $prefix . 'field_project_pro',
					'type'    => 'select',
					'options' => array(
						'1' => 'Project',
						'2' => 'Story',
					),
					'name'    => esc_html__( 'Pro Project', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_project_permit_number',
					'type' => 'text',
					'name' => esc_html__( 'Permit Number', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_project_customer_name',
					'type' => 'text',
					'name' => esc_html__( 'Customer Name', 'localseomap-for-elementor' ),
				),
				array(
					'id'           => $prefix . 'post_method',
					'type'         => 'select',
					'options'      => array(
						'profolio_api' => esc_html__( 'Profolio API', 'localseomap-for-elementor' ),
						'manual'       => esc_html__( 'Manual', 'localseomap-for-elementor' ),
					),
					'name'         => esc_html__( 'Post Method', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
			),
		);

		$meta_boxes[] = array(
			'id'            => trim( $prefix, '-' ),
			'title'         => esc_html__( 'Property details', 'localseomap-for-elementor' ),
			'post_types'    => array( 'localseomap_projects' ),
			'context'       => 'normal',
			'priority'      => 'default',
			'autosave'      => 'false',
			'default_state' => 'collapsed',
			'fields'        => array(


				array(
					'id'           => $prefix . 'field_real_estate_price',
					'type'         => 'text',
					'name'         => esc_html__( 'Price ($)', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'field_real_estate_sale_type',
					'type'         => 'select',
					'options'      => array(
						''  => 'none',
						'0' => 'for sale',
						'1' => 'for rent',
					),
					'name'         => esc_html__( 'Sale type', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'field_real_estate_status',
					'type'         => 'select',
					'options'      => array(
						''  => 'none',
						'0' => 'active',
						'1' => 'sold',
						'2' => 'inactive',
					),
					'name'         => esc_html__( 'Status', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'field_real_estate_mls_id',
					'type'         => 'text',
					'name'         => esc_html__( 'MLS ID', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'field_real_estate_home_size',
					'type'         => 'text',
					'name'         => esc_html__( 'Home size (sq ft)', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'field_real_estate_lot_size',
					'type'         => 'text',
					'name'         => esc_html__( 'Lot size (sq ft)', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'field_real_estate_bedrooms',
					'type'         => 'text',
					'name'         => esc_html__( 'Bedrooms', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'field_real_estate_bathrooms',
					'type'         => 'text',
					'name'         => esc_html__( 'Bathrooms', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'           => $prefix . 'field_real_estate_year_built',
					'type'         => 'text',
					'name'         => esc_html__( 'Year built', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
			),
		);

		$meta_boxes[] = array(
			'id'            => $prefix . 'testimonials',
			'title'         => esc_html__( 'Testimonials', 'localseomap-for-elementor' ),
			'post_types'    => array( 'localseomap_projects' ),
			'context'       => 'normal',
			'priority'      => 'default',
			'default_state' => 'collapsed',
			'autosave'      => 'false',
			'fields'        => array(
				array(
					'id'   => $prefix . 'field_story_testimonial_title',
					'type' => 'text',
					'name' => esc_html__( 'Testimonial title', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_story_testimonial_author',
					'type' => 'text',
					'name' => esc_html__( 'Testimonial author', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_story_testimonial_rating',
					'type' => 'number',
					'min'  => 0,
					'max'  => 5,
					'name' => esc_html__( 'Testimonial rating', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_story_testimonial_body',
					'type' => 'textarea',
					'name' => esc_html__( 'Testimonial body', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_story_testimonial_picture',
					'type' => 'textarea',
					'name' => esc_html__( 'Testimonial author picture url', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_story_testimonial_videos',
					'type' => 'textarea',
					'name' => esc_html__( 'Testimonial video', 'localseomap-for-elementor' ),
				),
				array(
					'id'           => $prefix . 'field_story_testimonial_cover',
					'type'         => 'image',
					'name'         => esc_html__( 'Testimonial video cover', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
			),
		);

		/* For media */
		$meta_boxes[] = array(
			'id'         => $prefix . 'media_details',
			'title'      => esc_html__( 'Media details', 'localseomap-for-elementor' ),
			'post_types' => array( 'localseomap_media' ),
			'context'    => 'normal',
			'priority'   => 'default',
			'autosave'   => 'false',
			'fields'     => array(
				array(
					'id'         => $prefix . 'project_uuid',
					'type'       => 'text',
					'name'       => esc_html__( 'Uuid of the parent project', 'localseomap-for-elementor' ),
					'attributes' => array(//'disabled' => 'disabled',
					),
				),
				array(
					'id'         => $prefix . 'uuid',
					'type'       => 'text',
					'name'       => esc_html__( 'Unique ID', 'localseomap-for-elementor' ),
					'attributes' => array(//'disabled' => 'disabled',
					),
				),
				array(
					'id'           => $prefix . 'state',
					'type'         => 'select',
					'options'      => array(
						'approved' => 'Approved',
						'declined' => 'Declined',
						'pending'  => 'Pending',
					),
					'name'         => esc_html__( 'State', 'localseomap-for-elementor' ),
					'force_delete' => 'false',
				),
				array(
					'id'          => $prefix . 'created',
					'type'        => 'datetime',
					'name'        => esc_html__( 'Created', 'localseomap-for-elementor' ),
					'save_format' => 'Y-m-d H:i:s',
				),
				array(
					'id'   => $prefix . 'vuuid',
					'type' => 'text',
					'name' => esc_html__( 'Vuuid', 'localseomap-for-elementor' ),
				),
				array(
					'id'      => $prefix . 'pin_pf_state',
					'type'    => 'select',
					'options' => array(
						''       => 'none',
						'before' => 'Before',
						'after'  => 'After',
						'during' => 'During',
					),
					'name'    => esc_html__( 'Project Flow State', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'nid',
					'type' => 'text',
					'name' => esc_html__( 'nid', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'comment',
					'type' => 'text',
					'name' => esc_html__( 'Comment', 'localseomap-for-elementor' ),
				),

				array(
					'id'   => $prefix . 'field_video',
					'type' => 'textarea',
					'name' => esc_html__( 'Video', 'localseomap-for-elementor' ),
				),

				array(
					'id'          => $prefix . 'media_create_datetime',
					'type'        => 'datetime',
					'name'        => esc_html__( 'Create Datetime', 'localseomap-for-elementor' ),
					'save_format' => 'Y-m-d H:i:s',
				),
				array(
					'id'   => $prefix . 'media_lat',
					'type' => 'text',
					'name' => esc_html__( 'Lat', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'media_long',
					'type' => 'text',
					'name' => esc_html__( 'Long', 'localseomap-for-elementor' ),
				),

				array(
					'id'      => $prefix . 'field_ps_folder_ref',
					'name'    => esc_html__( 'Folder', 'localseomap-for-elementor' ),
					'type'    => 'autocomplete',
					'options' => localseomap_get_projects_list(),
				),
				array(
					'id'   => $prefix . 'field_ps_url',
					'type' => 'text',
					'name' => esc_html__( 'URL', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_ps_pdf_url',
					'type' => 'text',
					'name' => esc_html__( 'PDF', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_ps_author_name',
					'type' => 'text',
					'name' => esc_html__( 'Author Name', 'localseomap-for-elementor' ),
				),
				array(
					'id'   => $prefix . 'field_ps_author_page_url',
					'type' => 'text',
					'name' => esc_html__( 'Author Page URL', 'localseomap-for-elementor' ),
				),
				array(
					'id'      => $prefix . 'field_ps_file_type',
					'name'    => esc_html__( 'File type', 'localseomap-for-elementor' ),
					'type'    => 'select',
					'options' => array(
						'image' => 'Image',
						'video' => 'Video',
					),
				),
				array(
					'id'   => $prefix . 'field_ps_weight',
					'name' => esc_html__( 'Weight', 'localseomap-for-elementor' ),
					'type' => 'number',
					'min'  => 0,
					'step' => 1,
				),
				array(
					'id'   => $prefix . 'field_ps_video_link',
					'type' => 'text',
					'name' => esc_html__( 'Video Link', 'localseomap-for-elementor' ),
				),
			),
		);

		$meta_boxes[] = array(
			'title'      => 'The map setting',
			'post_types' => array( 'page' ),
			'show'       => array(
				'template' => array( 'profolio-project-template.php' ),
			),
			'fields'     => array(
				array(
					'name' => esc_html__( 'Base location', 'localseomap-for-elementor' ),
					'id'   => 'location',
					'type' => 'text',
				),
				array(
					'name' => esc_html__( 'Fullheight', 'localseomap-for-elementor' ),
					'id'   => 'fullheight',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Height', 'localseomap-for-elementor' ),
					'id'   => 'height',
					'type' => 'text',
				),
				array(
					'name'    => esc_html__( 'Get coordinates from', 'localseomap-for-elementor' ),
					'id'      => 'number_projects',
					'options' => array(
						'all_pages'    => esc_html__( 'Display on map projects from all pages', 'localseomap-for-elementor' ),
						'current_page' => esc_html__( 'Display on map same projects as on current page', 'localseomap-for-elementor' ),
					),
					'type'    => 'select',
				),
				array(
					'name' => esc_html__( 'Marker', 'localseomap-for-elementor' ),
					'id'   => 'marker',
					'type' => 'image',
				),
				array(
					'name' => 'zoom',
					'id'   => 'zoom',
					'type' => 'text',
				),
				array(
					'name' => esc_html__( 'Zoom to fit all markers', 'localseomap-for-elementor' ),
					'id'   => 'zoom_fit',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Enable scrollwheel', 'localseomap-for-elementor' ),
					'id'   => 'scrollwheel',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Enable tilted at 45° imagery', 'localseomap-for-elementor' ),
					'id'   => 'imagery_45',
					'type' => 'checkbox',
				),
				array(
					'name'    => esc_html__( 'Select Map Types', 'localseomap-for-elementor' ),
					'id'      => 'select_map_types',
					'options' => array(
						'roadmap'   => esc_html__( 'Roadmap ', 'localseomap-for-elementor' ),
						'satellite' => esc_html__( 'Satellite', 'localseomap-for-elementor' ),
						'hybrid'    => esc_html__( 'Hybrid', 'localseomap-for-elementor' ),
						'terrain'   => esc_html__( 'Terrain', 'localseomap-for-elementor' ),
					),
					'type'    => 'select',
				),
				array(
					'name' => esc_html__( 'Enable all UI', 'localseomap-for-elementor' ),
					'id'   => 'enable_default_ui',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Show zoom control', 'localseomap-for-elementor' ),
					'id'   => 'zoom_control',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Show map type control', 'localseomap-for-elementor' ),
					'id'   => 'map_type_control',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Show scale control', 'localseomap-for-elementor' ),
					'id'   => 'scale_control',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Show fullscreen control', 'localseomap-for-elementor' ),
					'id'   => 'fullscreen_control',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Show rotate control', 'localseomap-for-elementor' ),
					'id'   => 'rotate_control',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Hide title', 'localseomap-for-elementor' ),
					'id'   => 'infowindow_hide_title',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Background infowindow', 'localseomap-for-elementor' ),
					'id'   => 'infowindow_color_bg',
					'type' => 'color',
				),
				array(
					'name' => esc_html__( 'Color title', 'localseomap-for-elementor' ),
					'id'   => 'infowindow_color_title',
					'type' => 'color',
				),
				array(
					'name' => esc_html__( 'Color location', 'localseomap-for-elementor' ),
					'id'   => 'infowindow_color_location',
					'type' => 'color',
				),
			),
		);

		$meta_boxes[] = array(
			'title'      => 'The project list settings',
			'post_types' => array( 'page' ),
			'show'       => array(
				'template' => array( 'profolio-project-template.php' ),
			),
			'fields'     => array(
				array(
					'name' => esc_html__( 'Posts per page', 'localseomap-for-elementor' ),
					'id'   => 'posts_per_page',
					'type' => 'text',
				),
				array(
					'name' => esc_html__( 'Show pagination (only Pro)', 'localseomap-for-elementor' ),
					'id'   => 'show_pagination',
					'type' => 'checkbox',
				),
				array(
					'name'    => esc_html__( 'Number of columns', 'localseomap-for-elementor' ),
					'id'      => 'number_columns',
					'options' => array(
						'profolio-col-md-12' => esc_html__( 'One column', 'localseomap-for-elementor' ),
						'profolio-col-md-6'  => esc_html__( 'Two columns', 'localseomap-for-elementor' ),
						'profolio-col-md-4'  => esc_html__( 'Three columns', 'localseomap-for-elementor' ),
						'profolio-col-md-3'  => esc_html__( 'Four columns', 'localseomap-for-elementor' ),
					),
					'type'    => 'select',
				),
				array(
					'name'    => esc_html__( 'Style', 'localseomap-for-elementor' ),
					'id'      => 'style',
					'options' => array(
						''             => esc_html__( 'Default', 'localseomap-for-elementor' ),
						'project_list' => esc_html__( 'Project list', 'localseomap-for-elementor' ),
					),
					'type'    => 'select',
				),
				array(
					'name'    => esc_html__( 'Order By', 'localseomap-for-elementor' ),
					'id'      => 'orderby',
					'options' => array(
						'start_date' => esc_html__( 'Start Date', 'localseomap-for-elementor' ),
						'unique_id'  => esc_html__( 'Unique ID', 'localseomap-for-elementor' ),
						'date'       => esc_html__( 'Date', 'localseomap-for-elementor' ),
						'ID'         => esc_html__( 'ID', 'localseomap-for-elementor' ),
					),
					'type'    => 'select',
				),
				array(
					'name' => esc_html__( 'Hide Title?', 'localseomap-for-elementor' ),
					'id'   => 'hide_title',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Hide Location?', 'localseomap-for-elementor' ),
					'id'   => 'hide_location',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Hide industry?', 'localseomap-for-elementor' ),
					'id'   => 'hide_industry',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Category shape background', 'localseomap-for-elementor' ),
					'id'   => 'category_background_shape',
					'type' => 'text',
				),
				array(
					'name' => esc_html__( 'Category shape color', 'localseomap-for-elementor' ),
					'id'   => 'category_color_shape',
					'type' => 'color',
				),
				array(
					'name' => esc_html__( 'Title color', 'localseomap-for-elementor' ),
					'id'   => 'title_color',
					'type' => 'color',
				),
				array(
					'name' => esc_html__( 'Remove background?', 'localseomap-for-elementor' ),
					'id'   => 'remove_bg',
					'type' => 'checkbox',
				),
				array(
					'name'    => esc_html__( 'Image size', 'localseomap-for-elementor' ),
					'id'      => 'image_size',
					'options' => localseomap_get_thumbnail_sizes(),
					'type'    => 'select',
				),
				array(
					'name' => esc_html__( 'Pro project', 'localseomap-for-elementor' ),
					'id'   => 'pro_project',
					'type' => 'checkbox',
				),
				array(
					'name' => esc_html__( 'Projects tags instead industry', 'localseomap-for-elementor' ),
					'id'   => 'tags_instead_industry',
					'type' => 'checkbox',
				),
			)
		);

		return $meta_boxes;
	}
}
