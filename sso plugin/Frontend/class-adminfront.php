<?php

namespace BPCSSO\Frontend;

use BPCSSO\Frontend\SAML\saml;
use BPCSSO\Frontend\Oauth\oauth;
use BPCSSO\Helper\BpcConstants;
use BPCSSO\Helper\bpcwrapper;

class adminFront {
	private static $instance;

	// Public method to get or create the instance
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function bpc_sso_protocol_main() {
		$saml_configuration  = bpcwrapper::bpc_sso_get_db_option( BpcConstants::SAML_CONFIG );
		$oauth_configuration = bpcwrapper::bpc_sso_get_db_option( BpcConstants::OAUTH_CONFIG );
		$protocol            = ! empty( $saml_configuration ) ? 'saml' : ( ! empty( $oauth_configuration ) ? 'oauth' : '' );

		if ( '' === $protocol ) {
			$this->bpc_sso_setup_flow();
		} else {
			$this->bpc_sso_admin_dashboard();
			$this->bpc_sso_protocol_view( $protocol );
		}
	}

	private function bpc_sso_setup_flow() {
		show_admin_bar(false);
		?>
		<html>
		<head>
		    <meta charset="utf-8">
		    <!-- load dependencies -->
		</head>
		<body>
		<div class="bpc_sso_admin_front">
			<div class="bpc_sso_admin_front_header">
				<div class="bpc_sso_admin_front_heading_and_img">
					<img class="bpc_sso_admin_front_header_img" src="<?php echo esc_url( bpcwrapper::bpc_sso_get_image_url( '4-squares-10581.svg' ) ); ?>"/>
					<h3>SAML/Oauth Single Sign On</h3>
				</div>
				<div>
					<button type="button">FAQs</button>
				</div>
			</div>
			<div class="bpc_sso_admin_container">
				<div class="bpc_sso_admin_tabs">
					<div class="bpc_sso_joining_message">Hi, Thank you for joining our service.</div>
					<div style="display:flex;align-items:center;">
						<a style="cursor:pointer;text-decoration:none;" href="<?php echo esc_url( admin_url() ); ?>/admin.php?page=bpc_sso&proto=saml"><div class="bpc_sso_admin_tab">SAML</div></a>
						<a style="cursor:pointer;text-decoration:none;" href="<?php echo esc_url( admin_url() ); ?>/admin.php?page=bpc_sso&proto=oauth"><div class="bpc_sso_admin_tab">OAuth</div></a>
					</div>
				</div>
			</div>
		</div>
		</body>
		</html>
		<?php
	}

	private function bpc_sso_admin_dashboard() {
		// nothing for now.
	}

	private function bpc_sso_protocol_view( $protocol ) {
		$object = saml::get_instance();
		if ( 'oauth' === $protocol ) {
			$object = oauth::get_instance();
		} elseif ( 'saml' === $protocol ) {
			$object = saml::get_instance();
		}
		$object->bpc_sso_render_view();
	}


}
