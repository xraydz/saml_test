<?php
/**
 * SAML 2.0 IdP configuration for SimpleSAMLphp.
 *
 * Patched from the kristophjunge/test-saml-idp default to explicitly:
 *  - sign the <Assertion> (not just the enclosing <Response>)
 *  - use SHA-256 for signatures instead of the deprecated SHA-1 default
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-hosted
 */

$metadata['__DYNAMIC:1__'] = array(
	'host' => '__DEFAULT__',

	// X.509 key and certificate. Relative to the cert directory.
	// Using a self-generated, UNREGISTERED cert (not the one Aiven's IdP
	// config has pinned) to test whether Aiven's ACS trusts whatever cert
	// is embedded in the response's own <ds:KeyInfo> instead of validating
	// against its pinned certificate (certificate-swap / KeyInfo trust test).
	'privatekey' => 'attacker-key.pem',
	'certificate' => 'attacker-cert.crt',

	// Interactive login (user1/user2) - reverted from autologin-user1 so the
	// login form renders again, needed to capture fresh RelayState/AuthnRequest
	// material for further SAML SP testing.
	'auth' => 'example-userpass',

	// Force SHA-256 signatures instead of the deprecated SHA-1 default.
	'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

	// Explicitly require the assertion itself to be signed (not just the response envelope).
	'saml20.sign.assertion' => true,
);
