<?php
/**
 * SAML-related exception classes for the BPCSSO plugin.
 *
 * @package BPCSSO\Exceptions
 */

namespace BPCSSO\Exceptions;

use Exception;

/**
 * Thrown when the SAML metadata URL is invalid or unreachable.
 */
class InvalidSAMLURLException extends Exception {

    protected $url;

    public function __construct(
        string $url = '',
        string $message = "The SAML metadata URL is invalid or unreachable.",
        int $code = 0,
        Exception $previous = null
    ) {
        $this->url = $url;
        parent::__construct($message, $code, $previous);
        error_log("[InvalidSAMLURLException] {$message} | URL: {$url}");
    }

    public function getUrl(): string {
        return $this->url;
    }
}

/**
 * Thrown when SAML metadata fails to parse correctly.
 */
class MetadataParseException extends Exception {

    protected $context;

    public function __construct(
        string $message = "Failed to parse SAML metadata.",
        array $context = [],
        int $code = 0,
        Exception $previous = null
    ) {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
        error_log("[MetadataParseException] {$message} | Context: " . print_r($context, true));
    }

    public function getContext(): array {
        return $this->context;
    }
}

/**
 * Thrown during failures in generating or sending a SAML AuthnRequest.
 */
class SamlRequestException extends Exception {

    protected $userHint;

    public function __construct(
        string $message = "SAML AuthnRequest generation failed.",
        int $code = 0,
        string $userHint = '',
        Exception $previous = null
    ) {
        $this->userHint = $userHint;
        parent::__construct($message, $code, $previous);
        $this->logError();
    }

    public function getUserHint(): string {
        return $this->userHint;
    }

    protected function logError(): void {
        error_log('[SamlRequestException] ' . $this->getMessage() . ' | Hint: ' . $this->userHint);
    }
}

/**
 * Thrown when SP metadata cannot be generated correctly.
 */
class SPMetadataGenerationException extends Exception {

    public function __construct(
        string $message = "Error generating SP metadata.",
        int $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        error_log("[SPMetadataGenerationException] {$message}");
    }

    public function getHint(): string {
        return 'Ensure SP Issuer and ACS URL are set before generating metadata.';
    }
}

/**
 * Thrown when loading or reading XML fails.
 */
class XMLLoadException extends Exception {

    protected $filePath;

    public function __construct(
        string $filePath = '',
        string $message = "Unable to load XML data.",
        int $code = 0,
        Exception $previous = null
    ) {
        $this->filePath = $filePath;
        parent::__construct($message, $code, $previous);
        error_log("[XMLLoadException] {$message} | File: {$filePath}");
    }

    public function getFilePath(): string {
        return $this->filePath;
    }
}
