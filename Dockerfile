FROM debian:latest

MAINTAINER Vítězslav Dvořák <info@vitexsoftware.cz>

RUN apt update;  apt-get install -my wget gnupg dpkg-dev debconf unzip ssmtp libapache2-mod-php

ADD clientzone_*_all.deb /repo/clientzone_all.deb
#RUN gdebi -n /tmp/clientzone_all.deb

RUN cd /repo ; dpkg-scanpackages . /dev/null | gzip -9c > Packages.gz 
RUN echo "deb [trusted=yes] file:///repo/ ./" > /etc/apt/sources.list.d/local.list

RUN wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key | apt-key add -
RUN echo deb http://v.s.cz/ stable main | tee /etc/apt/sources.list.d/vitexsoftware.list

RUN apt update; DEBIAN_FRONTEND="noninteractive" apt-get -y --allow-unauthenticated install clientzone
#    DEBCONF_DEBUG="developer" 

RUN chown www-data:www-data /etc/flexibee/clientzone.json /etc/flexibee/client.json
RUN composer require pear/mail -d /usr/share/clientzone/
RUN composer require pear/mail_mime -d /usr/share/clientzone/
RUN rm /var/www/html/index.html
ADD Docker/index.php /var/www/html/index.php
COPY Docker/mail.ini   /etc/php/7.0/conf.d/mail.ini
COPY Docker/ssmtp.conf /etc/ssmtp/ssmtp.conf

WORKDIR /usr/share/clientzone

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
EXPOSE 80
CMD ["/usr/sbin/apachectl","-DFOREGROUND"]
