FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    firebird-dev \
    libib-util \
    libtool \
    unixodbc-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_firebird

WORKDIR /var/www/html
