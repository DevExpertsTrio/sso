<?php

namespace BPCSSO\Helper;

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class Dbhandler {
	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function bpc_sso_create_saml_table() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'bpc_saml_idp_cofigs';

		$sql = 'CREATE TABLE IF NOT EXISTS ' . $table_name . " (
            id mediumint(20) NOT NULL auto_increment,
            idp_name text NOT NULL,
            idp_entity_id longtext NOT NULL,
            idp_login_url longtext NOT NULL,
            idp_certificate longtext NOT NULL,
            map_attribute longtext NOT NULL,
            map_role longtext NOT NULL,
            redirection longtext NOT NULL,
            idp_status bool,
            PRIMARY KEY (id)
        )$charset_collate;";

		dbDelta( $sql );
	}


}
