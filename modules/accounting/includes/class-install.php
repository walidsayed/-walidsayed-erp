<?php
namespace WeDevs\ERP\Accounting;
/**
 * Installer class
 */
class Install {

    public function install() {
        $this->create_tables();
        $this->populate_data();
        $this->create_roles();
        $this->set_role();
    }

    /**
     * Create user roles and capabilities
     *
     * @since 0.1
     *
     * @return void
     */
    public function create_roles() {
        $roles = erp_ac_get_roles();

        if ( $roles ) {
            foreach ($roles as $key => $role) {
                add_role( $key, $role['name'], $role['capabilities'] );
            }
        }
    }

    function set_role() {
        $admins = get_users( array( 'role' => 'administrator' ) );

        if ( $admins ) {
            foreach ($admins as $user) {
                $user->add_role( erp_ac_get_manager_role() );
            }
        }
    }

    /**
     * Create necessary tables
     *
     * @since 0.1
     *
     * @return  void
     */
    public function create_tables() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            if ( ! empty($wpdb->charset ) ) {
                $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
            }

            if ( ! empty($wpdb->collate ) ) {
                $collate .= " COLLATE $wpdb->collate";
            }
        }

        $table_schema = [
            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_chart_classes` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(100) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_chart_types` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(60) NOT NULL DEFAULT '',
              `class_id` tinyint(3) NOT NULL,
              PRIMARY KEY (`id`),
              KEY `class_id` (`class_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_journals` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `ledger_id` int(11) unsigned NOT NULL,
              `transaction_id` bigint(20) unsigned NOT NULL,
              `type` varchar(20) DEFAULT NULL,
              `debit` DECIMAL(13,4) unsigned DEFAULT NULL,
              `credit` DECIMAL(13,4) unsigned DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `ledger_id` (`ledger_id`),
              KEY `transaction_id` (`transaction_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_ledger` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `code` varchar(10) DEFAULT NULL,
              `name` varchar(100) DEFAULT NULL,
              `description` text,
              `parent` int(11) unsigned NOT NULL DEFAULT '0',
              `type_id` int(3) unsigned NOT NULL DEFAULT '0',
              `currency` varchar(10) DEFAULT '',
              `tax` bigint(20) DEFAULT NULL,
              `cash_account` tinyint(2) unsigned NOT NULL DEFAULT '0',
              `reconcile` tinyint(2) unsigned NOT NULL DEFAULT '0',
              `system` tinyint(2) unsigned NOT NULL DEFAULT '0',
              `active` tinyint(2) unsigned NOT NULL DEFAULT '1',
              PRIMARY KEY (`id`),
              KEY `code` (`code`),
              KEY `type_id` (`type_id`),
              KEY `parent` (`parent`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_banks` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `ledger_id` int(10) unsigned DEFAULT NULL,
              `account_number` varchar(20) DEFAULT NULL,
              `bank_name` varchar(30) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `ledger_id` (`ledger_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_transactions` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `type` varchar(10) DEFAULT NULL,
              `form_type` varchar(20) DEFAULT NULL,
              `status` varchar(20) DEFAULT NULL,
              `user_id` bigint(20) unsigned DEFAULT NULL,
              `billing_address` tinytext,
              `ref` varchar(50) DEFAULT NULL,
              `summary` text,
              `issue_date` date DEFAULT NULL,
              `due_date` date DEFAULT NULL,
              `currency` varchar(10) DEFAULT NULL,
              `conversion_rate` decimal(2,2) unsigned DEFAULT NULL,
              `total` DECIMAL(13,4) DEFAULT '0.00',
              `due` DECIMAL(13,4) unsigned DEFAULT '0.00',
              `trans_total` DECIMAL(13,4) DEFAULT '0.00',
              `files` varchar(255) DEFAULT NULL,
              `parent` bigint(20) unsigned NOT NULL DEFAULT '0',
              `created_by` int(11) unsigned DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `user_id` (`user_id`),
              KEY `type` (`type`),
              KEY `status` (`status`),
              KEY `issue_date` (`issue_date`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_transaction_items` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `transaction_id` bigint(20) unsigned DEFAULT NULL,
              `journal_id` bigint(20) unsigned DEFAULT NULL,
              `product_id` int(10) unsigned DEFAULT NULL,
              `description` text,
              `qty` tinyint(5) unsigned NOT NULL DEFAULT '1',
              `unit_price` DECIMAL(13,4) unsigned NOT NULL DEFAULT '0.00',
              `discount` tinyint(3) unsigned NOT NULL DEFAULT '0',
              `tax` tinyint(3) unsigned NOT NULL DEFAULT '0',
              `line_total` DECIMAL(13,4) unsigned NOT NULL DEFAULT '0.00',
              `order` tinyint(3) unsigned NOT NULL DEFAULT '0',
              PRIMARY KEY (`id`),
              KEY `transaction_id` (`transaction_id`),
              KEY `journal_id` (`journal_id`),
              KEY `product_id` (`product_id`)
            ) $collate;",

          "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_payments` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `transaction_id` int(11) NOT NULL,
            `parent` int(11) NOT NULL,
            `child` int(11) NOT NULL,
            PRIMARY KEY (`id`)
          ) $collate;",

          "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_tax` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(255) DEFAULT NULL,
            `tax_number` varchar(255) DEFAULT NULL,
            `is_compound` varchar(5) DEFAULT NULL,
            `created_by` bigint(20) NOT NULL,
             PRIMARY KEY (`id`)
          ) $collate;",
          
          "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_ac_tax_items` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `tax_id` bigint(20) NOT NULL,
            `component_name` varchar(255) DEFAULT NULL,
            `agency_name` varchar(255) DEFAULT NULL,
            `tax_rate` float NOT NULL,
            PRIMARY KEY (`id`)
          ) $collate;",
          
          "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_company_locations` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `company_id` int(11) unsigned DEFAULT NULL,
                `name` varchar(255) DEFAULT NULL,
                `address_1` varchar(255) DEFAULT NULL,
                `address_2` varchar(255) DEFAULT NULL,
                `city` varchar(100) DEFAULT NULL,
                `state` varchar(100) DEFAULT NULL,
                `zip` varchar(10) DEFAULT NULL,
                `country` varchar(5) DEFAULT NULL,
                `fax` varchar(20) DEFAULT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `company_id` (`company_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_depts` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `title` varchar(200) NOT NULL DEFAULT '',
                `description` text,
                `lead` int(11) unsigned DEFAULT '0',
                `parent` int(11) unsigned DEFAULT '0',
                `status` tinyint(1) unsigned DEFAULT '1',
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_designations` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `title` varchar(200) NOT NULL DEFAULT '',
                `description` text,
                `status` tinyint(1) DEFAULT '1',
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_employees` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `employee_id` varchar(20) DEFAULT NULL,
                `designation` int(11) unsigned NOT NULL DEFAULT '0',
                `department` int(11) unsigned NOT NULL DEFAULT '0',
                `location` int(10) unsigned NOT NULL DEFAULT '0',
                `hiring_source` varchar(20) NOT NULL,
                `hiring_date` date NOT NULL,
                `termination_date` date NOT NULL,
                `date_of_birth` date NOT NULL,
                `reporting_to` bigint(20) unsigned NOT NULL DEFAULT '0',
                `pay_rate` int(11) unsigned NOT NULL DEFAULT '0',
                `pay_type` varchar(20) NOT NULL DEFAULT '',
                `type` varchar(20) NOT NULL,
                `status` varchar(10) NOT NULL DEFAULT '',
                `deleted_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_employee_history` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `module` varchar(20) DEFAULT NULL,
                `category` varchar(20) DEFAULT NULL,
                `type` varchar(20) DEFAULT NULL,
                `comment` text,
                `data` longtext,
                `date` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `module` (`module`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_employee_notes` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `comment` text NOT NULL,
                `comment_by` bigint(20) unsigned NOT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_leave_policies` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(20) DEFAULT NULL,
                `value` mediumint(5) DEFAULT NULL,
                `color` varchar(7) DEFAULT NULL,
                `department` int(11) NOT NULL,
                `designation` int(11) NOT NULL,
                `gender` varchar(50) NOT NULL,
                `marital` varchar(50) NOT NULL,
                `description` LONGTEXT NOT NULL,
                `location` INT(3) NOT NULL,
                `effective_date` TIMESTAMP NOT NULL,
                `activate` INT(2) NOT NULL,
                `execute_day` INT(11) NOT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_holiday` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `title` varchar(200) NOT NULL,
                `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                `description` text NOT NULL,
                `range_status` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                `created_at` datetime NOT NULL,
                `updated_at` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_leave_entitlements` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL,
                `policy_id` int(11) unsigned DEFAULT NULL,
                `days` mediumint(4) DEFAULT NULL,
                `from_date` datetime NOT NULL,
                `to_date` datetime NOT NULL,
                `comments` text,
                `status` tinyint(2) unsigned NOT NULL,
                `created_by` bigint(20) unsigned DEFAULT NULL,
                `created_on` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_leaves` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `request_id` bigint(20) unsigned NOT NULL,
                `date` date NOT NULL,
                `length_hours` decimal(6,2) unsigned NOT NULL,
                `length_days` decimal(6,2) NOT NULL,
                `start_time` time NOT NULL,
                `end_time` time NOT NULL,
                `duration_type` tinyint(4) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                KEY `request_id` (`request_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_leave_requests` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL,
                `policy_id` int(11) unsigned NOT NULL,
                `days` tinyint(3) unsigned DEFAULT NULL,
                `start_date` datetime NOT NULL,
                `end_date` datetime NOT NULL,
                `comments` text,
                `reason` text NOT NULL,
                `status` tinyint(2) unsigned DEFAULT NULL,
                `created_by` bigint(20) unsigned DEFAULT NULL,
                `updated_by` bigint(20) unsigned DEFAULT NULL,
                `created_on` datetime NOT NULL,
                `updated_on` datetime DEFAULT NULL,
                `last_date` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `policy_id` (`policy_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_work_exp` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `employee_id` int(11) DEFAULT NULL,
                `company_name` varchar(100) DEFAULT NULL,
                `job_title` varchar(100) DEFAULT NULL,
                `from` date DEFAULT NULL,
                `to` date DEFAULT NULL,
                `description` text,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `employee_id` (`employee_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_education` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `employee_id` int(11) unsigned DEFAULT NULL,
                `school` varchar(100) DEFAULT NULL,
                `degree` varchar(100) DEFAULT NULL,
                `field` varchar(100) DEFAULT NULL,
                `finished` int(4) unsigned DEFAULT NULL,
                `notes` text,
                `interest` text,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `employee_id` (`employee_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_dependents` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `employee_id` int(11) DEFAULT NULL,
                `name` varchar(100) DEFAULT NULL,
                `relation` varchar(100) DEFAULT NULL,
                `dob` date DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `employee_id` (`employee_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_employee_performance` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `employee_id` int(11) unsigned DEFAULT NULL,
                `reporting_to` int(11) unsigned DEFAULT NULL,
                `job_knowledge` varchar(100) DEFAULT NULL,
                `work_quality` varchar(100) DEFAULT NULL,
                `attendance` varchar(100) DEFAULT NULL,
                `communication` varchar(100) DEFAULT NULL,
                `dependablity` varchar(100) DEFAULT NULL,
                `reviewer` int(11) unsigned DEFAULT NULL,
                `comments` text,
                `completion_date` datetime DEFAULT NULL,
                `goal_description` text,
                `employee_assessment` text,
                `supervisor` int(11) unsigned DEFAULT NULL,
                `supervisor_assessment` text,
                `type` text,
                `performance_date` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `employee_id` (`employee_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_hr_announcement` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL,
                `post_id` bigint(11) NOT NULL,
                `status` varchar(30) NOT NULL,
                PRIMARY KEY (id)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_peoples` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned DEFAULT '0',
                `first_name` varchar(60) DEFAULT NULL,
                `last_name` varchar(60) DEFAULT NULL,
                `company` varchar(60) DEFAULT NULL,
                `email` varchar(100) DEFAULT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `mobile` varchar(20) DEFAULT NULL,
                `other` varchar(50) DEFAULT NULL,
                `website` varchar(100) DEFAULT NULL,
                `fax` varchar(20) DEFAULT NULL,
                `notes` text,
                `street_1` varchar(255) DEFAULT NULL,
                `street_2` varchar(255) DEFAULT NULL,
                `city` varchar(80) DEFAULT NULL,
                `state` varchar(50) DEFAULT NULL,
                `postal_code` varchar(10) DEFAULT NULL,
                `country` varchar(20) DEFAULT NULL,
                `currency` varchar(5) DEFAULT NULL,
                `created` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_peoplemeta` (
                `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `erp_people_id` bigint(20) DEFAULT NULL,
                `meta_key` varchar(255) DEFAULT NULL,
                `meta_value` longtext,
                PRIMARY KEY (`meta_id`),
                KEY `erp_people_id` (`erp_people_id`),
                KEY `meta_key` (`meta_key`(191))
            ) $collate;",


            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_people_types` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(20) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `name` (`name`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_people_type_relations` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `people_id` bigint(20) unsigned DEFAULT NULL,
                `people_types_id` int(11) unsigned DEFAULT NULL,
                `deleted_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `people_id` (`people_id`),
                KEY `people_types_id` (`people_types_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_audit_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `component` varchar(50) NOT NULL DEFAULT '',
                `sub_component` varchar(50) NOT NULL DEFAULT '',
                `old_value` longtext,
                `new_value` longtext,
                `message` longtext,
                `changetype` varchar(10) DEFAULT NULL,
                `created_by` bigint(20) unsigned DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_crm_customer_companies` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `customer_id` bigint(20) DEFAULT NULL,
                `company_id` bigint(50) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_crm_customer_activities` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` int(11) DEFAULT NULL,
                `type` varchar(255) DEFAULT NULL,
                `message` longtext,
                `email_subject` text,
                `log_type` varchar(255) DEFAULT NULL,
                `start_date` datetime DEFAULT NULL,
                `end_date` datetime DEFAULT NULL,
                `created_by` int(11) DEFAULT NULL,
                `extra` longtext,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_crm_activities_task` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `activity_id` int(11) DEFAULT NULL,
                `user_id` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_crm_contact_group` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) DEFAULT NULL,
                `description` text,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_crm_contact_subscriber` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `user_id` int(11) DEFAULT NULL,
              `group_id` int(11) DEFAULT NULL,
              `status` varchar(25) DEFAULT NULL,
              `subscribe_at` datetime DEFAULT NULL,
              `unsubscribe_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `user_group` (`user_id`,`group_id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_crm_campaigns` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `title` text,
              `description` longtext,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_crm_campaign_group` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `campaign_id` int(11) DEFAULT NULL,
              `group_id` int(11) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_crm_save_search` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `user_id` int(11) DEFAULT NULL,
              `global` tinyint(4) DEFAULT '0',
              `search_name` text,
              `search_val` text,
              PRIMARY KEY (`id`)
            ) $collate;",

            "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}erp_crm_save_email_replies` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` text,
              `subject` text,
              `template` longtext,
              PRIMARY KEY (`id`)
            ) $collate;"

        ];

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        foreach ( $table_schema as $table ) {
            dbDelta( $table );
        }
    }

    public function populate_data() {
        global $wpdb;

        // check if classes exists
        if ( ! $wpdb->get_var( "SELECT id FROM `{$wpdb->prefix}erp_ac_chart_classes` LIMIT 0, 1" ) ) {
            $sql = "INSERT INTO `{$wpdb->prefix}erp_ac_chart_classes` (`id`, `name`)
                    VALUES (1,'Assets'), (2,'Liabilities'), (3,'Expenses'), (4,'Income'), (5,'Equity');";

            $wpdb->query( $sql );
        }

        // check if chart types exists
        if ( ! $wpdb->get_var( "SELECT id FROM `{$wpdb->prefix}erp_ac_chart_types` LIMIT 0, 1" ) ) {
            $sql = "INSERT INTO `{$wpdb->prefix}erp_ac_chart_types` (`id`, `name`, `class_id`)
                    VALUES (1,'Current Asset',1), (2,'Fixed Asset',1), (3,'Inventory',1),
                        (4,'Non-current Asset',1), (5,'Prepayment',1), (6,'Bank & Cash',1), (7,'Current Liability',2),
                        (8,'Liability',2), (9,'Non-current Liability',2), (10,'Depreciation',3),
                        (11,'Direct Costs',3), (12,'Expense',3), (13,'Revenue',4), (14,'Sales',4),
                        (15,'Other Income',4), (16,'Equity',5);";

            $wpdb->query( $sql );
        }

        // check if ledger exists
        if ( ! $wpdb->get_var( "SELECT id FROM `{$wpdb->prefix}erp_ac_ledger` LIMIT 0, 1" ) ) {

            $sql = "INSERT INTO `{$wpdb->prefix}erp_ac_ledger` (`id`, `code`, `name`, `description`, `parent`, `type_id`, `currency`, `tax`, `cash_account`, `reconcile`, `system`, `active`)
                        VALUES
                        (1,'120','Accounts Receivable',NULL,0,1,'',NULL,0,0,1,1),
                        (2,'140','Inventory',NULL,0,3,'',NULL,0,0,1,1),
                        (3,'150','Office Equipment',NULL,0,2,'',NULL,0,0,1,1),
                        (4,'151','Less Accumulated Depreciation on Office Equipment',NULL,0,2,'',NULL,0,0,1,1),
                        (5,'160','Computer Equipment',NULL,0,2,'',NULL,0,0,1,1),
                        (6,'161','Less Accumulated Depreciation on Computer Equipment',NULL,0,2,'',NULL,0,0,1,1),
                        (7,'090','Petty Cash',NULL,0,6,'',NULL,1,1,0,1),
                        (8,'200','Accounts Payable',NULL,0,7,'',NULL,0,0,1,1),
                        (9,'205','Accruals',NULL,0,7,'',NULL,0,0,0,1),
                        (10,'210','Unpaid Expense Claims',NULL,0,7,'',NULL,0,0,1,1),
                        (11,'215','Wages Payable',NULL,0,7,'',NULL,0,0,1,1),
                        (12,'216','Wages Payable - Payroll',NULL,0,7,'',NULL,0,0,0,1),
                        (13,'220','Sales Tax',NULL,0,7,'',NULL,0,0,1,1),
                        (14,'230','Employee Tax Payable',NULL,0,7,'',NULL,0,0,0,1),
                        (15,'235','Employee Benefits Payable',NULL,0,7,'',NULL,0,0,0,1),
                        (16,'236','Employee Deductions payable',NULL,0,7,'',NULL,0,0,0,1),
                        (17,'240','Income Tax Payable',NULL,0,7,'',NULL,0,0,0,1),
                        (18,'250','Suspense',NULL,0,7,'',NULL,0,0,0,1),
                        (19,'255','Historical Adjustments',NULL,0,7,'',NULL,0,0,1,1),
                        (20,'260','Rounding',NULL,0,7,'',NULL,0,0,1,1),
                        (21,'835','Revenue Received in Advance',NULL,0,7,'',NULL,0,0,0,1),
                        (22,'855','Clearing Account',NULL,0,7,'',NULL,0,0,0,1),
                        (23,'290','Loan',NULL,0,9,'',NULL,0,0,0,1),
                        (24,'500','Costs of Goods Sold',NULL,0,11,'',NULL,0,0,1,1),
                        (25,'600','Advertising',NULL,0,12,'',NULL,0,0,0,1),
                        (26,'605','Bank Service Charges',NULL,0,12,'',NULL,0,0,0,1),
                        (27,'610','Janitorial Expenses',NULL,0,12,'',NULL,0,0,0,1),
                        (28,'615','Consulting & Accounting',NULL,0,12,'',NULL,0,0,0,1),
                        (29,'620','Entertainment',NULL,0,12,'',NULL,0,0,0,1),
                        (30,'624','Postage & Delivary',NULL,0,12,'',NULL,0,0,0,1),
                        (31,'628','General Expenses',NULL,0,12,'',NULL,0,0,0,1),
                        (32,'632','Insurance',NULL,0,12,'',NULL,0,0,0,1),
                        (33,'636','Legal Expenses',NULL,0,12,'',NULL,0,0,0,1),
                        (34,'640','Utilities',NULL,0,12,'',NULL,0,0,1,1),
                        (35,'644','Automobile Expenses',NULL,0,12,'',NULL,0,0,0,1),
                        (36,'648','Office Expenses',NULL,0,12,'',NULL,0,0,1,1),
                        (37,'652','Printing & Stationary',NULL,0,12,'',NULL,0,0,0,1),
                        (38,'656','Rent',NULL,0,12,'',NULL,0,0,1,1),
                        (39,'660','Repairs & Maintenance',NULL,0,12,'',NULL,0,0,0,1),
                        (40,'664','Wages & Salaries',NULL,0,12,'',NULL,0,0,0,1),
                        (41,'668','Payroll Tax Expense',NULL,0,12,'',NULL,0,0,0,1),
                        (42,'672','Dues & Subscriptions',NULL,0,12,'',NULL,0,0,0,1),
                        (43,'676','Telephone & Internet',NULL,0,12,'',NULL,0,0,0,1),
                        (44,'680','Travel',NULL,0,12,'',NULL,0,0,0,1),
                        (45,'684','Bad Debts',NULL,0,12,'',NULL,0,0,0,1),
                        (46,'700','Depreciation',NULL,0,10,'',NULL,0,0,1,1),
                        (47,'710','Income Tax Expense',NULL,0,12,'',NULL,0,0,0,1),
                        (48,'715','Employee Benefits Expense',NULL,0,12,'',NULL,0,0,0,1),
                        (49,'800','Interest Expense',NULL,0,12,'',NULL,0,0,0,1),
                        (50,'810','Bank Revaluations',NULL,0,12,'',NULL,0,0,1,1),
                        (51,'815','Unrealized Currency Gains',NULL,0,12,'',NULL,0,0,1,1),
                        (52,'820','Realized Currency Gains',NULL,0,12,'',NULL,0,0,1,1),
                        (53,'825','Sales Discount',NULL,0,12,'',NULL,0,0,1,1),
                        (54,'400','Sales',NULL,0,13,'',NULL,0,0,0,1),
                        (55,'460','Interest Income',NULL,0,13,'',NULL,0,0,0,1),
                        (56,'470','Other Revenue',NULL,0,13,'',NULL,0,0,0,1),
                        (57,'475','Purchase Discount',NULL,0,13,'',NULL,0,0,1,1),
                        (58,'300','Owners Contribution',NULL,0,16,'',NULL,0,0,0,1),
                        (59,'310','Owners Draw',NULL,0,16,'',NULL,0,0,0,1),
                        (60,'320','Retained Earnings',NULL,0,16,'',NULL,0,0,1,1),
                        (61,'330','Common Stock',NULL,0,16,'',NULL,0,0,0,1),
                        (62,'092','Savings Account',NULL,0,6,'',NULL,1,1,0,1);";

            $wpdb->query( $sql );
        }

        // check if banks exists
        if ( ! $wpdb->get_var( "SELECT id FROM `{$wpdb->prefix}erp_ac_banks` LIMIT 0, 1" ) ) {
            $sql = "INSERT INTO `{$wpdb->prefix}erp_ac_banks` (`id`, `ledger_id`, `account_number`, `bank_name`)
                    VALUES  (1,7,'',''), (2,62,'012345689','ABC Bank');";

            $wpdb->query( $sql );
        }
    }
}