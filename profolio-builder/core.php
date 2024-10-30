<?php

namespace ProfolioBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

defined( 'PROFOLIO_BUILDER_VERSION' ) || define( 'PROFOLIO_BUILDER_VERSION', '1.0.0' );
defined( 'PROFOLIO_BUILDER__FILE__' ) || define( 'PROFOLIO_BUILDER__FILE__', __FILE__ );
defined( 'PROFOLIO_BUILDER_PATH' ) || define( 'PROFOLIO_BUILDER_PATH', wp_normalize_path( dirname( PROFOLIO_BUILDER__FILE__ ) ) . '/' );
defined( 'PROFOLIO_BUILDER_PARENT' ) || define( 'PROFOLIO_BUILDER_PARENT', dirname( PROFOLIO_BUILDER_PATH ) . '/' );
defined( 'PROFOLIO_BUILDER_DATA' ) || define( 'PROFOLIO_BUILDER_DATA', PROFOLIO_BUILDER_PARENT . 'data/' );
defined( 'PROFOLIO_BUILDER_DATA_URL' ) || define( 'PROFOLIO_BUILDER_DATA_URL', plugins_url( '/data/', PROFOLIO_BUILDER_DATA ) );

if ( ! class_exists( 'ProfolioBuilder\Core' ) ) {
	/**
	 * Main plugin class.
	 */
	class Core {

		/**
		 * Instance.
		 *
		 * @var Core
		 */
		public static $instance = null;


		/**
		 * @static
		 * @return Core
		 * @since  1.0.0
		 * @access public
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
				do_action( 'profoliobuilder/loaded' );
			}

			return self::$instance;
		}


		/**
		 * Plugin constructor.
		 *
		 * @since  1.0.0
		 * @access private
		 */
		private function __construct() {
			$this->register_autoloader();
		}

		/**
		 * Register autoloader.
		 *
		 * @since  1.6.0
		 * @access private
		 */
		private function register_autoloader() {

			require PROFOLIO_BUILDER_PATH . 'autoloader.php';

		}


	}

	Core::instance();
}

