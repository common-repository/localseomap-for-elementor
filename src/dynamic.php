<?php

namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}


class Dynamic extends Admin {


	private $slug_input_form;


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

		add_action( 'loop_start', array( &$this, 'action_loop_start' ), 10, 3 );

		register_activation_hook( LOCALSEOMAP_FILE, array( &$this, 'make_project_page' ) );

		add_filter( 'rest_industry_query', array( &$this, 'rest_industry_query' ), 10, 2 );

		/* Add editor to terms */
		add_action( 'project_area_tags_edit_form_fields', array( &$this, 'add_form_fields_term' ) );

		add_action( 'admin_head', array( &$this, 'remove_default_category_description' ) );
	}

	public function rest_industry_query( $args, $request ) {
		$pap_allowed_industry = carbon_get_theme_option( 'pap_allowed_industry' );

		$new_industry = get_terms(
			array(
				'taxonomy'   => 'localseomap_industry',
				'fields'     => 'ids',
				'hide_empty' => false,
				'search'     => 'manual_',
			)
		);

		$pap_allowed_industry = array_merge( $pap_allowed_industry, $new_industry );

		if ( ! empty( $pap_allowed_industry ) && is_array( $pap_allowed_industry ) ) {
			$args['include'] = $pap_allowed_industry;

			foreach ( $pap_allowed_industry as $id ) {
				$term = get_term( $id, 'localseomap_industry' );
				if ( $term->parent !== 0 ) {
					$subterm = get_term( $term->parent, 'localseomap_industry' );
					if ( $subterm->parent !== 0 ) {
						$args['include'][] = $subterm->parent;
					}
					$args['include'][] = $term->parent;
				}
			}
		}


		return $args;
	}

	public function add_form_fields_term( $term ) {

		$term_content = '';
		if ( ! empty( $term->description ) ) {
			$term_content = $term->description;
		}
		?>
        <tr class="term_editor">
            <th scope="row"><?php esc_html_e( 'Description', 'localseomap-for-elementor' ) ?></th>
            <td>
				<?php wp_editor( html_entity_decode( $term_content ), 'description', array( 'media_buttons' => false ) ); ?>
            </td>
        </tr>
		<?php
	}

	public function remove_default_category_description() {
		global $current_screen;
		if ( $current_screen->id == 'edit-project_area_tags' ) {
			?>
            <script type="text/javascript">
							jQuery(function ($) {
								$('textarea#description').closest('tr.form-field').remove();
							});
            </script>
			<?php
		}
	}

	public function action_loop_start() {

		add_filter( 'the_content', array( &$this, 'replace_content_to_form' ), 10 );

	}

	/**
	 * Replace content.
	 *
	 * @param string $content Content.
	 *
	 * @return false|string
	 */
	public function replace_content_to_form( $content ) {


		remove_filter( 'the_content', 'replace_content_to_form' );

		global $post;

		$show_input_form = sanitize_text_field( get_query_var( 'add-input-form' ) );
		$template_slug   = get_page_template_slug( $post->ID );
		if ( ( $template_slug == $this->template ) && empty( $show_input_form ) ) {

			remove_filter( 'the_content', 'wpautop' );

			ob_start();

			include LOCALSEOMAP_PATH . 'data/template/project-page.php';

			return ob_get_clean();

		} else {
			return $content;
		}
	}


	/**
	 *
	 */
	public function make_project_page() {

		$args = array(
			'post_type'  => 'page',
			'meta_key'   => '_wp_page_template',
			'meta_value' => $this->template
		);


		$pages = get_posts( $args );

		if ( ! empty( $pages ) ) {
			return;
		}

		$project_data = [
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => 'lsm-project-page',
			'post_author'  => '1',
			'post_title'   => esc_html__( 'Project Page', 'localseomap-for-elementor' ),
			'post_content' => esc_html__( 'This page will output the map and your projects. This message will not be shown on the front-end.', 'localseomap-for-elementor' ),
		];

		$post_id = wp_insert_post( wp_slash( $project_data ) );

		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_wp_page_template', $this->template );
		}

	}


}


