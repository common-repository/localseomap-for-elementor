<?php
/**
 * Type: wpbakery
 */

return array(
	'name'        => esc_html__( 'Media','localseomap-for-elementor'),
	'description' => esc_html__( 'Profolio media list','localseomap-for-elementor'),
	'base'        => 'media',
	'icon'        => plugin_dir_url( __FILE__ ) . 'icon.svg?' . LOCALSEOMAP_VERSION,
	'category'    => 'Profolio widgets',
	'params'      => array(
		array(
			'type'       => 'textfield',
			'heading'    => esc_html__( 'Posts per page','localseomap-for-elementor'),
			'param_name' => 'posts_per_page',
			'std'        => '26',
			'value'      => '',
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),

		array(
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Number of columns','localseomap-for-elementor'),
			'param_name' => 'number_columns',
			'value'      => array(
				esc_html__( 'One column','localseomap-for-elementor')    => 'profolio-col-lg-12',
				esc_html__( 'Two columns','localseomap-for-elementor')   => 'profolio-col-lg-6',
				esc_html__( 'Three columns','localseomap-for-elementor') => 'profolio-col-lg-4',
				esc_html__( 'Four columns','localseomap-for-elementor')  => 'profolio-col-lg-3',
			),
			'std'        => 'profolio-col-lg-6',
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),

		array(
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Order By','localseomap-for-elementor'),
			'param_name' => 'orderby',
			'value'      => array(
				esc_html__( 'Start Date','localseomap-for-elementor') => 'start_date',
				esc_html__( 'Unique ID','localseomap-for-elementor')  => 'unique_id',
				esc_html__( 'Date','localseomap-for-elementor')       => 'date',
				esc_html__( 'ID','localseomap-for-elementor')         => 'ID',
			),
			'std'        => 'date',
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),

		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Hide Title?','localseomap-for-elementor'),
			'param_name' => 'hide_title',
			'value'      => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),
		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Hide Location?','localseomap-for-elementor'),
			'param_name' => 'hide_location',
			'value'      => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),
		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Hide Industry?','localseomap-for-elementor'),
			'param_name' => 'hide_industry',
			'value'      => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),
	),
);
