<?php
/**
 * User: localseomap
 * Date: 25.10.2019
 * @package LocalSeoMap/Options
 */


namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;


class Options extends Admin {

	/**
	 * Leads constructor.
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Init Options.
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {

		/* Add the settings page to the admin menu */
		add_action( 'carbon_fields_register_fields', array( &$this, 'page_options' ) );
		add_action( 'carbon_fields_theme_options_container_saved', array( &$this, 'rewrite_rules' ) );

	}

	private function get_terms_hierarchical( $terms = array(), $parent_id = 0, $level = 1 ) {
		$new_terms = array();
		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( $term->parent == $parent_id && ! isset( $new_terms[ ' ' . $term->term_id ] ) ) {
					$new_terms[ ' ' . $term->term_id ] = str_repeat( ' — ', $level ) . $term->name;
					$new_terms                         = array_merge( $new_terms, $this->get_terms_hierarchical( $terms, $term->term_id, $level + 1 ) );
				}
			}
		}

		return $new_terms;
	}


	/**
	 * Get all industry for dropdown
	 *
	 * @param array $terms
	 *
	 * @return string
	 * @since  1.0.0
	 * @access private
	 */
	private function get_industry_options( $terms = array() ) {

		if ( empty( $_GET['page'] ) ) {
			return array();
		}
		if ( $_GET['page'] != 'crb_carbon_fields_container_localseomap_for_elementor.php' ) {
			return array();
		}

		if ( empty( $terms ) ) {
			$terms = $this->get_terms_sql( 'localseomap_industry', '*' );
		}

		$branch = array();
		foreach ( $terms as $term ) {
			if ( $term->parent === '0' && ! isset( $branch[ ' ' . $term->term_id ] ) ) {
				$branch[ ' ' . $term->term_id ] = $term->name;
			}
			$branch = array_merge( $branch, $this->get_terms_hierarchical( $terms, $term->term_id ) );
		}

		$keys = array_keys( $branch );

		$values     = array_values( $branch );
		$stringKeys = array_map( 'trim', $keys );

		$branch = array_combine( $stringKeys, $values );

		return $branch;
	}

	/**
	 * Add new menu item to the admin navigation and some fields.
	 * @since  1.0.0
	 * @access public
	 */

	public function page_options() {

		$framework = Container::make( 'theme_options', 'LocalSEOMap for Elementor' );
		$framework->set_icon( plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/img/icon-m.png' );

		$domain = str_replace( 'http://', '', home_url() );
		$domain = str_replace( 'https://', '', $domain );
		$domain = str_replace( 'www.', '', $domain );
		$ip     = gethostbyname( trim( $domain, '/' ) );

		$api_notice                = '<b style="margin:0;color:red;">' . __( 'Notice: ', 'localseomap-for-elementor' ) . '</b>';
		$api_notification_ip_text  = wp_sprintf( __( 'When restricting your Geocoding API key, make sure to accept requests from your serve IP address: <b> %s</b>', 'localseomap-for-elementor' ), $ip );
		$api_notification_dom_text = wp_sprintf( __( 'When restricting your Maps JavaScript API Key, make sure to accept requests from your HTTP referrer:  <b> %s/*</b>', 'localseomap-for-elementor' ), home_url() );


		$framework->add_tab( esc_html__( 'General', 'localseomap-for-elementor' ), array(

			Field::make( 'html', 'pap_knowledge_general' )
			     ->set_html( '<div style="text-align: right"><a target="_blank" href="https://www.localseomap.com/knowledge-base/general/">Get help</a></div>' ),

			/*
			 *
			 * */

			Field::make( 'text', 'pap_google_maps_api_key', esc_html__( 'Maps JavaScript API', 'localseomap-for-elementor' ) )
			     ->set_help_text(
				     $api_notice .
				     $api_notification_dom_text .
				     localseomap_test_api_button( __( 'Test JavaScript API', 'localseomap-for-elementor' ), 'profolio_js_api_test' )
			     ),

			Field::make( 'text', 'pap_google_maps_api_server_key', esc_html__( 'Geocoding API', 'localseomap-for-elementor' ) )
			     ->set_help_text(
				     $api_notice .
				     $api_notification_ip_text .
				     localseomap_test_api_button( __( 'Test Geocoding API', 'localseomap-for-elementor' ), 'profolio_js_geo_test' )
			     ),

			Field::make( 'select', 'pap_type_address', esc_html__( 'Type Address', 'localseomap-for-elementor' ) )->set_options( array(
				'exact'   => esc_html__( 'Exact', 'localseomap-for-elementor' ),
				'general' => esc_html__( 'Approximate', 'localseomap-for-elementor' ),
			) ),


			Field::make( 'text', 'pap_app_max_radius', esc_html__( 'Max. radius for Approximate (meter)', 'localseomap-for-elementor' ) )
			     ->set_conditional_logic( array(
				     array(
					     'field' => 'pap_type_address',
					     'value' => 'general',
				     )
			     ) )
			     ->set_default_value( '1609' ),


			Field::make( 'text', 'pap_map_zoom', esc_html__( 'The map zoom (0-20)', 'localseomap-for-elementor' ) )
			     ->set_attribute( 'type', 'number' )


		) );

		$framework->add_tab( esc_html__( 'Category', 'localseomap-for-elementor' ), array(

			Field::make( 'html', 'pap_knowledge_category' )
			     ->set_html( '<div style="text-align: right"><a target="_blank" href="https://www.localseomap.com/knowledge-base/category/">Get help</a></div>' ),

			Field::make( 'multiselect', 'pap_allowed_industry', esc_html__( 'Allowed industry', 'localseomap-for-elementor' ) )
			     ->set_options( $this->get_industry_options() ),
			Field::make( 'select', 'pap_category_template', esc_html__( 'Category template', 'localseomap-for-elementor' ) )->set_options( array(
				''       => esc_html__( 'Default', 'localseomap-for-elementor' ),
				'modern' => esc_html__( 'Modern', 'localseomap-for-elementor' ),
			) ),
			Field::make( 'select', 'pap_column_size', esc_html__( 'Column Size', 'localseomap-for-elementor' ) )->set_options( array(
				'6' => esc_html__( '2 Column', 'localseomap-for-elementor' ),
				'4' => esc_html__( '3 Column', 'localseomap-for-elementor' ),
				'3' => esc_html__( '4 Column', 'localseomap-for-elementor' ),
			) ),
			Field::make( 'checkbox', 'pap_remove_tag_buttons', esc_html__( 'Remove tag buttons from list', 'localseomap-for-elementor' ) )
			     ->set_option_value( 'yes' )
			     ->set_default_value( 'yes' ),
			Field::make( 'color', 'pap_tag_buttons_color', esc_html__( 'Tag buttons color', 'localseomap-for-elementor' ) )
			     ->set_conditional_logic( array(
				     array(
					     'field' => 'pap_remove_tag_buttons',
					     'value' => false,
				     )
			     ) ),

		) );


		$roles = array();
		if ( is_admin() ) {
			global $wp_roles;
			if ( ! empty( $wp_roles->roles ) && is_array( $wp_roles->roles ) ) {

				foreach ( $wp_roles->roles as $role => $details ) {
					$roles[ esc_attr( $role ) ] = translate_user_role( $details['name'] );
				}
			}
		}

		$framework->add_tab( esc_html__( 'Project Details', 'localseomap-for-elementor' ), array(

			Field::make( 'html', 'pap_knowledge_project' )
			     ->set_html( '<div style="text-align: right"><a target="_blank" href="https://www.localseomap.com/knowledge-base/project-details/">Get help</a></div>' ),

			Field::make( 'select', 'pap_single_project_template', esc_html__( 'Project template', 'localseomap-for-elementor' ) )->set_options( array(
				''       => esc_html__( 'Default', 'localseomap-for-elementor' ),
				'modern' => esc_html__( 'Modern', 'localseomap-for-elementor' ),
			) ),
			Field::make( 'text', 'pap_title_font_size', esc_html__( 'Title Font Size', 'localseomap-for-elementor' ) )
		,
			Field::make( 'color', 'pap_title_color', esc_html__( 'Title Color', 'localseomap-for-elementor' ) ),
			Field::make( 'color', 'pap_seperator_color', esc_html__( 'Seperator Color', 'localseomap-for-elementor' ) ),
			Field::make( 'color', 'pap_icon_bg_color', esc_html__( 'Icon Bg Color', 'localseomap-for-elementor' ) ),
			Field::make( 'color', 'pap_map_border_color', esc_html__( 'Map Border Color', 'localseomap-for-elementor' ) ),
			Field::make( 'text', 'pap_gallery_title_font_size', esc_html__( 'Gallery Title Font Size', 'localseomap-for-elementor' ) )
		,
			Field::make( 'color', 'pap_gallery_title_color', esc_html__( 'Gallery Title Color', 'localseomap-for-elementor' ) ),
			Field::make( 'color', 'pap_button_bg_color', esc_html__( 'Button Color', 'localseomap-for-elementor' ) ),

			Field::make( 'checkbox', 'pap_show_preview', esc_html__( 'Show video preview instead of the button', 'localseomap-for-elementor' ) )
			     ->set_option_value( 'yes' ),
			Field::make( 'checkbox', 'pap_show_share_links', esc_html__( 'Show share links', 'localseomap-for-elementor' ) )
			     ->set_option_value( 'yes' ),

			Field::make( 'checkbox', 'pap_enable_gallery', esc_html__( 'Enable galley list view', 'localseomap-for-elementor' ) )
			     ->set_option_value( 'yes' ),

			Field::make( 'select', 'pap_columns', esc_html__( 'Columns', 'localseomap-for-elementor' ) )->set_options( array(
				'12' => esc_html__( '1', 'localseomap-for-elementor' ),
				'6'  => esc_html__( '2', 'localseomap-for-elementor' ),
				'4'  => esc_html__( '3', 'localseomap-for-elementor' ),
				'3'  => esc_html__( '4', 'localseomap-for-elementor' ),
				'2'  => esc_html__( '6', 'localseomap-for-elementor' ),
			) )->set_conditional_logic( array(
				array(
					'field' => 'pap_enable_gallery',
					'value' => true,
				)
			) ),
			Field::make( 'select', 'pap_order_by', esc_html__( 'Order By', 'localseomap-for-elementor' ) )->set_options( array(
				'default' => esc_html__( 'Default', 'localseomap-for-elementor' ),
				'random'  => esc_html__( 'Random', 'localseomap-for-elementor' ),
			) )->set_conditional_logic( array(
				array(
					'field' => 'pap_enable_gallery',
					'value' => true,
				)
			) ),

			Field::make( 'select', 'pap_image_size', esc_html__( 'Image Size', 'localseomap-for-elementor' ) )->set_options( localseomap_get_thumbnail_sizes( 'left' ) ),

			Field::make( 'select', 'pap_type_location', esc_html__( 'Use location from:', 'localseomap-for-elementor' ) )->set_options( array(
				'project' => esc_html__( 'Project', 'localseomap-for-elementor' ),
				'media'   => esc_html__( 'Media', 'localseomap-for-elementor' ),
			) ),

			Field::make( 'text', 'pap_top_margin', esc_html__( 'Top margin for pages', 'localseomap-for-elementor' ) )
			     ->set_attribute( 'type', 'number' ),
			Field::make( 'text', 'pap_bottom_margin', esc_html__( 'Bottom margin for pages', 'localseomap-for-elementor' ) )
			     ->set_attribute( 'type', 'number' ),

			Field::make( 'checkbox', 'pap_hide_project_title', esc_html__( 'Hide project title', 'localseomap-for-elementor' ) ),

		) );

		if ( localseomap()->is_plan( 'business' ) ) {
			$framework->add_tab( esc_html__( 'CTA', 'localseomap-for-elementor' ), array(

				Field::make( 'separator', 'crb_btn_separator', esc_html__( 'Call to Action Button', 'localseomap-for-elementor' ) ),
				Field::make( 'checkbox', 'pap_show_leads_button', esc_html__( 'Show Call to Action Button', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' ),
				Field::make( 'text', 'pap_leads_button_label', esc_html__( 'Button label text', 'localseomap-for-elementor' ) )
				     ->set_default_value( esc_html__( 'Request Estimate', 'localseomap-for-elementor' ) ),
				Field::make( 'color', 'pap_color_btn', esc_html__( 'Button text color', 'localseomap-for-elementor' ) ),
				Field::make( 'color', 'pap_bg_color_btn', esc_html__( 'Button bg color', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_font_size_btn', esc_html__( 'Button font size', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_redirect_url_btn', esc_html__( 'Button redirect URL', 'localseomap-for-elementor' ) ),

				Field::make( 'separator', 'leads180_separator', esc_html__( 'Leads180 Integration', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'field_external_services_id', esc_html__( 'External Services ID', 'localseomap-for-elementor' ) ),

				Field::make( 'separator', 'crb_separator', esc_html__( 'Popup Form', 'localseomap-for-elementor' ) ),

				Field::make( 'checkbox', 'pap_override_btn_redirect', esc_html__( 'Override Button redirect URL?', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_title_above_form', esc_html__( 'Title above form', 'localseomap-for-elementor' ) ),
				Field::make( 'textarea', 'pap_subtitle_above_form', esc_html__( 'Subtitle above form', 'localseomap-for-elementor' ) ),

				Field::make( 'text', 'pap_change_form_text_btn', esc_html__( 'Submit button label', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_fn_field', esc_html__( 'Change Full Name Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_street_field', esc_html__( 'Change Street Address Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_city_field', esc_html__( 'Change City Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_state_field', esc_html__( 'Change State Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_zip_field', esc_html__( 'Change Zip Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_phone_field', esc_html__( 'Change Phone Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_email_field', esc_html__( 'Change Email Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_service_field', esc_html__( 'Change Service Type Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_detail_1_field', esc_html__( 'Change Detail First Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_detail_2_field', esc_html__( 'Change Detail Second Field', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_change_detail_3_field', esc_html__( 'Change Detail Third Field', 'localseomap-for-elementor' ) ),

				Field::make( 'checkbox', 'pap_show_street_field', esc_html__( 'Show Street Address Field', 'localseomap-for-elementor' ) ),
				Field::make( 'checkbox', 'pap_show_city_field', esc_html__( 'Show City Field', 'localseomap-for-elementor' ) ),
				Field::make( 'checkbox', 'pap_show_state_field', esc_html__( 'Show State Field', 'localseomap-for-elementor' ) ),
				Field::make( 'checkbox', 'pap_show_service_field', esc_html__( 'Show Service Type Field', 'localseomap-for-elementor' ) ),
				Field::make( 'checkbox', 'pap_show_detail_1_field', esc_html__( 'Show Detail First Field', 'localseomap-for-elementor' ) ),
				Field::make( 'checkbox', 'pap_show_detail_2_field', esc_html__( 'Show Detail Second Field', 'localseomap-for-elementor' ) ),
				Field::make( 'checkbox', 'pap_show_detail_3_field', esc_html__( 'Show Detail Third Field', 'localseomap-for-elementor' ) ),

			) );
		}


		if ( localseomap()->is_plan( 'professional' ) ) {
			$framework->add_tab( esc_html__( 'Cloud Connect', 'localseomap-for-elementor' ), array(
				Field::make( 'html', 'pap_knowledge_cloud' )
				     ->set_html( '<div style="text-align: right"><a target="_blank" href="https://www.localseomap.com/knowledge-base/cloud-connect/">Get help</a></div>' ),
				Field::make( 'text', 'pap_username', esc_html__( 'Username', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_password', esc_html__( 'Password', 'localseomap-for-elementor' ) )
				     ->set_attribute( 'type', 'password' ),
				Field::make( 'text', 'pap_client_id', esc_html__( 'Client ID', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_client_secret', esc_html__( 'Client secret', 'localseomap-for-elementor' ) )
				     ->set_attribute( 'type', 'password' ),
				Field::make( 'text', 'pap_sync_key', esc_html__( 'Your sync key', 'localseomap-for-elementor' ) ),
				Field::make( 'checkbox', 'pap_delete_projects', esc_html__( 'Allow deleting projects from API?', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' )
				     ->set_default_value( 'yes' ),
				Field::make( 'checkbox', 'pap_delete_media', esc_html__( 'Allow deleting media from API?', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' )
				     ->set_default_value( 'yes' ),
				Field::make( 'checkbox', 'pap_sort_by_media_create_datetime', esc_html__( 'Sort media by meta value ( media_create_datetime )', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' )
				     ->set_default_value( 'yes' ),
			) );
		}


		if ( localseomap()->is_plan( 'business' ) ) {

			$framework->add_tab( esc_html__( 'Access', 'localseomap-for-elementor' ), array(
				Field::make( 'html', 'pap_knowledge_access' )
				     ->set_html( '<div style="text-align: right"><a target="_blank" href="https://www.localseomap.com/knowledge-base/access/">Get help</a></div>' ),

				Field::make( 'multiselect', 'pap_capabilities', esc_html__( 'Roles of users who will be allowed to add projects', 'localseomap-for-elementor' ) )
				     ->set_options( $roles ),
				Field::make( 'checkbox', 'pap_allow_uploads', esc_html__( 'Allow users with these roles to upload images', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' )
				     ->set_default_value( 'yes' ),

				Field::make( 'select', 'pap_input_form_template', esc_html__( 'Select template for the input form', 'localseomap-for-elementor' ) )->set_options( array(
					'header_footer' => esc_html__( 'The header and the footer only', 'localseomap-for-elementor' ),
					'page'          => esc_html__( 'Page template of the theme (if it exists)', 'localseomap-for-elementor' ),
				) ),
			) );


		}

		if ( localseomap()->is_plan( 'starter' ) && is_admin() ) {
			ob_start(); ?>
            <div class="profolio_import_form import_projects">
                <div>
                    <a href="<?php echo home_url( 'wp-admin/admin.php?page=crb_carbon_fields_container_localseomap_for_elementor.php&profolio_run_import=true' ); ?>"
                       class="button button-primary button-large profolio_import_projects">
						<?php esc_html_e( 'Import all projects', 'localseomap-for-elementor' ); ?>
                    </a>

                    <label for="" style="padding: 10px 0; display: block;">
                        <input type="checkbox" value="1" id="skip_existing_projects">
                        <span><?php esc_html_e( 'Skip existing projects', 'localseomap-for-elementor' ); ?></span>
                    </label>
                    <div class="profolio_import_wrap_count" style="display:none;margin-top: 10px;font-size: 16px;">
						<?php esc_html_e( 'Number of imported projects', 'localseomap-for-elementor' ); ?>
                        <span class="current_imported">0</span> /
                        <span class="number_projects">0</span>
                    </div>
                    <img class="profolio_import_loader" style="display: none" width="30"
                         src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>/data/assets/img/ajax-loader.gif"
                         alt="">
                </div>
                <hr>
                <br>
                <div>
                    <a href="#" class="button button-primary button-large profolio_import_industry">
						<?php esc_html_e( 'Import all industry', 'localseomap-for-elementor' ); ?>
                    </a>
                    <img class="profolio_import_loader" style="display: none" width="30"
                         src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>/data/assets/img/ajax-loader.gif"
                         alt="">
                    <div class="profolio_import_wrap_count" style="display:none;margin-top: 10px;font-size: 16px;">
                        <span class="current_imported" style="color: green">0</span>
                    </div>
                </div>
            </div>

			<?php
			$html = ob_get_clean();
			$framework->add_tab( esc_html__( 'Import', 'localseomap-for-elementor' ), array(
				Field::make( 'html', 'pap_knowledge_import' )
				     ->set_html( '<div style="text-align: right"><a target="_blank" href="https://www.localseomap.com/knowledge-base/import/">Get help</a></div>' ),

				Field::make( 'html', 'pap_logs_import_button', esc_html__( 'Response API', 'localseomap-for-elementor' ) )
				     ->set_html( $html ),
			) );
		}


		if ( localseomap()->is_plan( 'business' ) ) {

			$framework->add_tab( esc_html__( 'SEO', 'localseomap-for-elementor' ), array(
				Field::make( 'html', 'pap_knowledge_seo' )
				     ->set_html( '<div style="text-align: right"><a target="_blank" href="https://www.localseomap.com/knowledge-base/seo/">Get help</a></div>' ),
				Field::make( 'text', 'rewrite_project_slug', esc_html__( 'Slug for project', 'localseomap-for-elementor' ) )
				     ->set_default_value( 'lsm_projects' )
				     ->set_help_text( esc_html__( 'It will be used in the url. For example: http://your-website.com/localseomap_projects/slug-of-project/', 'localseomap-for-elementor' ) ),

				Field::make( 'text', 'rewrite_media_slug', esc_html__( 'Slug for media', 'localseomap-for-elementor' ) )
				     ->set_default_value( 'lsm_media' )
				     ->set_help_text( esc_html__( 'It will be used in the url. For example: http://your-website.com/localseomap_media/slug-of-media/', 'localseomap-for-elementor' ) ),


				Field::make( 'multiselect', 'pap_select_area_levels', esc_html__( 'Area levels for area project tags:', 'localseomap-for-elementor' ) )
				     ->set_options(
					     array(
						     'city'     => esc_html__( 'City', 'localseomap-for-elementor' ),
						     'county'   => esc_html__( 'County', 'localseomap-for-elementor' ),
						     'province' => esc_html__( 'Province', 'localseomap-for-elementor' ),
						     'country'  => esc_html__( 'Country', 'localseomap-for-elementor' ),
					     )
				     )
				     ->set_default_value( array( 'city', 'county' ) ),

				Field::make( 'multiselect', 'pap_select_city_param', esc_html__( 'Select priority param for city:', 'localseomap-for-elementor' ) )
				     ->set_options(
					     array(
						     'neighborhood'        => esc_html__( 'Neighborhood', 'localseomap-for-elementor' ),
						     'locality'            => esc_html__( 'Locality', 'localseomap-for-elementor' ),
						     'sublocality_level_1' => esc_html__( 'Sublocality level 1', 'localseomap-for-elementor' ),
						     'sublocality_level_2' => esc_html__( 'Sublocality level 2', 'localseomap-for-elementor' ),
						     'sublocality_level_3' => esc_html__( 'Sublocality level 3', 'localseomap-for-elementor' ),
					     )
				     )
				     ->set_default_value( array( 'city', 'province' ) ),

				Field::make( 'checkbox', 'pap_disable_parent_area_tags', esc_html__( 'Disable parent area tag', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' )
				     ->set_default_value( 'yes' ),

				Field::make( 'checkbox', 'pap_use_scheme_aggregate_rating', esc_html__( 'Add aggregate rating ', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' )
				     ->set_default_value( 'yes' ),

				Field::make( 'checkbox', 'pap_use_scheme', esc_html__( 'Add schema (microdata) about your company', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' ),

				Field::make( 'select', 'pap_schema_type', esc_html__( 'Type:', 'localseomap-for-elementor' ) )
				     ->set_options(
					     array(
						     'Electrician'       => esc_html__( 'Electrician', 'localseomap-for-elementor' ),
						     'GeneralContractor' => esc_html__( 'General Contractor', 'localseomap-for-elementor' ),
						     'HVACBusiness'      => esc_html__( 'HVAC Business', 'localseomap-for-elementor' ),
						     'HousePainter'      => esc_html__( 'House Painter', 'localseomap-for-elementor' ),
						     'Locksmith'         => esc_html__( 'Locksmith', 'localseomap-for-elementor' ),
						     'MovingCompany'     => esc_html__( 'Moving Company', 'localseomap-for-elementor' ),
						     'Plumber'           => esc_html__( 'Plumber', 'localseomap-for-elementor' ),
						     'RoofingContractor' => esc_html__( 'RoofingContractor', 'localseomap-for-elementor' ),
						     'Manual'            => esc_html__( 'Manual', 'localseomap-for-elementor' ),
					     )
				     )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) )->set_required( true ),

				Field::make( 'text', 'pap_schema_type_custom', esc_html__( 'Your custom type', 'localseomap-for-elementor' ) )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_schema_type',
						     'value' => 'Manual',
					     )
				     ) ),

				Field::make( 'text', 'pap_schema_company_name', esc_html__( 'Name', 'localseomap-for-elementor' ) )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) )->set_required( true ),

				Field::make( 'image', 'pap_schema_company_logo', esc_html__( 'Logo', 'localseomap-for-elementor' ) )
				     ->set_value_type( 'url' )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) ),
				Field::make( 'textarea', 'pap_schema_description', esc_html__( 'Description', 'localseomap-for-elementor' ) )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) ),
				Field::make( 'text', 'pap_schema_address', esc_html__( 'Address', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_schema_city', esc_html__( 'City', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_schema_state_region', esc_html__( 'State/Region', 'localseomap-for-elementor' ) )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) ),
				Field::make( 'text', 'pap_schema_zip', esc_html__( 'Zip/Postal Code', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_schema_country', esc_html__( 'Country', 'localseomap-for-elementor' ) ),
				Field::make( 'text', 'pap_schema_latitude', esc_html__( 'Latitude', 'localseomap-for-elementor' ) )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) ),
				Field::make( 'text', 'pap_schema_longitude', esc_html__( 'Longitude', 'localseomap-for-elementor' ) )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) ),

				Field::make( 'select', 'pap_schema_price_range', esc_html__( 'Price range', 'localseomap-for-elementor' ) )
				     ->set_options(
					     array(
						     ''      => esc_html__( 'Choose', 'localseomap-for-elementor' ),
						     '$"'    => esc_html__( 'Inexpensive, usually $10 and under', 'localseomap-for-elementor' ),
						     '$$"'   => esc_html__( 'Moderately expensive, usually between $10-$25', 'localseomap-for-elementor' ),
						     '$$$"'  => esc_html__( 'Expensive, usually between $25-$45', 'localseomap-for-elementor' ),
						     '$$$$"' => esc_html__( 'Very Expensive, usually $50 and up', 'localseomap-for-elementor' ),
					     )
				     )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) ),
				Field::make( 'text', 'pap_schema_telephone', esc_html__( 'Telephone', 'localseomap-for-elementor' ) )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) )->set_required( true ),
				Field::make( 'image', 'pap_schema_image', esc_html__( 'Image:', 'localseomap-for-elementor' ) )
				     ->set_value_type( 'url' )
				     ->set_required( true )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) ),
				Field::make( 'text', 'pap_schema_url', esc_html__( 'URL', 'localseomap-for-elementor' ) )
				     ->set_default_value( home_url() )
				     ->set_conditional_logic( array(
					     array(
						     'field' => 'pap_use_scheme',
						     'value' => true,
					     )
				     ) )->set_required( true ),


				Field::make( 'checkbox', 'pap_use_scheme_video', esc_html__( 'Add schema for video', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' )
				     ->set_default_value( 'yes' ),

				Field::make( 'separator', 'crb_btn_char_separator', esc_html__( 'Character count', 'localseomap-for-elementor' ) ),

				Field::make( 'text', 'pap_char_count_localseomap_projects', esc_html__( 'Project character count', 'localseomap-for-elementor' ) )
				     ->set_default_value( '' ),
				Field::make( 'text', 'pap_char_count_localseomap_media', esc_html__( 'Media character count', 'localseomap-for-elementor' ) )
				     ->set_default_value( '' ),
				Field::make( 'text', 'pap_industry_char_count', esc_html__( 'Industry character count', 'localseomap-for-elementor' ) )
				     ->set_default_value( '' ),
				Field::make( 'text', 'pap_area_tag_char_count', esc_html__( 'Area Tag character count', 'localseomap-for-elementor' ) )
				     ->set_default_value( '' ),
				Field::make( 'text', 'pap_tag_char_count', esc_html__( 'Tag character count', 'localseomap-for-elementor' ) )
				     ->set_default_value( '' ),

				Field::make( 'html', 'pap_add_noindex', '' )
				     ->set_html(
					     localseomap_test_api_button( esc_html__( 'Add noindex to all projects', 'localseomap-for-elementor' ),
						     'profolio_js_add_noindex',
						     'Noindex is added',
						     'Error'
					     )
				     ),

				Field::make( 'checkbox', 'pap_remove_other_robots', esc_html__( 'Remove the other meta tags', 'localseomap-for-elementor' ) )
				     ->set_option_value( 'yes' )
				     ->set_default_value( 'yes' )
					->set_help_text( esc_html__( 'This option will remove the other meta tags from pages. If this option is not checked we will not add noindex to your pages.', 'localseomap-for-elementor' ) ),

			) );


			//$translation_industry = get_option( '_pap_translation_industry' );

			$name_industry = $this->get_terms_sql( 'localseomap_industry' );
			$name_industry = array_map( function ( $val ) {
				return ! empty( $val->name ) ? $val->name . ' | ' : '';
			}, $name_industry );

			$area_tags = $this->get_terms_sql( 'localseomap_area_tags' );
			$area_tags = array_map( function ( $val ) {
				return ! empty( $val->name ) ? $val->name . ' | ' : '';
			}, $area_tags );

			$framework->add_tab( esc_html__( 'Translate', 'localseomap-for-elementor' ), array(
				Field::make( 'html', 'pap_knowledge_translate' )
				     ->set_html( '<div style="text-align: right"><a target="_blank" href="https://www.localseomap.com/knowledge-base/translate/">Get help</a></div>' ),
				Field::make( 'select', 'pap_language', esc_html__( 'Language', 'localseomap-for-elementor' ) )
				     ->set_options( array(
					     ''       => 'By default',
					     'af'     => 'AFRIKAANS',
					     'sq'     => 'ALBANIAN',
					     'am'     => 'AMHARIC',
					     'ar'     => 'ARABIC',
					     'hy'     => 'ARMENIAN',
					     'az'     => 'AZERBAIJANI',
					     'eu'     => 'BASQUE',
					     'be'     => 'BELARUSIAN',
					     'bn'     => 'BENGALI',
					     'bs'     => 'BOSNIAN',
					     'bg'     => 'BULGARIAN',
					     'my'     => 'BURMESE',
					     'ca'     => 'CATALAN',
					     'zh'     => 'CHINESE',
					     'zh-CN'  => 'CHINESE (SIMPLIFIED)',
					     'zh-HK'  => 'CHINESE (HONG KONG)',
					     'zh-TW'  => 'CHINESE (TRADITIONAL)',
					     'hr'     => 'CROATIAN',
					     'cs'     => 'CZECH',
					     'da'     => 'DANISH',
					     'nl'     => 'DUTCH',
					     'en'     => 'ENGLISH',
					     'en-AU'  => 'ENGLISH (AUSTRALIAN)',
					     'en-GB'  => 'ENGLISH (GREAT BRITAIN)',
					     'et'     => 'ESTONIAN',
					     'fa'     => 'FARSI',
					     'fi'     => 'FINNISH',
					     'fil'    => 'FILIPINO',
					     'fr'     => 'FRENCH',
					     'fr-CA'  => 'FRENCH (CANADA)',
					     'gl'     => 'GALICIAN',
					     'ka'     => 'GEORGIAN',
					     'de'     => 'GERMAN',
					     'el'     => 'GREEK',
					     'gu'     => 'GUJARATI',
					     'iw'     => 'HEBREW',
					     'hi'     => 'HINDI',
					     'hu'     => 'HUNGARIAN',
					     'is'     => 'ICELANDIC',
					     'id'     => 'INDONESIAN',
					     'it'     => 'ITALIAN',
					     'ja'     => 'JAPANESE',
					     'kn'     => 'KANNADA',
					     'kk'     => 'KAZAKH',
					     'km'     => 'KHMER',
					     'ko'     => 'KOREAN',
					     'ky'     => 'KYRGYZ',
					     'lo'     => 'LAO',
					     'lv'     => 'LATVIAN',
					     'lt'     => 'LITHUANIAN',
					     'mk'     => 'MACEDONIAN',
					     'ms'     => 'MALAY',
					     'ml'     => 'MALAYALAM',
					     'mr'     => 'MARATHI',
					     'mn'     => 'MONGOLIAN',
					     'ne'     => 'NEPALI',
					     'no'     => 'NORWEGIAN',
					     'pl'     => 'POLISH',
					     'pt'     => 'PORTUGUESE',
					     'pt-BR'  => 'PORTUGUESE (BRAZIL)',
					     'pt-PT'  => 'PORTUGUESE (PORTUGAL)',
					     'pa'     => 'PUNJABI',
					     'ro'     => 'ROMANIAN',
					     'ru'     => 'RUSSIAN',
					     'sr'     => 'SERBIAN',
					     'si'     => 'SINHALESE',
					     'sk'     => 'SLOVAK',
					     'sl'     => 'SLOVENIAN',
					     'es'     => 'SPANISH',
					     'es-419' => 'SPANISH (LATIN AMERICA)',
					     'sw'     => 'SWAHILI',
					     'sv'     => 'SWEDISH',
					     'ta'     => 'TAMIL',
					     'te'     => 'TELUGU',
					     'th'     => 'THAI',
					     'tr'     => 'TURKISH',
					     'uk'     => 'UKRAINIAN',
					     'ur'     => 'URDU',
					     'uz'     => 'UZBEK',
					     'vi'     => 'VIETNAMESE',
					     'zu'     => 'ZULU',
				     ) )
				     ->set_help_text( esc_html__( 'This language will be used in for the map and all locations of the website', 'localseomap-for-elementor' ) ),
				Field::make( 'textarea', 'pap_translation', esc_html__( 'Translate strings of the plugin', 'localseomap-for-elementor' ) )
				     ->set_rows( 15 )
				     ->set_help_text( esc_html__( 'You can translate any text on the page. For examle: Original word | Translated word', 'localseomap-for-elementor' ) ),
				Field::make( 'textarea', 'pap_translation_industry', esc_html__( 'Translate industry', 'localseomap-for-elementor' ) )
				     ->set_value( implode( "\n", array_filter( $name_industry ) ) )
				     ->set_rows( 15 ),
				Field::make( 'textarea', 'pap_translation_area_tags', esc_html__( 'Translate area tags', 'localseomap-for-elementor' ) )
				     ->set_rows( 15 )
				     ->set_default_value( implode( "\n", array_filter( $area_tags ) ) )
				     ->set_help_text( esc_html__( 'This option re-create area tags with new names.', 'localseomap-for-elementor' ) ),
			) );

		}

		if ( localseomap()->is_plan( 'starter' ) && is_admin() ) {
			if ( defined( "LOCALSEOMAP_ENABLE_LOGS" ) && LOCALSEOMAP_ENABLE_LOGS == true ) {

				ob_start(); ?>
                <a href="<?php echo home_url( 'wp-admin/admin.php?page=crb_carbon_fields_container_localseomap_for_elementor.php&localseomap_clear_logs=true' ); ?>"
                   class="button button-primary button-large">
					<?php esc_html_e( 'Clear logs', 'localseomap-for-elementor' ); ?>
                </a>

				<?php
				$clear_button = ob_get_clean();

				$framework->add_tab( esc_html__( 'Logs', 'localseomap-for-elementor' ), array(
					Field::make( 'html', 'pap_knowledge_logs' )
					     ->set_html( '<div style="text-align: right"><a target="_blank" href="https://www.localseomap.com/knowledge-base/logs/">Get help</a></div>' ),

					Field::make( 'html', 'pap_logs_response', esc_html__( 'Errors', 'localseomap-for-elementor' ) )
					     ->set_html( '<p>' . esc_html__( 'Error logs', 'localseomap-for-elementor' ) . '</p><pre style="background: grey;padding: 10px;color: #fff;overflow-y: scroll;height: 400px;width: 870px">' . get_option( 'localseomap_error_logs' ) . '</pre>' ),

					Field::make( 'html', 'pap_logs_webhooks', esc_html__( 'Logs', 'localseomap-for-elementor' ) )
					     ->set_html( '<p>' . esc_html__( 'Logs', 'localseomap-for-elementor' ) . '</p><pre style="background: grey;padding: 10px;color: #fff;overflow-y: scroll;height: 400px;width: 870px">' . get_option( 'localseomap_success_logs' ) . '</pre>' ),
					Field::make( 'html', 'pap_logs_clear_button', esc_html__( 'Clear logs', 'localseomap-for-elementor' ) )
					     ->set_html( $clear_button )
				) );


			}
		}

	}

	public function rewrite_rules() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules( true );
		flush_rewrite_rules();
	}

}
