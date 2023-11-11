FROM php:8.1-cli
RUN apt-get update && apt-get install -y git zip && apt-get clean
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && pecl clear-cache
WORKDIR /var/www/deptrac
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
