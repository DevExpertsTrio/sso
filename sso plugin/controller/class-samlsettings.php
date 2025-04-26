<?php

namespace BPCSSO\Frontend;

use BPCSSO\Frontend\SAML\saml;
use BPCSSO\Frontend\Oauth\oauth;
use BPCSSO\Helper\BpcConstants;
use BPCSSO\Helper\bpcwrapper;

class SamlSettings {
	private static $instance;

	// Public method to get or create the instance
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public static function bpc_sso_save_settings_controller($option) {
        check_admin_referer( 'bpc_sso_get_idp_metadata' );
        $idp_name = ! empty($_POST['idp-name'] ) ? sanitize_text_field( wp_unslash( $_POST['idp-name'] ) ) : '';
        error_log('IDP Name: ' . print_r($idp_name, true));

        $redirect_url = add_query_arg(
            array(
                'page' => 'bpc_sso',
                'proto' => 'saml',
                'step' => 2,
            ),
            admin_url( 'admin.php' )
        );
        wp_redirect( $redirect_url );
    }
}
