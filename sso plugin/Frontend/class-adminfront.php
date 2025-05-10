<?php

namespace BPCSSO\Frontend;

use BPCSSO\Frontend\SAML\saml;
use BPCSSO\Frontend\Oauth\oauth;
use BPCSSO\Frontend\SAML\ContactUs;
use BPCSSO\Helper\BpcConstants;
use BPCSSO\Helper\bpcwrapper;

class adminFront {
	private static $instance;

	private static $step;
	private static $protocol;

	// Public method to get or create the instance
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function bpc_sso_protocol_main() {
		$proto               = ! empty( $_GET['proto'] ) ? sanitize_text_field( wp_unslash( $_GET['proto'] ) ) : '';
		self::$protocol      = 'saml' === $proto ? 'saml' : ( 'oauth' === $proto ? 'oauth' : '' );
		self::$step		     = ! empty( $_GET['step'] ) ? sanitize_text_field( wp_unslash( $_GET['step'] ) ) : '1';

		?>
		<div class="bpc_sso_admin_front">
			<?php
				// Render the contact us form
				ContactUs::bpc_sso_render_contact_us_form();
			?>
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
				<?php
					$this->bpc_sso_setup_flow();
				?>
				</div>
			</div>
		</div>
		<?php
	}

	private function bpc_sso_setup_flow() {
		$settings_css_url = BPC_SSO_PLUGIN_URL . '/assets/css/saml-settings.css';
		wp_enqueue_style('bpc_saml_sso_settings_css', $settings_css_url, array(), BPC_SSO_PLUGIN_VERSION);

		$this->bpc_saml_sso_stepped_ui();
		?>
		<?php
	}

	private function bpc_saml_sso_stepped_ui() {
	?>
		<div class="bpc-saml-container">
			<div class="bpc-saml-steps">
				<div class="step <?php echo self::$step > 1 ? 'complete' : 'current'; ?>">1</div>
				<div class="line"></div>
				<div class="step <?php echo self::$step > 2 ? 'complete' : (self::$step < 2 ? 'incomplete' : 'current'); ?>">2</div>
				<div class="line"></div>
				<div class="step <?php echo self::$step > 2 ? 'current' : 'incomplete'; ?>">3</div>
			</div>
			<div class="bpc-saml-step-content">
			<?php 
				if( '1' !== self::$step ) {
					$this->bpc_sso_protocol_view();
				} else {
					$this->bpc_saml_sso_first_step();
				}

			?>
		</div>
		<?php
			if( '2' === self::$step ) {
				$skip_to_manual_config = add_query_arg(
					array(
						'page' => 'bpc_sso',
						'proto' => 'saml',
						'step' => 3,
					),
					admin_url('admin.php')
				);
				?>
				<a href="<?php echo esc_url( $skip_to_manual_config ); ?>" class="skip-to-manual-config">skip to manual configuration >></a>
				<?php
			}
		?>
	<?php
	}

	private function bpc_saml_sso_first_step() {
		?>
		<div class="bpc_sso_first_step_container">
			<div class="bpc_sso_first_step_header">
				<h2>Welcome to SAML/OAuth Single Sign-On</h2>
				<p style="text-align: center; margin-top: 3rem;">To get started, please select a protocol:</p>
			</div>
			<div class="bpc_sso_admin_tabs_box">
				<a href="<?php echo esc_url( admin_url() ); ?>/admin.php?page=bpc_sso&proto=saml&step=2">
					<div class="bpc_sso_admin_tab">
						<img width="50px" height="50px" src="<?php echo esc_url( bpcwrapper::bpc_sso_get_image_url( 'saml.svg' ) ); ?>" alt="SAML" />
						<span>SAML</span>
					</div>
				</a>
				<a style="cursor:pointer;text-decoration:none;" href="<?php echo esc_url( admin_url() ); ?>/admin.php?page=bpc_sso&proto=oauth&step=2">
					<div class="bpc_sso_admin_tab">
						<img width="50px" height="50px" src="<?php echo esc_url( bpcwrapper::bpc_sso_get_image_url( 'oauth.png' ) ); ?>" alt="OAuth" />
						<span>OAuth</span>
					</div>
				</a>
			</div>
		</div>
	</div>
		<?php
	}

	private function bpc_sso_protocol_view() {
		$object = saml::get_instance();
		if ( 'oauth' === self::$protocol ) {
			$object = oauth::get_instance();
		} elseif ( 'saml' === self::$protocol ) {
			$object = saml::get_instance();
		}
		$object->bpc_sso_render_view( self::$step );
	}


}
