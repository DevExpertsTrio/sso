<?php

namespace BPCSSO\Helper;

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

class bpcwrapper {
	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function bpc_sso_get_image_url( $image ) {
		return plugins_url( '../assets/images/' . $image, __FILE__ );
	}

	public static function bpc_sso_get_db_option( $option ) {
		return get_option( $option );
	}

	public static function bpc_sso_update_db_option( $option, $value ) {
		if ( current_user_can( 'manage_options' ) ) {
			return update_option( $option, $value );
		}
	}

	public static function bpc_sso_delete_db_option( $option ) {
		if ( current_user_can( 'manage_options' ) ) {
			return delete_option( $option );
		}
	}
}
