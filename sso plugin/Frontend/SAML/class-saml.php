<?php

namespace BPCSSO\Frontend\SAML;

use BPCSSO\Helper\bpcwrapper;

class saml
{
	private static $instance;

	public static function get_instance()
	{
		if (! isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function bpc_sso_render_view($step)
	{
		switch ($step) {
			case '2':
				$this->bpc_saml_sso_second_step();
				break;
			case '3':
				$this->bpc_saml_sso_third_step();
				break;
		}
	}
	
	private function bpc_saml_sso_second_step() {
	?>
		<h2>Configure Idp Metadata</h1>
			<form action="" method="post" class="form">
				<input hidden name="option" value="bpc_sso_get_idp_metadata" />
				<input hidden name="tab" value="saml" />
				<?php echo wp_nonce_field( 'bpc_sso_get_idp_metadata' ); ?>

				<label for="idp-name">Identity Provider Name:</label>
				<input type="text" id="idp-name" name="idp-name" placeholder="IDP Name" required>
				<span class="desc">Enter the name of your IDP -- such as Azure, Google, Okta etc.</span>

				<label for="metadata-upload">To fetch data directly from .xml file or metadata URL:</label>
				<input type="file" id="metadata-upload" name="metadata-upload">
				<span class="desc">Metadata file (metadata.xml) of your IDP.</span>
				<span class="span-or">OR</span>
				<div></div>
				<input type="text" id="metadata-url" name="metadata-url" placeholder="Metadata URL">
				<span class="desc">URL of the IDP metadata.</span>

				<input type="submit" value="Submit">
			</form>
		<?php
	}


	private function bpc_saml_sso_third_step()
	{
		?>
		<h2>SAML Configuration</h2>
		<form action="" method="post" class="form">
			<input hidden name="option" value="bpc_sso_save_idp_metadata" />
			<input hidden name="tab" value="saml" />
			<?php echo wp_nonce_field( 'bpc_sso_save_idp_metadata' ); ?>

				<label for="name">Name:</label>
				<input type="text" id="name" name="name" placeholder="IDP Name (Azure, Okta, Google)" required>
				<span class="desc">Enter the name of your IDP -- such as Azure, Google, Okta etc.</span>

				<label for="entity-id">Entity ID:</label>
				<input type="text" id="entity-id" name="entity-id" placeholder="Entity ID" required>
				<span class="desc">Issuer ID, you can get this from IDP metadata.</span>

				<label for="saml-login-url">SAML Login URL:</label>
				<input type="url" id="saml-login-url" name="saml-login-url" placeholder="ACS URL" required>

				<label for="x509-certificate">X.509 Certificate:</label>
				<textarea id="x509-certificate" name="x509-certificate" rows="4" placeholder="=== X509 CERTIFICATE ===" required></textarea>

				<span></span>
				<span>
					<input type="submit" value="Submit">
					<input type="button" onclick="show_test_connection();" value="Test Connection">
				</span>
		</form>
		<script>
			function show_test_connection() {
				var myWindow = window.open("<?php echo esc_url_raw(admin_url('?option=bpcsso_test_saml')); ?>", "TEST Connection", "scrollbars=1 width=800, height=600");
			}
		</script>
	<?php
	}
}
