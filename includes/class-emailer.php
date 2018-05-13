<?php
namespace WeDevs\ERP;

/**
 * Emailer Class
 */
class Emailer {

    public $emails;

    /**
     * Initializes the WeDevs_ERP() class
     *
     * Checks for an existing WeDevs_ERP() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }

    public function __construct() {

        // Email Header, Footer and content hooks
        add_action( 'erp_email_header', array( $this, 'email_header' ) );
        add_action( 'erp_email_footer', array( $this, 'email_footer' ) );

        // Let 3rd parties unhook the above via this hook
        do_action( 'erp_email', $this );
    }

    function init_emails() {
        $this->emails = apply_filters( 'erp_email_classes', $this->emails );
    }

    /**
     * Return the email classes - used in admin to load settings.
     *
     * @return array
     */
    public function get_emails() {
        return $this->emails;
    }

    /**
     * Get an registered email instance
     *
     * @param  string  $class_name
     *
     * @return \Email|false
     */
    public function get_email( $class_name ) {
        if ( $this->emails && array_key_exists( $class_name, $this->emails ) ) {
            return $this->emails[ $class_name ];
        }

        return false;
    }

    /**
     * Get the email header.
     *
     * @param mixed $email_heading heading for the email
     */
    public function email_header( $email_heading ) {
        include WPERP_INCLUDES . '/email/email-header.php';
    }

    /**
     * Get the email footer.
     */
    public function email_footer() {
        include WPERP_INCLUDES . '/email/email-footer.php';
    }
}
