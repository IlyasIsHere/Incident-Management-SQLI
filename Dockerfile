FROM php:apache

COPY src /var/www/html/src
COPY index.php /var/www/html/


RUN docker-php-ext-install pdo pdo_mysql &&\
    apt-get update && apt-get upgrade -y &&\
    chmod -R 777 /var/www/html/src/uploads &&\
    pecl install xdebug &&\
    docker-php-ext-enable xdebug


COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip

RUN composer require pusher/pusher-php-server &&\
    composer require firebase/php-jwt



