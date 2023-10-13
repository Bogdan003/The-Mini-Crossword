FROM php:8.2-fpm

RUN docker-php-ext-install pdo pdo_mysql
RUN apt-get update && apt-get install -y \
    git
WORKDIR /var/www
