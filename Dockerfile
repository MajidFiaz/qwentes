FROM php:8.2-fpm

RUN apt-get -y update
RUN curl -L -C - --progress-bar -o /usr/local/bin/composer https://getcomposer.org/composer.phar
RUN chmod 755 /usr/local/bin/composer
RUN apt-get install -y git mc
RUN docker-php-ext-install pdo_mysql mysqli
RUN echo "date.timezone=UTC" >> /usr/local/etc/php/conf.d/timezone.ini