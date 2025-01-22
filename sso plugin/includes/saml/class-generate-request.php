<?php

namespace BPCSSO\includes\saml;

class CreateRequest {
	public static function generate_request() {
		// Define your SP's Entity ID and ACS URL.
		$sp_entity_id = site_url() . '/bpcsso/protocol/saml/';
		$acs_url      = site_url() . '/';

		// Define the IdP's SSO URL.
		$idp_sso_url   = 'https://dev-20398574.okta.com/app/dev-20398574_sso_1/exkcgoy0yfQHCG8Ay5d7/sso/saml';
		$issue_instant = gmdate( 'Y-m-d\TH:i:s\Z', time() );

		// Generate a unique ID for the SAML request (can be a random string).
		$request_id = '_' . sha1( uniqid( wp_rand(), true ) ); // ps_changed.

		// Create the SAML AuthnRequest XML.
		$authn_request = '<?xml version="1.0" encoding="UTF-8"?>' . <<<XML
        <samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
                            xmlns="urn:oasis:names:tc:SAML:2.0:assertion"
                            ID="$request_id"
                            Version="2.0"
                            IssueInstant="$issue_instant"
                            Destination="$idp_sso_url"
                            ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
                            AssertionConsumerServiceURL="$acs_url">
            <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">$sp_entity_id</saml:Issuer>
        </samlp:AuthnRequest>
        XML;

		// Send the AuthnRequest to the IdP's SSO endpoint (HTTP-Redirect binding).
		$deflated_request      = gzdeflate( $authn_request );
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- we are encoding the authentication request to protect data.
		$encoded_authn_request = base64_encode( $deflated_request );
		$url_encode_request    = rawurlencode( $encoded_authn_request );

		return $url_encode_request;
	}
}

