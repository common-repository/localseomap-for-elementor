<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Profolio_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base {

	public function get_name() {
		return 'profolio';
	}

	public function get_label() {
		return __( 'Leads180', 'localseomap-for-elementor' );
	}

	private function get_profolio_settings() {

	}

	public function run( $record, $ajax_handler ) {

		$settings = $record->get( 'form_settings' );

		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$normalize_fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$normalize_fields[ $id ] = $field['value'];
		}

		$field_external_services_id = carbon_get_theme_option( 'field_external_services_id' );

		if ( ! empty( $field_external_services_id ) ) {
			$normalize_fields['field_external_services_id'] = $field_external_services_id;
		}

		if ( isset( $_POST['referrer'] ) ) {
			$normalize_fields['hidden_submitted_page_url'] = sanitize_text_field( $_POST['referrer'] );
		}

		if ( ! empty( $record->get( 'sent_data' )['hidden_referrer_page_url'] ) ) {
			$normalize_fields['hidden_referrer_page_url'] = $record->get( 'sent_data' )['hidden_referrer_page_url'];
		}

		$body = json_encode( $normalize_fields );

		$options = array(
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'body'    => $body
		);

		$url = 'http://crm.leads180.com/profolio-crm/import-from-external-services';

		$response = wp_remote_post( $url, $options );

	}


	/**
	 * Register Settings Section
	 * Registers the Action controls
	 * @access public
	 *
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {

//        $widget->start_controls_section(
//            'profolio_settings',
//            [
//                'label'     => __( 'Leads180 Settings', 'text-domain' ),
//                'condition' => [
//                    'submit_actions' => $this->get_name(),
//                ],
//            ]
//        );
//
//        $widget->add_control(
//            'profolio_form_id',
//            [
//                'label'       => __( 'Form id', 'text-domain' ),
//                'type'        => \Elementor\Controls_Manager::TEXT,
//                'label_block' => true,
//                'separator'   => 'before',
//                'description' => __( 'Enter the form id', 'text-domain' ),
//            ]
//        );
//
//        $widget->end_controls_section();

	}

	/**
	 * On Export
	 * Clears form settings on export
	 * @access Public
	 *
	 * @param array $element
	 */
	public function on_export( $element ) {

	}
}
