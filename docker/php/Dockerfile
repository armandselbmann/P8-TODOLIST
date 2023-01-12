FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends locales apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev zlib1g-dev libzip-dev unzip nano;

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql gd opcache intl zip calendar dom mbstring gd xsl
RUN pecl install apcu && docker-php-ext-enable apcu

RUN mkdir -p /usr/local/etc/php/conf.d \
    && touch /usr/local/etc/php/conf.d/99-xdebug.ini \
    && echo "zend_extension=xdebug" >> /usr/local/etc/php/conf.d/99-xdebug.ini \
    && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/99-xdebug.ini

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN mkdir /var/www/.composer

RUN chown www-data:www-data /var/www/.composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

#RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
#   mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html