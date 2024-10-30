<?php
/**
 * User: localseomap
 * Date: 25.10.2019
 * @package LocalSeoMap/Leads
 */

namespace LocalSeoMap;

if ( ! defined( 'WPINC' ) ) {
	die;
}


class Leads extends Admin {

	/**
	 * Leads constructor.
	 * @since  1.0.0
	 * @access public
	 */
	public function __construct() {

	}

	/**
	 * Init Leads.
	 * @since  1.0.0
	 * @access public
	 */
	public function init() {
		add_action( 'wp_ajax_nopriv_submit_leads_form', array( $this, 'submit_leads_form' ) );
		add_action( 'wp_ajax_submit_leads_form', array( $this, 'submit_leads_form' ) );

	}

	/**
	 * Submit leads form.
	 */
	public function submit_leads_form() {

		parse_str( $_POST['request'], $request );

		$body = json_encode( $request );

		$options = array(
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'body'    => $body
		);

		$url = 'http://crm.leads180.com/profolio-crm/import-from-external-services';

		$response = wp_remote_post( $url, $options );

		$message = json_decode( $response['body'] );

		if ( $message->message == 'The phone number has invalid format' ) {
			$message->message = 'The phone number has invalid format. Thanks! Your information was successfully submitted.';
		}

		if ( $message->message == 'New lead has been successfully added.' ) {
			$message->message = 'Thanks! Your information was successfully submitted.';
		}


		$response_array = array(
			'message' => $message->message,
			'status'  => $response['response']['code']
		);

		echo json_encode( $response_array );

		wp_die();
	}

	/**
	 * Display Leads popup.
	 */
	public function leads_popup() {

		if ( ! get_option( '_pap_show_leads_button' ) ) {
			return;
		}

		$pap_color_btn              = get_option( '_pap_color_btn' );
		$pap_bg_color_btn           = get_option( '_pap_bg_color_btn' );
		$pap_font_size_btn          = get_option( '_pap_font_size_btn' );
		$field_external_services_id = get_option( '_field_external_services_id' );
		$pap_title_above_form       = get_option( '_pap_title_above_form' );
		$pap_subtitle_above_form    = get_option( '_pap_subtitle_above_form' );

		$pap_change_fn_field       = get_option( '_pap_change_fn_field' ) ? get_option( '_pap_change_fn_field' ) : esc_html__( 'Full Name', 'localseomap-for-elementor' );
		$pap_change_street_field   = get_option( '_pap_change_street_field' ) ? get_option( '_pap_change_street_field' ) : esc_html__( 'Street Address', 'localseomap-for-elementor' );
		$pap_change_city_field     = get_option( '_pap_change_city_field' ) ? get_option( '_pap_change_city_field' ) : esc_html__( 'City', 'localseomap-for-elementor' );
		$pap_change_state_field    = get_option( '_pap_change_state_field' ) ? get_option( '_pap_change_state_field' ) : esc_html__( 'State', 'localseomap-for-elementor' );
		$pap_change_zip_field      = get_option( '_pap_change_zip_field' ) ? get_option( '_pap_change_zip_field' ) : esc_html__( 'Zip', 'localseomap-for-elementor' );
		$pap_change_phone_field    = get_option( '_pap_change_phone_field' ) ? get_option( '_pap_change_phone_field' ) : esc_html__( 'Phone', 'localseomap-for-elementor' );
		$pap_change_email_field    = get_option( '_pap_change_email_field' ) ? get_option( '_pap_change_email_field' ) : esc_html__( 'Email', 'localseomap-for-elementor' );
		$pap_change_service_field  = get_option( '_pap_change_service_field' ) ? get_option( '_pap_change_service_field' ) : esc_html__( 'Service Type', 'localseomap-for-elementor' );
		$pap_change_detail_1_field = get_option( '_pap_change_detail_1_field' ) ? get_option( '_pap_change_detail_1_field' ) : esc_html__( 'Details First', 'localseomap-for-elementor' );
		$pap_change_detail_2_field = get_option( '_pap_change_detail_2_field' ) ? get_option( '_pap_change_detail_2_field' ) : esc_html__( 'Details Second', 'localseomap-for-elementor' );
		$pap_change_detail_3_field = get_option( '_pap_change_detail_3_field' ) ? get_option( '_pap_change_detail_3_field' ) : esc_html__( 'Details Third', 'localseomap-for-elementor' );
		$pap_change_form_text_btn  = get_option( '_pap_change_form_text_btn' ) ? get_option( '_pap_change_form_text_btn' ) : esc_html__( 'Submit', 'localseomap-for-elementor' );

		$pap_show_street_field   = get_option( '_pap_show_street_field' );
		$pap_show_city_field     = get_option( '_pap_show_city_field' );
		$pap_show_state_field    = get_option( '_pap_show_state_field' );
		$pap_show_service_field  = get_option( '_pap_show_service_field' );
		$pap_show_detail_1_field = get_option( '_pap_show_detail_1_field' );
		$pap_show_detail_2_field = get_option( '_pap_show_detail_2_field' );
		$pap_show_detail_3_field = get_option( '_pap_show_detail_3_field' );
		?>

        <style type="text/css">
            .profolio-leads-btn {
            <?php if ( ! empty( $pap_font_size_btn ) ) : ?> font-size: <?php echo esc_attr($pap_font_size_btn); ?>px;
            <?php endif; ?><?php if ( ! empty( $pap_color_btn ) ) : ?> color: <?php echo esc_attr($pap_color_btn); ?>;
            <?php endif; ?><?php if ( ! empty( $pap_bg_color_btn ) ) : ?> background-color: <?php echo esc_attr($pap_bg_color_btn); ?>;
                border-color: <?php echo esc_attr($pap_bg_color_btn); ?>;
            <?php endif; ?>
            }

            .profolio-leads-btn:focus {
            <?php if ( ! empty( $pap_color_btn ) ) : ?> color: <?php echo esc_attr($pap_color_btn); ?>;
            <?php endif; ?>
            }

            .profolio-leads-btn:hover {
            <?php if ( ! empty( $pap_bg_color_btn ) ) : ?> background-color: <?php echo esc_attr($pap_bg_color_btn); ?>;
                border-color: <?php echo esc_attr($pap_bg_color_btn); ?>;
            <?php endif; ?>
            }

            body.admin-bar .profolio-leads-popup .mfp-close {
                width: 45px;
                height: 45px;
            }
        </style>

        <div id="profolio-leads-popup" class="profolio-leads-popup mfp-hide">

            <form action="#" class="profolio-leads-form">
                <div class="profolio-container">
                    <div class="row">

                        <div class="profolio-col-md-12">
							<?php if ( ! empty( $pap_title_above_form ) ) : ?>
                                <h2 class="profolio-leads-heading"><?php echo esc_html( $pap_title_above_form ); ?></h2>
							<?php endif; ?>

							<?php if ( ! empty( $pap_subtitle_above_form ) ) : ?>
                                <h6 class="profolio-leads-heading"><?php echo esc_html( $pap_subtitle_above_form ); ?></h6>
							<?php endif; ?>
                        </div>

                        <div class="profolio-col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_fn_field ); ?>" name="full_name" required>
                            </div>
                        </div>

						<?php if ( isset( $pap_show_street_field ) && $pap_show_street_field ) : ?>
                            <div class="profolio-col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_street_field ); ?>" name="field_lead_street_address">
                                </div>
                            </div>
						<?php endif; ?>

						<?php if ( isset( $pap_show_city_field ) && $pap_show_city_field ) : ?>
                            <div class="profolio-col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_city_field ); ?>" name="field_lead_city" required>
                                </div>
                            </div>
						<?php endif; ?>

						<?php if ( isset( $pap_show_state_field ) && $pap_show_state_field ) : ?>
                            <div class="profolio-col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_state_field ); ?>" name="field_lead_state">
                                </div>
                            </div>
						<?php endif; ?>

                        <div class="profolio-col-md-6">
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="<?php echo esc_attr( $pap_change_email_field ); ?>" name="email" required>
                            </div>
                        </div>
                        <div class="profolio-col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_zip_field ); ?>" name="field_lead_zip" required>
                            </div>
                        </div>
                        <div class="profolio-col-md-6">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_phone_field ); ?>" name="phone" required>
                            </div>
                        </div>

