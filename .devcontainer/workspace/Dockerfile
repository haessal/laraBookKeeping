FROM php:8.5

LABEL maintainer="haessal@mizutamauki.net"

ENV DEBIAN_FRONTEND=noninteractive

# Upgrade packages and install common tools
RUN apt-get update && apt-get upgrade -y && apt-get install -y --no-install-recommends \
            ca-certificates \
            curl \
            git \
            less \
            openssh-client \
            tzdata \
            unzip \
            vim \
        && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
        && curl --location --fail --connect-timeout 30 --retry 5 \
               -o /usr/local/etc/git-completion.bash \
               https://raw.githubusercontent.com/git/git/master/contrib/completion/git-completion.bash

# Install Node.js 22 (LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
        && apt-get install -y nodejs \
        && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
        && npm install -g npm-check-updates

# Install CSpell(command line tool for spell checking code)
RUN npm install -g cspell

# Install libraries for building PECL extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
            autoconf \
            g++ \
            gcc \
            make \
        && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Xdebug
RUN pecl install xdebug \
        && docker-php-ext-enable xdebug

COPY xdebug.ini /usr/local/etc/php/conf.d/

# Install MySQL and related extension
RUN apt-get update && apt-get upgrade -y && apt-get install -y --no-install-recommends \
            default-libmysqlclient-dev \
            default-mysql-client \
        && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
        && docker-php-ext-install pdo_mysql

# Install libraries required by Composer/Laravel
RUN apt-get update && apt-get upgrade -y && apt-get install -y --no-install-recommends \
            libonig-dev \
            libzip-dev \
        && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
        && docker-php-ext-install bcmath mbstring zip pcntl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Install phpDocumentor
RUN curl -sS -L -o /usr/local/bin/phpdoc https://phpdoc.org/phpDocumentor.phar && chmod +x /usr/local/bin/phpdoc

ENV USERNAME=developer
ENV USER_UID=9001
ENV USER_GID=9001
RUN groupadd --gid $USER_GID ${USERNAME} \
        && useradd -s /bin/bash --uid ${USER_UID} --gid ${USER_GID} -m ${USERNAME} \
        && echo 'source /usr/local/etc/git-completion.bash' >> /home/${USERNAME}/.bashrc

COPY devcontainer-workspace-start /usr/local/bin/

CMD ["devcontainer-workspace-start"]
