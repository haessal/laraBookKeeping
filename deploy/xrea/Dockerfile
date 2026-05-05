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

# Install the Book Keeping
ENV USERNAME=user
ENV USER_UID=1000
ENV USER_GID=1000
RUN groupadd --gid $USER_GID ${USERNAME} \
        && useradd -s /bin/bash --uid ${USER_UID} --gid ${USER_GID} -m ${USERNAME}

RUN mkdir -p /var/laraBookKeeping/

COPY laraBookKeeping-dev.tar.gz /var/laraBookKeeping/laraBookKeeping-dev.tar.gz

RUN cd /var/laraBookKeeping \
    && tar -xzf laraBookKeeping-dev.tar.gz \
    && rm laraBookKeeping-dev.tar.gz

COPY setup.sh /var/laraBookKeeping/book-keeping/setup.sh

RUN cd /var/laraBookKeeping/book-keeping \
    && composer install --no-interaction --no-dev --optimize-autoloader --prefer-dist

RUN cd /var/laraBookKeeping/book-keeping/public && ln -s ../docs

RUN cd /var/laraBookKeeping && chown -R user:apache book-keeping

RUN cd /var/laraBookKeeping/book-keeping \
    && chown -R apache:apache storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 755 setup.sh

WORKDIR /var/laraBookKeeping/book-keeping

EXPOSE 80

CMD ["sh", "-c", "php-fpm && httpd -D FOREGROUND"]
