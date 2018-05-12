FROM vitexsoftware/clientzone
COPY src/ /usr/share/clientzone
COPY debian/conf/composer.json /usr/share/clientzone/composer.json
