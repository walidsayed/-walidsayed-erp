<?php
namespace WeDevs\ERP;

use WeDevs\ERP\Framework\Traits\Hooker;

/**
 * Installation related functions and actions.
 *
 * @author Tareq Hasan
 * @package ERP
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Installer Class
 *
 * @package ERP
 */
class Updates {

    use Hooker;

    /** @var array DB updates that need to be run */
    private static $updates = [
        '1.0'    => 'updates/update-1.0.php',
        '1.1.0'  => 'updates/update-1.1.0.php',
        '1.1.1'  => 'updates/update-1.1.1.php',
        '1.1.2'  => 'updates/update-1.1.2.php',
        '1.1.3'  => 'updates/update-1.1.3.php',
        '1.1.5'  => 'updates/update-1.1.5.php',
        '1.1.6'  => 'updates/update-1.1.6.php',
        '1.1.7'  => 'updates/update-1.1.7.php',
        '1.1.8'  => 'updates/update-1.1.8.php',
        '1.1.9'  => 'updates/update-1.1.9.php',
        '1.1.17' => 'updates/update-1.1.17.php',
        '1.2.1'  => 'updates/update-1.2.1.php',
        '1.2.2'  => 'updates/update-1.2.2.php',
        '1.2.5'  => 'updates/update-1.2.5.php',
        '1.2.7'  => 'updates/update-1.2.7.php',
        '1.3.2'  => 'updates/update-1.3.2.php',
        '1.3.3'  => 'updates/update-1.3.3.php',
        '1.3.4'  => 'updates/update-1.3.4.php',
    ];

    /**
     * Current active erp modules
     *
     * @since 1.1.9
     *
     * @var array
     */
    private $active_modules = [];

    /**
     * Binding all events
     *
     * @since 0.1
     *
     * @return void
     */
    function __construct() {
        $this->action( 'admin_notices', 'show_update_notice' );
        $this->action( 'admin_init', 'do_updates' );
    }

    /**
     * Check if need any update
     *
     * @since 1.0
     *
     * @return boolean
     */
    public function is_needs_update() {
        $installed_version = get_option( 'wp_erp_version' );

        // may be it's the first install
        if ( ! $installed_version ) {
            return false;
        }

        if ( version_compare( $installed_version, WPERP_VERSION, '<' ) ) {
            return true;
        }

        return false;
    }

    /**
     * Show update notice
     *
     * @since 1.0
     *
     * @return void
     */
    public function show_update_notice() {
        if ( ! current_user_can( 'update_plugins' ) || ! $this->is_needs_update() ) {
            return;
        }

        $installed_version  = get_option( 'wp_erp_version' );
        $updatable_versions = array_keys( self::$updates );

        if ( ! is_null( $installed_version ) && version_compare( $installed_version, end( $updatable_versions ), '<' ) ) {
            ?>
                <div id="message" class="updated">
                    <p><?php _e( '<strong>WP ERP Data Update Required</strong> &#8211; We need to update your install to the latest version', 'erp' ); ?></p>
                    <p class="submit"><a href="<?php echo add_query_arg( [ 'wperp_do_update' => true ], $_SERVER['REQUEST_URI'] ); ?>" class="wperp-update-btn button-primary"><?php _e( 'Run the updater', 'erp' ); ?></a></p>
                </div>

                <script type="text/javascript">
                    jQuery('.wperp-update-btn').click('click', function(){
                        return confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'erp' ); ?>' );
                    });
                </script>
            <?php
        } else {
            update_option( 'wp_erp_version', WPERP_VERSION );
        }
    }

    /**
     * Do all updates when Run updater btn click
     *
     * @since 1.0
     * @since 1.2.7 save plugin install date
     *
     * @return void
     */
    public function do_updates() {
        if ( isset( $_GET['wperp_do_update'] ) && $_GET['wperp_do_update'] ) {
            $this->perform_updates();
        }
    }

    /**
     * Perform all updates
     *
     * @since 1.0
     *
     * @return void
     */
    public function perform_updates() {
        if ( ! $this->is_needs_update() ) {
            return;
        }

        $installed_version = get_option( 'wp_erp_version' );

        $this->enable_all_erp_modules();

        foreach ( self::$updates as $version => $path ) {
            if ( version_compare( $installed_version, $version, '<' ) ) {
                include $path;
                update_option( 'wp_erp_version', $version );
            }
        }

        update_option( 'wp_erp_version', WPERP_VERSION );

        //save install date
        if ( false == get_option( 'wp_erp_install_date' ) ) {
            update_option( 'wp_erp_install_date', current_time( 'timestamp' ) );
        }

        $this->enable_active_erp_modules();

        $location = remove_query_arg( ['wperp_do_update'], $_SERVER['REQUEST_URI'] );
        wp_redirect( $location );
        exit();
    }

    /**
     * Enable all erp modules before run the updaters
     *
     * @since 1.1.9
     *
     * @return void
     */
    private function enable_all_erp_modules() {
        // Let's remember the active modules.
        $this->active_modules = wperp()->modules->get_active_modules();

        $all_modules = wperp()->modules->get_modules();

        update_option( 'erp_modules', $all_modules );

        wperp()->load_module();
    }

    /**
     * Enable modules that were active before running the updater
     *
     * @since 1.1.9
     *
     * @return void
     */
    private function enable_active_erp_modules() {
        update_option( 'erp_modules', $this->active_modules );

        wperp()->load_module();
    }
}
