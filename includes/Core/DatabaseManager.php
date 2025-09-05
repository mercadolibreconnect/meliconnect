<?php

namespace Meliconnect\Meliconnect\Core;


/**
 * Class DatabaseManager
 * @package Meliconnect\Meliconnect
 */
class DatabaseManager
{
    private $prefix;
    private $charset_collate;

    public $tables_names = [];

    public function __construct()
    {
        global $wpdb;
        $this->prefix = $wpdb->prefix . 'melicon_';
        $this->charset_collate = $wpdb->get_charset_collate();
    }

    public function create_or_update_tables()
    {
        $this->create_or_update_notification();
        $this->create_or_update_user_connection();
        $this->create_or_update_template();

        //Always after create_or_update_template
        $this->create_or_update_template_meta();
        $this->create_or_update_template_attributes();
        $this->create_or_update_process();
        $this->create_or_update_process_item();
        $this->create_or_update_user_listings_to_import();
        $this->create_or_update_products_to_export();
    }

    public function create_or_update_notification()
    {
        $table_name = $this->prefix . 'notifications';

        $this->tables_names[] = $table_name;

        $sql = "CREATE TABLE {$table_name} (
            `id` mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
            `unique_id` varchar(250) NOT NULL,
			`title_html` varchar(250) DEFAULT NULL,
			`text_html` text DEFAULT NULL,
			`icon_html` text DEFAULT NULL,
			`bt1_text` varchar(250) DEFAULT NULL,
			`bt1_link` varchar(250) DEFAULT NULL,
			`bt2_text` varchar(250) DEFAULT NULL,
			`bt2_link` varchar(250) DEFAULT NULL,
			`from_version` float DEFAULT 4,
			`date_from` timestamp NOT NULL DEFAULT current_timestamp(),
			`status` enum('show','test','hide') NOT NULL DEFAULT 'hide',
			`is_dismissed` tinyint(1) NOT NULL DEFAULT 0,
			`is_made` tinyint(1) NOT NULL DEFAULT 0,
			`created_at` timestamp NULL DEFAULT current_timestamp(),
			`updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY  (id)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function create_or_update_user_connection()
    {
        $table_name = $this->prefix . 'user_connection';

        $this->tables_names[] = $table_name;

        $sql = "CREATE TABLE {$table_name} (
            `id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `access_token` varchar(255) NOT NULL,
            `app_id` varchar(255) NOT NULL,
            `secret_key` varchar(255) NOT NULL,
            `user_id` bigint(20) NOT NULL,
            `nickname` varchar(255) NOT NULL,
            `permalink` varchar(255) NOT NULL,
            `site_id` varchar(255) NOT NULL,
            `country` varchar(255) NOT NULL,
            `has_mercadoshops` boolean NOT NULL DEFAULT false,
            `status` varchar(50) NOT NULL,
            `meli_user_data` text DEFAULT NULL,
            `api_token` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
			`updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY  (id),
            UNIQUE KEY user_id (user_id)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


    public function create_or_update_template()
    {
        $table_name = $this->prefix . 'templates';

        $this->tables_names[] = $table_name;

        $sql = "CREATE TABLE {$table_name} (
            `id` mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
            `used_by` enum('product','template') NOT NULL DEFAULT 'template',
			`used_asoc_id` varchar(100) DEFAULT NULL,
			`template_parent_id` varchar(200) DEFAULT NULL,
			`seller_meli_id` varchar(100) NOT NULL,
			`name` varchar(200) NOT NULL,
			`short_description` varchar(200) DEFAULT NULL,
			`category_id` varchar(80) NOT NULL,
            `category_name` varchar(80) DEFAULT NULL,
            `category_path` text DEFAULT NULL,
			`channels` enum('mercadolibre','mercadoshop','all') NOT NULL DEFAULT 'mercadolibre',
			`status` tinyint(1) NOT NULL DEFAULT 1,
			`created_at` timestamp NULL DEFAULT current_timestamp(),
			`updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY  (id),
			UNIQUE KEY `unique_template_by_used_asoc` (`used_asoc_id`,`used_by`)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function create_or_update_template_meta()
    {
        $table_name = $this->prefix .  'template_metas';
        $this->tables_names[] = $table_name;

        $sql = "CREATE TABLE {$table_name} (
            `meta_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `template_id` MEDIUMINT(9) UNSIGNED NOT NULL,
            `meta_key` VARCHAR(255) DEFAULT NULL,
            `meta_value` LONGTEXT DEFAULT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (meta_id),
            KEY template_id (template_id)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function create_or_update_template_attributes()
    {
        $table_name = $this->prefix . 'template_attributes';
        $this->tables_names[] = $table_name;

        $sql = "CREATE TABLE {$table_name} (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `template_id` varchar(200) DEFAULT NULL,
            `used_by` enum('product','template','variation') NOT NULL DEFAULT 'template',
			`used_asoc_id` int(11) DEFAULT NULL,
            `meli_variation_id` varchar(100) DEFAULT NULL,
			`meli_attribute_id` varchar(100) DEFAULT NULL,
            `meli_attribute_name` varchar(250) DEFAULT NULL,
            `meli_value_id` varchar(250) DEFAULT NULL,
            `meli_value_name` varchar(250) DEFAULT NULL,
            `meli_value_type` varchar(250) DEFAULT NULL,
            `product_parent_id` int(11) DEFAULT NULL,
            `woo_attribute_id` int(20) DEFAULT NULL,
            `allow_variations_tag` tinyint(1) NOT NULL DEFAULT 0, /* Attribute used in variation combination */
            `variation_attribute_tag` tinyint(1) NOT NULL DEFAULT 0, /* Attribute used as variation attribute */
            `required_tag` tinyint(1) NOT NULL DEFAULT 0,
            `not_apply` tinyint(1) NOT NULL DEFAULT 0, 
			`created_at` timestamp NOT NULL DEFAULT current_timestamp(),
			`updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY  (id)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function create_or_update_process()
    {
        $table_name = $this->prefix . 'processes';
        $this->tables_names[] = $table_name;

        $sql = "CREATE TABLE {$table_name} (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `process_id` varchar(150) NOT NULL,
            `status` enum('pending','processing','paused','finished','failed') NOT NULL DEFAULT 'pending',
            `executed` int(11) DEFAULT 0,
            `total` int(11) NOT NULL DEFAULT 0,
            `process_type` enum('automatic-import','automatic-export','custom-import','custom-export','sync') NOT NULL,
            `total_success` int(11) NOT NULL DEFAULT 0,
            `total_fails` int(11) NOT NULL DEFAULT 0,
            `error_log` text DEFAULT NULL,
            `starts_at` timestamp NULL DEFAULT NULL,
            `ends_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
			`updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY  (id),
            UNIQUE KEY `unique_process_id` (`process_id`)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function create_or_update_process_item()
    {
        $table_name = $this->prefix . 'process_items';
        $this->tables_names[] = $table_name;

        $sql = "CREATE TABLE {$table_name} (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `process_id` varchar(200) NOT NULL,
            `meli_user_id` varchar(100) NULL,
            `meli_listing_id` varchar(100) NULL,
            `woo_product_id` varchar(100) NULL,
            `template_id` varchar(100) NULL,
            `process_status` enum('pending','processed','failed') NOT NULL DEFAULT 'pending',
            `process_error` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
			`updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY  (id),
            KEY process_id (process_id)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


    public function create_or_update_user_listings_to_import()
    {
        $table_name = $this->prefix . 'user_listings_to_import';
        $this->tables_names[] = $table_name;

        $sql = "CREATE TABLE {$table_name} (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `meli_listing_id` VARCHAR(50) NOT NULL,
            `meli_user_id` VARCHAR(50) NOT NULL,
            `meli_listing_title` VARCHAR(200) NOT NULL,
            `meli_response` LONGTEXT DEFAULT NULL,
            `meli_sku` VARCHAR(50) DEFAULT NULL,
            `meli_gtin` VARCHAR(50) DEFAULT NULL,
            `meli_status` VARCHAR(50) NOT NULL,
            `meli_sub_status` VARCHAR(50) NOT NULL,
            `meli_product_type` enum('variable','simple') DEFAULT NULL,
            `vinculated_product_id` BIGINT(20) UNSIGNED DEFAULT NULL,
            `is_product_match_by_sku` tinyint(1) DEFAULT 0,
            `is_product_match_by_gtin` tinyint(1) DEFAULT 0,
            `is_product_match_by_name` tinyint(1) DEFAULT 0,
            `is_product_match_manually` tinyint(1) DEFAULT 0,
            `vinculated_template_id` BIGINT(20) UNSIGNED DEFAULT NULL,
            `import_status` enum('pending','processing','canceled','finished','failed') DEFAULT NULL,
            `import_error` text DEFAULT NULL,
            `process_id` varchar(200) NOT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (id),
            KEY process_id (process_id),
            UNIQUE KEY meli_listing_id (meli_listing_id)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


    public function create_or_update_products_to_export()
    {
        $table_name = $this->prefix . 'products_to_export';
        $this->tables_names[] = $table_name;

        $sql = "CREATE TABLE {$table_name} (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `woo_product_id` BIGINT(20) UNSIGNED NOT NULL,
            `woo_product_name` VARCHAR(255) NOT NULL, 
            `woo_sku` VARCHAR(50) NOT NULL,
            `woo_gtin` VARCHAR(50) NOT NULL,
            `woo_product_type` ENUM('variable','simple') DEFAULT NULL,
            `woo_status` VARCHAR(50) NOT NULL,
            `vinculated_template_id` BIGINT(20) UNSIGNED DEFAULT NULL,
            `vinculated_listing_id` VARCHAR(100) DEFAULT NULL,
            `template_match_by` VARCHAR(50) DEFAULT NULL,
            `listing_match_by` VARCHAR(50) DEFAULT NULL,
            `meli_seller_id` VARCHAR(255) DEFAULT NULL,
            `meli_permalink` VARCHAR(255) DEFAULT NULL,
            /* `is_listing_match_by_sku` TINYINT(1) DEFAULT 0,
            `is_listing_match_by_name` TINYINT(1) DEFAULT 0,
            `is_listing_match_by_gtin` TINYINT(1) DEFAULT 0,
            `is_listing_match_manually` TINYINT(1) DEFAULT 0,
            `is_template_match_manually` TINYINT(1) DEFAULT 0, */
            `export_status` ENUM('pending','processing','canceled','finished','failed') DEFAULT NULL,
            `export_error` TEXT DEFAULT NULL,
            `process_id` VARCHAR(100) NOT NULL,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `process_id` (`process_id`),
            UNIQUE KEY `woo_product_id` (`woo_product_id`)
        ) {$this->charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
