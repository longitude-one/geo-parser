FROM php:8.1-fpm-alpine

#These packages are need to configure and compile pcov
RUN apk --update --no-cache add autoconf gcc make g++

#Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

#Install PCOV
RUN pecl install pcov && docker-php-ext-enable pcov

##Install symfony
#RUN wget https://get.symfony.com/cli/installer -O /tmp/installer && \
#    chmod a+x /tmp/installer && \
#    /tmp/installer --install-dir=/usr/local/bin/ && \
#    rm /tmp/installer

#APCU
#RUN apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS \
#        && pecl install apcu \
#        && docker-php-ext-enable apcu \
#        && pecl clear-cache \
#        && apk del .build-dependencies

#OPCACHE
#COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini
#XSL because this conf file doesn't want to activate it
#COPY xsl.ini /usr/local/etc/php/conf.d/xsl.ini
#COPY php.ini /usr/local/etc/php/conf.d/php.ini

WORKDIR /var/www

CMD composer install ;  php-fpm
EXPOSE 9000