<?php
/**
 * User: localseomap
 * Date: 9.07.2019
 * @package LocalSeoMap/Core
 */

namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Core
 * @since  1.0.0
 */
class Core {

	/**
	 * Instance.
	 * Holds the plugin instance.
	 * @since  1.0.0
	 * @access public
	 * @static
	 * @var $instance Core.
	 */
	public static $instance = null;

	/**
	 * Instance.
	 * @return Core|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			/**
			 * LocalSeoMap loaded.
			 * @since 1.0.0
			 */
			do_action( 'localseomap_load' );
		}

		return self::$instance;
	}

	/**
	 * Constructor loads API functions, defines paths and adds required wp actions
	 * @since  1.0
	 */
	private function __construct() {

		/**
		 * Include autoloader.
		 */
		require dirname( __FILE__ ) . '/autoloader.php';

		/**
		 * Register autoloader and add namespaces.
		 */
		$this->register_autoloader();

		/**
		 * Init components.
		 */
		$this->init_components();

	}


	/**
	 * Register autoloader.
	 * @since  1.0.0
	 * @access private
	 */
	private function register_autoloader() {

		/**
		 *  Get the autoloader.
		 * */
		$autoloader = new Autoloader();

		/**
		 *  Register the autoloader.
		 * */
		$autoloader->register();

		/**
		 * Register the base directories for the namespace prefix.
		 * */
		$autoloader->add_namespace( 'LocalSeoMap', dirname( __FILE__ ) );
	}

	/**
	 * Init LocalSeoMap components.
	 * @since  1.0.0
	 * @access private
	 */
	public function init_components() {

		// Init Admin class.
		$admin = new Admin();
		$admin->init();

		// Init Admin class.
		$options = new Options();
		$options->init();


		$rewrite = new Rewrite();
		$rewrite->init();

		$types = new Types();
		$types->init();

		$metaboxes = new Metaboxes();
		$metaboxes->init();

		$importer = new Ajax();
		$importer->init();

		$importer = new Importer();
		$importer->init();

		$dynamic = new Dynamic();
		$dynamic->init();

		$front_ui = new FRONT_UI();
		$front_ui->init();

		$leads = new Leads();
		$leads->init();

		$seo = new Seo();
		$seo->init();

		$localize = new Localize();
		$localize->init();


		/**
		 * LocalSeoMap init.
		 * @since 1.0.0
		 */
		do_action( 'localseomap_init' );

	}


}

Core::instance();
