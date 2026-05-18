FROM php:8.2-fpm

RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /var/www/Florist
RUN chown -R www-data:www-data /var/www/Florist
EXPOSE 9000
