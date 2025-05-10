<?php

namespace BPCSSO\Frontend;

use BPCSSO\Frontend\SAML\saml;
use BPCSSO\Frontend\Oauth\oauth;
use BPCSSO\Helper\BpcConstants;
use BPCSSO\Helper\bpcwrapper;

class AdminSettings {
	private static $instance;

	// Public method to get or create the instance
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function bpc_sso__save_settings() {
        error_log('here');
        error_log('POST: ' . print_r($_POST, true));
        if( ! current_user_can( 'manage_option' ) && empty( $_POST['tab'] ) && ! empty( $_POST['option'] ) && ! check_admin_referer( sanitize_text_field( wp_unslash( $_POST['option'] ) ) ) ) {
            return;
        }

        $option = ! empty( $_POST['option'] ) ? sanitize_text_field( wp_unslash( $_POST['option'] ) ) : '';
        if( empty( $option ) ) {
            return;
        }
        $page  = ! empty( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';

        $handler = AdminSettings::get_instance();
        switch( $page ) {
            case 'saml':
                $handler = SamlSettings::get_instance();
                break;
            // case 'oauth':
            //     $handler = oauth::get_instance();
            //     break;
        }

        $handler->bpc_sso_save_settings_controller($option);
    }

    public static function bpc_sso_save_settings_controller($option) {
        echo 'No class found';
    }
}
