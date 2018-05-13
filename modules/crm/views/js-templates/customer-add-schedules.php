<div class="erp-crm-new-schedule-wrapper">
    <# if( new Date( data.current_date ) >= new Date() ) { #>
        <div class="feed-schedule-wrapper">

            <div class="schedule-title-assign-user">
                <p class="erp-left schedule-title">
                    <input type="text" required name="schedule_title" placeholder="<?php _e( 'Enter Schedule Title', 'erp' ); ?>">
                </p>

                <p class="erp-left schedule-assign-user">
                    <select name="user_id" required class="erp-crm-contact-list-dropdown" id="assign-contact" style="width: 100%" data-types="contact,company" data-placeholder="<?php _e( 'Assign to a contact..', 'erp' ) ?>">
                        <option value=""></option>
                    </select>
                </p>
                <div class="clearfix"></div>
            </div>

            <div class="schedule-datetime">
                <p class="erp-left schedule-start">
                    <label><?php _e( 'Start', 'erp' ); ?></label>
                    <span class="sep">:</span>
                    <span class="value">
                        <input name="start_date" type="hidden" value="{{ data.current_date }}">
                        <input class="start-date erp-date-field" name="start_date" type="text" value="{{ data.current_date }}" disabled="disabled" placeholder="yy-mm-dd"><span class="datetime-sep">@</span>
                        <input class="start-time erp-time-field" required name="start_time" type="text" placeholder="12.00pm" size="10">
                    </span>
                </p>

                <p class="erp-left schedule-end">
                    <label><?php _e( 'End', 'erp' ); ?></label>
                    <span class="sep">:</span>
                    <span class="value">
                        <input class="start-date erp-date-field" required name="end_date" type="text" value="{{ data.current_date }}"  placeholder="yy-mm-dd"><span class="datetime-sep">@</span>
                        <input class="start-time erp-time-field" required name="end_time" type="text" placeholder="12.00pm" size="10">
                    </span>
                </p>

                <p class="erp-left schedule-all-day">
                    <input type="checkbox" name="all_day" value="true"> <?php _e( 'All Day', 'erp' ); ?>
                </p>
                <div class="clearfix"></div>
            </div>
            <p>
                <input id="activity_message_edit" type="hidden" name="message" required value="">
                <trix-editor input="activity_message_edit" placeholder="<?php _e( 'Enter your schedule description .....', 'erp' ); ?>"></trix-editor>
            </p>
            <div class="clearfix"></div>

            <p>
                <select name="invite_contact[]" id="erp-crm-activity-invite-contact" class="select2" multiple="multiple" style="width: 100%" data-placeholder="<?php _e( 'Agents or managers..', 'erp' ) ?>">
                    <?php echo erp_crm_get_crm_user_html_dropdown(); ?>
                </select>
            </p>

            <div class="schedule-notification">
                <p class="erp-left schedule-type">
                    <label><?php _e( 'Schedule Type', 'erp' ) ?></label>
                    <span class="sep">:</span>
                    <span class="value">
                        <select name="schedule_type" id="schedule_type" required>
                            <option value="" selected><?php _e( '--Select--', 'erp' ) ?></option>
                            <option value="meeting"><?php _e( 'Meeting', 'erp' ); ?></option>
                            <option value="call"><?php _e( 'Call', 'erp' ); ?></option>
                        </select>
                    </span>
                </p>

                <p class="erp-left schedule-notification-allow">
                    <input type="checkbox" name="allow_notification" value="true"> <?php _e( 'Allow notification', 'erp' ); ?>
                </p>
                <div class="clearfix"></div>
            </div>

            <div class="schedule-notification" id="schedule-notification-wrap">
                <p class="erp-left schedule-notification-via">
                    <label><?php _e( 'Notify Via', 'erp' ); ?></label>
                    <span class="sep">:</span>
                    <span class="value">
                        <select name="notification_via" id="notification_via">
                            <option value="" selected><?php _e( '--Select--', 'erp' ); ?></option>
                            <option value="email"><?php _e( 'Email', 'erp' ); ?></option>
                            <option value="sms" value="disabled"><?php _e( 'SMS', 'erp' ); ?></option>
                        </select>
                    </span>
                </p>

                <p class="erp-left schedule-notification-before">
                    <label><?php _e( 'Notify before', 'erp' ); ?></label>
                    <span class="sep">:</span>
                    <span class="value">
                        <input type="text" name="notification_time_interval" placeholder="10" style="width:60px;">
                        <select name="notification_time" id="notification_time">
                            <option value="" selected><?php _e( '-Select-', 'erp' ); ?></option>
                            <option value="minute"><?php _e( 'minute', 'erp' ); ?></option>
                            <option value="hour"><?php _e( 'hour', 'erp' ); ?></option>
                            <option value="day"><?php _e( 'day', 'erp' ); ?></option>
                        </select>
                    </span>
                </p>
                <div class="clearfix"></div>
            </div>
        </div>
        <input type="hidden" name="type" value="schedule">
    <# } else { #>
        <div class="feed-log-activity">
            <p>
                <select required name="user_id" class="erp-crm-contact-list-dropdown" id="assign-contact"  data-types="contact,company" style="width: 100%" data-placeholder="<?php _e( 'Assign to a contact..', 'erp' ) ?>">
                    <option value=""></option>
                </select>
            </p>

            <p>
                <select name="log_type" required id="erp-crm-feed-log-type" class="erp-left">
                    <option value=""><?php _e( '-- Select type --', 'erp' ) ?></option>
                    <option value="call"><?php _e( 'Log a Call', 'erp' ) ?></option>
                    <option value="meeting"><?php _e( 'Log a Meeting', 'erp' ) ?></option>
                    <option value="email"><?php _e( 'Log an Email', 'erp' ) ?></option>
                    <option value="sms"><?php _e( 'Log an SMS', 'erp' ) ?></option>
                </select>
                <input class="erp-right erp-time-field" type="text" required placeholder="12.00pm" size="10" name="log_time">
                <input class="erp-right erp-date-field" disabled="disabled" name="log_date" value="{{ data.current_date }}" type="text" placeholder="yy-mm-dd">
                <input name="log_date" type="hidden" value="{{ data.current_date }}">
                <span class="clearfix"></span>
            </p>

            <p class="log-email-subject erp-hide">
                <label><?php _e( 'Subject', 'erp' ); ?></label>
                <span class="sep">:</span>
                <span class="value">
                    <input type="text" class="email_subject" name="email_subject" placeholder="<?php _e( 'Subject log...', 'erp' ); ?>">
                </span>
            </p>

            <p class="log-selected-contact erp-hide">
                <select name="invite_contact[]" id="erp-crm-activity-invite-contact" class="select2" multiple="multiple" style="width: 100%" data-placeholder="<?php _e( 'Agents or managers..', 'erp' ) ?>">
                    <?php echo erp_crm_get_crm_user_html_dropdown(); ?>
                </select>
            </p>

            <input id="activity_message_edit" type="hidden" name="message" value="">
            <trix-editor input="activity_message_edit" placeholder="<?php _e( 'Add your log .....', 'erp' ); ?>"></trix-editor>
        </div>
        <input type="hidden" name="type" value="log_activity">
    <# } #>

    <div class="submit-action">
        <input type="hidden" name="action" value="erp_crm_add_schedules_action">
        <input type="hidden" name="created_by" value="<?php echo get_current_user_id(); ?>" >
        <?php wp_nonce_field( 'wp-erp-crm-customer-feed' ); ?>
    </div>
</div>
