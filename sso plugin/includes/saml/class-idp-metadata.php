<?php

namespace BPCSSO\includes\saml;

use DOMNode;
use DOMXPath;
use DOMDocument;

class IdpMetadata {
	private $idp;

	public function __construct( DOMNode $xmld = null ) {
		$entities_desc_query = './saml_metadata:EntitiesDescriptor';
		$entity_desc_query   = './saml_metadata:EntityDescriptor';
		$idp_desc_query      = './saml_metadata:IDPSSODescriptor';

		$entity_desc = $this->bpc_sso_get_entity( $xmld, $entity_desc_query );
	}

	/**
	 * Execute an XPath query on a DOMNode and return the matching nodes.
	 *
	 * @param DOMNode $node The node to query.
	 * @param string  $query The XPath query.
	 *
	 * @return DOMNode[] An array of DOMNodes matching the query.
	 */
	private function bpc_sso_get_entity( DOMNode $node, $query ) {
		// Create a cached DOMXPath object if it doesn't exist or if the document has changed.
		static $xp_cache = null;

        // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- ownerDocument is the property of DOMNode so can't change the name.
		$doc = $node instanceof DOMDocument ? $node : $node->ownerDocument;

		if ( null === $xp_cache || ! $xp_cache->document->isSameNode( $doc ) ) {
			$xp_cache = new DOMXPath( $doc );
			// Register commonly used XML namespaces.
			$namespaces = array(
				'soap-env'       => 'http://schemas.xmlsoap.org/soap/envelope/',
				'saml_protocol'  => 'urn:oasis:names:tc:SAML:2.0:protocol',
				'saml_assertion' => 'urn:oasis:names:tc:SAML:2.0:assertion',
				'saml_metadata'  => 'urn:oasis:names:tc:SAML:2.0:metadata',
				'ds'             => 'http://www.w3.org/2000/09/xmldsig#',
				'xenc'           => 'http://www.w3.org/2001/04/xmlenc#',
			);
			foreach ( $namespaces as $prefix => $uri ) {
				$xp_cache->registerNamespace( $prefix, $uri );
			}
		}

		// Execute the XPath query and store the results in an array.
		$results = $xp_cache->query( $query, $node );
		$matching_nodes = array();
		foreach ( $results as $result ) {
			$matching_nodes[] = $result;
		}

		return $matching_nodes;
	}
}
