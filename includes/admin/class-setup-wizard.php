<?php
/**
 * Setup wizard class
 *
 * Walkthrough to the basic setup upon installation
 *
 * @package WP-ERP\Admin
 */

namespace WeDevs\ERP\Admin;

/**
 * The class
 */
class Setup_Wizard {

    /** @var string Currenct Step */
    private $step   = '';

    /** @var array Steps for the setup wizard */
    private $steps  = array();

    /**
     * Hook in tabs.
     */
    public function __construct() {

        // if we are here, we assume we don't need to run the wizard again
        // and the user doesn't need to be redirected here
        update_option( 'erp_setup_wizard_ran', '1' );

        if ( apply_filters( 'erp_enable_setup_wizard', true ) && current_user_can( 'manage_options' ) ) {
            add_action( 'admin_menu', array( $this, 'admin_menus' ) );
            add_action( 'admin_init', array( $this, 'setup_wizard' ) );
        }
    }

    /**
     * Add admin menus/screens.
     */
    public function admin_menus() {
        add_dashboard_page( '', '', 'manage_options', 'erp-setup', '' );
    }

    /**
     * Show the setup wizard
     */
    public function setup_wizard() {
        if ( empty( $_GET['page'] ) || 'erp-setup' !== $_GET['page'] ) {
            return;
        }

        $this->steps = array(
            'introduction' => array(
                'name'    =>  __( 'Introduction', 'erp' ),
                'view'    => array( $this, 'setup_step_introduction' ),
                'handler' => ''
            ),
            'basic' => array(
                'name'    =>  __( 'Basic', 'erp' ),
                'view'    => array( $this, 'setup_step_basic' ),
                'handler' => array( $this, 'setup_step_basic_save' )
            ),
            'module' => array(
                'name'    =>  __( 'Module', 'erp' ),
                'view'    => array( $this, 'setup_step_module' ),
                'handler' => array( $this, 'setup_step_module_save' )
            ),
            'department' => array(
                'name'    =>  __( 'Departments', 'erp' ),
                'view'    => array( $this, 'setup_step_departments' ),
                'handler' => array( $this, 'setup_step_departments_save' )
            ),
            'designation' => array(
                'name'    =>  __( 'Designations', 'erp' ),
                'view'    => array( $this, 'setup_step_designation' ),
                'handler' => array( $this, 'setup_step_designation_save' ),
            ),
            'workdays' => array(
                'name'    =>  __( 'Work Days', 'erp' ),
                'view'    => array( $this, 'setup_step_workdays' ),
                'handler' => array( $this, 'setup_step_workdays_save' ),
            ),
            // 'newsletter' => array(
            //     'name'    =>  __( 'Newsletter', 'erp' ),
            //     'view'    => array( $this, 'setup_step_newsletter' ),
            //     'handler' => array( $this, 'setup_step_newsletter_save' ),
            // ),
            'next_steps' => array(
                'name'    =>  __( 'Ready!', 'erp' ),
                'view'    => array( $this, 'setup_step_ready' ),
                'handler' => ''
            )
        );

        $this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );
        $suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '';

        wp_enqueue_style( 'jquery-ui', WPERP_ASSETS . '/vendor/jquery-ui/jquery-ui-1.9.1.custom.css' );
        wp_enqueue_style( 'erp-setup', WPERP_ASSETS . '/css/setup.css', array( 'dashicons', 'install' ) );

        wp_register_script( 'erp-select2', WPERP_ASSETS . '/vendor/select2/select2.full.min.js', false, false, true );
        wp_register_script( 'erp-setup', WPERP_ASSETS . "/js/erp$suffix.js", array( 'jquery', 'jquery-ui-datepicker', 'erp-select2' ), date( 'Ymd' ), true );

