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
class ProfolioBuilder_Profolio_filter extends Widget_Base {

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

		$result_params = array();
		foreach ( $params as $param ) {
			if ( empty( $param['group'] ) ) {
				continue;
			}
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

			if ( 'General' === $key ) {
                $this->add_control(
                    'filter_terms',
                    [
                        'label'     => __( 'Select Category','localseomap-for-elementor'),
                        'type'      => \Elementor\Controls_Manager::SELECT2,
                        'multiple'  => true,
                        'options'   => localseomap_get_filter_industry(),
                        'condition' => [
                            'tags_instead_industry' => '',
                        ],
                    ]
                );

                $this->add_control(
                    'filter_tags',
                    [
                        'label'    => __( 'Select Tags','localseomap-for-elementor'),
                        'type'     => \Elementor\Controls_Manager::SELECT2,
                        'multiple' => true,
                        'options'  => localseomap_get_filter_tags(),
                        'condition' => [
                            'tags_instead_industry' => 'yes',
                        ],
                    ]
                );
            }

			if ( 'Style' === $key ) {
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'     => 'input_typography',
						'label'    => esc_html__( 'Input typography','localseomap-for-elementor'),
						'selector' => '{{WRAPPER}} .profolio-search-form .form-control',

					]
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name'     => 'button_typography',
						'label'    => esc_html__( 'Button typography','localseomap-for-elementor'),
						'selector' => '{{WRAPPER}} .profolio-input-frame .btn',

					]
				);
			}

			$this->end_controls_section();
		}

	}


}

