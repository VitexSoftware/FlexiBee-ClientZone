FROM vitexsoftware/shop4flexibee
COPY src/ /usr/share/shop4flexibee
COPY debian/conf/composer.json /usr/share/shop4flexibee/composer.json
