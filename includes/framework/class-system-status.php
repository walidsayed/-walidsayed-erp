<?php

namespace WeDevs\ERP\Framework;

 // @since 1.3.4
class System_Status {

	/**
	 * Get array of environment information. Includes thing like software
	 * versions, and various server settings.
	 *
	 * @return array
	 */
	public function get_environment_info() {
		global $wpdb;

		// Figure out cURL version, if installed.
		$curl_version = '';
		if ( function_exists( 'curl_version' ) ) {
			$curl_version = curl_version();
			$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
		}

		// WP memory limit
		$wp_memory_limit = erp_let_to_num( WP_MEMORY_LIMIT );
		if ( function_exists( 'memory_get_usage' ) ) {
			$wp_memory_limit = max( $wp_memory_limit, erp_let_to_num( @ini_get( 'memory_limit' ) ) );
		}

		// Test POST requests
		$post_response = wp_safe_remote_post( 'https://www.paypal.com/cgi-bin/webscr', array(
			'timeout'     => 10,
			'user-agent'  => 'ERP/' . wperp()->version,
			'httpversion' => '1.1',
			'body'        => array(
				'cmd'    => '_notify-validate',
			),
		) );
		$post_response_successful = false;
		if ( ! is_wp_error( $post_response ) && $post_response['response']['code'] >= 200 && $post_response['response']['code'] < 300 ) {
			$post_response_successful = true;
		}

		// Test GET requests
		$get_response = wp_safe_remote_get( 'https://woocommerce.com/wc-api/product-key-api?request=ping&network=' . ( is_multisite() ? '1' : '0' ) );
		$get_response_successful = false;
		if ( ! is_wp_error( $post_response ) && $post_response['response']['code'] >= 200 && $post_response['response']['code'] < 300 ) {
			$get_response_successful = true;
		}

		$upload_dir =  wp_upload_dir();
		$log_directory = $upload_dir['basedir'] . '/erp-logs/';

		// Return all environment info. Described by JSON Schema.
		return array(
			'home_url'                  => get_option( 'home' ),
			'site_url'                  => get_option( 'siteurl' ),
			'version'                	=> wperp()->version,
			'log_directory'             => $log_directory,
			'log_directory_writable'    => ( @fopen( $log_directory . 'test-log.log', 'a' ) ? true : false ),
			'wp_version'                => get_bloginfo( 'version' ),
			'wp_multisite'              => is_multisite(),
			'wp_memory_limit'           => $wp_memory_limit,
			'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'wp_cron'                   => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
			'language'                  => get_locale(),
			'server_info'               => $_SERVER['SERVER_SOFTWARE'],
			'php_version'               => phpversion(),
			'php_post_max_size'         => erp_let_to_num( ini_get( 'post_max_size' ) ),
			'php_max_execution_time'    => ini_get( 'max_execution_time' ),
			'php_max_input_vars'        => ini_get( 'max_input_vars' ),
			'curl_version'              => $curl_version,
			'suhosin_installed'         => extension_loaded( 'suhosin' ),
			'max_upload_size'           => wp_max_upload_size(),
			'mysql_version'             => ( ! empty( $wpdb->is_mysql ) ? $wpdb->db_version() : '' ),
			'default_timezone'          => date_default_timezone_get(),
			'fsockopen_or_curl_enabled' => ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ),
			'soapclient_enabled'        => class_exists( 'SoapClient' ),
			'domdocument_enabled'       => class_exists( 'DOMDocument' ),
			'gzip_enabled'              => is_callable( 'gzopen' ),
			'mbstring_enabled'          => extension_loaded( 'mbstring' ),
			'remote_post_successful'    => $post_response_successful,
			'remote_post_response'      => ( is_wp_error( $post_response ) ? $post_response->get_error_message() : $post_response['response']['code'] ),
			'remote_get_successful'     => $get_response_successful,
			'remote_get_response'       => ( is_wp_error( $get_response ) ? $get_response->get_error_message() : $get_response['response']['code'] ),
		);
	}

	/**
	 * Add prefix to table.
	 *
	 * @param string $table table name
	 * @return stromg
	 */
	protected function add_db_table_prefix( $table ) {
		global $wpdb;
		return $wpdb->prefix . $table;
	}

	/**
	 * Get array of database information. Version, prefix, and table existence.
	 *
	 * @return array
	 */
	public function get_database_info() {
		global $wpdb;

		$database_table_sizes = $wpdb->get_results( $wpdb->prepare( "
			SELECT
			    table_name AS 'name',
			    round( ( data_length / 1024 / 1024 ), 2 ) 'data',
			    round( ( index_length / 1024 / 1024 ), 2 ) 'index'
			FROM information_schema.TABLES
			WHERE table_schema = %s
			ORDER BY name ASC;
		", DB_NAME ) );

		// ERP Core tables to check existence of
		$core_tables = apply_filters( 'erp_database_tables', array(
			'erp_hr_depts',
			'erp_hr_dependents'
		) );

		/**
		 * Adding the prefix to the tables array, for backwards compatibility.
		 *
		 * If we changed the tables above to include the prefix, then any filters against that table could break.
		 */
		$core_tables = array_map( array( $this, 'add_db_table_prefix' ), $core_tables );

		/**
		 * Organize WooCommerce and non-WooCommerce tables separately for display purposes later.
		 *
		 * To ensure we include all ERP tables, even if they do not exist, pre-populate the ERP array with all the tables.
		 */
		$tables = array(
			'erp' => array_fill_keys( $core_tables, false ),
			'other' => array()
		);

		$database_size = array(
			'data' => 0,
			'index' => 0
		);

		foreach ( $database_table_sizes as $table ) {
			$table_type = in_array( $table->name, $core_tables ) ? 'erp' : 'other';

			$tables[ $table_type ][ $table->name ] = array(
				'data'  => $table->data,
				'index' => $table->index
			);

			$database_size[ 'data' ] += $table->data;
			$database_size[ 'index' ] += $table->index;
		}

		// Return all database info. Described by JSON Schema.
		return array(
			'wp_erp_db_version'    => get_option( 'wp_erp_db_version' ),
			'database_prefix'        => $wpdb->prefix,
			'database_tables'        => $tables,
			'database_size'          => $database_size,
		);
	}

	/**
	 * Get array of counts of objects. Orders, products, etc.
	 *
	 * @return array
	 */
	public function get_post_type_counts() {
		global $wpdb;

		$post_type_counts = $wpdb->get_results( "SELECT post_type AS 'type', count(1) AS 'count' FROM {$wpdb->posts} GROUP BY post_type;" );

		return is_array( $post_type_counts ) ? $post_type_counts : array();
	}

	/**
	 * Get a list of plugins active on the site.
	 *
	 * @return array
	 */
	public function get_active_plugins() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		require_once( ABSPATH . 'wp-admin/includes/update.php' );

		if ( ! function_exists( 'get_plugin_updates' ) ) {
			return array();
		}

		// Get both site plugins and network plugins
		$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}

		$active_plugins_data = array();
		$available_updates   = get_plugin_updates();

		foreach ( $active_plugins as $plugin ) {
			$data           = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$dirname        = dirname( $plugin );
			$version_latest = '';
			$slug           = explode( '/', $plugin );
			$slug           = explode( '.', end( $slug ) );
			$slug           = $slug[0];

			if ( isset( $available_updates[ $plugin ]->update->new_version ) ) {
				$version_latest = $available_updates[ $plugin ]->update->new_version;
			}

			// convert plugin data to json response format.
			$active_plugins_data[] = array(
				'plugin'            => $plugin,
				'name'              => $data['Name'],
				'version'           => $data['Version'],
				'version_latest'    => $version_latest,
				'url'               => $data['PluginURI'],
				'author_name'       => $data['AuthorName'],
				'author_url'        => esc_url_raw( $data['AuthorURI'] ),
				'network_activated' => $data['Network'],
			);
		}

		return $active_plugins_data;
	}

	/**
	 * Get info on the current active theme, info on parent theme (if presnet)
	 * and a list of template overrides.
	 *
	 * @return array
	 */
	public function get_theme_info() {
		$active_theme = wp_get_theme();

		// Get parent theme info if this theme is a child theme, otherwise
		// pass empty info in the response.
		if ( is_child_theme() ) {
			$parent_theme      = wp_get_theme( $active_theme->Template );
			$parent_theme_info = array(
				'parent_name'           => $parent_theme->Name,
				'parent_version'        => $parent_theme->Version,
				'parent_version_latest' => \WeDevs\ERP\Status::get_latest_theme_version( $parent_theme ),
				'parent_author_url'     => $parent_theme->{'Author URI'},
			);
		} else {
			$parent_theme_info = array( 'parent_name' => '', 'parent_version' => '', 'parent_version_latest' => '', 'parent_author_url' => '' );
		}

		$active_theme_info = array(
			'name'                    => $active_theme->Name,
			'version'                 => $active_theme->Version,
			'version_latest'          => \WeDevs\ERP\Status::get_latest_theme_version( $active_theme ),
			'author_url'              => esc_url_raw( $active_theme->{'Author URI'} ),
			'is_child_theme'          => is_child_theme()
		);

		return array_merge( $active_theme_info, $parent_theme_info );
	}

	/**
	 * Returns security tips.
	 *
	 * @return array
	 */
	public function get_security_info() {
		return array(
			'secure_connection' => 'https' === substr( get_home_url(), 0, 5 ),
			'hide_errors'       => ! ( defined( 'WP_DEBUG' ) && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG && WP_DEBUG_DISPLAY ) || 0 === intval( ini_get( 'display_errors' ) ),
		);
	}

	/**
	 * Get any query params needed.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param( array( 'default' => 'view' ) ),
		);
	}

}
