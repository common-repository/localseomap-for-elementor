<div class="profolio-row">
    <div class="profolio-col-12">
        <form class="profolio-add-file-form" method="POST" action="">
            <hr class="profolio-form-divider">
            <div class="media-field">
                <div class="profolio-media-preview">
					<?php localseomap_the_project_thumbnail(); ?>
                </div>
				<?php $hidden_class = localseomap_project_data_attr( 'thumbnail_id' ) ? 'hidden' : ''; ?>
                <button class="profolio-add-button profolio_add_images profolio-main-img-btn <?php echo esc_attr( $hidden_class ); ?>" data-multiple="false"
                        data-name="profolio_project_main_image">
					<?php esc_html_e( 'Add Main Image', 'localseomap-for-elementor' ); ?>
                </button>
            </div>
            <div class="profolio-inpt">
                <span class="input-title"><?php esc_html_e( 'Title', 'localseomap-for-elementor' ); ?></span>
                <input required class="profolio-text-input" name="name" type="text"
                       value="<?php echo localseomap_project_data_attr( 'post_title' ); ?>"
                       placeholder="<?php esc_attr_e( 'Enter project name', 'localseomap-for-elementor' ); ?>">
            </div>
            <div class="profolio-inpt">
                <span class="input-title"><?php esc_html_e( 'Description', 'localseomap-for-elementor' ); ?></span>
                <textarea name="profolio_description" class="profolio-textarea" id="profolio_description" cols="30"
                          rows="10"><?php echo localseomap_project_data_attr( 'post_content' ); ?></textarea>
            </div>
            <div class="profolio-inpt">
                <span class="input-title"><?php esc_html_e( 'Date', 'localseomap-for-elementor' ); ?></span>
                <input name="start_date" value="<?php echo localseomap_project_data_attr( 'start_date' ); ?>" class="profolio-text-input profolio-datepick">
            </div>
            <div class="profolio-inpt">
                <span class="input-title"><?php esc_html_e( 'Address', 'localseomap-for-elementor' ); ?></span>
                <input class="profolio-text-input profolio-loc-input" name="profolio_location" type="text"
                       value="<?php echo localseomap_project_data_attr( 'address' ); ?>"
                       placeholder="<?php esc_attr_e( 'Enter a location', 'localseomap-for-elementor' ); ?>" autocomplete="on">

                <input name="profolio_location_lat" id="profolio-loc-lat" type="hidden" value="<?php echo localseomap_project_data_attr( 'latitude' ); ?>">
                <input name="profolio_location_lng" id="profolio-loc-lng" type="hidden" value="<?php echo localseomap_project_data_attr( 'longitude' ); ?>">

                <input name="profolio_city" id="profolio-loc-city" type="hidden" value="<?php echo localseomap_project_data_attr( 'city' ); ?>">
                <input name="profolio_county" id="profolio-loc-county" type="hidden" value="<?php echo localseomap_project_data_attr( 'county' ); ?>">
                <input name="profolio_province" id="profolio-loc-province" type="hidden" value="<?php echo localseomap_project_data_attr( 'province' ); ?>">
                <input name="profolio_country" id="profolio-loc-country" type="hidden" value="<?php echo localseomap_project_data_attr( 'country' ); ?>">
            </div>
            <div class="profolio-inpt">
                <span class="input-title"><?php esc_html_e( 'Type', 'localseomap-for-elementor' ); ?></span>
                <div class="profolio-check-frame">
					<?php
					$id          = localseomap_project_data_attr( 'ID' );
					$project_pro = localseomap_project_data_attr( 'field_project_pro' );
					?>
                    <select class="profolio-text-input" name="field_project_pro">
                        <option <?php selected( $project_pro, '1' ); ?> value="1"><?php esc_html_e( 'Project', 'localseomap-for-elementor' ); ?></option>
                        <option <?php selected( $project_pro, '2' ); ?> value="2"><?php esc_html_e( 'Story', 'localseomap-for-elementor' ); ?></option>
                    </select>
                </div>
            </div>
            <div class="profolio-inpt">

				<?php
				$pap_allowed_industry = carbon_get_theme_option( 'pap_allowed_industry' );

				if ( ! empty( $pap_allowed_industry ) && is_array( $pap_allowed_industry ) ) {
					$ids = $pap_allowed_industry;

					foreach ( $pap_allowed_industry as $id ) {
						$term = get_term( $id, 'localseomap_industry' );
						if ( $term->parent !== 0 ) {
							$subterm = get_term( $term->parent, 'localseomap_industry' );
							if ( $subterm->parent !== 0 ) {
								$ids[] = $subterm->parent;
							}
							$ids[] = $term->parent;
						}
					}
				}

				$terms = get_terms( array(
					'taxonomy'   => 'localseomap_industry',
					'orderby'    => 'count',
					'hide_empty' => false,
					'include'    => ! empty( $ids ) ? $ids : array(),
				) );


				$current_industry_ids = localseomap_get_terms_ids();

				if ( ! empty( $terms ) && is_array( $terms ) ): ?>
                    <span class="input-title"><?php esc_html_e( 'Category', 'localseomap-for-elementor' ); ?></span>
                    <select class="js-example-basic-multiple profolio-text-input" name="project_industry[]"
                            multiple="multiple">
						<?php

						/** Get terms that have children */
						$hierarchy = _get_term_hierarchy( 'localseomap_industry' );
						foreach ( $terms as $term ) :
							if ( $term->parent ) {
								continue;
							}

							$selected = in_array( $term->term_id, $current_industry_ids ) ? ' selected="selected" ' : '';
							?>
                            <option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $term->name ); ?>"><?php echo esc_html( $term->name ); ?></option>
							<?php
							if ( $hierarchy[ $term->term_id ] ) {
								foreach ( $hierarchy[ $term->term_id ] as $child ) {
									/** Get the term object by its ID */
									$child        = get_term( $child, 'localseomap_industry' );
									$sub_selected = in_array( $child->term_id, $current_industry_ids ) ? ' selected="selected" ' : '';
									echo '<option ' . esc_attr( $sub_selected ) . ' value="' . esc_attr( $child->name ) . '"> - ' . esc_html( $child->name ) . '</option>';
								}
							}
						endforeach; ?>
                    </select>
				<?php else: ?>
                    <span class="input-title"><?php esc_html_e( 'New category', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" type="text" name="project_custom_industry[]" />
                    <a href="#." class="profolio-delete-inpt"><i class="pro_fa pro_fa-times-circle"></i></a>

				<?php endif; ?>

            </div>

            <div class="profolio-ad-inp-frame"></div>
            <div class="profolio-add-form-field"><?php esc_html_e( 'Add new category', 'localseomap-for-elementor' ); ?>
                <i class="pro_fa pro_fa-plus"></i></div>
            <div class="profolio-inpt" style="display: none">
                <span class="input-title"><?php esc_html_e( 'New category', 'localseomap-for-elementor' ); ?></span>
                <input class="profolio-text-input" type="text" name="project_custom_industry[]" />
                <a href="#." class="profolio-delete-inpt"><i class="pro_fa pro_fa-times-circle"></i></a>
            </div>

            <div class="profolio-inpt">

				<?php
				$terms = get_terms( array(
					'taxonomy'   => 'localseomap_project_tag',
					'hide_empty' => false,
				) );

				$current_tag_ids = localseomap_get_terms_ids( 'localseomap_project_tag' );
				?>
				<?php if ( ! empty( $terms ) && is_array( $terms ) ): ?>
                    <span class="input-title"><?php esc_html_e( 'Tag', 'localseomap-for-elementor' ); ?></span>
                    <select class="js-example-basic-multiple profolio-text-input" name="project_tag[]"
                            multiple="multiple">
						<?php foreach ( $terms as $term ) :
							$selected = in_array( $term->term_id, $current_tag_ids ) ? ' selected="selected" ' : '';
							?>
                            <option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_html( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
                    </select>
				<?php else: ?>
                    <span class="input-title"><?php esc_html_e( 'Tag', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" type="text" name="profolio_custom_tag[]" />
                    <a href="#." class="profolio-delete-inpt"><i class="pro_fa pro_fa-times-circle"></i></a>

				<?php endif; ?>
            </div>

            <div class="profolio-ad-inp-frame"></div>
            <div class="profolio-add-form-field"><?php esc_html_e( 'Add new tags', 'localseomap-for-elementor' ); ?> <i
                        class="pro_fa pro_fa-plus"></i></div>
            <div class="profolio-inpt" style="display: none">
                <span class="input-title"><?php esc_html_e( 'Tag', 'localseomap-for-elementor' ); ?></span>
                <input class="profolio-text-input" type="text" name="profolio_custom_tag[]" />
                <a href="#." class="profolio-delete-inpt"><i class="pro_fa pro_fa-times-circle"></i></a>
            </div>
            <div class="profolio-hidden-part-btn">
                <h3 class="profolio-form-title"><?php esc_html_e( 'Real estate:', 'localseomap-for-elementor' ); ?></h3>
                <i class="pro_fa pro_fa-chevron-down"></i>
            </div>

            <div class="profolio-hidden-part">

                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Price', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="field_real_estate_price" type="text"
                           value="<?php echo localseomap_project_data_attr( 'field_real_estate_price' ); ?>"
                           placeholder="<?php esc_attr_e( 'Enter price', 'localseomap-for-elementor' ); ?>">
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Sale type', 'localseomap-for-elementor' ); ?></span>
                    <select class="profolio-text-input" name="profolio_type">
                        <option <?php selected( localseomap_project_data_attr( 'field_real_estate_sale_type' ), '' ); ?> value=""><?php esc_html_e( 'None', 'localseomap-for-elementor' ); ?></option>
                        <option <?php selected( localseomap_project_data_attr( 'field_real_estate_sale_type' ), '0' ); ?> value="0"><?php esc_html_e( 'For sale', 'localseomap-for-elementor' ); ?></option>
                        <option <?php selected( localseomap_project_data_attr( 'field_real_estate_sale_type' ), '1' ); ?> value="1"><?php esc_html_e( 'For rent', 'localseomap-for-elementor' ); ?></option>
                    </select>
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Status', 'localseomap-for-elementor' ); ?></span>
                    <select class="profolio-text-input" name="profolio_status">
                        <option <?php selected( localseomap_project_data_attr( 'field_real_estate_status' ), '' ); ?> value=""><?php esc_html_e( 'None', 'localseomap-for-elementor' ); ?></option>
                        <option <?php selected( localseomap_project_data_attr( 'field_real_estate_status' ), '1' ); ?> value="1"><?php esc_html_e( 'Completed', 'localseomap-for-elementor' ); ?></option>
                        <option <?php selected( localseomap_project_data_attr( 'field_real_estate_status' ), '2' ); ?> value="2"><?php esc_html_e( 'On Hold', 'localseomap-for-elementor' ); ?></option>
                        <option <?php selected( localseomap_project_data_attr( 'field_real_estate_status' ), '3' ); ?> value="3"><?php esc_html_e( 'Dropped', 'localseomap-for-elementor' ); ?></option>
                    </select>
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'MLS ID', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="estate_mls_id" type="text" placeholder="id"
                           value="<?php echo localseomap_project_data_attr( 'field_real_estate_mls_id' ); ?>"
                    >
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Home size (sq ft)', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="profolio_home_size" type="number"
                           value="<?php echo localseomap_project_data_attr( 'field_real_estate_home_size' ); ?>"
                           placeholder="<?php esc_attr_e( 'Size', 'localseomap-for-elementor' ); ?>">
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Lot size (sq ft )', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="profolio_lot_size" type="number"
                           value="<?php echo localseomap_project_data_attr( 'field_real_estate_lot_size' ); ?>"
                           placeholder="<?php esc_attr_e( 'Size', 'localseomap-for-elementor' ); ?>">
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Bedrooms', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="profolio_bedrooms" type="number"
                           value="<?php echo localseomap_project_data_attr( 'field_real_estate_bedrooms' ); ?>"
                           placeholder="<?php esc_attr_e( 'Number', 'localseomap-for-elementor' ); ?>">
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Bathrooms', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="profolio_bathrooms" type="number"
                           value="<?php echo localseomap_project_data_attr( 'field_real_estate_bathrooms' ); ?>"
                           placeholder="<?php esc_attr_e( 'Number', 'localseomap-for-elementor' ); ?>">
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Year built', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="year_built"
                           value="<?php echo localseomap_project_data_attr( 'field_real_estate_year_built' ); ?>"
                           placeholder="<?php esc_attr_e( 'Number', 'localseomap-for-elementor' ); ?>">
                </div>
            </div>
            <hr class="profolio-form-divider">

            <div class="profolio-hidden-part-btn">
                <h3 class="profolio-form-title"><?php esc_html_e( 'Testimonial:', 'localseomap-for-elementor' ); ?></h3>
                <i class="pro_fa pro_fa-chevron-down"></i>
            </div>

            <div class="profolio-hidden-part">
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Testimonial title', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="testimonial_title" type="text"
                           value="<?php echo localseomap_project_data_attr( 'field_story_testimonial_title' ); ?>"
                           placeholder="<?php esc_attr_e( 'Title', 'localseomap-for-elementor' ); ?>">
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Testimonial author', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="testimonial_author" type="text"
                           value="<?php echo localseomap_project_data_attr( 'field_story_testimonial_author' ); ?>"
                           placeholder="<?php esc_attr_e( 'Author Name', 'localseomap-for-elementor' ); ?>">
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Testimonial rating', 'localseomap-for-elementor' ); ?></span>
                    <input class="profolio-text-input" name="testimonial_rating" min="1" max="5" type="number"
                           value="<?php echo localseomap_project_data_attr( 'field_story_testimonial_rating' ); ?>"
                           placeholder="<?php esc_attr_e( 'Number', 'localseomap-for-elementor' ); ?>">
                </div>
                <div class="profolio-inpt">
                    <span class="input-title"><?php esc_html_e( 'Testimonial body', 'localseomap-for-elementor' ); ?></span>
                    <textarea class="profolio-textarea" name="testimonial_body" rows="6"
                              placeholder="<?php esc_attr_e( 'Text', 'localseomap-for-elementor' ); ?>"><?php echo localseomap_project_data_attr( 'field_story_testimonial_body' ); ?></textarea>
                </div>

            </div>
            <hr class="profolio-form-divider">

            <div class="profolio-hidden-part-btn">
                <h3 class="profolio-form-title"><?php esc_html_e( 'Gallery:', 'localseomap-for-elementor' ); ?></h3>
            </div>
            <div class="media-field profolio-media-gallery">
                <div class="profolio-media-preview">
					<?php
					localseomap_the_project_thumbnail( 'gallery' );
					?>
                </div>
                <button class="profolio-add-button profolio_add_images" data-multiple="add" data-name="profolio_project_media[]">+</button>
            </div>


            <button type="submit" class="profolio-add-button profolio_add_submit"><?php esc_html_e( 'Submit', 'localseomap-for-elementor' ); ?></button>
			<?php wp_nonce_field( 'profolio_action_nonce', 'profolio_action_nonce_field' ); ?>
            <input type="hidden" name="action" value="profolio_send_form">
            <input type="hidden" name="project_id" value="<?php echo localseomap_project_data_attr( 'ID' ); ?>">

        </form>
        <div class="profolio-ajax-form-loader"></div>
        <div class="profolio-form-success"></div>
        <div class="profolio-form-error"></div>
        <button class="profolio-add-new-project">
			<?php esc_html_e( 'Add New Project', 'localseomap-for-elementor' ); ?>
        </button>
    </div>
</div>

<script type="text/html" id="tmpl-localseomap-form-success-template">
	<?php esc_html_e( 'Project is added', 'localseomap-for-elementor' ); ?>
    <a href="{{{data.post_url}}}" target="_blank">{{{data.post_url}}}</a>
    <a href="{{{data.edit_url}}}">Edit this project</a>
</script>

<script type="text/html" id="tmpl-localseomap-gallery-item">
    <div class="profolio-pre-img" id="profolio-thumbnail-{{{data.image_id}}}" title="{{{data.title}}}">
        <img src="{{{data.image_url}}}">
        <i class="pro_fa pro_fa-times-circle profolio-close-img"></i>
        <input type="hidden" name="{{{data.field_name}}}" value="{{{data.image_id}}}">
    </div>
</script>


