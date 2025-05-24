<?php

namespace BPCSSO\Handler;

class DbHandler {

    private static $instance;

    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Save SAML IdP metadata to the database
     *
     * @param int $sp_id
     * @param int $idp_id
     * @param array $metadata Parsed metadata from XML
     * @param string $metadata_url Original metadata URL
     *
     * @return void
     */
    public function save_idp_metadata($sp_id, $idp_id, $metadata, $metadata_url = NULL) {
        global $wpdb;

        $table = $wpdb->prefix . 'bpc_metadata';

        $data = [
            'sp_id'          => $sp_id,
            'idp_id'         => $idp_id,
            'entity_id'      => $metadata['entityID'] ?? '',
            'saml_login_url' => $metadata['SingleSignOnService'][0]['Location'] ?? '',
            'name_id'        => $metadata['NameIDFormat'] ?? 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
            'binding_url'    => $metadata['SingleSignOnService'][0]['Binding'] ?? '',
            'metadata_url'   => $metadata_url,
            'created_at'     => current_time('mysql'),
        ];

        $format = ['%d','%d','%s','%s','%s','%s','%s','%s'];

        $wpdb->insert($table, $data, $format);
    }
}
