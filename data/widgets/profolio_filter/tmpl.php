<?php
/*
 * $atts - the widget params
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// get filter terms
$type_terms        = '';
$type_terms_filter = ! empty( $atts['tags_instead_industry'] ) ? 'filter_tags' : 'filter_terms';
$type_terms        = ! empty( $atts['tags_instead_industry'] ) ? 'localseomap_project_tag' : 'localseomap_industry';

if ( empty( $atts[ $type_terms_filter ] ) ) {
	$terms = get_terms(
		$type_terms,
		array( 'hide_empty' => true )
	);

	$terms_type = 'default';
} else {
	$terms      = $atts[ $type_terms_filter ];
	$terms_type = 'custom';
}

$uniqid = uniqid();

$styles = '';

if ( ! empty( $atts['input_icons_background'] ) ) {
	$styles .= '.profolio_wrapper_' . esc_attr( $uniqid ) . ' .input-group-text';
	$styles .= '{';
	$styles .= 'background-color: ' . esc_attr( $atts['input_icons_background'] ) . ';';
	$styles .= '}';

}

if ( ! empty( $atts['input_text_color'] ) ) {
	$styles .= '.profolio_wrapper_' . esc_attr( $uniqid ) . ' .form-control';
	$styles .= '{';
	$styles .= 'color: ' . esc_attr( $atts['input_text_color'] ) . ';';
	$styles .= '}';
}


$styles .= '.profolio_wrapper_' . esc_attr( $uniqid ) . ' .profolio-default-button';
$styles .= '{';
if ( ! empty( $atts['input_button_bg'] ) ) {
	$styles .= 'background-color: ' . esc_attr( $atts['input_button_bg'] ) . '!important;';
}
if ( ! empty( $atts['input_button_color'] ) ) {
	$styles .= 'color: ' . esc_attr( $atts['input_button_color'] ) . ' !important;';
}
$styles .= 'box-shadow: none;';
$styles .= 'border-color: none;';
$styles .= '}';


$styles .= '.profolio_wrapper_' . esc_attr( $uniqid ) . ' .profolio-default-button:focus,';
$styles .= '.profolio_wrapper_' . esc_attr( $uniqid ) . ' .profolio-default-button:hover';
$styles .= '{';

if ( ! empty( $atts['input_button_hover_color'] ) ) {
	$styles .= 'color: ' . esc_attr( $atts['input_button_hover_color'] ) . ' !important;';
}
if ( ! empty( $atts['input_button_hover_bg'] ) ) {
	$styles .= 'background-color: ' . esc_attr( $atts['input_button_hover_bg'] ) . '!important;';
}

$styles .= '}';

if ( ! empty( $atts['input_icons_background'] ) ) {
	$styles .= '.profolio_wrapper_' . esc_attr( $uniqid ) . ' .profolio-default-button.second-style';
	$styles .= '{';
	$styles .= 'background-color: transparent !important;';
	$styles .= 'border: 1px solid ' . esc_attr( $atts['input_icons_background'] ) . ' !important;';
	$styles .= 'color: ' . esc_attr( $atts['input_icons_background'] ) . ' !important;';
	$styles .= '}';
}

?>
<style>

    .profolio-default-input::-webkit-input-placeholder {
        color: <?php echo esc_attr($atts['input_text_color']);  ?>;
    }

    .profolio-default-input::-moz-placeholder {
        color: <?php echo esc_attr($atts['input_text_color']); ?>;
    }

    .profolio-default-input:-ms-input-placeholder {
        color: <?php echo esc_attr($atts['input_text_color']);   ?>;
    }

    .profolio-default-input:-moz-placeholder {
        color: <?php echo esc_attr($atts['input_text_color']); ?>;
    }

    <?php echo $styles; ?>
    .profolio_wrapper_<?php echo esc_attr( $uniqid ); ?> .profolio-default-button.reset-filter{
        border: 0 !important;
    }
</style>
<div class="profolio-search-bar profolio_wrapper_<?php echo esc_attr( $uniqid ); ?>">
    <form class="profolio-search-form js-profolio-search-form">
        <div class="profolio-row no-gutters">
            <div class="profolio-col-12 profolio-col-sm-12">
                <div class="input-group profolio-input-frame profolio-pr-15 profolio-ltr-pr-15">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="pro_fa pro_fa-map-marker-alt"></i></span>
                    </div>
                    <input type="text" class="profolio-loc-input form-control profolio-default-input js-filter-input" autocomplete="on" placeholder="" value="<?php esc_attr_e( 'Enter a location', 'localseomap-for-elementor' ); ?>">
                </div>
            </div>
            <div class="more-button-container">
                <a href="" class="btn btn-primary profolio-default-button profolio-input-more second-style"><?php esc_html_e( 'more', 'localseomap-for-elementor' ); ?></a>
                <div class="profolio-more-popup">
                    <div class="profolio-col-12 profolio-col-sm-4 profolio-col-md-12">
                        <div class="input-group profolio-input-frame profolio-md-rtl-pl-15">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="pro_fa pro_fa-road"></i></span>
                            </div>
                            <input type="text" id="profolio-radius-input" class="form-control profolio-default-input js-filter-input" value="100 mile(s)">
                        </div>
                    </div>
                    <div class="profolio-col-12 profolio-col-sm-4 profolio-col-md-12">
						<?php if ( ! empty( $terms ) ) : ?>
                            <div class="profolio-input-frame profolio-pr-15">
                                <div class="profolio-dropdown-frame">
                                    <div class="input-group profolio-open-dropdown">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="pro_fa pro_fa-th-list"></i></span>
                                        </div>
                                        <span class="profolio-dropdown-fake-button form-control profolio-default-input"><?php esc_html_e( 'Category', 'localseomap-for-elementor' ); ?></span>
                                    </div>
                                    <ul class="profolio-search-dropdown">
                                        <li>
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input profolio-sellect-all">
												<?php esc_html_e( 'Select', 'localseomap-for-elementor' ); ?>
                                                <span class="profolio-d-all profolio-d-true"><?php esc_html_e( 'All', 'localseomap-for-elementor' ); ?></span><span class="profolio-d-none"><?php esc_html_e( 'None', 'localseomap-for-elementor' ); ?></span>
                                            </label>
                                        </li>
										<?php foreach ( $terms as $key => $term ) : $key ++;
											$term_id   = $terms_type == 'custom' ? $term : $term->term_id;
											$term_name = $terms_type == 'custom' ? get_term_by( 'id', $term, $type_terms )->name : $term->name;
											?>
                                            <li>
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input js-form-check-input" value="<?php echo esc_attr( $term_id ); ?>">
													<?php echo esc_html( $term_name ); ?>
                                                </label>
                                            </li>
										<?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
						<?php endif; ?>
                    </div>

					<?php if ( ! empty( $atts['show_sale_type'] ) ): ?>
                        <div class="profolio-col-12 profolio-col-sm-4 profolio-col-md-12">
                            <div class="profolio-input-frame profolio-pr-15">
                                <div class="profolio-dropdown-frame">
                                    <div class="input-group profolio-open-dropdown">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="pro_fa pro_fa-th-list"></i></span>
                                        </div>
                                        <span class="profolio-dropdown-fake-button form-control profolio-default-input"><?php esc_html_e( 'Sale type', 'localseomap-for-elementor' ); ?></span>
                                    </div>
                                    <ul class="profolio-search-dropdown">
                                        <li>
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input profolio-sellect-all">
												<?php esc_html_e( 'Select', 'localseomap-for-elementor' ); ?>
                                                <span class="profolio-d-all profolio-d-true"><?php esc_html_e( 'All', 'localseomap-for-elementor' ); ?></span><span class="profolio-d-none"><?php esc_html_e( 'None', 'localseomap-for-elementor' ); ?></span>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="form-check-label">
                                                <input name="saleIds" type="checkbox" class="form-check-input js-form-sale-check-input" value="0">
												<?php esc_html_e( 'For sale', 'localseomap-for-elementor' ); ?>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="form-check-label">
                                                <input name="saleIds" type="checkbox" class="form-check-input js-form-sale-check-input" value="1">
												<?php esc_html_e( 'For rent', 'localseomap-for-elementor' ); ?>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
					<?php endif; ?>

					<?php if ( ! empty( $atts['show_status_field'] ) ): ?>

                        <div class="profolio-col-12 profolio-col-sm-4 profolio-col-md-12">
                            <div class="profolio-input-frame profolio-pr-15">
                                <div class="profolio-dropdown-frame">
                                    <div class="input-group profolio-open-dropdown">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="pro_fa pro_fa-th-list"></i></span>
                                        </div>
                                        <span class="profolio-dropdown-fake-button form-control profolio-default-input"><?php esc_html_e( 'Status', 'localseomap-for-elementor' ); ?></span>
                                    </div>
                                    <ul class="profolio-search-dropdown">
                                        <li>
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input profolio-sellect-all">
												<?php esc_html_e( 'Select', 'localseomap-for-elementor' ); ?>
                                                <span class="profolio-d-all profolio-d-true"><?php esc_html_e( 'All', 'localseomap-for-elementor' ); ?></span>
                                                <span class="profolio-d-none"><?php esc_html_e( 'None', 'localseomap-for-elementor' ); ?></span>
                                            </label>
                                        </li>

                                        <li>
                                            <label class="form-check-label">
                                                <input name="statusIds[]" type="checkbox" class="form-check-input js-form-status-check-input" value="0">
												<?php esc_html_e( 'Active', 'localseomap-for-elementor' ); ?>
                                            </label>
                                        </li>
                                        <li>

                                            <label class="form-check-label">
                                                <input name="statusIds[]" type="checkbox" class="form-check-input js-form-status-check-input" value="1">
												<?php esc_html_e( 'Sold', 'localseomap-for-elementor' ); ?>
                                            </label>
                                        </li>
                                        <li>

                                            <label class="form-check-label">
                                                <input name="statusIds[]" type="checkbox" class="form-check-input js-form-status-check-input" value="2">
												<?php esc_html_e( 'Inactive', 'localseomap-for-elementor' ); ?>
                                            </label>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>

					<?php endif; ?>

                    <button class="btn btn-primary profolio-default-button second-style reset-filter w-100">
                        <i class="pro_fa pro_fa-times"></i><span><?php esc_html_e( 'Reset filter', 'localseomap-for-elementor' ); ?></span>
                    </button>
                </div>


            </div>
            <div class="profolio-col-12 profolio-col-sm-3">
                <div class="profolio-input-frame profolio-rtl-pr-15">
                    <button type="submit" class="btn btn-primary profolio-default-button w-100">
                        <i class="pro_fa pro_fa-search"></i><span><?php esc_html_e( 'Search', 'localseomap-for-elementor' ); ?></span>
                    </button>
                </div>
            </div>
            <input type="hidden" name="tags_instead_industry" value="<?php echo esc_attr( $type_terms ); ?>">

			<?php if ( ! empty( $atts['pro_project'] ) ) : ?>
                <input type="hidden" name="pro_project" value="<?php echo esc_attr( $atts['pro_project'] ); ?>">
			<?php endif; ?>

            <input type="hidden" id="profolio-current-post-id" value="<?php echo esc_attr( get_the_ID() ); ?>">
        </div>
    </form>
</div>