						<?php if ( isset( $pap_show_service_field ) && $pap_show_service_field ) : ?>
                            <div class="profolio-col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_service_field ); ?>" name="field_service_type">
                                </div>
                            </div>
						<?php endif; ?>

						<?php if ( isset( $pap_show_detail_1_field ) && $pap_show_detail_1_field ) : ?>
                            <div class="profolio-col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_detail_1_field ); ?>" name="field_more_lead_details_first">
                                </div>
                            </div>
						<?php endif; ?>

						<?php if ( isset( $pap_show_detail_2_field ) && $pap_show_detail_2_field ) : ?>
                            <div class="profolio-col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_detail_2_field ); ?>" name="field_more_lead_details_second">
                                </div>
                            </div>
						<?php endif; ?>

						<?php if ( isset( $pap_show_detail_3_field ) && $pap_show_detail_3_field ) : ?>
                            <div class="profolio-col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="<?php echo esc_attr( $pap_change_detail_3_field ); ?>" name="field_more_lead_details_third">
                                </div>
                            </div>
						<?php endif; ?>

                        <div class="profolio-col-md-12">
                            <div class="form-group">
								<?php if ( ! empty( $field_external_services_id ) ) : ?>
                                    <input type="hidden" name="field_external_services_id" value="<?php echo esc_attr( $field_external_services_id ); ?>">
								<?php endif; ?>

								<?php if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) : ?>
                                    <input type="hidden" name="hidden_referrer_page_url" value="<?php echo esc_attr( $_SERVER['HTTP_REFERER'] ); ?>">
								<?php endif; ?>

                                <input type="hidden" name="hidden_submitted_page_url" value="<?php echo get_permalink(); ?>">

                                <button type="submit" class="btn btn-primary profolio-leads-btn"><?php echo esc_attr( $pap_change_form_text_btn ); ?></button>

                                <p class="profolio-leads-text profolio-leads-message"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

		<?php
	}

}
