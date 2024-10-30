<?php
/**
 *
 * Loader all params of widget
 *
 * @package ProfolioBuilder
 */

namespace ProfolioBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 *
 * Loader.
 */
class Loader {

	/**
	 * All params.
	 *
	 * @var $params
	 */
	private $params;

	/**
	 * The builder slug..
	 *
	 * @var $type_from
	 */
	private $type_from;

	/**
	 * The builder slug..
	 *
	 * @var $type_to
	 */
	private $type_to;


	/**
	 * Template file.
	 *
	 * @var $tmpl_file
	 */
	private $tmpl_file;

	/**
	 * Converter constructor.
	 *
	 * @param string $type_from  The builder slug.
	 * @param string $type_to    The builder slug.
	 * @param string $dir_widget The widget dir.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct( $type_from = null, $type_to = null, $dir_widget = '' ) {

		if ( ! $type_from ) {
			return null;
		}

		$this->type_from  = $type_from;
		$this->type_to    = $type_to;
		$this->dir_widget = $dir_widget;

		$this->params = $this->parse(
			array(
				'type' => $this->type_from,
				'file' => $dir_widget . '/config.php',
			)
		);

	}

	/**
	 * Get Converter.
	 *
	 * @return Converter
	 */
	private function converter() {
		return new Converter();
	}

	/**
	 * Get base params of the widget.
	 *
	 * @return array
	 */
	public function get_base_params() {

		if ( ! empty( $this->params['params'] ) && $this->type_from != $this->type_to ) {
			$this->params['params'] = $this->get_params();
		}

		return $this->params;

	}

	/**
	 * Get params from file.
	 *
	 * @param null $args All params.
	 *
	 * @return null
	 */
	private function parse( $args = null ) {

		if ( ! $args['file'] ) {
			return null;
		}

		$file = apply_filters( 'profoliobuilder/parse/path', $args['file'] );

		if ( $file && file_exists( $file ) && is_readable( $file ) ) {

			$file_content = include( $file );

			if ( 'vc' === $args['type'] && ! empty( $file_content['params'] ) ) {
				return $file_content['params'];
			}

			return $file_content;

		}

		return null;

	}

	/**
	 * Get all params of the widget.
	 *
	 * @return array
	 */
	public function get_params() {

		if ( empty( $this->type_from ) || empty( $this->type_to ) ) {
			return array();
		}

		return $this->converter()->convert_params( $this->params['params'], $this->type_from, $this->type_to );

	}

	/**
	 * Get the widget name.
	 *
	 * @return string
	 */
	public function get_name() {

		return $this->converter()->get_name( $this->params, $this->type_from );

	}

	/**
	 * Get the widget name.
	 *
	 * @return string
	 */
	public function get_title() {

		return $this->converter()->get_title( $this->params, $this->type_from );

	}

	/**
	 * Get the widget slug.
	 *
	 * @return string
	 */
	public function get_slug() {

		return $this->converter()->get_slug( $this->params, $this->type_from );

	}

	/**
	 * Get the widget category.
	 *
	 * @return string
	 */
	public function get_category() {
		return strtolower(
			str_replace(
				' ',
				'_',
				$this->converter()->get_category( $this->params, $this->type_from )
			)
		);

	}

	/**
	 * Get icon. Needs change.
	 *
	 * @return string
	 */
	public function get_icon() {
		return $this->converter()->get_icon( $this->params, $this->type_from, $this->type_to );
	}

	/**
	 * Get the template of the widget.
	 *
	 * @param string $atts All params.
	 *
	 * @return string
	 */
	public function get_tmpl_file( $atts ) {

		$this->tmpl_file = $this->dir_widget . '/tmpl.php';

		return apply_filters( 'profoliobuilder/widget_tmpl/' . $this->get_slug(), $this->tmpl_file );
	}

	/**
	 * Output content.
	 *
	 * @param string $atts All params.
	 */
	public function render( $atts ) {

		if ( is_array( $atts ) ) {

			$atts = array_filter(
				$atts,
				function ( $e ) {
					return ! empty( $e );
				}
			);

			$atts = array_filter(
				$atts,
				function ( $key ) {
					return substr( $key, 0, 1 ) !== '_';
				},
				ARRAY_FILTER_USE_KEY
			);

		}

		require $this->get_tmpl_file( $atts );
	}

}
