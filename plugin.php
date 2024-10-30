<?php
/**
 * Plugin Name: LocalSEOMap for Elementor
 * Plugin URI: https://localseomap.com/
 * Description: Speed up Local SEO rankings with an interactive Google map and search-optimized detail pages.
 * Author: LocalSEOMap.com
 * Author URI:
 * Version: 1.5.2
 * Text Domain: localseomap-for-elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


define( 'LOCALSEOMAP_FILE', __FILE__ );
define( 'LOCALSEOMAP_PATH', wp_normalize_path( plugin_dir_path( LOCALSEOMAP_FILE ) ) );
define( 'LOCALSEOMAP_URL', wp_normalize_path( plugin_dir_url( LOCALSEOMAP_FILE ) ) );
define( 'LOCALSEOMAP_VERSION', '_ver_1.5.2_' );
define( 'LOCALSEOMAP_ENABLE_LOGS', true );


include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/*
 * Include libs.
 */
include_once( plugin_dir_path( LOCALSEOMAP_FILE ) . 'vendor/autoload.php' );

/*
 * Init the freemius lib.
 * */
include_once( plugin_dir_path( LOCALSEOMAP_FILE ) . '/init_freemius.php' );

/*
 * Include framework.
 */
include_once( plugin_dir_path( LOCALSEOMAP_FILE ) . 'data/init.php' );

/*
 * Include the plugin core.
 */
include_once( plugin_dir_path( LOCALSEOMAP_FILE ) . 'src/core.php' );
