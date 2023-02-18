FROM php:7.3-apache

LABEL maintainer="haessal@mizutamauki.net"

RUN a2enmod rewrite

RUN docker-php-ext-install pdo_mysql

ENV LARAVEL_PROJECT project

RUN sed -ri -e 's!/var/www/html!/var/devcontainer/${LARAVEL_PROJECT}/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/devcontainer/${LARAVEL_PROJECT}/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

ENV WWW_DATA_UID 33
ENV WWW_DATA_GID 33

COPY devcontainer-web-start /usr/local/bin/

CMD ["devcontainer-web-start"]
