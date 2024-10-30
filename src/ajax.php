<?php
/**
 * User: localseomap
 * Date: 9.07.2019
 *
 * @package LocalSeoMap/API
 */

namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Ajax.
 *
 * @since  1.0.0
 */
class Ajax extends Admin {


	/**
	 * Ajax constructor.
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

		add_action( 'wp_ajax_localseomap_get_products', array( &$this, 'get_projects' ) );
		add_action( 'wp_ajax_nopriv_localseomap_get_products', array( &$this, 'get_projects' ) );


	}

	/**
	 * Get projects by AJAX.
	 */
	public function get_projects() {

		$prefix = $this->get_metabox_prefix();

		$atts = array(
			'posts_per_page' => '',
			'orderby'        => '',
			'number_columns' => '',
			'orderby'        => '',
		);

		$args = array(
			'numberposts'    => - 1,
			'post_type'      => 'localseomap_projects',
			'posts_per_page' => ! empty( $atts['posts_per_page'] ) ? $atts['posts_per_page'] : 26,
			'post_status'    => array( 'publish' ),
			'orderby'        => 'date',
		);

		if ( ! empty( $atts['orderby'] ) ) {

			if ( 'ID' === $atts['orderby'] ) {
				$args['orderby']  = 'meta_value';
				$args['meta_key'] = $prefix . 'uuid';
			}

			if ( 'start_date' === $atts['orderby'] ) {
				$args['orderby']  = 'meta_value';
				$args['meta_key'] = $prefix . 'start_date';
			}
		}

		$start_date = get_post_meta( '', $prefix . 'start_date', true );

		$col = ! empty( $atts['number_columns'] ) ? $atts['number_columns'] : 'profolio-col-lg-6';

		$projects_query = new WP_Query( $args );
		if ( $projects_query->have_posts() ) {

			die();

		}
	}
}
