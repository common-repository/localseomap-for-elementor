<?php
/**
 * Type: wpbakery
 */

return array(
	'name'        => esc_html__( 'Projects','localseomap-for-elementor'),
	'description' => esc_html__( 'Profolio projects list','localseomap-for-elementor'),
	'base'        => 'projects',
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
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Show pagination','localseomap-for-elementor'),
			'param_name' => 'show_pagination',
			'value'      => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),
		array(
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Number of columns','localseomap-for-elementor'),
			'param_name' => 'number_columns',
			'value'      => array(
				esc_html__( 'One column','localseomap-for-elementor')    => 'profolio-col-md-12',
				esc_html__( 'Two columns','localseomap-for-elementor')   => 'profolio-col-md-6',
				esc_html__( 'Three columns','localseomap-for-elementor') => 'profolio-col-md-4',
				esc_html__( 'Four columns','localseomap-for-elementor')  => 'profolio-col-md-3',
			),
			'std'        => 'profolio-col-md-6',
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),
		array(
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Style','localseomap-for-elementor'),
			'param_name' => 'style',
			'value'      => array(
				esc_html__( 'Default','localseomap-for-elementor')      => '',
				esc_html__( 'Project list','localseomap-for-elementor') => 'project_list',
			),
			'std'        => '',
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
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Category shape background','localseomap-for-elementor'),
			'param_name' => 'category_background_shape',
			'group'      => esc_html__( 'Style','localseomap-for-elementor'),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Category shape color','localseomap-for-elementor'),
			'param_name' => 'category_color_shape',
			'group'      => esc_html__( 'Style','localseomap-for-elementor'),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Title color','localseomap-for-elementor'),
			'param_name' => 'title_color',
			'group'      => esc_html__( 'Style','localseomap-for-elementor'),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Pagination text color','localseomap-for-elementor'),
			'param_name' => 'pagination_text_color',
			'group'      => esc_html__( 'Style','localseomap-for-elementor'),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Pagination background color','localseomap-for-elementor'),
			'param_name' => 'pagination_background_color',
			'group'      => esc_html__( 'Style','localseomap-for-elementor'),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Pagination hover background color','localseomap-for-elementor'),
			'param_name' => 'pagination_hover_background_color',
			'group'      => esc_html__( 'Style','localseomap-for-elementor'),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Pagination active text color','localseomap-for-elementor'),
			'param_name' => 'pagination_active_text_color',
			'group'      => esc_html__( 'Style','localseomap-for-elementor'),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Pagination active background color','localseomap-for-elementor'),
			'param_name' => 'pagination_active_background_color',
			'group'      => esc_html__( 'Style','localseomap-for-elementor'),
		),
		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Remove background?','localseomap-for-elementor'),
			'param_name' => 'remove_bg',
			'value'      => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),
		array(
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Image size','localseomap-for-elementor'),
			'param_name' => 'image_size',
			'value'      => localseomap_get_thumbnail_sizes( 'right' ),
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),
		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Pro project','localseomap-for-elementor'),
			'param_name' => 'pro_project',
			'value'      => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),
		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Projects tags instead industry','localseomap-for-elementor'),
			'param_name' => 'tags_instead_industry',
			'value'      => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),


	),
);
