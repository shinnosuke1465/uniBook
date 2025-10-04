FROM --platform=linux/amd64 php:8.4-fpm-bullseye

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    apt-get -y install --no-install-recommends git unzip libzip-dev libicu-dev libonig-dev && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install intl pdo_mysql zip bcmath && \
    groupadd gphp && \
    useradd php -g gphp

USER php

WORKDIR /var/www/html
COPY --chown=php:gphp ./src /var/www/html/

RUN composer install