FROM almalinux:8.10

# Install Remi's RPM repository
RUN dnf install -y epel-release \
        https://rpms.remirepo.net/enterprise/remi-release-8.rpm \
    && dnf clean all

# Install Apache and common tools
RUN dnf install -y \
        git \
        httpd \
    && dnf clean all

# Install PHP 8.5 and extensions required by Laravel
RUN dnf module reset php -y \
    && dnf module enable php:remi-8.5 -y \
    && dnf install -y \
        php \
        php-fpm \
        php-bcmath \
        php-curl \
        php-dom \
        php-fileinfo \
        php-mbstring \
        php-mysqlnd \
        php-opcache \
        php-pdo \
        php-pcntl \
        php-sodium \
        php-xml \
        php-zip \
    && dnf clean all

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Apache configuration
COPY httpd.conf /etc/httpd/conf.d/laravel.conf

# PHP-FPM configuration
COPY php-fpm.conf /etc/php-fpm.d/www.conf

# Create required directories for PHP-FPM
RUN mkdir -p /run/php-fpm

# Install the Book Keeping (dummy)
RUN mkdir -p /var/laraBookKeeping/book-keeping

COPY phpinfo.php /var/laraBookKeeping/book-keeping/public/phpinfo.php

WORKDIR /var/laraBookKeeping/book-keeping

EXPOSE 80

CMD ["sh", "-c", "php-fpm && httpd -D FOREGROUND"]
