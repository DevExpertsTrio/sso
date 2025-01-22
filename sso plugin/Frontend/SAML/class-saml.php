<?php

namespace BPCSSO\Frontend\SAML;

class saml {
	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function bpc_sso_render_view(){?>
		<div class="container">
			<h1>WordPress SSO Plugin</h1>
			<div class="steps">
				<div class="step completed">1</div>
				<div class="step current">2</div>
				<div class="step incomplete">3</div>
			</div>
			<form>
				<label for="idp-name">Identity Provider Name:</label>
				<input type="text" id="idp-name" name="idp-name" required>
				
				<label for="metadata-upload">To fetch data directly from .xml file or metadata URL:</label>
				<input type="file" id="metadata-upload" name="metadata-upload">
				<span>OR</span>
				<input type="text" id="metadata-url" name="metadata-url" placeholder="Metadata URL">
				
				<input type="submit" value="Submit">
			</form>
			<a href="#" class="manual-config">skip to manual configuration >></a>
		</div>
		<div class="form-container">
				<h1>SAML Configuration</h1>
				<form>
					<div class="form-group">
						<label for="name">Name:</label>
						<input type="text" id="name" name="name" required>
					</div>
					<div class="form-group">
						<label for="entity-id">Entity ID:</label>
						<input type="text" id="entity-id" name="entity-id" required>
					</div>
					<div class="form-group">
						<label for="saml-login-url">SAML Login URL:</label>
						<input type="url" id="saml-login-url" name="saml-login-url" required>
					</div>
					<div class="form-group">
						<label for="x509-certificate">X.509 Certificate:</label>
						<textarea id="x509-certificate" name="x509-certificate" rows="4" required></textarea>
					</div>
					<div class="form-group">
						<button type="submit">Submit</button>
					</div>
					<div class="form-group">
						<button onclick="show_test_connection();">Test Connection</button>
					</div>
				</form>
			</div>
			<script>
				function show_test_connection() {
					var myWindow = window.open("<?php echo esc_url_raw(admin_url('?option=bpcsso_test_saml')); ?>", "TEST Connection", "scrollbars=1 width=800, height=600");
				}
			</script>
		<?php

	}

}


