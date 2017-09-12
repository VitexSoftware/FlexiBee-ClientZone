#!/bin/bash
VERSION=`cat debian/lastversion`
REVISION=`cat debian/revision`
VERSTR="${VERSION}.${REVISION}"
sed -i "/\"version\":/c \"version\": \"${VERSTR}\"," debian/shop4flexibee/usr/share/shop4flexibee/composer.json