        if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
            call_user_func( $this->steps[ $this->step ]['handler'] );
        }

        ob_start();
        $this->setup_wizard_header();
        $this->setup_wizard_steps();
        $this->setup_wizard_content();
        $this->setup_wizard_footer();
        exit;
    }

    public function get_next_step_link() {
        $keys = array_keys( $this->steps );
        return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ], remove_query_arg( 'translation_updated' ) );
    }

    /**
     * Setup Wizard Header
     */
    public function setup_wizard_header() {
        ?>
        <!DOCTYPE html>
        <html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php _e( 'WP ERP &rsaquo; Setup Wizard', 'erp' ); ?></title>
            <?php wp_print_scripts( 'erp-setup' ); ?>
            <?php do_action( 'admin_print_styles' ); ?>
            <?php do_action( 'admin_head' ); ?>
        </head>
        <body class="erp-setup wp-core-ui">
            <h1 class="erp-logo"><a href="http://wperp.com/">WP ERP</a></h1>
        <?php
    }

    /**
     * Setup Wizard Footer
     */
    public function setup_wizard_footer() {
        ?>
            <?php if ( 'next_steps' === $this->step ) : ?>
                <a class="erp-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Return to the WordPress Dashboard', 'erp' ); ?></a>
            <?php endif; ?>
            </body>
        </html>
        <?php
    }

    /**
     * Output the steps
     */
    public function setup_wizard_steps() {
        $output_steps = $this->steps;
        array_shift( $output_steps );
        ?>
        <ol class="erp-setup-steps">
            <?php foreach ( $output_steps as $step_key => $step ) : ?>
                <li class="<?php
                    if ( $step_key === $this->step ) {
                        echo 'active';
                    } elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
                        echo 'done';
                    }
                    ?>"><a href="<?php echo admin_url( 'index.php?page=erp-setup&step=' . $step_key ); ?>"><?php echo esc_html( $step['name'] ); ?></a>
                </li>
            <?php endforeach; ?>
        </ol>
        <?php
    }

    /**
     * Output the content for the current step
     */
    public function setup_wizard_content() {
        echo '<div class="erp-setup-content">';
        call_user_func( $this->steps[ $this->step ]['view'] );
        echo '</div>';
    }

    public function next_step_buttons() {
        ?>
        <p class="erp-setup-actions step">
            <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'erp' ); ?>" name="save_step" />
            <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php _e( 'Skip this step', 'erp' ); ?></a>
            <?php wp_nonce_field( 'erp-setup' ); ?>
        </p>
        <?php
    }

    /**
     * Introduction step
     */
    public function setup_step_introduction() {
        ?>
        <h1><?php _e( 'Welcome to WP ERP!', 'erp' ); ?></h1>
        <p><?php _e( 'Thank you for choosing WP-ERP. An easier way to manage your company! This quick setup wizard will help you configure the basic settings. <strong>It’s completely optional and shouldn’t take longer than three minutes.</strong>', 'erp' ); ?></p>
        <p><?php _e( 'No time right now? If you don’t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!', 'erp' ); ?></p>
        <p class="erp-setup-actions step">
            <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next"><?php _e( 'Let\'s Go!', 'erp' ); ?></a>
            <a href="<?php echo esc_url( wp_get_referer() ? wp_get_referer() : admin_url( 'plugins.php' ) ); ?>" class="button button-large"><?php _e( 'Not right now', 'erp' ); ?></a>
        </p>
        <?php
    }

    public function setup_step_basic() {
        $general         = get_option( 'erp_settings_general', array() );
        $accounting      = get_option( 'erp_settings_accounting', array() );
        $company         = new \WeDevs\ERP\Company();
        $business_type   = $company->business_type;

        $financial_month = isset( $general['gen_financial_month'] ) ? $general['gen_financial_month'] : '1';
        $company_started = isset( $general['gen_com_start'] ) ? $general['gen_com_start'] : '';
        ?>
        <h1><?php _e( 'Basic Settings', 'erp' ); ?></h1>

        <form method="post">

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="company_name"><?php _e( 'Company Name', 'erp' ); ?></label></th>
                    <td>
                        <?php erp_html_form_input([
                            'name'    => 'company_name',
                            'id'      => 'company_name',
                            'type'    => 'text',
                            'value'   => $company->name,
                            'help'    => __( 'This name will be shown as your company name.', 'erp' ),
                        ]); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gen_financial_month"><?php _e( 'Financial Year Starts', 'erp' ); ?></label></th>
                    <td>
                        <?php erp_html_form_input([
                            'name'    => 'gen_financial_month',
                            'id'      => 'gen_financial_month',
                            'type'    => 'select',
                            'value'   => $financial_month,
                            'options' => erp_months_dropdown(),
                            'help'    => __( 'Financial and tax calculation starts from this month of every year.', 'erp' ),
                        ]); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gen_com_start"><?php _e( 'Company Start Date', 'erp' ); ?></label></th>
                    <td>
                        <?php erp_html_form_input([
                            'name'        => 'gen_com_start',
                            'type'        => 'text',
                            'value'       => $company_started,
                            'help'        => __( 'The date the company officially started.', 'erp' ),
                            'class'       => 'erp-date-field',
                            'placeholder' => 'YYYY-MM-DD'
                        ]); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="base_currency"><?php _e( 'Currency', 'erp' ); ?></label></th>
                    <td>
                        <?php erp_html_form_input([
                            'name'    => 'base_currency',
                            'type'    => 'select',
                            'value'   => 'USD',
                            'options' => erp_get_currencies(),
                            'desc'    => __( 'Format of date to show accross the system.', 'erp' ),
                        ]); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="date_format"><?php _e( 'Date Format', 'erp' ); ?></label></th>
                    <td>
                        <?php erp_html_form_input([
                            'name'    => 'date_format',
                            'type'    => 'select',
                            'value'   => 'd-m-Y',
                            'options' => [
                                'm-d-Y' => 'mm-dd-yyyy',
                                'd-m-Y' => 'dd-mm-yyyy',
                                'm/d/Y' => 'mm/dd/yyyy',
                                'd/m/Y' => 'dd/mm/yyyy',
                                'Y-m-d' => 'yyyy-mm-dd',
                            ],
                            'help' => __( 'Format of date to show accross the system.', 'erp' ),
                        ]); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="business_type"><?php _e( 'What sort of business do you do?', 'erp' ); ?></label></th>
                    <td>
                        <?php erp_html_form_input([
                            'name'    => 'business_type',
                            'type'    => 'select',
                            'value'   => $business_type,
                            'options' => [
                                ''                 => '-- select --',
                                'Freelance'        => 'Freelance',
                                'FreelanceDev'     => 'Freelance (Developer)',
                                'FreelanceDes'     => 'Freelance (Design)',
                                'SmallBLocal'      => 'Small Business: Local Service (e.g. Hairdresser)',
                                'SmallBWeb'        => 'Small Business: Web Business',
                                'SmallBOther'      => 'Small Business (Other)',
                                'ecommerceWoo'     => 'eCommerce (WooCommerce)',
                                'ecommerceShopify' => 'eCommerce (Shopify)',
                                'ecommerceOther'   => 'eCommerce (Other)',
                                'Other'            => 'Other'
                            ]
                        ]); ?>
                    </td>
                </tr>

                <?php if ( null !== get_option( 'erp_allow_tracking') ) : ?>

                <tr>
                    <th scope="row"><label for="share_essentials"><?php _e( 'Share Essentials', 'erp' ); ?></label></th>

                    <td class="updated">
                        <input type="checkbox" name="share_essentials" id="share_essentials" class="switch-input" checked>
                        <label for="share_essentials" class="switch-label">
                            <span class="toggle--on">On</span>
                            <span class="toggle--off">Off</span>
                        </label>
                        <span class="description">
                            Want to help make WP ERP even more awesome? Allow weDevs to collect non-sensitive diagnostic data and usage information. <a class="insights-data-we-collect" href="#">what we collect</a>
                        </span>
                        <p class="description" style="display:none;">Server environment details (php, mysql, server, WordPress versions), Number of users in your site, Site language, Number of active and inactive plugins, Site name and url, Your name and email address. No sensitive data is tracked.</p>
                    </td>

                    <script type="text/javascript">
                        jQuery('.insights-data-we-collect').on('click', function(e) {
                            e.preventDefault();
                            jQuery(this).parents('.updated').find('p.description').slideToggle('fast');
                        });
                    </script>
                </tr>
                <?php endif; ?>
            </table>

            <?php $this->next_step_buttons(); ?>
        </form>
        <?php
    }

    public function setup_step_basic_save() {
        check_admin_referer( 'erp-setup' );

        $company_name     = sanitize_text_field( $_POST['company_name'] );
        $business_type    = sanitize_text_field( $_POST['business_type'] );
        $financial_month  = sanitize_text_field( $_POST['gen_financial_month'] );
        $company_started  = sanitize_text_field( $_POST['gen_com_start'] );
        $base_currency    = sanitize_text_field( $_POST['base_currency'] );
        $date_format      = sanitize_text_field( $_POST['date_format'] );
        $share_essentials = sanitize_text_field( isset( $_POST['share_essentials'] ) );
        $company          = new \WeDevs\ERP\Company();
        $allowed          = '1';

        if ( $share_essentials === $allowed ) {
            update_option( 'erp_allow_tracking', 'yes' );
            update_option( 'erp_tracking_notice', 'hide' );

            wp_clear_scheduled_hook( 'erp_tracker_send_event' );
            wp_schedule_event( time(), 'weekly', 'erp_tracker_send_event' );

            $tracker = new \WeDevs\ERP\Tracker();
            $tracker->send_tracking_data();
        }

        $company->update( array(
            'name'          => $company_name,
            'business_type' => $business_type
        ) );

        update_option( 'erp_settings_general', [
            'gen_financial_month' => $financial_month,
            'gen_com_start'       => $company_started,
            'date_format'         => $date_format,
            'erp_currency'        => $base_currency,
            'erp_debug_mode'      => 0
        ] );

        update_option( 'erp_settings_accounting', [
            'base_currency' => $base_currency,
        ] );

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }

    /**
     * Module setup step
     * @since 1.3.4
     */
    public function setup_step_module() {
        $modules            = wperp()->modules->get_query_modules();
        $all_active_modules = wperp()->modules->get_active_modules();
        ?>

        <h1><?php _e( 'Active Modules', 'erp' ); ?></h1>

        <form method="post">
            <table class="form-table">
            <?php
            foreach ( $modules as $slug => $module ) {
                $checked = array_key_exists( $slug, $all_active_modules ) ? $slug : '';
                ?>
                <tr>
                    <th scope="row">
                        <label for="erp_module_<?php echo $slug; ?>"><?php echo isset( $module['title'] ) ? $module['title'] : ''; ?></label>
                    </th>
                    <td>
                        <input type="checkbox" name="modules[]" id="erp_module_<?php echo $slug; ?>" class="switch-input" value="<?php echo $slug; ?>" <?php checked( $slug, $checked ); ?>>
                        <label for="erp_module_<?php echo $slug; ?>" class="switch-label">
                            <span class="toggle--on">On</span>
                            <span class="toggle--off">Off</span>
                        </label>
                        <span class="description"><?php echo isset( $module['description'] ) ? $module['description'] : ''; ?></span>
                    </td>
                </tr>
                <?php } ?>
            </table>

            <?php $this->next_step_buttons(); ?>
        </form>
        <?php
    }

    /**
     * Module setup step save
     * @since 1.3.4
     */
    public function setup_step_module_save() {
        check_admin_referer( 'erp-setup' );

        $all_modules = wperp()->modules->get_modules();
        $modules     = array_map( 'sanitize_text_field', wp_unslash( $_POST['modules'] ) );

        foreach ( $all_modules as $key => $module ) {
            if ( ! in_array( $key, $modules ) ) {
                unset( $all_modules[$key] );
            }
        }

        update_option( 'erp_modules', $all_modules );

        // when HRM is inactive hide related steps
        if ( ! in_array( 'hrm', $modules ) ) {
            unset( $this->steps['department'] );
            unset( $this->steps['designation'] );
            unset( $this->steps['workdays'] );
        }

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }

    public function setup_step_departments() {
        ?>
        <h1><?php _e( 'Departments Setup', 'erp' ); ?></h1>
        <form method="post" class="form-table">

            <div class="two-col">
                <div class="col-first">
                    <p><?php _e( 'Create some departments for your company. e.g. HR, Engineering, Marketing, Support, etc. ', 'erp' ); ?></p>
                    <p><?php _e( 'Leave empty for not to create any departments.', 'erp' ); ?></p>
                </div>

                <div class="col-last">
                    <ul class="unstyled">
                        <li>
                            <input type="text" name="departments[]" class="regular-text" placeholder="<?php esc_attr_e( 'Department name', 'erp' ); ?>">
                        </li>
                        <li>
                            <input type="text" name="departments[]" class="regular-text" placeholder="<?php esc_attr_e( 'Department name', 'erp' ); ?>">
                        </li>
                        <li class="add-new"><a href="#" class="button"><?php _e( 'Add New', 'erp' ); ?></a></li>
                    </ul>
                </div>
            </div>

            <?php $this->next_step_buttons(); ?>
        </form>
        <?php

        $this->script_input_duplicator();
    }

    public function script_input_duplicator() {
        ?>
        <script type="text/javascript">
            jQuery(function($) {
                $('.add-new').on('click', 'a', function(e) {
                    e.preventDefault();

                    var self = $(this),
                        parent = self.closest('li');

                    parent.prev().clone().insertBefore( parent ).find('input').val('');
                });
            });
        </script>
        <?php
    }

    public function setup_step_departments_save() {
        check_admin_referer( 'erp-setup' );

        $departments = array_map( 'sanitize_text_field', $_POST['departments'] );

        if ( $departments ) {
            foreach ($departments as $department) {
                if ( ! empty( $department ) ) {
                    erp_hr_create_department([
                        'title' => $department
                    ]);
                }
            }
        }

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }

    public function setup_step_designation() {
        ?>
        <h1><?php _e( 'Designation Setup', 'erp' ); ?></h1>
        <form method="post" class="form-table">

            <div class="two-col">
                <div class="col-first">
                    <p><?php _e( 'Create some designations for your company. e.g. Manager, Senior Developer, Marketing Manager, Support Executive, etc. ', 'erp' ); ?></p>
                    <p><?php _e( 'Leave empty for not to create any designations.', 'erp' ); ?></p>
                </div>

                <div class="col-last">
                    <ul class="unstyled">
                        <li>
                            <input type="text" name="designations[]" class="regular-text" placeholder="<?php esc_attr_e( 'Designation name', 'erp' ); ?>">
                        </li>
                        <li>
                            <input type="text" name="designations[]" class="regular-text" placeholder="<?php esc_attr_e( 'Designation name', 'erp' ); ?>">
                        </li>
                        <li class="add-new"><a href="#" class="button"><?php _e( 'Add New', 'erp' ); ?></a></li>
                    </ul>
                </div>
            </div>

            <?php $this->next_step_buttons(); ?>
        </form>
        <?php

        $this->script_input_duplicator();
    }

    public function setup_step_designation_save() {
        check_admin_referer( 'erp-setup' );

        $designations = array_map( 'sanitize_text_field', $_POST['designations'] );

        if ( $designations ) {
            foreach ($designations as $designation) {
                if ( ! empty( $designation ) ) {
                    erp_hr_create_designation([
                        'title' => $designation
                    ]);
                }
            }
        }

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }

    public function setup_step_workdays() {
        $working_days = erp_company_get_working_days();
        $options = array(
            '8' => __( 'Full Day', 'erp' ),
            '4' => __( 'Half Day', 'erp' ),
            '0' => __( 'Non-working Day', 'erp' )
        );
        $days = array(
            'mon' => __( 'Monday', 'erp' ),
            'tue' => __( 'Tuesday', 'erp' ),
            'wed' => __( 'Wednesday', 'erp' ),
            'thu' => __( 'Thursday', 'erp' ),
            'fri' => __( 'Friday', 'erp' ),
            'sat' => __( 'Saturday', 'erp' ),
            'sun' => __( 'Sunday', 'erp' )
        );
        ?>
        <h1><?php _e( 'Workdays Setup', 'erp' ); ?></h1>

        <form method="post">
            <table class="form-table">

                <?php
                foreach( $days as $key => $day ) {
                    ?>
                    <tr>
                        <th scope="row"><label for="gen_financial_month"><?php echo $day; ?></label></th>
                        <td>
                            <?php erp_html_form_input( array(
                                'name'     => 'day[' . $key . ']',
                                'value'    => $working_days[ $key ],
                                'type'     => 'select',
                                'options'  => $options
                            ) ); ?>
                        </td>
                    </tr>
                <?php } ?>

            </table>

            <?php $this->next_step_buttons(); ?>
        </form>
        <?php
    }

    /**
     * Save company working days data
     *
     * @since 1.0.0
     * @since 1.1.14 Saving data in sun, mon etc keys, instead of `erp_settings_erp-hr_workdays`
     *               since ERP Settings saves data with these keys
     *
     * @return void
     */
    public function setup_step_workdays_save() {
        check_admin_referer( 'erp-setup' );

        if ( 7 === count( $_POST['day'] ) ) {
            foreach ( $_POST['day'] as $day => $hour_limit ) {
                update_option( $day, $hour_limit );
            }
        }

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }

    /**
     * Newsletter setup step
     * @since 1.3.4
     */
    public function setup_step_newsletter() {
        ?>
        <h1><?php _e( 'Newsletter Setup', 'erp' ); ?></h1>

        <?php
        $subscription = new \WeDevs\ERP\CRM\Subscription();
        ?>

        <form method="post">

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="full_name"><?php _e( 'Your Name', 'erp' ); ?></label>
                    </th>
                    <td>
                        <?php erp_html_form_input( array(
                            'name'     => 'full_name',
                            'value'    => '',
                            'type'     => 'text',
                            'help'    => __( 'This name will be used for newsletter name.', 'erp' ),
                        ) ); ?>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="subs_email"><?php _e( 'Your Email', 'erp' ); ?></label>
                    </th>
                    <td>
                        <?php erp_html_form_input( array(
                            'name'     => 'full_name',
                            'value'    => '',
                            'type'     => 'email',
                            'help'    => __( 'Email to get update critical updates.', 'erp' ),
                        ) ); ?>
                    </td>
                </tr>
            </table>

            <?php $this->next_step_buttons(); ?>
        </form>
        <?php
    }

    /**
     * Newsletter setup step save
     * @since 1.3.4
     */
    public function setup_step_newsletter_save() {
        check_admin_referer( 'erp-setup' );

        //

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }

    public function setup_step_ready() {
        ?>

        <div class="final-step">
            <h1><?php _e( 'Your Site is Ready!', 'erp' ); ?></h1>

            <div class="erp-setup-next-steps">
                <div class="erp-setup-next-steps-first">
                    <h2><?php _e( 'Next Steps &rarr;', 'erp' ); ?></h2>
                    <?php
                        $is_hrm_activated = erp_is_module_active( 'hrm' );

                        if ( $is_hrm_activated ) : ?>
                            <a class="button button-primary button-large"
                                href="<?php echo esc_url( admin_url( 'admin.php?page=erp-hr-employee' ) ); ?>">
                                <?php _e( 'Add your employees!', 'erp' ); ?>
                            </a>
                        <?php else: ?>
                            <a class="button button-primary button-large"
                                href="<?php echo esc_url( admin_url() ); ?>">
                                <?php _e( 'Go to Dashboard!', 'erp' ); ?>
                            </a>
                        <?php endif;
                    ?>
                </div>
            </div>
        </div>
        <?php
    }
}

return new Setup_Wizard();
