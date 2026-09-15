#!/bin/bash
# IdP-initiated SSO: authenticate at our own test IdP as user2, capture the raw auto-submitted SAMLResponse form.
set -e
IDP_HOST="https://01a0a557-d9ad-7a99-8940-65a809658605-8080.eur-1.aiven.app"
SP_ENTITY_ID="https://api.aiven.io/v1/sso/saml/account/a5df15817318/method/am5df219d2f36/metadata"
COOKIEJAR=$(mktemp)

# Step 1: hit IdP-initiated SSO endpoint, follow to login form
INIT_URL="${IDP_HOST}/simplesaml/saml2/idp/SSOService.php?spentityid=$(python3 -c "import urllib.parse; print(urllib.parse.quote('$SP_ENTITY_ID', safe=''))")"
echo "=== Step 1: GET $INIT_URL ==="
curl -s -c "$COOKIEJAR" -b "$COOKIEJAR" -L "$INIT_URL" -o /tmp/idp_login_form.html
grep -o 'action="[^"]*"' /tmp/idp_login_form.html | head -5
grep -o 'name="[^"]*"' /tmp/idp_login_form.html | head -20
echo "COOKIEJAR=$COOKIEJAR"
