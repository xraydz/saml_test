FROM kristophjunge/test-saml-idp:latest
COPY saml20-idp-hosted.php /var/www/simplesamlphp/metadata/saml20-idp-hosted.php
COPY authsources.php /var/www/simplesamlphp/config/authsources.php
COPY StaticUser.php /var/www/simplesamlphp/modules/autologin/lib/Auth/Source/StaticUser.php
RUN touch /var/www/simplesamlphp/modules/autologin/enable
COPY attacker-key.pem /var/www/simplesamlphp/cert/attacker-key.pem
COPY attacker-cert.crt /var/www/simplesamlphp/cert/attacker-cert.crt
RUN chmod 644 /var/www/simplesamlphp/cert/attacker-key.pem
