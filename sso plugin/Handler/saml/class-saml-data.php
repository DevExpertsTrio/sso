<?php

namespace BPCSSO\Handler\saml;

class Saml_Data {

    protected $sp_issuer;

    protected $acs_url;

    protected $idp_issuer;

    protected $saml_login_url;

    protected $x509_cert;

    protected $sign_cert;

    protected $enc_cert;

    protected $nameid_format;

    protected $binding_type;

    public function __construct( $idp_id ) {
        $this->load_saml_data( $idp_id );
    }

    public function load_saml_data( $idp_id ) {
        //Function to fetch the values from database and assign to vars.
    }

}