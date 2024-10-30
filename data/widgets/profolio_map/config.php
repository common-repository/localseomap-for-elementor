<?php
/**
 * Type: wpbakery.
 * @package LocalSeoMap/Config.
 */

return array(
	'name'        => esc_html__( 'Map','localseomap-for-elementor'),
	'description' => esc_html__( 'Profolio map','localseomap-for-elementor'),
	'base'        => 'map',
	'icon'        => plugin_dir_url( __FILE__ ) . 'icon.svg?' . LOCALSEOMAP_VERSION,
	'category'    => 'Profolio widgets',
	'params'      => array(

		array(
			'type'       => 'textfield',
			'heading'    => esc_html__( 'Base location','localseomap-for-elementor'),
			'param_name' => 'location',
			'value'      => '',
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),
		array(
			'type'        => 'checkbox',
			'heading'     => esc_html__( 'Fullheight','localseomap-for-elementor'),
			'param_name'  => 'fullheight',
			'value'       => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'description' => esc_html__( 'Automatically center and zoom to fit all markers','localseomap-for-elementor'),
			'group'       => esc_html__( 'Map settings','localseomap-for-elementor'),
		),

		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Height','localseomap-for-elementor'),
			'param_name'  => 'height',
			'description' => esc_html__( 'Numbers only','localseomap-for-elementor'),
			'value'       => '',
			'dependency'  => array(
				'element' => 'fullheight',
				'value'   => '',
			),
			'group'       => esc_html__( 'General','localseomap-for-elementor'),
		),
		array(
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Get coordinates from','localseomap-for-elementor'),
			'param_name' => 'number_projects',
			'std'        => 'current_page',
			'value'      => array(
				esc_html__( 'Display on map projects from all pages','localseomap-for-elementor')          => 'all_pages',
				esc_html__( 'Display on map same projects as on current page','localseomap-for-elementor') => 'current_page',
			),
			'group'      => esc_html__( 'General','localseomap-for-elementor'),
		),

		/* The map settings. */
		array(
			'type'       => 'attach_image',
			'heading'    => esc_html__( 'Marker','localseomap-for-elementor'),
			'param_name' => 'marker',
			'value'      => '',
			'group'      => esc_html__( 'Map settings','localseomap-for-elementor'),
		),
		array(
			'type'             => 'textfield',
			'heading'          => esc_html__( 'Zoom','localseomap-for-elementor'),
			'param_name'       => 'zoom',
			'value'            => '',
			'edit_field_class' => 'vc_profolio-col-sm-4 top-padding',
			'description'      => esc_html__( 'From 0 to 20','localseomap-for-elementor'),
			'group'            => esc_html__( 'Map settings','localseomap-for-elementor'),
		),
		array(
			'type'        => 'checkbox',
			'heading'     => esc_html__( 'Zoom to fit all markers','localseomap-for-elementor'),
			'param_name'  => 'zoom_fit',
			'value'       => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'description' => esc_html__( 'Automatically center and zoom to fit all markers','localseomap-for-elementor'),
			'group'       => esc_html__( 'Map settings','localseomap-for-elementor'),
		),
		array(
			'type'        => 'checkbox',
			'heading'     => esc_html__( 'Enable scrollwheel','localseomap-for-elementor'),
			'description' => esc_html__( 'If false, disables scrollwheel zooming on the map.','localseomap-for-elementor'),
			'param_name'  => 'scrollwheel',
			'std'         => 'yes',
			'value'       => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'       => esc_html__( 'Map settings','localseomap-for-elementor'),
		),
		array(
			'type'             => 'checkbox',
			'heading'          => esc_html__( 'Enable tilted at 45° imagery','localseomap-for-elementor'),
			'param_name'       => 'imagery_45',
			'std'              => true,
			'edit_field_class' => 'vc_profolio-col-sm-6',
			'value'            => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'            => esc_html__( 'Map settings','localseomap-for-elementor'),
		),
		array(
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Select Map Types','localseomap-for-elementor'),
			'param_name'  => 'select_map_types',
			'description' => esc_html__( 'MapType defines the display and usage of map tiles and the translation of coordinate systems from screen to world coordinates','localseomap-for-elementor'),
			'value'       => array(
				esc_html__( 'Roadmap ','localseomap-for-elementor')  => 'roadmap',
				esc_html__( 'Satellite','localseomap-for-elementor') => 'satellite',
				esc_html__( 'Hybrid','localseomap-for-elementor')    => 'hybrid',
				esc_html__( 'Terrain','localseomap-for-elementor')   => 'terrain',
			),
			'std'         => 'roadmap',
			'group'       => esc_html__( 'Map settings','localseomap-for-elementor'),
		),


		/* The controls section. */
		array(
			'type'        => 'checkbox',
			'heading'     => esc_html__( 'Enable all UI','localseomap-for-elementor'),
			'description' => esc_html__( 'This property disable any automatic UI behavior from the Google Maps API.','localseomap-for-elementor'),
			'std'         => 'yes',
			'param_name'  => 'enable_default_ui',
			'value'       => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'group'       => esc_html__( 'Map controls','localseomap-for-elementor'),
		),
		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Show zoom control','localseomap-for-elementor'),
			'param_name' => 'zoom_control',
			'std'        => 'yes',
			'value'      => array( esc_html__( 'Yes, please','localseomap-for-elementor') => 'yes' ),
			'dependency' => array(
				'element' => 'enable_default_ui',
				'value'   => 'yes',
			),
			'group'      => esc_html__( 'Map controls','localseomap-for-elementor'),
		),

		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Show map type control','localseomap-for-elementor'),
			'param_name' => 'map_type_control',
			'std'        => '',
			'value'      => '',
			'dependency' => array(
				'element' => 'enable_default_ui',
				'value'   => 'yes',
			),
			'group'      => esc_html__( 'Map controls','localseomap-for-elementor'),
		),
		array(
			'type'       => 'wpl_input_switcher',
			'heading'    => esc_html__( 'Show scale control','localseomap-for-elementor'),
			'param_name' => 'scale_control',
			'std'        => '',
			'value'      => '',
			'dependency' => array(
				'element' => 'enable_default_ui',
				'value'   => 'yes',
			),
			'group'      => esc_html__( 'Map controls','localseomap-for-elementor'),
		),
		array(
			'type'       => 'wpl_input_switcher',
			'heading'    => esc_html__( 'Show fullscreen control','localseomap-for-elementor'),
			'param_name' => 'fullscreen_control',
			'std'        => '',
			'value'      => '',
			'dependency' => array(
				'element' => 'enable_default_ui',
				'value'   => 'yes',
			),
			'group'      => esc_html__( 'Map controls','localseomap-for-elementor'),
		),
		array(
			'type'       => 'wpl_input_switcher',
			'heading'    => esc_html__( 'Show rotate control','localseomap-for-elementor'),
			'param_name' => 'rotate_control',
			'std'        => 'yes',
			'value'      => '',
			'dependency' => array(
				'element' => 'enable_default_ui',
				'value'   => 'yes',
			),
			'group'      => esc_html__( 'Map controls','localseomap-for-elementor'),
		),


		/* The infowindow section. */
		array(
			'type'        => 'checkbox',
			'heading'     => esc_html__( 'Hide title','localseomap-for-elementor'),
			'param_name'  => 'infowindow_hide_title',
			'description' => esc_html__( 'Automatically center and zoom to fit all markers','localseomap-for-elementor'),
			'group'       => esc_html__( 'Infowindow','localseomap-for-elementor'),
		),
		array(
			'type'        => 'colorpicker',
			'heading'     => esc_html__( 'Background infowindow','localseomap-for-elementor'),
			'param_name'  => 'infowindow_color_bg',
			'description' => esc_html__( 'Color of the  infowindow','localseomap-for-elementor'),
			'group'       => esc_html__( 'Infowindow','localseomap-for-elementor'),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Color title','localseomap-for-elementor'),
			'param_name' => 'infowindow_color_title',
			'group'      => esc_html__( 'Infowindow','localseomap-for-elementor'),
		),
		array(
			'type'       => 'colorpicker',
			'heading'    => esc_html__( 'Color location','localseomap-for-elementor'),
			'param_name' => 'infowindow_color_location',
			'group'      => esc_html__( 'Infowindow','localseomap-for-elementor'),
		),

	),
);
