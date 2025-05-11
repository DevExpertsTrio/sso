<?php

namespace BPCSSO\includes\saml;

use DOMDocument;
use BPCSSO\Handler\saml\IDP_Metadata_Handler;
use BPCSSO\Handler\saml\SP_Metadata_Handler;

class samlsso {

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function bpc_sso_test_saml() {
		$option = ! empty( $_GET['option'] ) ? sanitize_text_field( wp_unslash( $_GET['option'] ) ) : '';

		$metadata_file = plugin_dir_url( __FILE__ ) . '../../mo-idp-metadata.xml';
		$idp_metadata = IDP_Metadata_Handler::get_instance();
		$idp_met_arr = $idp_metadata->parse_metadata_xml( $metadata_file );

		$sp_met_obj = SP_Metadata_Handler::get_instance();
		$sp_met_obj->set_sp_entity_id('hello there');
		$sp_met_obj->set_acs_url('https://google.com');
		$sp_metadata = $sp_met_obj->get_sp_metadata();

		error_log('SP metadata => '.print_r($sp_metadata, true));

		// if ( ob_get_contents() ) {
		// 	ob_clean();
		// }

		// header( 'Content-Type: application/xml' );
		// if ( $download ) {
			// header( 'Content-Disposition: attachment; filename="Metadata.xml"' );
		// }

		// echo $sp_metadata;
		// exit;

		switch ( $option ) {
			case 'bpcsso_test_saml':
				$this->bpc_sso_send_test_request();
				break;
			case 'bpc_sso_saml_metadata':
				$this->bpc_sso_show_saml_metadata();
				break;
			case 'idp_metadata':
				$this->bpc_sso_read_idp();
				break;
		}
	}

	private function bpc_sso_send_test_request() {
		$request     = CreateRequest::generate_request();
		$redirecturl = 'https://dev-20398574.okta.com/app/dev-20398574_sso_1/exkcgoy0yfQHCG8Ay5d7/sso/saml?SAMLRequest=' . $request;
		header( 'Location: ' . $redirecturl );
		exit;
	}

	private function bpc_sso_show_saml_metadata() {
		$metadata = metadata::bpc_sso_get_metadata();
		ob_clean();
		header( 'Content-Type: text/xml' );
		header( 'Content-Disposition: attachment; filename="mo-saml-sp-metadata.xml"' );
		echo $metadata;
		exit;
	}

	private function bpc_sso_read_idp() {

		$idp_metadata = <<<XML
        <md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" entityID="http://www.okta.com/exkcgsral6QPUUDQb5d7">
            <md:IDPSSODescriptor WantAuthnRequestsSigned="false" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
                <md:KeyDescriptor use="signing">
                    <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
                        <ds:X509Data>
                            <ds:X509Certificate>MIIDqDCCApCgAwIBAgIGAYsVxL0sMA0GCSqGSIb3DQEBCwUAMIGUMQswCQYDVQQGEwJVUzETMBEG A1UECAwKQ2FsaWZvcm5pYTEWMBQGA1UEBwwNU2FuIEZyYW5jaXNjbzENMAsGA1UECgwET2t0YTEU MBIGA1UECwwLU1NPUHJvdmlkZXIxFTATBgNVBAMMDGRldi0yMDM5ODU3NDEcMBoGCSqGSIb3DQEJ ARYNaW5mb0Bva3RhLmNvbTAeFw0yMzEwMDkxODQ3MTZaFw0zMzEwMDkxODQ4MTZaMIGUMQswCQYD VQQGEwJVUzETMBEGA1UECAwKQ2FsaWZvcm5pYTEWMBQGA1UEBwwNU2FuIEZyYW5jaXNjbzENMAsG A1UECgwET2t0YTEUMBIGA1UECwwLU1NPUHJvdmlkZXIxFTATBgNVBAMMDGRldi0yMDM5ODU3NDEc MBoGCSqGSIb3DQEJARYNaW5mb0Bva3RhLmNvbTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoC ggEBALT76ZO7VAVaWuZ1kPZe8drLPG4qc1mdf+Wxbhf67/XPCnGTEoZwLj5C//X93PwLlXNYILvD xMAtbm/9p4+y0zPb8ztQHeqs9DW388q+SyiPtxiZoV/uUDJggJxa8vk2HPahE8Mt2N4dGpNadYmg EcUD9auJC+6iP8NjyIphJzkxEzyt+b0rhLhUb5smDrSyt6hLcbFyW0Tq8Vc+UIcjShi9JsXvr9io eXT7Fi0ZYEnD9P9/AV97PMuOY1e9nzmR1FHQ7xm9dchyeLIbbpcT/MKi9BDrY5oEQjWULjWbWtwj vDbXcmuhnem/UMzvcou4oIlEsPPfaFfVLh1hvptt0ucCAwEAATANBgkqhkiG9w0BAQsFAAOCAQEA IuQJWDvBgkyDA1V5wOKPzMVF38g220MOISPTE1FalyLfXX439JP7a/bNb2LOQQsImAjersgGCRvf XluYM0YkmlS7hdtJfgj7zwpOufn5MPsYVXtXnbEFPmlQvOxqYlPMKOBC09beEaFfzXjW7AvCHn0z CfGAW+KBXuNvJXpdJbf6ph0I80xeg0uyXQBh3KPOswhl0hTLuxBxavdCjWdpCNY0mG7ZSaIobGku TOpZYyJwZ1CYfilCy3v8YcB+T4bxl6VOtEhH2hl5LvzUhcbSIPer7FxogZg8YNyON5s3gsD/ARMr D3EDRm3xqyHqHKsqdL8IH8OxWP9htLkmlETrPA==</ds:X509Certificate>
                        </ds:X509Data>
                    </ds:KeyInfo>
                </md:KeyDescriptor>
                <md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified</md:NameIDFormat>
                <md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress</md:NameIDFormat>
                <md:SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="https://dev-20398574.okta.com/app/dev-20398574_newsso_1/exkcgsral6QPUUDQb5d7/sso/saml"/>
                <md:SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect" Location="https://dev-20398574.okta.com/app/dev-20398574_newsso_1/exkcgsral6QPUUDQb5d7/sso/saml"/>
            </md:IDPSSODescriptor>
        </md:EntityDescriptor>
        XML;

		$dom_document = new DOMDocument();

		// Load the XML string.
		$dom_document->loadXML( $idp_metadata );

		// Access the root node as a DOMNode
		// $rootNode = $domDocument->documentElement.

		new IdpMetadata( $dom_document );
	}
}
