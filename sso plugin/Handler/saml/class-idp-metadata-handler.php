<?php

namespace BPCSSO\Handler\saml;

use DOMDocument;
use DOMXPath;

class IDP_Metadata_Handler {

    private static $instance;

    public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    private function load_xml( $metadata_source ) {
        $dom = new DOMDocument();

        if ( filter_var( $metadata_source, FILTER_VALIDATE_URL ) ) {
            $xml_content = file_get_contents( $metadata_source );
            $dom->loadXML( $xml_content );
        } else {
            $dom->load( $metadata_source );
        }

        return $dom;
    }

    public function parse_metadata_xml( $source ) {

        $metadata_obj = $this->load_xml( $source );

        $xpath = new DOMXPath($metadata_obj);

        $namespaces = array(
            'md' => 'urn:oasis:names:tc:SAML:2.0:metadata',
            'ds' => 'http://www.w3.org/2000/09/xmldsig#',
        );

        foreach ( $namespaces as $key => $value ) {
            $xpath->registerNamespace( $key, $value );
        }

        $metadata = array();

        // Extract Entity ID
        $metadata['entityID'] = $metadata_obj->documentElement->getAttribute( 'entityID' );

        // Extract SingleSignOnService URLs
        $metadata['SingleSignOnService'] = array();
        $sso_nodes = $xpath->query( '//md:IDPSSODescriptor/md:SingleSignOnService' );
        foreach ($sso_nodes as $node) {
            $metadata['SingleSignOnService'][] = array(
                'Binding'  => $node->getAttribute( 'Binding' ),
                'Location' => $node->getAttribute( 'Location' )
            );
        }

        // Extract SingleLogoutService URLs
        $metadata['SingleLogoutService'] = [];
        $slo_nodes = $xpath->query( '//md:IDPSSODescriptor/md:SingleLogoutService' );
        foreach ($slo_nodes as $node) {
            $metadata['SingleLogoutService'][] = array(
                'Binding'  => $node->getAttribute( 'Binding' ),
                'Location' => $node->getAttribute( 'Location' )
            );
        }

        // Extract X509 Certificate
        $metadata['X509Certificates'] = [];
        $cert_nodes = $xpath->query( '//md:IDPSSODescriptor/md:KeyDescriptor/ds:KeyInfo/ds:X509Data/ds:X509Certificate' );
        foreach ($cert_nodes as $node) {
            $metadata['X509Certificates'][] = trim( $node->textContent );
        }

        error_log('Formed metadata => '.print_r($metadata, true));

        return $metadata;
    }
}