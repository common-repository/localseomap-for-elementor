<?php

namespace ProfolioBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * The widget class.
 *
 * @since  1.0.0
 */
class WPBakery_Profolio_filter {

	/**
	 * ProfolioBuilder_projects constructor.
	 */
	public function __construct() {

		$this->controls();
		$this->output();
	}

	/**
	 * Get Loader class.
	 *
	 * @return Loader
	 */
	private function loader() {

		return new Loader( 'wpbakery', 'wpbakery', dirname( __FILE__ ) );
	}

	/**
	 * The controls output.
	 */
	private function controls() {

		if ( function_exists( 'vc_map' ) ) {
			vc_map( $this->get_base_params() );
		}


	}

	/**
	 * The shortcode output.
	 */
	private function output() {

		add_shortcode(
			$this->get_base_params()['base'],
			function ( $atts ) {

				ob_start();
				$this->loader()->render( $atts );

				return ob_get_clean();

			}
		);

	}

	/**
	 * Get all params of the shortcode.
	 *
	 * @return mixed
	 */
	public function get_base_params() {
		$base_params                     = $this->loader()->get_base_params();
		$base_params['front_enqueue_js'] = PROFOLIO_BUILDER_DATA_URL . 'assets/admin/js/front-editor.js';

		return $base_params;
	}
}
