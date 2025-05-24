<?php

namespace BPCSSO\Handler\saml;


use BPCSSO\Exceptions\SPMetadataGenerationException;

class SP_Metadata_Handler extends Saml_Data {

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

    public function generate_metadata() {
        try {
            $sp_issuer = $this->get_sp_issuer();
            $acs_url = $this->get_acs_url();
            $nameid_format = $this->get_nameid_format() ?: 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified';

            if ( empty( $sp_issuer ) || empty( $acs_url ) ) {
                throw new SPMetadataGenerationException(
                    'Missing required SP data: SP Issuer or ACS URL is not set.'
                );
            }

            $sp_metadata = '<?xml version="1.0"?>' . "\n" .
                '<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" ' .
                'validUntil="2026-07-22T10:07:10Z" cacheDuration="PT1446808792S" entityID="' . esc_attr( $sp_issuer ) . '">' . "\n" .
                '    <md:SPSSODescriptor AuthnRequestsSigned="false" WantAssertionsSigned="true" protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">' . "\n" .
                '        <md:NameIDFormat>' . esc_html( $nameid_format ) . '</md:NameIDFormat>' . "\n" .
                '        <md:AssertionConsumerService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" Location="' . esc_url( $acs_url ) . '" index="1"/>' . "\n" .
                '    </md:SPSSODescriptor>' . "\n" .
                '</md:EntityDescriptor>';

            return $sp_metadata;

        } catch ( SPMetadataGenerationException $e ) {
            error_log( '[SP Metadata Error] ' . $e->getMessage() );
            throw $e;
        } catch ( \Exception $e ) {
            error_log( '[SP Metadata Fatal Error] ' . $e->getMessage() );
            throw new SPMetadataGenerationException( 'Failed to generate SP metadata.', 0, $e );
        }
    }
}
