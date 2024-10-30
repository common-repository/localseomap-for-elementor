<?php

namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Types extends Admin {

	/**
	 * Types constructor.
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

	}

	public function init() {
		add_action( 'init', array( $this, 'new_post_type' ), 100 );
		add_action( 'init', array( $this, 'new_taxonomy' ), 10 );
		add_filter( 'single_template', array( $this, 'project_template' ), 0 );

		global $profolio_content_counter;
		$profolio_content_counter = 0;

	}

	/**
	 * Create new custom post types.
	 * @since  1.0.0
	 * @access public
	 */
	public function new_post_type() {

		/* Projects post type */
		$labels = array(
			'name'               => _x( 'Profolio Projects', 'post type general name', 'localseomap-for-elementor' ),
			'singular_name'      => _x( 'Project', 'post type singular name', 'localseomap-for-elementor' ),
			'menu_name'          => _x( 'Projects', 'admin menu', 'localseomap-for-elementor' ),
			'name_admin_bar'     => _x( 'Project', 'add new on admin bar', 'localseomap-for-elementor' ),
			'add_new'            => _x( 'Add New', 'project', 'localseomap-for-elementor' ),
			'add_new_item'       => esc_html__( 'Add New Project', 'localseomap-for-elementor' ),
			'new_item'           => esc_html__( 'New Project', 'localseomap-for-elementor' ),
			'edit_item'          => esc_html__( 'Edit Project', 'localseomap-for-elementor' ),
			'view_item'          => esc_html__( 'View Project', 'localseomap-for-elementor' ),
			'all_items'          => esc_html__( 'All Projects', 'localseomap-for-elementor' ),
			'search_items'       => esc_html__( 'Search Projects', 'localseomap-for-elementor' ),
			'parent_item_colon'  => esc_html__( 'Parent Projects:', 'localseomap-for-elementor' ),
			'not_found'          => esc_html__( 'No Projects found.', 'localseomap-for-elementor' ),
			'not_found_in_trash' => esc_html__( 'No Projects found in Trash.', 'localseomap-for-elementor' ),
		);

		if ( function_exists( 'carbon_get_theme_option' ) ) {
			$rewrite_project_slug = carbon_get_theme_option( 'rewrite_project_slug' );
		}
		if ( empty( $rewrite_project_slug ) ) {
			$rewrite_project_slug = 'lsm_projects';
		}

		register_post_type(
			'localseomap_projects',
			// CPT Options.
			array(
				'labels'       => $labels,
				'public'       => true,
				'has_archive'  => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => $rewrite_project_slug, 'with_front' => false ),
				'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
				'menu_icon'    => plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/img/profolio-icon.svg',
			)
		);

		/* Media post type */
		$labels = array(
			'name'               => _x( 'Project media', 'post type general name', 'localseomap-for-elementor' ),
			'singular_name'      => _x( 'Project media', 'post type singular name', 'localseomap-for-elementor' ),
			'menu_name'          => _x( 'Project media', 'admin menu', 'localseomap-for-elementor' ),
			'name_admin_bar'     => _x( 'Project media', 'add new on admin bar', 'localseomap-for-elementor' ),
			'add_new'            => _x( 'Add New', 'project', 'localseomap-for-elementor' ),
			'add_new_item'       => esc_html__( 'Add New Media', 'localseomap-for-elementor' ),
			'new_item'           => esc_html__( 'New Media', 'localseomap-for-elementor' ),
			'edit_item'          => esc_html__( 'Edit Media', 'localseomap-for-elementor' ),
			'view_item'          => esc_html__( 'View Media', 'localseomap-for-elementor' ),
			'all_items'          => esc_html__( 'All Media', 'localseomap-for-elementor' ),
			'search_items'       => esc_html__( 'Search Media', 'localseomap-for-elementor' ),
			'parent_item_colon'  => esc_html__( 'Parent Media:', 'localseomap-for-elementor' ),
			'not_found'          => esc_html__( 'No Media found.', 'localseomap-for-elementor' ),
			'not_found_in_trash' => esc_html__( 'No Media found in Trash.', 'localseomap-for-elementor' ),
		);

		$rewrite_media_slug = carbon_get_theme_option( 'rewrite_media_slug' );
		if ( empty( $rewrite_media_slug ) ) {
			$rewrite_media_slug = 'profolio_media';
		}
		register_post_type(
			'localseomap_media',
			// CPT Options.
			array(
				'labels'       => $labels,
				'public'       => true,
				'show_in_rest' => true,
				'rewrite'      => array( 'slug' => $rewrite_media_slug, 'with_front' => false ),
				'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
				'menu_icon'    => plugin_dir_url( dirname( __FILE__ ) ) . 'data/assets/img/media-icon.svg',
			)
		);

	}

	/**
	 * Create new custom taxonomy
	 * @since  1.0.0
	 * @access public
	 */
	public function new_taxonomy() {

		$rewrite_term_slug = 'industry';
		// New Taxonomy.
		register_taxonomy(
			'localseomap_industry',
			array( 'localseomap_projects', 'localseomap_media' ),
			array(
				'label'        => esc_html__( 'Industry', 'localseomap-for-elementor' ),
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => $rewrite_term_slug, 'with_front' => true ),
				'show_in_rest' => true,
			)
		);


		$rewrite_tags_slug = 'tag';

		register_taxonomy(
			'localseomap_project_tag',
			array( 'localseomap_projects' ),
			array(
				'label'        => esc_html__( 'Tags', 'localseomap-for-elementor' ),
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => $rewrite_tags_slug ),
				'show_in_rest' => true,
			)
		);


		$rewrite_area_tags_slug = 'area_tags';
		register_taxonomy(
			'localseomap_area_tags',
			array( 'localseomap_projects' ),
			array(
				'label'        => esc_html__( 'Area tags', 'localseomap-for-elementor' ),
				'hierarchical' => true,
				'rewrite'      => array( 'slug' => $rewrite_area_tags_slug ),
				'show_in_rest' => true,
			)
		);

	}

	/**
	 * The single project template.
	 *
	 * @param string $single Template file.
	 *
	 * @return string
	 * @since  1.0.0
	 * @access public
	 */
	public function project_template( $single ) {
		global $post;

		/* Checks for single template by post type */
		if ( 'localseomap_projects' === $post->post_type ) {

			$style    = carbon_get_theme_option( 'pap_single_project_template' );
			$is_story = get_post_meta( $post->ID, $this->get_metabox_prefix() . 'field_project_pro', true );

			if ( ! empty( $style ) ) {
				if ( empty( $is_story ) || $is_story == '2' ) {
					if ( file_exists( LOCALSEOMAP_PATH . '/data/template/project-single-story-' . $style . '.php' ) ) {
						return LOCALSEOMAP_PATH . '/data/template/project-single-story-' . $style . '.php';
					}
				} else {
					if ( file_exists( LOCALSEOMAP_PATH . '/data/template/project-single-' . $style . '.php' ) ) {
						return LOCALSEOMAP_PATH . '/data/template/project-single-' . $style . '.php';
					}
				}
			} else {
				if ( empty( $is_story ) || $is_story == '2' ) {
					if ( file_exists( LOCALSEOMAP_PATH . '/data/template/project-single-story.php' ) ) {
						return LOCALSEOMAP_PATH . '/data/template/project-single-story.php';
					}
				} else {
					if ( file_exists( LOCALSEOMAP_PATH . 'data/template/project-single.php' ) ) {
						return LOCALSEOMAP_PATH . 'data/template/project-single.php';
					}
				}
			}
		}

		if ( 'localseomap_media' === $post->post_type ) {
			$style = carbon_get_theme_option( 'pap_single_project_template' );
			if ( ! empty( $style ) ) {
				if ( file_exists( LOCALSEOMAP_PATH . '/data/template/media-single-' . $style . '.php' ) ) {
					return LOCALSEOMAP_PATH . '/data/template/media-single-' . $style . '.php';
				}
			} else {
				if ( file_exists( LOCALSEOMAP_PATH . '/data/template/media-single.php' ) ) {
					return LOCALSEOMAP_PATH . '/data/template/media-single.php';
				}
			}
		}

		return $single;
	}
}
