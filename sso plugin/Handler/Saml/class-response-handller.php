<?php

namespace BPCSSO\Handler\saml;

use BPCSSO\Handler\saml\Saml_Data;
use BPCSSO\Exceptions\SamlRequestException;
use BPCSSO\Exceptions\XMLLoadException;
use BPCSSO\Exceptions\MetadataParseException;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecurityDSig;

class Response_Handler extends Saml_Data {

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

    public function validate_saml_response( $raw_response ) {
        try {
            if ( empty( $raw_response ) ) {
                throw new SamlRequestException('Empty SAML Response.');
            }

            $decoded = base64_decode( $raw_response );
            if ( ! $decoded ) {
                throw new SamlRequestException('Failed to base64 decode SAML Response.');
            }

            $doc = new \DOMDocument();
            if ( ! $doc->loadXML( $decoded ) ) {
                throw new XMLLoadException('Failed to parse SAML XML.');
            }

            $xpath = new \DOMXPath( $doc );
            $xpath->registerNamespace("ds", "http://www.w3.org/2000/09/xmldsig#");

            $signatureNode = $xpath->query("//ds:Signature")->item(0);
            if ( ! $signatureNode ) {
                throw new MetadataParseException('Missing Signature element in SAML Response.');
            }

            $sig = new XMLSecurityDSig();
            $sig->locateSignature( $doc );
            $sig->canonicalizeSignedInfo();
            $sig->readSignedInfo( $signatureNode );

            $key = new XMLSecurityKey( XMLSecurityKey::RSA_SHA256, [ 'type' => 'public' ] );
            $key->loadKey( $this->get_x509_cert(), false, true );

            if ( ! $sig->verify( $key ) ) {
                throw new SamlRequestException('SAML Signature validation failed.');
            }

            // Validate Conditions (NotBefore, NotOnOrAfter)
            $assertion = $doc->getElementsByTagName('Assertion')->item(0);
            if ( ! $assertion ) {
                throw new MetadataParseException('Missing Assertion in SAML Response.');
            }

            $conditions = $assertion->getElementsByTagName('Conditions')->item(0);
            if ( $conditions ) {
                $notBefore = strtotime( $conditions->getAttribute('NotBefore') );
                $notOnOrAfter = strtotime( $conditions->getAttribute('NotOnOrAfter') );
                $now = time();

                if ( $now < $notBefore || $now >= $notOnOrAfter ) {
                    throw new SamlRequestException('SAML Assertion time window is invalid.');
                }
            }

            // Optional: Validate Audience, Destination, etc.
            // Extract user info
            $nameIdNode = $assertion->getElementsByTagName('NameID')->item(0);
            $nameId = $nameIdNode ? $nameIdNode->nodeValue : null;

            $attributes = [];
            $attributeNodes = $assertion->getElementsByTagName('Attribute');
            foreach ( $attributeNodes as $attr ) {
                $attrName = $attr->getAttribute('Name');
                $attrValue = $attr->getElementsByTagName('AttributeValue')->item(0)->nodeValue ?? '';
                $attributes[ $attrName ] = $attrValue;
            }

            return [
                'name_id'   => $nameId,
                'attributes'=> $attributes
            ];

        } catch ( \Exception $e ) {
            // Log the error or handle it
            error_log( 'SAML Validation Error: ' . $e->getMessage() );
            throw $e; // Re-throw to allow upstream handling
        }
    }
}
