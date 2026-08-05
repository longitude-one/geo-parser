# The container is intentionally run as root because it is used
# as a local/CI CLI utility container and does not expose services.
FROM php:8.1-cli-alpine

#These packages are need to configure and compile pcov
RUN apk add --no-cache $PHPIZE_DEPS git

#Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# pcov installation
RUN git clone https://github.com/krakjoe/pcov.git /usr/src/php/ext/pcov \
    && docker-php-ext-install pcov

WORKDIR /var/www

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["/usr/local/bin/docker-entrypoint.sh"]
