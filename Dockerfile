FROM php:7.1-rc-apache
RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 80 80