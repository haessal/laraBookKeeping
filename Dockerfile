FROM php:fpm-alpine
LABEL  maintainer "haessal <haessal@mizutamauki.net>"

# Install the PHP pdo_mysql extention
RUN docker-php-ext-install pdo_mysql

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Copy the code of BookKeeping
COPY ./book-keeping /var/book-keeping
RUN chown -R www-data:www-data /var/book-keeping

# Install the packages
USER www-data
WORKDIR /var/book-keeping
RUN composer install --optimize-autoloader --no-dev