<?php
/**
 * User: localseomap
 * Date: 9.07.2019
 *
 * @package LocalSeoMap/Rewrite
 */

namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Rewrite.
 *
 * @since  1.0.0
 */
class Rewrite extends Admin {

	/**
	 * API constructor.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Init rewrite.
	 */
	public function init() {

		//add_action( 'parse_query', array( &$this, 'add_rewrite_rule' ), 10, 0 );
		add_action( 'init', array( &$this, 'add_rewrite_rule' ), 0, 0 );
		add_filter( 'query_vars', array( &$this, 'add_query_vars' ), 10, 1 );
	}

	/**
	 * Add new rewrite rule.
	 */
	public function add_rewrite_rule() {

		add_rewrite_rule( '^profolio/?$', 'index.php?profolio=/', 'top' );
		add_rewrite_rule( '^profolio/webhook/?$', 'index.php?profolio=webhook', 'top' );

		add_rewrite_rule( '^add-input-form/?$', 'index.php?add-input-form=true&project_id=0', 'top' );
		add_rewrite_rule( '^add-input-form/([0-9]{1,})/?$', 'index.php?add-input-form=true&project_id=$matches[1]', 'top' );

		global $wp_rewrite;
		$wp_rewrite->flush_rules( true );
		flush_rewrite_rules();

	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars All query vars.
	 *
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'profolio';
		$vars[] = 'add-input-form';
		$vars[] = 'project_id';

		return $vars;
	}

}


