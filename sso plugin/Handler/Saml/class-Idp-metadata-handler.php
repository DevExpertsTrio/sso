<?php

namespace BPCSSO\Handler\saml;

use DOMDocument;
use DOMXPath;
use Exception;
use BPCSSO\Exceptions\MetadataParseException;
use BPCSSO\Exceptions\XMLLoadException;
use BPCSSO\Exceptions\InvalidSAMLURLException;

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

		try {
			if ( filter_var( $metadata_source, FILTER_VALIDATE_URL ) ) {
				$xml_content = @file_get_contents( $metadata_source );
				if ( $xml_content === false ) {
					throw new InvalidSAMLURLException( "Failed to retrieve metadata from URL: $metadata_source" );
				}
				if ( ! $dom->loadXML( $xml_content ) ) {
					throw new XMLLoadException( "Failed to parse XML content from metadata URL." );
				}
			} else {
				if ( ! file_exists( $metadata_source ) ) {
					throw new XMLLoadException( "Metadata file does not exist: $metadata_source" );
				}
				if ( ! $dom->load( $metadata_source ) ) {
					throw new XMLLoadException( "Failed to load or parse XML from file: $metadata_source" );
				}
			}
		} catch ( Exception $e ) {
			error_log( "XML load error: " . $e->getMessage() );
			throw $e;
		}

		return $dom;
	}

	public function parse_metadata_xml( $source ) {
		try {
			$metadata_obj = $this->load_xml( $source );

			$xpath = new DOMXPath( $metadata_obj );

			$namespaces = array(
				'md' => 'urn:oasis:names:tc:SAML:2.0:metadata',
				'ds' => 'http://www.w3.org/2000/09/xmldsig#',
			);

			foreach ( $namespaces as $key => $value ) {
				$xpath->registerNamespace( $key, $value );
			}

			$metadata = array();

			$metadata['entityID'] = $metadata_obj->documentElement->getAttribute( 'entityID' );

			$metadata['SingleSignOnService'] = array();
			$sso_nodes = $xpath->query( '//md:IDPSSODescriptor/md:SingleSignOnService' );
			if ( ! $sso_nodes ) {
				throw new MetadataParseException( "No SingleSignOnService elements found in metadata." );
			}
			foreach ( $sso_nodes as $node ) {
				$metadata['SingleSignOnService'][] = array(
					'Binding'  => $node->getAttribute( 'Binding' ),
					'Location' => $node->getAttribute( 'Location' )
				);
			}

			$metadata['SingleLogoutService'] = [];
			$slo_nodes = $xpath->query( '//md:IDPSSODescriptor/md:SingleLogoutService' );
			foreach ( $slo_nodes as $node ) {
				$metadata['SingleLogoutService'][] = array(
					'Binding'  => $node->getAttribute( 'Binding' ),
					'Location' => $node->getAttribute( 'Location' )
				);
			}

			$metadata['X509Certificates'] = [];
			$cert_nodes = $xpath->query( '//md:IDPSSODescriptor/md:KeyDescriptor/ds:KeyInfo/ds:X509Data/ds:X509Certificate' );
			foreach ( $cert_nodes as $node ) {
				$metadata['X509Certificates'][] = trim( $node->textContent );
			}

			error_log( 'Parsed metadata: ' . print_r( $metadata, true ) );

			return $metadata;

		} catch ( Exception $e ) {
			error_log( "Metadata parsing error: " . $e->getMessage() );
			throw $e;
		}
	}
}
