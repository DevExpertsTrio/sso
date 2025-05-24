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
        // Function to fetch the values from database.
    }

    // ---- GETTERS ----

    public function get_sp_issuer() {
        return $this->sp_issuer;
    }

    public function get_acs_url() {
        return $this->acs_url;
    }

    public function get_idp_issuer() {
        return $this->idp_issuer;
    }

    public function get_saml_login_url() {
        return $this->saml_login_url;
    }

    public function get_x509_cert() {
        return $this->x509_cert;
    }

    public function get_sign_cert() {
        return $this->sign_cert;
    }

    public function get_enc_cert() {
        return $this->enc_cert;
    }

    public function get_nameid_format() {
        return $this->nameid_format;
    }

    public function get_binding_type() {
        return $this->binding_type;
    }

    // ---- SETTERS ----

    public function set_sp_issuer( $value ) {
        $this->sp_issuer = $value;
    }

    public function set_acs_url( $value ) {
        $this->acs_url = $value;
    }

    public function set_idp_issuer( $value ) {
        $this->idp_issuer = $value;
    }

    public function set_saml_login_url( $value ) {
        $this->saml_login_url = $value;
    }

    public function set_x509_cert( $value ) {
        $this->x509_cert = $value;
    }

    public function set_sign_cert( $value ) {
        $this->sign_cert = $value;
    }

    public function set_enc_cert( $value ) {
        $this->enc_cert = $value;
    }

    public function set_nameid_format( $value ) {
        $this->nameid_format = $value;
    }

    public function set_binding_type( $value ) {
        $this->binding_type = $value;
    }
}
