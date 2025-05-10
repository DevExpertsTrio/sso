<?php
/**
 * Plugin Name: SAML Oauth Single Sign On
 * Version: 1.0.0
 * Author: BPC
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace BPCSSO;

use BPCSSO\Frontend\adminFront;
use BPCSSO\Frontend\AdminSettings;
use BPCSSO\Helper\Dbhandler;
use BPCSSO\includes\saml\samlsso;

define( 'BPC_SSO_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'BPC_SSO_PLUGIN_VERSION', '1.0.1' );

require_once 'class-autoloader.php';

/**
 * iuhuyb.
 */
class Ssop {

	/**
	 *
	 */
	private static $instance;

	// Private constructor to prevent direct instantiation
	private function __construct() {
		$this->bpc_sso_hooks();
	}

	// Static method to get or create the instance
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	// Initialize hooks in a separate method
	private function bpc_sso_hooks() {
		add_action( 'admin_menu', array( $this, 'bpc_sso_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'bpc_sso_settings_style' ) );
		add_action( 'admin_init', array( samlsso::get_instance(), 'bpc_sso_test_saml' ) );
		register_deactivation_hook( __FILE__, array( $this, 'bpc_sso_deactivate' ) );
		register_uninstall_hook( __FILE__, array( __CLASS__, 'bpc_sso_uninstall' ) ); // Used __CLASS__ to reference the class.
		add_action( 'admin_init', array( AdminSettings::get_instance(), 'bpc_sso__save_settings' ) );
	}

	public function bpc_sso_admin_menu() {
		$page = add_menu_page(
			'bpc sso' . __( '+ login' ),
			'bpc sso login',
			'administrator',
			'bpc_sso',
			array( adminFront::get_instance(), 'bpc_sso_protocol_main' ),
			''
		);

		Dbhandler::bpc_sso_create_saml_metadata_table();
	}

	function bpc_sso_settings_style( $page ) {
		if ( 'toplevel_page_bpc_sso' !== $page ) {
			return;
		}
		$css_url = plugins_url( 'assets/css/sso-plugin.css', __FILE__ );

		wp_enqueue_style( 'bpc_sso_css', $css_url, array(), BPC_SSO_PLUGIN_VERSION );
	}
}
$sso_login = Ssop::get_instance();
