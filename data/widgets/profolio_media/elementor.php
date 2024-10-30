<?php
/**
 * User: localseomap
 * Date: 19.11.2018
 * Time: 15:46
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use ProfolioBuilder;

/**
 * The widget class.
 *
 * @since  1.0.0
 */
class ProfolioBuilder_Profolio_media extends Widget_Base {

	/**
	 * Get Loader class.
	 *
	 * @return ProfolioBuilder\Loader
	 */
	private function loader() {
		return new ProfolioBuilder\Loader( 'wpbakery', 'elementor', dirname( __FILE__ ) );
	}

	/**
	 * Get the widget category.
	 *
	 * @return array
	 */
	public function get_categories() {

		$category = $this->loader()->get_category();

		return [ $category ];
	}


	/**
	 * Get the widget name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->loader()->get_slug();

	}

	/**
	 * Get the title of the widget.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->loader()->get_title();
	}

	/**
	 *  Get the icon of the widget.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'profolio-builder-' . $this->loader()->get_slug() . '-icon';
	}


	/**
	 * Output content.
	 */
	protected function render() {
		$this->loader()->render( $this->get_settings_for_display() );
	}

	/**
	 * Register the Elementor controls.
	 */
	protected function _register_controls() {

		$params = $this->loader()->get_params();

		foreach ( $params as $param ) {
			$result_params[ $param['group'] ][] = $param;
		}

		foreach ( $result_params as $key => $param ) {

			$this->start_controls_section(
				$key,
				[
					'label' => $key,
				]
			);

			if ( ! empty( $param ) && is_array( $param ) ) {
				foreach ( $param as $sub_key => $subparam ) {

					if ( 'repeater' === $subparam['type'] ) {
						$fields = $subparam['fields'];
						foreach ( $fields as $key => $field ) {

							if ( 'slider' === $field['type'] ) {

								$fields[ $key ]['range'] = array(
									'px' => array(
										'min' => 0,
										'max' => 20,
									),
								);

								$subparam['fields'] = $fields;
							}
						}
					}

					$this->add_control( $subparam['name'], $subparam );
				}
			}

			$this->end_controls_section();
		}

	}


}

