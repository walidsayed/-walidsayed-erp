<?php
$customer_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
?>
<div id="log-activity">
    <p>
        <select name="log_type" v-model="feedData.log_type" id="log-type" class="erp-left">
            <option value=""><?php _e( '-- Select type --', 'erp' ) ?></option>
            <option value="call"><?php _e( 'Log a Call', 'erp' ) ?></option>
            <option value="meeting"><?php _e( 'Log a Meeting', 'erp' ) ?></option>
            <option value="email"><?php _e( 'Log an Email', 'erp' ) ?></option>
            <option value="sms"><?php _e( 'Log an SMS', 'erp' ) ?></option>
        </select>

        <input class="erp-right" v-model="feedData.tp" type="text" v-timepicker="feedData.tp" placeholder="12.00pm" size="10">
        <input class="erp-right" v-model="feedData.dt" type="text" v-datepicker="feedData.dt" datedisable="upcomming" placeholder="yy-mm-dd">
        <span class="clearfix"></span>
    </p>

    <p v-if="feedData.log_type == 'email'">
        <label><?php _e( 'Subject', 'erp' ) ?></label>
        <span class="sep">:</span>
        <span class="value">
            <input type="text" class="email_subject" name="email_subject" v-model="feedData.email_subject" placeholder="<?php _e( 'Subject log...', 'erp' ); ?>">
        </span>
    </p>

    <p v-if="feedData.log_type == 'meeting'">
        <select name="selected_contact" id="erp-crm-activity-invite-contact" v-model="feedData.inviteContact" v-selecttwo="feedData.inviteContact" class="select2" multiple="multiple" style="width: 100%" data-placeholder="<?php _e( 'Agents or managers..', 'erp' ) ?>">
            <?php echo erp_crm_get_crm_user_html_dropdown(); ?>
        </select>
    </p>

    <trix-editor v-if="!feed" id="log-text-editor" input="log_activity_message" placeholder="<?php _e( 'Type your description .....', 'erp' ); ?>"></trix-editor>
    <input v-if="!feed" id="log_activity_message" v-model="feedData.message" type="hidden" name="log_activity_message" value="">

    <trix-editor v-if="feed" id="log-text-editor" input="log_activity_message_{{ feed.id }}" placeholder="<?php _e( 'Type your description .....', 'erp' ); ?>"></trix-editor>
    <input v-if="feed" id="log_activity_message_{{ feed.id }}" v-model="feedData.message" type="hidden" name="log_activity_message_{{ feed.id }}" value="{{ feed.message }}">

    <div class="submit-action">
        <input type="hidden" name="user_id" v-model="feedData.user_id" value="<?php echo $customer_id; ?>" >
        <input type="hidden" name="created_by" v-model="feedData.created_by" value="<?php echo get_current_user_id(); ?>">
        <input type="hidden" name="action" v-model="feedData.action" value="erp_customer_feeds_save_notes">
        <input type="hidden" name="type" v-model="feedData.type" value="log_activity">
        <input type="submit" v-if="!feed" :disabled = "!isValid" class="button button-primary" name="add_log_activity" value="<?php _e( 'Add Log', 'erp' ); ?>">
        <input type="submit" v-if="feed" :disabled = "!isValid" class="button button-primary" name="edit_log_activity" value="<?php _e( 'Update Log', 'erp' ); ?>">
        <input type="reset" v-if="!feed" class="button button-default" value="<?php _e( 'Discard', 'erp' ); ?>">
        <button class="button" v-if="feed" @click.prevent="cancelUpdateFeed"><?php _e( 'Cancel', 'erp' ); ?></button>
    </div>
</div>
