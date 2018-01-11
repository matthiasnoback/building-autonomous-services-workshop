FROM php:7.1-fpm-alpine
RUN apk --update add --virtual build-dependencies --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-2.5.0 \
    && docker-php-ext-enable xdebug \
    && apk del build-dependencies
COPY php.ini /usr/local/etc/php/
