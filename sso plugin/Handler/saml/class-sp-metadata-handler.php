<?php

namespace BPCSSO\Handler\saml;

use BPCSSO\Handler\saml\Saml_Data;

class SP_Metadata_Handler extends Saml_Data {

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

    public function get_sp_metadata() {

        $sp_metadata = '<?xml version="1.0"?>
                        <md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" validUntil="2026-07-22T10:07:10Z" cacheDuration="PT1446808792S" entityID="' . esc_attr( $this->sp_issuer ) . '">
                            <md:SPSSODescriptor AuthnRequestsSigned="false" WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
                                <md:NameIDFormat>urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified</md:NameIDFormat>
                                <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="' . esc_url( $this->acs_url ) . '" index="1"/>
                            </md:SPSSODescriptor>
                        </md:EntityDescriptor>';

        return $sp_metadata;
    }

}