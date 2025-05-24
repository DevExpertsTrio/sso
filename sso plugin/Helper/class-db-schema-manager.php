<?php

namespace BPCSSO\Helper;

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class DbSchemaManager {

    public static function bpc_sso_install_all_tables() {
        self::bpc_sso_create_admin_details_table();
        self::bpc_sso_create_application_table();
        self::bpc_sso_create_saml_sp_metadata_table();
        self::bpc_sso_create_saml_metadata_table();
        self::bpc_sso_create_oauth_data_table();
        self::bpc_sso_create_sp_oauth_data_table();
    }

    private static function use_json_column() {
        global $wpdb;
        $mysql_version = $wpdb->db_version();
        return version_compare($mysql_version, '5.7.0', '>=');
    }

    public static function bpc_sso_create_admin_details_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $engine = 'ENGINE=InnoDB';
        $table_name = $wpdb->prefix . 'bpc_admin_details';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            config_key VARCHAR(255) NOT NULL,
            value VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate $engine;";

        dbDelta($sql);
    }

    public static function bpc_sso_create_application_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $engine = 'ENGINE=InnoDB';
        $table_name = $wpdb->prefix . 'bpc_application';

        $json_type = self::use_json_column() ? 'JSON' : 'LONGTEXT';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL UNIQUE,
            protocol VARCHAR(50) NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            redirection_url $json_type NOT NULL,
            role_mapping $json_type NOT NULL,
            attribute_mapping $json_type NOT NULL,
            button_settings $json_type,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate $engine;";

        dbDelta($sql);
    }

    public static function bpc_sso_create_saml_metadata_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $engine = 'ENGINE=InnoDB';
        $table_name = $wpdb->prefix . 'bpc_metadata';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            sp_id BIGINT(20) NOT NULL,
            idp_id BIGINT(20) NOT NULL,
            entity_id VARCHAR(255) NOT NULL,
            saml_login_url VARCHAR(2083) NOT NULL,
            name_id VARCHAR(255) NOT NULL,
            binding_url VARCHAR(2083) NOT NULL,
            metadata_url VARCHAR(2083) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (sp_id) REFERENCES {$wpdb->prefix}bpc_sp_metadata(id),
            FOREIGN KEY (idp_id) REFERENCES {$wpdb->prefix}bpc_application(id)
        ) $charset_collate $engine;";

        dbDelta($sql);
    }

    public static function bpc_sso_create_saml_sp_metadata_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $engine = 'ENGINE=InnoDB';
        $table_name = $wpdb->prefix . 'bpc_sp_metadata';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            entity_id VARCHAR(255) NOT NULL,
            audience_uri VARCHAR(2083) NOT NULL,
            acs_url VARCHAR(2083) NOT NULL,
            metadata_url VARCHAR(2083) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate $engine;";

        dbDelta($sql);
    }

    public static function bpc_sso_create_oauth_data_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $engine = 'ENGINE=InnoDB';
        $table_name = $wpdb->prefix . 'bpc_oauth_data';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            sp_id BIGINT(20) NOT NULL,
            idp_id BIGINT(20) NOT NULL,
            client_id VARCHAR(255) NOT NULL,
            client_secret VARCHAR(255) NOT NULL,
            access_token_url VARCHAR(2083) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (sp_id) REFERENCES {$wpdb->prefix}bpc_sp_metadata(id),
            FOREIGN KEY (idp_id) REFERENCES {$wpdb->prefix}bpc_application(id)
        ) $charset_collate $engine;";

        dbDelta($sql);
    }

    public static function bpc_sso_create_sp_oauth_data_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $engine = 'ENGINE=InnoDB';
        $table_name = $wpdb->prefix . 'bpc_sp_oauth_data';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT(20) NOT NULL AUTO_INCREMENT,
            client_id VARCHAR(255) NOT NULL,
            client_secret VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate $engine;";

        dbDelta($sql);
    }
}
