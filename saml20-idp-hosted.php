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
	'privatekey' => 'server.pem',
	'certificate' => 'server.crt',

	// Interactive login (user1/user2) - reverted from autologin-user1 so the
	// login form renders again, needed to capture fresh RelayState/AuthnRequest
	// material for further SAML SP testing.
	'auth' => 'example-userpass',

	// Force SHA-256 signatures instead of the deprecated SHA-1 default.
	'signature.algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',

	// Explicitly require the assertion itself to be signed (not just the response envelope).
	'saml20.sign.assertion' => true,
);
