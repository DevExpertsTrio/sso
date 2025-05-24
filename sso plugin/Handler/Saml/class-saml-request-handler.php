<?php

namespace BPCSSO\Handler\saml;

use BPCSSO\Handler\saml\Saml_Data;
use BPCSSO\Exceptions\SamlRequestException;

class Saml_Request_Handler extends Saml_Data {

    private static $instance;

    public function __construct( $idp_id ) {
        parent::__construct( $idp_id );
    }

    public static function get_instance( $idp_id ) {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self( $idp_id );
        }
        return self::$instance;
    }

    public function get_authn_request() {
        try {
            $saml_login_url   = $this->get_saml_login_url();
            $acs_url          = $this->get_acs_url();
            $sp_issuer        = $this->get_sp_issuer();
            $nameid_format    = $this->get_nameid_format();

            $issue_instant = gmdate('Y-m-d\TH:i:s\Z');
            $request_id = '_' . bin2hex(random_bytes(16));

            $saml_request = '<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
                ID="' . $request_id . '"
                Version="2.0"
                IssueInstant="' . $issue_instant . '"
                Destination="' . esc_url( $saml_login_url ) . '"
                AssertionConsumerServiceURL="' . esc_url( $acs_url ) . '">
                
                <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">'
                    . esc_html( $sp_issuer ) .
                '</saml:Issuer>
            
                <samlp:NameIDPolicy Format="' . esc_attr( $nameid_format ) . '" AllowCreate="true" />
            </samlp:AuthnRequest>';

            $deflated_request = gzdeflate($saml_request);
            $base64_request = base64_encode($deflated_request);
            $encoded_request = urlencode($base64_request);

            return $encoded_request;

        } catch (\Exception $e) {
            throw new SamlRequestException("Failed to generate AuthnRequest: " . $e->getMessage());
        }
    }
}
