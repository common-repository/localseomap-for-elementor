<?php

namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Localize extends Admin {

	/**
	 * Translate constructor.
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

	}

	public function init() {

		add_action( 'plugins_loaded', array( &$this, 'load_plugin_textdomain' ), 1 );
		add_filter( 'gettext', array( &$this, 'string_translate_hook' ), 20, 3 );

		add_action( 'carbon_fields_theme_options_container_saved', array( &$this, 'action_translate_terms' ) );
	}


	public function load_plugin_textdomain() {

		load_plugin_textdomain( 'localseomap-for-elementor', false, dirname( plugin_basename( LOCALSEOMAP_FILE ) ) . '/languages/' );
	}

	public static function translate( $translation, $option_key ) {
		/* Translate words */
		$opt_translation = get_option( $option_key );
		if ( ! empty( $opt_translation ) ) {

			$translations = preg_split( '/\r\n|\r|\n/', $opt_translation );
			if ( is_array( $translations ) ) {
				foreach ( $translations as $line ) {
					if ( strpos( $line, '|' ) === false ) {
						continue;
					}

					$parts = explode( '|', $line );

					$part1 = trim( $parts[0] );
					$part2 = trim( $parts[1] );

					if ( empty( $part1 ) || empty( $part2 ) ) {
						continue;
					}

					$translation = str_replace( $part1, $part2, $translation );

				}
			}
		}

		return $translation;
	}

	public function string_translate_hook( $translation, $text, $domain ) {

		$translation = $this->translate( $translation, '_pap_translation' );

		return $translation;
	}


	private function translate_terms( $option_key, $taxonomy ) {

		$opt_translation = get_option( $option_key );
		$translations    = preg_split( '/\r\n|\r|\n/', $opt_translation );
		if ( is_array( $translations ) ) {
			foreach ( $translations as $line ) {
				if ( strpos( $line, '|' ) === false ) {
					continue;
				}

				$line = explode( '|', $line );
				$term = $this->get_term_by_meta_value( $taxonomy, 'original_name', trim( $line[0] ) );
				if ( empty( $term ) ) {
					$term = get_term_by( 'name', trim( $line[0] ), $taxonomy );
					if ( ! is_wp_error( $term ) && ! empty( $term ) ) {
						add_term_meta( $term->term_id, 'original_name', trim( $line[0] ), true );
					}
				}

				if ( ! is_wp_error( $term ) && ! empty( $term ) ) {

					if ( ! empty( $line[1] ) ) {
						wp_update_term( $term->term_id, $taxonomy, array(
							'name' => trim( $line[1] ),
							'slug' => sanitize_title( trim( $line[1] ) )
						) );
						add_term_meta( $term->term_id, 'translated', 'true', true );
					}

				}

			}
		}
	}

	public function action_translate_terms() {

		/*
		 * Translate Area Tags terms
		 * */
		$this->translate_terms( '_pap_translation_area_tags', 'localseomap_area_tags' );

		/*
		 * Translate Industry terms
		 * */
		$this->translate_terms( '_pap_translation_industry', 'localseomap_industry' );
	}
}
