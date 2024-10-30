<?php

// If this file is accessed directory, then abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'localseomap' ) ) {
	// Create a helper function for easy SDK access.
	function localseomap() {
		global $localseomap;

		if ( ! isset( $localseomap ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/freemius/start.php';

			$localseomap = fs_dynamic_init( array(
				'id'                  => '4549',
				'slug'                => 'local_seo_map_addon_elementor',
				'type'                => 'plugin',
				'public_key'          => 'pk_0c85428187a8ad12e1095a8cbcdd6',
				'is_premium'          => true,
				// If your plugin is a serviceware, set this option to false.
				'has_premium_version' => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'menu'                => array(
					'slug'           => 'crb_carbon_fields_container_localseomap_for_elementor.php',
					'override_exact' => false,
					'first-path'     => 'admin.php?page=crb_carbon_fields_container_localseomap_for_elementor.php',
					'support'        => false,
				),
			) );
		}

		return $localseomap;
	}

	// Init Freemius.
	localseomap();
	// Signal that SDK was initiated.
	do_action( 'localseomap_loaded' );

	function localseomap_settings_url() {
		return admin_url( 'admin.php?page=crb_carbon_fields_container_localseomap_for_elementor.php' );
	}

	localseomap()->add_filter( 'connect_url', 'localseomap_settings_url' );
	localseomap()->add_filter( 'after_skip_url', 'localseomap_settings_url' );
	localseomap()->add_filter( 'after_connect_url', 'localseomap_settings_url' );
	localseomap()->add_filter( 'after_pending_connect_url', 'localseomap_settings_url' );
}
