<?php do_action( 'erp_email_header', $email_heading ); ?>

<?php echo apply_filters( 'erp_email_body', $email_body ); ?>

<?php do_action( 'erp_email_footer' ); ?>