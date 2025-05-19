<?php

namespace BPCSSO\Handler\saml;

use BPCSSO\Handler\saml\Saml_Data;

class Saml_Request_Handler extends Saml_Data {

    private static $instance;

    public function __construct( $idp_id ) {
        parent::__construct( $idp_id );
    }

    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self( $idp_id );
        }
        return self::$instance;
    }

    public function get_authn_request() {

        $saml_request = '<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol"
            ID="_abc123"
            Version="2.0"
            IssueInstant="2025-02-08T12:00:00Z"
            Destination="' . $this->saml_login_url . '"
            AssertionConsumerServiceURL="' . $this->acs_url . '">
            
            <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">
                ' . $this->sp_issuer . '
            </saml:Issuer>
        
            <samlp:NameIDPolicy Format="' . $this->nameid_format . '" AllowCreate="true" />
        </samlp:AuthnRequest>'; //Issue instant and ID need to be generated dynamically

        $deflated_request = gzdeflate($saml_request);
    
        $base64_request = base64_encode($deflated_request);
        
        $encoded_request = urlencode($base64_request);

        return $encoded_request;
    }

}