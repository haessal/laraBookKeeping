FROM php:8.0-apache
LABEL  maintainer "haessal <haessal@mizutamauki.net>"

# Enable apache module 'rewrite'
RUN a2enmod rewrite

# Install 'unzip' command. (It is used by Composer)
ENV DEBCONF_NOWARNINGS yes
RUN apt-get update && apt-get upgrade -y && apt-get install -y --no-install-recommends \
            unzip \
        && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Node.js
RUN apt-get update && apt-get upgrade -y && apt-get install -y --no-install-recommends \
            nodejs \
            npm \
        && npm install -g n && n stable \
        && apt-get purge -y nodejs npm \
        && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
RUN npm install -g npm-check-updates

# Install the PHP pdo_mysql extention
RUN docker-php-ext-install pdo_mysql

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Copy the code of BookKeeping
COPY ./book-keeping /var/book-keeping

# Install the packages, build css assets and modify files related to apache as follows
#  - Change owner of files that are loaded to apache
#  - Link from apache Document Root
RUN cd /var/book-keeping \
    && composer install --optimize-autoloader --no-dev \
    && npm ci && npm run build \
    && chown -R www-data:www-data /var/book-keeping \
    && rm -R ../www/html && ln -s /var/book-keeping/public ../www/html

# Set up directory for logging
RUN rm -R /var/log/apache2 \
    && mkdir -p /var/log/bookkeeping/laravel \
    && cd /var/book-keeping \
    && rm -R storage/logs && ln -s /var/log/bookkeeping/laravel storage/logs
ENV APACHE_LOG_DIR /var/log/bookkeeping/apache2
