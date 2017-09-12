#!/usr/bin/env bash
wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key|sudo apt-key add -
echo deb http://v.s.cz/ stable main > /etc/apt/sources.list.d/vitexsoftware.list

export DEBIAN_FRONTEND="noninteractive"
apt-get update
apt-get install -y php php-curl php-pear php-intl composer unzip php-zip devscripts php-xdebug php-mbstring mc screen aptitude postgresql php-pgsql
sed -i '/^error_reporting/c\error_reporting = E_ALL' /etc/php/7.0/apache2/php.ini
sed -i '/^display_errors/c\display_errors = On' /etc/php/7.0/apache2/php.ini
sed -i '/^display_startup_errors/c\display_startup_errors = On' /etc/php/7.0/apache2/php.ini

sudo -u postgres bash -c "psql -c \"CREATE USER shop4flexibee WITH PASSWORD 'shop4flexibee';\""
sudo -u postgres bash -c "psql -c \"create database shop4flexibee with owner shop4flexibee encoding='utf8' template template0;\""

cd /vagrant
#composer update
cd debian
./deb-package.sh
mkdir -p /vagrant/deb
mv /*.deb /vagrant/deb
cd /vagrant/deb
dpkg-scanpackages . /dev/null | gzip -9c > Packages.gz
echo "deb file:/vagrant/deb ./" > /etc/apt/sources.list.d/local.list
apt-get update
export DEBCONF_DEBUG="developer"
apt-get -y --allow-unauthenticated install shop4flexibee
a2dissite 000-default
a2enmod rewrite
systemctl restart apache2
apt search shop4flexibee
