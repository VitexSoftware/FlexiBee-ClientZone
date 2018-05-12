#!/bin/bash
VERSION=`cat debian/lastversion`
REVISION=`cat debian/revision`
VERSTR="${VERSION}.${REVISION}"
sed -i "/\"version\":/c \"version\": \"${VERSTR}\"," debian/clientzone/usr/share/clientzone/composer.json
