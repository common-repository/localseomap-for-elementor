<?php
/**
 * Type: wpbakery
 */

return array(
	'name'        => esc_html__( 'Filter', 'localseomap-for-elementor' ),
	'description' => esc_html__( 'Profolio filter', 'localseomap-for-elementor' ),
	'base'        => 'filter',
	'icon'        => plugin_dir_url( __FILE__ ) . 'icon.svg?' . LOCALSEOMAP_VERSION,
	'category'    => 'Profolio widgets',
	'params'      => array(

		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Filter projects with "Pro" only', 'localseomap-for-elementor' ),
			'param_name' => 'pro_project',
			'value'      => array( esc_html__( 'Yes, please', 'localseomap-for-elementor' ) => 'yes' ),
			'group'      => esc_html__( 'General', 'localseomap-for-elementor' ),
		),
		array(
			'type'        => 'checkbox',
			'heading'     => esc_html__( 'Filter in tags instead category', 'localseomap-for-elementor' ),
			'param_name'  => 'tags_instead_industry',
			'value'       => array( esc_html__( 'Yes, please', 'localseomap-for-elementor' ) => 'yes' ),
			'description' => esc_html__( 'This field affects the field below', 'localseomap-for-elementor' ),
			'group'       => esc_html__( 'General', 'localseomap-for-elementor' ),
		),
		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Show sale type field', 'localseomap-for-elementor' ),
			'param_name' => 'show_sale_type',
			'value'      => array( esc_html__( 'Yes, please', 'localseomap-for-elementor' ) => 'yes' ),
			'group'      => esc_html__( 'General', 'localseomap-for-elementor' ),
		),
		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Show status field', 'localseomap-for-elementor' ),
			'param_name' => 'show_status_field',
			'value'      => array( esc_html__( 'Yes, please', 'localseomap-for-elementor' ) => 'yes' ),
			'group'      => esc_html__( 'General', 'localseomap-for-elementor' ),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Input text color', 'localseomap-for-elementor' ),
			'param_name' => 'input_text_color',
			'group'      => esc_html__( 'Style', 'localseomap-for-elementor' ),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Input icons background', 'localseomap-for-elementor' ),
			'param_name' => 'input_icons_background',
			'group'      => esc_html__( 'Style', 'localseomap-for-elementor' ),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Button text color', 'localseomap-for-elementor' ),
			'param_name' => 'input_button_color',
			'group'      => esc_html__( 'Style', 'localseomap-for-elementor' ),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Button background color', 'localseomap-for-elementor' ),
			'param_name' => 'input_button_bg',
			'group'      => esc_html__( 'Style', 'localseomap-for-elementor' ),
		),

		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Button hover color', 'localseomap-for-elementor' ),
			'param_name' => 'input_button_hover_color',
			'group'      => esc_html__( 'Style', 'localseomap-for-elementor' ),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Button hover background color', 'localseomap-for-elementor' ),
			'param_name' => 'input_button_hover_bg',
			'group'      => esc_html__( 'Style', 'localseomap-for-elementor' ),
		),
	),
);
