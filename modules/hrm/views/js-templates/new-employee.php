<div class="erp-employee-form erp-form">

    <?php do_action( 'erp-hr-employee-form-top' ); ?>

    <div class="erp-grid-container">
        <div class="row">
            <div class="col-2 left-column">
                <div class="erp-employee-modal-left">
                    <div class="photo-container">
                        <input type="hidden" name="personal[photo_id]" id="emp-photo-id" value="{{ data.avatar.id }}">

                        <# if ( data.avatar.id ) { #>
                            <img src="{{ data.avatar.url }}" alt="Image">
                            <a href="#" class="erp-remove-photo">&times;</a>
                        <# } else { #>
                            <img src="<?php echo WPERP_ASSETS . '/images/mystery-person.png'; ?>" alt="">

                            <a href="#" id="erp-set-emp-photo" class="button-primary">
                                <i class="fa fa-cloud-upload"></i>
                                <?php _e( 'Upload Photo', 'erp' ); ?>
                            </a>
                        <# } #>
                    </div>
                </div>
            </div>

            <div class="col-4 right-column">
                <div class="erp-employee-modal-right">

                    <div class="erp-grid-container employee-basic">
                        <div class="row">
                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label'       => __( 'First Name', 'erp' ),
                                    'name'        => 'personal[first_name]',
                                    'id'          => 'first_name',
                                    'value'       => '{{ data.name.first_name }}',
                                    'required'    => true,
                                    'custom_attr' => array( 'maxlength' => 30 )
                                ) ); ?>

                            </div>


                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label'       => __( 'Middle Name', 'erp' ),
                                    'name'        => 'personal[middle_name]',
                                    'id'          => 'middle_name',
                                    'value'       => '{{ data.name.middle_name }}',
                                    'custom_attr' => array( 'maxlength' => 30 )
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label'       => __( 'Last Name', 'erp' ),
                                    'name'        => 'personal[last_name]',
                                    'id'          => 'last_name',
                                    'value'       => '{{ data.name.last_name }}',
                                    'required'    => true,
                                    'custom_attr' => array( 'maxlength' => 30 )
                                ) ); ?>
                            </div>

                            <?php if ( current_user_can( 'erp_edit_employee' ) ): ?>
                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Employee ID', 'erp' ),
                                    'name'  => 'personal[employee_id]',
                                    'value' => '{{ data.employee_id }}'
                                ) ); ?>
                            </div>
                            <?php else: ?>
                                <input type="hidden" name="personal[employee_id]" value="{{ data.employee_id }}">
                            <?php endif; ?>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label'    => __( 'Email', 'erp' ),
                                    'name'     => 'user_email',
                                    'value'    => '{{ data.user_email }}',
                                    'id'       => 'erp-hr-user-email',
                                    'required' => true,
                                    'type'     => 'email'
                                ) ); ?>
                            </div>

                            <?php if ( current_user_can( 'erp_edit_employee' ) ): ?>
                            <div class="col-3" data-selected="{{ data.work.type }}">
                                <?php
                                erp_html_form_input( array(
                                    'label'    => __( 'Employee Type', 'erp' ),
                                    'name'     => 'work[type]',
                                    'value'    => '{{ data.work.type }}',
                                    'class'    => 'erp-hrm-select2',
                                    'type'     => 'select',
                                    'required' => true,
                                    'options'  => array( '' => __( '- Select -', 'erp' ) ) + erp_hr_get_employee_types()
                                ) );
                                ?>
                            </div>

                            <div class="col-3" style="clear:left" data-selected="{{ data.work.status }}">
                                <?php
                                erp_html_form_input( array(
                                    'label'    => __( 'Employee Status', 'erp' ),
                                    'name'     => 'work[status]',
                                    'value'    => '{{ data.work.status }}',
                                    'class'    => 'erp-hrm-select2',
                                    'type'     => 'select',
                                    'required' => true,
                                    'options'  => array( '' => __( '- Select -', 'erp' ) ) + erp_hr_get_employee_statuses()
                                ) );
                                ?>
                            </div>

                            <div class="col-3">
                                <?php
                                    erp_html_form_input( array(
                                        'label'    => __( 'Date of Hire', 'erp' ),
                                        'name'     => 'work[hiring_date]',
                                        'value'    => '{{ data.work.hiring_date }}',
                                        'required' => true,
                                        'type'     => 'text',
                                        'class'    => 'erp-date-field'
                                    ) );
                                ?>
                            </div>
                            <?php endif; ?>

                        </div>
                        <?php do_action( 'erp-hr-employee-form-basic' ); ?>
                    </div>

                    <p class="advanced-fields">
                        <input type="checkbox" id="advanced_fields">
                        <label for="advanced_fields">Show Advanced Fields</label>
                    </p>

                    <?php if ( current_user_can( 'erp_edit_employee' ) ): ?>

                        <div class="erp-modal-advanced-fields employee-work">
                            <div class="erp-grid-container">
                                <# if ( ! data.id ) { #>
                                <div class="col-6">
                                    <h3 class="modal-section-title"><?php _e( 'Work', 'erp' ); ?></h3>
                                </div>

                                <div class="col-3 erp-hr-js-department" data-selected="{{ data.work.department }}">
                                    <?php erp_html_form_input( array(
                                        'label'       => __( 'Department', 'erp' ),
                                        'name'        => 'work[department]',
                                        'value'       => '',
                                        'class'       => 'erp-hrm-select2-add-more erp-hr-dept-drop-down',
                                        'custom_attr' => array( 'data-id' => 'erp-new-dept' ),
                                        'type'        => 'select',
                                        'options'     => erp_hr_get_departments_dropdown_raw()
                                    ) ); ?>
                                </div>

                                <div class="col-3" data-selected="{{ data.work.designation }}">
                                    <?php erp_html_form_input( array(
                                        'label'       => __( 'Job Title', 'erp' ),
                                        'name'        => 'work[designation]',
                                        'value'       => '{{ data.work.designation }}',
                                        'class'       => 'erp-hrm-select2-add-more erp-hr-desi-drop-down',
                                        'custom_attr' => array( 'data-id' => 'erp-new-designation' ),
                                        'type'        => 'select',
                                        'options'     => erp_hr_get_designation_dropdown_raw()
                                    ) ); ?>
                                </div>

                                <div class="col-3" data-selected="{{ data.work.location }}">
                                    <?php erp_html_form_input( array(
                                        'label'       => __( 'Location', 'erp' ),
                                        'name'        => 'work[location]',
                                        'value'       => '{{ data.work.location }}',
                                        'custom_attr' => array( 'data-id' => 'erp-company-new-location' ),
                                        // 'class'   => 'erp-hrm-select2-add-more erp-hr-location-drop-down',
                                        'class'       => 'erp-hrm-select2',
                                        'type'        => 'select',
                                        'options'     => erp_company_get_location_dropdown_raw()
                                    ) ); ?>
                                </div>

                                <div class="col-3" data-selected="{{ data.work.reporting_to }}">
                                    <?php erp_html_form_input( array(
                                        'label'   => __( 'Reporting To', 'erp' ),
                                        'name'    => 'work[reporting_to]',
                                        'value'   => '{{ data.work.reporting_to }}',
                                        'class'   => 'erp-hrm-select2',
                                        'type'    => 'select',
                                        'id'      => 'work_reporting_to',
                                        'options' => erp_hr_get_employees_dropdown_raw()
                                    ) ); ?>
                                </div>
                                <# } #>

                                <div class="col-3" data-selected="{{ data.work.hiring_source }}">
                                    <?php erp_html_form_input( array(
                                        'label'   => __( 'Source of Hire', 'erp' ),
                                        'name'    => 'work[hiring_source]',
                                        'value'   => '{{ data.work.hiring_source }}',
                                        'class'   => 'erp-hrm-select2',
                                        'type'    => 'select',
                                        'options' => array( '-1' => __( '- Select -', 'erp' ) ) + erp_hr_get_employee_sources()
                                    ) ); ?>
                                </div>

                            <# if ( ! data.id ) { #>
                                <div class="col-3">
                                    <?php erp_html_form_input( array(
                                        'label'   => __( 'Pay Rate', 'erp' ),
                                        'name'    => 'work[pay_rate]',
                                        'value'   => '{{ data.work.pay_rate }}',
                                        'type'    => 'text'
                                    ) ); ?>
                                </div>

                                <div class="col-3" data-selected="{{ data.work.pay_type }}">
                                    <?php erp_html_form_input( array(
                                        'label'   => __( 'Pay Type', 'erp' ),
                                        'name'    => 'work[pay_type]',
                                        'value'   => '{{ data.work.pay_type }}',
                                        'class'   => 'erp-hrm-select2',
                                        'type'    => 'select',
                                        'options' => array( '-1' => __( '- Select -', 'erp' ) ) + erp_hr_get_pay_type()
                                    ) ); ?>
                                </div>

                            <# } #>

                                <div class="col-3">
                                    <?php erp_html_form_input( array(
                                        'label'   => __( 'Work Phone', 'erp' ),
                                        'name'    => 'personal[work_phone]',
                                        'value'   => '{{ data.personal.work_phone }}'
                                    ) ); ?>
                                </div>

                            <?php do_action( 'erp-hr-employee-form-work' ); ?>

                            </div>
                        </div>

                    <?php endif; ?>

                    <div class="erp-modal-advanced-fields employee-personal">

                        <div class="erp-grid-container">
                            <div class="col-6">
                                <h3 class="modal-section-title"><?php _e( 'Personal Details', 'erp' ); ?></h3>
                            </div>
                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Mobile', 'erp' ),
                                    'name'  => 'personal[mobile]',
                                    'value' => '{{ data.personal.mobile }}'
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Phone', 'erp' ),
                                    'name'  => 'personal[phone]',
                                    'value' => '{{ data.personal.phone }}'
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Other Email', 'erp' ),
                                    'name'  => 'personal[other_email]',
                                    'value' => '{{ data.personal.other_email }}',
                                    'type'  => 'email'
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Date of Birth', 'erp' ),
                                    'name'  => 'work[date_of_birth]',
                                    'value' => '{{ data.work.date_of_birth }}',
                                    'class' => 'erp-date-field'
                                ) ); ?>
                            </div>

                            <div class="col-3" data-selected="{{ data.personal.nationality }}">
                                <?php erp_html_form_input( array(
                                    'label'   => __( 'Nationality', 'erp' ),
                                    'name'    => 'personal[nationality]',
                                    'value'   => '{{ data.personal.nationality }}',
                                    'class'   => 'erp-hrm-select2',
                                    'type'    => 'select',
                                    'options' => \WeDevs\ERP\Countries::instance()->get_countries( '-1' )
                                ) ); ?>
                            </div>

                            <div class="col-3" data-selected="{{ data.personal.gender }}">
                                <?php erp_html_form_input( array(
                                    'label'   => __( 'Gender', 'erp' ),
                                    'name'    => 'personal[gender]',
                                    'value'   => '{{ data.personal.gender }}',
                                    'class'   => 'erp-hrm-select2',
                                    'type'    => 'select',
                                    'options' => erp_hr_get_genders()
                                ) ); ?>
                            </div>

                            <div class="col-3" data-selected="{{ data.personal.marital_status }}">
                                <?php erp_html_form_input( array(
                                    'label'   => __( 'Marital Status', 'erp' ),
                                    'name'    => 'personal[marital_status]',
                                    'value'   => '{{ data.personal.marital_status }}',
                                    'class'   => 'erp-hrm-select2',
                                    'type'    => 'select',
                                    'options' => erp_hr_get_marital_statuses()
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Driving License', 'erp' ),
                                    'name'  => 'personal[driving_license]',
                                    'value' => '{{ data.personal.driving_license }}'
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Hobbies', 'erp' ),
                                    'name'  => 'personal[hobbies]',
                                    'value' => '{{ data.personal.hobbies }}'
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Website', 'erp' ),
                                    'name'  => 'personal[user_url]',
                                    'value' => '{{ data.personal.user_url }}',
                                    'type'  => 'url'
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Address 1', 'erp' ),
                                    'name'  => 'personal[street_1]',
                                    'value' => '{{ data.personal.street_1 }}'
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Address 2', 'erp' ),
                                    'name'  => 'personal[street_2]',
                                    'value' => '{{ data.personal.street_2 }}'
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'City', 'erp' ),
                                    'name'  => 'personal[city]',
                                    'value' => '{{ data.personal.city }}'
                                ) ); ?>
                            </div>

                            <div class="col-3" data-selected="{{ data.personal.country }}">
                                <label for="erp-popup-country"><?php _e( 'Country', 'erp' ); ?></label>
                                <select name="personal[country]" id="erp-popup-country"
                                        class="erp-country-select select2" data-parent="ol">
                                    <?php $country = \WeDevs\ERP\Countries::instance(); ?>
                                    <?php echo $country->country_dropdown( erp_get_country() ); ?>
                                </select>
                            </div>

                            <div class="col-3 state-field" data-selected="{{ data.personal.state }}" style="clear:left">
                                <?php erp_html_form_input( array(
                                    'label'   => __( 'Province / State', 'erp' ),
                                    'name'    => 'personal[state]',
                                    'id'      => 'erp-state',
                                    'type'    => 'select',
                                    'class'   => 'erp-state-select',
                                    'options' => array( '' => __( '- Select -', 'erp' ) )
                                ) ); ?>
                            </div>

                            <div class="col-3">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Post Code/Zip Code', 'erp' ),
                                    'name'  => 'personal[postal_code]',
                                    'value' => '{{ data.personal.postal_code }}'
                                ) ); ?>
                            </div>

                            <div class="col-6">
                                <?php erp_html_form_input( array(
                                    'label' => __( 'Biography', 'erp' ),
                                    'name'  => 'personal[description]',
                                    'value' => '{{ data.personal.description }}',
                                    'type'  => 'textarea'
                                ) ); ?>
                            </div>
                        </div>

                        <?php do_action( 'erp-hr-employee-form-personal' ); ?>

                       <# if ( ! data.id ) { #>

                        <div class="row">
                            <div class="col-6">
                                <?php erp_html_form_input( array(
                                    'label'       => __( 'Notification', 'erp' ),
                                    'name'        => 'user_notification',
                                    'help'        => __( 'Send the employee an welcome email.', 'erp' ),
                                    'type'        => 'checkbox',
                                ) ); ?>
                            </div>

                            <div class=" col-6 show-if-notification" style="display:none">
                                <?php erp_html_form_input( array(
                                    'label'       => '&nbsp;',
                                    'name'        => 'login_info',
                                    'help'        => __( 'Send the login details as well. If <code>{login_info}</code> present.', 'erp' ),
                                    'type'        => 'checkbox',
                                ) ); ?>
                            </div>
                        </div>

                        <# } #>

                        <?php do_action( 'erp-hr-employee-form-bottom' ); ?>

                        <input type="hidden" name="user_id" id="erp-employee-id" value="{{ data.user_id }}">
                        <input type="hidden" name="action" id="erp-employee-action" value="erp-hr-employee-new">
                        <?php wp_nonce_field( 'wp-erp-hr-employee-nonce' ); ?>
                        <?php do_action( 'erp_hr_employee_form' ); ?>

                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
